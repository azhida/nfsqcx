<?php

namespace App\Http\Controllers\Index;

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
            'phone' => 'required',
        ], [
            'phone.required' => '手机号码缺失',
        ]);

        if ($validator->fails()) {
            return $this->showJson('9999', $validator->errors()->first());
        }

        $appid = env('TXY_SMS_APPID'); // 短信应用SDK AppID,1400开头
        $appkey = env('TXY_SMS_APPKEY'); // 短信应用SDK AppKey
        $phoneNumbers = [strval($request->phone)]; // 需要发送短信的手机号码
        $templateId = env('TXY_SMS_TEMPLATEID');  // 短信模板ID，需要在短信控制台中申请
        $smsSign = env('TXY_SMS_SMSSIGN'); // 签名, 签名参数使用的是`签名内容`，而不是`签名ID`
        $msender = new SmsMultiSender($appid, $appkey);
        $code = rand(100000, 999999);
        $params = [strval($code),"30"];
        // 签名参数未提供或者为空时，会使用默认签名发送短信
        $result = $msender->sendWithParam("86", $phoneNumbers, $templateId, $params, $smsSign, "", "");
        $rsp = json_decode($result,true);
        if($rsp['result'] === 0 ) {
            DB::table('cx_sms_code')->insert(['phone' => $request->phone, 'code' => $code, 'type' => $request->sms_type ?? 1, 'create_time' => time()]);
            Cache::put($request->phone, $code, env('TXY_SMS_CACHETIME'));
            return $this->showJson("0000", "发送短信成功" . $code);
        } else {
            return $this->showJson("9999", "发送短信失败");
        }
    }

}
