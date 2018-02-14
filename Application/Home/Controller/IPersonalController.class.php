<?php

namespace Home\Controller;

use Think\Controller;

class IPersonalController extends CommonController
{


    /**
     *  获取个人贴首页信息
     */
    public function getPersonalPostsIndexlist()
    {
        //获取banner信息
        $BannerModel = D("Banner");
        $res["bannerlist"] = $BannerModel->getlist();

        $page = 1;
        $limit = 10;
        $limit1 = ($page - 1) * $limit . "," . $limit;

        //获取 贴子信息
        $PersonalPostsModel = D("PersonalPosts");
        $order = "addtime desc";
        $where = "posts_status=1";
        $res["personalpostslist"] = $PersonalPostsModel->getPostslist($where, $order, $limit1);
        get_api_result(200, "获取成功", $res);
    }

    /**
     * 发布个人帖子
     */
    public function publishPersonalPosts()
    {
        $data['userid'] = I("userid");
        $data["images"] = uplodeImg(I("images"), "personalposts");
        $data['posts_title'] = shielding(I('posts_title'));
        $data['post_content'] = shielding(I('post_content'));
        $data["type"] = $datafiletype = I('type');
        if ($data["type"] == 2) {
            $img = I("vedioimages");
            $data["vedioimages"] = uplodeImg($img, "posts");
        }

        $datafile = I("uploadurl");

        $PersonalPostsModel = D("PersonalPosts");

        $res = $PersonalPostsModel->addpost($data, $datafile, $datafiletype);

        if ($res) {
            $PostsReportModel = D("PostsReport");
            $PostsReportModel->adduserpostsbefore($data['userid']);  //成长值计算
            D('UserLevel')->addUserGrow($data['userid'],5);//成长值
            get_api_result(200, "发布成功", $res);
        } else {
            get_api_result(401, "发布失败");
        }
    }

    /*
   * 获取个人贴分页列表信息
   * @param  $page   //显示页数 1
   * @param  $limit   //显示条数 10
   */
    public function getPersonalPostslist()
    {
        $page = I("page") ? I("page") : 1;
        $limit = I("limit") ? I("limit") : 10;
        $limit1 = ($page - 1) * $limit . "," . $limit;

        //获取 贴子信息
        $PersonalPostsModel = D("PersonalPosts");
        $order = "addtime desc";
        $where = "posts_status=1";
        $res = $PersonalPostsModel->getPostslist($where, $order, $limit1);

        get_api_result(200, "获取成功", $res);
    }


    /*
   * 获取贴子详情
   *$postsid  贴子id
   */
    public function getOfficialPostsinfo()
    {
        $posts_id = I("postsid");
        $userid = I("userid") ? I("userid") : "";
        $page = I("page") ? I("page") : 1;
        $limit = I("limit") ? I("limit") : 5;
        $limit1 = ($page - 1) * $limit . "," . $limit;

        //获取 贴子信息
        $PersonalPostsModel = D("PersonalPosts");
        $order = "addtime desc";
        $res = $PersonalPostsModel->getpostsIndexbypostsid($posts_id, $userid);

        get_api_result(200, "获取成功", $res);
    }


    /*
    * 获取贴子评论列表（个人贴）
    * @param  $postsid  //贴子id
    * @param  $page   //请求页数
    * @param  $limit  //显示条数
    */
    public function getPostsreplylist()
    {
        $postsid = I("postsid");
        $page = I("page") ? I("page") : 1;
        $limit = I("limit") ? I("limit") : 5;
        $where = "posts_id=" . $postsid;
        $order = "addtime desc";
        $limit1 = ($page - 1) * $limit . "," . $limit;
        //获取 贴子信息
        $PersonalPostsModel = D("PersonalPosts");
        $res = $PersonalPostsModel->getreplymsgbypostsid($where, $order = "", $limit1);
        get_api_result(200, "获取成功", $res);
    }


    /*
    * 添加贴子论评（个人）
    * @param  $postsid  //贴子id
    * @param  $userid   //用户id
    * @param  $content  //评论内容
    * @param  $beuserid  //@的用户id 可不填
    */
    public function addPostsreply()
    {
        $data["posts_id"] = I("postsid");
        $data["userid"] = I("userid");
        $data["content"] = shielding(I("content"));
        $data["be_userid"] = I("be_userid");

        $PersonalPostsModel = D("PersonalPosts");
        $msg = $PersonalPostsModel->replypost($data);
        if (!$msg) {
            get_api_result(300, "添加失败");
        }
        $res = $PersonalPostsModel->getreplymsgbyreplyid($msg);
        get_api_result(200, "添加成功", $res);
    }


