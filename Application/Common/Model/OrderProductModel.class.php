<?php
namespace Common\Model;
use Think\Model;

/**订单商品**/
class OrderProductModel extends Model{
	protected $tableName = 'order_product'; 
	
	
	/*
	*添加订单商品
	*/
     public function addpost($data){
		$res=$this->add($data);
		return  $res;
	}
	
	/*
	*批量添加订单
	*/
     public function addAllpost($data){
		$res=$this->addAll($data);
		return  $res;
	}
	
	
	
	
	/**
     * 根据订单编号获取订单商品信息
	  *@param  $code       订单编号 
     */
    public function getproductlistbycode($code){
		
		$where="subcode='".$code."' ";
		$field="`product_id`,`userid`,`product_name`,`summary`,`product_img`,`product_price`,`product_num`";
		$res=$this->field($field)->where($where)->select(); 
		if(!$res){ 
		$res=array();
		}else{
			for($i=0;$i<count($res);$i++){
				$res[$i]["product_img"]=imgpath($res[$i]["product_img"]);
			}
		}
	
		
		return $res;	
    }
	
	
	/**
     * 根据订单编号获取订单商品总价及商品数量
	  *@param  $code       订单编号 
     */
    public function getproductpricebycode($code){
		
		$where="subcode='".$code."' ";
		$field="sum(`product_price`*`product_num`) as allprice,sum(`product_num`) as allproduct_num";
		$res=$this->field($field)->where($where)->select(); 
		if(!$res){ 
		$res[0]["allprice"]=0.00;
		$res[0]["allproduct_num"]=0;
		}
	
		
		return $res[0];	
    }
	
	
	

}