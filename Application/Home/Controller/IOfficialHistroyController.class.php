<?php

namespace Home\Controller;

use Think\Controller;

class IOfficialHistroyController extends CommonController {

	  /*
     * 获取编年史分页列表信息
	 * @param  $page   //显示页数 1
	 * @param  $limit   //显示条数 10
     */
    public function getOfficialHistroylist() {
		$page=I("page")?I("page"):1;
		$limit=I("limit")?I("limit"):10;
		$limit1=($page-1)*$limit.",".$limit;
		
		//获取 贴子信息
		$OfficialHistroyModel=D("OfficialHistroy");
		$order="dotime desc";
		$where="";
		$res=$OfficialHistroyModel->getPostslist($where,$order,$limit1); 
		
        get_api_result(200,"获取成功",$res);
    }
	

}
