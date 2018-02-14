<?php

namespace Home\Controller;

use Think\Controller;

class IUserPlanetController extends CommonController
{

    /**
     * 创建星球
     */
    public function addUserPlanet()
    {

        $data["planet_name"] = shielding(I("planet_name"));
        $data["planet_summary"] = shielding(I("planet_summary"));
        $data["userid"] = I("userid");
        $data["planet_style"] = I("planet_style");
        $data["province_id"] = I("province_id");
        $data["city_id"] = I("city_id");
        $data["city_name"] = shielding(I("city_name"));
        $data["musicfile"] = I("musicfile");
        $data["musicname"] = shielding(I("musicname")) ? shielding(I("musicname")) : "未知";

        $UserPlanetModel = D("UserPlanet");
        $ischeck = $UserPlanetModel->checkUserPlanet($data["userid"]);
        if ($ischeck) {
            $msg = "已有星球，创建失败！";
            get_api_result(300, $msg);
        }
        $res = $UserPlanetModel->addpost($data);
        if (!$res) {
            $msg = "创建失败！";
            get_api_result(300, $msg);
        }
        $planetinfo = $UserPlanetModel->getUserPlanetInfo($data["userid"]);
        $msg = "创建成功！";
        get_api_result(200, $msg, $planetinfo);
    }

    /**
     * 修改星球城市
     */
    public function editUserPlanetcityinfo()
    {
        $planet_id = I("planet_id");
        $data["province_id"] = I("province_id");
        $data["city_id"] = I("city_id");
        $data["city_name"] = shielding(I("city_name"));

        $UserPlanetModel = D("UserPlanet");
        $res = $UserPlanetModel->editPostMsg($planet_id, $data);
        if (!$res) {
            $msg = "修改失败！";
            get_api_result(300, $msg);
        } else {
            $msg = "修改成功！";
            get_api_result(200, $msg);
        }
    }


    /**
     * 修改星球名称
     */
    public function editUserPlanetnameinfo()
    {
        $userid = I("userid");
        $data["planet_name"] = shielding(I("planet_name"));

        $UserPlanetModel = D("UserPlanet");
        $UserPlanetinfo = $UserPlanetModel->getUserPlanetInfobyuserid($userid);  //获取用户星球资料
        if (!$UserPlanetinfo) {
            $msg = "您未拥有星球，请先创建！";
            get_api_result(300, $msg);
            die;
        } else {
            $planet_id = $UserPlanetinfo["planet_id"];

            $res = $UserPlanetModel->editPostMsg($planet_id, $data);
            if (!$res) {
                $msg = "修改失败！";
                get_api_result(300, $msg);
            } else {
                $msg = "修改成功！";
                get_api_result(200, $msg);
            }
        }


    }

    /**
     * 修改星球简介
     */
    public function editUserPlanetsummaryinfo()
    {
        $userid = I("userid");
        $data["planet_summary"] = shielding(I("planet_summary"));

        $UserPlanetModel = D("UserPlanet");
        $UserPlanetinfo = $UserPlanetModel->getUserPlanetInfobyuserid($userid);  //获取用户星球资料
        if (!$UserPlanetinfo) {
            $msg = "您未拥有星球，请先创建！";
            get_api_result(300, $msg);
            die;
        } else {
            $planet_id = $UserPlanetinfo["planet_id"];

            $res = $UserPlanetModel->editPostMsg($planet_id, $data);
            if (!$res) {
                $msg = "修改失败！";
                get_api_result(300, $msg);
            } else {
                $msg = "修改成功！";
                get_api_result(200, $msg);
            }
        }
    }


    /**
     * 获取某用户星球信息
     */
    public function getUserPlanetInfoByuserid()
    {
        $userid = I("userid");
        $UserModel = D("User");
        $res = $UserModel->getusermsgbyuserid($userid);
        $msg = "获取成功！";
        get_api_result(200, $msg, $res);
    }

    /*
     * 我的星球（信息）
     */
    public function myUserPlanetMsg()
    {
        $userid = I("userid");
        $UserModel = D("User");
        $res = $UserModel->getusermsgbyuserid($userid);
        get_api_result(200, "获取成功", $res);
    }

