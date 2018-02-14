<?php
namespace Common\Model;
use Think\Model;

/**订单模块**/
class OrderModel extends Model{
	protected $tableName = 'order_main'; 
	
	/*
	*获取订单号
	*/
     public function getordercode(){
		$res=date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
		return  $res;
	}
	
	/*
	*添加订单
	*/
     public function addpost($data){
		$Model=M("order_main");
		$data["enddotime"]=time();
		$res=$Model->add($data);
		return  $res;
	}
	
	/*
	*修改订单
	*/
     public function editpost($where,$data){
		$data["enddotime"]=time();
		$Model=M("order_main");
		$res=$Model->where($where)->save($data);
		return  $res;
	}
	/*
	*批量添加订单
	*/
     public function addAllpost($data){
		$Model=M("order_main");
		$res=$Model->addAll($data);
		return  $res;
	}
	
	
	
	
	/**
     * 获取某用户订单信息
	  *@param userid  用户id
	  *@param type  类型 1|全部 2|待支付  3|已支付（待发货）  4|完成
	  *@param  $page          页数 
	  *@param  $limit         显示条数 
     */
    public function getorderlist($userid,$type=1,$limit){
		$field="a.*,b.province as provincename,c.city as cityname,d.county as countyname";
		$where="a.userid=".$userid." and a.userdelstatus=0 and a.status <>1";
		if($type==2){
			$where.=" and a.status=0"; 
			}
		if($type==3){
			$where.=" and (a.status=2 or a.status=3 or a.status=6)"; 
			}
		if($type==4){
			$where.=" and (a.status=4 or a.status=5 or a.status=7)"; 
			}
		$join="province as b ON a.provinceid=b.province_id";
		$join1="city as c ON a.cityid=c.city_id";
		$join2="county as d ON a.countyid=d.county_id";
		$order="a.enddotime desc";
		$res=$this->alias("a")->field($field)->where($where)->limit($limit)->order($order)->join($join)->join($join1)->join($join2)->select(); 
		if(!$res){ $res=array();}
		$OrderProductModel=D("OrderProduct");
		for($i=0;$i<count($res);$i++){
			$res[$i]["productmsg"]=$OrderProductModel->getproductlistbycode($res[$i]["subcode"]);
			$res[$i]["countmsg"]=$OrderProductModel-> getproductpricebycode($res[$i]["subcode"]);
			}
		
		return $res;	
    }
	
	/**
     * 根据子订单号获取订单详细信息
	  *@param subcode  子单号
     */
    public function getOrderInfoBySubcode($subcode){
		$where="a.subcode='".$subcode."'";
		$field="a.*,b.province as provincename,c.city as cityname,d.county as countyname,e.sendname";
		$join="province as b ON a.provinceid=b.province_id";
		$join1="city as c ON a.cityid=c.city_id";
		$join2="county as d ON a.countyid=d.county_id";
		$join3="left join send as e ON a.send_id=e.id";
		$res=$this->alias("a")->field($field)->where($where)->join($join)->join($join1)->join($join2)->join($join3)->find();
		if(!$res){ $res=array();}
		else{
			$OrderProductModel=D("OrderProduct");
			$res["productmsg"]=$OrderProductModel->getproductlistbycode($subcode);
			$res["countmsg"]=$OrderProductModel-> getproductpricebycode($subcode);
			$dealordermsg=$this->getMoneynumByCode($subcode);
			if(empty($dealordermsg)){
				$res["paymethod"]="";
				}else{
					$paymethod=$dealordermsg["pay_method"]==3?"钱包支付":($dealordermsg["pay_method"]==2?"微信支付":($dealordermsg["pay_method"]==1?"支付宝支付":""));
					$res["paymethod"]=$paymethod;
					}
			}	
		return $res;	
    }
	
	
	
	
	/*根据子订单号获取订单商品合计*/
     public function getorderproductsummsgbysubcode($subcode){
		$Model=M("order_main");
		$where="subcode='".$subcode."'";
		$res["typenum"]=$Model->where($where)->count();   //商品总类数量
		
		$field="sum(product_num) as num";
		$pronum=$Model->field($field)->where($where)->select();   //商品总数量
		$res["pronum"]=$pronum[0]["num"];   //商品总数量
		
		$field1="sum(product_price*product_num+product_freight) as allmoney,sum(product_freight) as allfreight,sum(product_price*product_num)  as productmoney";
		$pronum=$Model->field($field1)->where($where)->select();   //商品总数量
		$res["allmoney"]=$pronum[0]["allmoney"];     //总金额
		$res["allfreight"]=$pronum[0]["allfreight"];      //总运费 
		$res["productmoney"]=$pronum[0]["productmoney"];  //总商品价格    
		
		return  $res;
	}
	
	
	
