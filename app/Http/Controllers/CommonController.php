<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommonController extends Controller
{
    // 将 账户表 合并到 办事处表中
    public function mergeAccountToOffice()
    {
        $saler_list = DB::table('cx_saler')->get();

        foreach ($saler_list as $value) {
            DB::table('cx_office')->where('id', $value->office_id)->update(['account' => $value->account, 'password' => $value->password, 'salt' => $value->salt, 'update_time' => time()]);
        }


        dd($saler_list);
    }
}
