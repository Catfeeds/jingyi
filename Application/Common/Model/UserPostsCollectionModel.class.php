<?php
namespace Common\Model;
use Think\Model;

/**用户贴子收藏信息**/
class UserPostsCollectionModel extends Model{
	  protected $tableName = 'user_posts_collection'; 
	  
	  
	  
	 /*
	*添加收藏
	*@param  $data  数据
	*/
     public function addpost($data){
		 $Model=M("user_posts_collection");
		 $data["addtime"]=time();
		 $res= $Model->add($data);
		return  $res;
	}
	
	/*
	*取消收藏
	*@param  $where  数据
	*/
     public function delpost($where){
		 $Model=M("user_posts_collection");
		 $res= $Model->where($where)->delete();
		return  $res;
	}
	
	/*
	*判断用户是否收藏了贴子
	*@param  userid  用户id
	*@param  postsid  贴子id
	*/
     public function checkCollection($userid,$postsid,$type){
		 if(empty($type)){
			 $type=2;
			 }
		 $res=false;
		 $Model=M("user_posts_collection");
		 $where="userid=".$userid." and posts_id=".$postsid." and type=".$type;
		 $msg= $Model->where($where)->find();
		 if($msg){
			 $res=true;
			 }
		return  $res;
	}
	  
	/*
	*通过贴子id 获取收藏人数
	*@param  postsid  贴子id
	*/
     public function getCollectioncountbyid($postsid,$type){
		  if(empty($type)){
			 $type=2;
			 }
		  $Model=M("user_posts_collection");
		  $where="posts_id=".$postsid." and type=".$type;
		  $res=$Model->where($where)->count();                              
		  return $res;
	}
	
	/*
	*通过用户id 获取收藏数量
	*@param  userid  用户id
	*/
     public function getCollectionnumbyuserid($userid){
		  $Model=M("user_posts_collection");
		  $where="userid=".$userid;
		  $res=$Model->where($where)->count();                              
		  return $res;
	}
	
	
    /*
	*通过用户id 获取收藏列表
	*@param  userid  用户id
	*/
     public function getCollectionlistbyuserid($userid,$order,$limit){
		  $Model=M("user_posts_collection");
		  $field="*,type as poststype";
		  $where="userid=".$userid;
		  $res=$Model->field($field)->where($where)->order($order)->limit($limit)->select(); 
		  
		  $PostsModel=D("Posts"); 
		  $OfficialPostsModel=D("OfficialPosts"); 
		  $PersonalPostsModel=D("PersonalPosts"); 
		  
		  for($i=0;$i<count($res);$i++){
			  if($res[$i]["type"]==1){
				  $res[$i]["posts"]=$OfficialPostsModel-> getCollectinfo($res[$i]["posts_id"],$userid);
				  }else if($res[$i]["type"]==2){
					  $res[$i]["posts"]= $PostsModel ->getCollectinfo($res[$i]["posts_id"],$userid);
					  }else{
						 $res[$i]["posts"]= $PersonalPostsModel ->getCollectinfo($res[$i]["posts_id"],$userid);
						  }
			  
			  }                             
		  return $res;
	}
	
	
	

}