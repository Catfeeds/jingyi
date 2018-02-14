<?php
/**
 * Created by PhpStorm.
 * User: 丿灬小疯
 * Date: 2017/6/1
 * Time: 10:57
 */

namespace Common\Model;
use Think\Model;

class UserFriendModel extends Model{
    protected $tableName = 'user_friend';
	
	
	
    /**
     * 添加好友
     */
    public function addUserFriend($data){
        $Model = M("user_friend");
        $data['agree_status'] = 0;
        $data['bu_u_status'] = 0;
        $data["addtime"] = time();
        $res = $Model->add($data);
        return $res;
    }
	
	 /**
     * 修改好友关系信息
     */
    public function editpost($where,$data){
        $res = $this->where($where)->save($data);
		if($res===0){
			$res=true;
			}
        return $res;
    }
	
	 /**
     * 删除好友关系信息
     */
    public function delpost($userid,$beuserid){
		$where="(userid= ".$userid." and user_friend_id=".$beuserid.") or (userid= ".$beuserid." and user_friend_id=".$userid.")";
        $res = $this->where($where)->delete();
        return $res;
    }
	
	 /**
     * 判断是否为已提出申请好友
     */
    public function ischeckaddfriend($userid,$beuserid){
       $where="(userid= ".$userid." and user_friend_id=".$beuserid.") or (userid= ".$beuserid." and user_friend_id=".$userid.")";
        $res = $this->where($where)->find();
        return $res;
    }
	
	 /**
     * 判断是否为好友
     */
    public function ischeckgoodfriend($userid,$beuserid){
       $where="(userid= ".$userid." and user_friend_id=".$beuserid." and agree_status=1) or (userid= ".$beuserid." and user_friend_id=".$userid." and agree_status=1)";
        $res = $this->where($where)->find();
        return $res;
    }
	
	 /**
     * 判断是否能访问他人星球
     */
    public function ischecklookplanet($userid,$beuserid){
       $where="(userid= ".$userid." and user_friend_id=".$beuserid." and agree_status=1 and bu_u_status=1) or (userid= ".$beuserid." and user_friend_id=".$userid." and agree_status=1 and u_bu_status=1)";
        $res = $this->where($where)->find();
        return $res;
    }


     /**
     * 添加好友
     */
    public function addfriend($userid,$beuserid,$u_bu_status){
      
        $ischeck = $this->ischeckaddfriend($userid,$beuserid);
		if(!$ischeck){
			 $data['userid'] =$userid;//用户id
       		 $data['user_friend_id'] = $beuserid;//被添加用户id
       		 $data['u_bu_status'] = $u_bu_status;//被添加用户是否可以查看用户星球  0|不屏蔽 1|屏蔽
			 $res=$this->addUserFriend($data);
			}
		if($ischeck){
			if($ischeck["agree_status"]==1){  //已经是好友
				$res=true;
			}else{
				if($ischeck["userid"]==$userid){  // 重新发送
					$where="userid= ".$userid." and user_friend_id=".$beuserid."";
					$data1["u_bu_status"]=$u_bu_status;
					$data1["addtime"]=time();
					$res=$this-> editpost($where,$data1);
				}else{   //对面用户添加好友
					$where="userid= ".$beuserid." and user_friend_id=".$userid."";
					$data1["bu_u_status"]=$u_bu_status;
					$data1["addtime"]=time();
					$res=$this-> editpost($where,$data1);	
				}
				
			}
		}
        return $res;
    }
    
	

    /**
     * 获取好友列表
     */
    public function getUserFriendList($userid,$keywords=""){
     
        //此用户添加其他用户,添加好友成功了状态为2
		$field="(case when userid = ".$userid." THEN user_friend_id  when user_friend_id = ".$userid." THEN  userid END ) as friendid";
        $where = "(userid=".$userid." or user_friend_id=".$userid.") and agree_status=1";
        if($keywords){
            $where1= "user.username like '%".$keywords."%' or user.tel like '%".$keywords."%'";
        }
       $subQuery=$this->field($field)->where($where)->select( false);
	   
	   $Model = M();
	   $field1="user.userid,user.username,user.autograph,user.headimg,user.sex,user.level,user.tel";
	   $join="LEFT  JOIN user ON user.userid = a.friendid";
	   $res=$Model->table("(".$subQuery.") a")->field($field1)->join($join)->where($where1)->select() ;
	   if(!$res){$res=array();}
	   if($res){
		   for($i=0;$i<count($res);$i++){
		  		$res[$i]["headimg"]= imgpath($res[$i]["headimg"]);
		   }
		}
	 
	  
        return $res;
    }

