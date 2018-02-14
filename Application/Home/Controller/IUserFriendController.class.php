<?php
/**
 * Created by PhpStorm.
 * User: 丿灬小疯
 * Date: 2017/6/1
 * Time: 11:01
 */

namespace Home\Controller;

use Common\Model\UserFriend;

class IUserFriendController extends CommonController
{
    /**
     * 显示好友列表
     */
    public function getUserFriendList()
    {
        $userid = I('userid');
        $keywords = I('keywords');
        $UserFriendModel = D('UserFriend');
        $res = $UserFriendModel->getUserFriendList($userid, $keywords);

        get_api_result(200, "获取成功", $res);

    }

    /**
     * 查找用户信息
     */
    public function getSearchList()
    {
        $keywords = I('keywords');
        $page = I("page") ? I("page") : 1;
        $limit = I("limit") ? I("limit") : 10;
        $limit1 = ($page - 1) * $limit . "," . $limit;

        $UserModel = D('User');
        $res = $UserModel->searchUser($keywords, $limit1);

        get_api_result(200, "获取成功", $res);

    }


    /**
     * 添加好友并发送消息给被添加者
     *
     * 推送未做
     */
    public function addUserFriend()
    {
        $userid = I('userid');//用户id
        $beuserid = I('user_friend_id');//被添加用户id
        $be=M('user')->where(['userid'=>$beuserid])->field('status,black')->find();
        if($be['status']==2){
            get_api_result(300, "该用户已被冻结");
        }
        if($be['black']==1){
            get_api_result(300, "该用户已被系统拉黑");
        }
        $u_bu_status = I('u_bu_status');//被添加用户是否可以查看用户星球  0|不屏蔽 1|屏蔽
        $text = I('text');
        $UserFriendModel = D('UserFriend');//添加好友
        $res = $UserFriendModel->addfriend($userid, $beuserid, $u_bu_status);


        $UserModel = D("User");
        $fromusermsg = $UserModel->getuserbasemsgbyuserid($userid);


        $SystemChatModel = D('SystemChat');    //系统消息

        $data2["system_chat_type"] = 2;
        $data2["to_userid"] = $beuserid;
        $data2["content"] = $fromusermsg["username"] . "(" . $fromusermsg["tel"] . ")请求添加您为好友。附加内容：" . $text;
        $data2["friend_id"] = $userid;
        $data2["dostatus"] = 0;
        $res2 = $SystemChatModel->addpost($data2);

        $ChatWindowModel = D('ChatWindow');   //消息窗口
        $data1['to_userid'] = "[" . $beuserid . "]";   //接收者ID
        $data1['type'] = 3;                           // 1好友 2群 3|系统
        $data1['organize_id'] = $res2;   //系统消息id
        $data1['content'] = $fromusermsg["username"] . "(" . $fromusermsg["tel"] . ")请求添加您为好友。";                    //内容
        $res1 = $ChatWindowModel->addmsgpost($type = 3, $data1);


        //缺少环信推送
        $str = $beuserid;
        $JpushmessageModel = D("Jpushmessage");
        $JpushmessageModel->setxxmsgdate($str);

        if ($res) {
            get_api_result(200, "发送成功", $res);
        } else {
            get_api_result(401, "发送失败");
        }
    }

    /**
     * 是否愿意成为好友
     */
    public function addUserFriendAgree()
    {
        $user_id = I('userid');//用户id
        $user_friend_id = I('user_friend_id');//被添加用户id
        $agree = I('agree_status');//是否愿意添加好友  0|未处理 1|愿意 2|不愿意
        $UserFriendModel = D('UserFriend');
        $res = $UserFriendModel->addUserFriendAgree($user_id, $user_friend_id, $agree);

        if ($res) {
            $SystemChatModel = D("SystemChat");
            $where = "to_userid=" . $user_id . " and  friend_id= " . $user_friend_id;
            $data["dostatus"] = 1;
            $SystemChatModel->editpost($where, $data);

            get_api_result(200, '操作成功', $res);
        } else {
            get_api_result(401, "操作失败", $res);
        }
    }

    /**
     * 解除好友关系
     */
    public function delFriend()
    {
        $userid = I('userid');//用户id
        $beuserid = I('user_friend_id');//被添加用户id

        $UserFriendModel = D('UserFriend');
        $res = $UserFriendModel->delpost($userid, $beuserid);
        $res2 = $UserFriendModel->delpost($beuserid, $userid);
        $ChatWindowModel = D("ChatWindow");
        $res1 = $ChatWindowModel->delfriendwindowspost($userid, $beuserid);
        if ($res) {
            get_api_result(200, '操作成功', $res);
        } else {
            get_api_result(401, "操作失败", $res);
        }
    }


    /**
     * 获取好友信息
     */
    public function getFriendinfo()
    {
        $userid = I('userid');//用户id
        $beuserid = I('user_friend_id');//被添加用户id

        $UserFriendModel = D('UserFriend');
        $ischeck = $UserFriendModel->ischeckgoodfriend($userid, $beuserid);
        $UserModel = D('User');
        $res = $UserModel->getusermsgbyuserid($beuserid);
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


        get_api_result(200, '操作成功', $res);

    }


    /**
     * 修改好友屏蔽星球状态
     */
    public function editplanetstatus()
    {
        $userid = I('userid');//用户id
        $beuserid = I('user_friend_id');//被添加用户id
        $status = I('status');  //状态

        $UserFriendModel = D('UserFriend');
        $res = $UserFriendModel->editplanetpost($userid, $beuserid, $status);
        if ($res) {
            get_api_result(200, '操作成功', $res);
        } else {
            get_api_result(401, "操作失败", $res);
        }

    }
}