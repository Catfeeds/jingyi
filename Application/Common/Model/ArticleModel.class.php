<?php
namespace Common\Model;
use Think\Model;

/**单篇文章**/
class ArticleModel extends Model{
	
	
	/*
	*获取列表
	*@param  $id  wen文章id
	*/
     public function getlist($id){
		 $Model=M("article");
		 $res= $Model->where("id=".$id)->find();
		 if(!$res){$res=(object)array();}
		return  $res;
	}
	
	
	
	
	

}