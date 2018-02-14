<?php
namespace Common\Model;
use Think\Model;

/**用户在线**/
class UseronlineModel extends Model{
	
	
	/*
	*获取当前在线用户信息
	*/
     public function getnowuserid(){
		 $where="lastdotime>=".(time()-5*60);
		 $res=$this->where($where)->getField("userid",true);
		return $res;
	}
	
	/*
	*更新用户在线状态
	*@param  $userid  用户id
	*/
     public function setstatus($userid){
		
		 $ischeck=$this-> checkpost($userid);
		 if(!$ischeck){
			 $data["userid"]=$userid;
			  $data["lastdotime"]=time();
			 $res=$this-> addpost($data);
			 }else{
			     $data["lastdotime"]=time();
				  $res=$this->editpost($userid,$data);
				 }

		return $res;
	}
	
	/*
	*创建用户在线状态
	*@param  $userid  用户id
	*/
     public function addpost($data){
		 
		$res=$this->add($data);
		return $res;
	}
	
	/*
	*更新用户匿名状态
	*@param  $userid  用户id
	*/
     public function editpost($userid,$data){
		 $where="userid=".$userid;
		$res=$this->where($where)->save($data);
		if($res===0){
			$res=true;
			}
		return $res;
	}
	
	/*
	*判断是否存在用户现在状态信息
	*@param  $userid  用户id
	*/
     public function checkpost($userid){
		 $where="userid=".$userid;
		$msg=$this->where($where)->find();
		if($msg){
			$res=true;
			}else{
				$res=false;
				}
		return $res;
	}
	
	

}