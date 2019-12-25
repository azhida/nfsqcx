<?php

namespace App\Http\Controllers\Admin;

use App\Models\Office;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OfficesController extends Controller
{
    public function officesList(Request $request)
    {
        $query = DB::table('cx_office');
        if ($request->start) {
            $query = $query->where('create_time', '>=', strtotime($request->start));
        }
        if ($request->end) {
            $query = $query->where('create_time', '<=', strtotime($request->end));
        }
        if ($request->name) {
            $query = $query->where('name', 'like', '%' . $request->name . '%');
        }

        $office_list = $query->orderBy('id', 'DESC')->paginate(10);

        return view('admin/officesList', ['list' => $office_list, 'search_params' => $request->all()]);
    }

    public function officesAdd(Request $request)
    {
        if ($request->isMethod('get')) {

            return view('admin/officesAdd');

        } else {

            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:cx_office,name',
            ], [
                'name.required' => '办事处必填',
                'name.unique' => '办事处已存在',
            ]);
            if ($validator->fails()) {
                return $this->showJson('9999', $validator->errors()->first());
            }

            $insert_data = [
                'name' => $request->name,
                'create_time' => time(),
                'update_time' => time(),
            ];

            $id = DB::table('cx_office')->insertGetId($insert_data);
            if ($id) {
                return $this->showJson('0000', '操作成功');
            } else {
                return $this->showJson('9999', '操作失败');
            }

        }

    }

    public function officesEdit(Request $request)
    {
        $info = Office::query()->find($request->id);

        if ($request->isMethod('get')) {

            return view('admin/officesEdit', ['info' => $info]);

        } else {

            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:cx_office,name,' . $request->id,
            ], [
                'name.required' => '办事处必填',
                'name.unique' => '办事处已存在',
            ]);
            if ($validator->fails()) {
                return $this->showJson('9999', $validator->errors()->first());
            }

            $update_data = $request->all();
            unset($update_data['_token']);

            $update_data['update_time'] = time();

            $info->update($update_data);

            return $this->showJson('0000', '操作成功');

        }
    }

    public function officesDelete(Request $request)
    {
        $count = DB::table('cx_dealers')->whereIn('office_id', $request->ids)->count();
        if ($count > 0) {
            return $this->showJson('9999', '该办事处已绑定经销商');
        }
        $count = DB::table('cx_saler')->whereIn('office_id', $request->ids)->count();
        if ($count > 0) {
            return $this->showJson('9999', '该办事处已绑定促销员');
        }
        DB::table('cx_office')->whereIn('id', $request->ids)->delete();
        return $this->showJson('0000', '操作成功');
    }
}
