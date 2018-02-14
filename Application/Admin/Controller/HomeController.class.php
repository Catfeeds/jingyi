<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/30
 * Time: 13:09
 */

namespace Admin\Controller;


class HomeController extends CommonController
{
    public function _initialize()
    {
        parent::_initialize();
        if (session('admin_key_id') != 1) {
            if (!in_array('21', session('admin_key_auth'))) {
                session('[destroy]');
                $this->redirect('Login/index', array(), 1, '无权限...');
            }
        }
    }

    /**
     * 官网首页
     */
    public function index(){
        $res=M('home')->select();
        $this->assign('data', $res);
        $this->display();
    }
    /**
     * 修改
     */
    public function edit(){
        if(IS_POST){//修改
            $post=I('post.');
            $id=$post['id'];
            unset($post['id']);
            $post['content']=replace_img($post['content']);
            $post['update_time']=time();
            if(!$id){
                echo json_encode(['code' => 201, 'msg' =>'数据错误']);exit;
            }
            $res=M('home')->where(['id'=>$id])->save($post);
            if($res){
                echo json_encode(['code' => 200, 'msg' =>'编辑成功']);exit;
            }
            echo json_encode(['code' => 201, 'msg' =>'编辑失败']);exit;
        }else{//修改界面
            $id=I('get.id');
            if($id==5){
                $data=M('home')->where(['id'=>$id])->find();
                $this->assign('data', $data);
                $this->display('Home/index_edit');
            }else{
                $data=M('home')->where(['id'=>$id])->find();
                $this->assign('data', $data);
                $this->display();
            }
        }
    }
    /**
     * 主页修改
     */
    public function index_edit(){
        $old=json_decode(M('home')->where(['id'=>5])->getField('content'));
        if(IS_POST){
            $a1=upload_file('a1','/img/',['mp4']);
            $a2=upload_file('a2','/img/');
            $a3=upload_file('a3','/img/',['mp4']);
            $data=[];
            $num=0;
            if($a1['errcode']==200){
                $data['file_1']=$a1['data'];
                $num+=1;
            }else{
                $data['file_1']=$old->file_1;
            }
            if($a2['errcode']==200){
                $data['file_2']=$a2['data'];
                $num+=1;
            }else{
                $data['file_2']=$old->file_2;
            }
            if($a3['errcode']==200){
                $data['file_3']=$a3['data'];
                $num+=1;
            }else{
                $data['file_3']=$old->file_3;
            }
            if($num==0){
                $this->error('未做任何修改');
            }
            $res=M('home')->where(['id'=>5])->save(['content'=>json_encode($data),'update_time'=>time()]);
            if($res){
                $this->success('编辑成功');
            }else{
                $this->error('编辑失败');
            }
        }else{
            $this->assign('old', $old);
            $this->display();
        }

    }
}