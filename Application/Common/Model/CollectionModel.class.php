<?php
namespace Common\Model;
use Think\Model;

/**商品收藏信息**/
class CollectionModel extends Model{
	
	
	/*
	*获取用户收藏列表
	*@param  $userid  条件
	*/
     public function getlist($userid,$order,$limit,$countryid=249){
		 $Model=M("collection");
		 $where="userid=".$userid;
		 $msg= $Model->where($where)->order($order)->limit($limit)->getField("product_id",true);
		
		 if($msg){
			 $ProModel=D("Product");
			 $where1="product_id in (".implode(",",$msg).")";
			 $order1="";
			 $limit1="";
			 $res=$ProModel->getlist($where1,$order1,$limit1,$countryid);
			 }else{
				 $res=array();
				 }
		 
		return  $res;
	}
	

	
	/*
	*收藏添加
	*@param  $data  
	*/
     public function addpost($data){
		 $Model=M("collection");
		  $res= $Model->add($data);
		return $res;
	}
	
	/*
	*收藏删除
	*@param  $data   
	*/
     public function delpost($id,$userid){
		 $Model=M("collection");
		 $where="product_id=".$id." and userid=".$userid;
		 $res= $Model->where($where)->delete();
		return $res;
	}
	
	
   /*
	*通过商品id判断商品是否收藏
	*@param  $data   
	*/
     public function checkCollection($id,$userid){
		 $Model=M("collection");
		 $where="product_id=".$id." and userid=".$userid;
		 $res= $Model->where($where)->find();
		return $res;
	}
	
		

}