		/*判断用户订单是否可以取消*/
     public function checkdocancelOrder($subcode,$userid){
		$Model=M("order_main");
		$where="subcode='".$subcode."' and userid=".$userid." and status =0";
		$res=$Model->where($where)->find(); 
		return  $res;
	}
	
		/*根据子订单号取消订单*/
     public function cancelOrder($subcode,$userid){
		$Model=M("order_main");
		$where="subcode='".$subcode."' and userid=".$userid." and status =0";
		$data["status"]=1;
		$data["enddotime"]=time();
		$res=$Model->where($where)->save($data); 
		return  $res;
	}
	
		/*判断订单是否为用户所有*/
     public function checkOrderByUserid($subcode,$userid){
		$Model=M("order_main");
		$where="subcode='".$subcode."' and userid=".$userid;
		$res=$Model->where($where)->find(); 
		return  $res;
	}
	
		/*添加支付订单*/
     public function adddealorder($data){
		$Model=M("deal_order");
		$where="code='".$data["code"]."' and userid=".$data["userid"]." and status=0";
		$del=$Model->where($where)->delete();   //删除其他种类付款订单信息、
		$res=$Model->add($data); 
		return  $res;
	}
	
		/*根据订单号码获取总支付价格*/
     public function getMoneynumByCode($code){
		$Model=M("deal_order");
		$where="code='".$code."'";
		$res=$Model->where($where)->find(); 
		return  $res;
	}
	
	/*钱包支付成功回调*/
     public function callbackwalletpay($code,$userid){
		$Model=M("deal_order");
		$where="code='".$code."' and userid=".$userid." and pay_method=3 and status=0";
		$ordermsg=$Model->where($where)->find();
		$ordermainModel=M("order_main");
		$where1="subcode='".$ordermsg["code"]."'";	
		
		$data1["status"]=2;
		$data1["payaddtime"]=time();
		$data1["enddotime"]=time();
		$ordermainModel->where($where1)->save($data1);  //订单状态改变
		
		$data["backtime"]=time();
		$data["status"]=1;
		$data["returnmoney"]=$ordermsg["money"];
		$res=$Model->where($where)->save($data);    //支付订单状态改变
		return  $res;
	}
	
		/*判断是否存在订单*/
     public function checkdealorderstatus($code,$userid){
		$Model=M("deal_order");
		$where="code='".$code."' and userid=".$userid." and pay_method=3 and status=0";
		$res=$Model->where($where)->find(); 
		return  $res;
	}
	
	/*
	*订单确认收货
	*/
     public function Confirmorder($subcode,$userid){
		$Model=M("order_main");
		$where="subcode='".$subcode."' and userid=".$userid;
		$data["status"]=4;
		$data["enddotime"]=time();
		$Model->startTrans();
		$res=$Model->where($where)->save($data);
		//$res1=$this->Confirmorderpay($subcode);           //返利
		$res1=true;
		$OrderProductModel=D("OrderProduct");
		$productmsg=$OrderProductModel->getproductlistbycode($subcode); //获取商品信息
		$ProductModel=D("Product");
		for($i=0;$i<count($productmsg);$i++){
			$res2=$ProductModel->editsalesnumpostbyid($productmsg[$i]["product_id"],$productmsg[$i]["product_num"]);  //更新商品销量
			}
		
	    if(!($res&&$res1)){ 
               $Model->rollback();
			   return  false;
        }else{
             $Model->commit();
			 return  true;
       }
		
	}
	
