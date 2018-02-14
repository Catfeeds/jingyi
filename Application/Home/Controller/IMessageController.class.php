<?php
namespace Home\Controller;
use Think\Controller;

class IMessageController extends CommonController {
	

     /**
     *  获取用户消息信息
     */
    public function getlist(){
        $userid = I('userid');
		$type = I('type')?I('type'):1;
		$limit = I('limit')?I('limit'):10;
		$page = I('page')?I('page'):1;
		$limit1=($page-1)*$limit.",".$limit;
      
        $MessageModel=D("Message");
		$res=$MessageModel->getList($userid,$type,$limit1);
		
		
	    $msg["code"]=2010;
        get_api_result(200, $msg,$res);
			
        
    }
	
	
     /**
     *  判断是否有未读信息
     */
    public function checkread(){
        $userid = I('userid');
        $MessageModel=D("Message");
		$res=$MessageModel->checkread($userid);
	    $msg["code"]=2010;
        get_api_result(200, $msg,$res);
			
        
    }
	
	 /**
     *  获取消息首页信息
     */
    public function getIndexlist(){
        $userid = I('userid');
		$useridarr = I('useridarr');
        $MessageModel=D("Message");
		$res["system"]["msg"]=$MessageModel->getnewmessage($userid,1);
		$res["system"]["no_read_num"]=$MessageModel->getnoreadnum($userid,1);
		$res["order"]["msg"]=$MessageModel->getnewmessage($userid,2);
		$res["order"]["no_read_num"]=$MessageModel->getnoreadnum($userid,2);
		$res["chat"]=$this->getUserinfoByUseridarr($useridarr);
	    $msg["code"]=2010;
        get_api_result(200, $msg,$res);
			
        
    }
	
	 /**
     *  消息删除
     */
    public function delmessage(){
        $userid = I('userid');
		$messageid = I('messageid');
        $MessageModel=D("Message");
		$res=$MessageModel-> delByid($userid,$messageid);
	    $msg["code"]=2010;
        get_api_result(200, $msg);
			
        
    }
	
	 /**
     *  消息阅读
     */
    public function readmessage(){
        $userid = I('userid');
		$messageid = I('messageid');
        $MessageModel=D("Message");
		$res=$MessageModel->editpost($userid);
		if($res){
			  $msg["code"]=2010;
        	 get_api_result(200, $msg);
			}else{
				  $msg["code"]=3013 ;
        		 get_api_result(300, $msg);
				}
	  
			
        
    }
	
	 /**
     *  通过用户id数组获取用户信息
     */
    public function getUserinfoByUseridarr($useridarr){
		
		if(empty($useridarr)){$res=array(); return $res;};
        $UserModel=M("User");
		for($i=0;$i<count($useridarr);$i++){
			$where="userid=".$useridarr[$i];
		    $res[$i]=$UserModel->where($where)->find();
			$res[$i]["headimg"]=imgpath($res[$i]["headimg"]);
			}
		return $res;			
    }
	
	

   


}