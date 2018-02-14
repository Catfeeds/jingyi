<?php

namespace Home\Controller;

use Think\Controller;

class IOfficialController extends CommonController {

   
    /*
     * 获取净一首页信息
	 * @param  $userid 
     */
    public function getIndexmsg() {
		//获取banner信息
		 $BannerModel=D("Banner");
		 $res["bannerlist"]=$BannerModel->getlist();
		
		
		//获取官方贴列表
		$OfficialPostsModel=D("OfficialPosts");
		$where="status=1";
		$order="addtime desc";
		$limit=6;
		$res["officialpostslist"]=$OfficialPostsModel->getPostslist($where,$order,$limit); 
		
		//获取商品信息
		$ProductModel=D("Product"); 
		$pwhere="hotstatus=1 and status=1";
		$porder="hot_addtime desc";
		$plimit=6;
		$res["productmsg"]=$ProductModel->getlist($pwhere,$porder,$plimit);
		
        get_api_result(200,"获取成功",$res);
    }
	
	
	  /*
     * 获取官方贴分页列表信息
	 * @param  $page   //显示页数 1
	 * @param  $limit   //显示条数 10
     */
    public function getOfficialPostslist() {
		$page=I("page")?I("page"):1;
		$limit=I("limit")?I("limit"):10;
		$limit1=($page-1)*$limit.",".$limit;
		
		//获取 贴子信息
		$OfficialPostsModel=D("OfficialPosts");
		$order="addtime desc";
		$res=$OfficialPostsModel->getPostslist($where,$order,$limit1); 
		
        get_api_result(200,"获取成功",$res);
    }
	
	
		
	  /*
     * 获取贴子详情
	 *$postsid  贴子id
     */
    public function getOfficialPostsinfo() {
		$posts_id=I("postsid");
		$userid=I("userid")?I("userid"):"";
		$page=I("page")?I("page"):1;
		$limit=I("limit")?I("limit"):10;
		$limit1=($page-1)*$limit.",".$limit;
		
		//获取 贴子信息
		$OfficialPostsModel=D("OfficialPosts");
		$order="addtime desc";
		$res=$OfficialPostsModel->getpostsIndexbypostsid($posts_id,$userid); 
		
        get_api_result(200,"获取成功",$res);
    }
	
	

	
	 /*
     * 获取贴子评论列表（官方）
	 * @param  $postsid  //贴子id
	 * @param  $page   //请求页数
	 * @param  $limit  //显示条数
     */
    public function getPostsreplylist() {
		$postsid=I("postsid");
		$page=I("page")?I("page"):"";
		$limit=I("limit")?I("limit"):5;
		$where="posts_id=".$postsid;
		$order="addtime desc";
		$limit1=($page-1)*$limit.",".$limit;
		//获取 贴子信息
		$OfficialPostsModel=D("OfficialPosts");
		$res=$OfficialPostsModel->getreplymsgbypostsid($where,$order="",$limit1);
        get_api_result(200,"获取成功",$res);
    }
	
	
	 /*
     * 添加贴子论评（官方）
	 * @param  $postsid  //贴子id
	 * @param  $userid   //用户id
	 * @param  $content  //评论内容
	 * @param  $beuserid  //@的用户id 可不填
     */
    public function addPostsreply() {
		$data["posts_id"]=I("postsid");
		$data["userid"]=I("userid");
		$data["content"]=I("content");
		$data["be_userid"]=I("be_userid");
	
		$OfficialPostsModel=D("OfficialPosts");
		$msg=$OfficialPostsModel->replypost($data);
		if(!$msg){
			 get_api_result(300,"添加失败",$res);
			}
		$res=$OfficialPostsModel->getreplymsgbyreplyid($msg);
        get_api_result(200,"添加成功",$res);
    }
	
	
	  /*
     * 收藏贴子(官方贴)
	 * @param  $userid   //用户id
     * @param  $postsid   //贴子id
     */
    public function addpostscollection() {
		$data["posts_id"]=I("posts_id");
		$data["userid"]=I("userid");
		$data["type"]=1;
		$UserPostsCollectionModel=D("UserPostsCollection"); 
		$ischeck=$UserPostsCollectionModel->checkCollection($data["userid"],$data["posts_id"],1);
		if($ischeck){
			 get_api_result(300,"请勿重复收藏");
			}
		$res=$UserPostsCollectionModel->addpost($data);
		if($res){
			 get_api_result(200,"添加成功",$res);
			}else{
				 get_api_result(300,"添加失败");
				}
       
    }
	
	  /*
     * 取消收藏贴子（官方）
	 * @param  $userid   //用户id
     * @param  $postsid   //贴子id
     */
    public function delpostscollection() {
		$data["posts_id"]=I("postsid");
		$data["userid"]=I("userid");
		$where="posts_id=".$data["posts_id"]." and userid=".$data["userid"]." and type=1" ;
		$UserPostsCollectionModel=D("UserPostsCollection"); 
		$res=$UserPostsCollectionModel->delpost($where);
		if($res){
			 get_api_result(200,"取消成功",$res);
			}else{
				 get_api_result(300,"取消失败");
				}
       
    }
	
	
	  /*
     * 贴子点赞
	 * @param  $userid   //用户id
	 * @param  $postsid   //贴子id
     */
    public function addzanposts() {
		$data["userid"]=I("userid");
		$data["posts_id"]=I("postsid");
		$data["type"]=1;
		
		$PostsLikeModel=D("PostsLike"); 
		
		$ischeck=$PostsLikeModel->checkuseridislike($data["posts_id"],$data["userid"],1);
		if($ischeck){
			 get_api_result(300,"已经点过赞了");
			}
		$res=$PostsLikeModel->addpost($data);
		if(!$res){
			 get_api_result(300,"点赞失败");
			}else{
			 get_api_result(200,"点赞成功",$res);	
				}
		
       
    }
	
	  /*
     * 取消点赞(官方)
	 * @param  $userid   //用户id
	 * @param  $postsid   //贴子id
     */
    public function delzanposts() {
		$userid=I("userid");
		$postsid=I("postsid");
		$PostsLikeModel=D("PostsLike"); 
		$ischeck=$PostsLikeModel->checkuseridislike($postsid,$userid,1);
		if(!$ischeck){
			 get_api_result(300,"请刷新后在操作");
			}
		$res=$PostsLikeModel->delpost($postsid,$userid,1);
		if(!$res){
			 get_api_result(300,"取消失败");
			}else{
			 get_api_result(200,"取消成功",$res);	
				}
    }


}
