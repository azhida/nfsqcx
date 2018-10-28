<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
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

}
