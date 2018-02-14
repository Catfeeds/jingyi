<?php
namespace Common\Model;
use Think\Model;
//飞船信息
class AirshipModel extends Model{
	/*
	ALTER TABLE  `airship` ADD  `type` INT( 3 ) NOT NULL DEFAULT  '1' COMMENT  '类型 1|文字 2|图片 3|语音' AFTER  `airship_content` ,
ADD  `url` TEXT NOT NULL COMMENT  '图片音频地址 图片使用,隔开' AFTER  `type` ,
ADD  `times` INT( 11 ) NOT NULL COMMENT  '语音时长（秒）' AFTER  `url` ;
	*/
	 protected $tableName = 'airship'; 
	 
    /*
    * 获取我的飞船列表
    */
    public function getMyShipList($userid,$limit1) {
       
        $where = " userid =".$userid;
        $Model = M('Airship');
        $result = $Model->where($where)->limit($limit1)->order('addtime desc')->select();
		
        if(!empty($result)){
            $UserModel = D('User');
            $fabuzhe= $UserModel->getusermsgbyuserid($userid);
            $AirShipStopsModel=D("AirShipStops");
			
            for($i=0;$i<count($result);$i++){
                $result[$i]['usermsg'] =$fabuzhe;  //发布者信息
                //停靠点（用户信息、星球信息）
                $result[$i]['stopsmsg']= $AirShipStopsModel->getMyShipStops($result[$i]["airship_id"] );

            }
        }
        if(!$result){
            $result=array();
            //$result=(object)array();   
        }
        return $result;
    }

    /*
     * 获取飞船详情
     */
    public function getShipDetail($airship_id) {
        $UserModel = D('User');
        $where = "airship_id =".$airship_id;
		$Model=M("Airship");
        $result = $Model->where($where)->find();
		if($result){
			$result['user_msg'] = $UserModel->getusermsgbyuserid($result["userid"]);
			if($result['url']!=""){
				$imgarr = explode(",",$result['url']);
				for($i=0;$i<count($imgarr);$i++){
					$result['urlpath'][$i]["img"]=imgpath($imgarr[$i]);
					}				
				}
			
			}
		if(!$result){
			$result=(object)array();
			}
        return $result;
    }
    
    /*
     * 根据爱好id（字符串）获取爱好信息
     */
    public function getHobbyNameString($hobbyid) {
		if($hobbyid==0){
			$result="";
			return $result;
			}
        $hobby = explode(",", $hobbyid);
		if(count($hobby)!=0){
        for($i=0;$i<count($hobby);$i++){
            $hobbyname = $this->getHobbyName($hobbyid);
        }
        $result = implode(",", $hobbyname);
		}else{
			$result="";
			}
        return $result;
    }
    
    /*
     * 根据爱好id获取爱好信息
     */
    public function getHobbyName($hobbyid) {
        $model = M('Hobby');
        $where = "hobbyid =".$hobbyid;
        $result = $model->where($where)->find();
        return $result;
    }
    
    /*
     * 发送飞船（添加）
     */
    public function addship($data) {
        $model = M('Airship');
        $data['addtime'] = time();
        $data['airship_status'] = 0;
        $result = $model->add($data);
        return $result;
    }
    
    /*
     * 判断飞船id与userid是否一致
     */
    
    public function judgeUserShip($userid,$airship_id) {
        $model = M('Airship');
        $where = " airship_id =".$airship_id;
        $result = $model->field('userid')->where($where)->find();
        if($result['userid'] == $userid){
            return true;
        }else{
            return false;
        }
    }
    
    /*
     *删除airship 
     */
    public function deleteAirShip($airship_id) {
        $model = M('Airship');
        $where = " airship_id =".$airship_id;
        $result = $model->where($where)->delete();
        return $result;
    }
    
    /*
     * 删除airship_stops中的airship
     */
    public function deleteAirShipStops($airship_id) {
        $model = M('Airship_stops');
        $where = " airship_id =".$airship_id;
        $result = $model->where($where)->delete();
        return $result;
    }
    
    /*
     * 改变飞船状态
     */
    public function changeAirShipStatus($airship_id) {
        $model = M('Airship');
        $where = " airship_id =".$airship_id;
        $data['airship_status'] = 0;
        $result = $model->where($where)->save($data);
        return $result;
    }
	
	 /*
     * 修改飞船信息
     */
    public function editpost($where,$data) {
        $Model = M('airship');
        $res = $Model->where($where)->save($data);
        return $res;
    }
    
    /*
     * 改变停靠仓飞船状态
     */
    public function changeAirStopsStatus($airship_id,$airship_stops_id) {
        $model = M('Airship_stops');
        $where = " airship_id =".$airship_id." and airship_stops_id =".$airship_stops_id;
        $data['stops_status'] = 2;
        $data['leavetime'] = time();
        $result = $model->where($where)->save($data);
        return $result;
    }
    
    
}