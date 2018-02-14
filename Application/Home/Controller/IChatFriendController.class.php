<?php

namespace Home\Controller;

use Think\Controller;

//好友聊天
class IChatFriendController extends CommonController
{


    /**
     *  发送聊天信息
     */
    public function addchat()
    {
        $data["from_id"] = I("userid");
        $data["to_id"] = I("beuserid");
        $data["type"] = $type = I("type");  // 1文字  2 图片  3音频
        $data['stop_type']=(I('stop_type')==2?I('stop_type'):1);//1 好友聊天 2 飞船停靠聊天
        $data['airship_stops_id']=(I('airship_stops_id')?I('airship_stops_id'):0);
        if($data['airship_stops_id']){
            $air=M('airship_stops')->where(['airship_stops_id'=>$data['airship_stops_id']])->find();
            if(!$air){
                get_api_result(300, "未找到飞船停靠信息");
            }
        }
        if(($data['stop_type']==2) && !$data['airship_stops_id']){
            get_api_result(300, "未接收到飞船停靠id");
        }
        if ($type == 1) {
            $data["content"] = shielding(I("content"));
            if(!$data['content']){
                get_api_result(300, "请输入聊天信息");
            }
        } else if ($type == 2) {
            $data["content"] = "[图片]";
            $savefilepath = "chat";
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
            $savefilepath = "chat";
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

        $ChatModel = D("Chat");
        $res = $ChatModel->addpost($data);
        $JpushmessageModel = D("Jpushmessage");
        $JpushmessageModel->setxxmsgdate(I("beuserid"));

        $result = $ChatModel->getmychatbychatid($res);
        get_api_result(200, "获取成功", $result);
    }

    /**
     * 飞船停靠消息全部已读
     */
    public function all_read(){
        $airship_stops_id=I('airship_stops_id');
        $userid=I('userid');
        if(!$airship_stops_id || !$userid){
            get_api_result(300, "数据不完整");
        }
        M('chat')->where(['airship_stops_id'=>$airship_stops_id,'to_id'=>$userid])->save(['read_weight'=>1]);
        get_api_result(200, "全部消息已读");
    }

    /**
     *  获取用户消息信息
     */
    public function getuserchatlist()
    {
        $userid = I("userid");
        $friendid = I("friendid");
        $small_chatid = I("small_chatid") ? I("small_chatid") : "";
        $airship_stops_id=(I('airship_stops_id')?I('airship_stops_id'):0);
        //$page=I("page")?I("page"):1;
        //$limit=I("limit")?I("limit"):10;
        //$limit1=($page-1)*$limit.",".$limit;

        $ChatModel = D("Chat");
        $res = $ChatModel->getmychat($userid, $friendid, $small_chatid,$airship_stops_id);
        $res1 = $ChatModel->changRead($userid, $friendid);
        get_api_result(200, "获取成功", $res);
    }

    /**
     *  好友消息已读
     */
    public function changeread()
    {
        $userid = I("userid");
        $friendid = I("friendid");
        $ChatModel = D("Chat");
        $res = $ChatModel->changRead($userid, $friendid);
        get_api_result(200, "获取成功", $res);
    }


    /**
     *  删除消息
     */
    public function delchat()
    {
        $userid = I("userid");
        $chatid = I("chatid");

        $ChatModel = D("Chat");
        $res = $ChatModel->delchat($userid, $chatid);
        if ($res) {
            get_api_result(200, "操作成功", $res);
        } else {
            get_api_result(300, "操作失败");
        }

    }


    /**
     *  添加个人收藏表情
     */
    public function addchatimg()
    {
        $data["userid"] = I("userid");
        $savefilepath = "userchatimg";
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
                $data["img"] = str_replace('./Public', 'Public', $file['savepath']) . $file['savename'];
            }
        }
        $UserChatPicModel = D("UserChatPic");
        $res = $UserChatPicModel->addpost($data);
        if ($res) {
            get_api_result(200, "操作成功", $res);
        } else {
            get_api_result(300, "操作失败");
        }

    }


    /**
     *  获取个人收藏表情列表
     */
    public function getuserchatpiclist()
    {
        $userid = I("userid");
        $page = I("page") ? I("page") : 1;
        $limit = I("limit") ? I("limit") : 10;
        $limit1 = ($page - 1) * $limit . "," . $limit;

        $UserChatPicModel = D("UserChatPic");
        $res = $UserChatPicModel->getuserchatpiclist($userid, $limit1);

        get_api_result(200, "操作成功", $res);
    }

    /**
     *  删除个人收藏表情列表
     */
    public function deluserchatpic()
    {
        $userid = I("userid");
        $userchatpicid = I("userchatpicid");

        $UserChatPicModel = D("UserChatPic");
        $res = $UserChatPicModel->delallpost($userchatpicid, $userid);
        if ($res) {
            get_api_result(200, "操作成功", $res);
        } else {
            get_api_result(300, "操作失败");
        }
    }


}