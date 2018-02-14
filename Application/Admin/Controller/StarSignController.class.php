<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/7
 * Time: 11:15
 */

namespace Admin\Controller;

use Think\Controller;

//星球标签设定
class StarSignController extends CommonController
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
        $Model = M("star_sign");
        $where = $_GET["search_key"] ? "name like '%" . $_GET["search_key"] . "%'" : "";
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
        $Model = M("star_sign");
        $data = $Model->create();
        $data["admin_id"] = session('admin_key_id');
        $data["addtime"] = time();
        $res = $Model->add($data);
        if ($res) {
            $opdata["content"] = "添加了星球标签信息。编号为:" . $res . "。";
            admin_log($opdata["content"]);
            echo "<div id='kk' style='display:none'>1</div>";
            $this->display("StarSign/add");
        } else {
            echo "<div id='kk' style='display:none'>2</div>";
            $this->display("StarSign/add");
        }
    }


    public function edit()
    {
        $Model = M("star_sign");
        $id = $_GET["id"];
        $where = "star_id=" . $id;
        $info = $Model->where($where)->find();

        $this->assign('res', $info);
        $this->display();
    }

    public function editPost()
    {
        $Model = M("star_sign");
        $id = $_POST["id"];
        $data = $Model->create();
        $data["admin_id"] = session('admin_key_id');
        $data["addtime"] = time();
        $where = "star_id=" . $id;
        $res = $Model->where($where)->save($data);

        if ($res) {
            $opdata["content"] = "修改了星球标签信息。编号为:" . $_POST["id"] . "。";
            admin_log($opdata["content"]);
            echo "<div id='kk' style='display:none'>1</div>";
            $this->display("StarSign/edit");
        } else {
            echo "<div id='kk' style='display:none'>2</div>";
            $this->display("StarSign/edit");
        }
    }

    //删除
    public function del()
    {
        $Model = M("star_sign");
        $id = $_POST["id"];
        $where = "star_id=" . $id;
        $res = $Model->where($where)->delete();
        if ($res) {
            $opdata["content"] = "删除了星球标签信息。编号为:" . $_POST["id"] . "。";
            admin_log($opdata["content"]);
            $this->ajaxReturn(1);
        } else {
            $this->ajaxReturn(0);
        }
    }
}