<?php

namespace Home\Controller;

use Common\Model\UserLevelModel;
use Think\Controller;

//活动信息
class IGroupModelArticleController extends CommonController
{

    /**
     * 获取二级模块列表下所有文章
     */
    public function getGroupModelArticleList()
    {
        $id = I('model_class_id') ? I('model_class_id') : 0;
        $model_id = I('model_id');
        $type = I('type') ? I('type') : 0;//0 默认为二级文章 1 三级文章
        if ($model_id == 1) {
            $BannerModel = M("Banner");
            $res["bannerlist"] = $BannerModel->field('id as banner_id,images as banner_images,tui,group_model')
                ->where(['group_model' => $model_id])
                ->order('tui asc')
                ->select();
            foreach ($res["bannerlist"] as &$v) {
                $v["banner_images"] = imgpath($v['banner_images']);
            }
        }
        $pageIndex = I('pageIndex') ? I('pageIndex') : 1;
        $pageSize = I('pageSize') ? I('pageSize') : 10;
        $ArticleModel = M('group_model_article');
        if ($type) {
            $res['article_list'] = $ArticleModel->field('id as article_id,images as article_images,title,author_id,discipline,group_model_class_id')
                ->where(['group_model_three_id' => $id, 'status' => 0, 'is_publish' => 1])
                ->order('create_time desc')
                ->page($pageIndex . "," . $pageSize)
                ->select();
        } else {
            $res['article_list'] = $ArticleModel->field('id as article_id,images as article_images,title,author_id,discipline,group_model_class_id')
                ->where(['group_model_class_id' => $id, 'status' => 0, 'is_publish' => 1])
                ->order('create_time desc')
                ->page($pageIndex . "," . $pageSize)
                ->select();
        }
        foreach ($res['article_list'] as &$v) {
            $v['article_images'] = imgpath($v['article_images']);
        }
        get_api_result(200, '获取成功', $res);
    }

    /**
     *  获取单篇文章详情
     */
    public function getArticleInfo()
    {
        $id = I('article_id');
        $pageIndex = I('pageIndex') ?: 1;
        $pageSize = I('pageSize') ?: 20;
        $user_id = I('user_id');
        $planet_userid = I('author_id');
        $article_id = I('article_id');
        $ArticleModel = M('group_model_article');
        $res['article_info'] = $ArticleModel->alias('a')
            ->field('a.id,a.title,b.headimg as author_headimg,a.author,a.browse_number,a.update_time,a.html_code,a.fabulous_number,a.collection_number')
            ->join('left join user as b on b.userid = a.author_id')
            ->where(['id' => $id])
            ->find();
        $res['article_info']['update_time'] = date('Y-m-d', $res['article_info']['update_time']);
        $res['article_info']['author_headimg'] = imgpath($res['article_info']['author_headimg']);
        $res['comment_count'] = M('article_comment')->where(['article_id' => $id])->count();
        $res['comment_list'] = [];
        $comment_list = M('article_comment')->alias('a')
            ->field('a.comment_content,a.images as comment_images,a.comment_time,b.userid,b.headimg as user_headimg,b.username,a.bei_userid,c.username as bei_username,a.type')
            ->join('left join user as b on b.userid = a.user_id')
            ->join('left join user as c on c.userid = a.bei_userid')
            ->where(['a.article_id' => $id])
            ->order('a.comment_time desc')
            ->page($pageIndex . "," . $pageSize)
            ->select();
        $comment_count = $res['comment_count'] + 1;
        foreach ($comment_list as &$v) {
            $comment_count = $comment_count - 1;
            $v['sort'] = $comment_count;
            $v['comment_images'] = imgpath($v['comment_images']);
            $v['user_headimg'] = imgpath($v['user_headimg']);
            $v['comment_time'] = $this->getTimeConversion($v['comment_time']);
        }
        $res['comment_list'] = $comment_list;
        //是否关注
        $res['is_follow'] = $this->getUseridByFollow($planet_userid, $user_id);
        //是否点赞
        $res['is_fabulous'] = $this->getUseridByFabulous($article_id, $user_id);
        //是否收藏
        $res['is_collection'] = $this->getUseridByCollection($article_id, $user_id);
        get_api_result(200, '获取成功', $res);
    }

