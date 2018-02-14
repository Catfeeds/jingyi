<?php
namespace Common\Model;
use Think\Model;

/**系统设定**/
class WebconfigModel extends Model{
      protected $tableName = 'order_main'; 
	
	
	/*
	*获取系统参数	*/
     public function getSystemParam(){
		$Webconfig = M('appconfig');
    	$list = $Webconfig->select();
    	$num = count($list);
    	for ($i=0;$i<$num;$i++){
    		$key=$list[$i]['keyname'];
    		$value=$list[$i]['value'];
    		$web_config[$key]=$value;
    	}
		return  $web_config;
	}
	
	
	
	/*
	*获取订单分成参数	*/
     public function getOrderRatio($payaddtime){
		$Model = M('order_ratio');
		$where="addtime<=".$payaddtime;
		$order="addtime desc";
    	$res = $Model->where($where)->order($order)->find();
		return  $res;
	}
	
 
	
	

}