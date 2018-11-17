<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ActivityitemController extends Controller
{
    public function activityitemList(Request $request)
    {
        $query = DB::table('cx_activity_item');
        if ($request->start) {
            $query = $query->where('create_time', '>=', strtotime($request->start));
        }
        if ($request->end) {
            $query = $query->where('create_time', '<=', strtotime($request->end));
        }
        if ($request->name) {
            $query = $query->where('name', 'like', '%' . $request->name . '%');
        }


        $activity_item_list = $query->orderBy('id', 'ASC')->paginate(10);

        return view('admin/activityitemList', ['list' => $activity_item_list, 'search_params' => $request->all()]);
    }

    public function activityitemAdd(Request $request)
    {
        if ($request->isMethod('get')) {
            return view('admin/activityitemAdd');
        } else {

            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:cx_activity_item,name',
            ], [
                'name.required' => '活动品项名称必填',
                'name.unique' => '活动品项名称已存在',
            ]);
            if ($validator->fails()) {
                return $this->showJson('9999', $validator->errors()->first());
            }

            $insert_data = [
                'name' => $request->name,
                'create_time' => time(),
                'update_time' => time(),
            ];

            $id = DB::table('cx_activity_item')->insertGetId($insert_data);
            if ($id) {
                return $this->showJson('0000', '操作成功');
            } else {
                return $this->showJson('9999', '操作失败');
            }

        }
    }

    public function activityitemEdit(Request $request)
    {
        if ($request->isMethod('get')) {
            $info = DB::table('cx_activity_item')->where('id', $request->id)->first();
            return view('admin/activityitemEdit', ['info' => $info]);
        } else {

            $update_data = $request->all();
            unset($update_data['_token']);
            if ($request->name) {
                // 先查 修改的 账号 是否已经存在
                $count = DB::table('cx_activity_item')->where('id', '<>', $request->id)->where('name', $request->name)->count();
                if ($count) return $this->showJson('9999', '产品分类已经在');
            }

            $update_data['update_time'] = time();
            DB::table('cx_activity_item')->where('id', $request->id)->update($update_data);

            return $this->showJson('0000', '操作成功');

        }
    }

    public function activityitemDelete(Request $request)
    {
        DB::table('cx_activity_item')->whereIn('id', $request->ids)->delete();
        return $this->showJson('0000', '操作成功');
    }
}
