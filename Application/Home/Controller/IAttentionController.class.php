<?php

namespace Home\Controller;

use Think\Controller;

//关注信息
class IAttentionController extends CommonController
{

    /**
     *  获取用户的关注列表
     *
     */
    public function getAttentionByUserid()
    {
        $userid = I('userid');
        $UserPlanetFollowModel = D("UserPlanetFollow");
        $res = $UserPlanetFollowModel->getuserAttentionlist($userid);

        $msg = "获取成功";
        get_api_result(200, $msg, $res);

    }

    /**
     *  获取用户的粉丝列表
     *
     */
    public function getFansByUserid()
    {
        $userid = I('userid');
        $UserPlanetFollowModel = D("UserPlanetFollow");
        $res = $UserPlanetFollowModel->getuserFanslist($userid);

        $msg = "获取成功";
        get_api_result(200, $msg, $res);

    }


    /**
     *  修改关注屏蔽状态
     *userid 用户id
     *beuserid 粉丝用户id
     *status  状态  0|不屏蔽 1|屏蔽
     */
    public function editfollowstatus()
    {
        $userid = I('userid');
        $beuserid = I('beuserid') ? I('beuserid') : 0;
        $status = I('status');
        if ($beuserid <= 0) {
            $msg = "非法操作";
            get_api_result(300, $msg);
        }
        $where = "userid=" . $beuserid . " and planet_userid=" . $userid;
        $UserPlanetFollowModel = D("UserPlanetFollow");
        $data["status"] = $status;
        $res = $UserPlanetFollowModel->editpost($where, $data);
        if ($status != 0) {
            $msg = "屏蔽成功";
            $msg1 = "屏蔽失败";
        } else {
            $msg = "取消屏蔽成功";
            $msg1 = "取消屏蔽失败";
        }
        if ($res) {
            get_api_result(200, $msg);
        } else {
            get_api_result(200, $msg1);
        }


    }


}
