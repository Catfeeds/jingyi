<?php
namespace Common\Model;
use Think\Model;

/**购物车**/
class ShoppingcarModel extends Model{
	
	/*
	*添加购物车
	*@param  $data  
	*/
     private function addpost($data){
		$data["addtime"]=time();
		$res= $this->add($data);
		return $res;
	}
	
	
	/*
	*根据用户id获取购物车列表
	*@param  $userid  用户id
	*/
     public function getlistbyuserid($userid){
		 $where="a.userid=".$userid;
		 $field="b.product_id,b.summary,b.product_name,b.product_img,b.price,a.product_num";
		 $join="product as b ON a.product_id=b.product_id";
		 $res= $this->alias("a")->field($field)->where($where)->join($join)->select();
		 if(!$res){$res=array();}
		 if($res){
			  for($i=0;$i<count($res);$i++){
				  $res[$i]["product_img"]=imgpath($res[$i]["product_img"]);
				  }
			 }
		return  $res;
	}
	
	/*
	*购物车商品数量增加
	*@param  $data  
	*/
     public function editnumpost($product_id,$userid,$num){
		$where="product_id=".$product_id." and userid=".$userid;
		$res= $this->where($where)->setInc("product_num",$num);
		return $res;
	}
	
	/*
	*修改购物车(数量)
	*@param  $data  
	*/
     public function editpost($product_id,$userid,$num){
		$where="product_id=".$product_id." and userid=".$userid;
		$data["product_num"]=$num;
		$res= $this->where($where)->save($data);
		if($res===0){ $res=true;}
		return $res;
	}
	
	/*
	*判断购物车中是否存在商品
	*@param  $data  
	*/
     public function ischeckhave($product_id,$userid){
		$where="product_id=".$product_id." and userid=".$userid;
		$msg= $this->where($where)->find();
		$res=false;
		if($msg){
			$res=true;
			}
		return $res;
	}
	
	/*
	*添加购物车
	*@param  $data  
	*/
     public function addshipingcarpost($data){
		$ischeck=$this->ischeckhave($data["product_id"],$data["userid"]);
		if($ischeck){
			$res=$this->editnumpost($data["product_id"],$data["userid"],$data["product_num"]);
			}else{
				$res=$this->addpost($data);
				}
		return $res;
	}
	
	/*
	*修改购物车商品数量
	*@param  $data  
	*/
     public function editshipingcarnumpost($product_id,$userid,$num){
		$res=$this->editpost($product_id,$userid,$num);
		return $res;
	}
	
	/*
	*删除购物车商品
	*@param  $data  
	*/
     public function delpost($product_id,$userid){
		$where="product_id=".$product_id." and userid=".$userid;
		$res= $this->where($where)->delete();
		return $res;
	}
	
	/*
	*批量删除购物车商品
	*@param  $idstr   商品id字符串 以,隔开
	*/
     public function delAllpost($idstr,$userid){
		 $Model=M("Shoppingcar");
		 $where="product_id in (".$idstr.") and userid=".$userid;
		 $res= $Model->where($where)->delete();
		return $res;
	}
	
	/*
	*根据用户id获取购物车种类树龄
	*@param  $userid  用户id
	*/
     public function getproducttypenumbyuserid($userid){
		 $where="userid=".$userid;
		 $res= $this->where($where)->count();
		 if(!$res){$res=0;}
		return  $res;
	}

}