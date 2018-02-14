<?php
class WxpayService
{
    protected $mchid;
    protected $appid;
    protected $key;
    public function __construct()
    {
        $this->mchid = '1452376402'; // 微信支付商户号 PartnerID 通过微信支付商户资料审核后邮件发送
        $this->appid = 'wx4d875b5d3570aa32'; //公众号APPID 通过微信支付商户资料审核后邮件发送
        $this->key = 'cfdb507d1d5c7053e8e66c5803b34c38';   //https://pay.weixin.qq.com 帐户设置-安全设置-API安全-API密钥-设置API密钥
    }
    /**
     * @param string $openid 调用【网页授权获取用户信息】接口获取到用户在该公众号下的Openid
     * @param float $totalFee 收款总费用 单位元
     * @param string $outTradeNo 唯一的订单号
     * @param string $orderName 订单名称
     * @param string $notifyUrl 支付结果通知url 不要有问号
     *   https://mp.weixin.qq.com/ 微信支付-开发配置-测试目录
     *   测试目录 http://mp.izhanlue.com/paytest/  最后需要斜线，(需要精确到二级或三级目录)
     * @return string
     */
    public function createJsBizPackage($totalFee, $outTradeNo, $orderName, $notifyUrl, $timestamp,$attachStr='')
    {
        $config = array(
            'mch_id' => $this->mchid,
            'appid' => $this->appid,
            'key' => $this->key,
        );
        $unified = array(
            'appid' => $config['appid'],
            'mch_id' => $config['mch_id'],
            'nonce_str' => self::createNonceStr(),
            'body' => $orderName,
            'out_trade_no' => $outTradeNo,
            'total_fee' => intval($totalFee * 100),
            'spbill_create_ip' => $_SERVER['REMOTE_ADDR'],
            'trade_type' => 'APP',
            'notify_url' => $notifyUrl,
            'attach' => $attachStr

        );
        $unified['sign'] = $this->MakeSign($unified,$config['key']);//self::getSign($unified, $config['key']);
        $responseXml = self::curlPost('https://api.mch.weixin.qq.com/pay/unifiedorder', self::arrayToXml($unified));
        /*
        <xml>
        <return_code><![CDATA[SUCCESS]]></return_code>
        <return_msg><![CDATA[OK]]></return_msg>
        <appid><![CDATA[wx00e5904efec77699]]></appid>
        <mch_id><![CDATA[1220647301]]></mch_id>
        <nonce_str><![CDATA[1LHBROsdmqfXoWQR]]></nonce_str>
        <sign><![CDATA[ACA7BC8A9164D1FBED06C7DFC13EC839]]></sign>
        <result_code><![CDATA[SUCCESS]]></result_code>
        <prepay_id><![CDATA[wx2015032016590503f1bcd9c30421762652]]></prepay_id>
        <trade_type><![CDATA[JSAPI]]></trade_type>
        </xml>
        */

        $simplexmlObj = simplexml_load_string($responseXml, 'SimpleXMLElement', LIBXML_NOCDATA);
        $unifiedOrder = json_decode(json_encode($simplexmlObj),true);

        if ($unifiedOrder === false) {
            die('parse xml error');
        }
        if ($unifiedOrder['return_code'] != 'SUCCESS') {
            die($unifiedOrder['return_msg']);
        }
        if ($unifiedOrder['result_code'] != 'SUCCESS') {
            die($unifiedOrder['err_code']);
        }

        $arr = array(
            "appid" => $config['appid'],
            "timestamp" => $timestamp,
            "noncestr" => self::createNonceStr(),
            'partnerid' => $config['mch_id'],
            'prepayid' => $unifiedOrder['prepay_id'],
            'package' => 'Sign=WXPay',
        );
        $arr['sign'] = $this->MakeSign($arr,$config['key']);//self::getSign($arr, $config['key']);
        return $arr;
    }

