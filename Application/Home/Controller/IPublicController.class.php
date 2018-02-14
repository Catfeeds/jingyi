<?php

namespace Home\Controller;

use Think\Controller;

class IPublicController extends CommonController
{

    /**
     *  上传图片
     */
    public function uploadimg()
    {
        $img = I('img');
        $type = I('type') ? I('type') : 1;
        if ($type == 1) {
            $savefilepath = "userPlanet";
        } else if ($type == 2) {
            $savefilepath = "product";
        }
        $res["img"] = uplodeImg($img, $savefilepath);
        $res["imgpath"] = C("imgpathurl") . $res["img"];
        if (!$res["img"]) {
            $msg["code"] = 3014;
            get_api_result(300, $msg);  //图片上传失败
        } else {
            $msg["code"] = 2007;
            get_api_result(200, $msg, $res);  //密码修改成功，请使用新密码登陆	
        }
    }

    /**
     *  多图上传图片
     */
    public function uploadimgarr()
    {
        $img = I('img');
        $savefilepath = "tousu";
        for ($i = 0; $i < count($img); $i++) {
            $res["img"][$i] = uplodeImg($img[$i], $savefilepath);
            $res["imgpath"][$i] = C("imgpathurl") . $res["img"][$i];
        }

        if (!$res["img"]) {
            $msg["code"] = 3014;
            get_api_result(300, $msg);  //图片上传失败
        } else {
            $msg["code"] = 2007;
            get_api_result(200, $msg, $res);  //密码修改成功，请使用新密码登陆	
        }
    }

    /**
     *  删除图片
     */
    public function delimg()
    {
        $img = I('img');
        $delkey = I("delkey");
        if ($delkey == "Dappdelimg") {
            unlink($img);
            $msg["code"] = 2008;
            get_api_result(200, $msg);  //密码修改成功，请使用新密码登陆	
        } else {
            $msg["code"] = 3015;
            get_api_result(300, $msg);  //图片上传失败
        }
    }

    //上传图片

    public function uploadimgbyisream()
    {
        $type = I('type') ? I('type') : 1;
        if ($type == 1) {
            $savefilepath = "header";
        } else if ($type == 2) {
            $savefilepath = "product";
        }
        $upload = new \Think\Upload(); // 实例化上传类
        $upload->maxSize = 1024 * 1024 * 200;
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');
        $upload->rootPath = './'; // 设置附件上传目录
        $upload->savePath = './Public/upload/' . $savefilepath . "/"; // 设置附件上传目录
        $upload->saveName = array('uniqid', '');
        $upload->ischeckfile = true;  //必须上传文件
        $info = $upload->upload();
        if (!$info) { // 上传错误提示错误信息
            // $msg = "上传失败";
            $msg = $upload->getError();
            get_api_result(300, $msg, $upload->getError());  //上传失败
        } else { //上传成功获取上传文件信息
            $i = 0;
            foreach ($info as $file) {
                //$data[$file['key']]=$file['savepath'].$file['savename'];
                $res[$i]["img"] = str_replace('./Public', 'Public', $file['savepath']) . $file['savename'];
                $res[$i]["imgpath"] = C("imgpathurl") . $res[$i]["img"];
                $i++;
            }
            $msg["code"] = 2007;
            get_api_result(200, $msg, $res);
        } // 保存表单数据包括附件数据<br />
    }

    //上传音乐

