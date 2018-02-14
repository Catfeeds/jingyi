<?php
/**
 * Created by PhpStorm.
 * User: 丿灬小疯
 * Date: 2017/6/1
 * Time: 16:03
 */

namespace Common\Model;


use Think\Model;

class ChatWindowModel extends Model
{
    protected $tableName = "chat_window";


    /**
     * 判断此用户是否存在该窗体
     * $type  类型 1|好友 2|群  3|好友小助手  4|系统消息
     */
    public function ischeckwindow($type, $option)
    {
        $res = false;
        if ($type == 4) {
            $where = " type=" . $type;
        } else if ($type == 3) {
            $where = "to_userid='" . $option["to_userid"] . "' and type=" . $type;
        } else if ($type == 2) {
            $where = "group_id=" . $option["group_id"] . " and type=" . $type;
        }
        $ischeck = $this->where($where)->find();
        if ($ischeck) {
            $res = true;
        }
        return $res;
    }


    /**
     * 判断此用户是否存在该窗体
     * $type  类型 1|好友 2|群 3|好友小助手  4|系统消息
     */
    public function ischeckgoodfriendwindow($to_userid, $friend_id)
    {
        $res = false;
        $where = "to_userid='[" . $to_userid . "]' and type=1 and friend_id=" . $friend_id;
        $ischeck = $this->where($where)->find();
        if ($ischeck) {
            $res = true;
        }
        return $res;
    }

    /**
     * 添加窗体
     */
    public function addpost($data)
    {
        $data['addtime'] = time();          //更新时间
        $res = $this->add($data);
        return $res;
    }


    /**
     * 修改信息
     */
    public function editpost($where, $data)
    {
        $res = $this->where($where)->save($data);
        if ($res === 0) {
            $res = true;
        }
        return $res;
    }


    /**
     * 删除信息窗体
     */
    public function delpost($id, $userid)
    {
        $where = "windowid=" . $id;
        $info = $this->where($where)->find();
        if (!$info) {
            return false;
        }
        if (strpos($info['is_dels'], '[' . $userid . ']') !== false) {
            return true;
        } else {
            $data["is_dels"] = $info['is_dels'] . "[" . $userid . "]";
            $res = $this->where($where)->save($data);
            return $res;
        }


    }

    /**
     * 删除信息窗体
     */
    public function delfriendwindowspost($userid, $to_userid)
    {
        $where1 = "to_userid='[" . $to_userid . "]' and friend_id=" . $userid . " and type=1";
        $where2 = "to_userid='[" . $userid . "]' and friend_id=" . $to_userid . " and type=1";
        $res1 = $this->where($where1)->delete();
        $res2 = $this->where($where2)->delete();

        return true;


    }


    /**
     * 解散群--删除消息窗体
     */
    public function delgrouppost($group_id)
    {
        $where = "group_id=" . $group_id . " and type=2";

        $res = $this->where($where)->delete();

        return $res;


    }


    /**
     * 离开群--删除消息窗体
     */
    public function delleavegrouppost($group_id, $userid)
    {
        $where = "group_id=" . $group_id . " and type=2";
        $msg = $this->where($where)->find();
        if (!$msg) {
            $res = true;
        } else {
            $data["to_userid"] = str_replace("[" . $userid . "]", "", $msg["to_userid"]);
            $res = $this->where($where)->save($data);
        }
        return $res;


    }


