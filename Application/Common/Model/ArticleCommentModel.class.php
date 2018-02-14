<?php

namespace Common\Model;

use Think\Model;

/**星球贴子信息**/
class ArticleCommentModel extends Model
{
    protected $tableName = 'user_planet_posts';


    /*
    *添加个人贴子
    *@param  $data  贴子数据
    *@param  $datafile  贴子图片、音频、视频数据
    *@param  $datafiletype  1|图片 3|音频 2|视频数据
    */
    public function addpost($data, $datafile, $datafiletype)
    {
        $Model = M();

        $Model->startTrans();
        $data["addtime"] = time();
        $res1 = $Model->table("user_planet_posts")->add($data);
        $ischeck = true;
        if (!empty($datafile)) {
            for ($i = 0; $i < count($datafile); $i++) {
                $mydatafile[$i]["posts_id"] = $res1;
                $mydatafile[$i]["addtime"] = $data["addtime"];
                $mydatafile[$i]["type"] = $datafiletype;
                $mydatafile[$i]["uploadurl"] = $datafile[$i];
                $res2[$i] = $Model->table("posts_upload")->add($mydatafile[$i]);
                if (!$res2[$i]) {
                    $ischeck = false;
                }
            }
        }

        if ($res1 && $ischeck) {
            $Model->commit();

            $UserFriendModel = D('UserFriend');
            $friendlist = $UserFriendModel->getUserFriendList($data["userid"]);   //好朋友

            if (count($friendlist) != 0) {
                $str = array();
                $UserPlanetPostsMessageModel = D("UserPlanetPostsMessage");
                for ($i = 0; $i < count($friendlist); $i++) {

                    $str[$i] = $friendlist[$i]["userid"];


                    $data1["userid"] = $data["userid"];
                    $data1["beuserid"] = $friendlist[$i]["userid"];
                    $data1["posts_id"] = $res1;
                    $data1["message"] = "您的好友发布了新的星球贴，快去查看吧！";

                    $UserPlanetPostsMessageModel->addpost($data1);
                }

                $JpushmessageModel = D("Jpushmessage");
                $JpushmessageModel->setplanetmsgdate($str);
            }


            return $res1;
        } else {
            $Model->rollback();
            return false;
        }


    }


    /*
    *删除个人贴子
    *@param  $postsid  贴子id
    */
    public function delpost($postsid)
    {
        $Model1 = M("user_planet_posts");
        $where1 = "posts_id=" . $postsid;
        $data["posts_status"] = 2;
        $res1 = $Model1->where($where1)->save($data);   //删除贴子

        $UserPostsCollectionModel = D("UserPostsCollection");
        $where = "posts_id=" . $postsid . " and type=2";
        $res = $UserPostsCollectionModel->delpost($where); //收藏此贴的人都不在收藏了
        return $res1;

    }

    /*
    *判断贴子是否存在
    *@param  $postsid  贴子id
    */
    public function checkposts($postsid)
    {
        $Model = M("user_planet_posts");
        $where = "posts_id=" . $postsid . " and posts_status=1";
        $msg = $Model->where($where)->find();
        $res = false;
        if ($msg) {
            $res = true;
        }
        return $res;

    }

    /*
    *回复个人贴子
    *@param  $data   回复内容
    */
    public function replypost($data)
    {
        $Model = M("user_planet_posts_comment");
        $data["addtime"] = time();
        $res = $Model->add($data);
        return $res;

    }


    /*
    *删除个人贴子评论
    *@param  $postsid  贴子id
    */
    public function delpostcomment($posts_comment_id, $userid)
    {
        $Model1 = M("user_planet_posts_comment");
        $where1 = "comment_id=" . $posts_comment_id . " and userid=" . $userid;
        $res1 = $Model1->where($where1)->delete();   //删除贴子

        return $res1;

    }

    /*
    *获取贴子回复信息列表
    */
    public function getreplymsgbypostsid($where, $order = "", $limit)
    {
        $Model = M("user_planet_posts_comment");
        if (empty($order)) {
            $order = "addtime desc";
        }
        if (empty($limit)) {
            $limit = 5;
        }
        $res = $Model->where($where)->order($order)->limit($limit)->select();
        if (count($res) == 0) {
            $res = array();
        } else {
            $UserModel = D("User");
            for ($i = 0; $i < count($res); $i++) {

                $res[$i]["usermsg"] = $UserModel->getusermsg1byuserid($res[$i]["userid"]);  //获取发帖回复人信息
                if ($res[$i]["be_userid"] > 0) {
                    $res[$i]["beusermsg"] = $UserModel->getusermsg1byuserid($res[$i]["be_userid"]);  //获取@的人信息
                } else {
                    $res[$i]["beusermsg"] = (object)array();
                }

            }

        }
        return $res;

    }


