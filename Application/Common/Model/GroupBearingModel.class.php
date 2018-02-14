<?php

namespace Common\Model;

use Think\Model;

/* * 群人员* */

class GroupBearingModel extends Model
{

    //人员加入 type  1|创建群 2|添加群成员
    public function addpost($data, $type)
    {
        $data["addtime"] = time();
        $res = $this->add($data);

        if ($type == 2) {
            $GroupModel = D("Group");
            $groupmsg = $GroupModel->getbasemsg($data["groupid"]);
            $EasemobModel = D("Easemob");
            $group_id = $groupmsg["hx_groupid"];
            $usernames['usernames'] = array("0" => "jingyi" . $data["userid"]);
            $hxres = $EasemobModel->register_hx_group_add($group_id, $usernames);
        }
        return $res;
    }


    //人员批量加入 type  1|创建群 2|添加群成员
    public function addallpost($groupid, $useridarr)
    {
        $addtime = time();
        for ($i = 0; $i < count($useridarr); $i++) {
            $ischeck[$i] = $this->ischeckjoingroup($groupid, $useridarr[$i]);
            if (!$ischeck[$i]) {
                $userarrid[$i] = "jingyi" . $useridarr[$i];
                $data[$i]["userid"] = $useridarr[$i];
                $data[$i]["groupid"] = $groupid;
                $data[$i]["role"] = 3;
                $data[$i]["addtime"] = $addtime;
            }

        }
        $res = $this->addAll($data);

        $GroupModel = D("Group");
        $groupmsg = $GroupModel->getbasemsg($groupid);   //获取群信息
        $EasemobModel = D("Easemob");
        $group_id = $groupmsg["hx_groupid"];   //群环信id
        $usernames['usernames'] = $userarrid;
        $hxres = $EasemobModel->register_hx_group_add($group_id, $usernames);

        return $res;
    }

    //修改信息
    public function editpost($where, $data)
    {

        $res = $this->where($where)->save($data);
        if ($res === 0) {
            $res = true;
        }
        return $res;
    }

    //获取群人员信息
    public function getuserlistbyGroupid($groupid, $limit)
    {
        $where = "a.`groupid`=" . $groupid;
        $field = "a.userid,a.role,user.headimg,user.username";
        $join = "user ON a.userid=user.userid";
        $order = "a.role asc";
        $res = $this->alias("a")->field($field)->where($where)->join($join)->limit($limit)->order($order)->select();
        if ($res) {
            for ($i = 0; $i < count($res); $i++) {
                $res[$i]["headimg"] = imgpath($res[$i]["headimg"]);
            }

        }
        if (!$res) {
            $res = array();
        }
        return $res;
    }


    //获取群人员id
    public function getuseridlistbyGroupid($groupid)
    {
        $where = "`groupid`=" . $groupid;
        $res = $this->where($where)->getField('userid', true);
        if (!$res) {
            $res = array();
        }
        return $res;
    }


    //获取用户信息
    public function getusermsg($groupid, $userid)
    {
        $where = "groupid=" . $groupid . " and userid=" . $userid;
        $res = $this->where($where)->find();
        return $res;
    }

    //退出群（除了群主之外的人员）
    public function leavegroup($groupid, $userid)
    {
        $GroupModel = D("Group");
        $groupmsg = $GroupModel->getbasemsg($groupid);   //获取群信息
        $EasemobModel = D("Easemob");
        $group_id = $groupmsg["hx_groupid"];   //群环信id
        $username = "jingyi" . $userid;
        $hxres = $EasemobModel->register_hx_group_leave($group_id, $username);

        $where = "groupid=" . $groupid . " and userid=" . $userid . " and role<>1";
        $res = $this->where($where)->delete();

        $ChatWindowModel = D("ChatWindow");
        $res2 = $ChatWindowModel->delleavegrouppost($groupid, $userid);    //删除窗体

        return $res;
    }


    //踢出群
    public function shotoffgroup($groupid, $userid)
    {
        $GroupModel = D("Group");
        $groupmsg = $GroupModel->getbasemsg($groupid);   //获取群信息
        $EasemobModel = D("Easemob");
        $group_id = $groupmsg["hx_groupid"];   //群环信id
        $username = "jingyi" . $userid;
        $hxres = $EasemobModel->register_hx_group_del($group_id, $username);

        $where = "groupid=" . $groupid . " and userid=" . $userid . " and role<>1";
        $res = $this->where($where)->delete();

        return $res;
    }

    //解散群（群主）
    public function delgroup($groupid)
    {

        $GroupModel = D("Group");
        $groupmsg = $GroupModel->getbasemsg($groupid);   //获取群信息
        $EasemobModel = D("Easemob");
        $group_id = $groupmsg["hx_groupid"];   //群环信id
        $hxres = $EasemobModel->register_hx_group_destroy($group_id);

        $where = "groupid=" . $groupid;
        $res = $this->where($where)->delete();
        return $res;
    }


    //获取我参与的群id
    public function getGroupidbyuserid($userid)
    {
        $where = "`userid`=" . $userid;
        $res = $this->where($where)->getField('groupid', true);
        if (!$res) {
            $res = array();
        }
        return $res;
    }


    /**
     * 获取可邀请加入的好友信息列表
     */
    public function getUserFriendList($groupid, $userid)
    {

        $groupuserid = $this->getuseridlistbyGroupid($groupid);  //加入群的用户id
        $string = implode(",", $groupuserid);
        $UFModel = M("user_friend");
        //此用户添加其他用户,添加好友成功了状态为2
        $field = "(case when userid = " . $userid . " THEN user_friend_id  when user_friend_id = " . $userid . " THEN  userid END ) as friendid";
        $where = "(userid=" . $userid . " or user_friend_id=" . $userid . ") and agree_status=1";
        $subQuery = $UFModel->field($field)->where($where)->select(false);

        $Model = M();
        $field1 = "user.userid,user.username,user.autograph,user.headimg,user.sex,user.level";
        $join = "LEFT  JOIN user ON user.userid = a.friendid";
        $where1 = "a.friendid not in (" . $string . ")";
        $res = $Model->table("(" . $subQuery . ") a")->field($field1)->join($join)->where($where1)->select();
        if (!$res) {
            $res = array();
        }
        if ($res) {
            for ($i = 0; $i < count($res); $i++) {
                $res[$i]["headimg"] = imgpath($res[$i]["headimg"]);
            }
        }


        return $res;
    }

    //判断是否已经加入群
    public function ischeckjoingroup($groupid, $userid)
    {
        $where = "groupid=" . $groupid . " and userid=" . $userid;
        $res = $this->where($where)->find();
        return $res;
    }

    //获取管理员数量
    public function getadminnum($groupid)
    {
        $where = "groupid=" . $groupid . " and role=2";
        $res = $this->where($where)->count();
        return $res;
    }

}
