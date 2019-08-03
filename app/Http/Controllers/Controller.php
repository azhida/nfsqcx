<?php

namespace App\Http\Controllers;

use App\Services\OSS;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    // 应用公共文件
    public function showJson($code, $mes, $data = NULL)
    {
        if($data) {
            return response()->json(['code' => $code, 'mes' => $mes, 'data' => $data]);
        } else {
            return response()->json(['code' => $code, 'mes' => $mes]);
        }
    }

    public function getNumberSalt()
    {
        $code = '';
        //随机生成4位数字和字母混合的字符串
        $pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ';
        for($i = 0; $i < 4; $i++) {
            $code .= $pattern{mt_rand(0, 61)};
        }
        return $code;
    }
    
    // 根据 url 删除 oss图片
    public function deleteOssFile(Request $request)
    {
        $file_name = $request->file_name ?? '';
        if (!$file_name) {
            return $this->showJson('9999', '请传递正确的文件名');
        }
        $oss = new OSS();
        $res = $oss->deleteFile($file_name);
        if ($res['code'] == '0') {
            return $this->showJson('0000', $res['msg']);
        } else {
            return $this->showJson('9999', $res['msg']);
        }
    }

    // 获取 oss 文件的访问路径
    public function getOssFileUrl($file_name = '')
    {
        return 'http://' . env('OSS_BUCKET') . '.' . env('OSS_ENDPOINT') . '/' . $file_name;
    }

    // 获取 oss 的纯文件名（ 不含 域名 和 参数 ）
    public function getOssFileName($full_file_name = '')
    {
        if (!$full_file_name) return '';
        $res_1 = explode(env('OSS_ENDPOINT') . '/', $full_file_name);
        $res_2 = explode('?', $res_1[1]);
        return $res_2[0];
    }

}
