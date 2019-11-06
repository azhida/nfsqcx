<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsCode extends Model
{
    protected $table = 'cx_sms_code';

    const STATUS_0 = '0';
    const STATUS_1 = '1';
    const STATUS_2 = '2';
    public static $statusMap = [
        self::STATUS_0 => '未发送',
        self::STATUS_1 => '发送成功',
        self::STATUS_2 => '发送失败',
    ];

    protected $appends = [
        'status_text',
    ];

    public function getStatusTextAttribute()
    {
        return self::$statusMap[$this->status] ?? '';
    }
}
