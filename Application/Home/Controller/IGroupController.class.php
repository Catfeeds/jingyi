<?php

namespace Home\Controller;

use Think\Controller;

//群信息
class IGroupController extends CommonController
{

    /**
     * 创建组
     */
    public function addgroup()
    {
        $data["userid"] = I('userid');
        $data["name"] = shielding(I('name'));
        $data["intro"] = shielding(I('intro'));

        $savefilepath = "groupimg";
        $upload = new \Think\Upload(); // 实例化上传类
        $upload->maxSize = 1024 * 1024 * 20;
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');
        $upload->rootPath = './'; // 设置附件上传目录
        $upload->savePath = './Public/upload/' . $savefilepath . "/"; // 设置附件上传目录
        $upload->saveName = array('uniqid', '');
        $upload->ischeckfile = true;  //必须上传文件
        $info = $upload->upload();
        if (!$info) { // 上传错误提示错误信息
            $msg = $upload->getError();
            get_api_result(300, $msg);  //图片上传失败
        } else { //上传成功获取上传文件信息
            foreach ($info as $file) {
                $data["headimg"] = str_replace('./Public', 'Public', $file['savepath']) . $file['savename'];
            }
        }

        $GroupModel = D('Group');
        $num = $GroupModel->getGroupNum($data["userid"]);
        if ($num >= 5) {
            get_api_result(300, "创建失败,每人最多创建5个群。");
        }
        $res = $GroupModel->addpost($data);
        if ($res) {
            get_api_result(200, "创建成功", $res);
        } else {
            get_api_result(300, "创建失败");
        }
    }


    /**
     * 修改群信息
     */
    public function editgroupmsg()
    {
        $groupid = I('groupid');
        $where = "groupid=" . $groupid;
        if (I('affiche')) {  //公告
            $data["affiche"] = shielding(I('affiche'));
        }
        if (I('name')) { //群名称
            $data["name"] = shielding(I('name'));
        }
        if (I('intro')) { //简介
            $data["intro"] = shielding(I('intro'));
        }
        if ($_FILES) { //简介
            $savefilepath = "groupimg";
            $upload = new \Think\Upload(); // 实例化上传类
            $upload->maxSize = 1024 * 1024 * 20;
            $upload->exts = array('jpg', 'gif', 'png', 'jpeg');
            $upload->rootPath = './'; // 设置附件上传目录
            $upload->savePath = './Public/upload/' . $savefilepath . "/"; // 设置附件上传目录
            $upload->saveName = array('uniqid', '');
            $upload->ischeckfile = true;  //必须上传文件
            $info = $upload->upload();
            if (!$info) { // 上传错误提示错误信息
                $msg = $upload->getError();
                get_api_result(300, $msg);  //图片上传失败
            } else { //上传成功获取上传文件信息
                foreach ($info as $file) {
                    $data["headimg"] = str_replace('./Public', 'Public', $file['savepath']) . $file['savename'];
                }
            }
        }

        $GroupModel = D('Group');
        $res = $GroupModel->editpost($where, $data);
        if ($res) {
            get_api_result(200, "操作成功");
        } else {
            get_api_result(300, "请刷新后在操作");
        }
    }

    /**
     *获取群详细信息
     */
    public function getgroupinfobyid()
    {
        $userid = I('userid');
        $groupid = I('groupid');
        $GroupModel = D("Group");
        $GroupBearingModel = D("GroupBearing");
        $res["memberlist"] = $GroupBearingModel->getuserlistbyGroupid($groupid, $limit = 11);//群成员
        $res["groupmsg"] = $GroupModel->getbasemsg($groupid);//群信息
        $myinfo = $GroupBearingModel->getusermsg($groupid, $userid);
        $res["mygroupinfo"] = $myinfo["role"];//我的群身份  1| 群主  2|管理员  3|普通成员

        get_api_result(200, "获取成功", $res);

    }

    /**
     *获取群成员列表信息
     */
    public function getgroupmemberlist()
    {
        $groupid = I('groupid');
        $GroupBearingModel = D("GroupBearing");
        $res = $GroupBearingModel->getuserlistbyGroupid($groupid, $limit = "");//群成员
        get_api_result(200, "获取成功", $res);

    }

