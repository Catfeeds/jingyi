<?php
namespace Home\Controller;
use Think\Controller;
//活动信息
class IActivityController extends CommonController {
	
	
	  /**
     *  获取官方活动贴首页信息
     */
    public function getActivityIndexlist(){
		//获取banner信息
		 $BannerModel=D("Banner");
		 $res["bannerlist"]=$BannerModel->getlist();
		 
		$ActivityModel = D('Activity');
        $page =1;
        $limit =10;
        $limit1 = ($page-1)*$limit.",".$limit;
		$order="addtime desc";
		$where="type=1";
        $res["activitylist"] = $ActivityModel->getlist($where,$limit1,$order);
        get_api_result(200, "获取成功", $res);
    }

     /**
     *  获取官方活动贴列表
     */
    public function getActivitylist(){
		$ActivityModel = D('Activity');
        $page = I('page')?I('page'):1;
        $limit = I('limit')?I('limit'):10;
        $limit1 = ($page-1)*$limit.",".$limit;
		$order="addtime desc";
		$where="type=1";
        $res = $ActivityModel->getlist($where,$limit1,$order);
        get_api_result(200, "获取成功", $res);
    }

    /*
     * 获取活动详情
     */
    public function getActivityinfo() {
        $activity_id = I('activity_id');
		$userid = I('userid');
        $ActivityModel = D('Activity');
        $res = $ActivityModel->getinfo($activity_id,$userid) ;
        get_api_result(200, "获取成功", $res);
    }

    /*
     * 加入活动   
     */
    public function addActivity() {
        $data['userid'] = I('userid');
        $data['activity_id'] = I('activity_id');
        $data['order_no'] = build_order_no();
		$ActivityModel=D("Activity");
		$ActivityJoinOrderModel = D('ActivityJoinOrder');
		$del=$ActivityJoinOrderModel->delpost( $data['activity_id'],$data['userid']); 	//清除之前该用户已经报名的信息但没有缴费的信息
		$Activitymsg=$ActivityModel->getinfo( $data['activity_id'],$data['userid']) ;  //获取活动信息
		//判断活动是否存在   正常情况下 活动必然存在
		if(count($Activitymsg)==0){
			 get_api_result(401, "非法操作");
			}
			
		//判断用户没有加入该活动
		if($Activitymsg["joinstatus"]){
			 get_api_result(402, "已加入活动，请不要重复操作！",$Activitymsg);
			}
		
		//判断用户人数上线是否为无限人  无限人数量为-1
		if($Activitymsg["personnum"]!=-1){	
		//判断加入的人数是否达到上线
		if($Activitymsg["joinnum"]>=$Activitymsg["personnum"]){
			 get_api_result(402, "人数已达到上线！",$Activitymsg);
			}
		}
		
		//判断是否到达开始时间  
		if(time()>=$Activitymsg["begintime"]-C("activity_gettime")){
			 get_api_result(402, "已经截止报名");
			}
		
		//判断是否免费
		if(strval($Activitymsg["moneynum"]) != strval(0)){
			$data["status"]=1;
			}else{
			$data["status"]=2;	
				}
					
        $result = $ActivityJoinOrderModel->addpost($data); //添加完成
        if($result){
            get_api_result(200, "加入成功", $result);
        }else{
            get_api_result(401, "加入失败", $result);
        }
    }
    
   
      /*
     * 任务计划
     */
    public function doeditstatus() {
        $ActivityModel = D('Activity');
        $res = $ActivityModel->checkendtimetoeditstatus() ;
        get_api_result(200, "获取成功", $res);
    }

	

}