    /**
     * 修改星球旋转那儿的60个字信息
     */
    public function edit_planet_ball()
    {
        $post = I('post.');
        if (!$post['planet_id']) {
            get_api_result(300, '请传入planet_id');
        }
        $res = M('user_planet')->where(['planet_id' => $post['planet_id']])->getField('planet_id');
        if (!$res) {
            get_api_result(300, '找不到该星球');
        }
        if ((!empty($post['mercury']) && (strlen($post['mercury']) > 60)) || (!empty($post['venus']) && (strlen($post['venus']) > 60)) || (!empty($post['earth']) && (strlen($post['earth']) > 60)) || (!empty($post['mars']) && (strlen($post['mars']) > 60)) || (!empty($post['jupiter']) && (strlen($post['jupiter']) > 60))) {
            get_api_result(300, '星球信息最多只能包含60个字符');
        }

        $msg = M('user_planet_msg')->where(['planet_id' => $post['planet_id']])->find();
        $planet_id = $post['planet_id'];
        $result = false;
        if (!$msg) {
            //添加
            $result = M('user_planet_msg')->add($post);
        } else {
            //修改
            unset($post['planet_id']);
            $result = M('user_planet_msg')->where(['planet_id' => $planet_id])->save($post);
        }
        if ($result==false) {
            get_api_result(300, '星球信息修改失败');
        }
        $back = M('user_planet_msg')->where(['planet_id' => $planet_id])->find();
        get_api_result(200, '星球信息修改成功', $back);
    }

    /*
     * 获取所有背景图
     */
    public function getAllBackImg()
    {
        $UserPlanetModel = D("UserPlanet");
        $result = $UserPlanetModel->getAllBackImg();
        get_api_result(200, "获取成功", $result);
    }

    /*
     * 修改背景图
     */
    public function editPostBackImg()
    {

        $userid = I('userid');
        $data['backimg_id'] = I('backimg_id');

        $UserPlanetModel = D("UserPlanet");
        $planetmsg = $UserPlanetModel->getUserPlanetInfo($userid);
        if (empty($planetmsg)) {
            $msg = "修改失败，请先创建星球！";
            get_api_result(300, $msg);
        }
        $planet_id = $planetmsg["planet_id"];
        $res = $UserPlanetModel->editPostMsg($planet_id, $data);

        if ($res) {
            $result = $UserPlanetModel->getUserPlanetInfoByPlanetid($planet_id);
            get_api_result(200, "修改成功", $result);
        } else {
            get_api_result(300, "修改失败");
        }
    }

    /*
    * 修改背景音乐
    */
    public function editPostBackMusic()
    {

        $userid = I('userid');
        $data["musicfile"] = I("musicfile");
        $data["musicname"] = I("musicname") ? I("musicname") : "未知";

        $UserPlanetModel = D("UserPlanet");
        $planetmsg = $UserPlanetModel->getUserPlanetInfo($userid);
        if (empty($planetmsg)) {
            $msg = "修改失败，请先创建星球！";
            get_api_result(300, $msg);
        }
        $planet_id = $planetmsg["planet_id"];
        $res = $UserPlanetModel->editPostMsg($planet_id, $data);

        if ($res) {
            $result = $UserPlanetModel->getUserPlanetInfoByPlanetid($planet_id);
            get_api_result(200, "修改成功", $result);
        } else {
            get_api_result(300, "修改失败");
        }
    }

    /**
     * 发布星球帖子
     */
    public function publishPlanetPosts()
    {
        $data['userid'] = I("userid");
        $data['post_content'] = shielding(I('post_content'));
        $data['cityid'] = I('cityid');
        $data['address'] = shielding(I('address'));
        $data['status'] = I('status');
        $data["type"] = $datafiletype = I('type');
        if ($data["type"] == 2) {
            $img = I("vedioimages");
            $data["vedioimages"] = uplodeImg($img, "posts");
        }

        $datafile = I("uploadurl");

        $PostsModel = D("Posts");

        $res = $PostsModel->addpost($data, $datafile, $datafiletype);

        if ($res) {
            $PostsReportModel = D("PostsReport");
            $PostsReportModel->adduserpostsbefore($data['userid']);
            D('UserLevel')->addUserGrow($data['userid'],5);//成长值
            get_api_result(200, "发布成功", $res);
        } else {
            get_api_result(401, "发布失败");
        }
    }

