<?php

namespace App\Http\Controllers;

use App\Services\OSS;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;

class CommonController extends Controller
{
    // 导出数据到excel表
    public function export()
    {
        $cellData = [
            ['id','姓名','年龄'],
            ['10001','张三','19'],
            ['10002','李四','22'],
            ['10003','王五','23'],
            ['10004','赵六','19'],
            ['10005','猴七','30'],
        ];
        $name = iconv('UTF-8', 'GBK', '成员信息');

        Excel::create($name, function($excel) use ($cellData){
            $excel->sheet('score', function($sheet) use ($cellData){
                $sheet->rows($cellData);
            });
        })
//            ->store('xls') // 将文件报错在服务器上，注意：如果要保存文件，则 文件名不要写 中文，否则报错 failed to open stream: Protocol error
            ->export('xls');
        
    }
    
    // 导入 excel表的数据
    public function import()
    {
        $filePath = 'storage/exports/'.iconv('UTF-8', 'GBK', '成员信息').'.xls';
        Excel::load($filePath, function($reader) {
            $data = $reader->all(); dump($data);
        });
        exit;
    }

    // 将 账户表 合并到 办事处表中
    public function mergeAccountToOffice()
    {
        $saler_list = DB::table('cx_saler')->get();

        foreach ($saler_list as $value) {
            DB::table('cx_office')->where('id', $value->office_id)->update(['account' => $value->account, 'password' => $value->password, 'salt' => $value->salt, 'update_time' => time()]);
        }


        dd($saler_list);
    }

    // 将 用户打卡上传的 图片 上传至 oss
    public function uploadSignImgToOss()
    {
        $sign_list = DB::table('cx_sign')->orderBy('id', 'DESC')->limit(1)->get();
        foreach ($sign_list as $value) {
            $img_array = unserialize($value->img);
            foreach ($img_array as $img) {

                $img = explode('|', $img);

                if ($value->type == 1) { // 上班
                    $save_path = 'sign/clock_in';
                } else { // 下班
                    $save_path = 'sign/clock_out';
                }

                $oss = new OSS();
                $res = $oss->uploadFile($save_path, public_path() . $img[1]);
                if ($res && $res['code'] == 0) {
                    $oss_img_url = $res['file_name'] ?? '';

                    $sign_update_data = '';
                    if ($img[0] == 1) {
                        $sign_update_data = ['oss_img_1' => $oss_img_url];
                    } elseif ($img[0] == 2) {
                        $sign_update_data = ['oss_img_2' => $oss_img_url];
                    } elseif ($img[0] == 3) {
                        $sign_update_data = ['oss_img_3' => $oss_img_url];
                    }

                    DB::table('cx_sign')->where('id', $value->id)->update($sign_update_data);
                }
            }
        }
    }

    // 从 oss 下载打卡图片
    public function downloadSignImgFromOss(Request $request)
    {
//        $file_name = 'http://nfsqcx.oss-cn-hangzhou.aliyuncs.com/clock_in_and_out_pics/2019/05/05c98076dd14d32391a83c3f3f56adadad.jpg?x-oss-process=image/resize,w_500/watermark,text_44CQ5Yac5aSr5bGx5rOJ44CRMjAxOS0wNS0wMyAxMTo0MA,g_ne,size_13';

        $file_name = $request->file_name ?? '';

        if (!$file_name) {
            return $this->showJson(1, '参数错误');
        }

        $object = $this->getOssFileName($file_name);

        $oss = new OSS();
        $res = $oss->downloadFile($object, $object);

        return $this->showJson($res['code'], $res['msg'], ['file_url' => $res['file_url'] ?? '']);
    }

    // 从 oss 下载产品图片
    public function downloadProductImgFromOss()
    {
        $product_list = DB::table('cx_product')->get();

        foreach ($product_list as $product) {

            $object = $this->getOssFileName($product->oss_img_url);

            $oss = new OSS();
            $res = $oss->downloadFile($object, $object);

            if ($res['code'] == 0) {
                DB::table('cx_product')->where('id', $product->id)->update(['img_url' => $res['file_name']]);
            }

        }

        dd('下载完成');

    }

    // 此方法 供外部下载文件
    public function downloadFileFromLocal(Request $request)
    {
//        $file_name = 'common\clock_in_and_out_pics\2019\05\05c98076dd14d32391a83c3f3f56adadad.jpg';

        $file_name = $request->file_name ?? '';

        if (File::exists($file_name)) {
            return response()->download($file_name);
        } else {
            return $this->showJson(1, '文件不存在');
        }
    }

    // 请求下载外部文件到本地服务器
    public function downloadFileToLocal(Request $request)
    {
//        $file_name = 'common/clock_in_and_out_pics/2019/06/07/20190607925531.jpg';

        $file_name = $request->file_name ?? '';

        if (!$file_name) return $this->showJson(1, '参数错误');

        $url = 'http://nfsqcx.weilh.vip/downloadFileFromLocal?file_name=' . $file_name;

        $file_name_array = explode('/', $file_name);

        array_pop($file_name_array);

        $save_path = join('/', $file_name_array);

        if (!is_dir(public_path($save_path))) {
            mkdir($save_path, 0777, true);
        }

        $client = new \GuzzleHttp\Client(['verify' => false]);  // 忽略SSL错误
        $response = $client->get($url, ['save_to' => public_path($file_name)]);  // 保存远程url到文件

        if ($response->getStatusCode() == 200) {

            $file_url = config('app.url') . '/' . $file_name;
            return $this->showJson(0, '下载成功', ['file_url' => $file_url]);

        } else {

            return $this->showJson(1, '下载失败');

        }

    }


    // 更新 cx_sign 打卡数据
    public function updateSignData()
    {
        set_time_limit(0);

        $i = 0;
        while (true) {

            $this->updateSignData_One();

            $i++;
            \Log::error('$i = ' . $i);
            echo $i . '；' . ($i % 10 == 0 ? '<br>' : '');
        }

        dd('结束');
    }
    public function updateSignData_One()
    {
        dd('stop');
        $max_sign_id = DB::table('cx_a')->max('sign_id') ?? 0;

        $sign_list = DB::table('cx_sign')->where('id', '>', $max_sign_id)->orderBy('id', 'ASC')->limit(100)->get();

        foreach ($sign_list as $value) {

            $created_at = date('Y-m-d H:i:s', $value->create_time);
            DB::table('cx_sign')->where('id', $value->id)->update(['created_at' => $created_at]);

            if ($value->phone) {
                $count = DB::table('cx_sign_phones')->where('phone', $value->phone)->where('date', date('Y-m-d', $value->create_time))->count() ?? 0;
                if ($count == 0) {
                    $sign_phones_insert_data = [
                        'phone' => $value->phone,
                        'date' => date('Y-m-d', $value->create_time),
                        'create_time' => date('Y-m-d H:i:s', $value->create_time)
                    ];
                    DB::table('cx_sign_phones')->insert($sign_phones_insert_data);
                }
            }

            DB::table('cx_a')->insert(['sign_id' => $value->id, 'add_time' => date('Y-m-d H:i:s', time())]);

        }
    }

    // 删除
    public function deleteSignData()
    {
        DB::table('cx_sign_clock_in')->where('type', 2)->delete();
        DB::table('cx_sign_clock_out')->where('type', 1)->delete();
        dd('ok');
    }

}
