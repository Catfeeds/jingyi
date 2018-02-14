<?php
namespace Common\Model;
use Think\Model;

/**订单信息表**/
class DealOrderModel extends Model{
	protected $tableName = 'deal_order'; 
	
	/*
	*判断订单是否存在
	*@param  $code  订单编号
	*/
     public function ischeckorder($code){
		 $Model=M("deal_order");
		 $where="code='".$code."'";
		 $msg=$Model->where($where)->find();
		return  $res;
	}

	
	/*
	*支付成功回调
	*@param  $code  订单编号
	*@param  $order_no  交易号
	*@param  $money  支付钱数
	*@param  $pay_methor  支付方式
	*/
     public function successcallback($code,$order_no,$money,$pay_methor){
		$Model=M("deal_order");
		$where="code='".$code."' and pay_method=".$pay_methor." and status=0";
		$ordermsg=$Model->where($where)->find();
		$ordermainModel=M("order_main");
	
		$where1="subcode='".$ordermsg["code"]."'";	

		$data1["status"]=2;
		$data1["payaddtime"]=time();
		$ordermainModel->where($where1)->save($data1);  //订单状态改变
		
		$data["backtime"]=time();
		$data["status"]=1;
		$data["order_no"]=$order_no;
		$data["returnmoney"]=$money;
		$res=$Model->where($where)->save($data);    //支付订单状态改变
		return  $res;
	}
	
	
	
	
	

}