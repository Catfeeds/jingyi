<?php

namespace Home\Controller;

use Think\Controller;

class UserController extends CommonController
{

    /**
     * 创建星球动画背景页面
     */
    public function index()
    {
        $userid = I("userid");
        $UserModel = D("User");
        $res = $UserModel->getuserbasemsgbyuserid($userid);
        $this->assign("info", $res);
        $this->display();

    }


}
