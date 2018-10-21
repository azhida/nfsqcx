<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function adminList()
    {
        $admin_list = DB::table('cx_user as u')
            ->select('u.*', 'r.role_name')
            ->join('cx_role as r', 'r.id', '=', 'u.role_id')
            ->orderBy('id', 'ASC')
            ->paginate(10);

//        dd($admin_list);
        return view('admin/adminList', ['list' => $admin_list]);
    }

    public function openAdmin(Request $request)
    {
        $status = $request->status ?? 0;
        $id = $request->id ?? 0;
        if (!$id) return $this->showJson('9999', '参数有误');
        DB::table('cx_user')->where('id', $id)->update(['status' => $status]);
        return $this->showJson('0000', '操作成功');
    }
}