    /**
     * 消息发送，窗体变动
     */
    public function addmsgpost($type, $option)
    {
        if ($type != 1) {
            $ischeck = $this->ischeckwindow($type, $option);
            if (!$ischeck) {
                $res = $this->addpost($option);     //之前没有窗体 添加   有 修改
            } else {
                if ($type == 3) {
                    $where = "to_userid='" . $option["to_userid"] . "' and type=" . $type;

                    $data["is_dels"] = "";
                    $data["is_reads"] = "";
                    $data["addtime"] = time();
                    $data["content"] = $option["content"];
                    $data['organize_id'] = $option["organize_id"];
                    $res = $this->editpost($where, $data);

                } else if ($type == 2) {
                    $where = "group_id=" . $option["group_id"] . " and type=" . $type;
                    $option["is_dels"] = "";
                    $option["is_reads"] = "";
                    $option["addtime"] = time();
                    $res = $this->editpost($where, $option);
                }
            }
        } else {
            $ischeckA = $this->ischeckgoodfriendwindow($option["to_userid"], $option["friend_id"]);

            if ($ischeckA) {
                $data1["is_dels"] = "";
                $data1["is_reads"] = "";
                $data1["addtime"] = time();
                $data1["content"] = $option["content"];
                $where1 = "to_userid='[" . $option["to_userid"] . "]' and type=1 and friend_id=" . $option["friend_id"];
                $res = $this->editpost($where1, $data1);
            } else {
                $option1 = $option;
                $option1["to_userid"] = "[" . $option["to_userid"] . "]";
                $option1["addtime"] = time();
                $res = $this->addpost($option1);
            }
            $ischeckB = $this->ischeckgoodfriendwindow($option["friend_id"], $option["to_userid"]);
            if ($ischeckB) {
                $data2["is_dels"] = "";
                $data2["is_reads"] = "[" . $option["friend_id"] . "]";
                $data2["addtime"] = time();
                $data2["content"] = $option["content"];
                $where2 = "to_userid='[" . $option["friend_id"] . "]' and type=1 and friend_id=" . $option["to_userid"];
                $res = $this->editpost($where2, $data2);
            } else {
                $option2 = $option;
                $option2["to_userid"] = "[" . $option["friend_id"] . "]";
                $option2["friend_id"] = $option["to_userid"];
                $option2["is_reads"] = "[" . $option["friend_id"] . "]";
                $option2["addtime"] = time();
                $res = $this->addpost($option2);
            }
        }
        return $res;
    }


    /**
     * 消息发送，窗体变动
     */
    public function agreeaddfriendaddmsgpost($type, $option)
    {
        if ($type != 1) {
            $ischeck = $this->ischeckwindow($type, $option);
            if (!$ischeck) {
                $res = $this->addpost($option);     //之前没有窗体 添加   有 修改
            } else {
                if ($type == 3) {
                    $where = "to_userid='" . $option["to_userid"] . "' and type=" . $type;

                    $data["is_dels"] = "";
                    $data["is_reads"] = "";
                    $data["addtime"] = time();
                    $data["content"] = $option["content"];
                    $data['organize_id'] = $option["organize_id"];
                    $res = $this->editpost($where, $data);

                } else if ($type == 2) {
                    $where = "group_id=" . $option["group_id"] . " and type=" . $type;
                    $option["is_dels"] = "";
                    $option["is_reads"] = "";
                    $option["addtime"] = time();
                    $res = $this->editpost($where, $option);
                }
            }
        } else {
            $ischeckA = $this->ischeckgoodfriendwindow($option["to_userid"], $option["friend_id"]);

            if ($ischeckA) {
                $data1["is_dels"] = "";
                $data1["is_reads"] = "";
                $data1["addtime"] = time();
                $data1["content"] = $option["content"];
                $where1 = "to_userid='[" . $option["to_userid"] . "]' and type=1 and friend_id=" . $option["friend_id"];
                $res = $this->editpost($where1, $data1);
            } else {
                $option1 = $option;
                $option1["to_userid"] = "[" . $option["to_userid"] . "]";
                $option1["addtime"] = time();
                $res = $this->addpost($option1);
            }
            $ischeckB = $this->ischeckgoodfriendwindow($option["friend_id"], $option["to_userid"]);
            if ($ischeckB) {
                $data2["is_dels"] = "";
                $data2["is_reads"] = "";
                $data2["addtime"] = time();
                $data2["content"] = $option["content"];
                $where2 = "to_userid='[" . $option["friend_id"] . "]' and type=1 and friend_id=" . $option["to_userid"];
                $res = $this->editpost($where2, $data2);
            } else {
                $option2 = $option;
                $option2["to_userid"] = "[" . $option["friend_id"] . "]";
                $option2["friend_id"] = $option["to_userid"];
                $option2["is_reads"] = "";
                $option2["addtime"] = time();
                $res = $this->addpost($option2);
            }
        }
        return $res;
    }


