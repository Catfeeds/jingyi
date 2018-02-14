<?php

namespace Common\Model;

use Think\Model;

/**消息推送**/
class JpushModel extends Model
{
    protected $tableName = 'order_accept';

    public function __construct()
    {
        vendor('JPush.Client');
        vendor('JPush.Config');
        vendor('JPush.Http');
        vendor('JPush.PushPayload');
        vendor('JPush.Exceptions.JPushException');
        vendor('JPush.Exceptions.APIRequestException');
        vendor('JPush.Exceptions.APIConnectionException');
    }

    /*推送消息
    *@param type  消息类型  1|全体推送  2|别名推送
    *@param alias  别名  数组
    *@param Alert  别名  数组
    *@param msg_content  消息内容 string
    *@param extras  扩展字段  数组
    */
    public function sendmsg($type, $alias, $Alert, $msg_content, $extras)
    {
        $app_key = C("jpush_app_key");
        $master_secret = C("jpush_master_secret");
        $options["time_to_live"] = 86400;   //推送当前用户不在线时，为该用户保留多长时间的离线消息，以便其上线时再次推送。
        $options["apns_production"] = C("jpush_apns_production");

        if (empty($type)) {
            $type = 1;
        }
        if (empty($Alert)) {
            $Alert = "有新消息。";
        }
        if (empty($msg_content)) {
            $msg_content = "有新消息。";
        }
        if (empty($extras)) {
            $extras = array();
        }
        $msg["extras"] = $extras;

        $client = new \JPush\Client($app_key, $master_secret);
        $pusher = $client->push();
        $pusher->setPlatform('all');
        if ($type == 1) {
            $pusher->setAudience('all');
        } else {
            $pusher->addAlias($alias);
        }
        $pusher->setNotificationAlert($Alert);
        $msg1 = $msg;
        $msg1["sound"] = "1312";
        $pusher->iosNotification($Alert, $msg1);
        $pusher->androidNotification($Alert, $msg);
        $pusher->message($msg_content, $msg);
        //$pusher->extras($msg["extras"]);
        $pusher->options($options);
        try {
            $res = $pusher->send();
            return $res;
        } catch (\JPush\Exceptions\JPushException $e) {
            // try something else here
            return $e;
        }


    }


}