<?php
namespace Common\Model;
use Think\Model;

/**星球贴子点赞**/
class PostsLikeModel extends Model{
	  protected $tableName = 'user_planet_posts_like'; 
	  
	  
	  
	/*
	*点赞
	*@param  $data  贴子数据
	*/
     public function addpost($data){
		  $Model=M("user_planet_posts_like");
		  $data["addtime"]=time();
		  $res=$Model->add($data);
		  return  $res;	
		
	}
	
	
	/*
	*取消点赞
	*@param  $postsid  贴子id
	*@param  $userid  用户id
	*@param  $type  类型  1|官方贴 2|星球贴 3|个人贴
	*/
     public function delpost($postsid,$userid,$type){
		 if(empty($type)){
			 $type=2;
			 }
		  $Model=M("user_planet_posts_like");
		  $where="posts_id=".$postsid." and userid =".$userid." and type=".$type;
		  $res=$Model->where($where)->delete();   //删除贴子      
		  return $res;
		
	}
	
	/*
	*贴子点赞数量
	*@param  $postsid   贴子id
	*/
     public function getlikenum($postsid,$type){
		  if(empty($type)){
			 $type=2;
			 }
		  $Model=M("user_planet_posts_like");
		  $where="posts_id=".$postsid." and type=".$type;
		  $res=$Model->where($where)->count();                                
		  return $res;
		
	}
	
	/*
	*判断用户是否对贴子点赞
	*@param  $postsid   贴子id
	*@param  $userid   用户id
	*/
     public function checkuseridislike($postsid,$userid,$type){
		 if(empty($type)){
			 $type=2;
			 }
		  $Model=M("user_planet_posts_like");
		  $where="posts_id=".$postsid." and userid=".$userid." and type=".$type;
		  $msg=$Model->where($where)->find();
		  $res=false; 
		  if($msg){
			  $res=true; 
			  }                               
		  return $res;
	}
	
	

	
	
	
}