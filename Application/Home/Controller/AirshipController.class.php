<?php
namespace Home\Controller;
use Think\Controller;
//飞船信息
class AirshipController extends CommonController {

     /**
     *  我的飞船（列表）
     */
    public function my_ship(){
        $userid = I('userid');
		$model = D('Airship');
        $page = I('page')?I('page'):1;
        $limit = I('limit')?I('limit'):10;
        $limit1 = ($page-1)*$limit.",".$limit;
        $result = $model->getMyShipList($userid,$limit1);
        get_api_result(200, "获取成功", $result);
    }

    /*
     * 飞船详情
     */
    public function ship_detail() {
        $airship_id = I('airship_id');
        $model = D('Airship');
        $res["shipinfo"] = $model->getShipDetail($airship_id);
		$res["chatmsg"] =array();
        get_api_result(200, "获取成功", $res);
    }

    /*
     * 发送飞船
     */
    public function launch_ship() {
        $data['userid'] = I('userid');
        $data['airship_title'] = I('airship_title');
        $data['airship_content'] = I('airship_content');
        $data['airship_hobby_id'] = I('airship_hobby_id')? I('airship_hobby_id'):0;
        $data['airship_hobby_name'] = I('airship_hobby_name')? I('airship_hobby_name'):"";
        $data['cityid'] = I('city_id')?I('city_id'):0;
		$data['type'] = I('type')?I('type'):1;
		$data['url'] = I('url');
		$data['times'] = I('times');
		$data['niming_status'] = I('niming_status');
        $AirshipModel = D('Airship');
        $result = $AirshipModel->addship($data);
        if($result){
            get_api_result(200, "发射成功", $result);
        }else{
            get_api_result(401, "发射失败", $result);
        }
    }
    
    /*
     * 删除飞船
     */
    public function deleteShip() {
        $userid = I('userid');
        $airship_id = I('airship_id');
        $AirshipModel = D('Airship');
        $res = $AirshipModel->judgeUserShip($userid,$airship_id);
        if($res != true){
            get_api_result(401, "用户id与airship_id不一致，无法删除！");
        }
        $row = $AirshipModel->deleteAirShip($airship_id);
        $rat = $AirshipModel->deleteAirShipStops($airship_id);
        if($row){
            $result = $AirshipModel->getMyShipList($userid,10);
            get_api_result(200, "删除成功",$result);
        }else{
            get_api_result(401, "删除失败");
        }
    }
    
    /*
     * 飞船起飞
     */
    public function letShipFlyAgain() {
        $airship_id = I('airship_id');
        $airship_stops_id = I('airship_stops_id');
        $stops_userid = I('stops_userid');
        $AirshipModel = D('Airship');
        $AirshipModel->startTrans();
        $res = $AirshipModel->changeAirShipStatus($airship_id);
        $row = $AirshipModel->changeAirStopsStatus($airship_id,$airship_stops_id);
        if($res && $row){
            $AirshipModel->commit();
            $AirShipStopsModel = D('AirShipStops');
            $result = $AirShipStopsModel->getStopsCountShip($stops_userid);
            get_api_result(200, "起飞成功",$result);
        }else{
            $AirshipModel->rollback();
            get_api_result(401, "起飞失败");
        }
    }
	
	 /*
     * 设置是否接收飞船状态
     */
    public function edituserkjstatus() {
		$userid = I('userid');
		$data["kj_status"]=I('kjstatus');
		$UserModel = D('User');
        $res=$UserModel-> editpostbyuserid($userid, $data);
        if($res){
            get_api_result(200, "修改成功");
        }else{
            get_api_result(300, "修改失败");
        }
    }
	
	 /*
     * 设置是否匿名接收飞船状态
     */
    public function editusernmstatus() {
		$userid = I('userid');
		$status=I('nmstatus');
		$UserairshipsetModel = D('Userairshipset');
        $res=$UserairshipsetModel->  setstatus($userid,$status);
        if($res){
            get_api_result(200, "设置成功");
        }else{
            get_api_result(300, "设置失败");
        }
    }
	
	
	 /*
     * 判断空间站消息红点是否存在  1 为存在 0为不存在
     */
    public function checkredprint() {
		$userid = I('userid');
		$AirshipmessageModel = D('Airshipmessage');
        $res["status"]=$AirshipmessageModel-> checkisreadmessage($userid);
       
        get_api_result(200, "获取成功",$res);
       
    }
	
	 /*
     * 空间站消息红点已读
     */
    public function redprintisread() {
		$userid = I('userid');
		$AirshipmessageModel = D('Airshipmessage');
		$data["isread"]=0;
        $res=$AirshipmessageModel-> editpost($userid,$data);
       
        get_api_result(200, "操作成功");
       
    }
	
	 /*
     * 空间站消息推送信息
     */
    public function senkjchatmsg() {
		$userid = I('userid');
		$touserid = I('to_userid');
		$AirshipmessageModel=D("Airshipmessage");
		$AirshipmessageModel->setpost($touserid) ;
		$JpushmessageModel = D('Jpushmessage');
        $res=$JpushmessageModel->  setkjchatmsgdate($touserid);
       
        get_api_result(200, "操作成功",$res);
       
    }
	
	
	

}