<?php

function echoOk($status, $msg='', $return='') {
    $arr = array(
        'status' => $status,
        'msg' => $msg,
        'result' => $return
    );
    echo json_encode($arr);
    die;
}

/*function uplodeImg($img) {
    if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $img, $result)) {
        $type = $result[2];
        $jpgdata = base64_decode(str_replace($result[1], '', $img)); // 对头像图片进行解码
    } else {
        $type = "jpg";
        $jpgdata = base64_decode($img); // 对头像图片进行解码
    }
    $thumbname= time().mt_rand(10000,99999);
    $filename = $thumbname.".".$type;
    $path = C('uploadpath').'/'.date('Ymd').'/'.$filename;
    mkdir(dirname($path));
    file_put_contents ($path, $jpgdata);
    if(file_exists($path))
    {
        return $path;
    }else{
        return '插入文件失败';
    }
}*/

/*function httpImg($img) {
    $imgarray=explode('/Public/',$img);
    $count=count($imgarray)-1;
    $img1= C('webaddress').$imgarray[$count];
    return $img1;
}

function httpImg1($img) {
    $imgarray=explode('/Public/',$img);
    $count=count($imgarray)-1;
    $img1= C('webaddress').'Public/'.$imgarray[$count];
    return $img1;
}*/

/*function Post($curlPost,$url){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_NOBODY, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
    $return_str = curl_exec($curl);
    curl_close($curl);
    return $return_str;
}
function xml_to_array($xml){
    $reg = "/<(\w+)[^>]*>([\\x00-\\xFF]*)<\\/\\1>/";
    if(preg_match_all($reg, $xml, $matches)){
        $count = count($matches[0]);
        for($i = 0; $i < $count; $i++){
            $subxml= $matches[2][$i];
            $key = $matches[1][$i];
            if(preg_match( $reg, $subxml )){
                $arr[$key] = xml_to_array( $subxml );
            }else{
                $arr[$key] = $subxml;
            }
        }
    }
    return $arr;
}
function random($length = 6 , $numeric = 0) {
    PHP_VERSION < '4.2.0' && mt_srand((double)microtime() * 1000000);
    if($numeric) {
        $hash = sprintf('%0'.$length.'d', mt_rand(0, pow(10, $length) - 1));
    } else {
        $hash = '';
        $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789abcdefghjkmnpqrstuvwxyz';
        $max = strlen($chars) - 1;
        for($i = 0; $i < $length; $i++) {
            $hash .= $chars[mt_rand(0, $max)];
        }
    }
    return $hash;
}*/







