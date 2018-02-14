<?php
namespace Common\Model;
use Think\Model;

/**banner**/
class BannerModel extends Model{
	
	
	/*
	*获取列表
	*@param  $where  条件
	*/
     public function getlist(){
		 $Model=M("banner");
		 $order="tui desc";
		 $limit=5;
		 $res= $Model->order($order)->limit($limit)->select();
		 if(!$res){$res=array();}
		 if($res){
			  for($i=0;$i<count($res);$i++){
				  $res[$i]["images"]=imgpath($res[$i]["images"]);
				  }
			 }
		return  $res;
	}
	
	
	
	
	

}