    /**
     * 微信回调验证
     * @return SimpleXMLElement
     */
    public function wxNotify()
    {
        $config = array(
            'mch_id' => $this->mchid,
            'appid' => $this->appid,
            'key' => $this->key,
        );

        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

        /*$fp = fopen("./test666.text", "w+");
        fwrite($fp,$postStr);
        fclose($fp);*/

        $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);

        $postArr = json_decode(json_encode($postObj),true);


        if ($postArr === false) {
            die('parse xml error');
        }
        if ($postArr['return_code'] != 'SUCCESS') {
            die($postArr['return_msg']);
        }
        if ($postArr['result_code'] != 'SUCCESS') {
            die($postArr['err_code']);
        }
        $sign = $postArr['sign'];
        unset($postArr['sign']);
        if ($this->MakeSign($postArr,$config['key']) == $sign) {
            return $postArr;
        }else{
            return false;
        }
    }
    /**
     * curl get
     *
     * @param string $url
     * @param array $options
     * @return mixed
     */
    public static function curlGet($url = '', $options = array())
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        if (!empty($options)) {
            curl_setopt_array($ch, $options);
        }
        //https请求 不验证证书和host
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
    public static function curlPost($url = '', $postData = '', $options = array())
    {
        if (is_array($postData)) {
            $postData = http_build_query($postData);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); //设置cURL允许执行的最长秒数
        if (!empty($options)) {
            curl_setopt_array($ch, $options);
        }
        //https请求 不验证证书和host
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
    public static function createNonceStr($length = 32)
    {
        //$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }


    public static function arrayToXml($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
        }
        $xml .= "</xml>";
        return $xml;
    }
    /**
     * 例如：
     * appid：  wxd930ea5d5a258f4f
     * mch_id：  10000100
     * device_info： 1000
     * Body：  test
     * nonce_str： ibuaiVcKdpRxkhJA
     * 第一步：对参数按照 key=value 的格式，并按照参数名 ASCII 字典序排序如下：
     * stringA="appid=wxd930ea5d5a258f4f&body=test&device_info=1000&mch_i
     * d=10000100&nonce_str=ibuaiVcKdpRxkhJA";
     * 第二步：拼接支付密钥：
     * stringSignTemp="stringA&key=192006250b4c09247ec02edce69f6a2d"
     * sign=MD5(stringSignTemp).toUpperCase()="9A0A8659F005D6984697E2CA0A9CF3B7"
     */
    public static function getSign($params, $key)
    {
        ksort($params, SORT_STRING);
        $unSignParaString = self::formatQueryParaMap($params, false);
        $signStr = strtoupper(md5($unSignParaString . "&key=" . $key));
        return $signStr;
    }
    protected static function formatQueryParaMap($paraMap, $urlEncode = false)
    {
        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v) {
            if (null != $v && "null" != $v) {
                if ($urlEncode) {
                    $v = urlencode($v);
                }
                $buff .= $k . "=" . $v . "&";
            }
        }
        $reqPar = '';
        if (strlen($buff) > 0) {
            $reqPar = substr($buff, 0, strlen($buff) - 1);
        }
        return $reqPar;
    }

    /**
     * 生成签名
     * @return 签名，本函数不覆盖sign成员变量，如要设置签名需要调用SetSign方法赋值
     */
    protected function MakeSign($params, $key)
    {
        //签名步骤一：按字典序排序参数
        ksort($params);
        $string = $this->ToUrlParams($params);
        //签名步骤二：在string后加入KEY
        $string = $string . "&key=".$key;
        //签名步骤三：MD5加密
        $string = md5($string);
        //签名步骤四：所有字符转为大写
        $result = strtoupper($string);
        return $result;
    }

    /**
     * 格式化参数格式化成url参数
     */
    private function ToUrlParams($params)
    {
        $buff = "";
        foreach ($params as $k => $v)
        {
            if($k != "sign" && $v != "" && !is_array($v)){
                $buff .= $k . "=" . $v . "&";
            }
        }

        $buff = trim($buff, "&");
        return $buff;
    }

}