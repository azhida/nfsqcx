<?php

namespace App\Services;

use OSS\Core\OssException;
use OSS\OssClient;

class OSS {
    private $accessKeyId = '';
    private $accessKeySecret = '';
    private $endpoint = '';
    private $bucket = '';

    public function __construct()
    {
        $this->accessKeyId = env('OSS_ACCESS_KEY_ID');
        $this->accessKeySecret = env('OSS_ACCESS_KEY_SECRET');
        $this->endpoint = env('OSS_ENDPOINT');
        $this->bucket = env('OSS_BUCKET');
    }

    // 获取文件扩展名
    public function getExtension($file_name)
    {
        return pathinfo($file_name, PATHINFO_EXTENSION);
    }

    public function uploadFile1($save_path = '', $file_name = '')
    {
        $save_path = $save_path . '/' . date('Ymd') . '/' . time() . '.' . $this->getExtension($file_name);
        try{
            $ossClient = new OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint);
            $res = $ossClient->uploadFile($this->bucket, $save_path, $file_name);
        } catch(OssException $e) {
            return ['code' => '1', 'msg' => $e->getMessage()];
        }
        return ['code' => '0', 'msg' => '上传成功', 'file_name' => $res['info']['url']];
    }
}