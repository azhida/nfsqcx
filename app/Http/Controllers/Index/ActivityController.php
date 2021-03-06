<?php

namespace App\Http\Controllers\Index;

use App\Services\OSS;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class ActivityController extends Controller
{
    public function clockIn(Request $request)
    {
        if ($request->isMethod('get')) {
            $user_id = Session::get('user_id');
            $office_info = DB::table('cx_office as o')
                ->join('cx_saler as s', 's.office_id', '=', 'o.id')
                ->where('s.id', $user_id)
                ->select('o.*')
                ->first();

            return view('index/clockIn', ['office_info' => $office_info]);
        }
    }

    public function clockOut()
    {
        $user_id = Session::get('user_id');
        $office_info = Db::table('cx_office as o')
            ->join('cx_saler as s', 's.office_id', '=', 'o.id')
            ->where('s.id', $user_id)
            ->select('o.*')
            ->first();

        return view('index/clockOut', ['office_info' => $office_info]);
    }

    // 获取数据列表
    public function getSelectData(Request $request)
    {
        $_data = [];
        switch ($request->type)
        {
            case '0':
                //办事处
                $_list =  DB::table('cx_office')->get();
                break;
            case '1':
                //经销商
                $_list = DB::table('cx_dealers')->select('dealers_name as name', 'id')->where('is_show', true)->where('office_id', $request->office_id ?? 0)->get();
                break;
            case '3':
                //销售点//渠道
                $_list = DB::table('cx_sales')->select('sales_name as name', 'id')->get();
                break;
            case'4':
                //品牌
                $_list = DB::table('cx_activity_item')->get();
                break;
        }

        foreach ($_list as $key => $val)
        {
            $_data[$key]['id'] = $val->id;
            $_data[$key]['name'] = $val->name;
        }
        return $this->showJson("0000", "获取数据成功", $_data);

    }


    // 上传 上下班打卡照片
    public function uploadClockInAndOutPic(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'base64' => 'required',
        ], [
            'base64.required' => '请选择照片',
        ]);

        if ($validator->fails()) {
            return $this->showJson('9999', $validator->errors()->first());
        }

        $name = date('Ymd', time()) . rand(10000, 99999) . '.jpg';
        $base64_string = explode(',', $request->base64);
        $img = base64_decode($base64_string[1]);

        $pic_path = '/common/clock_in_and_out_pics/' . date('Y') . '/' . date('m') . '/' . date('d') . '/';
        $pic = public_path() . $pic_path;

        if (!file_exists ($pic)) {
            mkdir($pic, 0777, true);
        }
        $pic .= $name;

        if(file_put_contents($pic, $img))
        {
            if ($is_use_oss = 0) {
                $oss = new OSS();
                $res = $oss->uploadFile('clock_in_and_out_pics', $pic);
                if ($res['code'] == 1) {
                    return $this->showJson("9999", "上传图片失败");
                }
                $file_name = getOssWatermark($res['file_name']);
                unlink($pic);

            } else {

                // create Image from file
                $img = Image::make($pic);

                // 让 水印宽度 占 图片宽度的 50%
                $img_width = $img->width();
                $size = $img_width / 2 / 18; // 经测试， $size =1px 时， 水印的宽度 为 18px

                // write text at position
                $text = '【农夫山泉】' . date('Y-m-d H:i:s', time());
                $text = mb_convert_encoding($text, "html-entities", "utf-8" ); // 转码 为 utf8，否则有可能出错
//                $img->text('【农夫山泉】' . date('Y-m-d H:i', time()), $img_width / 2, 0, function ($font) use ($size) {
                $img->text($text, $img_width - 10, 10, function ($font) use ($size) {
                    $font->file('./common/msyh.ttf');
//                $font->file(5);
                    $font->size($size);
                    $font->color('#000000');
                    $font->align('right');
                    $font->valign('top');
//                $font->angle(45);
                });

                $img->save($pic);

//                $file_name = config('app.url') . $pic_path .$name;
                $file_name = $pic_path .$name;
            }

            return $this->showJson("0000", "上传图片成功", ['url'=> $file_name]);

        }
    }

    // 提交上班打卡数据
    public function saveClockInData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'office_id' => 'required|numeric',
            'dealers_id' => 'required|numeric',
            'sale_id' => 'required|numeric',
            'activity_item_id' => 'required|numeric',
            'salesOffice' => 'required',
            'phone' => 'required|numeric',
        ], [
            'office_id.required' => '请选择办事处',
            'dealers_id.required' => '请选择经销商',
            'sale_id.required' => '请选择渠道',
            'activity_item_id.required' => '请选择品牌',
            'salesOffice.required' => '请填写售点',
            'phone.required' => '请填写手机号码',
        ]);

        if ($validator->fails()) {
            return $this->showJson('9999', $validator->errors()->first());
        }

        $_check = DB::table('cx_sign')
            ->where(['type' => 1, 'user_id' => Session::get('user_id')])
            ->where('create_time', '>=', strtotime(date('Y-m-d')))
            ->first();
        if(!empty($_check))
        {
            // 可以多次打卡
//            return $this->showJson('9988', '已经打卡');
        }

        $_office =  DB::table('cx_office')->where('id', $request->office_id)->first();
        if(empty($_office))
        {
            return $this->showJson('9988', '办事处不存在');
        }

        $_Dealers = DB::table('cx_dealers')->where('id', $request->dealers_id)->first();
        if(empty($_Dealers))
        {
            return $this->showJson('9988', '经销商不存在');
        }
        $_Sale =  DB::table('cx_sales')->where('id', $request->sale_id)->first();
        if(empty($_Sale))
        {
            return $this->showJson('9988', '渠道不存在');
        }

        $_Activityitem =  DB::table('cx_activity_item')->where('id', $request->activity_item_id)->first();
        if(empty($_Activityitem))
        {
            return $this->showJson('9988', '产品不存在');
        }

        $_data = [
            'dealers_id' =>$request->dealers_id,
            'office_id' =>$request->office_id,
            'sale_id' =>$request->sale_id,
            'activity_item_id' => $request->activity_item_id,
            'points'=>$request->salesOffice,
            'phone'=>$request->phone,
            'img' => serialize($request->imgs),
            'oss_img_1' => $request->img_1,
            'oss_img_2' => $request->img_2,
            'oss_img_3' => $request->img_3,
            'type' => 1,
            'date' => date('Y-m-d'),
            'create_time' => time(),
            'created_at' => date('Y-m-d H:i:s', time()),
            'update_time' => time(),
            'user_id' => Session::get('user_id')
        ];
        $_data['id'] = DB::table('cx_sign')->insertGetId($_data);
        DB::table('cx_sign_clock_in')->insert($_data); // 同步上班打卡数据 到 cx_sign_clock_in 表中
        $sign_phone_insert_data = [
            'phone' => $request->phone,
            'office_id' => $request->office_id,
            'date' => date('Y-m-d'),
            'create_time' => date('Y-m-d H:i:s', time())
        ];
        $where = [
            'phone' => $request->phone,
            'office_id' => $request->office_id,
            'date' => date('Y-m-d'),
        ];
        $sign_phone_count = DB::table('cx_sign_phones')->where($where)->count();
        if ($sign_phone_count == 0) DB::table('cx_sign_phones')->insert($sign_phone_insert_data);

        return $this->showJson("0000", "打卡成功");
    }

    // 获取口味列表
    public function getFlavorList()
    {
        $list = Db::table('cx_flavor')->get();

        return $this->showJson('0000','数据获取成功', $list);
    }

    // 获取产品数据
    public function getUploadingData(Request $request)
    {
        $_list = DB::table('cx_product')->where('cat_id', $request->cat_id)->get();
        return $this->showJson('0000', '数据获取成功', $_list);
    }

    // 保存下班打卡数据
    public function saveClockOutData(Request $request)
    {
        \Log::error('下班打卡数据：' . json_encode($request->all(), JSON_UNESCAPED_UNICODE));
        \Log::error('当日销量是否已填写：' . (empty($request->product_nums) ? '当日销量未填写' : '当日销量已填写'));

        $validator = Validator::make($request->all(), [
            'office_id' => 'required|numeric',
            'dealers_id' => 'required|numeric',
            'salesOffice' => 'required',
            'phone' => 'required|numeric',
            'names' => 'required',
            'code' => 'required',
            'imgs' => 'required',
        ], [
            'office_id.required' => '请选择办事处',
            'dealers_id.required' => '请选择经销商',
            'salesOffice.required' => '请填写售点',
            'phone.required' => '请填写手机号码',
            'names.required' => '请填写姓名',
            'code.required' => '请填写验证码',
            'imgs.required' => '请拍照上传',
        ]);

        if ($validator->fails()) {
            return $this->showJson('9999', $validator->errors()->first());
        }

        // 是否是 不需要验证的手机号码
        $is_no_verify_phone = DB::table('cx_no_verify_phones')->where('phone', $request->phone)->first();
        if (!$is_no_verify_phone) {
            if(Cache::get($request->phone) != $request->code) {
                return $this->showJson('9999', '验证失败');
            }
        }

        $sale_data = [];
        $product_nums = $request->product_nums;
//        if (empty($product_nums)) return $this->showJson('9999', '当日销量未填写');
        if (!empty($product_nums)) {

            foreach ($product_nums as $product_id => $product_num) {

                if($product_num < 0) {
                    return $this->showJson('9999', '销量应大于0');
                }

                if (intval($product_num) > 0) {
                    array_push($sale_data, [
                        'product_id' => $product_id,
                        'product_num' => intval($product_num),
                    ]);
                }

            }

        } else {

            $product_ids = $request->product_id;
            $product_nums = $request->product_num;
            foreach ($product_ids as $key => $product_id) {

                $product_num = $product_nums[$key];

                if (intval($product_num) > 0) {
                    array_push($sale_data, [
                        'product_id' => $product_id,
                        'product_num' => intval($product_num),
                    ]);
                }
            }

        }
        
        $imgs = [
            '1|' . $request->img_1,
            '2|' . $request->img_2,
            '3|' . $request->img_3,
        ];

        // 每天上午 06:00:00 之前上报的下班数据，记录为 前一天的下班打卡记录，06:00:00 之后的下班卡，记录为当天的下班卡
        $date = date('H:i:s', time()) < '06:00:00' ? date("Y-m-d", strtotime("-1 day")) : date('Y-m-d');
        $_data = [
            'img' => serialize($imgs),
            'oss_img_1' => $request->img_1,
            'oss_img_2' => $request->img_2,
            'oss_img_3' => $request->img_3,
            'type' => 2,
            'date' => $date,
            'create_time' => time(),
            'created_at' => date('Y-m-d H:i:s', time()),
            'update_time' => time(),
            'user_id' => Session::get('user_id'),
            'phone'=> $request->phone,
            'names'=> $request->names,
            'data' => serialize($sale_data),
            'office_id' => $request->office_id,
            'dealers_id' => $request->dealers_id,
            'points' => $request->salesOffice,
        ];
        $_data['id'] = DB::table('cx_sign')->insertGetId($_data);
        DB::table('cx_sign_clock_out')->insert($_data); // 同步上班打卡数据 到 cx_sign_clock_out 表中
        $sign_phone_insert_data = [
            'phone' => $request->phone,
            'office_id' => $request->office_id,
            'date' => $date,
            'create_time' => date('Y-m-d H:i:s', time())
        ];
        $where = [
            'phone' => $request->phone,
            'office_id' => $request->office_id,
            'date' => $date,
        ];
        $sign_phone_count = DB::table('cx_sign_phones')->where($where)->count();
        if ($sign_phone_count == 0) DB::table('cx_sign_phones')->insert($sign_phone_insert_data);

        return $this->showJson('0000', '今日上报数据成功');
    }
}
