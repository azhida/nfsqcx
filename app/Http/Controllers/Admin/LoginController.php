<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        if ($request->isMethod('get')) {
            return view('admin/login');
        } else {
            // 参数校验
            $validator = Validator::make($request->all(), [
                'user_name' => 'required|max:255',
                'password' => 'required',
            ], [
                'user_name.required' => '账号必填',
                'password.required' => '密码必填',
            ]);

            if ($validator->fails()) {
                return $this->showJson('9999', $validator->errors()->first());
            }

            $user_info = DB::table('cx_user as u')
                ->join('cx_role as r', 'r.id', '=', 'u.role_id')
                ->select('u.id as admin_id', 'u.*', 'r.*')
                ->where('u.user_name', $request->user_name)
                ->first();

            if(empty($user_info)) return $this->showJson('9999', '管理员不存在');

            if(md5($request->password . env('SALT')) != $user_info->password){
                return $this->showJson('9999', '密码错误');
            }

            if(1 != $user_info->status){
                return $this->showJson('9999', '该账号被禁用');
            }

            session(['admin_id' => $user_info->admin_id]);
            session(['user_name' => $user_info->user_name]);
            session(['head' => $user_info->head]);
            session(['role' => $user_info->role_name]);  // 角色名
            session(['role_id' => $user_info->role_id]);
            session(['rule' => $user_info->rule]);

            // 更新管理员状态
            $param = [
                'login_times' => $user_info->login_times + 1,
                'last_login_ip' => $request->getClientIp(),
                'last_login_time' => time()
            ];

            $res = DB::table('cx_user')->where('id', $user_info->admin_id)->update($param);
            if(1 != $res){
                return $this->showJson('9999', '登录失败');
            }

            return $this->showJson('0000', '登陆成功');
        }
    }

}
