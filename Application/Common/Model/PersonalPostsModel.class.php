<?php
namespace Common\Model;
use Think\Model;

/**个人贴子**/
class PersonalPostsModel extends Model{
	  protected $tableName = 'personal_posts'; 
	  
	  
	  
	/*
	*添加贴子
	*@param  $data  贴子数据
	*@param  $datafile  贴子图片、音频、视频数据
	*@param  $datafiletype  1|图片 3|音频 2|视频数据
	*/
     public function addpost($data,$datafile,$datafiletype){
		  $Model=M();
		  $Model->startTrans();
		  $data["addtime"]=time();
		  $res1=$Model->table("personal_posts")->add($data);
		  $ischeck=true;
		  if(!empty($datafile)){
			   for($i=0;$i<count($datafile);$i++){
				   if(!empty($datafile[$i])){
				   $mydatafile[$i]["posts_id"]=$res1;
				   $mydatafile[$i]["addtime"]= $data["addtime"];
				   $mydatafile[$i]["type"]= $datafiletype;
				   $mydatafile[$i]["uploadurl"]= $datafile[$i];
				   $res2[$i]=$Model->table("personal_posts_file")->add($mydatafile[$i]);
				   if(!$res2[$i]){
					   $ischeck=false;
					   }
					}
				 }
		 }
		 
		 if($res1&& $ischeck){
			  $Model->commit();
			   return  $res1;
			 }else{
				  $Model->rollback();
				return  false;
				 }
		
		
	}
	
	
	/*
	*删除个人贴子
	*@param  $postsid  贴子id
	*/
     public function delpost($postsid){
		  $Model1=M("personal_posts");
		  $where1="posts_id=".$postsid;
		  $data["posts_status"]=2;
		  $res1=$Model1->where($where1)->save($data);   //删除贴子     
		  
		  $UserPostsCollectionModel=D("UserPostsCollection");
		  $where="posts_id=".$postsid." and type=3";
		  $res= $UserPostsCollectionModel->delpost($where); //收藏此贴的人都不在收藏了                    
		  return $res1;
		
	}
	
	/*
	*判断贴子是否存在
	*@param  $postsid  贴子id  
	*/
     public function checkposts($postsid){
		  $Model=M("personal_posts");
		  $where="posts_id=".$postsid." and posts_status=1";
		  $msg=$Model->where($where)->find();
		  $res=false;
		    if($msg){
				$res=true;
				}                              
		  return $res;
		
	}
	
	/*
	*回复个人贴子
	*@param  $data   回复内容
	*/
     public function replypost($data){
		  $Model=M("personal_posts_comment");
		  $data["addtime"]=time();
		  $res=$Model->add($data);                                
		  return $res;
		
	}
	
	
	/*
	*获取贴子回复信息列表
	*/
     public function getreplymsgbypostsid($where,$order="",$limit){
		  $Model=M("personal_posts_comment");
		  if(empty($order)){$order="addtime desc";}
		  if(empty($limit)){$limit=5;}
		  $res=$Model->where($where)->order($order)->limit($limit)->select();  
		  if(count($res)==0){
			  $res=array();
			  }else{
				$UserModel=D("User");  
				for($i=0;$i<count($res);$i++){
					
					$res[$i]["usermsg"]=$UserModel->getusermsg1byuserid($res[$i]["userid"]);  //获取发帖回复人信息 
					if($res[$i]["be_userid"]>0){
						$res[$i]["beusermsg"]=$UserModel->getusermsg1byuserid($res[$i]["be_userid"]);  //获取@的人信息 
					}else{
						$res[$i]["beusermsg"]=(object)array();
						}
					
					}
				
			  }               
		  return $res;
		
	}
	
	/*
	*获取贴子列表
	*/
     public function getPostslist($where,$order="",$limit=""){
		  $Model=M("personal_posts");
		  $res=$Model->where($where)->order($order)->limit($limit)->select();  
		  if(count($res)==0){
			  $res=array();
			  } else{
				  $UserModel=D("User");  
				   for($i=0;$i<count($res);$i++){  //获取封面图
				   $res[$i]["usermsg"]=$UserModel->getusermsg1byuserid($res[$i]["userid"]);  //获取发帖人信息 
				   $res[$i]["images"]= imgpath($res[$i]["images"]);
				   }  
			 }           
		  return $res;
		
	}
	
