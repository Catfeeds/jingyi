<?php

namespace Common\Model;

use Think\Model;

/* * 用户自定义表情* */

class UserChatPicModel extends Model {
    /*
     * 添加自定义表情
     * @param  $data  
     */

    public function addpost($data) {
       $data["addtime"]=time();
      $res = $this->add($data);
      return $res;
    }
	
	 /*
     * 删除自定义表情
     * @param  $user_chat_pic_id  
     */

    public function delpost($user_chat_pic_id,$userid) {
      $where="user_chat_pic_id=".$user_chat_pic_id." and userid=".$userid;
      $res = $this->where($where)->delete();
      return $res;
    }
	
	/*
     * 批量删除自定义表情
     */
    public function delallpost($idstr,$userid) {
		
       $where="user_chat_pic_id in (".$user_chat_pic_id.") and userid=".$userid;
      $res = $this->where($where)->delete();
      return $res;
    }
	
	/*
     * 获取用户的自定义表情列表
     */
    public function getuserchatpiclist($userid,$limit) {
      $where="userid=".$userid;
	  $order="addtime asc";
      $res = $this->where($where)->order($order)->limit($limit)->select();
	  if(!$res){$res=array();}
	  if($res){
		  for($i=0;$i<count($res);$i++){
			  $res[$i]["img"]=imgpath($res[$i]["img"]);
		  }
		}
      return $res;
    }
	
	

}
