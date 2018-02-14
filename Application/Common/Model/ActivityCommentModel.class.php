<?php
namespace Common\Model;
use Think\Model;
//群活动评价表
class ActivityCommentModel extends Model{
	
	 
    /*
    * 添加评价
    */
    public function addpost($data) {  
		$data["addtime"]=time();
        $res = $this->add($data);
        return $res;
    }
	
		/*
	*获取活动贴子评论信息列表
	*/
     public function getreplymsgbypostsid($activity_id,$order="",$limit,$smallid){
		 $where="activity_id=".$activity_id;
		  if(empty($order)){$order="addtime desc";}
		  if(empty($limit)){$limit=5;}
		  if(!empty($smallid)){ $where.=" and activity_comment_id<".$smallid;}
		
		  $res=$this->where($where)->order($order)->limit($limit)->select();  
		  if(count($res)==0){
			  $res=array();
			  }else{
				$UserModel=D("User");  
				for($i=0;$i<count($res);$i++){
					$res[$i]["usermsg"]=$UserModel->getuserbasemsgbyuserid($res[$i]["userid"]);  //获取发帖回复人信息 
					if($res[$i]["be_userid"]>0){
						$res[$i]["beusermsg"]=$UserModel->getuserbasemsgbyuserid($res[$i]["be_userid"]);  //获取@的人信息 
					}else{
						$res[$i]["beusermsg"]=(object)array();
						}
					
					}
				
			  }               
		  return $res;
		
	}
	
	
  	/*
	*通过贴子回复id获取贴子回复信息
	*/
     public function getreplymsgbyreplyid($replyid){
		 $where="activity_comment_id=".$replyid;
		  $res=$this->where($where)->find();  
		  if(count($res)==0){
			  $res=array();
			  }else{
				$UserModel=D("User");  
					$res["usermsg"]=$UserModel->getuserbasemsgbyuserid($res["userid"]);  //获取发帖回复人信息 
					if($res["be_userid"]>0){
						$res["beusermsg"]=$UserModel->getuserbasemsgbyuserid($res["be_userid"]);  //获取@的人信息 
					}else{
						$res["beusermsg"]=(object)array();
						}

			  }               
		  return $res;
		
	}
}