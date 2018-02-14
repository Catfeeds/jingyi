<?php

namespace Home\Controller;

use Think\Controller;

class IOrderEvaluateController extends CommonController {

    /**
     *  订单评论
	 *  添加评论
     */
    public function addEvaluate() {
        $userid = I('userid');
        $subcode = I('subcode');
        $evaluate_msg = shielding(I('msg'));
        $evaluate_star = I('star');
        $addtime= time();
		$OrderModel=D("Order");
		
		$ordermsg=$OrderModel->getOrderInfoBySubcode($subcode);
		if(empty($ordermsg)){
			 $msg = "非法操作。";
             get_api_result(300, $msg);
			}
		if($ordermsg["status"]!=4){
			 $msg = "非法操作。";
             get_api_result(300, $msg);
			}
		$OrderProductModel=D("OrderProduct");
		$productmsg=$OrderProductModel->getproductlistbycode($subcode); //获取订单商品列表
		for($i=0;$i<count($productmsg);$i++){
			  $data[$i]["product_id"] =  $productmsg[$i]["product_id"];
			  $data[$i]["userid"] =  $userid;
			  $data[$i]["subcode"] = $subcode;
			  $data[$i]["evaluate_msg"] =$evaluate_msg;
			  $data[$i]["evaluate_star"] = $evaluate_star;
			  $data[$i]["addtime"] = $addtime;
			}
          $OrderEvaluateModel=D("OrderEvaluate");
		  $res= $OrderEvaluateModel->addAllpost($data);
		  
        if ($res) {
			
			$where="subcode='".$subcode."'";
			$data1["status"]=5;
			$res1=$OrderModel->editpost($where,$data1);
            $msg = "评价成功。";
            D('UserLevel')->addUserGrow($userid,4);//成长值
            get_api_result(200, $msg);
        } else {
            $msg = "评价失败。";
            get_api_result(300, $msg);
        }
    }
	
	/**
     * 获取评价列表通过商品id
     */
    public function getEvaluatelistbyproid() {
        $product_id = I('product_id');
		$page=I("page")?I("page"):1;
		$limit=I("limit")?I("limit"):10;
		$limit1=($page-1)*$limit.",".$limit;
    
		$OrderEvaluateModel=D("OrderEvaluate");
		
		$res=$OrderEvaluateModel->getevaluatebyproid($product_id,$limit1);

        $msg = "获取成功";
        get_api_result(200, $msg,$res);
       
    }

}
