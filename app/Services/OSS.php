<?php

namespace App\Services;

use OSS\Core\OssException;
use OSS\OssClient;

class OSS {
    private $accessKeyId = '';
    private $accessKeySecret = '';
    private $endpoint = '';
    private $bucket = '';
    private $ossClient;

    public function __construct()
    {
        $this->accessKeyId = env('OSS_ACCESS_KEY_ID');
        $this->accessKeySecret = env('OSS_ACCESS_KEY_SECRET');
        $this->endpoint = env('OSS_ENDPOINT');
        $this->bucket = env('OSS_BUCKET');
        $this->ossClient = new OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint);
    }

    /**
     * 上传文件
     * @param string $save_path 保存的文件夹，如 product，前面不带 /
     * @param string $file_name 要上传的文件，必须是绝对路径
     * @return array
     */
    public function uploadFile($save_path = '', $file_name = '')
    {
        $save_path = $save_path . '/' . date('Ymd') . '/' . md5(time() . $file_name) . '.' . $this->getExtension($file_name);
        try{
            $res = $this->ossClient->uploadFile($this->bucket, $save_path, $file_name);
        } catch(OssException $e) {
            return ['code' => '1', 'msg' => $e->getMessage()];
        }
        return ['code' => '0', 'msg' => '上传成功', 'file_name' => $res['info']['url']];
    }

    /**
     * 删除文件
     * @param string $file_name
     * @return array
     */
    public function deleteFile($file_name = '')
    {
        try{
            $this->ossClient->deleteObject($this->bucket, $file_name);
        } catch(OssException $e) {
            return ['code' => '1', 'msg' => $e->getMessage()];
        }
        return ['code' => '0', 'msg' => '删除成功'];
    }

    // 获取文件扩展名
    public function getExtension($file_name)
    {
        return pathinfo($file_name, PATHINFO_EXTENSION);
    }
}