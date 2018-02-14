<?php
namespace Common\Model;
use Think\Model;

/**每日发帖纪录**/
class PostsReportModel extends Model{
	  protected $tableName = 'posts_report'; 
	  
	  
	  
	/*
	*添加每日发帖信息
	*@param  $data  贴子数据
	*/
     public function addpost($userid){
		 $Model=M("posts_report");
		 $data["userid"]=$userid;
		 $data["addtime"]=time();
		 $res= $Model->add($data);
		return  $res;
	}
	
	/*
	*判断是否存在本日发帖
	*@param  $userid
	*/
     public function ischeck($userid){
		 $Model=M("posts_report");
		 $where="userid=".$userid." and addtime>=".strtotime(date('Y-m-d'));
		 $msg= $Model->where($where)->find();
		 $res=false;
		 if($msg){
			 $res=true;
			 }
		return  $res;
	}
	
	
	/*
	*用户发帖成功后  
	* 1 判断是否是本天第一次发帖
	* 2 是第一次 则记录信息
	* 3 是第一次 用户增加成长经验
	*@param  $userid
	*/
     public function adduserpostsbefore($userid){
		$ischeck=$this->ischeck($userid);
		if($ischeck){   //已经今天发布过了  就不再增加经验了
			return true; 
			}
		 
		 $res1= $this-> addpost($userid);  //增加发帖的记录
		 
		 $UserLevelModel=D("UserLevel");
		 $data["msg"]="今日首次发帖，成长值+".C("frist_day_posts_growth");
		 $data["userid"]=$userid;
		 $data["sroce"]=C("frist_day_posts_growth");
		 $res2= $UserLevelModel-> addpost($data);  //增加用户成长记录
		 
		 $UserModel=D("User");
		 $usermsg= $UserModel->getusermsgbyuserid($userid); //获取用户信息
		 
		 $data1["now_growth_value"]=$usermsg["now_growth_value"]+C("frist_day_posts_growth");
		 $data1["level"]= $UserLevelModel->userlevel($data1["now_growth_value"]);
		 $res3=$UserModel->editpostbyuserid($userid, $data1) ;   //更新用户等级信息
		 
		 $UserPlanetModel=D("UserPlanet");   
		 $data2["growth_value"]= $usermsg["userplanetinfo"]["growth_value"]+C("frist_day_posts_growth");
		 $res4= $UserPlanetModel->editPostMsg($usermsg["userplanetinfo"]["planet_id"],$data2);  //更新星球成长值
		 	
		return  true;
	}
	
	
	
	/*
	*  用户好评后星球成长
	*@param  $userid   评价者id
	*@param  $postsid  评价的贴子id  或者 官方活动id 或者群活动id
	*@param  $type     类型  1|个人贴好评度  2| 官方活动好评度  3| 群活动好评度
	*@param  $praise   评价  1|好评 2|中评 3|差评 
	*@param  $beuserid     被评价用户id 
	*/
     public function addPraisebefore($userid,$postsid,$type,$praise,$beuserid){

		 $UserLevelModel=D("UserLevel");
		 if($praise==1){
			 $score=C("good_praise");
			  $praisemsg="好评";
			  
			 }else if($praise==2){
				 $score=C("commonly_praise");
				   $praisemsg="中评";
				 }else{
					$score=C("bad_praise"); 
					  $praisemsg="差评";
					 }
		 
		if($type==1){
			$postsname="用户个人贴";
		}else if($type==2){
			$postsname="官方活动";
		}else{
			$postsname="群活动";	 
		}
		
		$data["msg"]= $postsname."，id为：“".$postsid."”,获得用户id为“".$userid."”的评价,评价为“".$praisemsg."”，成长值 ";
		if($score>=0){
			$data["msg"].="+";
			}
	   $data["msg"].=$score; 
	   $data["userid"]=$beuserid;
	   $data["sroce"]=$score;
	   $res2= $UserLevelModel-> addpost($data);  //增加用户成长记录
	   
	   $UserModel=D("User");
	   $usermsg= $UserModel->getusermsgbyuserid($beuserid); //获取用户信息
	   
	   $data1["now_growth_value"]=$usermsg["now_growth_value"]+$score;
	   $data1["level"]= $UserLevelModel->userlevel($data1["now_growth_value"]);
	   $res3=$UserModel->editpostbyuserid($beuserid, $data1) ;   //更新用户等级信息
	   
	   $UserPlanetModel=D("UserPlanet");   
	   $data2["growth_value"]= $usermsg["userplanetinfo"]["growth_value"]+$score;
	   $res4= $UserPlanetModel->editPostMsg($usermsg["userplanetinfo"]["planet_id"],$data2);  //更新星球成长值
		 	
		return  true;
	}
	

	
}