<?php
namespace Common\Model;
use Think\Model;

/**职业**/
class ProfessionSignModel extends Model{
	  protected $tableName = 'profession_sign'; 
	
	/*
	*获取全部职业列表
	*/
     public function getlist(){
		 $Model=M("profession_sign");
		 $res= $Model->select();
		 if(!$res){$res=array();}
		return  $res;
	}
	
	
	/*
	*根据id获取职业名称
	*/
     public function getnamebyid($id){
		 $Model=M("profession_sign");
		 if(!empty($id)){
			  $where="pro_id=".$id;
		      $res= $Model->where($where)->find();
		}else{
			$res["name"]="";
			}
		return  $res["name"];
	}
	
	
	
	
	

}