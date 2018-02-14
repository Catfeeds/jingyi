<?php

namespace Home\Controller;

use Think\Controller;

class ILoginController extends CommonController
{

    /**
     *  获取短信验证码
     */
    public function getVerify()
    {
        $tel = I('mobile');
        $countrynum = I("countrynum") ? I("countrynum") : "+86";
        $TelverifyModel = D("Telverify");
        $res = $TelverifyModel->getcode($tel, $countrynum);
        if ($res) {   //获取成功
            $res1 = $TelverifyModel->getcodebytel($tel, $countrynum);
            $msg = "发送成功！";
            get_api_result(200, $msg, $res1);
        } else {  //获取失败
            $msg = "发送失败！";
            get_api_result(300, $msg);
        }
    }

    /**
     *  用户登录
     */
    public function login()
    {
        $tel = I("mobile");
        $countrynum = I("countrynum") ? I("countrynum") : "+86";
        $password = md5(I("password"));
        $device=I('post.device');
        $device_id=I('post.device_id');
        $type=I('post.type')==1?I('post.type'):0;
        $code=I('post.code')?I('post.code'):0;
        $userModel = D("User");
        $res = $userModel->getUserinfoByTel($tel, $countrynum);
        if (!$res) {   //账号不存在
            $msg = "该手机号未注册！";
            get_api_result(310, $msg);
        }
        if ($res["password"] != $password) {   //密码有误
            $msg = "密码有误！";
            get_api_result(310, $msg);
        }
        if ($res["status"] != 1) {   //账号被冻结
            $msg = "账号被冻结！";
            get_api_result(310, $msg);
        }
        if ($res["black"] == 1) {   //账号被冻结
            $msg = "账号被拉黑！";
            get_api_result(310, $msg);
        }
        if ($res["userplanetstatus"]) {
            if ($res["userplanetinfo"]["growth_value"] <= -100) {
                $msg = "星球已爆炸！";
                get_api_result(310, $msg);

            }

        }
        $userinfo = $userModel->getusermsgbyuserid($res["userid"]);

        $device_old=json_decode($userinfo['device'],1);

        //第一次登录时验证设备
        if($type==0){
            if(count($device_old)>=3){
                if(!in_array($device_id,array_keys($device_old))){
                    get_api_result(300,'非常用设备登录',['countrynum'=>$countrynum,'mobile'=>$tel]);
                }
            }else{
                if(!in_array($device_id,array_keys($device_old))){
                    $all='';
                    if($device_old){
                        $all=array_merge($device_old,["$device_id"=>$device]);
                    }else{
                        $all=["$device_id"=>$device];
                    }

                    $result=M('user')->where(['userid'=>$userinfo['userid']])->save(['device'=>json_encode($all)]);
                    if(!$result){
                        get_api_result(301, '登录失败');
                    }
                }
            }
        }
        //非常用设备验证验证码并登录
//        if($type==1){
//            $TelverifyModel = D("Telverify");
//            $ischeck = $TelverifyModel->checkcode($tel, $code, $countrynum);  //验证码
//            if ($ischeck == 2) {   //验证码错误
//                $msg = "验证码错误";
//                get_api_result(302, $msg);
//            }
//            if ($ischeck == 3) {   //验证码超时
//                $msg = "验证码超时";
//                get_api_result(302, $msg);
//            }
////            $msg = "验证码正确";
////            get_api_result(200, $msg);
//        }

        $msg = "登陆成功！";
        D('UserLevel')->addUserGrow($userinfo['userid'],1);//首次登陆
        get_api_result(200, $msg, $userinfo);  //登陆成功
    }


    /**
     *  判断推荐号码是否存在
     */
    public function checkpuser()
    {
        $ptel = I("ptel");
        $UserModel = D("User");
        if (empty($ptel)) {
            $msg = "您没有填写推荐用户，是否确认直接注册！";
            get_api_result(300, $msg);
        }
        $pusermsg = $UserModel->getUserinfoByTel($ptel);
        if (empty($pusermsg)) {
            $msg = "推荐号码错误，请核查后提交。";
            get_api_result(300, $msg);
        }

        get_api_result(200, $msg);  //登陆成功
    }

