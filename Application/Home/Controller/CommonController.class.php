<?php
namespace Home\Controller;
use Think\Controller;

class CommonController extends Controller {

   // static $_DATA;

    public function _initialize() {
        header('Access-Control-Allow-Origin: *');
		/*
        $md5 = '5B7106F98A4EA6C10E7753D276745672';
		$postmd5=I('md5');
        if (empty($postmd5)) {
			$msg["code"]=3001;
            get_api_result(300, $msg);
            exit;
        } else {
            if (I('md5') != $md5) {
			   $msg["code"]=3002;
               get_api_result(300,$msg);
               exit;
            }
        }
        self::$_DATA = getRequest();
		*/
		
    }

}