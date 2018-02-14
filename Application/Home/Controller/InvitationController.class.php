<?php

namespace Home\Controller;

use Think\Controller;

class InvitationController extends CommonController
{


//    public function index()
//    {
//        $tel = $_GET["tel"];
//        $this->assign("tel", $tel);
//        $this->display();
//    }
//
//    public function addpost()
//    {
//        $Model = M("question");
//        $data = $Model->create();
//        $where = "tel='" . $data["tel"] . "'";
//        $ischeck = $Model->where($where)->find();;
//        if ($ischeck) {
//            $this->assign("jumpUrl", "index");
//            $this->error("请勿重复提交。");
//        }
//        $res = $Model->add($data);
//        if ($res) {
//            $this->assign("jumpUrl", "index");
//            $this->success("您已成功提交，感谢您的使用！");
//        } else {
//            $this->assign("jumpUrl", "index");
//            $this->error("提交失败。");
//        }
//    }

    public function index(){
        $question=M('question')->where(['status'=>0])->select();
        $this->assign("question", $question);
        $this->display();
    }
    public function addpost(){
        $data=I('post.');
        $user=M('user')->where(['tel'=>$data['tel']])->getField('userid');
        if($user){
            $this->error('该手机号已存在');
        }
        M()->startTrans();
        //新建用户
        $userid=M('user')->add(['add_status'=>1,'tel'=>$data['tel'],'phone'=>$data['tel'],'apply_time'=>time(),'countrynum'=>'']);
        if(!$userid){
            M()->rollback();
            $this->error('提交申请失败');exit;
        }
        $insert=[];
        unset($data['tel']);
        foreach ($data as $key=>$item){
            $insert[]=['userid'=>$userid,'question_id'=>$key,'answer'=>$item];
        }
//        var_dump($insert);exit;
        $question=M('question_answer')->addAll($insert);
        if(!$question){
            M()->rollback();
            $this->error('提交申请失败');exit;
        }
        M()->commit();
        $this->success('提交申请成功');
    }

}
