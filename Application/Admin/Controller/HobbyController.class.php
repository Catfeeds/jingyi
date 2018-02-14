<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/7
 * Time: 11:15
 */

namespace Admin\Controller;

use Think\Controller;

class HobbyController extends CommonController
{
    public function _initialize()
    {
        parent::_initialize();
        if (session('admin_key_id') != 1) {
            if (!in_array('17', session('admin_key_auth'))) {
                session('[destroy]');
                $this->redirect('Login/index', array(), 1, '无权限...');
            }
        }
    }

    public function index()
    {
        $Model = M("hobby");
        $where = $_GET["search_key"] ? "hobbyname like '%" . $_GET["search_key"] . "%'" : "";
        $list = $Model->where($where)->select();
        $this->assign('list', $list);
        $this->assign('search_key', $_GET["search_key"]);
        $this->display();
    }

    public function add()
    {
        $this->display();
    }

    public function addpost()
    {
        $Model = M('hobby');
        $data = $Model->create();
        $data["admin_id"] = session('admin_key_id');
        $data["addtime"] = time();
        $res = $Model->add($data);
        if ($res) {
            $opdata["content"] = "添加了爱好设定信息。编号为:" . $res . "。";
            admin_log($opdata["content"]);
            echo "<div id='kk' style='display:none'>1</div>";
            $this->display("Hobby/add");
        } else {
            echo "<div id='kk' style='display:none'>2</div>";
            $this->display("Hobby/add");
        }
    }


    public function edit()
    {
        $Model = M('hobby');
        $id = $_GET["id"];
        $where = "hobbyid=" . $id;
        $info = $Model->where($where)->find();

        $this->assign('res', $info);
        $this->display();
    }

    public function editPost()
    {
        $Model = M('hobby');
        $id = $_POST["id"];
        $data = $Model->create();
        $data["admin_id"] = session('admin_key_id');
        $data["addtime"] = time();
        $where = "hobbyid=" . $id;
        $res = $Model->where($where)->save($data);

        if ($res) {
            $opdata["content"] = "修改了爱好设定信息。编号为:" . $_POST["id"] . "。";
            admin_log($opdata["content"]);
            echo "<div id='kk' style='display:none'>1</div>";
            $this->display("Hobby/edit");
        } else {
            echo "<div id='kk' style='display:none'>2</div>";
            $this->display("Hobby/edit");
        }
    }

    //删除
    public function del()
    {
        $Model = M('hobby');
        $id = $_POST["id"];
        $where = "hobbyid=" . $id;
        $res = $Model->where($where)->delete();
        if ($res) {
            $opdata["content"] = "删除了爱好设定信息。编号为:" . $_POST["id"] . "。";
            admin_log($opdata["content"]);
            $this->ajaxReturn(1);
        } else {
            $this->ajaxReturn(0);
        }
    }
}