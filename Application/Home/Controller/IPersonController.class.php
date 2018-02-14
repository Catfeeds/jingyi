<?php

namespace Home\Controller;

use Think\Controller;

//停靠站点
class IPersonController extends CommonController
{

    /*
     * 修改登录密码
     */
    public function edit_password()
    {
        $tel = I('tel');
        $data['password'] = md5(I('password'));
        $UserModel = D('User');
        $result = $UserModel->editpostbytel($tel, $data);
        get_api_result(200, "修改成功", $result);
    }

    /*
     * 修改用户信息
     */
    public function editUserMsg()
    {
        $UserModel = D('User');
        $userid = I('userid');
        if (I('autograph')) {
            $data['autograph'] = shielding(I('autograph'));
        }
        if (I('username')) {
            $data['username'] = shielding(I('username'));
        }
        if (I("starsignid")) {
            $starsignidstr = I("starsignid");
            $arr = explode(",", $starsignidstr);
            for ($i = 0; $i < count($arr); $i++) {
                $getstr .= "#" . $arr[$i] . "|";
            }
            $data["starsignid"] = $getstr;
        }
        if (I('marriage')) {
            $data['marriage'] = I('marriage');
        }
        $UserModel->editpostbyuserid($userid, $data);
        $result = $UserModel->getusermsgbyuserid($userid);
        get_api_result(200, "修改成功", $result);
    }

    /*
     * 修改头像
     */
    public function editUserHeadimg()
    {
        $UserModel = D('User');
        $userid = I('userid');
        $headimg = I('headimg');
        $savefilepath = "user";
        if ($headimg)
            $headimg_url = uplodeImg($headimg, $savefilepath);
        if ($headimg_url)
            $data['headimg'] = $headimg_url;

        $UserModel->editpostbyuserid($userid, $data);
        $result = $UserModel->getusermsgbyuserid($userid);
        get_api_result(200, "修改头像成功", $result);
    }


}