<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/8
 * Time: 11:21
 */
namespace Home\Controller;
use Think\Controller;
class ArticleController extends Controller
{


    public function index(){
        $Model=M("article");
		$where="id=".$_GET["id"];
        $info = $Model->where($where)->find();
        $this->assign('info',$info);
        $this->display();
    }

}