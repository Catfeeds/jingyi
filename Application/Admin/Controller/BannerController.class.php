<?php

namespace Admin\Controller;

use Think\Controller;

class BannerController extends CommonController
{

    public function _initialize()
    {
        parent::_initialize();
        if (session('admin_key_id') != 1) {
            if (!in_array('2', session('admin_key_auth'))) {
                session('[destroy]');
                $this->redirect('Login/index', array(), 1, '无权限...');
            }
        }
    }

    public function index()
    {
        $Model = M("banner");
        $order = "tui asc";
        $model_list = getOneFunctionModel();
        $this->assign('model_list',$model_list);
        //获取查询参数
        $model_list_id = I('get.model_list_id') ? I('get.model_list_id') : '';
        $this->assign('model_list_id',$model_list_id);
        $where = '1=1';
        if ($model_list_id != ''){
            $where .= " and group_model='".$model_list_id."'";
        }
        $list = $Model->where($where)->order($order)->select();
        $this->assign('list', $list);
        $this->display();
    }

    public function add()
    {
        $model_list = getOneFunctionModel();
        $this->assign('model_list',$model_list);
        $this->display();
    }

    public function addpost()
    {
        $Model = M('banner');
        $data = $Model->create();
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 1024 * 1024 * 20;
        $upload->exts = array('jpg', 'png', 'jpeg');
        $upload->rootPath = './'; // 设置附件上传目录
        $upload->savePath = '/Public/upload/banner/'; // 设置附件上传目录
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
        $res = $Model->add($data);
        if ($res) {
            admin_log('添加banner');
            echo "<div id='kk' style='display:none'>1</div>";
        } else {
            echo "<div id='kk' style='display:none'>2</div>";
        }
        $this->display("Banner/add");
    }

    public function edit()
    {
        $id = I('get.id');
        $Model = M("banner");
        $res = $Model->where(['id'=>$id])->find();
        $this->assign('res', $res);
        $model_list = getOneFunctionModel();
        $this->assign('model_list',$model_list);
        $this->display();
    }

    public function editpost()
    {
        $Model = M('banner');
        $data = $Model->create();
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 1024 * 1024 * 20;
        $upload->exts = array('jpg', 'png', 'jpeg');
        $upload->rootPath = './'; // 设置附件上传目录
        $upload->savePath = '/Public/upload/banner/'; // 设置附件上传目录
        $upload->saveName = array('uniqid', '');
        //$upload-> ischeckfile  =   true;  //必须上传文件
        $info = $upload->upload();
        if (!$info) { // 上传错误提示错误信息
            // $this->error($upload->getError());
        } else { //上传成功获取上传文件信息
            foreach ($info as $file) {
                $data[$file['key']] = $file['savepath'] . $file['savename'];
            }
            $res = $Model->where("id=" . $_POST["id"])->find();
            unlink($res["images"]);
        } // 保存表单数据包括附件数据<br />
        $re = $Model->where("id=" . $_POST["id"])->save($data);
        if ($re === 0) {
            $opdata["content"] = "修改了banner信息。编号为:" . $_POST["id"] . "。";
            admin_log($opdata["content"]);
            echo "<div id='kk' style='display:none'>1</div>";
        } else {
            echo "<div id='kk' style='display:none'>2</div>";
        }
        $this->display("Banner/edit");
    }


    public function del()
    {
        $id = $_POST["id"];
        $Model = M("banner");
        $where = "id=" . $id;
        $res = $Model->where($where)->find();
        unlink($res["images"]);
        $res1 = $Model->where($where)->delete();
        if ($res1) {
            $opdata["content"] = "删除了banner信息。编号为:" . $id . "。";
            admin_log($opdata["content"]);
            $this->ajaxReturn(1);
        } else {
            $this->ajaxReturn(0);
        }

    }
}