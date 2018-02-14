<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/29
 * Time: 16:49
 */
namespace Frontend\Controller;
use Think\Controller;

class IndexController extends Controller
{
    /**
     * 首页
     */
    public function index(){
        $res=get_home(0);
        $url=json_decode($res[4]['content']);
        $this->assign("data", $res);
        $this->assign("url", $url);
        $this->display();
    }

    /**
     * 邀约入口
     */
    public function invitation(){
        $question=M('question')->where(['status'=>0])->select();
        $this->assign("question", $question);
        $this->display();
    }

    /**
     * 游客入口
     */
    public function visitor(){
        $question=M('question')->where(['status'=>0])->select();
        $this->assign("question", $question);
        $this->display();
    }
    /**
     * 关于
     */
    public function about(){
        $res=get_home(0);
        $this->assign("data", $res);
        $this->display();
    }
    /**
     * 俱乐部
     */
    public function club(){
        $res=get_home(0);
        $this->assign("data", $res);
        $this->display();
    }
    /**
     * 联系我们
     */
    public function contact(){
        $res=get_home(0);
        $this->assign("data", $res);
        $this->display();
    }
    /**
     * 国际活动
     */
    public function international(){
        $res=get_home(0);
        $this->assign("data", $res);
        $this->display();
    }

}
