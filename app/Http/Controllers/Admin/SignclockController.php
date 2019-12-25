<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class SignclockController extends Controller
{
    // 上班打卡列表
    public function signclockinList(Request $request)
    {
        $query = DB::table('cx_sign_clock_in as sci')
            ->select('sci.*', 'cx_office.name as office_name', 'cx_dealers.dealers_name', 'ai.name as activity_item_name', 'cx_sale_channels.sales_name')
//            ->join('cx_saler', 'cx_saler.id', '=', 'sci.user_id')
            ->join('cx_office', 'cx_office.id', '=', 'sci.office_id')
            ->join('cx_dealers', 'cx_dealers.id', '=', 'sci.dealers_id')
            ->join('cx_activity_item as ai', 'ai.id', '=', 'sci.activity_item_id')
            ->join('cx_sale_channels', 'cx_sale_channels.id', '=', 'sci.sale_id')
            ->where('sci.type', 1)
        ;
        if ($request->start) {
            $query = $query->where('sci.date', '>=', $request->start);
        }
        if ($request->end) {
            $query = $query->where('sci.date', '<=', $request->end);
        }
        if ($request->account) { // 办事处账号
            $query = $query->where('cx_saler.account', 'like', '%' . $request->account . '%');
        }
        if ($request->office_name) { // 办事处名称
            $query = $query->where('cx_office.name', 'like', '%' . $request->office_name . '%');
        }
        if ($request->dealers_name) { // 经销商名称
            $query = $query->where('cx_dealers.dealers_name', 'like', '%' . $request->dealers_name . '%');
        }
        if ($request->activity_item_name) { // 品牌名称
            $query = $query->where('ai.name', 'like', '%' . $request->activity_item_name . '%');
        }
        if ($request->sales_name) { // 渠道名称
            $query = $query->where('cx_sale_channels.sales_name', 'like', '%' . $request->sales_name . '%');
        }
        if ($request->points) { // 售点名称
            $query = $query->where('sci.points', 'like', '%' . $request->points . '%');
        }
        if ($request->phone) { // 促销员手机号
            $query = $query->where('sci.phone', 'like', '%' . $request->phone . '%');
        }

        $clock_in_list = $query->orderBy('sci.id', 'DESC')->paginate(10);

        return view('admin/sign_clock/signclockinList', ['list' => $clock_in_list, 'search_params' => $request->all()]);
    }

    // 上班打卡详情
    public function signclockinDetail(Request $request, $sign_clock_in_id)
    {
        // 上班数据
        $sign_clock_in_detail = DB::table('cx_sign_clock_in as sci')
            ->select('sci.*', 'cx_office.name as office_name', 'cx_dealers.dealers_name', 'ai.name as activity_item_name', 'cx_sales.sales_name')
//            ->join('cx_saler', 'cx_saler.id', '=', 'sci.user_id')
            ->join('cx_office', 'cx_office.id', '=', 'sci.office_id')
            ->join('cx_dealers', 'cx_dealers.id', '=', 'sci.dealers_id')
            ->join('cx_activity_item as ai', 'ai.id', '=', 'sci.activity_item_id')
            ->join('cx_sales', 'cx_sales.id', '=', 'sci.sale_id')
            ->where('sci.id', $sign_clock_in_id)
            ->first();

        return view('admin/sign_clock/signclockinDetail', ['info' => $sign_clock_in_detail]);
    }

    public function signclockEdit(Request $request, $id)
    {
        $clock_time = $request->clock_time ?? '';
        if (!$clock_time) return $this->showJson('9999', '请选择打卡时间');

        $date = date('Y-m-d', strtotime($clock_time));

        $create_time = strtotime($clock_time);

        $clock_type = $request->clock_type ?? '';

        // 修改时间
        DB::table('cx_sign')->where('id', $id)->update(['date' =>$date, 'created_at' => $clock_time, 'create_time' => $create_time]);

        if ($clock_type == 'clock_in') {
            DB::table('cx_sign_clock_in')->where('id', $id)->update(['date' => $date, 'created_at' => $clock_time, 'create_time' => $create_time]);
        } else if ($clock_type == 'clock_out') {
            DB::table('cx_sign_clock_out')->where('id', $id)->update(['date' => $date, 'created_at' => $clock_time, 'create_time' => $create_time]);
        }

        // 数据保存到 cx_sign_phones
        $sign = DB::table('cx_sign')->where('id', $id)->first();
        $sign_phone_insert_data = [
            'phone' => $sign->phone,
            'office_id' => $sign->office_id,
            'date' => $sign->date,
            'create_time' => $sign->create_time,
        ];
        $where = [
            'phone' => $sign->phone,
            'office_id' => $sign->office_id,
            'date' => $sign->date,
        ];
        $sign_phone_count = DB::table('cx_sign_phones')->where($where)->count();
        if ($sign_phone_count == 0) DB::table('cx_sign_phones')->insert($sign_phone_insert_data);

        return $this->showJson('0000', '操作成功');
    }

    // 下班打卡列表
    public function signclockoutList(Request $request)
    {
        $query = DB::table('cx_sign_clock_out')
            ->select('cx_sign_clock_out.*', 'cx_office.name as office_name', 'cx_dealers.dealers_name')
//            ->join('cx_saler', 'cx_saler.id', '=', 'cx_sign_clock_out.user_id')
            ->join('cx_office', 'cx_office.id', '=', 'cx_sign_clock_out.office_id')
            ->join('cx_dealers', 'cx_dealers.id', '=', 'cx_sign_clock_out.dealers_id')
            ->where('type', 2);

        if ($request->start) {
            $query = $query->where('cx_sign_clock_out.date', '>=', $request->start);
        }
        if ($request->end) {
            $query = $query->where('cx_sign_clock_out.date', '<=', $request->end);
        }
        if ($request->user_name) { // 办事处账号
            $query = $query->where('cx_saler.account', 'like', '%' . $request->user_name . '%');
        }
        if ($request->office_name) { // 办事处名称
            $query = $query->where('cx_office.name', 'like', '%' . $request->office_name . '%');
        }
        if ($request->dealers_name) { // 经销商名称
            $query = $query->where('cx_dealers.dealers_name', 'like', '%' . $request->dealers_name . '%');
        }
        if ($request->points) { // 售点名称
            $query = $query->where('cx_sign_clock_out.points', 'like', '%' . $request->points . '%');
        }
        if ($request->phone) { // 手机号码
            $query = $query->where('cx_sign_clock_out.phone', 'like', '%' . $request->phone . '%');
        }
        if ($request->names) { // 促销员姓名
            $query = $query->where('cx_sign_clock_out.names', 'like', '%' . $request->names . '%');
        }

        $clock_out_list = $query->orderBy('cx_sign_clock_out.id', 'DESC')->paginate(10);

        return view('admin/sign_clock/signclockoutList', ['list' => $clock_out_list, 'search_params' => $request->all()]);
    }

    // 下班打卡详情
    public function signclockoutDetail(Request $request, $sign_clock_out_id)
    {
        $sign_clock_detail = DB::table('cx_sign as sco')
//            ->join('cx_saler as s', 's.id', '=', 'sco.user_id')
            ->select('sco.*')
            ->where('sco.id', $sign_clock_out_id)
            ->first();

        $product_data = unserialize($sign_clock_detail->data);
        $product_ids = array_column($product_data, 'product_id');
        if (!empty($product_ids)) {
            $product_list = DB::table('cx_product')->whereIn('id', $product_ids)->get();
        } else {
            $product_list = [];
        }

        foreach ($product_data as &$value) {
            foreach ($product_list as $v) {
                if ($v->id == $value['product_id']) {
                    $value['product_name'] = $v->name ?? '';
                }
            }
        }

        $sign_clock_detail->data = $product_data;

        return view('admin/sign_clock/signclockoutDetail', ['info' => $sign_clock_detail]);
    }

    // 上下班打卡列表
    public function signclockList(Request $request)
    {
        // 上班时间： 当日 00:00:00 - 23:59:59 ，下班打卡时间： 当日 00:00:00 - 23:59:59
        // 先取 促销员 上班打卡列表 ， 同一个促销员多次打卡，只取最早一次

        $query = DB::table('cx_sign_phones');
        if ($request->start) $query->where('date', '>=', $request->start);
        if ($request->end) $query->where('date', '<=', $request->end);
        if ($request->phone) $query->where('phone', 'like', '%' . $request->phone . '%');
        if ($request->office_id) $query->where('office_id', $request->office_id);
        $list = $query->orderBy('id', 'DESC')->paginate(10);

        $phones = array_column($list->items(), 'phone');
        $dates = array_column($list->items(), 'date');

        $sign_clock_in_query = DB::table('cx_sign_clock_in as sci')
            ->select('sci.*', 'cx_office.name as office_name', 'cx_dealers.dealers_name', 'ai.name as activity_item_name', 'cx_sales.sales_name')
//            ->join('cx_saler', 'cx_saler.id', '=', 'sci.user_id')
            ->join('cx_office', 'cx_office.id', '=', 'sci.office_id')
            ->join('cx_dealers', 'cx_dealers.id', '=', 'sci.dealers_id')
            ->join('cx_activity_item as ai', 'ai.id', '=', 'sci.activity_item_id')
            ->join('cx_sales', 'cx_sales.id', '=', 'sci.sale_id')
            ->whereIn('sci.phone', $phones)
            ->whereIn('sci.date', $dates);
        $sign_clock_out_query = DB::table('cx_sign_clock_out')
            ->whereIn('phone', $phones)
            ->whereIn('date', $dates);
        if ($request->office_id) {
            $sign_clock_in_query->where('sci.office_id', $request->office_id);
            $sign_clock_out_query->where('office_id', $request->office_id);
        }
        $sign_clock_in_list = $sign_clock_in_query->orderBy('sci.create_time', 'DESC')->get();
        $sign_clock_out_list = $sign_clock_out_query->orderBy('create_time', 'ASC')->get();

        // 办事处列表
        $office_list = DB::table('cx_office')->orderBy('id', 'DESC')->get();
        $office_names = [];
        foreach ($office_list as $office) {
            $office_names[$office->id] = $office->name;
        }

        foreach ($list as $key => &$value) {

            $value->office_name = $office_names[$value->office_id] ?? '';

            foreach ($sign_clock_in_list as $v) {
                if ($v->phone == $value->phone && $v->date == $value->date) {
                    $value->clock_in_list = $v;
                }
            }

            foreach ($sign_clock_out_list as $v) {
                if ($v->phone == $value->phone && $v->date == $value->date) {
                    $v->sale_data = $this->getSaleData($v->data); // 销售数据
                    $value->clock_out_list = $v;
                }
            }

            if (!isset($value->clock_in_list) && !isset($value->clock_out_list)) {
                DB::table('cx_sign_phones')->where('id', $value->id)->delete();
                unset($list[$key]);
            }
        }

        $data = [
            'list' => $list,
            'search_params' => $request->all(),
            'office_list' => $office_list,
        ];

        return view('admin/sign_clock/signclockList', $data);
    }

    // 上下班打卡详情
    public function signclockDetail(Request $request, $phone, $date)
    {
        // 上班数据
        $sign_clock_in_list = DB::table('cx_sign_clock_in as sci')
            ->select('sci.*', 'cx_office.name as office_name', 'cx_dealers.dealers_name', 'ai.name as activity_item_name', 'cx_sales.sales_name')
//            ->join('cx_saler', 'cx_saler.id', '=', 'sci.user_id')
            ->join('cx_office', 'cx_office.id', '=', 'sci.office_id')
            ->join('cx_dealers', 'cx_dealers.id', '=', 'sci.dealers_id')
            ->join('cx_activity_item as ai', 'ai.id', '=', 'sci.activity_item_id')
            ->join('cx_sales', 'cx_sales.id', '=', 'sci.sale_id')
            ->where('sci.phone', $phone)
            ->where('sci.date', $date)
            ->orderBy('sci.create_time', 'ASC')
            ->get();

        // 下班数据
        $sign_clock_out_list = DB::table('cx_sign_clock_out as sco')
            ->select('sco.*', 'cx_office.name as office_name', 'cx_dealers.dealers_name')
//            ->join('cx_saler', 'cx_saler.id', '=', 'sco.user_id')
            ->join('cx_office', 'cx_office.id', '=', 'sco.office_id')
            ->join('cx_dealers', 'cx_dealers.id', '=', 'sco.dealers_id')
            ->where('sco.phone', $phone)
            ->where('sco.date', $date)
            ->orderBy('sco.create_time', 'ASC')
            ->get();

        foreach ($sign_clock_out_list as &$v) {
            $v->sale_data = $this->getSaleData($v->data); // 销售数据
        }

        $info = [
            'phone' => $phone,
            'date' => $date,
            'sign_clock_in_list' => $sign_clock_in_list,
            'sign_clock_out_list' => $sign_clock_out_list,
        ];

//        dd($info);
        return view('admin/sign_clock/signclockDetail', ['info' => $info]);
    }

    // 获取 下班打卡的销售数据
    public function getSaleData($data, $is_export = 0)
    {
        $sale_arr = unserialize($data);

        $product_ids = array_column($sale_arr, 'product_id');

        if (empty($product_ids)) {
            return '';
        } else {
            $product_list = DB::table('cx_product')
                ->select('id', 'name')
                ->whereIn('id', $product_ids)
                ->get();
        }

        $product_list_temp = [];
        if (!empty($product_list)) {
            foreach ($product_list as $value) {
                $product_list_temp[$value->id] = $value->name;
            }
        }

        if ($is_export == 1) {
            // 导出数据到 excel
            $sale_data_temp_arr = [];
            foreach ($sale_arr as $value) {
                if ($value['product_num'] > 0 && isset($product_list_temp[$value['product_id']])) {
                    $sale_data_temp_arr[$product_list_temp[$value['product_id']]] = intval($value['product_num']);
                }
            }

            return $sale_data_temp_arr;
        }

        $sale_data_temp_str = '<ul>';
        foreach ($sale_arr as $value) {
            if ($value['product_num'] > 0 && isset($product_list_temp[$value['product_id']])) {
                $sale_data_temp_str .= '<li><span>' . $product_list_temp[$value['product_id']] . '</span>：<span>' . $value['product_num'] . ' 瓶；</span></li>';
            }
        }
        $sale_data_temp_str .= '</ul>';

        return $sale_data_temp_str;
    }

    // 导出 上下班打卡数据 到 excel
    public function exportSignClockData(Request $request)
    {
        if (!$request->isMethod('get')) {
            if (!$request->start) return $this->showJson(1, '开始日必选');
            if (!$request->end) return $this->showJson(1, '截止日必选');

            $datetime_start = new \DateTime($request->start);
            $datetime_end = new \DateTime($request->end);
            $diff_days = $datetime_start->diff($datetime_end)->days;

            if ($diff_days + 1 > 31) return $this->showJson(1, '选择时间范围不能超过31天');

            // 可以导出
            $export_key = 'export_key_' . time() . rand(10000, 999999);
            $export_key_value = rand(10000, 999999);
            $expiredAt = now()->addMinutes(1);
            \Cache::put($export_key, $export_key_value, $expiredAt);

            // 生成下载链接
            $export_url = url('admin/exportSignClockData') . '?start=' . $request->start . '&end=' . $request->end . '&office_id=' . $request->office_id . '&phone=' . $request->phone . '&export_key=' . $export_key . '&export_key_value=' . $export_key_value;

            return $this->showJson(0, '可以导出数据', ['url' => $export_url]);

        }

        set_time_limit(0);

        // 校验
        $export_key_value = \Cache::get($request->export_key);
        if ($export_key_value != $request->export_key_value) {
            exit('非法操作');
        } else {
            \Cache::forget($request->export_key); // 清除验证码缓存
        }

        $query = DB::table('cx_sign_phones')->select('phone', 'office_id', 'date');
        if ($request->start) $query->where('date', '>=', $request->start);
        if ($request->end) $query->where('date', '<=', $request->end);
        if ($request->phone) $query->where('phone', 'like', '%' . $request->phone . '%');
        if ($request->office_id) $query->where('office_id', $request->office_id);
        $list = $query->orderBy('id', 'DESC')->get()->toArray();

        $phones = array_column($list, 'phone');
        $dates = array_column($list, 'date');

        $sign_clock_in_query = DB::table('cx_sign_clock_in as sci')
            ->select('sci.date', 'sci.points', 'sci.phone', 'sci.created_at', 'cx_saler.account as user_name', 'cx_office.name as office_name', 'cx_dealers.dealers_name', 'ai.name as activity_item_name', 'cx_sales.sales_name')
            ->join('cx_saler', 'cx_saler.id', '=', 'sci.user_id')
            ->join('cx_office', 'cx_office.id', '=', 'sci.office_id')
            ->join('cx_dealers', 'cx_dealers.id', '=', 'sci.dealers_id')
            ->join('cx_activity_item as ai', 'ai.id', '=', 'sci.activity_item_id')
            ->join('cx_sales', 'cx_sales.id', '=', 'sci.sale_id')
            ->whereIn('sci.phone', $phones)
            ->whereIn('sci.date', $dates);
        $sign_clock_out_query = DB::table('cx_sign_clock_out')
            ->select('date', 'points', 'phone', 'data', 'names', 'created_at')
            ->whereIn('phone', $phones)
            ->whereIn('date', $dates);
        if ($request->office_id) {
            $sign_clock_in_query->where('sci.office_id', $request->office_id);
            $sign_clock_out_query->where('office_id', $request->office_id);
        }
        $sign_clock_in_list = $sign_clock_in_query->orderBy('sci.create_time', 'DESC')->get()->toArray();
        $sign_clock_out_list = $sign_clock_out_query->orderBy('create_time', 'ASC')->get()->toArray();

        // 办事处列表
        $office_list = DB::table('cx_office')->orderBy('id', 'DESC')->get();
        $office_names = [];
        foreach ($office_list as $office) {
            $office_names[$office->id] = $office->name;
        }

        foreach ($list as $key => $value) {

            $value->office_name = $office_names[$value->office_id] ?? '';

            foreach ($sign_clock_in_list as $v) {
                if ($v->phone == $value->phone && $v->date == $value->date) {
                    $value->clock_in_list = $v;
                }
            }

            foreach ($sign_clock_out_list as $v) {
                if ($v->phone == $value->phone && $v->date == $value->date) {
                    $v->sale_data = $this->getSaleData($v->data, 1); // 销售数据
                    $value->clock_out_list = $v;
                }
            }

            if (!isset($value->clock_in_list) && !isset($value->clock_out_list)) {
                unset($list[$key]);
            }

        }

        $cellData = [];
        $cellData[0] = ['办事处名称', '经销商名称', '销售点名称', '渠道', '品牌', '上班打卡时间', '下班打卡时间', '促销员姓名', '促销员手机号', '品项', '数量（瓶）'];
        foreach ($list as $key => $value) {

            if (empty($value->clock_out_list->sale_data)) {

                $office_name = $value->office_name ?? ''; // 办事处名称
                $dealers_name = $value->clock_in_list->dealers_name ?? ''; // 经销商名称
                $points = $value->clock_in_list->points ?? ''; // 销售点名称
                $sale_channels_name = $value->clock_in_list->sales_name ?? ''; // 渠道
                $activity_item_name = $value->clock_in_list->activity_item_name ?? ''; // 品牌
                $created_at_clock_in = $value->clock_in_list->created_at ?? ''; // 上班打卡时间
                $created_at_clock_out = $value->clock_out_list->created_at ?? ''; // 下班打卡时间
                $names = $value->clock_out_list->names ?? ''; // 促销员姓名
                $phone = $value->phone ?? ''; // 促销员手机号

                $product_name = ''; // 品项（即 ： 产品）
                $product_num = ''; // 销售数量

                $cellData_one = [
                    $office_name, $dealers_name, $points, $sale_channels_name, $activity_item_name,
                    $created_at_clock_in, $created_at_clock_out, $names, $phone, $product_name,
                    $product_num
                ];

                array_push($cellData, $cellData_one);

            } else {

                foreach ($value->clock_out_list->sale_data as $k => $v) {

                    $office_name = $value->office_name ?? ''; // 办事处名称
                    $dealers_name = $value->clock_in_list->dealers_name ?? ''; // 经销商名称
                    $points = $value->clock_in_list->points ?? ''; // 销售点名称
                    $sale_channels_name = $value->clock_in_list->sales_name ?? ''; // 渠道
                    $activity_item_name = $value->clock_in_list->activity_item_name ?? ''; // 品牌
                    $created_at_clock_in = $value->clock_in_list->created_at ?? ''; // 上班打卡时间
                    $created_at_clock_out = $value->clock_out_list->created_at ?? ''; // 下班打卡时间
                    $names = $value->clock_out_list->names ?? ''; // 促销员姓名
                    $phone = $value->phone ?? ''; // 促销员手机号

                    $product_name = $k; // 品项（即 ： 产品）
                    $product_num = $v; // 销售数量

                    $cellData_one = [
                        $office_name, $dealers_name, $points, $sale_channels_name, $activity_item_name,
                        $created_at_clock_in, $created_at_clock_out, $names, $phone, $product_name,
                        $product_num
                    ];

                    array_push($cellData, $cellData_one);

                }

            }

        }

        $name = iconv('UTF-8', 'GBK', '打卡数据【' . $request->start . ' 至 ' . $request->end . '】');

        Excel::create($name, function($excel) use ($cellData, $request){
            $excel->sheet($request->start . ' 至 ' . $request->end, function($sheet) use ($cellData){
                $sheet->rows($cellData);
            });
        })
//            ->store('xls') // 将文件报错在服务器上，注意：如果要保存文件，则 文件名不要写 中文，否则报错 failed to open stream: Protocol error
            ->export('xlsx');

        return $this->showJson(0, '操作成功');
    }
}
