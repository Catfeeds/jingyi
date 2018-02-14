<?php
namespace Common\Model;
use Think\Model;

/**接收飞船匿名设定**/
class UserairshipsetModel extends Model{
	
	
	/*
	*获取用户匿名状态
	*@param  $userid  用户id
	*/
     public function getstatus($userid){
		 $where="userid=".$userid;
		 $info=$this->where($where)->find();
		 if(!$info){
			 $res["status"]=0;
			 }else{
				 $res["status"]=$info["status"]; 
				 }

		return $res;
	}
	
	/*
	*更新用户匿名状态
	*@param  $userid  用户id
	*@param  $status  状态 0|非匿名 1|匿名
	*/
     public function setstatus($userid,$status){
		
		 $ischeck=$this-> checkpost($userid);
		 if(!$ischeck){
			 $data["userid"]=$userid;
			  $data["status"]=$status;
			 $res=$this-> addpost($data);
			 }else{
			      $data["status"]=$status;
				  $res=$this->editpost($userid,$data);
				 }

		return $res;
	}
	
	/*
	*创建用户匿名状态
	*@param  $userid  用户id
	*@param  $status  状态 0|非匿名 1|匿名
	*/
     public function addpost($data){
		 
		$res=$this->add($data);
		return $res;
	}
	
	/*
	*更新用户匿名状态
	*@param  $userid  用户id
	*@param  $status  状态 0|非匿名 1|匿名
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
	*判断是否存在用户匿名状态信息
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