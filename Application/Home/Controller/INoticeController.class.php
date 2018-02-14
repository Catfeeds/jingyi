<?php
/**
 * Created by PhpStorm.
 * User: 丿灬小疯
 * Date: 2017/6/1
 * Time: 11:01
 */

namespace Home\Controller;

use Common\Model\UserFriend;

class INoticeController extends CommonController
{
    /**
     * 获取消息窗体信息列表
     */
    public function getwindowslist()
    {
        $userid = I('userid');
        $keywords = I('keywords');
        $where = "to_userid like '%[" . $userid . "]%' and is_dels not like '%[" . $userid . "]%'";
        if (!empty($keywords)) {
            $where .= " and content like '%" . $keywords . "%'";
        }
        $where.=' and type <5';
        $order = "addtime desc";
        $ChatWindowModel = D('ChatWindow');

        $res = $ChatWindowModel->getwindowslist($where, $order, $userid);
        get_api_result(200, "获取成功", $res);

    }

    /**
     * 获取好友小助手内容列表
     */
    public function getcontentlist()
    {
        $userid = I('userid');
        $page = I("page") ? I("page") : 1;
        $limit = I("limit") ? I("limit") : 10;
        $limit1 = ($page - 1) * $limit . "," . $limit;

        $SystemChatModel = D('SystemChat');
        $res = $SystemChatModel->getsystemmsglist($userid, $limit1);

        if (count($res) > 0 && $page == 1) {
            $info = $SystemChatModel->getsystemmsglist($userid, "");
            for ($i = 0; $i < count($info); $i++) {
                $system_chat_id[$i] = $info[$i]['system_chat_id'];
                $SystemChatModel->isreadpost($system_chat_id[$i], $userid);

            }


        }


        get_api_result(200, "获取成功", $res);

    }


    /**
     * 删除消息
     */
    public function delwindows()
    {
        $userid = I('userid');
        $windowid = I('windowid');
        $ChatWindowModel = D('ChatWindow');
        $res = $ChatWindowModel->delpost($windowid, $userid);
        if ($res) {
            get_api_result(200, "删除成功", $res);
        } else {
            get_api_result(300, "请刷新后在操作");
        }


    }

    /**
     * 删除好友小助手消息
     */
    public function delsystemchat()
    {
        $userid = I('userid');
        $system_chat_id = I('system_chat_id');
        $SystemChatModel = D('SystemChat');
        $res = $SystemChatModel->delpost($system_chat_id, $userid);
        if ($res) {
            get_api_result(200, "删除成功", $res);
        } else {
            get_api_result(300, "请刷新后在操作");
        }


    }

    /**
     * 已读好友小助手
     */
    public function readsystemchat()
    {
        $userid = I('userid');
        $system_chat_id = I('system_chat_id');
        $SystemChatModel = D('SystemChat');
        $res = $SystemChatModel->isreadpost($system_chat_id, $userid);

        if ($res) {
            get_api_result(200, "操作成功", $res);
        } else {
            get_api_result(300, "请刷新后在操作");
        }


    }


    /**
     * 获取用户未读消息数量
     */
    public function getnoreadmsgnum()
    {
        $userid = I('userid');

        $ChatWindowModel = D('ChatWindow');

        $res = $ChatWindowModel->getnoreadmsgnum($userid);

        get_api_result(200, "获取成功", $res);

    }

    /**
     * 获取顶部菜单是否有红点
     */
    public function getmenuredprint()
    {
        $userid = I('userid');
        if (empty($userid)) {
            $res["status"] = false;
        } else {

            $ChatWindowModel = D('ChatWindow');

            $res1 = $ChatWindowModel->getnoreadmsgnum($userid);  //消息模块

            $AirshipmessageModel = D('Airshipmessage');
            $res2 = $AirshipmessageModel->checkisreadmessage($userid);   //空间站

            $UserPlanetPostsMessageModel = D("UserPlanetPostsMessage");
            $res3 = $UserPlanetPostsMessageModel->checkUserPlanet($userid);  //星球

            if ($res1 || $res2 || $res3) {
                $res["status"] = true;
            } else {
                $res["status"] = false;
            }
        }
        get_api_result(200, "获取成功", $res);

    }

    /**
     * 获取系统消息内容列表
     */
    public function getsystemcontentlist()
    {
        $userid = I('userid');
        $page = I("page") ? I("page") : 1;
        $limit = I("limit") ? I("limit") : 10;
        $limit1 = ($page - 1) * $limit . "," . $limit;

        $SystemMessageModel = D('SystemMessage');
        $res = $SystemMessageModel->getsystemmsglist($userid, $limit1);

        if (count($res) > 0 && $page == 1) {


            $info = $SystemMessageModel->getsystemmsglist($userid, "");
            for ($i = 0; $i < count($info); $i++) {
                $system_chat_id[$i] = $info[$i]['system_chat_id'];
                $SystemMessageModel->isreadpost($system_chat_id[$i], $userid);

            }


        }

        get_api_result(200, "获取成功", $res);

    }

    /**
     * 删除系统消息
     */
    public function delsystemmessage()
    {
        $userid = I('userid');
        $system_chat_id = I('system_chat_id');
        $SystemMessageModel = D('SystemMessage');
        $res = $SystemMessageModel->delpost($system_chat_id, $userid);
        if ($res) {
            get_api_result(200, "删除成功", $res);
        } else {
            get_api_result(300, "请刷新后在操作");
        }


    }


    /**
     * 已读系统消息
     */
    public function readsystemmessage()
    {
        $userid = I('userid');
        $system_chat_id = I('system_chat_id');
        $SystemMessageModel = D('SystemMessage');
        $res = $SystemMessageModel->isreadpost($system_chat_id, $userid);

        if ($res) {
            get_api_result(200, "操作成功", $res);
        } else {
            get_api_result(300, "请刷新后在操作");
        }


    }

}