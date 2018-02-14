<?php

namespace Home\Controller;

use Think\Controller;


class IOrderController extends CommonController
{


    /**
     * 下单(购物车下单)
     */
    public function setOrderFromShopcar()
    {
        $orderModel = D("Order");
        $product_type_num = count(I('product_id'));
        if ($product_type_num == 0) {
            $msg = "请先选取购买商品";
            get_api_result(300, $msg);
        }

        $orderdata["subcode"] = $orderModel->getordercode();  //订单编号
        $orderdata["addtime"] = time();
        $orderdata["userid"] = I('userid');                    //下单人id
        $orderdata["username"] = I('username');                //收件人姓名
        $orderdata["usertel"] = I('usertel');                //收件人电话
        $orderdata["provinceid"] = I('provinceid');            //目的地省id
        $orderdata["cityid"] = I('cityid');                    //目的地市id
        $orderdata["countyid"] = I('countyid');                //目的地区县id
        $orderdata["address"] = I('address');                //详细地址(全地址)
        $orderdata[$i]["status"] = 0;                         //待支付

        $product_id = I('product_id');                    //商品id
        $num = I('product_num');                           //商品所需数量
        $productprice = I('productprice');               //商品单价

        $orderModel->startTrans();
        $ProductModel = D("Product");
        $OrderProductModel = D("OrderProduct");

        for ($i = 0; $i < count($product_id); $i++) {   //订单商品
            $shopdata[$i] = $product_id[$i];

            $ordersenddata[$i]["subcode"] = $orderdata["subcode"];                                             //订单编号
            $ordersenddata[$i]["product_id"] = $product_id[$i];                                  //商品id
            $productmsg = $ProductModel->getproductbyid($product_id[$i]);                         //商品信息
            $ordersenddata[$i]["product_name"] = $productmsg["product_name"];
            $ordersenddata[$i]["product_img"] = $productmsg["product_img_y"];
            $ordersenddata[$i]["summary"] = $productmsg["summary"];
            $ordersenddata[$i]["userid"] = $productmsg["userid"];
            $ordersenddata[$i]["product_num"] = $num[$i];                                        //商品所需数量
            $ordersenddata[$i]["product_price"] = $productprice[$i];                            //商品价格
        }
        $res = $orderModel->addpost($orderdata);                                             //添加订单商品
        $res1 = $OrderProductModel->addAllpost($ordersenddata);                                             //添加订单商品

        $ShoppingcarModel = D("Shoppingcar");
        $idstr = implode(",", $shopdata);
        $delres = $ShoppingcarModel->delAllpost($idstr, $orderdata["userid"]);                                   //删除购物车中此商品

        if (!($res && $res1 && $delres)) {
            $orderModel->rollback();
            $msg = "下单失败";
            get_api_result(300, $msg);
        } else {
            $orderModel->commit();
            $msg = "下单成功";
            get_api_result(200, $msg, $orderdata["subcode"]);
        }

    }

    /**
     * 下单(商品详情下单)
     */
    public function setOrderFromproduct()
    {
        $orderModel = D("Order");
        $product_type_num = count(I('product_id'));
        if ($product_type_num == 0) {
            $msg = "请先选取购买商品";
            get_api_result(300, $msg);
        }

        $orderdata["subcode"] = $orderModel->getordercode();  //订单编号
        $orderdata["addtime"] = time();
        $orderdata["userid"] = I('userid');                    //下单人id
        $orderdata["username"] = I('username');                //收件人姓名
        $orderdata["usertel"] = I('usertel');                //收件人电话
        $orderdata["provinceid"] = I('provinceid');            //目的地省id
        $orderdata["cityid"] = I('cityid');                    //目的地市id
        $orderdata["countyid"] = I('countyid');                //目的地区县id
        $orderdata["address"] = I('address');                //详细地址(全地址)
        $orderdata[$i]["status"] = 0;                         //待支付

        $product_id = I('product_id');                    //商品id
        $num = I('product_num');                           //商品所需数量
        $productprice = I('productprice');               //商品单价

        $orderModel->startTrans();
        $ProductModel = D("Product");
        $OrderProductModel = D("OrderProduct");

        for ($i = 0; $i < count($product_id); $i++) {   //订单商品
            $shopdata[$i] = $product_id[$i];

            $ordersenddata[$i]["subcode"] = $orderdata["subcode"];                                             //订单编号
            $ordersenddata[$i]["product_id"] = $product_id[$i];                                  //商品id
            $productmsg = $ProductModel->getproductbyid($product_id[$i]);                         //商品信息
            $ordersenddata[$i]["product_name"] = $productmsg["product_name"];
            $ordersenddata[$i]["product_img"] = $productmsg["product_img_y"];
            $ordersenddata[$i]["summary"] = $productmsg["summary"];
            $ordersenddata[$i]["userid"] = $productmsg["userid"];
            $ordersenddata[$i]["product_num"] = $num[$i];                                        //商品所需数量
            $ordersenddata[$i]["product_price"] = $productprice[$i];                            //商品价格
        }
        $res = $orderModel->addpost($orderdata);                                             //添加订单商品
        $res1 = $OrderProductModel->addAllpost($ordersenddata);

        if (!$res) {
            $orderModel->rollback();
            $msg = "下单失败";
            get_api_result(300, $msg);
        } else {
            $orderModel->commit();
            $msg = "下单成功";
            get_api_result(200, $msg, $orderdata["subcode"]);
        }

    }