    public function uploadmusicbyisream()
    {
        $savefilepath = "music";
        $upload = new \Think\Upload(); // 实例化上传类
        $upload->maxSize = 1024 * 1024 * 50;
        $upload->exts = array('mp4', 'mp3', 'wav');
        $upload->rootPath = './'; // 设置附件上传目录
        $upload->savePath = './Public/upload/' . $savefilepath . "/"; // 设置附件上传目录
        $upload->saveName = array('uniqid', '');
        $upload->ischeckfile = true;  //必须上传文件
        $info = $upload->upload();
        if (!$info) { // 上传错误提示错误信息
            $msg = "上传失败";
            get_api_result(300, $msg, $upload->getError());  //上传失败
        } else { //上传成功获取上传文件信息
            $i = 0;
            foreach ($info as $file) {
                //$data[$file['key']]=$file['savepath'].$file['savename'];
                $res[$i]["music"] = str_replace('./Public', 'Public', $file['savepath']) . $file['savename'];
                $res[$i]["musicpath"] = C("imgpathurl") . $res[$i]["music"];
                $i++;
            }
            $msg = "上传成功";
            get_api_result(200, $msg, $res);
        } // 保存表单数据包括附件数据<br />
    }

    //上传视频、音频文件

    public function uploadvideobyisream()
    {
        $type = I('type') ? I('type') : 1;   //1|视频 2|音频 3|图片 4|其他

        if ($type == 1) {
            $savefilepath = "shipin";
        } else if ($type == 2) {
            $savefilepath = "video";
        } else if ($type == 3) {
            $savefilepath = "pic";
        } else {
            $savefilepath = "other";
        }
        $upload = new \Think\Upload(); // 实例化上传类
        $upload->maxSize = 1024 * 1024 * 1024 * 500;
        //$upload->exts = array('mp3', 'wma', 'flac', 'ape','wmv','mp4',);

        $upload->rootPath = './'; // 设置附件上传目录
        $upload->savePath = './Public/upload/' . $savefilepath . "/"; // 设置附件上传目录
        $upload->saveName = 'com_create_guid';
        // $upload->saveName = array('uniqid', '');
        $upload->replace = false;
        $upload->ischeckfile = true;  //必须上传文件
        $info = $upload->upload();
        if (!$info) { // 上传错误提示错误信息
            $msg = $upload->getError();
            get_api_result(300, $msg, $_FILES);  //上传失败
        } else { //上传成功获取上传文件信息

            $i = 0;
            foreach ($info as $file) {
                //$data[$file['key']]=$file['savepath'].$file['savename'];
                $res[$i]["filepath"] = str_replace('./Public', 'Public', $file['savepath']) . $file['savename'];
                $res[$i]["allfilepath"] = C("imgpathurl") . $res[$i]["filepath"];
                $i++;
            }
            $msg = "上传成功";
            get_api_result(200, $msg, $res);
        } // 保存表单数据包括附件数据<br />
    }

    /**
     *  获取引导图列表
     */
    public function getydlist()
    {
        $Model = M("Yindao");
        $limit = 4;
        $order = "tui asc";
        $res = $Model->limit($limit)->order($order)->select();
        if (!$res) {
            $res = array();
        } else {
            for ($i = 0; $i < count($res); $i++) {
                $res[$i]["images"] = imgpath($res[$i]["images"]);
            }
        }
        $msg = "获取成功";
        get_api_result(200, $msg, $res);  //密码修改成功，请使用新密码登陆
    }

    /**
     *  获取启动图列表
     */
    public function getqdlist()
    {
        $Model = M("Qidong");
        $info = $Model->find();
        if (!$info) {
            $res = array();
        } else {
            $res = $info;
            $res["images"] = imgpath($res["images"]);
        }
        $msg = "获取成功";
        get_api_result(200, $msg, $res);  //密码修改成功，请使用新密码登陆

    }

    /**
     *  获取国家区号列表
     */
    public function getcountrynumlist()
    {
        $CountrymobileprefixModel = D("Countrymobileprefix");
        $res = $CountrymobileprefixModel->getlist();
        $msg = "获取成功";
        get_api_result(200, $msg, $res);

    }


    /**
     *  更新用户在线时间
     */
    public function setuserlastdotime()
    {
        $userid = I('userid');
        $UseronlineModel = D("Useronline");
        $res = $UseronlineModel->setstatus($userid);
        $msg = "更新成功";
        get_api_result(200, $msg);

    }


}