    /**
     *获取群成员详细信息
     */
    public function getgroupmemberinfo()
    {
        $groupid = I('groupid');
        $userid = I('userid');
        $beuserid = I('beuserid');
        $GroupBearingModel = D("GroupBearing");
        $info = $GroupBearingModel->getusermsg($groupid, $userid);

        $UserFriendModel = D('UserFriend');
        $ischeck = $UserFriendModel->ischeckgoodfriend($userid, $beuserid);
        $UserModel = D('User');
        $res = $UserModel->getusermsgbyuserid($beuserid);

        if ($info["role"] != 1) {
            $res["morebtn"] = false;
        } else {
            $res["morebtn"] = true;
            $info1 = $GroupBearingModel->getusermsg($groupid, $beuserid);
            if ($info1["role"] == 2) {
                $res["adminstatus"] = 1;
            } else {
                $res["adminstatus"] = 0;
            }
        }

        if ($ischeck) {
            $res["isgoodfriend"] = true;
            if ($ischeck["userid"] == $userid) {
                $res["lookplanetstatus"] = $ischeck["u_bu_status"];
            } else {
                $res["lookplanetstatus"] = $ischeck["bu_u_status"];
            }

        } else {
            $res["isgoodfriend"] = false;
            $res["lookplanetstatus"] = 0;
        }
        get_api_result(200, "获取成功", $res);

    }


    /**
     *获取我的群信息列表
     */
    public function getgrouplistbyuserid()
    {
        $userid = I('userid');
        $GroupModel = D('Group');
        $res = $GroupModel->getmyjoinGroup($userid);

        get_api_result(200, "获取成功", $res);

    }

    /**
     *解散群
     */
    public function delgroup()
    {
        $userid = I('userid');
        $groupid = I('groupid');
        $GroupModel = D('Group');
        $ischeck = $GroupModel->checkgrouprole($groupid, $userid);
        if (!$ischeck) {
            get_api_result(300, "非法操作");
        }
        $res = $GroupModel->delgroup($groupid, $userid);
        if ($res) {
            get_api_result(200, "操作成功");
        } else {
            get_api_result(300, "请刷新后在操作");
        }
    }

    /**
     *退出群
     */
    public function leavegroup()
    {
        $userid = I('userid');
        $groupid = I('groupid');
        $GroupModel = D('Group');
        $ischeck = $GroupModel->checkgrouprole($groupid, $userid);
        if ($ischeck) {
            get_api_result(300, "请刷新后在操作");
        }
        $GroupBearingModel = D("GroupBearing");
        $res1 = $GroupBearingModel->leavegroup($groupid, $userid);
        if ($res1) {
            get_api_result(200, "操作成功");
        } else {
            get_api_result(300, "请刷新后在操作");
        }
    }

    /**
     *批量踢出群
     */
    public function shotoffgroup()
    {
        $userid = I('userid');
        $groupid = I('groupid');
        $useridstr = I('useridstr');

        $GroupBearingModel = D("GroupBearing");

        $userinfo = $GroupBearingModel->ischeckjoingroup($groupid, $userid);  //踢人信息
        $useridarr = explode(",", $useridstr);
        for ($i = 0; $i < count($useridarr); $i++) {
            $userinfo1[$i] = $GroupBearingModel->ischeckjoingroup($groupid, $useridarr[$i]);  //被踢者信息
            if ($userinfo["role"] >= $userinfo1[$i]["role"]) {
                get_api_result(300, "非法操作");
            }
            $res = $GroupBearingModel->shotoffgroup($groupid, $useridarr[$i]);
        }

        if ($res) {
            get_api_result(200, "操作成功");
        } else {
            get_api_result(300, "请刷新后在操作");
        }
    }

    /**
     *提升为管理员/或撤销管理员
     */
    public function groupadmin()
    {
        $userid = I('userid');
        $groupid = I('groupid');
        $beuserid = I('beuserid');
        $status = I('status');

        $GroupModel = D('Group');
        $ischeck = $GroupModel->checkgrouprole($groupid, $userid);    //非群主
        if (!$ischeck) {
            get_api_result(300, "您无此权限");
        }
        $GroupBearingModel = D("GroupBearing");
        $count = $GroupBearingModel->getadminnum($groupid);
        if ($count >= 2) {
            get_api_result(300, "只能创建2个管理员");
        }
        $ischeckjoin = $GroupBearingModel->ischeckjoingroup($groupid, $beuserid); //判断此人再群中
        if (!$ischeckjoin) {
            get_api_result(300, "请刷新后在操作");
        }
        $where = "groupid=" . $groupid . " and userid=" . $beuserid;
        if ($status == 1) {
            $data["role"] = 2;
        } else {
            $data["role"] = 3;
        }
        $res = $GroupBearingModel->editpost($where, $data);
        if ($res) {
            get_api_result(200, "操作成功");
        } else {
            get_api_result(300, "请刷新后在操作");
        }
    }

