<?php

namespace App\Services;

use Qcloud\Sms\SmsSingleSender;

class QcloudSms {
    private $appid; // 短信应用 SDK AppID , 1400开头
    private $appkey; // 短信应用 SDK AppKey
    private $templateId; // 短信模板 ID，需要在短信应用中申请
    private $smsSign; // 签名参数使用的是`签名内容`，而不是`签名ID`

    // 自定义短信发送结果的错误信息
    // 以下是 对 常见错误进行 提醒，如需更多，请根据自己的需求定义；
    // 完整错误信息 详查：https://cloud.tencent.com/document/product/382/3771
    private $customEerrMsgs = [
        1016 => '手机号格式错误',
        1023 => '60秒内最多获取1条短信',
        1024 => '1小时内最多获取5条短信',
        1025 => '1天最多获取10条短信',
        1031 => '短信余量不足，请联系管理员',
        1033 => '短信余量不足，请联系管理员',
    ];

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
    public function send($phone, $sms_type) {

        try {

            $ssender = new SmsSingleSender($this->appid, $this->appkey);
            $sms_code = rand(100000, 999999);
            $cache_time = env('TXY_SMS_CACHETIME');
            $params = [$sms_code, $cache_time];// 数组具体的元素个数和模板中变量个数必须一致，例如示例中 templateId:5678 对应一个变量，参数数组中元素个数也必须是一个
            $result = $ssender->sendWithParam("86", $phone, $this->templateId, $params, $this->smsSign, "", "");

            $rsp = json_decode($result, true);

            \Log::error('短信发送结果：$rsp' . json_encode($rsp, JSON_UNESCAPED_UNICODE));

            // 处理发送结果
            if($rsp['result'] === 0 ) {
                // 发送成功

                $status = 1;

                $code = 0;
                $msg = '短信发送成功';

                \Cache::put($phone, $sms_code, $cache_time); // 缓存

            } else {
                // 发送失败

                $status = 2;

                $code = 1;
                $msg = $this->customEerrMsgs[$rsp['result']] ?? '短信发送失败';

            }

            $this->makeSmsCode($phone, $sms_code, $sms_type, $cache_time, $status, $rsp);

            return ['code' => $code, 'msg' => $msg, 'sms_code' => $sms_code];
            
        } catch(\Exception $e) {
            return ['code' => 1, 'msg' => '网络异常'];
        }

    }

    // 生成短信记录
    public function makeSmsCode($phone, $sms_code, $sms_type, $cache_time, $status, $result)
    {
        $sms_code_insert_data = [
            'phone' => $phone,
            'code' => $sms_code, // 短信验证码
            'type' => $sms_type, // 短信类型：1 登录注册，2 忘记密码， 3 数据上报
            'cache_time' => $cache_time, // 短信有效时间
            'status' => $status, // 发送状态：0 未发送，1 发送成功，2 发送失败
            'errmsg' => $result['errmsg'], // 运营商返回的错误信息
            'result' => json_encode($result, JSON_UNESCAPED_UNICODE), // 运营商返回的全部结果
            'create_time' => time(),
            'created_at' => date('Y-m-d H:i:s', time()),
        ];
        \DB::table('cx_sms_code')->insert($sms_code_insert_data);
    }

}