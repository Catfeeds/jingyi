<?php
namespace Common\Model;
use Think\Model;
//群活动人员评价表
class ActivityUserCommentModel extends Model{

    /*
    * 添加活动评价
    */
    public function addpost($data) {  
		$data["addtime"]=time();
        $res = $this->add($data);
        return $res;
    }
	
	 /*
    * 批量添加活动评价
    */
    public function addallpost($data) {
        $res = $this->addAll($data);
        return $res;
    }
	
	 /*
    * 判断是否已评价活动成员
    */
    public function ischeckcomment($activity_id,$userid) {  
	    $where="activity_id=".$activity_id." and userid =".$userid;
        $msg = $this->where($where)->find();
		$res=false;
		if($msg){
			$res=true;
			}
        return $res;
    }
	
	
	 /*
    * 获取评价列表信息
    */
    public function getuserlistbyactivityid($activity_id,$userid) {
		$field="a.status,user.headimg,user.username,user.userid";  
	    $where="a.activity_id=".$activity_id." and a.userid =".$userid;
		$join=" left join user ON a.beuserid=user.userid";
        $res = $this->alias("a")->field($field)->where($where)->join($join)->select();
		if(!$res){
			$res=array();
			
		}else{
			for($i=0;$i<count($res);$i++){
				$res[$i]["headimg"]= imgpath($res[$i]["headimg"]);
				}
			}
        return $res;
    }
	
  
}