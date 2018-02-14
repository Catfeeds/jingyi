<?php
/**
 * @param string $str
 * @return mixed|string
 * 屏蔽敏感词
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
/**
 * 验证11位电话号码
 */
function is_mobile($mobile){
    if (!is_numeric($mobile)) {
        return false;
    }
    return preg_match('#^1[\d]{10}$#', $mobile) ? true : false;
}