    /**
     * 是否愿意添加好友
     *
     * 拒绝添加好友  未做推送
     */
    public function addUserFriendAgree($user_id,$user_friend_id,$agree){
        if ($agree == 1){   //1|同意
            $where = "userid=".$user_friend_id." and user_friend_id=".$user_id;
            $data['agree_status'] = 1;
	    	$res = $this-> editpost($where,$data);   //好友信息做更新
				
			if($res){
				 $info=$this->where($where)->find();
				  $res1 = $this->addPlanetFollow($user_id,$user_friend_id,$info["bu_u_status"]);  //关注
				  $res2 = $this->addPlanetFollow($user_friend_id,$user_id,$info["u_bu_status"]);  //关注
				  
				  $SystemChatModel=D("SystemChat");
				  $UserModel=D("User");
				  $fromusermsg=$UserModel->getuserbasemsgbyuserid($user_id);
				  $data2["system_chat_type"]=1;
				  $data2["to_userid"]= $user_friend_id;
				  $data2["content"]=$fromusermsg["username"]."(".$fromusermsg["tel"].")已成为你的好友，快去联系他吧！";
				  $res3=$SystemChatModel->addpost($data2);  //系统消息
				  
				  $ChatWindowModel = D('ChatWindow');   //消息窗口

				  $data1['to_userid'] =  "[". $user_friend_id."]";   //接收者ID
				  $data1['type'] = 3;                           // 1好友 2群 3|系统
				  $data1['organize_id']=$res3;   //系统消息id
				  $data1['content'] =$fromusermsg["username"]."(".$fromusermsg["tel"].")已成为你的好友，快去联系他吧！"; //内容
				  $res1 = $ChatWindowModel->addmsgpost($type=3,$data1);
				  
				  
				  
				  $data["from_id"]=$user_id;
				  $data["to_id"]=$user_friend_id;
	              $data["type"]=$type=1;  // 1文字  2 图片  3音频
				  $data["content"]="我们已成为好友，快开始聊天吧！";
				  
				  $ChatModel=D("Chat");
		          $res=$ChatModel->agreeaddfriendpost($data);
				  
				  $str=$user_friend_id;
		 		  $JpushmessageModel=D("Jpushmessage");
		 		  $JpushmessageModel-> setxxmsgdate($str);
				  
				  $str1=$user_id;
		 		  $JpushmessageModel-> setxxmsgdate($str1);
				  
				}
				
				
				
           

        }else if($agree == 2){  //2|拒绝
            $res = $this-> delpost($user_id,$user_friend_id);
			
			$SystemChatModel=D("SystemChat");
			$UserModel=D("User");
		    $fromusermsg=$UserModel->getuserbasemsgbyuserid($user_id);
			$data2["system_chat_type"]=1;
			$data2["to_userid"]= $user_friend_id;
			$data2["content"]=$fromusermsg["username"]."(".$fromusermsg["tel"].")拒绝了您的好友申请！";
			$res3=$SystemChatModel->addpost($data2);  //系统消息
			
			
			$ChatWindowModel = D('ChatWindow');   //消息窗口

			$data1['to_userid'] =  "[". $user_friend_id."]";   //接收者ID
			$data1['type'] = 3;                           // 1好友 2群 3|系统
			$data1['organize_id']=$res3;   //系统消息id
			$data1['content'] =$fromusermsg["username"]."(".$fromusermsg["tel"].")拒绝了您的好友申请！"; //内容
			$res1 = $ChatWindowModel->addmsgpost($type=3,$data1);
			
			 $str=$user_friend_id.",". $user_id;
			$JpushmessageModel=D("Jpushmessage");
			$JpushmessageModel-> setxxmsgdate($str);
  		}
		
		if ($res){
                return true;
            }else{
                return false;
            }
    }
	
	 /**
     * 修改好友关系信息
     */
    public function editplanetpost($userid,$beuserid,$status){
		$info=$this->ischeckgoodfriend($userid,$beuserid);
		if($info["userid"]==$userid){
			$data["u_bu_status"]=$status;
			}else{
				$data["bu_u_status"]=$status;
				}
		$where="friendid=".$info["friendid"];
        $res = $this->editpost($where,$data);
		$UserPlanetFollowModel=D("UserPlanetFollow");
		 $ischeck=$UserPlanetFollowModel->checkAttentionByplanetuserid($beuserid,$userid);  //查看他是否关注了我
		 if($ischeck){
			 $where1="userid=".$beuserid." and planet_userid=" .$userid;
			 $data1["status"]=$status;
			 $res1=$UserPlanetFollowModel->where($where1)->save($data1);
			 }
        return $res;
    }
	
	/**
     * 用户互相关注
     */
	public function addPlanetFollow($user_id,$user_friend_id,$u_bu_status){
		$UserPlanetModel=D("UserPlanet");
		$ischeck=$UserPlanetModel->checkUserPlanet($user_id);
		$ischeck1=$UserPlanetModel->checkUserPlanet($user_friend_id);
		if(!($ischeck&&$ischeck1)){
			 return true;
			}
		$UserPlanetFollowModel=D("UserPlanetFollow");
        $isAttention = $UserPlanetFollowModel->checkAttentionByplanetuserid($user_id,$user_friend_id);
		
		if($isAttention){
			$where="userid=".$user_id." and planet_userid=".$user_friend_id;
            $data['status'] = $u_bu_status;                                  //0|不屏蔽 1|屏蔽
            $result = $UserPlanetFollowModel->editpost($where,$data);
		}else{
			
		    $userplanetmsg=$UserPlanetModel->getUserPlanetInfobyuserid($user_friend_id);
			
				$data['userid'] = $user_id; 
				$data['planet_userid'] = $user_friend_id;                       //被添加者id
          	    $data['planet_id'] =$userplanetmsg["planet_id"];                //添加者的星球id
           	    $data['status'] = $u_bu_status;                                  //0|不屏蔽 1|屏蔽
            	$result = $UserPlanetFollowModel->addpost($data);
			
			}
        return $result;
    }
}