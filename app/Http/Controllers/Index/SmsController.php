<?php

namespace App\Http\Controllers\Index;

use App\Services\QcloudSms;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Qcloud\Sms\SmsMultiSender;

class SmsController extends Controller
{
    public function send(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|regex:/^1[3456789][0-9]{9}$/',
        ], [
            'phone.required' => '手机号码缺失',
            'phone.regex' => '手机号码格式错误',
        ]);

        if ($validator->fails()) {
            return $this->showJson('9999', $validator->errors()->first());
        }

        $sms = new QcloudSms();
        $res = $sms->send($request->phone, $request->sms_type ?? 1);
        if ($res['code'] == 0) {
            return $this->showJson("0000", "发送短信成功" . $res['sms_code']);
        } else {
            return $this->showJson("9999", $res['msg']);
        }
    }

}
