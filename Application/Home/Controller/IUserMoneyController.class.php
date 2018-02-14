<?php

namespace Home\Controller;

use Think\Controller;

class IUserMoneyController extends CommonController
{

    /**
     *  用户提现
     */
    public function withdrawal()
    {
        $data["userid"] = I('userid');
        $data["moneynum"] = I('moneynum');
        $data["cardusername"] = I('cardusername');
        $data["cardbank"] = I('cardbank');
        $data["cardnum"] = I('cardnum');
        $data["addtime"] = time();
        $data["status"] = 1;

//        $WalletWaterModel = D("WalletWater");
//        $user["balance"] = $WalletWaterModel->getMoneynumByuserid($data["userid"]);   //获取用户钱包钱数
        $balance = M('user')->where(['userid' => $data['userid']])->getField('balance');
        if ($data["moneynum"] > $balance) {
            $msg = "提现金额大于钱包钱数。";
            get_api_result(300, $msg);
        }
        if ($data["moneynum"] <= 0) {
            $msg = "请填写正确的取现金额。";
            get_api_result(300, $msg);
        }
        $WithdrawalModel = D("Withdrawal");
        $res = $WithdrawalModel->addpost($data);

        if ($res) {
            $msg = "操作成功，请等待平台审核、打款。";
            get_api_result(200, $msg);
        } else {
            $msg = "操作失败。";
            get_api_result(300, $msg);
        }
    }

    /**
     *  获取用户钱数
     */
    public function getusermoney()
    {
        $userid = I('userid');
        $WalletWaterModel = D("WalletWater");
        $res["usermoney"] = $WalletWaterModel->getMoneynumByuserid($userid);
        get_api_result(200, "获取成功", $res);

    }

    /**
     *  用户充值获取签名
     * $pay_method  支付方式  1|支付宝2|微信
     */
    public function rechargegetsign()
    {
        $data["userid"] = I('userid');
        $data["moneynum"] = I('moneynum');
        $data["pay_method"] = I('pay_method');
        $orderModel = D("Order");
        $data["order_no"] = $orderModel->getordercode();  //订单编号

        $RechargeorderModel = D("Rechargeorder");
        $setdealorder = $RechargeorderModel->addpost($data);   //添加订单

        if ($data["pay_method"] == 2) {  //微信
            $wxPay = new \WxPay();
            $NOTIFY_URL = C("HOST") . "IPayCallBack/WeixinReachargeCallBack";
            //$NOTIFY_URL="http://www.baidu.com";
            $order_no = $data["order_no"];
            $txnAmt = intval($data['moneynum'] * 100);
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

        } else if ($data["pay_method"] == 1) { //支付宝
            $AliPayModel = D("AliPay");
            $NOTIFY_URL = C("HOST") . "IPayCallBack/AliReachargeCallBack";
            $order_no = $data["order_no"];
            $money = $data["moneynum"];
            $sign = $AliPayModel->getRequestParam($order_no, $money, $NOTIFY_URL);
            $res["code"] = $order_no;
            $res["money"] = $money;
            $res["pay_method"] = $data["pay_method"];
            $res["sign"] = $sign;
        } else {
            get_api_result(300, "非法操作");
        }
        get_api_result(200, "获取成功", $res);
    }

}
