<?php

namespace Admin\Controller;

use Think\Controller;

class GroupModelController extends CommonController
{

    public function _initialize()
    {
        parent::_initialize();
        if (session('admin_key_id') != 1) {
            if (!in_array('20', session('admin_key_auth'))) {
                session('[destroy]');
                $this->redirect('Login/index', array(), 1, '无权限...');
            }
        }
    }

    /**
     * 列表
     */
    public function index()
    {
        $list = getOneFunctionModel();
        $this->assign('list', $list);
        $this->display();
    }

    /**
     * 添加
     */
    public function add()
    {
        $model_list = M('group_model')->where(['id'=>['neq',3]])->select();
        $this->assign('model_list',$model_list);
        $this->display();
    }
    /**
     * 添加提交
     */
    public function addpost()
    {
        $Model = M('group_model_class');
        $data = $Model->create();
        if ($data['group_model_id'] != 1){
            $data['type'] = 1;
        }
        $data['update_time'] = time();
        $res = $Model->add($data);
        if ($res) {
            admin_log('添加二级功能模块');
            echo "<div id='kk' style='display:none'>1</div>";
        } else {
            echo "<div id='kk' style='display:none'>2</div>";
        }
        $this->display("add");
    }

    /**
     * 二级版块列表
     */
    public function class_index()
    {
        $id = I('get.id');
        $model_list = M('group_model_class')->where(['pid'=>$id])->select();
        $this->assign('model_list', $model_list);
        $Model = M('group_model_class');
        $list = $Model->alias('a')
            ->field('a.*,b.name as group_name')
            ->join('left join group_model as b on b.id = a.group_model_id')
            ->where(['group_model_id'=>$id,'pid'=>0])
            ->order('a.group_model_id,a.order')
            ->select();
        $this->assign('list',$list);
        $this->assign('id',$id);
        $this->display();
    }
    /**
     * 三级版块列表
     */
    public function class_three()
    {
        $id = I('get.id');
        $p_name=M('group_model_class')->where(['id'=>$id])->getField('name');
        $Model = M('group_model_class');
        $list = $Model->alias('a')
            ->field('a.*,b.name as group_name')
            ->join('left join group_model as b on b.id = a.group_model_id')
            ->where(['pid'=>$id])
            ->order('a.group_model_id,a.order')
            ->select();
        $this->assign('list',$list);
        $this->assign('p_name',$p_name);
        $this->display();
    }
    /**
     * 四级版块列表
     */
    public function class_four()
    {
        $id = I('get.id');
        $p=M('group_model_class')->where(['id'=>$id])->field('name,pid')->find();//三级
        $p_name=$p['name'];//三级
        $p_name2=M('group_model_class')->where(['id'=>$p['pid']])->getField('name');//二级
        $Model = M('group_model_class');
        $list = $Model->alias('a')
            ->field('a.*,b.name as group_name')
            ->join('left join group_model as b on b.id = a.group_model_id')
            ->where(['pid'=>$id])
            ->order('a.group_model_id,a.order')
            ->select();
        $this->assign('list',$list);
        $this->assign('p_name',$p_name);
        $this->assign('p_name2',$p_name2);
        $this->display();
    }

    /**
     * 添加
     */
    public function add_four()
    {
        if(IS_POST){
            $res=false;
            $Model = M('group_model_class');
            $data = $Model->create();
            $img=upload_file('img','/img/');
            if($img['errcode']==200){
                $data['images']=$img['data'];
            }
            $data['update_time'] = time();
            $data['group_model_id']=3;
            $data['pid']=17;
            if($data['id']){//修改

            }else{
                $res = $Model->add($data);
                $opdata["content"] = "修改了四级功能模块信息。编号为:" . I('post.id') . "。";
            }

            if ($res) {
                admin_log($opdata["content"]);
                echo "<div id='kk' style='display:none'>1</div>";
            } else {
                echo "<div id='kk' style='display:none'>2</div>";
            }
            $this->display("edit_three");
        }else{
            $id = I('get.id');
            $Model = M("group_model_class");
            $res = $Model->where(['id'=>$id])->find();
            $this->assign('res', $res);
            $this->display();
        }
    }
    /**
     * 屏蔽
     */
    public function stop()
    {
        $id = I('post.id');
        $result = M('c')->where(['id'=>$id])->save(['status' => 0]);
        $opdata["content"] = "屏蔽了二级功能模块信息。编号为:" . I('post.id') . "。";
        admin_log($opdata["content"]);
        $this->ajaxReturn($result);
    }

    /**
     * 启用
     */
    public function start()
    {
        $id = I('post.id');
        $result = M('group_model_class')->where(['id'=>$id])->save(['status' => 1]);

        $opdata["content"] = "启用了二级功能模块信息。编号为:" . I('post.id') . "。";
        admin_log($opdata["content"]);
        $this->ajaxReturn($result);
    }

    /**
     * 修改
     */
    public function edit()
    {
        $id = I('get.id');
        $Model = M("group_model_class");
        $res = $Model->where(['id'=>$id])->find();
        $this->assign('res', $res);
        $model_list = getOneFunctionModel();
        $this->assign('model_list',$model_list);
        $this->display();
    }

    /**
     * 修改提交
     */
    public function editpost()
    {
        $Model = M('group_model_class');
        $data = $Model->create();
        $img=upload_file('img','/img/');
        if($img['errcode']==200){
            $data['images']=$img['data'];
        }
        $data['update_time'] = time();
        $res = $Model->where(['id'=>I('post.id')])->save($data);
        if ($res) {
            $opdata["content"] = "修改了二级功能模块信息。编号为:" . I('post.id') . "。";
            admin_log($opdata["content"]);
            echo "<div id='kk' style='display:none'>1</div>";
        } else {
            echo "<div id='kk' style='display:none'>2</div>";
        }
        $this->display("edit");
    }

    /**
     * 修改三级板块
     */
    public function edit_three()
    {
        if(IS_POST){
            $Model = M('group_model_class');
            $data = $Model->create();
            $img=upload_file('img','/img/');
            if($img['errcode']==200){
                $data['images']=$img['data'];
            }
            $data['update_time'] = time();
            $res = $Model->where(['id'=>I('post.id')])->save($data);
            if ($res) {
                $opdata["content"] = "修改了三级功能模块信息。编号为:" . I('post.id') . "。";
                admin_log($opdata["content"]);
                echo "<div id='kk' style='display:none'>1</div>";
            } else {
                echo "<div id='kk' style='display:none'>2</div>";
            }
            $this->display("edit_three");
        }else{
            $id = I('get.id');
            $Model = M("group_model_class");
            $res = $Model->where(['id'=>$id])->find();
            $this->assign('res', $res);
            $model_list = getOneFunctionModel();
            $this->assign('model_list',$model_list);
            $this->display();
        }
    }

    /**
     * 删除
     */
    public function del()
    {
        $id = I('post.id');
        $Model = M('group_model');
        $res = $Model->where(['id'=>I('post.id')])->find();

        if ($res) {
            $opdata["content"] = "删除了二级功能模块信息。编号为:" . $id . "。";
            admin_log($opdata["content"]);
            $this->ajaxReturn(1);
        } else {
            $this->ajaxReturn(0);
        }
    }
}