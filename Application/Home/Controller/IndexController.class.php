<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/8
 * Time: 11:21
 */

namespace Home\Controller;

use Think\Controller;

class IndexController extends Controller
{


    public function index()
    {

        $this->display();
    }

    public function download()
    {

        $this->display();
    }

    public function erweima()
    {

        $this->display();
    }

    public function register()
    {
        $tuiuser = $_GET["usertel"];
        $this->assign("tuiuser", $tuiuser);
        $this->display();
    }

    public function adduser()
    {

        $data["tel"] = $tel = I("mobile");
        $data["password"] = md5(I("password"));
        $code = I("code");
        $ptel = I("ptel") ? I("ptel") : "";
        $UserModel = D("User");
        if (!empty($ptel)) {
            $pusermsg = $UserModel->getUserinfoByTel($ptel);
            if (!empty($pusermsg)) {
                $data["pid"] = $pusermsg["userid"];
                $data["pcon"] = $pusermsg["pcon"] . "#" . $pusermsg["userid"] . "|";

            }
        }

        $ischecktel = $UserModel->getUserinfoByTel($tel);
        if ($ischecktel) {
            $redata["status"] = 300;
            $redata["msg"] = "账号已注册！";
            $this->ajaxReturn($redata);
        }


        $TelverifyModel = D("Telverify");
        $ischeck = $TelverifyModel->checkcode($tel, $code);  //验证码

        if ($ischeck == 2) {    //验证码错误
            $redata["status"] = 300;
            $redata["msg"] = "验证码错误！";
            $this->ajaxReturn($redata);
        }
        if ($ischeck == 3) {    //验证码超时
            $redata["status"] = 300;
            $redata["msg"] = "验证码超时！";
            $this->ajaxReturn($redata);
        }


        $res = $UserModel->registerpost($data);
        if (!$res) {    //注册失败
            $redata["status"] = 300;
            $redata["msg"] = "注册失败！";
            $this->ajaxReturn($redata);
        }
        $redata["status"] = 200;
        $redata["msg"] = "注册成功！";
        $this->ajaxReturn($redata);

        $this->display();
    }


}