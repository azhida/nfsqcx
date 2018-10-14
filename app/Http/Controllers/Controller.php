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

}
