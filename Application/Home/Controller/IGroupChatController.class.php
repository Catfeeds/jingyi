<?php

namespace Home\Controller;

use Think\Controller;

//群聊天信息
class IGroupChatController extends CommonController
{

    /**
     *  发送聊天信息
     */
    public function addchat()
    {

        $data["from_id"] = I("userid");
        $data["group_id"] = I("group_id");
        $data["type"] = $type = I("type");  // 1文字  2 图片  3音频

        if ($type == 1) {
            $data["content"] = shielding(I("content"));
        } else if ($type == 2) {
            $data["content"] = "[图片]";
            $savefilepath = "groupchat";
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
                    $data["url"] = str_replace('./Public', 'Public', $file['savepath']) . $file['savename'];
                }
            }
        } else if ($type == 3) {
            $data["content"] = "[语音]";
            $data["time"] = I("time");  //视频时常
            $savefilepath = "groupchat";
            $upload = new \Think\Upload(); // 实例化上传类
            $upload->maxSize = 1024 * 1024 * 20;
            //$upload->exts = array("");
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
                    $data["url"] = str_replace('./Public', 'Public', $file['savepath']) . $file['savename'];
                }
            }
        }

        $GroupChatModel = D("GroupChat");
        $res = $GroupChatModel->addpost($data);


        $GroupBearingModel = D("GroupBearing");
        $useridarray = $GroupBearingModel->getuseridlistbyGroupid($data["group_id"]);
        if (count($useridarray) > 1) {
            $str = "";
            $j = 0;
            for ($i = 0; $i < count($useridarray); $i++) {

                if ($useridarray[$i] == I("userid")) {
                    $j = $j - 1;
                    continue;
                }

                if ($j == 0) {
                    $str .= $useridarray[$i];
                } else {
                    $str .= "," . $useridarray[$i];
                }
                $j++;
            }
            $JpushmessageModel = D("Jpushmessage");
            $JpushmessageModel->setxxmsgdate($str);
        }


        $result = $GroupChatModel->getmychatbychatid($res);
        get_api_result(200, "获取成功", $result);
    }

    /**
     *  获取用户群消息信息
     */
    public function getuserchatlist()
    {
        $userid = I("userid");
        $data["group_id"] = I("group_id");
        $small_chatid = I("small_chatid") ? I("small_chatid") : "";
        //$page=I("page")?I("page"):1;
        //$limit=I("limit")?I("limit"):10;
        //$limit1=($page-1)*$limit.",".$limit;

        $GroupChatModel = D("GroupChat");
        $res = $GroupChatModel->getmychat($userid, $data["group_id"], $small_chatid);
        $res1 = $GroupChatModel->changRead($userid, $data["group_id"]);
        get_api_result(200, "获取成功", $res);
    }

    /**
     *  群聊天消息已读
     */
    public function changread()
    {
        $userid = I("userid");
        $groupid = I("group_id");
        $GroupChatModel = D("GroupChat");
        $res = $GroupChatModel->changRead($userid, $groupid);
        get_api_result(200, "获取成功", $res);
    }


    /**
     *  删除消息
     */
    public function delchat()
    {
        $userid = I("userid");
        $chatid = I("chatid");

        $GroupChatModel = D("GroupChat");
        $res = $GroupChatModel->delchat($userid, $chatid);
        if ($res) {
            get_api_result(200, "操作成功", $res);
        } else {
            get_api_result(300, "操作失败");
        }

    }


}