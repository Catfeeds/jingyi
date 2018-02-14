<?php

namespace Home\Controller;

use Think\Controller;

class IPayCallBackController extends CommonController
{
    private $aliPayApi;

    public function __construct()
    {
        vendor("alipay.aliPayApi");
        $this->aliPayApi = new \aliPayApi();
        vendor("wxpay.lib.WxPay#Api");
    }

    /**
     * 支付宝订单回调接口
     */
    public function AliPayCallBack()
    {

        if (I('out_trade_no') && I('trade_status') == 'TRADE_SUCCESS') {
            $code = I('out_trade_no');  // 支付宝返回的订单号
            $order_no = I('out_trade_no') ? I('out_trade_no') : "";
            $money = I('total_fee');  //支付的金额
            $DealOrderModel = D("DealOrder");
            $res = $DealOrderModel->successcallback($code, $order_no, $money, 1);
            if ($res) {
                echo "success";
            } else {
                echo "fail";
            }

        }


    }

    /**
     * 支付宝会员卡回调接口
     */
    public function AliReachargeCallBack()
    {

        if (I('out_trade_no') && I('trade_status') == 'TRADE_SUCCESS') {
            $order_no = I('out_trade_no');  // 支付宝返回的订单号
            $trade_no = I('out_trade_no') ? I('out_trade_no') : "";
            $nowmoney = I('total_fee');  //支付的金额
            $RechargeorderModel = D("Rechargeorder");
            $res = $RechargeorderModel->updatesuccess($order_no, $trade_no, $nowmoney);

            if ($res) {
                echo "success";
            } else {
                echo "fail";
            }

        }


    }

    /**
     * 微信订单回调接口
     */
    public function WeixinPayCallBack()
    {
        $request_body = file_get_contents("php://input");
        $myfile = fopen("weixincallback.txt", "a+");
        $txt = "微信参数\n";
        fwrite($myfile, $txt);
        $txt = $request_body . "\n";
        fwrite($myfile, $txt);
        fclose($myfile);

        $xml = simplexml_load_string($request_body, 'SimpleXMLElement', LIBXML_NOCDATA);
        if (strval($xml->return_code) == 'SUCCESS' && strval($xml->out_trade_no)) {
            $code = strval($xml->out_trade_no);  // 订单编号
            $order_no = strval($xml->transaction_id);
            $money = ($xml->total_fee) / 100;   //支付的金额
            $DealOrderModel = D("DealOrder");
            $res = $DealOrderModel->successcallback($code, $order_no, $money, 2);
            if ($res) {
                echo '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
            }

        }


    }

    /**
     * 微信充值回调
     */
    public function WeixinReachargeCallBack()
    {

        $request_body = file_get_contents("php://input");
        $xml = simplexml_load_string($request_body, 'SimpleXMLElement', LIBXML_NOCDATA);
        if (strval($xml->return_code) == 'SUCCESS' && strval($xml->out_trade_no)) {
            $order_no = strval($xml->out_trade_no);  // 订单编号
            $trade_no = strval($xml->transaction_id);  //交易号
            $nowmoney = ($xml->total_fee) / 100;   //支付的金额
            $RechargeorderModel = D("Rechargeorder");
            $res = $RechargeorderModel->updatesuccess($order_no, $trade_no, $nowmoney);
            if ($res) {
                echo '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
            }

        }
    }


}