    /**
     * 根据订单号获取订单详细内容
     */
    public function getOrderinfoBysubcode()
    {
        $subcode = I("subcode");
        $orderModel = D("Order");
        $res = $orderModel->getOrderInfoBySubcode($subcode);
        $msg = "获取成功";
        get_api_result(200, $msg, $res);
    }



    /***************************************我的-订单**************************************************************************/
    /**
     * 订单接取-获取订单列表信息
     * @param userid  用户id
     * @param type  类型 1|全部 2|待支付  3|已支付（待发货）  4|完成
     * @param  $page          页数
     * @param  $limit         显示条数
     */
    public function getOrderlist()
    {
        $userid = I("userid");
        $type = I("type") ? I("type") : 1;
        $page = I("page") ? I("page") : 1;
        $limit = I("limit") ? I("limit") : 10;
        $limit1 = ($page - 1) * $limit . "," . $limit;
        $orderModel = D("Order");
        $res = $orderModel->getorderlist($userid, $type, $limit1);
        $msg = "获取成功";
        get_api_result(200, $msg, $res);
    }

    /***************************************我的-订单--取消**************************************************************************/
    /**
     * 取消订单-取消待支付的订单
     * @param userid  用户id
     * @param subcode  订单编号
     */
    public function cancelOrder()
    {
        $userid = I("userid");
        $subcode = I("subcode");
        $orderModel = D("Order");
        $ischeck = $orderModel->checkOrderByUserid($subcode, $userid);
        if (!$ischeck) {
            $msg = "非法操作";
            get_api_result(300, $msg);
        }
        $ischeckdo = $orderModel->checkdocancelOrder($subcode, $userid);

        if (!$ischeckdo) {
            $msg = "操作失败，请刷新后再次提交";
            get_api_result(300, $msg);
        }

        $res = $orderModel->cancelOrder($subcode, $userid);
        if ($res) {
            $msg = "取消成功";
            get_api_result(200, $msg);
        } else {
            $msg = "取消失败";
            get_api_result(300, $msg);
        }

    }

    /***************************************我的-订单--支付**************************************************************************/
    /**
     * 支付订单-获取支付签名
     * @param userid  用户id
     * @param $code  订单编号（订单来源决定 来源为1时 、为主单号code,来源为2时 、为子单号subcode  ）
     * @param $codetype  订单来源 1|购物车支付（直接支付） 2|待支付订单
     * @param $pay_method  支付方式  1|支付宝2|微信3|钱包
     * @param $money  总订单钱数
     */
    public function payOrder()
    {
        $data["userid"] = I("userid");
        $data["code"] = I("code");
        //$data["codetype"]=I("codetype");
        $data["pay_method"] = I("pay_method");
        $data["money"] = I("money");
        $data["addtime"] = time();
        $data["status"] = 0;
        if ($data["money"] <= 0) {
            $msg = "非法操作";
            get_api_result(300, $msg);
        }
        $orderModel = D("Order");
        $setdealorder = $orderModel->adddealorder($data);   //添加订单
        if (!$setdealorder) {
            $msg = "获取失败";
            get_api_result(300, $msg);
        }

        //返回支付签名
        if ($data["pay_method"] == 3) {
            $WalletWaterModel = D("WalletWater");
            $usermoney = $WalletWaterModel->getMoneynumByuserid($data["userid"]);  //用户钱包钱数
            $res["code"] = $data["code"];  //订单编号
            $res["usermoneynum"] = $usermoney;  //用户钱包钱数
        } else if ($data["pay_method"] == 2) {  //微信
            $wxPay = new \WxPay();
            $NOTIFY_URL = C("HOST") . "IPayCallBack/WeixinPayCallBack";
            //$NOTIFY_URL="http://www.baidu.com";
            $order_no = $data["code"];
            $txnAmt = intval($data['money'] * 100);
            $txnTime = time();
            $result = $wxPay->payOrder($order_no, $txnTime, $txnAmt, $NOTIFY_URL);
            $appRequest = $wxPay->getAppRequest($result['prepay_id']);
            $appRequest['_package'] = $appRequest['package'];
            unset($appRequest['package']);
            $res["code"] = $order_no;
            $res["money"] = $txnAmt;
            $res["pay_method"] = $data["pay_method"];
            $res["app_request"] = $appRequest;
            $res["prepay_id"] = $appRequest["prepayid"];

        } else { //支付宝
            $AliPayModel = D("AliPay");
            $NOTIFY_URL = C("HOST") . "IPayCallBack/AliPayCallBack";
            $order_no = $data["code"];
            $money = $data["money"];
            $sign = $AliPayModel->getRequestParam($order_no, $money, $NOTIFY_URL);
            $res["code"] = $order_no;
            $res["money"] = $money;
            $res["pay_method"] = $data["pay_method"];
            $res["sign"] = $sign;
        }
        //$res=$orderModel->cancelOrder($subcode,$userid);

        $msg = "获取成功";
        get_api_result(200, $msg, $res);


    }


