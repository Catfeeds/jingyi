<?php
namespace Common\Model;
use Think\Model;

/**编年史**/
class OfficialHistroyModel extends Model{
	  protected $tableName = 'official_histroy'; 
	  
	  
	  
	/*
	*添加贴子
	*@param  $data  贴子数据
	*@param  $datafile  贴子图片、音频、视频数据
	*@param  $datafiletype  1|图片 3|音频 2|视频数据
	*/
     public function addpost($data,$datafile,$datafiletype){
		  $Model=M();
		  $Model->startTrans();
		  $data["addtime"]=time();
		  $res1=$Model->table("official_histroy")->add($data);
		  $ischeck=true;
		  if(!empty($datafile)){
			   for($i=0;$i<count($datafile);$i++){
				   $mydatafile[$i]["posts_id"]=$res1;
				   $mydatafile[$i]["addtime"]= $data["addtime"];
				   $mydatafile[$i]["type"]= $datafiletype;
				   $mydatafile[$i]["uploadurl"]= $datafile[$i];
				   $res2[$i]=$Model->table("official_histroy_file")->add($mydatafile[$i]);
				   if(!$res2[$i]){
					   $ischeck=false;
					   }
				 }
		 }
		 
		 if($res1&& $ischeck){
			  $Model->commit();
			   return  $res1;
			 }else{
				  $Model->rollback();
				return  false;
				 }
		
		
	}
	
	
	/*
	*删除编年史
	*@param  $postsid  贴子id
	*/
     public function delpost($postsid){
		  $Model1=M("official_histroy");
		  $Model2=M("official_histroy_file");
		  
		  $where1="posts_id=".$postsid;

		  $res1=$Model1->where($where1)->delete();   //删除贴子
		  
		  $where2="posts_id=".$postsid;
		  $msg=$Model2->where($where2)->select();
		  for($i=0;$i<count($msg);$i++){
			unlink($msg[$i]["uploadurl"]);
		   }
		  $res2=$Model2->where($where2)->delete();   //删除贴子上传内容
		                             
		  return $res1;
		
	}
	
	/*
	*获取贴子列表
	*/
     public function getPostslist($where,$order="",$limit=""){
		 if(empty($order)){
			 $order=" addtime desc";  //时间倒叙
			 }
		 if(empty($limit)){
			 $limit=10;  //默认10条
			 }
		  $Model=M("official_histroy");
		  $res=$Model->where($where)->order($order)->limit($limit)->select();  
		  if(count($res)==0){
			  $res=array();
			  } else{
				  for($i=0;$i<count($res);$i++){
					  $res[$i]["vedioimages"]=imgpath($res[$i]["vedioimages"]);
					  $res[$i]["filemsg"]=$this->getpostsfilesinfobypostsid($res[$i]["posts_id"]);  
					  }
				  }           
		  return $res;
		
	}
	
	
	/*
	*通过贴子id获取贴子上传信息
	*@param  $postsid   贴子id
	*/
     public function getpostsfilesinfobypostsid($postsid){
		  $Model=M("official_histroy_file");
		  $where="posts_id=".$postsid;
		  $res=$Model->where($where)->select();
		  if($res){
			   for($i=0;$i<count($res);$i++){
				   
				   $res[$i]["uploadurl"]= imgpath($res[$i]["uploadurl"]);
				   }  
			  }
		 
		  if(!$res){$res=array();}                       
		  return $res;
		
	}
	
	
	
}