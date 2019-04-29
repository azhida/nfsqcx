<?php

namespace App\Services;

use Qcloud\Sms\SmsSingleSender;

class QcloudSms {
    private $appid; // 短信应用 SDK AppID , 1400开头
    private $appkey; // 短信应用 SDK AppKey
    private $templateId; // 短信模板 ID，需要在短信应用中申请
    private $smsSign; // 签名参数使用的是`签名内容`，而不是`签名ID`

    public function __construct()
    {
        $this->appid = env('TXY_SMS_APPID');
        $this->appkey = env('TXY_SMS_APPKEY');
        $this->templateId = env('TXY_SMS_TEMPLATEID');
        $this->smsSign = env('TXY_SMS_SMSSIGN');
    }

    /**
     * 发送短信
     * @param $phone
     * @return array
     */
    public function send($phone) {

        try {
            $ssender = new SmsSingleSender($this->appid, $this->appkey);
            $sms_code = rand(100000, 999999);
            $cache_time = env('TXY_SMS_CACHETIME');
            $params = [$sms_code, $cache_time];// 数组具体的元素个数和模板中变量个数必须一致，例如示例中 templateId:5678 对应一个变量，参数数组中元素个数也必须是一个
            $result = $ssender->sendWithParam("86", $phone, $this->templateId, $params, $this->smsSign, "", "");
            $rsp = json_decode($result, true);
            \Log::error('短信发送结果：' . json_encode($result));
            if($rsp['result'] === 0 ) {
                
                \Cache::put($phone, $sms_code, env('TXY_SMS_CACHETIME'));
                return ['code' => 0, 'msg' => '短信发送成功', 'sms_code' => $sms_code, 'cache_time' => $cache_time];
                
            } else {
                return ['code' => 1, 'msg' => '短信发送失败'];
            }
            
        } catch(\Exception $e) {
            return ['code' => 1, 'msg' => $e];
        }

    }

}