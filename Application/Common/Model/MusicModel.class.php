<?php
namespace Common\Model;
use Think\Model;

/**banner**/
class MusicModel extends Model{
	
	
	/*
	*获取列表
	*@param  $where  条件
	*/
     public function getlist(){
		 $Model=M("music");
		
		 $res= $Model->select();
		 if(!$res){$res=array();}
		 if($res){
			  for($i=0;$i<count($res);$i++){
				  $res[$i]["allpath"]=imgpath($res[$i]["images"]);
				 }
			 }
		return  $res;
	}
	
	
	
	
	

}