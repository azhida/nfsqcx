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

        $clock_in_list = $query->orderBy('sci.id', 'DESC')->paginate(20);

        return view('admin/signClockIn', ['list' => $clock_in_list, 'search_params' => $request->all()]);
    }

    // 下班打卡列表
    public function signclockoutList(Request $request)
    {
        $query = DB::table('cx_sign_clock_out');
        if ($request->start) {
            $query = $query->where('create_time', '>=', strtotime($request->start));
        }
        if ($request->end) {
            $query = $query->where('create_time', '<=', strtotime($request->end) + 60 * 60 * 24);
        }
        if ($request->name) {
            $query = $query->where('name', 'like', '%' . $request->name . '%');
        }

        $clock_in_list = $query->where('type', 2)->orderBy('id', 'DESC')->paginate(10);

        $user_ids = array_column($clock_in_list->items(), 'user_id');
        $user_list = [];
        if (!empty($user_ids)) {
            $user_list = DB::table('cx_saler')->whereIn('id', $user_ids)->get();
        }

        $office_ids = array_column($clock_in_list->items(), 'office_id');
        $office_list = [];
        if (!empty($office_ids)) {
            $office_list = DB::table('cx_office')->whereIn('id', $office_ids)->get();
        }

        $dealers_ids = array_column($clock_in_list->items(), 'dealers_id');
        $dealers_list = [];
        if (!empty($dealers_ids)) {
            $dealers_list = DB::table('cx_dealers')->whereIn('id', $dealers_ids)->get();
        }

        foreach ($clock_in_list as &$value) {
            // 办事处账号
            foreach ($user_list as $v) {
                if ($v->id == $value->user_id) {
                    $value->user_name = $v->account ?? '';
                }
            }
            foreach ($office_list as $v) {
                if ($v->id == $value->office_id) {
                    $value->office_name = $v->name ?? '';
                }
            }
            foreach ($dealers_list as $v) {
                if ($v->id == $value->dealers_id) {
                    $value->dealers_name = $v->dealers_name ?? '';
                }
            }
        }

        return view('admin/signClockOut', ['list' => $clock_in_list, 'search_params' => $request->all()]);
    }

    // 上下班打卡列表
    public function signclockList(Request $request)
    {
        dd(111);
    }

    // 上下班打卡详情
    public function signclockDetail(Request $request)
    {
        $sign_clock_out_id = $request->sign_clock_out_id ?? 0;

        $sign_clock_detail = DB::table('cx_sign_clock_out as sco')
            ->join('cx_saler as s', 's.id', '=', 'sco.user_id')
            ->select('sco.*', 's.account')
            ->where('sco.id', $sign_clock_out_id)
            ->first();

        $imgs = [];
        foreach (unserialize($sign_clock_detail->img) as $img) {
            $img_temp = explode('|', $img);
            if ($img_temp[0] == 1) {
                $imgs['店头照'] = $img_temp[1];
            } elseif ($img_temp[0] == 2) {
                $imgs['现场布建'] = $img_temp[1];
            } elseif ($img_temp[0] == 3) {
                $imgs['促销员照'] = $img_temp[1];
            }
        }
        $sign_clock_detail->imgs = $imgs;

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

        return view('admin/signclockDetail', ['info' => $sign_clock_detail]);
    }
}
