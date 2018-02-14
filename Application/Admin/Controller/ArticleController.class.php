<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/8
 * Time: 11:21
 */
namespace Admin\Controller;
use Think\Controller;
class ArticleController extends CommonController
{

    public function _initialize()
    {
        parent::_initialize();
        if (session('admin_key_id') != 1) {
            if (!in_array('6', session('admin_key_auth'))) {
                session('[destroy]');
                $this->redirect('Login/index', array(), 1, '无权限...');
            }
        }
    }

    public function index(){
        $Model=M("article");
        $list = $Model->select();
        $this->assign('list',$list);
        $this->display();
    }

    public function add(){
        $this->display();
    }

    public function edit(){
        $id = I('id',0,'int');
        $Model = M("article");
        $where = "id=".$id;
        $res = $Model->where($where)->find();
        $this->assign('res',$res);
        $this->display();
    }

    public function addPost(){
        $Model = M('article');
        $data = $Model->create();
        $rep = $Model->add($data);
        if($rep){
            admin_log('添加文章，编号：'.$rep);
            $this->success("增加成功",'',2);
        }else{
            $this->error("增加失败",'',2);
        }
    }

    public function about(){
        $id = I('get.id',0,'int');
        $Model = M("article");
        $where = "id=".$id;
        $res = $Model->where($where)->find();//print_r($res);die;
        $this->assign('res',$res);
        $this->display();
    }

    public function protocol(){
        $id = I('id',0,'int');
        $Model = M("article");
        $where = "id=".$id;
        $res = $Model->where($where)->find();//print_r($res);die;
        $this->assign('res',$res);
        $this->display();
    }

    /**
     * 关于我们修改
     */
    public function editPost(){
        $id = I('get.id');
        $Model = M('article');
        $data = $Model->create();
		$data["content"]=htmlspecialchars_decode($data["content"]);
        $rep = $Model->where('id='.$id)->save($data);
        if($rep){
            $type='';
            $id==2?$type='关于我们':$type='联系客服';
            admin_log('修改'.$type);
            $this->success("修改成功",'',2);
        }else{
            $this->error("修改失败",'',2);
        }
    }
}