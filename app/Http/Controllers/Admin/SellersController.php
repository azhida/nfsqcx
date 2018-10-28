<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SellersController extends Controller
{
    public function sellersList(Request $request)
    {
        $query = DB::table('cx_saler');
        if ($request->start) $query = $query->where('create_time', '>=', strtotime($request->start));
        if ($request->end) $query = $query->where('create_time', '<=', strtotime($request->end));
        if ($request->account) $query = $query->where('account', 'like', '%' . $request->account . '%');
        if ($request->office_id) $query = $query->where('office_id', $request->office_id);

        $saller_list = $query->orderBy('id', 'DESC')->paginate(10);

        // 办事处列表
        $office_list = DB::table('cx_office')->orderBy('id', 'DESC')->get();

        if (!empty($office_list)) {
            foreach ($saller_list as &$value) {
                $value->add_time = date('Y-m-d H:i:s', $value->create_time);
                $value->update_time = date('Y-m-d H:i:s', $value->update_time);
                foreach ($office_list as $v) {
                    if ($v->id == $value->office_id) {
                        $value->office_name = $v->name;
                    }
                }
            }
        }

        // 办事处

        return view('admin/sellersList', ['list' => $saller_list, 'search_params' => $request->all(), 'office_list' => $office_list]);
    }

    public function sellersAdd(Request $request)
    {
        if ($request->isMethod('get')) {

            $office_list = DB::table('cx_office')->orderBy('id', 'DESC')->get();
            return view('admin/sellersAdd', ['list' => $office_list]);

        } else {

            $validator = Validator::make($request->all(), [
                'account' => 'required|unique:cx_saler,account',
                'password' => 'required',
                'office_id' => 'required|numeric|min:1',
            ], [
                'account.required' => '登录账号必填',
                'account.unique' => '登录账号已存在',
                'password.required' => '登录密码必填',
                'office_id.required' => '办事处必选',
                'office_id.numeric' => '办事处必选',
                'office_id.min' => '办事处必选',
            ]);
            if ($validator->fails()) {
                return $this->showJson('9999', $validator->errors()->first());
            }

            $salt = $this->getNumberSalt();
            $insert_data = [
               'account' => $request->account,
               'password' => md5($request->password . $salt ),
               'office_id' => $request->office_id,
               'salt' => $salt,
               'create_time' => time(),
               'update_time' => time(),
            ];

            $id = DB::table('cx_saler')->insertGetId($insert_data);
            if ($id) {
                return $this->showJson('0000', '操作成功');
            } else {
                return $this->showJson('9999', '操作失败');
            }

        }
    }

    public function sellersEdit(Request $request)
    {
        if ($request->isMethod('get')) {

        } else {

        }

    }

    public function sellersDelete(Request $request)
    {
        DB::table('cx_saler')->whereIn('id', $request->ids)->delete();
        return $this->showJson('0000', '操作成功');
    }

}
