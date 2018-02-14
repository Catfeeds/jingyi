<?php
/**
 * Created by PhpStorm.
 * User: walter
 * Date: 2016/8/24
 * Time: 9:58
 */

class WeiXin {
    public function __construct(){
        vendor("weixin.WeiXin#Api");
    }

    /**
     * 获取认证URL
     * @param $token
     * @return string
     */
    public function AuthUrl($token){
        $wx = new WeiXinApi();
        $redirect_url = "http://ykm.cdnhxx.com/youkongmei/Admin/WeiXinCallBack/auth?token=".$token;
        $url = $wx->getAuthUrl($redirect_url);
        return $url;
    }
}