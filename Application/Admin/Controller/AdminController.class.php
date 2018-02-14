<?php

namespace Admin\Controller;

use Think\Controller;

class AdminController extends CommonController
{

    public function _initialize(){
        parent::_initialize();
        if(session('admin_key_id') !=1){
            if(!in_array('18',session('admin_key_auth'))){
                session('[destroy]');
                $this->redirect('Login/index',array(), 1, '无权限...');
            }
        }
    }

    /**
     * 管理员列表
     */
    public function index()
    {
        $count = M('admin')->count();
        $Page = new \Think\Page($count, 10);
        $Page->setConfig('prev', '上一页');
        $Page->setConfig('next', '下一页');
        $show = $Page->show();
        $info = M('admin')->order('id asc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('page', $show);// 赋值分页输出
        $this->assign("list", $info);
        $this->assign("count", $count);
        $this->display();

    }

    /**
     * 管理员停用
     */
    public function admin_stop()
    {
        $id = I('post.id');
        $result = M('admin')->where('id=' . $id)->save(array('status' => 2));

        $opres = M('admin')->where('id=' . $id)->find();
        $opdata["content"] = "冻结了管理账号，账号为：" . $opres["admin"] . "。";
        admin_log($opdata["content"]);

        $this->ajaxReturn($result);
    }

    /**
     * 管理员启用
     */
    public function admin_start()
    {
        $id = I('post.id');
        $result = M('admin')->where('id=' . $id)->save(array('status' => 1));

        $opres = M('admin')->where('id=' . $id)->find();
        $opdata["content"] = "恢复了管理账号，账号为：" . $opres["admin"] . "。";
        admin_log($opdata["content"]);

        $this->ajaxReturn($result);
    }

    /**
     * 删除管理员
     */
    public function admin_del()
    {
        $id = I('post.id');
        $opres = M('admin')->where('id=' . $id)->find();
        $opdata["content"] = "删除了管理账号，账号为：" . $opres["admin"] . "。";
        admin_log($opdata["content"]);
        $result = M('admin')->where('id=' . $id)->delete();
        $this->ajaxReturn($result);
    }

    /**
     * 添加管理员
     */
    public function add()
    {
        if ($_POST) {
            $Model = M("admin");
            $data = $Model->create();
            $data["auth"] = implode(",", $_POST["auth"]);
            $data["pid"] = session("admin_key_id");
            $data["password"] = md5($_POST["password"]);
            $data["created_time"] = time();
            $res = $Model->where("admin='" . $data["admin"] . "'")->find();
            if ($res) {
                echo "<div id='close' style='display:none;'>3</div>";
            } else {
                $result = $Model->add($data);
                if ($result) {
//						$option=D("Option");
                    $opres = M('admin')->where('id=' . $result)->find();
                    $opdata["content"] = "创建了管理账号，账号为：" . $opres["admin"] . "。";
//						$option->add($opdata);
                    admin_log($opdata["content"]);

                    echo "<div id='close' style='display:none;'>1</div>";
                } else {
                    echo "<div id='close' style='display:none;'>2</div>";
                }
            }

        }
        $nodeModel = M("role");
        $list = $nodeModel->select();
        $this->assign('list', $list);
        $this->display();
    }


    /**
     * 修改管理员
     */
    public function edit()
    {
        $id = I('get.id');
        $res = M('admin')->where('id=' . $id)->find();
        $res["authlist"] = explode(",", $res["auth"]);
        $this->assign('res', $res);
        $nodeModel = M("role");
        $list = $nodeModel->select();
        $this->assign('list', $list);
        $this->display();
    }

    /**
     * 修改管理员权限
     */
    public function editpost()
    {
        $id = $_POST["id"];
        $Model = M("admin");
        $data["auth"] = implode(",", $_POST["auth"]);
        $res = $Model->where("id=" . $id)->save($data);

        if ($res) {
            $opres = M('admin')->where('id=' . $id)->find();
            $opdata["content"] = "修改了管理账号的权限，账号为：" . $opres["admin"] . "。";
            admin_log($opdata["content"]);

            echo "<div id='close' style='display:none;'>1</div>";
            $this->display('Admin/edit');
        } else {
            echo "<div id='close' style='display:none;'>2</div>";
        }
    }

    /**
     * 操作信息
     */
    public function option()
    {
        $adminid = $_GET["id"];
        $count = M('option')->where(['option.adminid'=>$adminid])->count();
        $Page = new \Think\Page($count, 10);
        $Page->setConfig('prev', '上一页');
        $Page->setConfig('next', '下一页');
        $show = $Page->show();
        $info = M('option')
            ->join('admin on admin.id = `option`.adminid')
            ->where(['`option`.adminid'=>$adminid])->order('`option`.id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('page', $show);// 赋值分页输出
        $this->assign("res", $info);
        $this->display();
    }
}