	/*
	*通过贴子回复id获取贴子回复信息
	*/
     public function getreplymsgbyreplyid($replyid){
		  $Model=M("personal_posts_comment");
		 $where="comment_id=".$replyid;
		  $res=$Model->where($where)->find();  
		  if(count($res)==0){
			  $res=array();
			  }else{
				$UserModel=D("User");  
					$res["usermsg"]=$UserModel->getusermsg1byuserid($res["userid"]);  //获取发帖回复人信息 
					if($res["be_userid"]>0){
						$res["beusermsg"]=$UserModel->getusermsg1byuserid($res["be_userid"]);  //获取@的人信息 
					}else{
						$res["beusermsg"]=(object)array();
						}

			  }               
		  return $res;
		
	}
	
	/*
	*通过贴子id获取贴子回复数量
	*@param  $postsid   贴子id
	*/
     public function getreplycountbypostsid($postsid){
		  $Model=M("personal_posts_comment");
		  $where="posts_id=".$postsid;
		  $res=$Model->where($where)->count();                         
		  return $res;
		
	}
	
	/*
	*通过贴子id获取贴子内容
	*@param  $postsid   贴子id
	*/
     public function getpostsinfobypostsid($postsid){
		  $Model=M("personal_posts");
		  $where="posts_id=".$postsid;
		  $res=$Model->where($where)->find();
		  $res["vedioimages"]=imgpath($res["vedioimages"]);
		  $res["filemsg"]=$this->getpostsfilesinfobypostsid($postsid);                        
		  return $res;
		
	}
	
	/*
	*通过贴子id获取贴子上传信息
	*@param  $postsid   贴子id
	*/
     public function getpostsfilesinfobypostsid($postsid){
		  $Model=M("personal_posts_file");
		  $where="posts_id=".$postsid;
		  $res=$Model->where($where)->select();
		  if($res){
			   for($i=0;$i<count($res);$i++){
				   
				   $res[$i]["uploadurl"]= imgpath($res[$i]["uploadurl"]);
				   }  
			  }
		 
		  if(!$res){$res=array();}                       
		  return $res;
		
	}
	
