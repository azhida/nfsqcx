<?php

/**
 * 将 base64 图片实体转化并保存，返回 保存的图片路径
 * @param string $savepath      图片保存的路径，不是全路径
 * @param string $img           base64图片实体，含base64图片头
 * @param array $types          图片类型
 * @return array
 */
function img64_transform($save_path = 'common', $img, $types = []){

    $save_path = '/' . $save_path . '/' . date('Y') . '/' . date('m') . '/' . date('d');
    $fullpath = storage_path() . $save_path;
    if(!is_dir($fullpath)){
        mkdir($fullpath, 0777, true);
    }
    $types = empty($types) ? ['jpg', 'gif', 'png', 'jpeg'] : $types;
    $img = str_replace(['_', '-'], ['/', '+'], $img);
    $b64img = substr($img, 0,100);
    if(preg_match('/^(data:\s*image\/(\w+);base64,)/', $b64img, $matches)){
        $type = $matches[2];
        if(!in_array($type, $types)){
            return ['error' => 1, 'msg' => '图片格式不正确', 'url' => ''];
        }
        $img = str_replace($matches[1], '', $img);
        $img = base64_decode($img);
        $photo = '/' . md5(date('YmdHis') . rand(1000, 9999)) . '.' . $type;
        file_put_contents($fullpath . $photo, $img);
        return ['error' => 0, 'msg' => '保存图片成功', 'url' => $save_path . $photo];
    }
    return ['error' => 2, 'msg' => '网络异常'];

}


function getOssWatermark($oss_img_url, $text = '', $img_width = 500, $watermark_position = 'ne') {

    // 图片大小
    $oss_img_url .= '?x-oss-process=image/resize,w_' . $img_width;

    // 水印文字
    $text = $text == '' ? ('【农夫山泉】' . date('Y-m-d H:i', time())) : $text;

    // 安全转码
    $text = base64_encode($text);
    $text = str_replace(array('+','/','='),array('-','_',''), $text);

    // 水印文字大小
    $size = floor($img_width / 2 / 18);

    // $watermark_position 水印的位置，取值范围：[nw,north,ne,west,center,east,sw,south,se]

    $oss_img_url .= '/watermark,text_' . $text . ',g_' . $watermark_position . ',size_' . $size;

    return $oss_img_url;

}