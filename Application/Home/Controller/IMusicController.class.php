<?php

namespace Home\Controller;

use Think\Controller;

class IMusicController extends CommonController
{


    /**
     *  获取背景音乐列表
     */
    public function getlist()
    {

        $MusicModel = D("Music");
        $res = $MusicModel->getList($userid, $type, $limit1);
        $msg = "获取成功";
        get_api_result(200, $msg, $res);


    }


}