	/*
	*通过贴子id串获取全部贴子信息
	*@param  $idlist   贴子id 数组
	*/
     public function getpostslistbyidlist($idlist,$limit,$userid){
		 if(count($idlist)!=0){
			  $Model=M("personal_posts");
			  $where["posts_id"]=array('in',$idlist);
			  $order="addtime desc";
			  $res=$Model->where($where)->order($order)->limit($limit)->select();  
			  $UserModel=D("User");
			  $UserPostsCollectionModel=D("UserPostsCollection");
			   $PostsLikeModel=D("PostsLike");
			   
			  if($res){
				   for($i=0;$i<count($res);$i++){
					   $res[$i]["vedioimages"]=imgpath($res[$i]["vedioimages"]);
					  $res[$i]["postsfilemsg"]= $this->getpostsfilesinfobypostsid($res[$i]["posts_id"]);  //获取贴子评论人数 
		    		  $res[$i]["usermsg"]=$UserModel->getusermsg1byuserid($res[$i]["userid"]);  //获取发帖人信息
					  $res[$i]["postscollectionnum"]= $UserPostsCollectionModel->getCollectioncountbyid($res[$i]["posts_id"],3);  //获取贴子收藏数  
					  $res[$i]["postszannum"]=  $PostsLikeModel->getlikenum($res[$i]["posts_id"],3);  //获取贴子点赞数  
					  $res[$i]["postsreplynum"]= $this->getreplycountbypostsid($res[$i]["posts_id"]);  //获取贴子评论人数
					  if(!empty($userid)){
						 $res[$i]["zanstatus"]=$PostsLikeModel->checkuseridislike($res[$i]["posts_id"],$userid,3);  //是否点赞  true 是  false  否 
						  } else{
							  $res[$i]["zanstatus"]=false;
							  }
				   }
			 }
			  
			 }else{
				 $res=array();
				 }
		                             
		  return $res;
	}
	
	
	
	
	/*
	*通过贴子id获取星球贴子详细页面信息
	*@param  userid  用户id
	*@param  postsid  贴子id
	*/
     public function getpostsIndexbypostsid($postsid,$userid=""){
		$Model=M("personal_posts");
		$where="posts_id=".$postsid;
		$res=$Model->where($where)->find();  
		$UserModel=D("User");
		$PostsLikeModel=D("PostsLike");
		$UserPostsCollectionModel=D("UserPostsCollection");
		$PersonalPraiseModel=D("PersonalPraise");
		if($res){
			$res["vedioimages"]=imgpath($res["vedioimages"]);
			$res["postsfilemsg"]= $this->getpostsfilesinfobypostsid($postsid);  //获取贴子上传（图片等）信息 
			$res["usermsg"]=$UserModel->getusermsgbyuserid($res["userid"]);  //获取发帖人信息
			$res["postscollectionnum"]= $UserPostsCollectionModel->getCollectioncountbyid($postsid,3);  //获取贴子收藏数  
			$res["postszannum"]=  $PostsLikeModel->getlikenum($postsid,3);  //获取贴子赞  
			$res["postsreplynum"]= $this->getreplycountbypostsid($postsid);  //获取贴子评论人数
			$res["praisenum"]= $PersonalPraiseModel->getPraise($postsid);  //获取贴子好评度
			
			
			$replywhere="posts_id=".$postsid;
			$replyorder="addtime desc";
			$replylimit=5;
			
			$res["replymsg"]=$this->getreplymsgbypostsid($replywhere,$replyorder,$replylimit);  //贴子回复信息
			
			if(!empty($userid)){
				$UserPostsCollectionModel=D("UserPostsCollection");
				$res["collectionstatus"]=$UserPostsCollectionModel->checkCollection($userid,$res["posts_id"],3);  //是否收藏了此贴  true 是  false  否
				$res["zanstatus"]=$PostsLikeModel->checkuseridislike($postsid,$userid,3);  //是否点赞  true 是  false  否
				$res["praisestatus"]= $PersonalPraiseModel->ischeckPraise($postsid,$userid);  //用户是否已好评
				}else{
					$res["collectionstatus"]=false;
					$res["zanstatus"]=false;
					$res["praisestatus"]= false;  //用户是否已好评
					}
			
		}
		
		 return $res;
	}
	
	
	
	/*
	*通过贴子id获取收藏列表展示贴子信息
	*@param  userid  用户id
	*@param  postsid  贴子id
	*/
     public function getCollectinfo($postsid,$userid=""){
		$Model=M("personal_posts");
		$where="posts_id=".$postsid;
		$res=$Model->where($where)->find();  
		$UserModel=D("User");
		$PostsLikeModel=D("PostsLike");
		$UserPostsCollectionModel=D("UserPostsCollection");
		$PersonalPraiseModel=D("PersonalPraise");
		if($res){
			$res["vedioimages"]=imgpath($res["vedioimages"]);
			$res["postsfilemsg"]= $this->getpostsfilesinfobypostsid($postsid);  //获取贴子上传（图片等）信息 
			$res["usermsg"]=$UserModel->getusermsgbyuserid($res["userid"]);  //获取发帖人信息
			$res["postscollectionnum"]= $UserPostsCollectionModel->getCollectioncountbyid($postsid,3);  //获取贴子收藏数  
			$res["postszannum"]=  $PostsLikeModel->getlikenum($postsid,3);  //获取贴子赞  
			$res["postsreplynum"]= $this->getreplycountbypostsid($postsid);  //获取贴子评论人数
			$res["praisenum"]= $PersonalPraiseModel->getPraise($postsid);  //获取贴子好评度
			if(!empty($userid)){
				$UserPostsCollectionModel=D("UserPostsCollection");
				$res["collectionstatus"]=$UserPostsCollectionModel->checkCollection($userid,$res["posts_id"],3);  //是否收藏了此贴  true 是  false  否
				$res["zanstatus"]=$PostsLikeModel->checkuseridislike($postsid,$userid,3);  //是否点赞  true 是  false  否
				$res["praisestatus"]= $PersonalPraiseModel->ischeckPraise($postsid,$userid);  //用户是否已好评
				}else{
					$res["collectionstatus"]=false;
					$res["zanstatus"]=false;
					$res["praisestatus"]= false;  //用户是否已好评
					}
			
		}
		
		 return $res;
	}
	
	
	
	
}