    /*
    *通过贴子回复id获取贴子回复信息
    */
    public function getreplymsgbyreplyid($replyid)
    {
        $Model = M("user_planet_posts_comment");
        $where = "comment_id=" . $replyid;
        $res = $Model->where($where)->find();
        if (count($res) == 0) {
            $res = array();
        } else {
            $UserModel = D("User");
            $res["usermsg"] = $UserModel->getusermsg1byuserid($res["userid"]);  //获取发帖回复人信息
            if ($res["be_userid"] > 0) {
                $res["beusermsg"] = $UserModel->getusermsg1byuserid($res["be_userid"]);  //获取@的人信息
            } else {
                $res["beusermsg"] = (object)array();
            }

        }
        return $res;

    }

    /*
    *通过贴子id获取贴子回复数量
    *@param  $postsid   贴子id
    */
    public function getreplycountbypostsid($postsid)
    {
        $Model = M("user_planet_posts_comment");
        $where = "posts_id=" . $postsid;
        $res = $Model->where($where)->count();
        return $res;

    }

    /*
    *通过贴子id获取贴子内容
    *@param  $postsid   贴子id
    */
    public function getpostsinfobypostsid($postsid)
    {
        $Model = M("user_planet_posts");
        $where = "posts_id=" . $postsid;
        $res = $Model->where($where)->find();
        $res["vedioimages"] = imgpath($res["vedioimages"]);
        $res["filemsg"] = $this->getpostsfilesinfobypostsid($postsid);
        return $res;

    }

    /*
    *通过贴子id获取贴子上传信息
    *@param  $postsid   贴子id
    */
    public function getpostsfilesinfobypostsid($postsid)
    {
        $Model = M("posts_upload");
        $where = "posts_id=" . $postsid;
        $res = $Model->where($where)->select();
        if ($res) {
            for ($i = 0; $i < count($res); $i++) {

                $res[$i]["uploadurl"] = imgpath($res[$i]["uploadurl"]);
            }
        }

        if (!$res) {
            $res = array();
        }
        return $res;

    }

    /*
    *通过贴子id串获取全部贴子信息
    *@param  $idlist   贴子id 数组
    */
    public function getpostslistbyidlist($idlist, $limit, $userid)
    {
        if (count($idlist) != 0) {
            $Model = M("user_planet_posts");
            $where["posts_id"] = array('in', $idlist);
            $order = "addtime desc";
            $res = $Model->where($where)->order($order)->limit($limit)->select();
            $UserModel = D("User");
            $UserPostsCollectionModel = D("UserPostsCollection");
            $PostsLikeModel = D("PostsLike");

            if ($res) {
                for ($i = 0; $i < count($res); $i++) {
                    $res[$i]["vedioimages"] = imgpath($res[$i]["vedioimages"]);
                    $res[$i]["postsfilemsg"] = $this->getpostsfilesinfobypostsid($res[$i]["posts_id"]);  //获取贴子评论人数
                    $res[$i]["usermsg"] = $UserModel->getusermsg1byuserid($res[$i]["userid"]);  //获取发帖人信息
                    $res[$i]["postscollectionnum"] = $UserPostsCollectionModel->getCollectioncountbyid($res[$i]["posts_id"]);  //获取贴子收藏数
                    $res[$i]["postszannum"] = $PostsLikeModel->getlikenum($res[$i]["posts_id"]);  //获取贴子点赞数

                    //获取点赞的用户id
                    $planet_user=M("user_planet_posts_like")
                        ->join('user on user.userid=user_planet_posts_like.userid')
                        ->where(['user_planet_posts_like.posts_id'=>$res[$i]["posts_id"],'user_planet_posts_like.type'=>2])->field('user.username')->select();
                    $user_name=[];
                    foreach ($planet_user as $item){
                        $user_name[]=$item['username'];
                    }
                    $res[$i]["posts_username"] = $user_name;  //获取贴子点赞人名

                    $res[$i]["postsreplynum"] = $this->getreplycountbypostsid($res[$i]["posts_id"]);  //获取贴子评论人数
                    if (!empty($userid)) {
                        $res[$i]["zanstatus"] = $PostsLikeModel->checkuseridislike($res[$i]["posts_id"], $userid);  //是否点赞  true 是  false  否
                    } else {
                        $res[$i]["zanstatus"] = false;
                    }
                }
            }

        } else {
            $res = array();
        }

        return $res;
    }


