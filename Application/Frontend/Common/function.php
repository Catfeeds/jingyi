<?php
	//	下载
  function xiazai($file_dir, $file_name, $redName)
    // 参数说明：
    // file_dir:文件所在目录
    // file_name:文件名
    {
        $file_dir = chop($file_dir); // 去掉路径中多余的空格
                                     // 得出要下载的文件的路径
        if ($file_dir != '') {
            $file_path = $file_dir;
            if (substr($file_dir, strlen($file_dir) - 1, strlen($file_dir)) != '/')
                $file_path .= '/';
            $file_path .= $file_name;
        } else
            $file_path = $file_name;

            // 判断要下载的文件是否存在
        if (! file_exists($file_path)) {
            alert('对不起,你要下载的文件不存在');
            return false;
        }
        $file_size = filesize($file_path);
        header("Content-type: application/octet-stream;charset=gbk");
        header("Accept-Ranges: bytes");
        header("Accept-Length: $file_size");
        header("Content-Disposition: attachment; filename=" . $redName);

        $fp = fopen($file_path, "r");
        $buffer_size = 1024;
        $cur_pos = 0;

        while (! feof($fp) && $file_size - $cur_pos > $buffer_size) {
            $buffer = fread($fp, $buffer_size);
            echo $buffer;
            $cur_pos += $buffer_size;
        }

        $buffer = fread($fp, $file_size - $cur_pos);
        echo $buffer;
        fclose($fp);
        return true;
    }


/**
 * 图片路径替换（全路径换成半路径）
 * @param unknown $imgpath
 * @return boolean
 */
function img_path_str_replace($imgpath){
	if(!empty($imgpath)){
		$arrpath=explode('Public/',$imgpath);
		return "Public/".$arrpath[1];
	}
	return true;
}

/**
 * 数字转字母
 * @param $num
 * @return string
 */
function getLetter($num) {
    $str = "$num";
    $num = intval($num);
    if ($num <= 26){
        $ret = chr(ord('A') + intval($str) - 1);
    } else {
        $first_str = chr(ord('A') + intval(floor($num / 26)) - 1);
        $second_str = chr(ord('A') + intval($num % 26) - 1);
        if ($num % 26 == 0){
            $first_str = chr(ord('A') + intval(floor($num / 26)) - 2);
            $second_str = chr(ord('A') + intval($num % 26) + 25);
        }
        $ret = $first_str.$second_str;
    }
    return $ret;
}

/**
 * 生成uuid
 * @param string $prefix
 * @return string
 */
function uuid($prefix = ""){    //可以指定前缀
    $str = md5(uniqid(mt_rand(), true));
    $uuid  = substr($str,0,2);
    $uuid .= substr($str,8,2);
    $uuid .= substr($str,12,2);
    return $prefix . $uuid;
}

/**
 * @param $content
 * @return bool
 * 日志
 */
function admin_log($content){
    if(!$content){
        return false;
    }
    $data['adminid']=session('admin_key_id');
    $data['addtime']=time();
    $data['content']=$content;
    $res=M('option')->add($data);
    if(!$res){
        return false;
    }
    return true;
}

/**
 * @param string $str
 * @return mixed|string
 * 敏感词屏蔽
 */
function shielding($str=''){
    $res=M('shielding')->field('content')->select();
    foreach ($res as $item){
        $num=strlen($item['content']);
        $star='';
        for($i=0;$i<$num;$i++){
            $star.='*';
        }
        $pattern='/'.$item['content'].'/';
        $str=preg_replace($pattern, $star, $str);
    }
    return $str;
}
//获取一级功能模块名称
function getOneFunctionModel(){
    $res = M('group_model')->select();
    return $res;
}

/**
 * 替换百度编辑器图片地址为域名地址
 */
function replace_img($content){
    if(preg_match('/http:\/\/yi.com\/ueditor\/php\/upload\/image/',$content)){
        return $content;
    }
    $result=preg_replace('/\/ueditor\/php\/upload\/image/', "http://yi.com/ueditor/php/upload/image", $content);
    return $result;
}
/**
 * 获取首页内容
 */
function get_home($type,$id=0){
    if($type==0){
        $res=M('home')->select();
        return $res;
    }elseif ($type==1){
        $res=M('home')->where(['id'=>$id])->find();
        return $res;
    }
    return false;
}
