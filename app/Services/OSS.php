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
        $save_path = $save_path . '/' . date('Y') . '/' . date('m') . '/' . date('m') . md5(time() . $file_name) . '.' . $this->getExtension($file_name);
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

    /**
     * 下载文件
     * @param string $object oss上的文件名称，表示您在下载文件时需要指定的文件名称，如abc/efg/123.jpg。
     * @param string $localfile 指定下载到本地的文件路径（指定下载到本地的文件名称）
     */
    public function downloadFile($object = '', $localfile = '')
    {
        if (!$object || !$localfile) return ['code' => 1, 'msg' => '文件下载失败'];

        $localfile_array = explode('/', $localfile);

        array_pop($localfile_array); // 去掉数组最后一个元素

        $save_path = join('/', $localfile_array); // 重新拼装 保存路径
        if ($localfile_array[0] != 'common') {
            $save_path = 'common/' . $save_path;
            $localfile = 'common/' . $localfile;
        }

        if (!is_dir($save_path)) { // 不存在则创建
            mkdir($save_path, 0777, true);
        }

        $options = array(
            OssClient::OSS_FILE_DOWNLOAD => $localfile
        );

        try{
            $this->ossClient->getObject($this->bucket, $object, $options);
        } catch(OssException $e) {
            return ['code' => '1', 'msg' => $e->getMessage()];
        }
        return ['code' => '0', 'msg' => '文件下载成功', 'file_url' => config('app.url') . '/' . $localfile, 'file_name' => '/' . $localfile];
    }

    // 获取文件扩展名
    public function getExtension($file_name)
    {
        return pathinfo($file_name, PATHINFO_EXTENSION);
    }
}