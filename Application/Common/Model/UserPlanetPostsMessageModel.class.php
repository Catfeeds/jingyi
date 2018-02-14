<?php

namespace Common\Model;

use Think\Model;

/* * 用户星球* */

class UserPlanetPostsMessageModel extends Model {


    /*
     * 判断用户是否存在星球贴未读信息
     * @param  $userid  用户id
     */

    public function checkUserPlanet($userid) {
        $where = "beuserid=" . $userid." and isread=1";
        $msg = $this->where($where)->count();
        if ($msg==0) {
            $res = false;
        }  //查询不到 返回空数组
        else {
            $res = true;
        }
        return $res;
    }
	
	 /*
     * 获取用户星球贴未读信息数量
     * @param  $userid  用户id
	 * @param  $postsid  贴子id
     */

    public function getmessagecount($userid) {
        $where = "beuserid=" . $userid." and isread=1";
        $res = $this->where($where)->count();
        return $res;
    }
    
	
    /*
     * 获取用户星球贴未读信息列表
     * @param  $userid  用户id
     */

    public function getmessagelist($userid) {
        $where = "beuserid=" . $userid." and isread=1";
        $res = $this->where($where)->select();
		if(count($res)==0){
			$res=array();
			}else{
				$where1 = "beuserid=" . $userid." and isread=1";
				$res1 = $this->where($where1)->group("posts_id")->select();
				for($i=0;$i<count($res1);$i++){
					$this->messageisread($userid, $res1[$i]["posts_id"]);  //消息已读
					
					}
				$UserModel=D("User");
				for($i=0;$i<count($res);$i++){
				$res[$i]["userinfo"]=$UserModel->getuserbasemsgbyuserid($res[$i]["userid"]); 
					
			}
				
				}
        return $res;
    }
	
	 /*
     * 用户星球贴未读信息已读
     * @param  $userid  用户id
	  * @param  $postsid  贴子id
     */

    public function messageisread($userid, $postsid) {
        $where = "beuserid=" . $userid." and posts_id=".$postsid." and isread=1";
		$data["isread"]=2;
        $res = $this->where($where)->save($data);
        return $res;
    }
	
	 /*
     * 添加用户星球贴未读信息
     * @param  $userid  用户id
	  * @param  $postsid  贴子id
     */

    public function addpost($data) {

       
		$data["addtime"]=time();
		$data["isread"]=1;
        $res = $this->add($data);
        return $res;
    }
	
	 /*
     * 删除用户星球贴全部信息
	  * @param  $postsid  贴子id
     */

    public function delpost($postsid) {
		$where = "posts_id=".$postsid;
        $res = $this->where($where)->delete();
        return $res;
    }
    
    

}
