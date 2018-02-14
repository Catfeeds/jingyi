<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/11
 * Time: 13:46
 */

namespace Admin\Controller;

use Think\Controller;

class QuestionController extends CommonController
{
    public function _initialize()
    {
        parent::_initialize();
        if (session('admin_key_id') != 1) {
            if (!in_array('15', session('admin_key_auth'))) {
                session('[destroy]');
                $this->redirect('Login/index', array(), 1, '无权限...');
            }
        }
    }

    public function index()
    {
        $Model = M('question');
        $where = ['status'=>0];
        $order = "question_id asc";
        $count = $Model->where($where)->count();
        $limit = 10;
        $Page = new \Think\Page($count, $limit);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $show = $Page->show();// 分页显示输出
        $limit1 = $Page->firstRow . ',' . $Page->listRows;
        $list = $Model->where($where)->limit($limit1)->order($order)->select();
        $this->assign('list', $list);
        $this->assign('page', $show);// 赋值分页输出
        $this->display();
    }

    /**
     * 问题删除
     */
    public function del(){
        $question_id=I('post.question_id');
        $res=M('question')->where(['question_id'=>$question_id])->find();
        $str=M()->getLastSql();
        if(!$res){
            echo json_encode(['code'=>201,'msg'=>'获取数据失败',$str]);
            exit;
        }
        $result=M('question')->where(['question_id'=>$question_id])->save(['status'=>1]);
        $str=M()->getLastSql();
        if($result){
            echo json_encode(['code'=>200,'msg'=>'删除成功']);
            exit;
        }else{
            echo json_encode(['code'=>201,'msg'=>'删除失败',$str]);
            exit;
        }
    }
    /**
     * 问题添加
     */
    public function add(){
        $question_id=I('get.question_id');
        if($question_id){
            $question=M('question')->where(['question_id'=>$question_id])->find();
            $this->assign('question',$question);
        }
        if(IS_POST){
            $name=shielding(I('post.name'));
            $question_id=I('post.question_id');
            if(!$name){
                echo json_encode(['code'=>201,'msg'=>'获取数据失败']);
                exit;
            }
            if($question_id){//修改
                $result=M('question')->where(['question_id'=>$question_id])->save(['name'=>$name]);
                if($result){
                    echo json_encode(['code'=>200,'msg'=>'添加成功']);
                    exit;
                }
            }else{//添加
                $result=M('question')->add(['name'=>$name,'add_time'=>time()]);
                if($result){
                    echo json_encode(['code'=>200,'msg'=>'添加成功']);
                    exit;
                }
            }
            echo json_encode(['code'=>201,'msg'=>'添加失败']);
            exit;

        }
        $this->display();
    }


}