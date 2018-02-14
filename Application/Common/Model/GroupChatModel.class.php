<?php
/**
 * Created by PhpStorm.
 * User: 丿灬小疯
 * Date: 2017/6/1
 * Time: 16:03
 */

namespace Common\Model;


use Think\Model;
//群聊天
class GroupChatModel extends Model
{
    protected $tableName = "group_chat";
	
	/**
     * 判断上一条消息是否10分钟以前发的 
     */
    public function ischeckmsgtime($groupid){
        $where = 'group_id= '.$groupid;
        $chat = $this->where($where)->order('addtime DESC')->find();
		$res=0;
		if (time() > $chat['addtime'] + 600) {
            $res=1;
        }
        return $res;
    }
	
	
    /**
     * 添加消息 (群消息)
     */
    public function addpost($data){
		$data['time_show']=$this->ischeckmsgtime($data["group_id"]); 
        $data['addtime'] = time();          //更新时间
		$data['is_read'] = "[".$data["from_id"]."]";          //已读
		
        $char_id1 = $this->add($data);   //消息增加
		
		$GroupBearingModel=D("GroupBearing");
		$useridarray=$GroupBearingModel->getuseridlistbyGroupid($data["group_id"]) ;
		
		
		$ChatWindowModel=D("ChatWindow");
		$option["to_userid"]=$this->useridarrTostr($useridarray);
		$option["group_id"]=$data["group_id"];
		$option["type"]=2;
		$option["content"]=$data["content"];
		$option["is_reads"]=$data['is_read'];
		$addwindows=$ChatWindowModel->addmsgpost($type=2,$option);
		
	   $GroupModel=D("Group");
	   $groupinfo=$GroupModel->getbasemsg($data["group_id"]);
	   $EasemobModel=D("Easemob");
	   	   
	   $from="jingyi".$data["from_id"];
	   $target_type="chatgroups";
	   $target[]=$groupinfo["hx_groupid"];
	   $content=$data["content"];
	   $ext["title"]=$groupinfo["name"];
	   $ext["content"]=$data["content"];
	   $ext["type"]=2;
	   $ext["chatmsg"]=$this->getmychatbychatid($char_id1);
	   $hxres=$EasemobModel->sendText($from,$target_type,$target,$content,$ext);
		
        return $char_id1 ;
    }
	
	
	/**
     * 用户id数组转字符串
     */
    public function useridarrTostr($useridarr){
		if(count($useridarr)==0){
			$res="";
		}else{	
		for($i=0;$i<count($useridarr);$i++){
			$res.="[".$useridarr[$i]."]";
			}
		}
        return $res ;
    }
	
	/**
     * 获取我的群消息
     */
    public function getmyGroupchat($userid,$groupid,$limit){
		$where="group_id=".$groupid." and  is_del not like '%[".$userid."]%'";
		$order="addtime desc";
		$res=$this->where($where)->order($order)->limit($limit)->select();
		if(!$res){ $res=array();}
		if($res){
			$UserModel=D("User");
			for($i=0;$i<count($res);$i++){
				    $userifo=$UserModel->getuserbasemsgbyuserid($res[$i]["from_id"]);
					$res[$i]["username"]=$userifo["username"];
			        $res[$i]["headimg"]=$userifo["headimg"];
					$res[$i]["url"]= imgpath($res[$i]["url"]);
					unset($userifo);
				}
			}
        return $res ;
    }
	
	/**
     * 删除消息
     */
    public function delchat($userid,$chatid){
		$where="id=".$chatid;
		$info=$this->where($where)->find();
		$data["is_del"]=$info["is_del"]."[".$userid."]";
		$res=$this->where($where)->save($data);
        return $res ;
    }
	
   /**
     * 获取未读数量
     */
    public function getNoReadnum($userid,$groupid){
		$where="group_id=".$groupid." and  is_read not like '%[".$userid."]%' ";
		$res=$this->where($where)->count();
        return $res ;
    }
	
		
	/**
     * 根据id获取聊天信息
     */
    public function getmychatbychatid($chatid){
		$where="id=".$chatid;
		$field="*,id as chatid";
		$res=$this->field($field)->where($where)->find();
		if(!$res){ $res=array();}
		if($res){
			$UserModel=D("User");
		    $userifo=$UserModel->getuserbasemsgbyuserid($res["from_id"]);
			$res["username"]=$userifo["username"];
			$res["headimg"]=$userifo["headimg"];
			$res["url"]= imgpath($res["url"]);
				
			}
        return $res ;
    }
 
    /**
     *  消息已读
     */
    public function changRead($userid,$groupid){
		$where="group_id=".$groupid." and  is_read not like '%[".$userid."]%' ";
		$res=$this->field($field)->where($where)->select();
		for($i=0;$i<count($res);$i++){
			$data["is_read"]=$res[$i]["is_read"]."[".$userid."]";
			$where1="id=".$res[$i]["id"];
			$res1=$this->where($where1)->save($data);
			}
		$ChatWindowModel=D("ChatWindow");
		$where2="to_userid like '%[".$userid."]%' and group_id=".$groupid." and type=2 and is_reads not like '%".$userid."%'";
		$info=$ChatWindowModel->getchatwindowsinfo($where2);
		$data2["is_reads"]=$info["is_reads"]."[".$userid."]";
		$res2=$ChatWindowModel->editpost($where2,$data2);
		
        return $res1 ;
    }
	
	/**
     * 获取我的群消息
     */
    public function getmychat($userid,$groupid,$small_chatid){
		$where="group_id=".$groupid;
		if($small_chatid!=""){
			$where.=" and id <".$small_chatid;
			}
		$field="*,id as chatid";
		$order="addtime desc";
		$limit=10;
		$res=$this->field($field)->where($where)->order($order)->limit($limit)->select();
		if(!$res){ $res=array();}
		if($res){
			$UserModel=D("User");
			for($i=0;$i<count($res);$i++){
				    $userifo=$UserModel->getuserbasemsgbyuserid($res[$i]["from_id"]);
					$res[$i]["username"]=$userifo["username"];
			        $res[$i]["headimg"]=$userifo["headimg"];
					$res[$i]["url"]= imgpath($res[$i]["url"]);
					unset($userifo);
				}
			}
        return $res ;
    }

}