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
 * @param $content
 * @return null|string|string[]
 */
function replace_img($content){
    if(preg_match('/http:\/\/'.C('imgpath').'\/ueditor\/php\/upload\/image/',$content)){
        return $content;
    }
    $result=preg_replace('/\/ueditor\/php\/upload\/image/', "http://".C('imgpath')."/ueditor/php/upload/image", $content);
    return $result;
}

/**
 * 替换百度编辑器视频地址为域名地址
 * @param $content
 * @return null|string|string[]
 */
function replace_video($content){
    if(preg_match('/http:\/\/'.C('imgpath').'\/ueditor\/php\/upload\/video/',$content)){
        return $content;
    }
    $result=preg_replace('/\/ueditor\/php\/upload\/video/', "http://".C('imgpath')."/ueditor/php/upload/video", $content);
    return $result;
}

/**
 * @param $code
 * @param $data
 * @param string $msg
 * 返回json格式数据
 */
function jsonReturn($code,$msg='',$data=[]){
    if(trim($code)){
        header('Content-Type:application/json; charset=utf-8');
        $arr = array('errcode'=>$code,'data'=>$data,'msg'=>$msg,'time'=>time());
        exit(json_encode($arr,JSON_UNESCAPED_UNICODE));
    }else{
        exit('request error: not code！');
    }
}

/**
 * @param $name name
 * @param $path  路径
 * @param array $exts arr文件类型
 * @return string   图片名称
 * api 单张图片上传
 */
function upload_file($name,$path,$exts=array('jpg', 'gif', 'png', 'jpeg')){
    $upload = new \Think\Upload();// 实例化上传类
    $upload->maxSize   =     993145728 ;// 设置附件上传大小
    $upload->exts      =     $exts;// 设置附件上传类型
    $upload->rootPath ='Public';
    $upload->savePath  =      $path; // 设置附件上传目录
    $info   =   $upload->uploadOne($_FILES[$name]);
    if($info){
        $imgSrc = '/Public'.$info['savepath'].$info['savename'];
        return ['errcode'=>200,'data'=>$imgSrc];
    }else{
        return ['errcode'=>201,'data'=>$upload->getError()];
    }
}