    /*
     * 关注
     */
    public function userFollow()
    {
        $data['userid'] = I('userid');
        $data['planet_userid'] = I('planet_userid');
        $data['planet_id'] = I('planet_id');
        $UserPlanetModel = D("UserPlanet");
        $row = $UserPlanetModel->judgeIsFollow($data['userid'], $data['planet_userid']);
        if ($row) {
            get_api_result(401, "已关注");
        }
        $res = $UserPlanetModel->addUserFollow($data);
        if ($res) {
            get_api_result(200, "关注成功", $res);
        } else {
            get_api_result(401, "关注失败");
        }
    }

    /*
     * 取消关注
     */
    public function userCancelFollow()
    {
        $data['userid'] = I('userid');
        $data['planet_userid'] = I('planet_userid');
        $UserPlanetModel = D("UserPlanet");
        $row = $UserPlanetModel->judgeIsFollow($data['userid'], $data['planet_userid']);
        if (!$row) {
            get_api_result(401, "已取消关注");
        }
        $res = $UserPlanetModel->deleteUserFollow($data['userid'], $data['planet_userid']);
        if ($res) {
            get_api_result(200, "取消关注成功", $res);
        } else {
            get_api_result(401, "取消关注失败");
        }
    }

    /*
     * 获取全部星球标签
     */
    public function getAllStarSign()
    {
        $model = M('Star_sign');
        $result = $model->select();
        get_api_result(200, "获取成功", $result);
    }

    /**
     * 星级旅行列表
     */
    public function starTrek()
    {
        $userid = I("userid");
        if (!$userid) {
            get_api_result(300, '获取用户失败');
        }
        $age = I('age');
        $sex = I('sex');
        $professionsign_id = I('professionsign_id');
        $hobbyid = I('hobbyid');
        $city_id = I('city_id');
        $where = array();
        $where['user.status'] = 1;
        if (!empty($age)) {
            $where['user.age'] = array("ELT", $age);
        }
        if (!empty($sex)) {
            $where['user.sex'] = $sex;
        }
        if (!empty($professionsign_id)) {
            $where['user.professionsign_id'] = $professionsign_id;
        }
        if (!empty($hobbyid)) {
            $array = explode(',', $hobbyid);
            for ($i = 0; $i < count($array); $i++) {
                $find[$i] = "%#" . $array[$i] . "|%";
            }
            $where['user.hobbyid'] = array('like', $find, 'OR');
        }
        if (!empty($city_id)) {
            $where['user_planet.city_id'] = $city_id;
        }
        $list = M('user')->field('user.userid,user.username,user.headimg,user.sex,user_planet.planet_id')
            ->join('user_planet on user_planet.userid = user.userid')
            ->where($where)->select();


        if (count($list) == 0) {
            $res = array();
        } else {
            $count = count($list);
            if ($count <= 11) {
                $res = $list;
            }
            shuffle($list);
            $res = array_splice($list, 0, 11);
            $UserPlanetFollowModel = D("UserPlanetFollow");
            if (!empty($res)) {
                for ($i = 0; $i < count($res); $i++) {
                    if (!empty($userid)) {
                        $res[$i]["attention_status"] = $UserPlanetFollowModel->checkAttentionByplanetuserid($userid, $res[$i]["userid"]);
                    }

                    $res[$i]["headimg"] = imgpath($res[$i]["headimg"]);
                }
            }
        }
        $search_user_id = [];
        foreach ($res as $item) {
            $search_user_id[] = $item['userid'];
        }
        $old = M('trek_search')->where(['user_id' => $userid])->getField('search_user_id');
        $old_user_id = explode(',', $old);
        $all_user_id = array_merge($old_user_id, $search_user_id);
        $all_user_id = array_unique($all_user_id);
        if (count($all_user_id) > 110) {
            $num = count($all_user_id) - 110;
            for ($i = 0; $i < $num; $i++) {
                unset($all_user_id[$i]);
            }
        }
        $search_user_id = implode(',', $all_user_id);
        $trek_id = M('trek_search')->where(['user_id' => $userid])->getField('trek_id');
        $result = false;
        if ($trek_id) {
            $result = M('trek_search')->where(['trek_id' => $trek_id])->save(['search_user_id' => $search_user_id]);
        } else {
            $result = M('trek_search')->add(['search_user_id' => $search_user_id, 'user_id' => $userid]);
        }

//        if($result){
//            get_api_result(300,'获取失败');
//        }
        get_api_result(200, '获取成功', $res);
    }

