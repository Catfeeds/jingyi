<?php
namespace Home\Controller;
use Think\Controller;

class IBannerController extends CommonController {
	

     /**
     *  获取banner信息
     */
    public function getlist(){
		
        $BannerModel=D("Banner");
		$res=$BannerModel->getlist();
		$msg="获取成功！";
		get_api_result(200, $msg, $res);  
			 
    }
	
	
}