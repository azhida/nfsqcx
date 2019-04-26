<?php

namespace App\Http\Controllers\Admin;

use App\Services\OSS;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function productList(Request $request)
    {
        $query = DB::table('cx_product');

        if ($request->start) {
            $query = $query->where('create_time', '>=', strtotime($request->start));
        }
        if ($request->end) {
            $query = $query->where('create_time', '<=', strtotime($request->end));
        }
        if ($request->cat_id) {
            $query = $query->where('cat_id', '=', $request->cat_id);
        }
        if ($request->flavor_id) {
            $query = $query->where('flavor_id', '=', $request->flavor_id);
        }
        if ($request->name) {
            $query = $query->where('name', 'like', '%' . $request->name . '%');
        }

        $product_list = $query->orderBy('id', 'DESC')->paginate(10);

        // 产品分类
        $product_cat_list = DB::table('cx_product_cat')->orderBy('id', 'DESC')->get();
        // 产品口味
        $flavor_list = DB::table('cx_flavor')->orderBy('id', 'DESC')->get();

        foreach ($product_list as &$value) {
            // 产品分类
            foreach ($product_cat_list as $v) {
                if ($v->id == $value->cat_id) {
                    $value->cat_name = $v->name;
                }
            }

            // 产品口味
            foreach ($flavor_list as $v) {
                if ($v->id == $value->flavor_id) {
                    $value->flavor_name = $v->name;
                }
            }
            $value->oss_img_url = $value->oss_img_url . '?x-oss-process=image/resize,h_100';
            $value->add_time = date('Y-m-d H:i:s', $value->create_time);
            $value->update_time = date('Y-m-d H:i:s', $value->update_time);
        }

        return view('admin/productList', ['list' => $product_list, 'cat_list' => $product_cat_list, 'flavor_list' => $flavor_list, 'search_params' => $request->all()]);
    }

    public function productAdd(Request $request)
    {
        if ($request->isMethod('get')) {
            $product_cat_list = DB::table('cx_product_cat')->get();
            return view('admin/productAdd', ['product_cat_list' => $product_cat_list]);
        } else {

            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:cx_product,name',
            ], [
                'name.required' => '产品名称必填',
                'name.unique' => '产品名称已存在',
            ]);
            if ($validator->fails()) {
                return $this->showJson('9999', $validator->errors()->first());
            }

            $insert_data = [
                'cat_id' => $request->cat_id,
                'flavor_id' => $request->flavor_id,
                'name' => $request->name,
                'oss_img_url' => $request->product_img,
                'create_time' => time(),
                'update_time' => time(),
            ];

            $id = DB::table('cx_product')->insertGetId($insert_data);
            if ($id) {
                return $this->showJson('0000', '操作成功');
            } else {
                return $this->showJson('9999', '操作失败');
            }

        }
    }

    public function productEdit(Request $request)
    {
        if ($request->isMethod('get')) {
            $info = DB::table('cx_product')->where('id', $request->id)->first();
            $cat_list = DB::table('cx_product_cat')->orderBy('id', 'DESC')->get();
            $flavor_list = DB::table('cx_flavor')->orderBy('cat_id', 'DESC')->get();
            return view('admin/productEdit', ['info' => $info, 'cat_list' => $cat_list, 'flavor_list' => $flavor_list]);
        } else {

            $update_data = $request->all();
            unset($update_data['_token']);
            unset($update_data['file']);
            if ($request->name) {
                // 先查 修改的 账号 是否已经存在
                $count = DB::table('cx_product')->where('id', '<>', $request->id)->where('name', $request->name)->count();
                if ($count) return $this->showJson('9999', '产品名称已经在');
            }

            $update_data['update_time'] = time();
            DB::table('cx_product')->where('id', $request->id)->update($update_data);

            return $this->showJson('0000', '操作成功');
        }
    }

    public function productDelete(Request $request)
    {
        DB::table('cx_product')->whereIn('id', $request->ids)->delete();
        return $this->showJson('0000', '操作成功');
    }

    // 将 产品图片上传至 oss
    public function uploadProductImgToOss()
    {
        $product_list = DB::table('cx_product')->orderBy('id', 'DESC')->get();
        foreach ($product_list as $key => $value) {
            if ($value->oss_img_url) continue;
            $oss = new OSS();
            $res = $oss->uploadFile('product', public_path() . '/static/' . $value->img_url);
            Log::error('$key = ' . $key . ' ; $value = ' . json_encode($value));
            Log::error('$key = ' . $key . ' ; $res = ' . json_encode($res));
            if ($res['code'] == '0') {
                DB::table('cx_product')->where('id', $value->id)->update(['oss_img_url' => $res['file_name']]);
            }
        }
    }

    // 接受客户端上传 base64 图片，并上传 oss -- 一张
    public function uploadProductImgToOssOnlyOne(Request $request)
    {
        $file = $request->file('file');
        // 文件是否上传成功
        if ($file->isValid()) {

            $allowed_extensions = ["xls", 'png', 'jpg']; // 允许的文件类型
            if($file->getClientOriginalExtension() && !in_array($file->getClientOriginalExtension(), $allowed_extensions)) {
                return $this->showJson('9999', '不允许上传后缀为' . $file->getClientOriginalExtension() . '的文件');
            }else{

                $destinationPath = 'upload/' . date('Ymd') . '/';
                $extension = $file->getClientOriginalExtension();
                $fileName = date('YmdHis') . '.' . $extension;
                $info = $file->move($destinationPath, $fileName);
                if($info){
                    // 本地服务器保存成功
                    $file_name = public_path() . '/' . $destinationPath . $fileName;

                    // 上传oss
                    $oss = new OSS();
                    $res = $oss->uploadFile('product', $file_name);
                    unlink($file_name);
                    if ($res['code'] == '0') {
                        // oss上传成功
                        return $this->showJson('0000', $res['msg'], $res['file_name']);
                    } else {
                        // oss上传失败
                        return $this->showJson('9999', $res['msg']);
                    }

                }else{
                    return $this->showJson('9999', '上传失败');
                }

            }
        }
    }
}
