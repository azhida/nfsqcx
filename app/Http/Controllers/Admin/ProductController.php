<?php

namespace App\Http\Controllers\Admin;

use App\Services\OSS;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
            $value->img_url = 'http://www.nfsqcx.com/static/' . $value->img_url;
            $value->add_time = date('Y-m-d H:i:s', $value->create_time);
            $value->update_time = date('Y-m-d H:i:s', $value->update_time);
        }

        return view('admin/productList', ['list' => $product_list, 'cat_list' => $product_cat_list, 'flavor_list' => $flavor_list, 'search_params' => $request->all()]);

    }

    public function productAdd(Request $request)
    {

    }

    public function productEdit(Request $request)
    {

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
            $res = $oss->uploadFile1('product', public_path() . '/static/' . $value->img_url);
            Log::error('$key = ' . $key . ' ; $value = ' . json_encode($value));
            Log::error('$key = ' . $key . ' ; $res = ' . json_encode($res));
            if ($res['code'] == '0') {
                DB::table('cx_product')->where('id', $value->id)->update(['oss_img_url' => $res['file_name']]);
            }
        }
    }
}
