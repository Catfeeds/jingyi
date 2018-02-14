<?php
namespace Home\Controller;
use Think\Controller;
//群活动信息
class IGroupActivityController extends CommonController {
	
	
	 /**
     *  创建群活动贴列表
     */
    public function addGroupActivity(){
		
		$ActivityModel = D('Activity');
		
        $data["activity_name"] = I("activity_name");
		$data["activity_content"] = I("activity_content");
		$data["activity_address"] = I("activity_address");
		$data["activity_contactstel"] = I("activity_contactstel");
		$data["personnum"] = I("personnum");
		$data["moneynum"] = I("moneynum");
		$data["userid"] = I("userid");
		$data["groupid"] = I("groupid");
		$data["begintime"]=strtotime(I("begintime"));
		$data["endtime"]=strtotime(I("endtime"));
		if($data["begintime"]>=$data["endtime"]){
			 get_api_result(300, "结束时间错误！");
			}
		if($data["begintime"]-C('activity_begin')<=time()){
			 get_api_result(300, "只能创建1天后！");
			}
		if($data["begintime"]<=time()){
			 get_api_result(300, "开始时间错误！");
			}
        $data["status"]=1;
		$data["type"] = 2;
		$upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 1024 * 1024 * 20;
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');
        $upload->rootPath = './'; // 设置附件上传目录
        $upload->savePath = './Public/upload/Activity/'; // 设置附件上传目录
        $upload-> saveName  =   array('uniqid','');
        $upload-> ischeckfile  =   true;  //必须上传文件
        $info = $upload->upload();
        if (!$info) { // 上传错误提示错误信息
            $this->error($upload->getError());
        } else { //上传成功获取上传文件信息
            foreach($info as $file){
                $data["activity_img"]=$file['savepath'].$file['savename'];
            }
        } // 保存表单数据包括附件数据<br />
		
		$ActivityModel = D('Activity');
        $res = $ActivityModel->addpost($data);
        get_api_result(200, "创建成功", $res);
    }

	
     /**
     *  获取群活动贴列表
     */
    public function getActivitylist(){
		$ActivityModel = D('Activity');
		$status = I('status')?I('status'):1; 
		$groupid = I('groupid'); 
        $page = I('page')?I('page'):1;
        $limit = I('limit')?I('limit'):10;
        $limit1 = ($page-1)*$limit.",".$limit;
		$order="addtime desc";
		$where="type=2";
		$where.=" and status=".$status;
		$where.=" and groupid=".$groupid;
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
		$ActivityCommentModel = D('ActivityComment');
        $res["activityinfo"] = $ActivityModel->getinfo($activity_id,$userid) ; //活动详情
		$res["comment"] = $ActivityCommentModel->getreplymsgbypostsid($activity_id,$order="",$limit=5,$smallid="") ;  	// 评价列表
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
     * 获取活动参加人员(活动未完成)
     */
    public function getJoinuserlist() {
        $activity_id = I('activity_id');
        $ActivityJoinOrderModel = D('ActivityJoinOrder');
        $res = $ActivityJoinOrderModel->getjoinuserlist($activity_id);
        get_api_result(200, "获取成功", $res);
    }
	
	  /*
     * 获取活动参加人员（活动已完成）
     */
    public function getcommentJoinuserlist() {
        $activity_id = I('activity_id');
		$userid = I('userid');
		$ActivityUserCommentModel=D("ActivityUserComment");
	    $ActivityJoinOrderModel = D('ActivityJoinOrder');
		$res["commentstatus"]=$ActivityUserCommentModel->ischeckcomment($activity_id,$userid);
	
			if($res["commentstatus"]){  //已经论评了
				  $res["userlist"] = $ActivityUserCommentModel->getuserlistbyactivityid($activity_id,$userid);
				}else{
					 $res["userlist"] = $ActivityJoinOrderModel->getjoinuserlist($activity_id);

				}
		
        get_api_result(200, "获取成功", $res);
    }
	
	
	/*
     * 批量评论已完成活动的用户
     */
    public function addJoinUserComment() {
        $activity_id = I('activity_id');
		$userid = I('userid');
		$useridstr = I('beuseridstr');
		$commentstr = I('commentstr');
		if($useridstr==""){
			 get_api_result(300, "请选择用户");
			}
		if($commentstr==""){
			 get_api_result(300, "评价不能为空");
			}
		$array=explode(",",$useridstr);
		$array1=explode(",",$commentstr); 
		if(count($array)!=count($array1)){
			 get_api_result(300, "非法操作");
			}
		$addtime= time();
		$ActivityUserCommentModel=D("ActivityUserComment");
		$UserModel=D("User");
		$UserLevelModel=D("UserLevel");
		$UserPlanetModel=D("UserPlanet");
		for($i=0;$i<count($array);$i++){
			$data[$i]["activity_id"]= $activity_id;
			$data[$i]["userid"]= $userid;
			$data[$i]["beuserid"]= $array[$i];
			$data[$i]["status"]= $array1[$i];
			$data[$i]["addtime"]= $addtime;
			
			$data1[$i]["userid"]=$array[$i];
			$data1[$i]["addtime"]=$addtime;
			if($array1[$i]==1){
				$data1[$i]["sroce"]=C("activity_good_comment");
				$data1[$i]["msg"]="群活动，id为：“".$activity_id."”,被用户id为：“".$userid."”评价，评价为“好评”，成长值+".C("activity_good_comment")."。";
				}else{
					$data1[$i]["sroce"]=C("activity_bad_comment");
					$data1[$i]["msg"]="群活动，id为：“".$activity_id."”,被用户id为：“".$userid."”评价，评价为“差评”，成长值".C("activity_bad_comment")."。";
					}
			
			
			 $usermsg= $UserModel->getusermsgbyuserid($array[$i]); //获取用户信息
			 
			 $data2["now_growth_value"]=$usermsg["now_growth_value"]+$data1[$i]["sroce"];
			 $data2["level"]= $UserLevelModel->userlevel($data2["now_growth_value"]);
			 $res3=$UserModel->editpostbyuserid($array[$i], $data2) ;   //更新用户等级信息
			 
			 if($usermsg["userplanetstatus"]){ //存在星球
			    $data3["growth_value"]= $usermsg["userplanetinfo"]["growth_value"]+$data1[$i]["sroce"];
			    $res4= $UserPlanetModel->editPostMsg($usermsg["userplanetinfo"]["planet_id"],$data3);  //更新星球成长值
			}
			 unset($usermsg);
			 unset($data2);
			 unset($data3);
			 unset($res3);
			 unset($res4);
			}
			
		$res=$ActivityUserCommentModel->addallpost($data);
		
		$UserLevelModel=D("UserLevel");
		$UserLevelModel->addAllpost($data1); //用户成长记录  
		                                     //用户成长值增加
											 //星球成长值增加
		if($res){
            get_api_result(200, "评价成功", $res);
        }else{
            get_api_result(300, "评价失败");
        }
    }
	
	 /*
     * 获取活动评论信息列表
     */
    public function getActivitycommentlist() {
        $activity_id = I('activity_id');
		$userid = I('userid');
		$page = I('page')?I('page'):1;
        $limit = I('limit')?I('limit'):10;
        $limit1 = ($page-1)*$limit.",".$limit;
		
       // $smallid = I('smallid')?I('smallid'):"";
		$ActivityCommentModel=D("ActivityComment");
		$res["comment"] = $ActivityCommentModel->getreplymsgbypostsid($activity_id,$order="",$limit1,$smallid) ;  	// 评价列表
		$ActivityJoinOrderModel=D("ActivityJoinOrder");
		$res["commentstatus"]=$ActivityJoinOrderModel-> ischeckjoin($userid,$activity_id);
        get_api_result(200, "获取成功", $res);
    }
	
	  /*
     * 添加贴子论评（群活动）
	 * @param  $activity_id  //活动id
	 * @param  $userid   //用户id
	 * @param  $content  //评论内容
	 * @param  $beuserid  //@的用户id 可不填
     */
    public function addPostsreply() {
		$data["activity_id"]=I("activity_id");
		$data["userid"]=I("userid");
		$data["content"]=I("content");
		$data["be_userid"]=I("beuserid");
		$ActivityJoinOrderModel=D("ActivityJoinOrder");
		$ischeck=$ActivityJoinOrderModel-> ischeckjoin($data["userid"],$data["activity_id"]);
		if(!$ischeck){
			 get_api_result(300,"您没有参与活动！");
			}
		$ActivityCommentModel=D("ActivityComment"); 
		 
		$msg=$ActivityCommentModel->addpost($data);
		
		$res=$ActivityCommentModel->getreplymsgbyreplyid($msg);
		if(!$msg){
			 get_api_result(300,"添加失败");
			}
        get_api_result(200,"添加成功",$res);
    }

}