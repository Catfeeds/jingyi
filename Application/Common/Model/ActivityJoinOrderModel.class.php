<?php
namespace Common\Model;
use Think\Model;
//活动报名信息表
class ActivityJoinOrderModel extends Model{
	
	 protected $tableName = 'activity_join_order'; 
	 
    /*
    * 加入活动
    */
    public function addpost($data) { 
        $Model = M('activity_join_order');
		$data["addtime"]=time();
        $res = $Model->add($data);
        return $res;
    }
	
	 /*
    * 清除之前报名的信息
    */
    public function delpost($activity_id,$userid) {  
        $Model = M('activity_join_order');
		$where="activity_id=".$activity_id." and userid =".$userid." and status=1" ;
        $res = $Model->where($where)->delete();
        return $res;
    }
	
	/*
    * 加入活动人数
    */
    public function getjoinnum($activity_id) {  
        $Model = M('activity_join_order');
		$where="activity_id=".$activity_id;
        $res = $Model->where($where)->count();
        return $res;
    }
	
	/*
    * 修改活动信息
    */
    public function editpost($where,$data) {  
        $Model = M('activity_join_order');
        $res = $Model->where($where)->save($data);
		if($res===0){
			$res=true;
			}
        return $res;
    }
	
	/*
    * 判断是否参加了活动 (已经支付了的 没支付的不算)
    */
    public function ischeckjoin($userid,$activity_id) {  
        $Model = M('activity_join_order');
		$where="userid=".$userid." and activity_id=".$activity_id;
        $res = $Model->where($where)->find();
		if($res){
			$res=true;
			}else{
				$res=false;
				}
        return $res;
    }
	
	/*
    * 获取参加活动的人员列表
    */
    public function getjoinuserlist($activity_id) {  
		$where="activity_id=".$activity_id;
		$field="a.userid,user.headimg,user.username";
		$join="user ON a.userid=user.userid"; 
		$order="a.addtime desc";
        $res = $this->alias("a")->field($field)->where($where)->join($join)->limit($limit)->order($order)->select();
		if($res){
			for($i=0;$i<count($res);$i++){
					$res[$i]["headimg"]= imgpath($res[$i]["headimg"]);
				}
		}
		if(!$res){$res=array();}
        return $res;
    }
	
	/*
    * 获取用户参与的活动id
	*$type 1|进行中 2|以完成
    */
    public function getUserJoinActivityid($userid,$type,$order,$limit) {  
		$where="a.userid=".$userid." and b.status=".$type;
		$field="b.*";
		$join="activity as b ON a.activity_id=b.activity_id";
        $res = $this->alias("a")->where($where)->join($join)->order($order)->limit($limit)->select();
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
	
	
	
	
  
}