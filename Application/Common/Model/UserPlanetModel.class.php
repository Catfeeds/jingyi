<?php

namespace Common\Model;

use Think\Model;

/* * 用户星球* */

class UserPlanetModel extends Model {

    protected $tableName = 'user_planet';

    /*
     * 判断用户是否存在星球
     * @param  $userid  用户id
     */

    public function checkUserPlanet($userid) {
        $Model = M("user_planet");
        $where = "userid=" . $userid;
        $msg = $Model->where($where)->find();
        if (!$msg) {
            $res = false;
        }  //查询不到 返回空数组
        else {
            $res = true;
        }
        return $res;
    }
	
	  /*
     * 获取所有背景图
     */
    public function getAllBackImg() {
        $model = M('Backimg');
		$field="id as backimgid ,images";
        $res = $model->field($field)->select();
		if($res){
			for($i=0;$i<count($res);$i++){
				$res[$i]["images"]=imgpath($res[$i]["images"]);
				}
			}
		if(!$res){
			$res=array();
		}
        return $res;
    }
	
	
     /*
     * 判断是否为自己的星球
     */
    public function checkplanetbyuserid($userid,$planetid) {
		$res=false;
        $Model = M('user_planet');
		$where="planet_id=".$planetid." and userid=".$userid;
        $msg = $Model->where($where)->find();
		if($msg){
			$res=true;
		}
        return $res;
    }

    /*
     * 通过userid获取用户星球信息
     * @param  $userid  用户id
     */
    public function getUserPlanetInfo($userid) {
        $Model = M("user_planet");
        $where = "userid=" . $userid;
        $res = $Model->where($where)->find();
        if($res){
            $res['follownum'] = $this->getPlanetFollowNum($res["userid"]);
            $res['backimg_url'] = $this->getImagesByBackid($res["backimg_id"]);
			$res['musicfile'] =  imgpath($res["musicfile"]);
			$msg=M('user_planet_msg')->where(['planet_id'=>$res['planet_id']])->find();
            $res['mercury']=$msg['mercury']?$msg['mercury']:'';
            $res['venus']=$msg['venus']?$msg['venus']:'';
            $res['earth']=$msg['earth']?$msg['earth']:'';
            $res['mars']=$msg['mars']?$msg['mars']:'';
            $res['jupiter']=$msg['jupiter']?$msg['jupiter']:'';
        }
        if (!$res) {
            $res =  array();
        }  //查询不到 返回空数组
        return $res;
    }
	
	 /*
     * 通过userid获取用户星球信息
     * @param  $userid  用户id
     */
    public function getUserPlanetInfobyuserid($userid) {
        $Model = M("user_planet");
        $where = "userid=" . $userid;
        $res = $Model->where($where)->find();
        return $res;
    }
    
    /*
     * 通过星球id获取用户星球信息
     * @param  $userid  用户id
     */
    public function getUserPlanetInfoByPlanetid($planet_id) {
        $Model = M("user_planet");
        $where = "planet_id=" .$planet_id;
        $res = $Model->where($where)->find();
        if (!$res) {
            $res = array();
        }  //查询不到 返回空数组
        return $res;
    }

    /*
     * 创建星球
     * @param  $data  星球数据
     */
    public function addpost($data) {
        $Model = M("user_planet");
        $data["growth_value"] = 0;
        $data["addtime"] = time();
        $res = $Model->add($data);
        return $res;
    }
    
    /*
     * 根据背景id获取背景url
     */
    public function getImagesByBackid($backimg_id) {
        $model = M('Backimg');
        $where = " id =".$backimg_id;
        $result = $model->where($where)->find();
        if(!$result){
            $res = C("imgpathurl")."Public/defaultimg/planetbackimg/img_bg.png";
        }else{
			$res=imgpath($result["images"]);
		}
        return $res;
    }
    
    /*
     * 修改星球信息
     */
    public function editPostMsg($planet_id,$data) {
        $model = M('User_planet');
        $where = " planet_id =".$planet_id;
        $result = $model->where($where)->save($data);
		if($result===0){
			$result=true;
			}
        return $result;
    }
	
	
	  /*
     * 修改星球阅读数
     */
    public function editPostreadnum($planet_id) {
        $model = M('User_planet');
        $where = " planet_id =".$planet_id;
        $result = $model->where($where)->setInc('readernum',1);
		if($result===0){
			$result=true;
			}
        return $result;
    }
    
    /*
     * 获取星球用户关注数
     */
    public function getPlanetFollowNum($planet_userid) {
        $model = M('User_planet_follow');
        $where = "planet_userid =".$planet_userid;
        $result = $model->where($where)->count();
        if($result > 99){
            $result = "99+";
        }
        return $result;
    }
    
    /*
     * 发布星球帖子
     * @param  $data  帖子数据
     */
    public function addPlanetPosts($data) {
        $Model = M("user_planet_posts");
        $data["addtime"] = time();
        $res = $Model->add($data);
        return $res;
    }
    
    /*
     * 添加帖子上传文件
     */
    public function addPlanetPostsFile($data) {
        $Model = M("posts_upload");
        $res = $Model->add($data);
        return $res;
    }
    
    /*
     * 判断是否关注
     */
    public function judgeIsFollow($userid,$planet_userid) {
        $model = M('User_planet_follow');
        $where = " userid =".$userid." and planet_userid =".$planet_userid;
        $result = $model->where($where)->find();
        if(!$result){
            $result = array();
        }
        return $result;
    }
    
    /*
     * 添加关注
     */
    public function addUserFollow($data) {
        $model = M('User_planet_follow');
        $data['addtime'] = time();
        $result = $model->add($data);
        return $result;
    }
    
    /*
     * 取消关注
     */
    public function deleteUserFollow($userid,$planet_userid) {
        $model = M('User_planet_follow');
        $where = " userid =".$userid." and planet_userid =".$planet_userid;
        $result = $model->where($where)->delete();
        return $result;
    }
    
    /*
     * 我的帖子列表
     */
    public function functionName($param) {
        
    }
    
    
    

}
