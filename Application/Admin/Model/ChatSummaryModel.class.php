<?php
namespace Admin\Model;
use Think\Model;

class ChatSummaryModel extends Model{
	
	
	public function add($data){   //添加消息
	 $Model=M("chat_summary");
	 $data["time"]=time();
	 $data["is_read"]=0;
	 $res=$Model->add($data);
	 return $res;  
    }
	
	public function edit($data,$where){   //修改
	 $Model=M("chat_summary");
	 $data["time"]=time();
	 $data["is_read"]=0;
	 $res=$Model->where($where)->save($data);
	 return $res;  
    }
	
	public function messageadd($data){   //信息写入  
	if($data["type"]==1){
		$where="to_id=".$data["to_id"]." and type=".$data["type"];
		}
	 $check=$this->checkmsg($where);
	 if($check){
		 $res=$this->edit($data,$where);
		 }else{
			 $res=$this->add($data); 
			 }
	 return $res;  
    }
	
	public function checkmsg($where){
	    $Model=M("chat_summary");
		$res=$Model->where($where)->find();
		return $res;
		}
	


}