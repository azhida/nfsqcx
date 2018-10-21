<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class IndexController extends Controller
{
    public function index()
    {
        return view('admin/index');
    }

    public function welcome()
    {
        // 管理员数量
        $data['admin_count'] = DB::table('cx_user')->count();

        // 促销员数量
        $data['saler_count'] = DB::table('cx_saler')->count();

        // 办事处数量
        $data['office_count'] = DB::table('cx_office')->count();

        // 经销商数量
        $data['dealers_count'] = DB::table('cx_dealers')->count();

        // 产品数量
        $data['product_count'] = DB::table('cx_product')->count();

        // 渠道数量
        $data['sales_count'] = DB::table('cx_sales')->count();


        return view('admin/welcome', ['data' => $data]);
    }
}
