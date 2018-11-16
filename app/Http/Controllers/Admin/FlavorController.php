<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FlavorController extends Controller
{
    public function flavorList(Request $request)
    {
        $query = DB::table('cx_flavor');
        if ($request->start) {
            $query = $query->where('create_time', '>=', strtotime($request->start));
        }
        if ($request->end) {
            $query = $query->where('create_time', '<=', strtotime($request->end));
        }
        if ($request->name) {
            $query = $query->where('name', 'like', '%' . $request->name . '%');
        }
        if ($request->cat_id) {
            $query = $query->where('cat_id', '=', $request->cat_id);
        }

        $flavor_list = $query->orderBy('id', 'DESC')->paginate(10);

        $cat_list = DB::table('cx_product_cat')->orderBy('id', 'DESC')->get();

        foreach ($flavor_list as &$value) {
            foreach ($cat_list as $v) {
                if ($v->id == $value->cat_id) {
                    $value->cat_name = $v->name;
                }
            }
        }

        return view('admin/flavorList', ['list' => $flavor_list, 'search_params' => $request->all(), 'cat_list' => $cat_list]);
    }

    public function flavorAdd(Request $request)
    {
        if ($request->isMethod('get')) {
            $cat_list = DB::table('cx_product_cat')->orderBy('id', 'DESC')->get();
            return view('admin/flavorAdd', ['cat_list' => $cat_list]);
        } else {

            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:cx_flavor,name',
            ], [
                'name.required' => '产品口味名称必填',
                'name.unique' => '产品口味名称已存在',
            ]);
            if ($validator->fails()) {
                return $this->showJson('9999', $validator->errors()->first());
            }

            $insert_data = [
                'cat_id' => $request->cat_id,
                'name' => $request->name,
                'create_time' => time(),
                'update_time' => time(),
            ];

            $id = DB::table('cx_flavor')->insertGetId($insert_data);
            if ($id) {
                return $this->showJson('0000', '操作成功');
            } else {
                return $this->showJson('9999', '操作失败');
            }
        }

    }

    public function flavorEdit(Request $request)
    {
        if ($request->isMethod('get')) {
            $info = DB::table('cx_flavor')->where('id', $request->id)->first();
            $cat_list = DB::table('cx_product_cat')->orderBy('id', 'DESC')->get();
            return view('admin/flavorEdit', ['info' => $info, 'cat_list' => $cat_list]);
        } else {

            $update_data = $request->all();
            unset($update_data['_token']);
            if ($request->name) {
                // 先查 修改的 账号 是否已经存在
                $count = DB::table('cx_flavor')->where('id', '<>', $request->id)->where('name', $request->name)->count();
                if ($count) return $this->showJson('9999', '产品分类已经在');
            }

            $update_data['update_time'] = time();
            DB::table('cx_flavor')->where('id', $request->id)->update($update_data);

            return $this->showJson('0000', '操作成功');
        }

    }

    public function flavorDelete(Request $request)
    {
        $ids = $request->ids ?? 0;
        $count = DB::table('cx_product')->whereIn('flavor_id', $ids)->count();
        if ($count) {
            return $this->showJson('9999', '要删除的口味下已绑定产品');
        }

        DB::table('cx_flavor')->whereIn('id', $request->ids)->delete();
        return $this->showJson('0000', '操作成功');
    }

    // 根据 cat_id 获取 数据
    public function getFlavorListByCatId(Request $request)
    {
        $cat_id = $request->cat_id ?? 0;
        if (!$cat_id) {
            return $this->showJson('9999', '产品分类错误');
        }

        $flavor_list = DB::table('cx_flavor')->where('cat_id', $cat_id)->get();
        return $this->showJson('0000', '数据获取成功', $flavor_list);
    }

}
