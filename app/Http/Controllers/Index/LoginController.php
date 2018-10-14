<?php

namespace App\Http\Controllers\Index;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function index()
    {
        return view('index/index');
    }
    public function login(Request $request)
    {
        if ($request->isMethod('get')) {

            if(Session::has('user_id')) { // 已登录
                return view('index/index');
            } else { // 未登录
                return view('index/login');
            }

        } else {

            // 接收校验参数
            $validator = Validator::make($request->all(), [
                'account' => 'required|max:255',
                'password' => 'required',
                'login_type' => 'required|numeric',
            ], [
                'account.required' => '账号必填',
                'password.required' => '密码必填',
            ]);

            if ($validator->fails()) {
                return $this->showJson('9999', $validator->errors()->first());
            }

            // 比对 ，读库 验证密码
            $saler_info = DB::table('cx_saler')->where('account', $request->account)->first();
            if(!empty($saler_info) && md5($request->password . $saler_info->salt) == $saler_info->password) {
                Session::put('user_id', $saler_info->id);
                return $this->showJson("0000","登陆成功");
            } else {
                return $this->showJson('9999', '账号或密码错误');
            }

        }
    }

    // 退出登录
    public function logout()
    {
        Session::flush(); // 移除所有项目

        return redirect('index/login', 302); // 302是临时重定向，301是永久重定向
    }

}
