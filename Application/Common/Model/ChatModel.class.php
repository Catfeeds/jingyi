<?php
/**
 * Created by PhpStorm.
 * User: 丿灬小疯
 * Date: 2017/6/1
 * Time: 16:03
 */

namespace Common\Model;


use Think\Model;

//好友聊天
class ChatModel extends Model
{
    protected $tableName = "chat";

    /**
     * 判断上一条消息是否10分钟以前发的
     */
    public function ischeckmsgtime($userid, $touserid,$airship_stops_id)
    {
        $where = 'from_id = ' . $userid . ' AND to_id = ' . $touserid;
        $where = ['from_id'=>$userid,'to_id'=>$touserid,'airship_stops_id'=>$airship_stops_id];
        $chat = $this->where($where)->order('addtime DESC')->find();

        $res = 0;
        if (time() > $chat['addtime'] + 600) {
            $res = 1;
        }
        return $res;
    }


    /**
     * 添加消息 (好友消息)
     */
    public function addpost($data)
    {
        $data['time_show'] = $this->ischeckmsgtime($data["from_id"], $data["to_id"],$data['airship_stops_id']);
        $data['user_id'] = $data['from_id'];   //那个用户的信息
        $data['addtime'] = time();          //更新时间
        $data['read_weight'] = 1;          //已读

        $char_id1 = $this->add($data);   //消息增加

        $data['user_id'] = $data['to_id'];
        $data['read_weight'] = 0;
        $chat_id2 = $this->add($data);   //消息增加

        $ChatWindowModel = D("ChatWindow");
        $option["to_userid"] = $data["to_id"];
        $option["friend_id"] = $data["from_id"];
        $option["type"] = ($data['stop_type']==2?5:1);
//        $option["type"] = 1;
        $option["content"] = $data["content"];
        $addwindows = $ChatWindowModel->addmsgpost($type = 1, $option);

        $EasemobModel = D("Easemob");

        $from = "jingyi" . $data["from_id"];
        $target_type = "users";
        $target[] = "jingyi" . $data["to_id"];
        $content = $data["content"];
        $UserModel = D("User");
        $userinfo = $UserModel->getuserbasemsgbyuserid($data["from_id"]);
        $ext["title"] = $userinfo["username"] . "(" . $userinfo["tel"] . ")";
        $ext["content"] = $data["content"];
//        $ext["type"] = 1;
        $ext["type"] = $data["stop_type"];//1 好友消息  2飞船停靠聊天消息
        $ext["chatmsg"] = $this->getmychatbychatid($chat_id2);
        $hxres = $EasemobModel->sendText($from, $target_type, $target, $content, $ext);

        return $char_id1;
    }

    /**
     * 添加消息 (同意添加好友消息)
     */
    public function agreeaddfriendpost($data)
    {
        $data['time_show'] = $this->ischeckmsgtime($data["from_id"], $data["to_id"]);
        $data['user_id'] = $data['from_id'];   //那个用户的信息
        $data['addtime'] = time();          //更新时间
        $data['read_weight'] = 1;          //已读

        $char_id1 = $this->add($data);   //消息增加

        $data['user_id'] = $data['to_id'];
        $data['read_weight'] = 0;
        $chat_id3 = $this->add($data);   //消息增加

        $from_id = $data["from_id"];
        $to_id = $data["to_id"];
        $data['user_id'] = $data['to_id'];
        $data['to_id'] = $from_id;
        $data['from_id'] = $to_id;
        $data['read_weight'] = 1;
        $chat_id2 = $this->add($data);   //消息增加

        $data['user_id'] = $from_id;
        $data['read_weight'] = 0;
        $chat_id4 = $this->add($data);   //消息增加


        $ChatWindowModel = D("ChatWindow");
        $option["to_userid"] = $data["to_id"];
        $option["friend_id"] = $data["from_id"];
        $option["type"] = 1;
        $option["content"] = $data["content"];
        $addwindows = $ChatWindowModel->agreeaddfriendaddmsgpost($type = 1, $option);

        $EasemobModel = D("Easemob");

        $from = "jingyi" . $data["from_id"];
        $target_type = "users";
        $target[] = "jingyi" . $data["to_id"];
        $content = $data["content"];
        $UserModel = D("User");
        $userinfo = $UserModel->getuserbasemsgbyuserid($data["from_id"]);
        $ext["title"] = $userinfo["username"] . "(" . $userinfo["tel"] . ")";
        $ext["content"] = $data["content"];
        $ext["type"] = 1;
        $ext["chatmsg"] = $this->getmychatbychatid($chat_id2);
        $hxres = $EasemobModel->sendText($from, $target_type, $target, $content, $ext);


        $from1 = "jingyi" . $data["to_id"];
        $target_type1 = "users";
        $target1[] = "jingyi" . $data["from_id"];
        $content1 = $data["content"];
        $UserModel = D("User");
        $userinfo1 = $UserModel->getuserbasemsgbyuserid($data["to_id"]);
        $ext1["title"] = $userinfo1["username"] . "(" . $userinfo1["tel"] . ")";
        $ext1["content"] = $data1["content"];
        $ext1["type"] = 1;
        $ext1["chatmsg"] = $this->getmychatbychatid($chat_id2);
        $hxres = $EasemobModel->sendText($from1, $target_type1, $target1, $content1, $ext1);

        return $char_id1;
    }


