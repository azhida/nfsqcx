<?php

namespace App\Http\Controllers;

use App\Services\OSS;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommonController extends Controller
{
    // 将 账户表 合并到 办事处表中
    public function mergeAccountToOffice()
    {
        $saler_list = DB::table('cx_saler')->get();

        foreach ($saler_list as $value) {
            DB::table('cx_office')->where('id', $value->office_id)->update(['account' => $value->account, 'password' => $value->password, 'salt' => $value->salt, 'update_time' => time()]);
        }


        dd($saler_list);
    }

    // 将产品图片上传oss
    public function uploadProductImgToOss()
    {
        $product_list = DB::table('cx_product')->get();
        $oss = new OSS();
        foreach ($product_list as $value) {
            $res = $oss->uploadFile('product', public_path() . '/static/' . $value->img_url);
            if ($res && $res['code'] == 0) {
                $oss_img_url = $res['file_name'] ?? '';
                DB::table('cx_product')->where('id', $value->id)->update(['oss_img_url' => $oss_img_url]);
            }
        }
    }

    // 将 用户打卡上传的 图片 上传至 oss
    public function uploadSignImgToOss()
    {
        $sign_list = DB::table('cx_sign')->orderBy('id', 'DESC')->limit(1)->get();
        foreach ($sign_list as $value) {
            $img_array = unserialize($value->img);
            foreach ($img_array as $img) {

                $img = explode('|', $img);

                if ($value->type == 1) { // 上班
                    $save_path = 'sign/clock_in';
                } else { // 下班
                    $save_path = 'sign/clock_out';
                }

                $oss = new OSS();
                $res = $oss->uploadFile($save_path, public_path() . $img[1]);
                if ($res && $res['code'] == 0) {
                    $oss_img_url = $res['file_name'] ?? '';

                    $sign_update_data = '';
                    if ($img[0] == 1) {
                        $sign_update_data = ['oss_img_1' => $oss_img_url];
                    } elseif ($img[0] == 2) {
                        $sign_update_data = ['oss_img_2' => $oss_img_url];
                    } elseif ($img[0] == 3) {
                        $sign_update_data = ['oss_img_3' => $oss_img_url];
                    }

                    DB::table('cx_sign')->where('id', $value->id)->update($sign_update_data);
                }
            }
        }
    }
}
