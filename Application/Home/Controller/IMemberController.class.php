<?php

namespace Home\Controller;

use Think\Controller;

//会员管理
class IMemberController extends CommonController
{


    /**
     *  添加订单
     */
    public function addorderpost()
    {

        $data["userid"] = $userid = I('userid');
        $data["order_no"] = build_order_no();
        $data["pay_method"] = I('pay_method');
        if ($data["pay_method"] > 3 && $data["pay_method"] < 1) {
            $msg = "支付类型错误。";
            get_api_result(300, $msg);
        }

        $MemberModel = D("Member");
        $ischeck = $MemberModel->ischeckmember($userid);
        if ($ischeck) {
            $msg = "您已经是会员。";
            get_api_result(300, $msg);
        }
        $res = $MemberModel->addpost($data);  //创建订单

        if ($data["pay_method"] == 3) {
            $WalletWaterModel = D("WalletWater");
            $usermoney = $WalletWaterModel->getMoneynumByuserid($userid);  //用户钱包钱数
            $res1["order_no"] = $data["order_no"];  //订单编号
            $res1["usermoneynum"] = $usermoney;  //用户钱包钱数
        } else if ($data["pay_method"] == 2) {  //微信


        } else { //支付宝
            $AliPayModel = D("AliPay");
            $NOTIFY_URL = C("HOST") . "IPayCallBack/AliPayMCallBack";
            $order_no = $data["order_no"];
            $money = C("membermoney");
            $sign = $AliPayModel->getRequestParam($order_no, $money, $NOTIFY_URL);
            $res1["code"] = $order_no;
            $res1["money"] = $money;
            $res1["pay_method"] = $data["pay_method"];
            $res1["sign"] = $sign;
        }
        //返回支付签名
        if ($res) {   //添加成功
            $msg = "添加成功";
            get_api_result(200, $msg, $res1);
        } else {  //添加失败
            $msg = "添加失败";
            get_api_result(300, $msg);
        }
    }


    /**
     *  获取会员卡金额
     */
    public function getMeMbermoney()
    {
        $data["moneynum"] = C("membermoney");
        $msg = "获取成功";
        get_api_result(200, $msg, $data);

    }


    /**
     *  余额支付
     */
    public function UserMoneyPay()
    {
        $userid = I('userid');
        $order_no = I('order_no');
        $paypassword = md5(I('paypassword'));
        $moneynum = C("membermoney");
        $WalletWaterModel = D("WalletWater");
        $usermoney = $WalletWaterModel->getMoneynumByuserid($userid);  //用户钱包钱数
        if ($usermoney < $moneynum) {
            $msg = "余额不足！";
            get_api_result(300, $msg);
        }
        $UserModel = D("User");
        $ischeckpay = $UserModel->checkpayPassword($userid, $paypassword); //判断支付密码是否正确
        if (!$ischeckpay) {
            $msg = "支付密码错误！";
            get_api_result(300, $msg);
        }
        $Model = M();
        $Model->startTrans();

        $data["userid"] = $userid;
        $data["moneynum"] = $moneynum;
        $data["type"] = 2;
        $res = $WalletWaterModel->addpost($data);    //用户流水增加

        $nowmoney = $moneynum;
        $MemberModel = D("Member");
        $res1 = $MemberModel->updatesuccess($order_no, $trade_no = "", $nowmoney);  //支付成功回调

        if ($res && $res1) {
            $Model->commit();
            $msg = "操作成功";
            get_api_result(200, $msg);
        } else {
            $Model->rollback();
            $msg = "操作失败";
            get_api_result(300, $msg);
        }


    }


}