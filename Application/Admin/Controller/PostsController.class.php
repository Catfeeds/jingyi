<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/17
 * Time: 13:25
 */

namespace Admin\Controller;


class PostsController extends CommonController
{
    public function _initialize()
    {
        parent::_initialize();
        if (session('admin_key_id') != 1) {
            if (!in_array('9', session('admin_key_auth'))) {
                session('[destroy]');
                $this->redirect('Login/index', array(), 1, '无权限...');
            }
        }
    }

    /**
     * 星球帖列表
     */
    public function index()
    {
        $where=['user_planet_posts.posts_status'=>1];
        $return=[];
        $get=I('get.');
        if($get['start']){
            $where=array_merge($where,['user_planet_posts.addtime'=>['egt',strtotime($get['start'])]]);
            $return['start']=$get['start'];
        }
        if($get['end']){
            $where=array_merge($where,['user_planet_posts.addtime'=>['elt',strtotime($get['end'])]]);
            $return['end']=$get['end'];
        }
        if($get['username']){
            $where=array_merge($where,['user.username'=>['like','%'.$get['username'].'%']]);
            $return['username']=$get['username'];
        }
        if($get['type']){
            $where=array_merge($where,['user_planet_posts.type'=>$get['type']]);
            $return['type']=$get['type'];
        }

        $posts = M('user_planet_posts');
        $count      = $posts->join('user on user.userid=user_planet_posts.userid')->where($where)->count();
        $Page       = new \Think\Page($count,10);
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $show       = $Page->show();
        $info = $posts
            ->join('user on user.userid=user_planet_posts.userid')
            ->field('user_planet_posts.*,user.username')
            ->where($where)->order('user_planet_posts.posts_id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach ($info as &$item){
            $PostsModel = D("Posts");
            $item["filemsg"] = $PostsModel->getpostsfilesinfobypostsid($item['posts_id']);
        }
//        var_dump($info);exit;
        $this->assign('page',$show);
        $this->assign("list", $info);
        $this->assign("count", $count);
        $this->assign('return', $return);
        $this->display();
    }
    /**
     * 星球帖删除
     */
    public function del(){
        $posts_id=I('post.posts_id');
        $res=M('user_planet_posts')->where(['posts_id'=>$posts_id])->find();
        if(!$res){
            echo json_encode(['code'=>201,'msg'=>'获取数据失败']);
            exit;
        }
        $keyModel = D("Posts");
        $result = $keyModel->delpost($posts_id);
        if($result){
            echo json_encode(['code'=>200,'msg'=>'删除成功']);
            exit;
        }else{
            echo json_encode(['code'=>201,'msg'=>'删除失败']);
            exit;
        }
    }

}