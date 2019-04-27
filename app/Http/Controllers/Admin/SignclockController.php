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
            ->select('sci.*', 'cx_saler.account as user_name', 'cx_office.name as office_name', 'cx_dealers.dealers_name', 'ai.name as activity_item_name', 'cx_sales.sales_name')
            ->join('cx_saler', 'cx_saler.id', '=', 'sci.user_id')
            ->join('cx_office', 'cx_office.id', '=', 'sci.office_id')
            ->join('cx_dealers', 'cx_dealers.id', '=', 'sci.dealers_id')
            ->join('cx_activity_item as ai', 'ai.id', '=', 'sci.activity_item_id')
            ->join('cx_sales', 'cx_sales.id', '=', 'sci.sale_id')
            ->where('sci.type', 1)
        ;
        if ($request->start) {
            $query = $query->where('sci.create_time', '>=', strtotime($request->start));
        }
        if ($request->end) {
            $query = $query->where('sci.create_time', '<=', strtotime($request->end));
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
            $query = $query->where('cx_sales.sales_name', 'like', '%' . $request->sales_name . '%');
        }
        if ($request->points) { // 售点名称
            $query = $query->where('sci.points', 'like', '%' . $request->points . '%');
        }
        if ($request->phone) { // 促销员手机号
            $query = $query->where('sci.phone', 'like', '%' . $request->phone . '%');
        }

        $clock_in_list = $query->orderBy('sci.id', 'DESC')->paginate(10);

        return view('admin/signClockIn', ['list' => $clock_in_list, 'search_params' => $request->all()]);
    }

    // 下班打卡列表
    public function signclockoutList(Request $request)
    {
        $query = DB::table('cx_sign')
            ->select('cx_sign.*', 'cx_saler.account as user_name', 'cx_office.name as office_name', 'cx_dealers.dealers_name')
            ->join('cx_saler', 'cx_saler.id', '=', 'cx_sign.user_id')
            ->join('cx_office', 'cx_office.id', '=', 'cx_sign.office_id')
            ->join('cx_dealers', 'cx_dealers.id', '=', 'cx_sign.dealers_id')
            ->where('type', 2);

        if ($request->start) {
            $query = $query->where('cx_sign.create_time', '>=', strtotime($request->start));
        }
        if ($request->end) {
            $query = $query->where('cx_sign.create_time', '<=', strtotime($request->end) + 60 * 60 * 24);
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
            $query = $query->where('cx_sign.points', 'like', '%' . $request->points . '%');
        }
        if ($request->phone) { // 手机号码
            $query = $query->where('cx_sign.phone', 'like', '%' . $request->phone . '%');
        }
        if ($request->names) { // 促销员姓名
            $query = $query->where('cx_sign.names', 'like', '%' . $request->names . '%');
        }

        $clock_out_list = $query->orderBy('cx_sign.id', 'DESC')->paginate(10);

        return view('admin/signClockOut', ['list' => $clock_out_list, 'search_params' => $request->all()]);
    }

    // 上下班打卡列表
    public function signclockList(Request $request)
    {
        // 上班时间： 当日 00:00:00 - 23:59:59 ，下班打卡时间： 当日 00:00:00 - 23:59:59
        // 先取 促销员 上班打卡列表 ， 同一个促销员多次打卡，只取最早一次

        set_time_limit(0);

//        $a = DB::table('cx_sign')->where('date', null)->limit(10000)->get();
//        foreach ($a as $value) {
//            DB::table('cx_sign')->where('id', $value->id)->update(['date' => date('Y-m-d', $value->create_time)]);
//        }
//        dd(112, $a);

        $query = DB::table('cx_sign as sci')
            ->select(DB::raw('min(sci.id) as id, sci.phone, sci.date'))
            ->join('cx_saler', 'cx_saler.id', '=', 'sci.user_id')
            ->join('cx_office', 'cx_office.id', '=', 'sci.office_id')
            ->join('cx_dealers', 'cx_dealers.id', '=', 'sci.dealers_id')
            ->join('cx_activity_item as ai', 'ai.id', '=', 'sci.activity_item_id')
            ->join('cx_sales', 'cx_sales.id', '=', 'sci.sale_id')
            ->where('sci.type', '=', 1)
            ->where('sci.phone', '!=', '')
        ;
        if ($request->start) {
            $query = $query->where('sci.create_time', '>=', strtotime($request->start));
        }
        if ($request->end) {
            $query = $query->where('sci.create_time', '<=', strtotime($request->end));
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
            $query = $query->where('cx_sales.sales_name', 'like', '%' . $request->sales_name . '%');
        }
        if ($request->points) { // 售点名称
            $query = $query->where('sci.points', 'like', '%' . $request->points . '%');
        }
        if ($request->phone) { // 促销员手机号
            $query = $query->where('sci.phone', 'like', '%' . $request->phone . '%');
        }

        $list = $query
            ->orderBy('sci.id', 'DESC')
            ->orderBy('sci.create_time', 'ASC')
            ->groupBy('sci.date', 'sci.phone')
            ->paginate(10);

        $clock_in_ids = array_column($list->items(), 'id');
        $phones = array_column($list->items(), 'phone');
        $dates = array_column($list->items(), 'date');

        // 取 上班打卡数据
        $clock_in_list = DB::table('cx_sign_clock_in as sci')
            ->select('sci.*', 'cx_saler.account as user_name', 'cx_office.name as office_name', 'cx_dealers.dealers_name', 'ai.name as activity_item_name', 'cx_sales.sales_name')
            ->join('cx_saler', 'cx_saler.id', '=', 'sci.user_id')
            ->join('cx_office', 'cx_office.id', '=', 'sci.office_id')
            ->join('cx_dealers', 'cx_dealers.id', '=', 'sci.dealers_id')
            ->join('cx_activity_item as ai', 'ai.id', '=', 'sci.activity_item_id')
            ->join('cx_sales', 'cx_sales.id', '=', 'sci.sale_id')
            ->whereIn('sci.id', $clock_in_ids)
            ->get();

        // 取 下班打卡数据
        $clock_out_list = DB::table('cx_sign')
            ->where('type', 2)
            ->whereIn('phone', $phones)
            ->whereIn('date', $dates)
            ->get();

        foreach ($list as &$value) {

            foreach ($clock_in_list as $v) {
                if ($v->id == $value->id) {
                    $v->add_time = date('Y-m-d H:i:s', $v->create_time);
                    $value->clock_in_list = $v;
                }
            }

            foreach ($clock_out_list as $v) {
                if ($v->phone == $value->phone && $v->date == $value->date) {
                    $v->add_time = date('Y-m-d H:i:s', ($v->create_time));

                    // 销售数据
                    $v->sale_data = $this->getSaleData($v->data);

                    $value->clock_out_list = $v;
                }
            }
        }

        return view('admin/signClockList', ['list' => $list, 'search_params' => $request->all()]);
    }

    // 上下班打卡详情
    public function signclockDetail(Request $request)
    {
        $sign_clock_out_id = $request->sign_clock_out_id ?? 0;

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

        return view('admin/signClockDetail', ['info' => $sign_clock_detail]);
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
