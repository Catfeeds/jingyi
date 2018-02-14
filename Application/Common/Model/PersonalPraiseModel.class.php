<?php
namespace Common\Model;
use Think\Model;

/**个人贴子好评度**/
class PersonalPraiseModel extends Model{
	  protected $tableName = 'personal_praise'; 
	  
	  
	  
	/*
	*添加好评度
	*@param  $data  贴子数据
	*/
     public function addpost($data){
		  $data["addtime"]=time();
		  $res=$this->add($data);
		  return  $res;	 
	}
	
	
	/*
	*判断是否已好评个人贴
	*@param  $postsid  贴子id
	*@param  $userid  用户id
	*/
     public function ischeckPraise($postsid,$userid){
		 $where="userid=".$userid."  and  posts_id=".$postsid;
		 $msg=$this->where($where)->find();
		  $res=false;
		  if($msg){
			 $res=true;	  
			  }                              
		  return $res;
		
	}
	
	/*
	*好评度计算
	*@param  $postsid  贴子id
	*/
     public function getPraise($postsid){
		 $where="posts_id=".$postsid." and praise=1";
		 $where1="posts_id=".$postsid;
		 $msg=$this->where($where)->count();
		 $msg1=$this->where($where1)->count();
		if($msg1===0){
			$res="100%";
			}else{
				$res=floor(100*($msg/$msg1))."%";
				}                    
		  return $res;
		
	}
	
}