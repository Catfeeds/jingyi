<?php
namespace Admin\Controller;
use Think\Controller;
class SetoutlayController extends CommonController {

    public function _initialize(){
        parent::_initialize();
        if(session('admin_key_id') !=1){
            if(!in_array('7',session('admin_key_auth'))){
                $this->redirect('Login/index',array(), 3, '无权限...');
            }
        }

    }

    public function index(){
        if($_POST){
            $Model = M("webconfig");
            $data = $Model->create();
            $res=$Model->add($data);
            if($res){
                echo "<div id='close' style='display:none;'>1</div>";
            }else{
                echo "<div id='close' style='display:none;'>2</div>";
            }
        }else{
            $this->display();
        }
    }

    //订单分成比例设置
    public function scale_set(){
        if($_POST){
            $Model = M("order_ratio");
            $data = $Model->create();
            if($data['userratio'] + $data['tuibussnessratio'] + $data['ptratio'] + $data['bussnessratio'] != 100){
                $this->error('比例设置有误','scale_set',3);die;
            }
            $data['addtime'] = time();
            $res=$Model->add($data);
            if($res){
                echo "<div id='close' style='display:none;'>1</div>";
                $this->display('Setoutlay/scale-set');
            }else{
                echo "<div id='close' style='display:none;'>2</div>";
                $this->display('Setoutlay/scale-set');
            }
        }else{
            $Model=M("order_ratio");
            $order="addtime desc";
            $list = $Model->order($order)->limit(1)->select();

            $this->assign('list',$list[0]);
            $this->display('scale-set');
        }
    }
}