    /**
     * 新 邀请加入
     */
    public function joingroup()
    {
        $useridstr = I('useridstr');
        if ($useridstr == "") {
            get_api_result(200, "操作成功");
        }
        $useridarr = explode(",", $useridstr);
        $groupid = I('groupid');
        $userid = I('userid');
        $GroupBearingModel = D('GroupBearing');
        $myinfo = $GroupBearingModel->getusermsg($groupid, $userid);
        if ($myinfo["role"] > 2) {
            get_api_result(300, "您无权邀请用户");
        }

//        $res = $GroupBearingModel->addallpost($groupid, $useridarr);
        $from=M('user')->where(['userid'=>$userid])->field('username,tel')->find();
        $group=M('group')->where(['groupid'=>$groupid])->find();
        //拉人入群需要被邀请者同意，需要发送系统消息
        foreach ($useridarr as $item){
            $SystemChatModel = D('SystemChat');    //系统消息
            $data2["system_chat_type"] = 3;//入群邀请
            $data2["to_userid"] = $item;
            $data2["content"] = $from['username'] . "(" . $from["tel"] . ")请求添加您入群（".$group['name']."）。";
            $data2["friend_id"] = $userid;
            $data2["dostatus"] = 0;
            $data2["group_id"] = $groupid;
            $res2 = $SystemChatModel->addpost($data2);

            $ChatWindowModel = D('ChatWindow');   //消息窗口
            $data1['to_userid'] = "[" . $item . "]";   //接收者ID
            $data1['type'] = 3;                           // 1好友 2群 3|系统
            $data1['group_id'] = $myinfo['groupid'];                           // 1好友 2群 3|系统
            $data1['organize_id'] = $res2;   //系统消息id
            $data1['content'] = $from["username"] . "(" . $from["tel"] . ")请求添加您入群（".$group['name']."）。";                    //内容
            $res1 = $ChatWindowModel->addmsgpost($type = 3, $data1);

            //缺少环信推送
            $str = $item;
            $JpushmessageModel = D("Jpushmessage");
            $JpushmessageModel->setxxmsgdate($str);
        }
        get_api_result(200, "操作成功");
    }

    /**
     * 被邀请者愿意加入群
     */
    public function joingroup_agree()
    {
        $system_chat_id = I('system_chat_id');
        $dostatus = I('dostatus');
        if(!$system_chat_id || !$dostatus){
            get_api_result(300, "数据不完整");
        }
        $res=M('system_chat')->where(['system_chat_id'=>$system_chat_id])->find();
        if($res['dostatus']>0){
            get_api_result(300, "请勿重复操作");
        }
        if($dostatus==1){
            M()->startTrans();
            $result=M('system_chat')->where(['system_chat_id'=>$system_chat_id])->save(['dostatus'=>$dostatus]);
            $GroupBearingModel = D('GroupBearing');
            $result2 = $GroupBearingModel->addallpost($res['group_id'], [$res['to_userid']]);
            if($result && $result2){
                M()->commit();
                get_api_result(200, "操作成功");
            }
        }else{
            $result=M('system_chat')->where(['system_chat_id'=>$system_chat_id])->save(['dostatus'=>$dostatus]);
            if($result){
                get_api_result(200, "操作成功");
            }
        }
        get_api_result(300, "操作失败");
    }

    /**
     *老 邀请加入
     */
    public function joingroup2()
    {
        $useridstr = I('useridstr');
        if ($useridstr == "") {
            get_api_result(200, "操作成功");
        }
        $useridarr = explode(",", $useridstr);
        $groupid = I('groupid');
        $userid = I('userid');
        $GroupBearingModel = D('GroupBearing');
        $myinfo = $GroupBearingModel->getusermsg($groupid, $userid);
        if ($myinfo["role"] > 2) {
            get_api_result(300, "您无权邀请用户");
        }
        $res = $GroupBearingModel->addallpost($groupid, $useridarr);
        get_api_result(200, "操作成功");

    }

    /**
     *获取可邀请加入的好友信息列表
     */
    public function joinuserlist()
    {
        $groupid = I('groupid');
        $userid = I('userid');
        $GroupBearingModel = D('GroupBearing');
        $myinfo = $GroupBearingModel->getusermsg($groupid, $userid);
        if ($myinfo["role"] > 2) {
            get_api_result(300, "您无权邀请用户");
        }

        $GroupBearingModel = D("GroupBearing");
        $res = $GroupBearingModel->getUserFriendList($groupid, $userid);

        get_api_result(200, "获取成功", $res);

    }


}