	/*
	*订单确认收货后返利信息
	*/
     public function Confirmorderpay($subcode){
		$Model=M("order_main");
		$where="subcode='".$subcode."'";
		$msg=$Model->where($where)->find();   //获取订单信息
		
		$ProductModel=D("Product");
		$business=$ProductModel->getBusinessInfo($msg["product_storeid"]); //获取商铺信息
		
		$UserModel=D("User");
		$userinfo=$UserModel->getusermsgbyuserid($business["userid"]); //商铺用户信息
		
		$WalletWaterModel=D("WalletWater");
		$orderratio=$this->getordermoneybyorder($subcode,$userinfo["pid"]);   //获取订单分成信息
	    $data1["type"]=6;
	    $data1["userid"]=$msg["userid"];
	    $data1["moneynum"]= $orderratio["buyuser"];
	    $res=$WalletWaterModel->addpost($data1); //添加消费者返利
		 
		if($userinfo["pid"]>0){
			 $data2["type"]=5;
			 $data2["userid"]=$userinfo["pid"];
			 $data2["moneynum"]= $orderratio["tuibussness"];
			 $res1=$WalletWaterModel->addpost($data2); //添加商铺推荐用户返利
			}
		$wwdata["subcode"]= $subcode;
		$wwdata["orderallmoney"]= $orderratio["allmoney"];
		$wwdata["usermoney"]= $orderratio["buyuser"];
		$wwdata["bussnessmoney"]= $orderratio["bussness"];
		$wwdata["tuibussnessmoney"]= $orderratio["tuibussness"];
		$wwdata["ptmoney"]= $orderratio["pingtai"];
		$wwdata["addtime"]= $msg["addtime"];
		$wwdata["userid"]=$msg["userid"]; 
		$wwdata["bussness_id"]=$msg["product_storeid"];
		$RatioWalletWaterModel=D("RatioWalletWater");
		$res2=$RatioWalletWaterModel->addpost($wwdata);
		return  $res;
	}
	

 
	/*
	*删除已完成订单
	*/
     public function delorder($subcode,$userid){
		$Model=M("order_main");
		$where="subcode='".$subcode."' and userid=".$userid;
		$data["userdelstatus"]=1;
		$res=$Model->where($where)->save($data);
		return  $res;
	}
	
	
	   /*
	*订单确认后返利计算
	*/
     public function getordermoneybyorder($subcode,$Businesspid){
		$WebconfigModel=D("Webconfig");
		$payaddtime=$this->getorderpayaddtimebysubcode($subcode);		 //根据子订单号获取订单支付时间
		$orderinfo=$this->getorderproductsummsgbysubcode($subcode); // 订单商品合计信息
		
		$Web_config=$WebconfigModel->getOrderRatio($payaddtime["payaddtime"]);
		
		if($Businesspid>0){
			$res["tuibussness"]=$orderinfo["allmoney"]*$Web_config["tuibussnessratio"]/100;    //商铺推荐人分成
			$res["pingtai"]=$orderinfo["allmoney"]*$Web_config["ptratio"]/100;    //平台分成
			}else{
				$res["tuibussness"]=0;
				$res["pingtai"]=$orderinfo["allmoney"]*($Web_config["ptratio"]+$Web_config["tuibussnessratio"])/100;    //平台分成
				}
		$res["buyuser"]=$orderinfo["allmoney"]*$Web_config["userratio"]/100;    //购买者分成
		$res["bussness"]=$orderinfo["allmoney"]*$Web_config["bussnessratio"]/100;    //商铺分成
		$res["allmoney"]=$orderinfo["allmoney"];    //订单总价
		return  $res;
	}
	
	/*根据子订单号获取订单支付时间*/
     public function getorderpayaddtimebysubcode($subcode){
		$Model=M("order_main");
		$where="subcode='".$subcode."'";
		$field="payaddtime";
		$res=$Model->field($field)->where($where)->find(); 
		return  $res;
	}
	
	
	
	/**
     * 获取快递类型
	  *@param typeid  快递id
     */
    public function getsendmsgByid($typeid){
		if(!empty($typeid)){
			$Model=M("send");
		    $where="id=".$typeid;
		    $msg=$Model->where($where)->find(); 
		    if(!$msg){ $res["sendname"]="未知类型";}
			else{
				$res["sendname"]= $msg["sendname"];
				}
		}else{
			$res["sendname"]="";
			}
		return $res["sendname"];	
    }
	
	

}