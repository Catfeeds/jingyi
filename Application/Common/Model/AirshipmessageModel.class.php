<?php

namespace Common\Model;

use Think\Model;

/* * 空间站消息* */

class AirshipmessageModel extends Model {
    /*
     * 判断是否存在未读消息
     * @param  $userid  用户id
     */

    public function checkisreadmessage($userid) {
        $where="userid=".$userid." and isread=1";
        $res= $this->where($where)->find();
        if (!$res) {
            return false;
        } else {
            return true;
        }
    }
	
	
	   /*
     * 设置空间站未读消息
     * @param  $userid  用户id
     */

    public function setpost($userid) {
        $ischeck=$this->checkmessage($userid);
        if (!$ischeck) {
			$data["userid"]=$userid;
			$data["isread"]=1;
           $res=$this->addpost($data);
        } else {
			$data["isread"]=1;
           $res=$this->editpost($userid,$data);
        }
		return $res;
    }
	
	
    /*
     * 添加空间站未读消息
     * @param  $userid  用户id
     */

    public function addpost($data) {
        $res=$this->add($data);
		return $res;
    }
	
	 /*
     * 修改空间站未读消息
     * @param  $userid  用户id
     */

    public function editpost($userid,$data) {
		$where="userid=".$userid;
        $res=$this->where($where)->save($data);
		if($res===0){
			$res=true;
			}
		return $res;
    }

	
	   /*
     * 判断是否存在空间站消息信息
     * @param  $userid  用户id
     */

    public function checkmessage($userid) {
        $where="userid=".$userid;
        $res= $this->where($where)->find();
        
        return $res;
        
    }

}