    /**
     * 星际旅行搜索历史记录
     */
    public function trek_search()
    {
        $user_id = I('post.user_id');
        $page = I('post.page', 1);
        $limit = I('post.limit', 10);
        $trek = M('trek_search')->where(['user_id' => $user_id])->find();
        if (!$trek) {
            get_api_result(300, '暂无搜索历史记录');
        }
        $search_id = $trek['search_user_id'];
        if (!$search_id) {
            get_api_result(300, '暂无搜索历史记录');
        }
        $search_user_id = explode(',', $search_id);
        $result = M('user')->where(['userid' => ['in', $search_user_id]])->page($page, $limit)->field('userid,username,headimg,sex')->select();
        $UserPlanetFollowModel = D("UserPlanetFollow");
        foreach ($result as &$item) {
            $item['headimg'] = imgpath($item['headimg']);
            $item["attention_status"] = $UserPlanetFollowModel->checkAttentionByplanetuserid($user_id, $item["userid"]);
            $res = M('user_planet')->where(['userid' => $item["userid"]])->field('planet_id,planet_summary')->find();
            $item['planet_id'] = $res['planet_id'];
            $item['planet_summary'] = $res['planet_summary'];
            $item['constellation'] = M('user')->where(['userid' => $user_id])->getField('constellation');
        }

        if (!$result) {
            get_api_result(300, '获取失败');
        }
        get_api_result(200, '获取成功', $result);

    }

    /*
     * 获取我的星球首页信息
	 * @param  $userid 
	 * @param  $page   //显示页数 1
	 * @param  $limit   //显示条数 10
     */
    public function getMyPlanetinfo()
    {
        $userid = I("userid");

        $page = I("page") ? I("page") : 1;
        $limit = I("limit") ? I("limit") : 10;
        $limit1 = ($page - 1) * $limit . "," . $limit;

        //获取星球信息
        $UserPlanetModel = D("UserPlanet");
        $res["planet"] = $UserPlanetModel->getUserPlanetInfo($userid);


        //获取星球等级分值
        $res["planet_lvl"]["lvl1"] = C("planet_lvl1");
        $res["planet_lvl"]["lvl2"] = C("planet_lvl2");
        $res["planet_lvl"]["lvl3"] = C("planet_lvl3");
        $res["planet_lvl"]["lvl4"] = C("planet_lvl4");

        if (C("planet_lvl1") > $res["planet"]["growth_value"]) {
            $res["planet"]["planet_lvl"] = 1;
        } else if (C("planet_lvl1") <= $res["planet"]["growth_value"] and $res["planet"]["growth_value"] < C("planet_lvl2")) {
            $res["planet"]["planet_lvl"] = 1;
        } else if (C("planet_lvl2") <= $res["planet"]["growth_value"] and $res["planet"]["growth_value"] < C("planet_lvl3")) {
            $res["planet"]["planet_lvl"] = 2;
        } else if (C("planet_lvl3") <= $res["planet"]["growth_value"] and $res["planet"]["growth_value"] < C("planet_lvl4")) {
            $res["planet"]["planet_lvl"] = 3;
        } else {
            $res["planet"]["planet_lvl"] = 4;
        }


        //获取个人信息（星球主）
        $UserModel = D("User");
        $res["usermsg"] = $UserModel->getusermsg1byuserid($userid);

        //获取 贴子信息
        $PostsModel = D("Posts");
        $res["postsmsg"] = $PostsModel->getmyplanetposts($userid, $limit1);

        get_api_result(200, "获取成功", $res);
    }


    /*
   * 获取我的星球首页贴子分页列表信息
   * @param  $userid
   * @param  $page   //显示页数 1
   * @param  $limit   //显示条数 10
   */
    public function getMyPlanetinfoPostslist()
    {
        $userid = I("userid");

        $page = I("page") ? I("page") : 1;
        $limit = I("limit") ? I("limit") : 10;
        $limit1 = ($page - 1) * $limit . "," . $limit;

        //获取 贴子信息
        $PostsModel = D("Posts");
        $res["postsmsg"] = $PostsModel->getmyplanetposts($userid, $limit1);

        get_api_result(200, "获取成功", $res);
    }

