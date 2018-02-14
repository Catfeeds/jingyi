<?php
namespace Common\Model;
use Think\Model;

/**手机区号**/
class CountrymobileprefixModel extends Model{
	//ALTER TABLE  `country` ADD  `code` VARCHAR( 255 ) NOT NULL COMMENT  '国家编号' AFTER  `country` ;

	 protected $tableName = 'country'; 
	 
	/*
	*获取区号列表
	*@param  $where  条件
	*/
     public function getlist(){
		 $Model=M("country");
		 
		 $res= $Model->select();
		 if(!$res){$res=array();}
		return  $res;
	}
	
	
	
	
	

}