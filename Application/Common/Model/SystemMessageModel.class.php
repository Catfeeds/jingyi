<?php
/**
 * Created by PhpStorm.
 * User: 丿灬小疯
 * Date: 2017/6/1
 * Time: 16:03
 */

namespace Common\Model;
use Think\Model;

class SystemMessageModel extends Model
{	
	
    /**
     * 添加信息
     */
    public function addpost($data){
        $data['addtime'] = time();          //更新时间
        $res = $this->add($data);
        return $res;
    }
	
	 /**
     * 修改信息
     */
    public function editpost($where,$data){
        $res = $this->where($where)->save($data);
        return $res;
    }
	
	 /**
     * 获取用户未读的系统消息数量
     */
    public function getNoReadnum($userid){
		$where="to_userid like '%[".$userid."]%' and is_del not like '%[".$userid."]%' and is_read not like '%[".$userid."]%'";
        $res = $this->where($where)->count();
        return $res;
    }
	
	 /**
     * 获取用户全部系统消息列表
     */
    public function getsystemmsglist($userid,$limit){
		$where="to_userid like '%[".$userid."]%' and is_del  not like '%[".$userid."]%'";
		$order="addtime desc";
        $res = $this->where($where)->order($order)->limit($limit)->select();
		if(!$res){
			$res=array();
			}
        return $res;
    }
 
   /**
     * 删除系统信息
     */
    public function delpost($system_chat_id,$userid){
	    $where="system_chat_id=".$system_chat_id;
		$info = $this->where($where)->find();
		$data["is_del"]=$info["is_del"]."[".$userid."]";
        $res = $this->where($where)->save($data);
		$lastchat=$this->getlastchat($userid);
		$ChatWindowModel=D("ChatWindow");
		$where1="type=4";
		if(!$lastchat){  //没找到系统消息 
			 $data1["is_dels"]="[".$userid."]" ;   //隐藏消息窗体
			 $ChatWindowModel-> editpost($where1,$data1);
			}
		
        return $res;
    }
	
	  /**
     * 已读系统信息
     */
    public function isreadpost($system_chat_id,$userid){
	    $where="system_chat_id=".$system_chat_id;
		$info = $this->where($where)->find();
		$data["is_read"]=$info["is_read"]."[".$userid."]";
        $res = $this->where($where)->save($data);
		$nownum=$this->getNoReadnum($userid);
		if($nownum==0){    //当前未读系统消息是否存在   不存在 窗口就全部已读
			$ChatWindowModel=D("ChatWindow");
			$where2="type=4";
			$info1=$ChatWindowModel->getchatwindowsinfo($where2);
			$data2["is_reads"]=$info1["is_reads"]."[".$userid."]";
			$res2=$ChatWindowModel->editpost($where2,$data2);
		}
		
		if($res===0){ $res=true;}
        return $res;
    }
	
	 /**
     * 获取最新的一条未删除的系统消息内容
     */
    public function getlastchat($userid){
	     $where="to_userid like '%[".$userid."]%'  and is_del  not like  '%[".$userid."]%'";
		$order="addtime desc";
        $res = $this->where($where)->order($order)->find();
        return $res;
    }

}