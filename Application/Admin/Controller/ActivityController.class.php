<?php

namespace Admin\Controller;

use Think\Controller;

class ActivityController extends CommonController
{

    public function _initialize()
    {
        parent::_initialize();
        if (session('admin_key_id') != 1) {
            if (!in_array('9', session('admin_key_auth'))) {
                session('[destroy]');
                $this->redirect('Login/index', array(), 1, '无权限...');
            }
        }

    }

    public function index()
    {
        $where = " type=" . $_GET["type"];
        $field='';
        $join='';
        $where .= $_GET["status"] ? " and status=" . $_GET["status"] : "";
        $Model = M("activity");
        $order = "addtime desc";
        $list = $Model->field($field)->where($where)->order($order)->join($join)->select();
        $this->assign('list', $list);
        $this->assign('type', $_GET["type"]);
        $this->display();
    }

    public function add()
    {
        $this->display();
    }

    public function addpost()
    {
        $Model = M('activity');
        $data = $Model->create();
        $data["begintime"] = strtotime($data["begintime"]);
        $data["endtime"] = strtotime($data["endtime"]);
        if ($data["begintime"] >= $data["endtime"]) {
            echo "<div id='kk' style='display:none'>2</div>";
            $this->display("Activity/add");
            die;
        }
        $data["status"] = 1;
        $data["type"] = 1;
        $data["adminid"] = session("admin_key_id");
        $data["addtime"] = time();
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 1024 * 1024 * 20;
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');
        $upload->rootPath = './'; // 设置附件上传目录
        $upload->savePath = './Public/upload/Activity/'; // 设置附件上传目录
        $upload->saveName = array('uniqid', '');
        $upload->ischeckfile = true;  //必须上传文件
        $info = $upload->upload();
        if (!$info) { // 上传错误提示错误信息
            $this->error($upload->getError());
        } else { //上传成功获取上传文件信息
            foreach ($info as $file) {
                $data[$file['key']] = $file['savepath'] . $file['savename'];
            }
        } // 保存表单数据包括附件数据<br />

        $res = $Model->add($data); //添加数据
        if ($res) {
            $Model->commit();
            $opdata["content"] = "创建了官方活动。活动id为:" . $res . "。";
            admin_log($opdata["content"]);
            echo "<div id='kk' style='display:none'>1</div>";
            $this->display("Activity/add");
        } else {
            $Model->rollback();
            echo "<div id='kk' style='display:none'>2</div>";
            $this->display("Activity/add");
        }


    }

    public function show()
    {

        $Model = M("hobby");
        $hobbylist = $Model->select();
        $this->assign('hobbylist', $hobbylist);
        $profession_signModel = M("profession_sign");
        $profession_signlist = $profession_signModel->select();
        $this->assign('profession_signlist', $profession_signlist);

        $id = I('get.id');
        $UserModel = M('User');
        $where = "user.userid=" . $id;
        $field = "user.*,profession_sign.name";
        $join = "left JOIN  profession_sign on user.professionsign_id=profession_sign.pro_id";
        $info = $UserModel->where($where)->join($join)->find();
        if (!empty($info["hobbyid"])) {
            $hobbyModel = M("hobby");
            $hobbyidstr = substr(str_replace('|', ",", str_replace('#', "", $info["hobbyid"])), 0, -1);
            $hobby = $hobbyModel->where("hobbyid in (" . $hobbyidstr . ")")->getField('hobbyname', true);
            $info["hobbyname"] = implode('/', $hobby);  //爱好名称
        }
        if ($info["pid"] > 0) {
            $puserid = $UserModel->where("userid=" . $info["pid"])->find();
            $info["ptel"] = $puserid["tel"];
        }
        $this->assign("info", $info);
        $this->display();
    }


    public function del()
    {
        $id = $_POST["id"];
        $Model = M("activity");
        $where = "activity_id=" . $id;
        $res = $Model->where($where)->find();
        $res['type']==1?$type='官方活动贴':$type='群活动贴';
        unlink($res["activity_img"]);
        $res1 = $Model->where($where)->delete();
        if ($res1) {
            admin_log('删除'.$type.'，编号为：'.$id);
            $this->ajaxReturn(1);
        } else {
            $this->ajaxReturn(0);
        }

    }


}