    /*
   * 获取他人的星球首页信息
   * @param  $userid
   * @param  $otheruserid
   * @param  $page   //显示页数 1
   * @param  $limit   //显示条数 10
   */
    public function getotherPlanetinfo()
    {
        $userid = I("userid");
        $otheruserid = I("otheruserid");

        $UserFriendModel = D("UserFriend");
        $ischecklookplanet = $UserFriendModel->ischecklookplanet($userid, $otheruserid);
        if ($ischecklookplanet) {
            get_api_result(300, "无权访问");
        }
        $page = I("page") ? I("page") : 1;
        $limit = I("limit") ? I("limit") : 10;
        $limit1 = ($page - 1) * $limit . "," . $limit;

        //获取星球信息 访问的星球
        $UserPlanetModel = D("UserPlanet");

        $res["planet"] = $UserPlanetModel->getUserPlanetInfo($otheruserid);
        if(!$res["planet"]){
            get_api_result(300, "访问用户没有星球", $res);
        }

        //获取星球等级分值
        $res["planet_lvl"]["lvl1"] = C("planet_lvl1");
        $res["planet_lvl"]["lvl2"] = C("planet_lvl2");
        $res["planet_lvl"]["lvl3"] = C("planet_lvl3");
        $res["planet_lvl"]["lvl4"] = C("planet_lvl4");
        if (C("planet_lvl1") > $res["planet"]["growth_value"]) {
            $res["planet"]["planet_lvl"] = 1;
        } else if (C("planet_lvl1") <= $res["planet"]["growth_value"] and $res["planet"]["growth_value"] < C("planet_lvl2")) {
            $res["planet"]["planet_lvl"] = 1;
        } else if (C("planet_lvl2") <= $res["planet"]["growth_value"] and $res["planet"]["growth_value"] < C("planet_lvl3")) {
            $res["planet"]["planet_lvl"] = 2;
        } else if (C("planet_lvl3") <= $res["planet"]["growth_value"] and $res["planet"]["growth_value"] < C("planet_lvl4")) {
            $res["planet"]["planet_lvl"] = 3;
        } else {
            $res["planet"]["planet_lvl"] = 4;
        }

        //获取个人信息（星球主）
        $UserModel = D("User");
        $res["usermsg"] = $UserModel->getusermsg1byuserid($otheruserid);

        //是否已关注星球主
        $UserPlanetFollowModel = D("UserPlanetFollow");
        $res["isfollow"] = $UserPlanetFollowModel->checkAttention($userid, $res["planet"]["planet_id"]);

        //获取 贴子信息
        $PostsModel = D("Posts");
        $res["postsmsg"] = $PostsModel->getotherplanetposts($otheruserid, $limit1, $userid);

        $updatereadnum = $UserPlanetModel->editPostreadnum($res["planet"]["planet_id"]); //阅读数增加
        $res["planet"]["readernum"] = $res["planet"]["readernum"] + 1;
        get_api_result(200, "获取成功", $res);
    }

    /*
   * 获取他人的星球首页贴子分页列表信息
    * @param  $userid
   * @param  $otheruserid
   * @param  $page   //显示页数 1
   * @param  $limit   //显示条数 10
   */
    public function getotherPlanetinfoPostslist()
    {
        $userid = I("userid");
        $otheruserid = I("otheruserid");
        $page = I("page") ? I("page") : 1;
        $limit = I("limit") ? I("limit") : 10;
        $limit1 = ($page - 1) * $limit . "," . $limit;

        //获取 贴子信息
        $PostsModel = D("Posts");
        $res["postsmsg"] = $PostsModel->getotherplanetposts($otheruserid, $limit1, $userid);

        get_api_result(200, "获取成功", $res);
    }


    /*
       * 获取推荐人列表
       * @param  $userid
       */
    public function gettuiuserinfo()
    {
        $userid = I("userid");

        //获取星球信息 访问的星球
        $UserPlanetModel = D("UserPlanet");
        $res["planet"] = $UserPlanetModel->getUserPlanetInfo($otheruserid);

        //获取个人信息（星球主）
        $UserModel = D("User");
        $res["usermsg"] = $UserModel->getusermsg1byuserid($otheruserid);

        //是否已关注星球主
        $UserPlanetFollowModel = D("UserPlanetFollow");
        $res["isfollow"] = $UserPlanetFollowModel->checkAttention($userid, $res["planet"]["planet_id"]);

        //获取 贴子信息
        $PostsModel = D("Posts");
        $res["postsmsg"] = $PostsModel->getotherplanetposts($otheruserid);

        get_api_result(200, "获取成功", $res);
    }