    /**
     * 文章点击量加1
     */
    public function getArticleIdByAddBrowse()
    {
        $article_id = I('article_id');
        $ArticleModel = M('group_model_article');
        $ArticleModel->startTrans();
        $browse_number = $ArticleModel->where(['id' => $article_id])->setInc('browse_number');
        if ($browse_number) {
            $ArticleModel->commit();
            get_api_result(200, '文章点击量+1', $browse_number);
        } else {
            $ArticleModel->rollback();
            get_api_result(300, '获取失败', $browse_number);
        }
    }

    /**
     * 查看是否关注
     */
    public function getUseridByFollow($planet_userid = "", $user_id = "")
    {
        $data['userid'] = $user_id ?: I('user_id');
        $data['planet_userid'] = $planet_userid ?: I('planet_userid');
        $UserPlanetModel = D("UserPlanet");
        $row = $UserPlanetModel->judgeIsFollow($data['userid'], $data['planet_userid']);
        if ($row) {
            if ($user_id == '') {
                get_api_result(200, '已关注', 1);
            } else {
                return 1;
            }
        } else {
            if ($user_id == '') {
                get_api_result(200, '未关注', 0);
            } else {
                return 0;
            }
        }
    }

    /**
     * 关注
     */
    public function userFollow()
    {
        $data['userid'] = I('user_id');
        $data['planet_userid'] = I('planet_userid');
        $data['planet_id'] = M('user_planet')->where(['userid' => $data['userid']])->getField('planet_id');
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

    /**
     * 取消关注
     */
    public function userCancelFollow()
    {
        $data['userid'] = I('user_id');
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

    /**
     * 查看是否点赞
     */
    public function getUseridByFabulous($article = "", $userid = "")
    {
        $article_id = $article ?: I('article_id');
        $user_id = $userid ?: I('user_id');
        $Model = M('article_fabulous_collection');
        $res = $Model->where(['article_id' => $article_id, 'user_id' => $user_id, 'is_fabulous' => 1])->find();
        if ($res) {
            if ($article == "") {
                get_api_result(200, '已点赞', 1);
            } else {
                return 1;
            }
        } else {
            if ($article == "") {
                get_api_result(200, '未点赞', 0);
            } else {
                return 0;
            }
        }
    }

    /**
     * 点赞
     */
    public function userFabulous()
    {
        $data['article_id'] = I('article_id');
        $data['user_id'] = I('user_id');
        $data['is_fabulous'] = 1;
        $data['create_time'] = time();
        $Model = M('article_fabulous_collection');
        $Model->startTrans();
        $where = 'article_id=' . $data['article_id'] . ' and user_id=' . $data['user_id'];
        $res = $Model->where($where)->find();
        if ($res) {
            $re = $Model->where($where)->save(['is_fabulous' => 1]);
        } else {
            $re = $Model->add($data);
        }
        $result = M('group_model_article')->where(['id' => $data['article_id']])->setInc('fabulous_number');
        if ($re && $result) {
            $Model->commit();
            get_api_result(200, '已点赞', 1);
        } else {
            $Model->rollback();
            get_api_result(300, '点赞失败', 0);
        }
    }

    /**
     * 取消点赞
     */
    public function userCancelFabulous()
    {
        $article_id = I('article_id');
        $user_id = I('user_id');
        $Model = M('article_fabulous_collection');
        $Model->startTrans();
        $where = 'article_id=' . $article_id . ' and user_id=' . $user_id;
        $res = $Model->where($where)->save(['is_fabulous' => 0]);
        $re = M('group_model_article')->where(['id' => $article_id])->setDec('fabulous_number');
        if ($res && $re) {
            $Model->commit();
            get_api_result(200, '取消点赞成功', 1);
        } else {
            $Model->rollback();
            get_api_result(300, '取消点赞失败', 0);
        }
    }

    /**
     * 查看是否收藏
     */
    public function getUseridByCollection($article = "", $userid = "")
    {
        $article_id = $article ?: I('article_id');
        $user_id = $userid ?: I('user_id');
        $Model = M('article_fabulous_collection');
        $res = $Model->where(['article_id' => $article_id, 'user_id' => $user_id, 'is_collection' => 1])->find();
        if ($res) {
            if ($article == "") {
                get_api_result(200, '已收藏', 1);
            } else {
                return 1;
            }
        } else {
            if ($article == "") {
                get_api_result(200, '未收藏', 0);
            } else {
                return 0;
            }
        }
    }

    /**
     * 收藏
     */
    public function userCollection()
    {
        $data['article_id'] = I('article_id');
        $data['user_id'] = I('user_id');
        $data['is_collection'] = 1;
        $data['create_time'] = time();
        $Model = M('article_fabulous_collection');
        $Model->startTrans();
        $where = 'article_id=' . $data['article_id'] . ' and user_id=' . $data['user_id'];
        $res = $Model->where($where)->find();
        if ($res) {
            $re = $Model->where($where)->save(['is_collection' => 1]);
        } else {
            $re = $Model->add($data);
        }
        $result = M('group_model_article')->where(['id' => $data['article_id']])->setInc('collection_number');
        if ($re && $result) {
            $Model->commit();
            get_api_result(200, '已收藏', 1);
        } else {
            $Model->rollback();
            get_api_result(300, '收藏失败', 0);
        }
    }

    /**
     * 取消收藏
     */
    public function userCancelCollection()
    {
        $article_id = I('article_id');
        $user_id = I('user_id');
        $Model = M('article_fabulous_collection');
        $Model->startTrans();
        $where = 'article_id=' . $article_id . ' and user_id=' . $user_id;
        $res = $Model->where($where)->save(['is_collection' => 0]);
        $re = M('group_model_article')->where(['id' => $article_id])->setDec('collection_number');
        if ($res && $re) {
            $Model->commit();
            get_api_result(200, '取消收藏成功', 1);
        } else {
            $Model->rollback();
            get_api_result(300, '取消收藏失败', 0);
        }
    }

    /**
     * 时间转换
     */
    public function getTimeConversion($comment_time)
    {
        $time = date('Y-m-d', time());
        $time = strtotime($time);
        $difference = $time - $comment_time;
        switch ($difference) {
            case $difference < 0:                                               //当天
                $res = date('H:i', $comment_time);
                break;
            case $comment_time < $time && $comment_time >= ($time - 86400):     //昨天
                $res = "昨天 " . date('H:i', $comment_time);
                break;
            case $comment_time < $time && $comment_time >= ($time - 172800):    //前天
                $res = "前天 " . date('H:i', $comment_time);
                break;
            default:                                                            //超过前天
                $res = date('Y-m-d H:i', $comment_time);
                break;
        }
        return $res;
    }

    /**
     * 添加评论
     */
    public function addUserComment()
    {
        $data['article_id'] = I('article_id');
        $data['user_id'] = I('user_id');
        $data['comment_content'] = shielding(I('comment_content'));
        $data['bei_userid'] = I('bei_userid');
        $data['comment_time'] = time();
        $author_id = I('author_id');
        $data['type'] = I('type');
        $location = I('location');
        $sign=[
            'user_id'=>$data['user_id'],
            'article_id'=>$data['article_id'],
            'add_time'=>time(),
            'location'=>$location,
            'comment'=>$data['comment_content']
        ];
        //获取四级id
        $three_id=M('group_model_article')->where(['id'=>$data['article_id']])->getField('group_model_three_id');
        if ($data['type'] == 4) {
            $upload = new \Think\Upload();// 实例化上传类
            $upload->maxSize = 1024 * 1024 * 20;
            $upload->exts = array('jpg', 'png', 'jpeg');
            $upload->rootPath = './'; // 设置附件上传目录
            $upload->savePath = '/Public/upload/ArticleComment/'; // 设置附件上传目录
            $upload->saveName = array('uniqid', '');
            $upload->ischeckfile = true;  //必须上传文件
            $info = $upload->upload();
            if (!$info) { // 上传错误提示错误信息
                $this->error($upload->getError());
            } else { //上传成功获取上传文件信息
                foreach ($info as $file) {
                    $data["images"] = $file['savepath'] . $file['savename'];
                }
            } // 保存表单数据包括附件数据<br />
        }
        M()->startTrans();
        $commen_model = M('article_comment');
        $msg = $commen_model->add($data);
        if (!$msg) {
            M()->rollback();
            get_api_result(300, "评论添加失败", $msg);
        }
        $JpushmessageModel = D("Jpushmessage");
        if (I("user_id") != $author_id) {   //自己评论自己的贴子
            //添加打卡记录
            $reault2=true;
            if($three_id==16){//倒计时打卡
                $reault=D('UserLevel')->addUserGrow($data['user_id'],3);//加成长值
                $sign['type']=0;
                $reault2=M('user_sign')->add($sign);
            }elseif($three_id==17){//每日打卡
                $reault=D('UserLevel')->addUserGrow($data['user_id'],2);
                $sign['type']=1;
                $reault2=M('user_sign')->add($sign);
            }else{
                //添加用户成长值
                $reault=D('UserLevel')->addUserGrow($data['user_id'],4);//4|评论+1分
            }
            if(!$reault || !$reault2){
                M()->rollback();
                get_api_result(300, "评论添加失败", $reault);
            }
            $JpushmessageModel->setplanetmsgdate($author_id);
        }
        if (I("bei_userid") != "" || I("bei_userid") > 0) {
            $JpushmessageModel->setplanetmsgdate(I("bei_userid"));
        }
        M()->commit();
        get_api_result(200, "添加成功", $msg);
    }

    /**
     *  判定用户是否设置支付密码
     */
    public function getUserIdBycheckPayPassword()
    {
        $user_id = I('user_id');
        $Model = M('user');
        $info = $Model->where(['userid' => $user_id])->find();
        if($info){
            if ($info['status'] == 1){
                $paypassword = $Model->where(['userid' => $user_id])->getField('paypassword');
                if (!empty($paypassword)) {
                    get_api_result(200, '已设置支付密码', 1);
                } else {
                    get_api_result(200, '未设置支付密码', 0);
                }
            }elseif($info['status'] == 2){
                get_api_result(300,'用户帐号冻结',$info);
            }else{
                get_api_result(300,'用户状态异常',$info);
            }
        }else{
            get_api_result(300,'用户不存在',$info);
        }
    }

    /**
     *  文章打赏
     */
    public function reward()
    {
        $paypassword = I('paypassword');
        $money = abs(I('money'));
        $data['userid'] = I('user_id');
        $data['goods_type'] = I('article_id');
        $data['type'] = 100;
        $data['moneynum'] = 0 - $money;
        $data['addtime'] = time();
        $UserModel = D("User");
        $info = $UserModel->where(['userid' => $data['userid']])->find();
        if($info){
            if ($info['status'] == 1){
                $paypasswords = $UserModel->where(['userid' => $data['userid']])->getField('paypassword');
                if (!empty($paypasswords)) {
                    $balance = $UserModel->where(['userid' => $data['userid']])->getField('balance');
                    if (($balance - $money) >= 0) {
                        $ischeckpay = $UserModel->checkpayPassword($data['userid'], md5($paypassword)); //判断支付密码是否正确
                        if (!$ischeckpay) {
                            get_api_result(300, '支付密码错误！');
                        }
                        M('')->startTrans();
                        //扣掉用户余额
                        $result = $UserModel->where(['userid' => $data['userid']])->setDec('balance', $money);
                        //记录流水
                        $res = M('wallet_water')->add($data);
                        if ($res && $result) {
                            M('')->commit();
                            get_api_result(200, '打赏成功', $res);
                        } else {
                            M('')->rollback();
                            get_api_result(300, '扣款失败');
                        }
                    } else {
                        get_api_result(401, "账户余额不足");
                    }
                } else {
                    get_api_result(200, '未设置支付密码', 0);
                }
            }elseif($info['status'] == 2){
                get_api_result(300,'用户帐号冻结',$info);
            }else{
                get_api_result(300,'用户状态异常',$info);
            }
        }else{
            get_api_result(300,'用户不存在',$info);
        }
    }

    /**
     * 报名
     */
    public function enroll()
    {
        $id = I('article_id');
        $Model = M('group_model_article');
        $res = imgpath($Model->where(['id' => $id])->getField('enroll_images'));
        get_api_result(200, '获取成功', $res);
    }

    /**
     * 我要发声  添加文章
     */
    public function add_voice(){
        $images=upload_file('images','/img/');
        if($images['errcode']==201){
            get_api_result(300, $images['data']);
        }
        $data['images']=$images['data'];
        $data['title']=shielding(I('post.title'));
        $content=shielding(htmlspecialchars_decode(I('post.content')));
        $data['discipline']=mb_substr(strip_tags($content), 0, 20, "utf-8").'......';
        $data['html_code']=$content;
        $data['author_id']=I('post.user_id');
        $author=M('user')->where(['userid'=>$data['author_id']])->field('username')->find();
        if(!$author){
            get_api_result(300, '未找到该用户');
        }
        $data['author']=$author['username'];
        if(!$data['title'] || !$data['discipline'] || !$data['html_code'] || !$data['author_id']){
            get_api_result(300, '提交数据不完整');
        }
        $data['group_model_id']=3;
        $data['group_model_class_id']=7;
        $data['create_time']=time();
        $data['update_time']=time();
        $data['is_publish']=1;
        $res=M('group_model_article')->add($data);
        if($res){
            D('UserLevel')->addUserGrow($data['author_id'],5);//成长值
            get_api_result(200, '文章发布成功');
        }else{
            get_api_result(300, '文章发布失败');
        }
    }
    /**
     * 我要发声 添加活动
     */
    public function add_activity(){
        $img=upload_file('img','/img/');
        if($img['errcode']==201){
            get_api_result(300, $img['data']);
        }
        $data['img']=$img['data'];
        $data['title']=shielding(I('post.title'));
        $data['content']=shielding(htmlspecialchars_decode(I('post.content')));
        $data['discipline']=mb_substr(strip_tags(shielding(htmlspecialchars_decode(I('post.content')))), 0, 20, "utf-8").'......';
        $data['start_time']=strtotime(I('post.start_time'));
        $data['end_time']=strtotime(I('post.end_time'));
        if($data['start_time']<time()){
            get_api_result(300, '开始时间不能小于现在的时间');
        }
        if($data['start_time']>=$data['end_time']){
            get_api_result(300, '结束时间不能小于或等于开始时间');
        }
        $data['address']=shielding(I('post.address'));
        $data['most_people']=I('post.most_people');
        $data['user_id']=I('post.user_id');
        if(!$data['title'] || !$data['content'] || !$data['start_time'] || !$data['end_time'] || !$data['address']  || !$data['user_id']){
            get_api_result(300, '请输入完整数据');
        }
        $data['mobile']=I('post.mobile');
        if(!is_mobile($data['mobile'])){
            get_api_result(300, '请输入正确的手机号');
        }
        if(!is_numeric(I('post.money'))){
            get_api_result(300, '请输入正确的费用');
        }
        $data['money']=round(I('post.money'),2);

        $data['add_time']=time();
        $res=M('group_model_activity')->add($data);
        if($res){
            get_api_result(200, '活动发布成功');
        }else{
            get_api_result(300, '活动发布失败');
        }

    }
    /**
     * 我要发声 列表
     */
    public function voice(){
        $pageIndex = I('pageIndex') ? I('pageIndex') : 1;
        $pageSize = I('pageSize') ? I('pageSize') : 10;
        $ArticleModel = M('group_model_article');
        $res['article_list'] = $ArticleModel->field('id as article_id,images as article_images,title,author_id,discipline,group_model_class_id')
            ->where(['group_model_class_id' => 7, 'status' => 0, 'is_publish' => 1])
            ->order('create_time desc')
            ->page($pageIndex . "," . $pageSize)
            ->select();
        foreach ($res['article_list'] as &$v) {
            $v['article_images'] = imgpath($v['article_images']);
        }
        $res['activity_list']=M('group_model_activity')
            ->field('id as activity_id,img,title,discipline')
            ->order('add_time desc')->page($pageIndex . "," . $pageSize)->select();
        foreach ($res['activity_list'] as &$val) {
            $val['img'] = imgpath($val['img']);
        }
        get_api_result(200, '获取成功', $res);
    }
    /**
     * 获取我要发声 活动详情
     */
    public function get_activity(){
        $id=I('post.activity_id');
        if(!$id){
            get_api_result(300, '未获取到活动id');
        }
        $activity=M('group_model_activity')->where(['id'=>$id,'status'=>0])->field('id,img,title,add_time,content,start_time,end_time,mobile,address,most_people,money')->find();
        if(!$activity){
            get_api_result(300, '活动不存在');
        }
        $activity['start_time']=date('Y-m-d H:i:s',$activity['start_time']);
        $activity['end_time']=date('Y-m-d H:i:s',$activity['end_time']);
        $activity['add_time']=date('Y-m-d H:i:s',$activity['add_time']);
        $activity['img'] = imgpath($activity['img']);
        unset($activity['county_id']);
        get_api_result(200, '成功获取活动',$activity);
    }

    /**
     * 保存单张图片
     */
    public function add_img(){
        $img=upload_file('img','/img/');
        if($img['errcode']==201){
            get_api_result(300, $img['data']);
        }elseif ($img['errcode']==200){
            get_api_result(200, "保存图片成功",imgpath($img['data']));
        }
    }

}