<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class SignclockController extends Controller
{
    // 上班打卡列表
    public function signclockinList(Request $request)
    {
        $query = DB::table('cx_sign_clock_in as sci')
            ->select('sci.*', 'cx_saler.account as user_name', 'cx_office.name as office_name', 'cx_dealers.dealers_name', 'ai.name as activity_item_name', 'cx_sale_channels.sales_name')
            ->join('cx_saler', 'cx_saler.id', '=', 'sci.user_id')
            ->join('cx_office', 'cx_office.id', '=', 'sci.office_id')
            ->join('cx_dealers', 'cx_dealers.id', '=', 'sci.dealers_id')
            ->join('cx_activity_item as ai', 'ai.id', '=', 'sci.activity_item_id')
            ->join('cx_sale_channels', 'cx_sale_channels.id', '=', 'sci.sale_id')
            ->where('sci.type', 1)
        ;
        if ($request->start) {
            $query = $query->where('sci.created_at', '>=', $request->start);
        }
        if ($request->end) {
            $query = $query->where('sci.created_at', '<=', date('Y-m-d', strtotime('+1 day', strtotime($request->end))));
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
            ->select('sci.*', 'cx_saler.account as user_name', 'cx_office.name as office_name', 'cx_dealers.dealers_name', 'ai.name as activity_item_name', 'cx_sales.sales_name')
            ->join('cx_saler', 'cx_saler.id', '=', 'sci.user_id')
            ->join('cx_office', 'cx_office.id', '=', 'sci.office_id')
            ->join('cx_dealers', 'cx_dealers.id', '=', 'sci.dealers_id')
            ->join('cx_activity_item as ai', 'ai.id', '=', 'sci.activity_item_id')
            ->join('cx_sales', 'cx_sales.id', '=', 'sci.sale_id')
            ->where('sci.id', $sign_clock_in_id)
            ->first();

        return view('admin/sign_clock/signclockinDetail', ['info' => $sign_clock_in_detail]);
    }

    // 下班打卡列表
    public function signclockoutList(Request $request)
    {
        $query = DB::table('cx_sign_clock_out')
            ->select('cx_sign_clock_out.*', 'cx_saler.account as user_name', 'cx_office.name as office_name', 'cx_dealers.dealers_name')
            ->join('cx_saler', 'cx_saler.id', '=', 'cx_sign_clock_out.user_id')
            ->join('cx_office', 'cx_office.id', '=', 'cx_sign_clock_out.office_id')
            ->join('cx_dealers', 'cx_dealers.id', '=', 'cx_sign_clock_out.dealers_id')
            ->where('type', 2);

        if ($request->start) {
            $query = $query->where('cx_sign_clock_out.create_time', '>=', strtotime($request->start));
        }
        if ($request->end) {
            $query = $query->where('cx_sign_clock_out.create_time', '<=', strtotime($request->end) + 60 * 60 * 24);
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
            ->join('cx_saler as s', 's.id', '=', 'sco.user_id')
            ->select('sco.*', 's.account')
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
        if ($request->end) $query->where('date', '<=', date('Y-m-d', strtotime('+1 day', strtotime($request->end))));
        if ($request->phone) $query->where('phone', 'like', '%' . $request->phone . '%');
        $list = $query->orderBy('id', 'DESC')->paginate(10);

        $phones = array_column($list->items(), 'phone');
        $dates = array_column($list->items(), 'date');

        // 上班数据
        $sign_clock_in_list = DB::table('cx_sign_clock_in as sci')
            ->select('sci.*', 'cx_saler.account as user_name', 'cx_office.name as office_name', 'cx_dealers.dealers_name', 'ai.name as activity_item_name', 'cx_sales.sales_name')
            ->join('cx_saler', 'cx_saler.id', '=', 'sci.user_id')
            ->join('cx_office', 'cx_office.id', '=', 'sci.office_id')
            ->join('cx_dealers', 'cx_dealers.id', '=', 'sci.dealers_id')
            ->join('cx_activity_item as ai', 'ai.id', '=', 'sci.activity_item_id')
            ->join('cx_sales', 'cx_sales.id', '=', 'sci.sale_id')
            ->whereIn('sci.phone', $phones)
            ->whereIn('sci.date', $dates)
            ->orderBy('sci.create_time', 'DESC')
            ->get();

        // 下班数据
        $sign_clock_out_list = DB::table('cx_sign_clock_out')
            ->whereIn('phone', $phones)
            ->whereIn('date', $dates)
            ->orderBy('create_time', 'ASC')
            ->get();

        foreach ($list as &$value) {

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

        }

        return view('admin/sign_clock/signclockList', ['list' => $list, 'search_params' => $request->all()]);
    }

    // 上下班打卡详情
    public function signclockDetail(Request $request, $phone, $date)
    {
        // 上班数据
        $sign_clock_in_list = DB::table('cx_sign_clock_in as sci')
            ->select('sci.*', 'cx_saler.account as user_name', 'cx_office.name as office_name', 'cx_dealers.dealers_name', 'ai.name as activity_item_name', 'cx_sales.sales_name')
            ->join('cx_saler', 'cx_saler.id', '=', 'sci.user_id')
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
            ->select('sco.*', 'cx_saler.account as user_name', 'cx_office.name as office_name', 'cx_dealers.dealers_name')
            ->join('cx_saler', 'cx_saler.id', '=', 'sco.user_id')
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
    public function getSaleData($data)
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

        $sale_data_temp_str = '<ul>';
        foreach ($sale_arr as $value) {
            if ($value['product_num'] > 0 && isset($product_list_temp[$value['product_id']])) {
                $sale_data_temp_str .= '<li><span>' . $product_list_temp[$value['product_id']] . '</span>：<span>' . $value['product_num'] . ' 瓶；</span></li>';
            }
        }
        $sale_data_temp_str .= '</ul>';

        return $sale_data_temp_str;
    }
}
