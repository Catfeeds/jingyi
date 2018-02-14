<?php
namespace Common\Model;
use Think\Model;

/**商品图片**/
class ProductImgModel extends Model{
		protected $tableName = 'product_img';

	/*
	*根据商品id获取商品图片列表
	*@param  $where  条件
	*/
     public function getlist($product_id){

		 $Model=M("product_img");
		 $where="product_id=".$product_id;
		 $res= $Model->where($where)->select();
		 if(!$res){$res=array();}
		 if($res){
			  for($i=0;$i<count($res);$i++){
				  $res[$i]["imgs"]=imgpath($res[$i]["imgs"]);
				  }
			 }
		return  $res;
	}


}