<?php

namespace Home\Controller;

use Think\Controller;

class IFriendStarSignController extends CommonController
{


    /**
     *  打标签
     * userid  打标签人id
     * beuserid 被打标签人id
     * postsid   被打标签人星球id
     * starsignid    标签id 已,隔开
     */
    public function addFriendStarSign()
    {
        $data["userid"] = I("userid");
        $data["beuserid"] = I("beuserid");
        $data["beuser_posts_id"] = I("postsid");
        $starsignid = I("starsignid");
        $data["addtime"] = time();
        if (empty($starsignid)) {
            $msg = "请先选择标签！";
            get_api_result(300, $msg);
        }
        if (empty($data["beuserid"])) {
            $msg = "非法操作！";
            get_api_result(300, $msg);
        }
        if (empty($data["userid"])) {
            $msg = "非法操作！";
            get_api_result(300, $msg);
        }
        if (empty($data["beuser_posts_id"])) {
            $msg = "非法操作！";
            get_api_result(300, $msg);
        }


        $starsignidarr = explode(",", $starsignid);

        $FriendStarSignModel = D("FriendStarSign");
        for ($i = 0; $i < count($starsignidarr); $i++) {
            $data1[$i]["userid"] = $data["userid"];
            $data1[$i]["beuserid"] = $data["beuserid"];
            $data1[$i]["beuser_posts_id"] = $data["beuser_posts_id"];
            $data1[$i]["starsignid"] = $starsignidarr[$i];
            $data1[$i]["addtime"] = $data["addtime"];
        }
        $res = $FriendStarSignModel->addallpost($data1);

        $msg = "标记成功！";
        get_api_result(200, $msg, $res);

    }


}