    /*
    *通过我的关注人id筛选符合条件的贴子id（我的星球 贴子列表使用）
    *@param  useridlist  用户id 数组
    */
    public function getpostsidbyuserid($useridlist)
    {
        $Model = M("user_planet_posts");
        $where["userid"] = array('in', $useridlist);
        $where["posts_status"] = array('eq', 1);
        $res = $Model->where($where)->getField('posts_id', true);
        if (!$res) {
            $res = array();
        }
        return $res;
    }

    /*
    *筛选我能收到贴子信息的人的id
    *@param  userid  用户id
    */
    public function getfollowuseridbyuserid($userid)
    {
        $UserPlanetFollowModel = D("UserPlanetFollow");
        $res = $UserPlanetFollowModel->getfollowuseridbyuserid($userid);
        array_push($res, $userid);   //将自己加进去
        return $res;
    }

    /*
    *获取我的星球贴子列表
    *@param  userid  用户id
    */
    public function getmyplanetposts($userid, $limit)
    {
        $useridlist = $this->getfollowuseridbyuserid($userid);
        $postsidlist = $this->getpostsidbyuserid($useridlist);
        $res = $this->getpostslistbyidlist($postsidlist, $limit, $userid);
        return $res;
    }

    /*
    *获取他人的星球贴子列表
    *@param  userid  用户id
    */
    public function getotherplanetposts($userid, $limit = "", $nowuserid)
    {
        $Model = M("user_planet_posts");
        $where = "userid=" . $userid . " and posts_status=1";
        $order = "addtime desc";
        $res = $Model->where($where)->order($order)->limit($limit)->select();
        $UserModel = D("User");
        $PostsLikeModel = D("PostsLike");
        $UserPostsCollectionModel = D("UserPostsCollection");
        if ($res) {
            for ($i = 0; $i < count($res); $i++) {
                $res[$i]["vedioimages"] = imgpath($res[$i]["vedioimages"]);
                $res[$i]["postsfilemsg"] = $this->getpostsfilesinfobypostsid($res[$i]["posts_id"]);  //获取贴子评论人数
                $res[$i]["postscollectionnum"] = $UserPostsCollectionModel->getCollectioncountbyid($res[$i]["posts_id"]);  //获取贴子收藏数
                $res[$i]["postszannum"] = $PostsLikeModel->getlikenum($res[$i]["posts_id"]);  //获取贴子赞数
                $res[$i]["postsreplynum"] = $this->getreplycountbypostsid($res[$i]["posts_id"]);  //获取贴子评论人数
                if (!empty($nowuserid)) {
                    $res[$i]["zanstatus"] = $PostsLikeModel->checkuseridislike($res[$i]["posts_id"], $nowuserid);  //是否点赞  true 是  false  否
                } else {
                    $res[$i]["zanstatus"] = false;
                }
            }
        }

        return $res;
    }


