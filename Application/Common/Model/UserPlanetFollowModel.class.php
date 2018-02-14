<?php
namespace Common\Model;
use Think\Model;

/**用户星球关注信息**/
class UserPlanetFollowModel extends Model{
	  protected $tableName = 'user_planet_follow'; 
	  
	  
	  
	 /*
	*添加关注
	*@param  $data  数据
	*/
     public function addpost($data){
		 $Model=M("user_planet_follow");
		 $data["addtime"]=time();
		 $res= $Model->add($data);
		return  $res;
	}
	
	/*
	*取消关注
	*@param  $where  数据
	*/
     public function delpost($where){
		 $Model=M("user_planet_follow");
		 $res= $Model->where($where)->delete();
		return  $res;
	}
	
	/*
	*判断是否关注了星球
	*@param  userid  关注者id
	*@param  planetid  星球id
	*/
     public function checkAttention($userid,$planetid){
		 $res=false;
		 $Model=M("user_planet_follow");
		 $where="userid=".$userid." and planet_id=".$planetid;
		 $msg= $Model->where($where)->find();
		 if($msg){
			 $res=true;
			 }
		return  $res;
	}
	
	/*
	*通过星球主用户id判断是否关注了星球
	*@param  userid  关注者id
	*@param  planet_userid  星球主id
	*/
     public function checkAttentionByplanetuserid($userid,$planet_userid){
		 $res=false;
		 $Model=M("user_planet_follow");
		 $where="userid=".$userid." and planet_userid=".$planet_userid;
		 $msg= $Model->where($where)->find();
		 if($msg){
			 $res=true;
			 }
		return  $res;
	}
	  
	
	/*
	*获取列表
	*@param  $where  条件
	*/
     public function getlist($where){
		 $Model=M("user_planet_follow");
		 $res= $Model->where($where)->select();
		return  $res;
	}
	
	/*
	*获取用户的全部关注信息列表
	*@param  $userid  用户id
	*/
     public function getuserAttentionlist($userid){
		 $Model=M("user_planet_follow");
		 $field="a.planet_userid as userid,b.headimg,b.username,b.user_hx,b.user_hx_id,c.planet_name,c.planet_id";
		 $where="a.userid=".$userid;
		 $join="user as b ON a.planet_userid= b.userid";
		 $join1="user_planet as c ON a.planet_userid =c.userid";
		 $res= $Model->alias('a')->field($field)->where($where)->join($join)->join($join1)->select();
		 if($res){
			 for($i=0;$i<count($res);$i++){
				 $res[$i]["headimg"]=imgpath($res[$i]["headimg"]);
				 }
			 }
		return  $res;
	}
	
	
	/*
	*获取用户的粉丝信息列表
	*@param  $userid  用户id
	*/
     public function getuserFanslist($userid){
		 $Model=M("user_planet_follow");
		 $field="a.userid ,a.status,b.headimg,b.username,b.user_hx,b.user_hx_id,c.planet_name";
		 $where="a.planet_userid=".$userid;
		 $join="user as b ON a.userid= b.userid";
		 $join1="user_planet as c ON a.userid =c.userid";
		 $res= $Model->alias('a')->field($field)->where($where)->join($join)->join($join1)->select();
		 if($res){
			 for($i=0;$i<count($res);$i++){
				 $res[$i]["headimg"]=imgpath($res[$i]["headimg"]);
				 }
			 }
		return  $res;
	}
	
	
	/*
	*通过用户id获取全部被关注人的用户id
	*@param  $userid
	*/
     public function getfollowuseridbyuserid($userid){
		$Model=M("user_planet_follow");
		$where="userid=".$userid." and status=0";  //没有被关注的人屏蔽
		$res= $Model->where($where)->getField('planet_userid',true);
		if(!$res){$res=array();}
		return  $res;
	}
	
	
	/*
	*获取用户的粉丝数量
	*@param  $userid  用户id
	*/
     public function getuserFansnum($userid){
		 $Model=M("user_planet_follow");
		 $where="planet_userid=".$userid;
		 $res= $Model->where($where)->count();
		return  $res;
	}
	
	/*
	*获取用户的关注数量
	*@param  $userid  用户id
	*/
     public function getuserAttentionnum($userid){
		 $Model=M("user_planet_follow");
		 $where="userid=".$userid;
		 $res= $Model->where($where)->count();
		return  $res;
	}
	
	/*
	*修改关注信息
	*/
     public function editpost($where,$data){
		 $Model=M("user_planet_follow");
		 $res= $Model->where($where)->save($data);
		 if($res===0){
			 $res=true;
			 }
		return  $res;
	}

	
}