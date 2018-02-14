<?php
namespace Common\Model;
use Think\Model;
//活动信息表
class ActivityModel extends Model{
	
	 protected $tableName = 'activity'; 
	 
    /*
    * 创建活动
    */
    public function addpost($data) {  
        $Model = M('activity');
		$data["addtime"]=time();
        $res = $Model->add($data);
        return $res;
    }
	
	 /*
    * 修改活动信息
    */
    public function editpost($where,$data) {  
        $Model = M('activity');
        $res = $Model->where($where)->save($data);
		if($res===0){
			$res=true;
			}
        return $res;
    }
	
	
	/*
    * 按条件获取活动列表
    */
    public function getlist($where,$limit,$order) {  
        $Model = M('activity');
        $res = $Model->where($where)->limit($limit)->order($order)->select();
		if(count($res)==0){
			$res=array();
			}else{
				$UserModel=D("User");
				$nowtime=time();
				for($i=0;$i<count($res);$i++){
					if($res[$i]["status"]==1){
						if($res[$i]["begintime"]-C("activity_gettime")>$nowtime){ //报名截止时间
							$res[$i]["activity_status"]=1;
							}else{
							$res[$i]["activity_status"]=2;	
								}
						}else{
							$res[$i]["activity_status"]=3;
							}
					$res[$i]["activity_img"]=imgpath($res[$i]["activity_img"]);
					if($res[$i]["type"]==2){
						$res[$i]["usermsg"]=$UserModel->getuserbasemsgbyuserid($res[$i]["userid"]);
						}else{
							$res[$i]["usermsg"]=array();
							}
					}
				}
		
        return $res;
    }
	
	/*
    * 获取活动详情
    */
    public function getinfo($activity_id,$userid="") {  
        $Model = M('activity');
		$where="activity_id=".$activity_id;
        $res = $Model->where($where)->find();
		
		$UserModel=D("User");
		$res["activity_img"]=imgpath($res["activity_img"]);
		if($res["type"]==2){
			$res["usermsg"]=$UserModel->getusermsg1byuserid($res["userid"]); //群活动发起人信息
			}else{
				$res["usermsg"]=array();
			}
		$ActivityJoinOrderModel=D("ActivityJoinOrder");
		$res["joinnum"]=$ActivityJoinOrderModel->getjoinnum($activity_id);
		if(empty($userid)){
			$res["joinstatus"]=false;
			}else{
				$res["joinstatus"]=$ActivityJoinOrderModel->ischeckjoin($userid,$activity_id);
				}
		
        return $res;
    }

   /*
    * 活动已完成(任务计划)
    */
    public function checkendtimetoeditstatus() {  
        $Model = M('activity');
		$where="endtime<=".time()." and status=1";
        $res = $Model->where($where)->select();
		
		for($i=0;$i<count($res);$i++){
		$dowhere="activity_id=".$res[$i]["activity_id"];
		$data["status"]=2;
	    $msg=$this-> editpost($dowhere,$data) ;
		}
		
        return true;
    }
	

  
}