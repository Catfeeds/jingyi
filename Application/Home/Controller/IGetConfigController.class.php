<?php
namespace Home\Controller;
use Think\Controller;

class IGetConfigController extends CommonController {
	

     /**
     *  获取单篇文章
     */
    public function getarticle(){
        $id = I('id');
        $ArticleModel=D("Article");
		$res=$ArticleModel->getlist($id);
		$msg="获取成功！";
		get_api_result(200, $msg,$res);
    }

    /**
     *  获取全部职业选项
     */
    public function getProfessionSign(){
        $ProfessionSignModel=D("ProfessionSign");
		$res=$ProfessionSignModel->getlist();
		$msg="获取成功！";
		get_api_result(200, $msg,$res);
    }
  

}