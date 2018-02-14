<?php
namespace Common\Model;
use Think\Model;

/**支付支付**/
class AliPayModel extends Model
{
    private $aliPayApi;

    public function __construct()
    {
        vendor("alipay.aliPayApi");
        $this->aliPayApi = new \aliPayApi();
    }

    /**
     *  获取带签名的请求体
     * @param string $order_no  订单号
     * @param float $money   交易金额 单位：元
     * @param string $NOTIFY_URL   回调地址
     * @return string
     */
    public function getRequestParam($order_no, $money, $NOTIFY_URL)
    {
        $request = [
                        "partner" => PARTNER, 
                        "seller_id" => SELLER_ID,
                        "out_trade_no" => $order_no,
                        "subject" => "APP在线支付",
                        "body" => "APP在线支付",
                        "total_fee" => $money,
                        "notify_url" =>$NOTIFY_URL,
                        "service" => "mobile.securitypay.pay",
                        "payment_type" => "1",
                        "_input_charset" => "utf-8", "it_b_pay" => "30m",
            //            "return_url" => "m.alipay.com"
                     ];
        $request_str = $this->aliPayApi->getRequestStr(($request));
        $sign = $this->aliPayApi->rsaSign($request_str);
        $request['sign'] = urlencode($sign);
        $request['sign_type'] = "RSA";
        $result = $this->aliPayApi->getRequestStr($request);
        return $result;
    }


}
