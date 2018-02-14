<?php

namespace Home\Controller;

use Think\Controller;

//活动信息
class IGroupModelController extends CommonController
{


    /**
     *  获取首页模块列表
     */
    public function getGroupModelBannerList()
    {
        //获取banner信息
        $id = I('banner_id');
        $BannerModel = M("Banner");
        $res["bannerlist"] = $BannerModel->where(['group_model' => $id])->order('tui asc')->select();
        $page = 1;
        $limit = 10;
        $limit1 = ($page - 1) * $limit . "," . $limit;
        $order = " desc";
        $where = "type=1";
        $res["activitylist"] = $BannerModel->getlist($where, $limit1, $order);
        get_api_result(200, "获取成功", $res);
    }

    /**
     *  获取首页模块列表
     */
    public function getGroupModelList()
    {
        //获取banner信息
        $id = I('model_id');
        $BannerModel = M("Banner");
        $res["bannerlist"] = $BannerModel->field('id as banner_id,images as banner_images,tui,group_model')
            ->where(['group_model' => $id])
            ->order('tui asc')
            ->select();
        foreach($res["bannerlist"] as &$v){
            $v["banner_images"] = imgpath($v['banner_images']);
        }
        //获取二级分类信息
        $Model = M('group_model_class');
        $res['class_list'] = $Model->field('id as class_id,name,type')
            ->where(['status' => 1, 'group_model_id' => $id])
            ->order('`type` asc,`order` asc')
            ->select();
        if ($id == 1){
            foreach ($res['class_list'] as &$v) {
                $v['article_list'] = $this->getArticleList($v['class_id']);
            }
        }elseif($id == 2 || $id == 3 || $id == 4){
            foreach ($res['class_list'] as &$v) {
                $v['article_list'] = $this->getGoodsList($v['class_id']);
            }
        }else{
            get_api_result(300, "获取失败", $res);
        }
        get_api_result(200, "获取成功", $res);
    }

    /**
     * 获取模块文章列表
     */
    public function getArticleList($model_id)
    {
        //获取图片及标题信息
        $ArticleModel = M('group_model_article');
        $limit = M('class_display')->where(['type' => $model_id])->getField('number');
        $list = $ArticleModel->field('id as article_id,author_id,images as article_images,title,group_model_class_id')
            ->where(['group_model_class_id' => $model_id, 'status' => 0, 'is_publish' => 1])
            ->limit($limit)
            ->order('create_time desc')
            ->select();
        foreach ($list as &$v){
            $v['article_images'] = imgpath($v['article_images']);
        }
        return $list;
    }

    /**
     * 获取二级模块列表
     */
    public function getGoodsList($model_id){
        //获取图片及标题信息
        $GroupModel = M('group_model_class');
        $list = $GroupModel->field('images as article_images,images_discipline as title')
            ->where(['id' => $model_id, 'status' => 1])
            ->select();
        foreach ($list as &$v){
            $v['article_images'] = imgpath($v['article_images']);
        }
        return $list;
    }




    /**
     *  获取官方活动贴列表
     */
    public function getActivitylist()
    {
        $ActivityModel = D('Activity');
        $page = I('page') ? I('page') : 1;
        $limit = I('limit') ? I('limit') : 10;
        $limit1 = ($page - 1) * $limit . "," . $limit;
        $order = "addtime desc";
        $where = "type=1";
        $res = $ActivityModel->getlist($where, $limit1, $order);
        get_api_result(200, "获取成功", $res);
    }

    /*
     * 获取活动详情
     */
    public function getActivityinfo()
    {
        $activity_id = I('activity_id');
        $userid = I('userid');
        $ActivityModel = D('Activity');
        $res = $ActivityModel->getinfo($activity_id, $userid);
        get_api_result(200, "获取成功", $res);
    }

    /*
     * 加入活动   
     */
    public function addActivity()
    {
        $data['userid'] = I('userid');
        $data['activity_id'] = I('activity_id');
        $data['order_no'] = build_order_no();
        $ActivityModel = D("Activity");
        $ActivityJoinOrderModel = D('ActivityJoinOrder');
        $del = $ActivityJoinOrderModel->delpost($data['activity_id'], $data['userid']);    //清除之前该用户已经报名的信息但没有缴费的信息
        $Activitymsg = $ActivityModel->getinfo($data['activity_id'], $data['userid']);  //获取活动信息
        //判断活动是否存在   正常情况下 活动必然存在
        if (count($Activitymsg) == 0) {
            get_api_result(401, "非法操作");
        }

        //判断用户没有加入该活动
        if ($Activitymsg["joinstatus"]) {
            get_api_result(402, "已加入活动，请不要重复操作！", $Activitymsg);
        }

        //判断用户人数上线是否为无限人  无限人数量为-1
        if ($Activitymsg["personnum"] != -1) {
            //判断加入的人数是否达到上线
            if ($Activitymsg["joinnum"] >= $Activitymsg["personnum"]) {
                get_api_result(402, "人数已达到上线！", $Activitymsg);
            }
        }

        //判断是否到达开始时间
        if (time() >= $Activitymsg["begintime"] - C("activity_gettime")) {
            get_api_result(402, "已经截止报名");
        }

        //判断是否免费
        if (strval($Activitymsg["moneynum"]) != strval(0)) {
            $data["status"] = 1;
        } else {
            $data["status"] = 2;
        }

        $result = $ActivityJoinOrderModel->addpost($data); //添加完成
        if ($result) {
            get_api_result(200, "加入成功", $result);
        } else {
            get_api_result(401, "加入失败", $result);
        }
    }


    /*
   * 任务计划
   */
    public function doeditstatus()
    {
        $ActivityModel = D('Activity');
        $res = $ActivityModel->checkendtimetoeditstatus();
        get_api_result(200, "获取成功", $res);
    }

    /**
     * 获取论坛二三四级列表和banner
     */
    public function get_forum_list(){
        $result['two']=M('group_model_class')->where(['group_model_id'=>3,'pid'=>0])->order('`order` asc')->select();
        //净一下的三级
        $result2=M('group_model_class')->where(['group_model_id'=>3,'pid'=>6])->order('`order` asc')->select();
        foreach ($result2 as &$item2){
            $item2['images']=imgpath($item2['images']);
        }
        //打卡下的三级
        $result3=M('group_model_class')->where(['group_model_id'=>3,'pid'=>8])->order('`order` asc')->select();
        foreach ($result3 as &$item3){
            $item3['images']=imgpath($item3['images']);
            if($item3['id']==17){
                $item3['four']=M('group_model_class')->where(['pid'=>17])->order('`order` asc')->select();
            }
        }
        foreach ($result['two'] as &$item){
            $item['images']=imgpath($item['images']);
            if($item['id']==6){
                $item['three']=$result2;
            }
            if($item['id']==8){
                $item['three']=$result3;
            }
        }
        $result["banner"] = M("Banner")->field('id as banner_id,images as banner_images,tui,group_model')
            ->where(['group_model' => 3])->order('tui asc')->select();
        foreach ($result["banner"] as &$item4){
            $item4['banner_images']=imgpath($item4['banner_images']);
        }
        get_api_result(200, "获取成功", $result);
    }

}