    /**
     * 支付订单-钱包支付回调
     * @param userid  用户id
     * @param $code  订单编号
     * @param $paypassword  支付密码
     */
    public function walletpay()
    {
        $userid = I("userid");
        $code = I("code");
        $paypassword = md5(I('paypassword'));
        $orderModel = D("Order");
        $ischeck = $orderModel->checkdealorderstatus($code, $userid);  //是否存在待支付订单
        if (!$ischeck) {
            $msg = "不存在订单！";
            get_api_result(303, $msg);
        }

        $WalletWaterModel = D("WalletWater");
        $usermoney = $WalletWaterModel->getMoneynumByuserid($userid);  //用户钱包钱数

        $ordermsg = $orderModel->getMoneynumByCode($code); //获取订单信息
        if ($usermoney < $ordermsg["money"]) {       //判断用户钱包是否够支付
            $msg = "余额不足！";
            get_api_result(302, $msg);
        }

        $UserModel = D("User");
        $ischeckpay = $UserModel->checkpayPassword($userid, $paypassword); //判断支付密码是否正确
        if (!$ischeckpay) {
            $msg = "支付密码错误！";
            get_api_result(301, $msg);
        }

        $Model = M();
        $Model->startTrans();

        $data["userid"] = $userid;
        $data["moneynum"] = -$ordermsg["money"];
        $data["type"] = 3;
        $res = $WalletWaterModel->addpost($data);    //用户流水增加
        $res1 = $orderModel->callbackwalletpay($code, $userid);  //支付成功回调

        if ($res && $res1) {
            $Model->commit();
            $msg = "支付成功";
            get_api_result(200, $msg);
        } else {
            $Model->rollback();
            $msg = "支付失败";
            get_api_result(300, $msg);
        }
    }


    /***************************************我的-订单--退款****************************************************************/

    /**
     * 待发货退款申请
     * @param userid  用户id
     * @param $subcode  订单编号
     */

    public function refundorder()
    {
        $userid = I("userid");
        $subcode = I("subcode");
        $refundmsg = I("refundmsg");

        $orderModel = D("Order");
        $ordermsg = $orderModel->getOrderInfoBySubcode($subcode); //获取订单信息
        if (empty($ordermsg)) {
            $msg = "非法操作";
            get_api_result(301, $msg);
        }
        if ($ordermsg["status"] != 2) {  //退款必须是发货之前
            $msg = "请刷新后再次提交";
            get_api_result(301, $msg);
        }

        if ($ordermsg["userid"] != $userid) {
            $msg = "非法操作";
            get_api_result(301, $msg);
        }
        $data["status"] = 6;
        $data["refundmsg"] = $refundmsg;
        $data["refundtime"] = time();
        $where = "userid=" . $userid . " and subcode='" . $subcode . "' and status=2";

        $res = $orderModel->editpost($where, $data);  //修改订单信息

        if ($res) {
            $msg = "操作成功";
            get_api_result(200, $msg, $res);
        } else {
            $msg = "操作失败";
            get_api_result(300, $msg, $res);
        }


    }

    /***************************************我的-订单--确认收货****************************************************************/

    /**
     * 确认收货
     * @param userid  用户id
     * @param $subcode  订单编号
     */

    public function Confirmorder()
    {
        $userid = I("userid");
        $subcode = I("subcode");

        $orderModel = D("Order");
        $res = $orderModel->Confirmorder($subcode, $userid);  //确认收货

        if ($res) {
            $msg = "操作成功";
            get_api_result(200, $msg, $res);
        } else {
            $msg = "操作失败";
            get_api_result(300, $msg, $res);
        }


    }

    /***************************************我的-订单-删除已完成订单************************************************************/

    /**
     * 删除已完成订单
     * @param userid  用户id
     * @param $subcode  订单编号
     */

    public function delorder()
    {
        $userid = I("userid");
        $subcode = I("subcode");

        $orderModel = D("Order");
        $res = $orderModel->delorder($subcode, $userid);  //删除已完成订单

        if ($res) {
            $msg = "操作成功";
            get_api_result(200, $msg);
        } else {
            $msg = "操作失败";
            get_api_result(300, $msg);
        }


    }


}