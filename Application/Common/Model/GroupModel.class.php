<?php

namespace Common\Model;

use Think\Model;

/* * 聊天群* */

class GroupModel extends Model {
	
	//创建群
	  public function addpost($data) {
		$option["groupname"]=$data["name"];
		$option["desc"]=$data["intro"];
		$option["owner"]="jingyi".$data["userid"];
		$option["members"]=array();
		 
		$EasemobModel=D("Easemob");
		$hxres=$EasemobModel->register_hx_group($option); 
		if(empty($hxres["hx_groupid"])){
			 return false; 
			} 
		$data["addtime"]=time();
		$data["hx_groupid"]=$hxres["hx_groupid"];
        $res = $this->add($data);
		$GroupBearingModel=D("GroupBearing");
		$data1["groupid"]= $res;
		$data1["userid"]= $data["userid"];
		$data1["role"]=1;
		$res1=$GroupBearingModel->addpost($data1,$type=1);
		
        return $res;
    }
	
	//修改群信息
	  public function editpost($where,$data) {
		  
        $res = $this->where($where)->save($data);
		if($res===0){ $res=true;}
		
		$info = $this->where($where)->find();
		$group_id=$info["hx_groupid"];
		if(isset($data["name"])){
			$options['groupname']=$data["name"];
			}
		if(isset($data["intro"])){
			$options['description']=$data["intro"];
			}
	
		
		$EasemobModel=D("Easemob");
		$hxres=$EasemobModel->register_hx_group_edit($group_id,$options);
        return $res;
    }
   
	//获取群基本信息
	  public function getbasemsg($groupid) {
        $where = "`groupid`=" . $groupid;
        $res = $this->where($where)->find();
		if($res){
		$res["headimg"]= imgpath($res["headimg"]);
		}
        return $res;
    }
	
	//解散群
	//  思路  1  删除群  2  群成员全部 发信息  3群成员表清空  4群聊天信息全部删除 5环信群解散
	  public function delgroup($groupid,$userid) {
		$where = "`groupid`=" . $groupid." and 	userid=".$userid;  
		$info = $this->where($where)->find();
		
		$group_id=$info["hx_groupid"];
       
        $res = $this->where($where)->delete(); //删群
		$GroupBearingModel=D("GroupBearing");
		$res1 =$GroupBearingModel->delgroup($groupid);  //删人
		
		$EasemobModel=D("Easemob");
		$hxres=$EasemobModel->register_hx_group_destroy($group_id);  //删环信
		
		$ChatWindowModel=D("ChatWindow");
		$res2=$ChatWindowModel->delgrouppost($groupid);    //删除窗体
        return $res;
    }
	
	
	//获取用户已注册的群数
	  public function getGroupNum($userid) {
		$where="userid=".$userid; 
        $res = $this->where($where)->count();
        return $res;
    }
	
	
	//获取加入的群信息
	  public function getmyjoinGroup($userid) {
		$GroupBearingModel=D("GroupBearing");
		$groupidarr=$GroupBearingModel->getGroupidbyuserid($userid);  //群id 数组
		if(count($groupidarr)==0){ 
			$res=array();
		}else{
			$string=implode(",",$groupidarr);
			$where="`groupid` in (".$string.")";
			  $res = $this->where($where)->select();
			  if(!$res){
				  $res=array();
				  }else{
					  for($i=0;$i<count($res);$i++){
						  $res[$i]["headimg"]= imgpath($res[$i]["headimg"]);
					  }
					  
				  }
			}

        return $res;
    }
	
	//判断是否为群主
	  public function checkgrouprole($groupid,$userid) {
		$where = "`groupid`=" . $groupid;  
        $msg = $this->where($where)->find();
		$res=false;
		if($msg["userid"]==$userid){
			$res=true;
			}
        return $res;
    }


}
