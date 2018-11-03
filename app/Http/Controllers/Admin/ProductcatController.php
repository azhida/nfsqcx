<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductcatController extends Controller
{
    public function productCatList(Request $request)
    {
        $query = DB::table('cx_product_cat');
        if ($request->start) {
            $query = $query->where('create_time', '>=', strtotime($request->start));
        }
        if ($request->end) {
            $query = $query->where('create_time', '<=', strtotime($request->end));
        }
        if ($request->name) {
            $query = $query->where('name', 'like', '%' . $request->name . '%');
        }


        $product_cat_list = $query->orderBy('id', 'DESC')->paginate(10);

        return view('admin/productCatList', ['list' => $product_cat_list, 'search_params' => $request->all()]);

    }

    public function productCatAdd(Request $request)
    {

        if ($request->isMethod('get')) {
            return view('admin/productCatAdd');
        } else {

            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:cx_product_cat,name',
            ], [
                'name.required' => '产品分类名称必填',
                'name.unique' => '产品分类名称已存在',
            ]);
            if ($validator->fails()) {
                return $this->showJson('9999', $validator->errors()->first());
            }

            $insert_data = [
                'name' => $request->name,
                'create_time' => time(),
                'update_time' => time(),
            ];

            $id = DB::table('cx_product_cat')->insertGetId($insert_data);
            if ($id) {
                return $this->showJson('0000', '操作成功');
            } else {
                return $this->showJson('9999', '操作失败');
            }

        }
    }

    public function productCatEdit(Request $request)
    {
        if ($request->isMethod('get')) {

            $info = DB::table('cx_product_cat')->where('id', $request->id)->first();

            return view('admin/productCatEdit', ['info' => $info]);

        } else {

            $update_data = $request->all();
            unset($update_data['_token']);
            if ($request->name) {
                // 先查 修改的 账号 是否已经存在
                $count = DB::table('cx_product_cat')->where('id', '<>', $request->id)->where('name', $request->name)->count();
                if ($count) return $this->showJson('9999', '产品分类已经在');
            }

            $update_data['update_time'] = time();
            DB::table('cx_product_cat')->where('id', $request->id)->update($update_data);

            return $this->showJson('0000', '操作成功');

        }
    }

    public function productCatDelete(Request $request)
    {
        $ids = $request->ids ?? 0;
        $count = DB::table('cx_product')->whereIn('cat_id', $ids)->count();
        if ($count) {
            return $this->showJson('9999', '要删除的分类下已绑定产品');
        }

        DB::table('cx_product_cat')->whereIn('id', $request->ids)->delete();
        return $this->showJson('0000', '操作成功');
    }
}
