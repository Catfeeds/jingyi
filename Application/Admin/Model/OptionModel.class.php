<?php
namespace Admin\Model;
use Think\Model;

class OptionModel extends Model{
	
	public function getlist($adminid){   //获取操作记录
	  $Model=M("option");
	  $where="adminid=".$adminid;
	  $order="addtime desc";
	  $res = $Model->where($where)->order($order)->select(); 
	  return $res;  
    }
	
	public function add($data){   //添加操作记录
	 $Model=M("option");
	 $data["adminid"]=session("admin_key_id");
	 $data["addtime"]=time();
	 $res=$Model->add($data);
	 return $res;  
    }
	


}