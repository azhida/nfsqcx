<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function adminList()
    {
        $admin_list = DB::table('cx_user as u')
            ->select('u.*', 'r.role_name')
            ->join('cx_role as r', 'r.id', '=', 'u.role_id')
            ->orderBy('id', 'ASC')
            ->paginate(10);
        return view('admin/adminList', ['list' => $admin_list]);
    }

    public function adminAdd(Request $request)
    {
        if ($request->isMethod('get')) {
            $role_list = DB::table('cx_role')->get();
            return view('admin/adminAdd', ['list' => $role_list]);
        } else {

            $validator = Validator::make($request->all(), [
                'user_name' => 'required',
                'password' => 'required',
                'role_id' => 'required|numeric',
            ], [
                'user_name.required' => '管理员名称必填',
                'password.required' => '登录密码必填',
                'role_id.required' => '请选择管理员角色',
                'role_id.numeric' => '角色选择有误',
            ]);

            if ($validator->fails()) {
                return $this->showJson('9999', $validator->errors()->first());
            }

            $insert_data = $request->all();
            unset($insert_data['_token']);
            $insert_data['password'] = md5($insert_data['password'] . env('SALT'));;

            $id = DB::table('cx_user')->insertGetId($insert_data);
            if ($id) {
                return $this->showJson('0000', '操作成功');
            } else {
                return $this->showJson('9999', '操作失败');
            }
        }

    }

    public function adminEdit(Request $request)
    {
        if (!$request->id) {
            return $this->showJson('9999', '参数有误');
        }

        if ($request->isMethod('get')) {
            $data = DB::table('cx_user')->where('id', $request->id)->first();
            $role_list = DB::table('cx_role')->get();
            return view('admin/adminEdit', ['data' => $data, 'list' => $role_list]);
        } else {

            $validator = Validator::make($request->all(), [
                'user_name' => 'required',
                'role_id' => 'required|numeric',
            ], [
                'user_name.required' => '管理员名称必填',
                'role_id.required' => '请选择管理员角色',
                'role_id.numeric' => '角色选择有误',
            ]);
            if ($validator->fails()) {
                return $this->showJson('9999', $validator->errors()->first());
            }

            $update_data = $request->all();
            unset($update_data['_token']);
            $update_data['password'] = md5($update_data['password'] . env('SALT'));;
            foreach ($update_data as $k => $v) {
                if (!$v) unset($update_data[$k]);
            }

            $res = DB::table('cx_user')->where('id', $request->id)->update($update_data);

            if ($res) {
                return $this->showJson('0000', '操作成功');
            } else {
                return $this->showJson('9999', '操作失败');
            }

        }
    }

    public function adminDelete(Request $request)
    {
        if (!$request->id) $this->showJson('9999', '操作失败');

        $res = DB::table('cx_user')->where('id', $request->id)->delete();

        if ($res) {
            return $this->showJson('0000', '操作成功');
        } else {
            return $this->showJson('9999', '操作失败');
        }

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