    /**
     *  用户注册
     */
    public function register()
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
                $data["pcon"] = $pusermsg["pcon"] . "a" . $pusermsg["userid"] . "b";
            }
        }

        $ischecktel = $UserModel->getUserinfoByTel($tel);
        if ($ischecktel) {
            $msg = "账号已存在！";
            get_api_result(300, $msg);
        }


        $TelverifyModel = D("Telverify");
        $ischeck = $TelverifyModel->checkcode($tel, $code);  //验证码

        if ($ischeck == 2) {   //验证码错误
            $msg = "验证码错误";
            get_api_result(300, $msg);
        }
        if ($ischeck == 3) {   //验证码超时
            $msg = "验证码超时";
            get_api_result(300, $msg);
        }


        $res = $UserModel->registerpost($data);
        if (!$res) {   //注册失败
            $msg = "注册失败";
            get_api_result(300, $msg);
        }
        $result = $UserModel->getusermsgbyuserid($res);
        $msg = "登录成功";
        get_api_result(200, $msg, $result);  //登陆成功
    }

    /**
     *  用户忘记密码
     */
    public function forgotpassword()
    {
        $countrynum = I("countrynum") ? I("countrynum") : "+86";
        $tel = I("mobile");
        $data["password"] = md5(I("newpassword"));

        $UserModel = D("User");
        $ischecktel = $UserModel->getUserinfoByTel($tel, $countrynum);
        if (!$ischecktel) {   //不存在账号
            $msg = "账号错误！";
            get_api_result(300, $msg);
        }

        $userModel = D("User");
        $res = $userModel->editpostbytel($tel, $data, $countrynum);
        if (!$res) {   //密码修改失败
            $msg = "密码修改失败";
            get_api_result(300, $msg);
        }
        $msg = "密码修改成功，请使用新密码登陆";
        $result = $userModel->getUserinfoByTel($tel);
        get_api_result(200, $msg, $result);  //密码修改成功，请使用新密码登陆
    }

    /**
     *  用户修改密码
     */
    public function editpassword()
    {
        $userid = I("userid");
        $data["password"] = md5(I("password"));
        $userModel = D("User");
        $res = $userModel->editpostbyuserid($userid, $data);
        if (!$res) {   //密码修改失败
            $msg = "密码修改失败";
            get_api_result(300, $msg);
        }
        $msg = "密码修改成功，请使用新密码登陆";
        get_api_result(200, $msg);  //密码修改成功，请使用新密码登陆
    }

    /**
     *  验证码验证
     */
    public function checkcode()
    {
        $tel = I("mobile");
        $countrynum = I("countrynum") ? I("countrynum") : "+86";
        $code = I("code");
        $TelverifyModel = D("Telverify");
        $ischeck = $TelverifyModel->checkcode($tel, $code, $countrynum);  //验证码

        if ($ischeck == 2) {   //验证码错误
            $msg = "验证码错误";
            get_api_result(300, $msg);
        }
        if ($ischeck == 3) {   //验证码超时
            $msg = "验证码超时";
            get_api_result(300, $msg);
        }
        $msg = "验证码正确";
        get_api_result(200, $msg);
    }

    /**
     *  获取手机区号
     */
    public function getcountrymobileprefix()
    {
        $tel = I("mobile");
        $code = I("code");
        $TelverifyModel = D("Telverify");
        $ischeck = $TelverifyModel->checkcode($tel, $code);  //验证码

        if ($ischeck == 2) {   //验证码错误
            $msg = "验证码错误";
            get_api_result(300, $msg);
        }
        if ($ischeck == 3) {   //验证码超时
            $msg = "验证码超时";
            get_api_result(300, $msg);
        }
        $msg = "验证码正确";
        get_api_result(200, $msg);
    }

}
