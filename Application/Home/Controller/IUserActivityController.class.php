<?php

namespace Home\Controller;

use Think\Controller;
//用户参加的活动列表
class IUserActivityController extends CommonController {

	  /*
     * 获取用户参加的活动列表
	 * @param  $userid   //用户id
     */
    public function getusertuilist() {
		$userid = I('userid');
		$status = I('status')?I('status'):1; 
        $page = I('page')?I('page'):1;
        $limit = I('limit')?I('limit'):10;
        $limit1 = ($page-1)*$limit.",".$limit;
		if($status==1){ //进行中
			$order="b.begintime asc";
			}else{
			$order="b.endtime desc";	
			}
		
		$ActivityJoinOrderModel = D('ActivityJoinOrder');
		$res=$ActivityJoinOrderModel->getUserJoinActivityid($userid,$status,$order,$limit1); //获取参与的活动id
		
        get_api_result(200, "获取成功", $res);
       
    }


}