    /*
   * 收藏贴子(个人贴)
   * @param  $userid   //用户id
   * @param  $postsid   //贴子id
   */
    public function addpostscollection()
    {
        $data["posts_id"] = I("posts_id");
        $data["userid"] = I("userid");
        $data["type"] = 3;

        $PersonalPostsModel = D("PersonalPosts");
        $ischeckposts = $PersonalPostsModel->checkposts($data["posts_id"]);
        if (!$ischeckposts) {
            get_api_result(300, "贴子已删除，请刷新后操作。");
        }
        $UserPostsCollectionModel = D("UserPostsCollection");

        $ischeck = $UserPostsCollectionModel->checkCollection($data["userid"], $data["posts_id"], 3);
        if ($ischeck) {
            get_api_result(300, "请勿重复收藏");
        }
        $res = $UserPostsCollectionModel->addpost($data);
        if ($res) {
            get_api_result(200, "添加成功", $res);
        } else {
            get_api_result(300, "添加失败");
        }

    }

    /*
   * 取消收藏贴子（个人贴）
   * @param  $userid   //用户id
   * @param  $postsid   //贴子id
   */
    public function delpostscollection()
    {
        $data["posts_id"] = I("postsid");
        $data["userid"] = I("userid");
        $where = "posts_id=" . $data["posts_id"] . " and userid=" . $data["userid"] . " and type=3";
        $UserPostsCollectionModel = D("UserPostsCollection");
        $res = $UserPostsCollectionModel->delpost($where);
        if ($res) {
            get_api_result(200, "取消成功", $res);
        } else {
            get_api_result(300, "取消失败");
        }

    }


    /*
   * 贴子点赞
   * @param  $userid   //用户id
   * @param  $postsid   //贴子id
   */
    public function addzanposts()
    {
        $data["userid"] = I("userid");
        $data["posts_id"] = I("postsid");
        $data["type"] = 3;

        $PostsLikeModel = D("PostsLike");

        $ischeck = $PostsLikeModel->checkuseridislike($data["posts_id"], $data["userid"], 3);
        if ($ischeck) {
            get_api_result(300, "已经点过赞了");
        }
        $res = $PostsLikeModel->addpost($data);
        if (!$res) {
            get_api_result(300, "点赞失败");
        } else {
            get_api_result(200, "点赞成功", $res);
        }


    }

    /*
   * 取消点赞(个人)
   * @param  $userid   //用户id
   * @param  $postsid   //贴子id
   */
    public function delzanposts()
    {
        $userid = I("userid");
        $postsid = I("postsid");
        $PostsLikeModel = D("PostsLike");
        $ischeck = $PostsLikeModel->checkuseridislike($postsid, $userid, 3);
        if (!$ischeck) {
            get_api_result(300, "请刷新后在操作");
        }
        $res = $PostsLikeModel->delpost($postsid, $userid, 3);
        if (!$res) {
            get_api_result(300, "取消失败");
        } else {
            get_api_result(200, "取消成功", $res);
        }
    }

    /*
     * 添加贴子好评
	 * @param  $userid   //用户id
	 * @param  $postsid   //贴子id
	 * @param  $praise   //评价值
	 
     */
    public function addpraise()
    {
        $data["userid"] = I("userid");
        $data["posts_id"] = I("postsid");
        $data["praise"] = I("praise");

        $PersonalPraiseModel = D("PersonalPraise");

        $ischeck = $PersonalPraiseModel->ischeckPraise($data["posts_id"], $data["userid"]);
        if ($ischeck) {
            get_api_result(300, "已经评价过了");
        }
        $res = $PersonalPraiseModel->addpost($data);
        if (!$res) {
            get_api_result(300, "评价失败");
        } else {
            $PersonalPostsModel = D("PersonalPosts");
            $postsmsg = $PersonalPostsModel->getpostsinfobypostsid($data["posts_id"]);
            $PostsReportModel = D("PostsReport");
            $PostsReportModel->addPraisebefore($data["userid"], $data["posts_id"], 1, $data["praise"], $postsmsg["userid"]);

            $msg["praisenum"] = $PersonalPraiseModel->getPraise($data["posts_id"]);
            get_api_result(200, "评价成功", $msg);
        }


    }

}