    /**
     * 获取用户的消息窗口列表
     */
    public function getwindowslist($where, $order, $userid)
    {
        $res = $this->where($where)->order($order)->select();
        if (count($res) == 0) {
            $result = array();
        } else {
            $UserModel = D("User");
            $GroupModel = D("Group");
            $SystemChatModel = D("SystemChat");
            $GroupChatModel = D("GroupChat");
            $SystemMessageModel = D("SystemMessage");
            $ChatModel = D("Chat");
            for ($i = 0; $i < count($res); $i++) {
                if ($res[$i]["type"] == 1) {  //好友信息
                    $usermsg = $UserModel->getuserbasemsgbyuserid($res[$i]["friend_id"]);
                    $result[$i]["relationid"] = $res[$i]["friend_id"];
                    $result[$i]["headimg"] = $usermsg["headimg"];
                    $result[$i]["title"] = $usermsg["username"];
                    $result[$i]["tel"] = $usermsg["tel"];
                    $result[$i]["type"] = $res[$i]["type"];
                    $result[$i]["content"] = $res[$i]["content"];
                    $result[$i]["windowid"] = $res[$i]["windowid"];
                    $result[$i]["addtime"] = $res[$i]["addtime"];

                    $result[$i]["noreadnum"] = $ChatModel->getNoReadnum($userid, $res[$i]["friend_id"]);
                    unset($usermsg);
                } else if ($res[$i]["type"] == 2) {
                    $groupmsg = $GroupModel->getbasemsg($res[$i]["group_id"]);
                    $result[$i]["relationid"] = $groupmsg["groupid"];
                    $result[$i]["headimg"] = $groupmsg["headimg"];
                    $result[$i]["title"] = $groupmsg["name"];
                    $result[$i]["content"] = $res[$i]["content"];
                    $result[$i]["type"] = $res[$i]["type"];
                    $result[$i]["windowid"] = $res[$i]["windowid"];
                    $result[$i]["addtime"] = $res[$i]["addtime"];

                    $result[$i]["noreadnum"] = $GroupChatModel->getNoReadnum($userid, $res[$i]["group_id"]);
                    unset($groupmsg);
                } else if ($res[$i]["type"] == 3) {  //好友小助手
                    $result[$i]["relationid"] = "";
                    $result[$i]["headimg"] = "";
                    $result[$i]["title"] = "好友小助手";
                    $result[$i]["content"] = $res[$i]["content"];
                    $result[$i]["type"] = $res[$i]["type"];
                    $result[$i]["windowid"] = $res[$i]["windowid"];
                    $result[$i]["addtime"] = $res[$i]["addtime"];
                    $result[$i]["noreadnum"] = $SystemChatModel->getNoReadnum($userid);
                } else if ($res[$i]["type"] == 4) {  //系统
                    $result[$i]["relationid"] = "";
                    $result[$i]["headimg"] = "";
                    $result[$i]["title"] = "净一通知";
                    $result[$i]["content"] = $res[$i]["content"];
                    $result[$i]["type"] = $res[$i]["type"];
                    $result[$i]["windowid"] = $res[$i]["windowid"];
                    $result[$i]["addtime"] = $res[$i]["addtime"];
                    $result[$i]["noreadnum"] = $SystemMessageModel->getNoReadnum($userid);
                }
            }
        }
        return $result;
    }


    /**
     * 获取用户未读消息数量
     */
    public function getnoreadmsgnum($userid)
    {
        $where = "to_userid LIKE '%[" . $userid . "]%' and is_reads not like '%[" . $userid . "]%' and is_dels not like '%[" . $userid . "]%' and type < 5";

        $res = $this->where($where)->count();

        return $res;


    }


    /**
     * 获取用户查询的好友聊天信息
     */
    public function getfriendchatid($keywords)
    {


        return $res;


    }

    /**
     * 根据条件获取群消息信息
     */
    public function getchatwindowsinfo($where)
    {
        $res = $this->where($where)->find();
        return $res;


    }


}