    /**
     * 获取我的好友消息
     */
    public function getmychat($userid, $friendid, $small_chatid,$airship_stops_id)
    {
        $where = "user_id=" . $userid . " and ((from_id=" . $friendid . " and to_id=" . $userid . ") or (from_id=" . $userid . " and to_id=" . $friendid . ") )";
        if ($small_chatid != "") {
            $where .= " and id <" . $small_chatid;
        }
        if($airship_stops_id>0){
            $where .= " and airship_stops_id =" . $airship_stops_id;
        }
        $field = "*,id as chatid";
        $order = "addtime desc";
        $limit = 10;
        $res = $this->field($field)->where($where)->order($order)->limit($limit)->select();
        if (!$res) {
            $res = array();
        }
        if ($res) {
            $UserModel = D("User");
            for ($i = 0; $i < count($res); $i++) {
                $userifo = $UserModel->getuserbasemsgbyuserid($res[$i]["from_id"]);
                $res[$i]["username"] = $userifo["username"];
                $res[$i]["headimg"] = $userifo["headimg"];
                $res[$i]["url"] = imgpath($res[$i]["url"]);
                unset($userifo);
            }
        }
        return $res;
    }

    /**
     * 删除消息
     */
    public function delchat($userid, $chatid)
    {
        $where = "id=" . $chatid . " and user_id=" . $userid;
        $res = $this->where($where)->delete();
        return $res;
    }

    /**
     * 获取好友未读数量
     */
    public function getNoReadnum($userid, $friendid)
    {
        $where = "user_id=" . $userid . " and from_id=" . $friendid . " and to_id=" . $userid . " and read_weight=0 ";
        $res = $this->where($where)->count();
        return $res;
    }


    /**
     * 根据id获取聊天信息
     */
    public function getmychatbychatid($chatid)
    {
        $where = "id=" . $chatid;
        $field = "*,id as chatid";
        $res = $this->field($field)->where($where)->find();
        if (!$res) {
            $res = array();
        }
        if ($res) {
            $UserModel = D("User");
            $userifo = $UserModel->getuserbasemsgbyuserid($res["from_id"]);
            $res["username"] = $userifo["username"];
            $res["headimg"] = $userifo["headimg"];
            $res["url"] = imgpath($res["url"]);

        }
        return $res;
    }

    /**
     *  消息已读
     */
    public function changRead($userid, $friendid)
    {
        $where = "user_id=" . $userid . " and from_id=" . $friendid . " and to_id=" . $userid . " and read_weight=0 ";
        $data["read_weight"] = 1;
        $res = $this->where($where)->save($data);

        $ChatWindowModel = D("ChatWindow");

        $where1 = "to_userid='[" . $userid . "]' and friend_id=" . $friendid . " and type=1";
        $data1["is_reads"] = "[" . $userid . "]";
        $res1 = $ChatWindowModel->editpost($where1, $data1);
        return $res;
    }

}