    /*
   * 获取贴子详情（星球贴）
   * @param  $userid   //当前用户id
   * @param  $postsid  //贴子id
   */
    public function getPostsInfoByPostsid()
    {
        $userid = I("userid");
        $postsid = I("postsid");

        //获取 贴子信息
        $PostsModel = D("Posts");
        $res = $PostsModel->getpostsIndexbypostsid($postsid, $userid);
        $UserPlanetPostsMessageModel = D("UserPlanetPostsMessage");
        $UserPlanetPostsMessageModel->messageisread($userid, $postsid);
        get_api_result(200, "获取成功", $res);
    }

    /*
   * 获取贴子评论列表（星球贴）
   * @param  $postsid  //贴子id
   * @param  $page   //请求页数
   * @param  $limit  //显示条数
   */
    public function getPostsreplylist()
    {
        $postsid = I("postsid");
        $page = I("page") ? I("page") : "";
        $limit = I("limit") ? I("limit") : 5;
        $where = "posts_id=" . $postsid;
        $order = "addtime desc";
        $limit1 = ($page - 1) * $limit . "," . $limit;
        //获取 贴子信息
        $PostsModel = D("Posts");
        $res = $PostsModel->getreplymsgbypostsid($where, $order = "", $limit1);
        get_api_result(200, "获取成功", $res);
    }

    /*
   * 添加贴子论评（星球贴）
   * @param  $postsid  //贴子id
   * @param  $userid   //用户id
   * @param  $content  //评论内容
   * @param  $beuserid  //@的用户id 可不填
   */
    public function addPostsreply()
    {
        $data["posts_id"] = I("postsid");
        $data["userid"] = I("userid");
        $data["content"] = I("content");
        $data["be_userid"] = I("beuserid");

        $PostsModel = D("Posts");
        $msg = $PostsModel->replypost($data);
        if (!$msg) {
            get_api_result(300, "添加失败", $res);
        }
        $res = $PostsModel->getreplymsgbyreplyid($msg);

        $UserModel = D("User");
        $userinfo = $UserModel->getuserbasemsgbyuserid(I("userid"));
        $PostsModel = D("Posts");
        $postsinfo = $PostsModel->getpostsinfobypostsid(I("postsid"));
        if (I("userid") != $postsinfo["userid"]) {   //自己论评自己的贴子
            $data1["userid"] = I("userid");
            $data1["beuserid"] = $postsinfo["userid"];
            $data1["posts_id"] = I("postsid");
            $data1["message"] = $userinfo["username"] . "(" . substr_replace($userinfo["tel"], '****', 3, 4) . ") 论评了您的贴子。";
            $UserPlanetPostsMessageModel = D("UserPlanetPostsMessage");
            $UserPlanetPostsMessageModel->addpost($data1);
            $JpushmessageModel = D("Jpushmessage");
            $JpushmessageModel->setplanetmsgdate($postsinfo["userid"]);
        }


        if (I("beuserid") != "" || I("beuserid") > 0) {
            $data2["userid"] = I("userid");
            $data2["beuserid"] = I("beuserid");
            $data2["posts_id"] = I("postsid");
            $data2["message"] = $userinfo["username"] . "(" . substr_replace($userinfo["tel"], '****', 3, 4) . ") 在贴子中评论了您。";
            $UserPlanetPostsMessageModel = D("UserPlanetPostsMessage");
            $UserPlanetPostsMessageModel->addpost($data2);

            $JpushmessageModel = D("Jpushmessage");
            $JpushmessageModel->setplanetmsgdate(I("beuserid"));
        }


        get_api_result(200, "添加成功", $res);
    }

    /*
   * 修改转发数量
   * @param  $postsid   //贴子id

   */
    public function editPostsinfo()
    {
        $postsid = I("postsid");
        $PostsModel = D("Posts");
        $res = $PostsModel->editretransmissionnum($postsid);
        get_api_result(200, "添加成功");
    }