    /*
    *通过贴子id获取星球贴子详细页面信息
    *@param  userid  用户id
    *@param  postsid  贴子id
    */
    public function getpostsIndexbypostsid($postsid, $userid = "")
    {
        $Model = M("user_planet_posts");
        $where = "posts_id=" . $postsid;
        $res = $Model->where($where)->find();
        $UserModel = D("User");
        $PostsLikeModel = D("PostsLike");
        $UserPostsCollectionModel = D("UserPostsCollection");
        if ($res) {
            $res["vedioimages"] = imgpath($res["vedioimages"]);
            $res["postsfilemsg"] = $this->getpostsfilesinfobypostsid($postsid);  //获取贴子上传（图片等）信息

            $res["postscollectionnum"] = $UserPostsCollectionModel->getCollectioncountbyid($postsid);  //获取贴子收藏数
            $res["postszannum"] = $PostsLikeModel->getlikenum($postsid);  //获取贴子赞
            $res["postsreplynum"] = $this->getreplycountbypostsid($postsid);  //获取贴子评论人数

            $res["usermsg"] = $UserModel->getusermsgbyuserid($res["userid"]);  //获取发帖人信息
            $replywhere = "posts_id=" . $postsid;
            $replyorder = "addtime desc";
            $replylimit = 5;

            $res["replymsg"] = $this->getreplymsgbypostsid($replywhere, $replyorder, $replylimit);  //贴子回复信息

            if (!empty($userid)) {

                $UserPlanetFollowModel = D("UserPlanetFollow");
                $res["attentionstatus"] = $UserPlanetFollowModel->checkAttentionByplanetuserid($userid, $res["userid"]);  //是否关注了发帖者的星球  true 是  false  否
                $UserPostsCollectionModel = D("UserPostsCollection");
                $res["collectionstatus"] = $UserPostsCollectionModel->checkCollection($userid, $res["posts_id"]);  //是否收藏了此贴  true 是  false  否
                $res["zanstatus"] = $PostsLikeModel->checkuseridislike($postsid, $userid);  //是否点赞  true 是  false  否

                //获取点赞的用户id
                $planet_user=M("user_planet_posts_like")
                    ->join('user on user.userid=user_planet_posts_like.userid')
                    ->where(['user_planet_posts_like.posts_id'=>$postsid,'user_planet_posts_like.type'=>2])->field('user.username,user.headimg,user.userid')->select();

//                var_dump(M()->getLastSql());exit;
                $user_name=[];
                $head_img=[];
                $userids=[];
                foreach ($planet_user as $item){
                    $user_name[]=trim($item['username']);
                    $head_img[]=imgpath($item['headimg']);
                    $userids[]=$item['userid'];
                }
                $res["posts_username"] = $user_name;  //获取贴子点赞人名
                $res["head_img"] = $head_img;  //获取贴子点赞人头像
                $res["userids"] = $userids;  //获取贴子点赞人头像

            } else {
                $res["attentionstatus"] = false;
                $res["collectionstatus"] = false;
                $res["zanstatus"] = false;
            }

        }

        return $res;
    }


    /*
    *增加贴子转发数
    *@param  $postsid   贴子id
    */
    public function editretransmissionnum($postsid)
    {
        $Model = M("user_planet_posts");
        $where = "posts_id=" . $postsid;
        $res = $Model->where($where)->setInc("retransmissionnum", 1);
        return $res;

    }


    /*
    *通过贴子id获取收藏列表展示贴子信息
    *@param  userid  用户id
    *@param  postsid  贴子id
    */
    public function getCollectinfo($postsid, $userid = "")
    {
        $Model = M("user_planet_posts");
        $where = "posts_id=" . $postsid;
        $res = $Model->where($where)->find();
        $UserModel = D("User");
        $PostsLikeModel = D("PostsLike");
        $UserPostsCollectionModel = D("UserPostsCollection");
        if ($res) {
            $res["vedioimages"] = imgpath($res["vedioimages"]);
            $res["postsfilemsg"] = $this->getpostsfilesinfobypostsid($postsid);  //获取贴子上传（图片等）信息

            $res["postscollectionnum"] = $UserPostsCollectionModel->getCollectioncountbyid($postsid);  //获取贴子收藏数
            $res["postszannum"] = $PostsLikeModel->getlikenum($postsid);  //获取贴子赞
            $res["postsreplynum"] = $this->getreplycountbypostsid($postsid);  //获取贴子评论人数

            $res["usermsg"] = $UserModel->getusermsgbyuserid($res["userid"]);  //获取发帖人信息


            if (!empty($userid)) {

                $UserPlanetFollowModel = D("UserPlanetFollow");
                $res["attentionstatus"] = $UserPlanetFollowModel->checkAttentionByplanetuserid($userid, $res["userid"]);  //是否关注了发帖者的星球  true 是  false  否
                $UserPostsCollectionModel = D("UserPostsCollection");
                $res["collectionstatus"] = $UserPostsCollectionModel->checkCollection($userid, $res["posts_id"]);  //是否收藏了此贴  true 是  false  否
                $res["zanstatus"] = $PostsLikeModel->checkuseridislike($postsid, $userid);  //是否点赞  true 是  false  否

            } else {
                $res["attentionstatus"] = false;
                $res["collectionstatus"] = false;
                $res["zanstatus"] = false;
            }

        }

        return $res;
    }


}