<?php
/**
 * json格式
 *
 * @param unknown_type $status
 * @param unknown_type $msg
 * @param unknown_type $type
 * @param unknown_type $data
 */
function get_api_result($status, $msg, $arr = array(), $type = 'json')
{
    header("Content-Type:application/json");
    if ($type == 'json') {
        if ($status == 200) {
            echo json_encode(array('code' => $status, 'msg' => $msg, 'result' => $arr, 'time' => time()));
            die;
        } else if ($status >= 300) {
            echo json_encode(array('code' => $status, 'msg' => $msg, 'result' => (object)$arr, 'time' => time()));
            die;
        } else {
            echo json_encode(array('code' => 'Err', 'msg' => 'status 不存在', 'time' => time()));
            die;
        }
    }
}

/**
 * 对象数组转为普通数组
 *
 * AJAX提交到后台的JSON字串经decode解码后为一个对象数组，
 * 为此必须转为普通数组后才能进行后续处理，
 * 此函数支持多维数组处理。
 *
 * @param array
 * @return array
 */
function objarray_to_array($obj)
{
    $ret = array();
    foreach ($obj as $key => $value) {
        if (gettype($value) == "array" || gettype($value) == "object") {
            $ret[$key] = objarray_to_array($value);
        } else {
            $ret[$key] = $value;
        }
    }
    return $ret;
}

/**
 * 获取请求数据
 * @return array|mixed
 */
function getRequest()
{
    $data = array();
    if (!empty($GLOBALS['HTTP_RAW_POST_DATA'])) {
        $data = array_merge($data, json_decode($GLOBALS['HTTP_RAW_POST_DATA'], true));
    }
    if (!empty($_POST)) {
        $data = array_merge($data, $_POST);
    }
    if (!empty($_GET)) {
        $data = array_merge($data, $_GET);
    }
    return $data;
}


/**
 * 上传图片
 * @param $img
 * @return string
 */
function uplodeImg($img, $savefilepath)
{
    if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $img, $result)) {
        $type = $result[2];
        $jpgdata = base64_decode(str_replace($result[1], '', $img)); // 对头像图片进行解码
    } else {
        $type = "jpg";
        $jpgdata = base64_decode($img); // 对头像图片进行解码
    }
    $thumbname = time() . mt_rand(10000, 99999);
    $filename = $thumbname . "." . $type;
    $path = C('uploadpath') . '/' . $savefilepath . '/' . $filename;
    mkdir(dirname($path), 0777);
    file_put_contents($path, $jpgdata);
    return $path;
}

/**
 * 图片补全路径
 */
function imgpath($img)
{
    if (empty($img)) {
        return '';
    } else {
        return 'http://' . $_SERVER['HTTP_HOST'] . '' . __ROOT__ . '/' . $img;
    }
}


/**
 * 编辑器内容图片替换
 * @param unknown $content
 * @return boolean
 */
function _str_replace($content)
{
    if (!empty($content)) {
        $str = 'src="';
        return str_replace($str, $str . 'http://' . $_SERVER['HTTP_HOST'], $content);
    }
    return true;
}

/**
 * 验证手机号是否正确
 * @param String $mobile
 */
function isMobile($mobile)
{
    if (!is_numeric($mobile)) {
        return false;
    }
    return preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#', $mobile) ? true : false;
}


/**
 * 验证邮箱规则
 * @param String $mobile
 */
function isEmail($email)
{
    return preg_match('/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/ ', $email) ? true : false;
}

/**
 * 短信发送
 * @param String $mobile
 */
function xml_to_array($xml)
{
    $reg = "/<(\w+)[^>]*>([\\x00-\\xFF]*)<\\/\\1>/";
    if (preg_match_all($reg, $xml, $matches)) {
        $count = count($matches[0]);
        for ($i = 0; $i < $count; $i++) {
            $subxml = $matches[2][$i];
            $key = $matches[1][$i];
            if (preg_match($reg, $subxml)) {
                $arr[$key] = xml_to_array($subxml);
            } else {
                $arr[$key] = $subxml;
            }
        }
    }
    return $arr;
}


function Post($curlPost, $url)
{
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


/**
 * 生成订单号
 * @return string
 */
function build_order_no()
{
    return date('Ymd') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
}


/**
 * @param int $time
 * @param string $test
 * @return string
 * 例如得到一小时前
 */
function put_time($time = 0,$test=''){
    if(empty($time)){
        return $test;
    }
    $time = substr($time,0,10);
    $ttime = time() - $time;
    if($ttime <= 0 || $ttime < 60){
        return '几秒前';
    }
    if($ttime > 60 && $ttime <120){
        return '1分钟前';
    }

    $i = floor($ttime / 60);                            //分
    $h = floor($ttime / 60 / 60);                       //时
    $d = floor($ttime / 86400);                         //天
    $m = floor($ttime / 2592000);                       //月
    $y = floor($ttime / 60 / 60 / 24 / 365);            //年
    if($i < 30){
        return $i.'分钟前';
    }
    if($i > 30 && $i < 60){
        return '一小时内';
    }
    if($h>=1 && $h < 24){
        return $h.'小时前';
    }
    if($d>=1 && $d < 30){
        return $d.'天前';
    }
    if($m>=1 && $m < 12){
        return $m.'个月前';
    }
    if($y){
        return $y.'年前';
    }
    return "";
}




