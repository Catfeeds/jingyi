<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/16
 * Time: 11:06
 */

namespace Admin\Controller;


class ShieldingController extends CommonController
{
    public function _initialize()
    {
        parent::_initialize();
        if (session('admin_key_id') != 1) {
            if (!in_array('19', session('admin_key_auth'))) {
                session('[destroy]');
                $this->redirect('Login/index', array(), 1, '无权限...');
            }
        }
    }

    /**
     * 敏感词列表
     */
    public function index()
    {
        $shielding = M('shielding');
        $count      = $shielding->count();
        $Page       = new \Think\Page($count,10);
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $show       = $Page->show();
        $info = $shielding->order('shielding_id')->limit($Page->firstRow.','.$Page->listRows)->select();
        $this->assign('page',$show);
        $this->assign("list", $info);
        $this->assign("count", $count);
        $this->display();
    }
    /**
     * 敏感词删除
     */
    public function del(){
        $shielding_id=I('post.shielding_id');
        $res=M('shielding')->where(['shielding_id'=>$shielding_id])->find();
        if(!$res){
            echo json_encode(['code'=>201,'msg'=>'获取数据失败']);
            exit;
        }
        $result=M('shielding')->where(['shielding_id'=>$shielding_id])->delete();
        if($result){
            echo json_encode(['code'=>200,'msg'=>'删除成功']);
            exit;
        }else{
            echo json_encode(['code'=>201,'msg'=>'删除失败']);
            exit;
        }
    }
    /**
     * 敏感词添加
     */
    public function add(){
        if(IS_POST){
            $content=I('post.content');
            if(!$content){
                echo json_encode(['code'=>201,'msg'=>'获取数据失败']);
                exit;
            }
            $result=M('shielding')->add(['content'=>$content,'created_time'=>time()]);
            if($result){
                echo json_encode(['code'=>200,'msg'=>'添加成功']);
                exit;
            }else{
                echo json_encode(['code'=>201,'msg'=>'添加失败']);
                exit;
            }
        }
        $this->display();
    }

}