    /*
   * 收藏贴子
   * @param  $userid   //用户id
   * @param  $postsid   //贴子id
   */
    public function addpostscollection()
    {
        $data["posts_id"] = I("postsid");
        $data["userid"] = I("userid");
        $data["type"] = 2;

        $PostsModel = D("Posts");
        $ischeckposts = $PostsModel->checkposts($data["posts_id"]);
        if (!$ischeckposts) {
            get_api_result(300, "贴子已删除，请刷新后操作。");
        }

        $UserPostsCollectionModel = D("UserPostsCollection");
        $ischeck = $UserPostsCollectionModel->checkCollection($data["userid"], $data["posts_id"]);
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
   * 取消收藏贴子
   * @param  $userid   //用户id
   * @param  $postsid   //贴子id
   */
    public function delpostscollection()
    {
        $data["posts_id"] = I("postsid");
        $data["userid"] = I("userid");
        $where = "posts_id=" . $data["posts_id"] . " and userid=" . $data["userid"] . " and type=2";
        $UserPostsCollectionModel = D("UserPostsCollection");
        $res = $UserPostsCollectionModel->delpost($where);
        if ($res) {
            get_api_result(200, "取消成功", $res);
        } else {
            get_api_result(300, "取消失败");
        }

    }

    /*
   * 获取用户的推荐列表
   * @param  $userid   //用户id
   */
    public function getusertuilist()
    {
        $userid = I("userid");
        $UserModel = D("User");
        $res = $UserModel->getTuilistByuserid($userid);
        get_api_result(200, "获取成功", $res);

    }


    /*
* 获取用户的推荐列表
* @param  $userid   //用户id
*/
    public function getPTuilistByuserid()
    {
        $userid = I("userid");
        $UserModel = D("User");
        $res = $UserModel->getPTuilistByuserid($userid);
        get_api_result(200, "获取成功", $res);

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
        $data["type"] = 2;

        $PostsLikeModel = D("PostsLike");

        $ischeck = $PostsLikeModel->checkuseridislike($data["posts_id"], $data["userid"]);
        if ($ischeck) {
            get_api_result(300, "已经点过赞了");
        }
        $res = $PostsLikeModel->addpost($data);
        if (!$res) {
            get_api_result(300, "点赞失败");
        } else {
            $UserModel = D("User");
            $userinfo = $UserModel->getuserbasemsgbyuserid(I("userid"));
            $PostsModel = D("Posts");
            $postsinfo = $PostsModel->getpostsinfobypostsid(I("postsid"));
            if (I("userid") != $postsinfo["userid"]) {   //自己点赞自己的贴子
                $data1["userid"] = I("userid");
                $data1["beuserid"] = $postsinfo["userid"];
                $data1["posts_id"] = I("postsid");
                $data1["message"] = $userinfo["username"] . "(" . substr_replace($userinfo["tel"], '****', 3, 4) . ") 点赞了您的贴子。";
                $UserPlanetPostsMessageModel = D("UserPlanetPostsMessage");
                $UserPlanetPostsMessageModel->addpost($data1);
                $JpushmessageModel = D("Jpushmessage");
                $JpushmessageModel->setplanetmsgdate($postsinfo["userid"]);
            }

            get_api_result(200, "点赞成功", $res);
        }


    }

    /*
   * 取消点赞
   * @param  $userid   //用户id
   * @param  $postsid   //贴子id
   */
    public function delzanposts()
    {
        $userid = I("userid");
        $postsid = I("postsid");
        $PostsLikeModel = D("PostsLike");
        $ischeck = $PostsLikeModel->checkuseridislike($postsid, $userid);
        if (!$ischeck) {
            get_api_result(300, "请刷新后在操作");
        }
        $res = $PostsLikeModel->delpost($postsid, $userid);
        if (!$res) {
            get_api_result(300, "取消失败");
        } else {
            $UserModel = D("User");
            $userinfo = $UserModel->getuserbasemsgbyuserid(I("userid"));
            $PostsModel = D("Posts");
            $postsinfo = $PostsModel->getpostsinfobypostsid(I("postsid"));
            if (I("userid") != $postsinfo["userid"]) {   //自己取消点赞自己的贴子
                $data1["userid"] = I("userid");
                $data1["beuserid"] = $postsinfo["userid"];
                $data1["posts_id"] = I("postsid");
                $data1["message"] = $userinfo["username"] . "(" . substr_replace($userinfo["tel"], '****', 3, 4) . ") 取消了贴子点赞。";
                $UserPlanetPostsMessageModel = D("UserPlanetPostsMessage");
                $UserPlanetPostsMessageModel->addpost($data1);
                $JpushmessageModel = D("Jpushmessage");
                $JpushmessageModel->setplanetmsgdate($postsinfo["userid"]);
            }


            get_api_result(200, "取消成功", $res);
        }
    }

    /*
   * 删除星球贴
   */
    public function delUserPlanetPosts()
    {

        $userid = I('userid');
        $postsid = I("postsid");
        $PostsModel = D("Posts");
        $info = $PostsModel->getpostsinfobypostsid($postsid);
        if (!$info) {
            get_api_result(300, "非法操作");
        }
        if ($info["userid"] != $userid) {  //不是发布者 没权删除
            get_api_result(300, "非法操作");
        }
        $res = $PostsModel->delpost($postsid);
        if ($res) {
            $UserPlanetPostsMessageModel = D("UserPlanetPostsMessage");
            $UserPlanetPostsMessageModel->delpost($postsid);
            get_api_result(200, "删除成功", $res);
        } else {
            get_api_result(300, "删除失败");
        }
    }

    /*
     * 删除星球贴评论
     */
    public function delUserPlanetPostscomment()
    {

        $userid = I('userid');
        $commentid = I("commentid");
        $PostsModel = D("Posts");
        $info = $PostsModel->getreplymsgbyreplyid($commentid);
        if (!$info) {
            get_api_result(300, "非法操作");
        }
        if ($info["userid"] != $userid) {  //不是发布者 没权删除
            get_api_result(300, "非法操作");
        }
        $res = $PostsModel->delpostcomment($commentid, $userid);
        if ($res) {
            $UserModel = D("User");
            $userinfo = $UserModel->getuserbasemsgbyuserid(I("userid"));
            $PostsModel = D("Posts");
            $postsinfo = $PostsModel->getpostsinfobypostsid($info["posts_id"]);
            if (I("userid") != $postsinfo["userid"]) {   //自己论评自己的贴子
                $data1["userid"] = I("userid");
                $data1["beuserid"] = $postsinfo["userid"];
                $data1["posts_id"] = $info["posts_id"];
                $data1["message"] = $userinfo["username"] . "(" . substr_replace($userinfo["tel"], '****', 3, 4) . ") 删除贴子评论。";
                $UserPlanetPostsMessageModel = D("UserPlanetPostsMessage");
                $UserPlanetPostsMessageModel->addpost($data1);
                $JpushmessageModel = D("Jpushmessage");
                $JpushmessageModel->setplanetmsgdate($postsinfo["userid"]);
            }

            if ($info["be_userid"] > 0) {
                $data1["userid"] = I("userid");
                $data1["beuserid"] = $info["be_userid"];
                $data1["posts_id"] = $info["posts_id"];
                $data1["message"] = $userinfo["username"] . "(" . substr_replace($userinfo["tel"], '****', 3, 4) . ") 删除贴子评论。";
                $UserPlanetPostsMessageModel = D("UserPlanetPostsMessage");
                $UserPlanetPostsMessageModel->addpost($data1);
                $JpushmessageModel = D("Jpushmessage");
                $JpushmessageModel->setplanetmsgdate($info["be_userid"]);
            }

            get_api_result(200, "删除成功", $res);
        } else {
            get_api_result(300, "删除失败");
        }
    }


    /*
     * 获取星球贴评论、点赞消息红点
     */
    public function getredprint()
    {
        $userid = I('userid');
        $UserPlanetPostsMessageModel = D("UserPlanetPostsMessage");
        $res["status"] = $UserPlanetPostsMessageModel->checkUserPlanet($userid);
        get_api_result(200, "获取成功", $res);

    }

    /*
     * 获取星球贴评论、点赞消息条数
     */
    public function getmessagecount()
    {
        $userid = I('userid');
        $UserPlanetPostsMessageModel = D("UserPlanetPostsMessage");
        $res["count"] = $UserPlanetPostsMessageModel->getmessagecount($userid);
        get_api_result(200, "获取成功", $res);
    }

    /*
     * 获取星球贴评论、点赞消息列表
     */
    public function getmessagelist()
    {
        $userid = I('userid');
        $UserPlanetPostsMessageModel = D("UserPlanetPostsMessage");
        $res = $UserPlanetPostsMessageModel->getmessagelist($userid);
        get_api_result(200, "获取成功", $res);

    }


}
