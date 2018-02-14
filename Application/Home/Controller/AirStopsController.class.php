<?php
namespace Home\Controller;
use Think\Controller;
//停靠站点
class AirStopsController extends CommonController {

    /*
     * 我的停靠仓飞船数量
     */
    public function myStops_count() {
        $stops_userid = I('stops_userid');
        $AirShipStopsModel = D('AirShipStops');
        $result = $AirShipStopsModel->getStopsCountShip($stops_userid);
        get_api_result(200, "获取成功", count($result));
    }

    /*
     * 我的停靠仓列表信息
     */
    public function myStopsList() {
        $stops_userid = I('stops_userid');
        $AirShipStopsModel = D('AirShipStops');
        $result = $AirShipStopsModel->getStopsCountShip($stops_userid);
        get_api_result(200, "获取成功", $result);
    }
	
	
	 /*
     * 飞船停靠（任务计划）
     */
    public function findusertostop() {
		set_time_limit(0); 
		
		$AirShipStopsModel = D('AirShipStops');
        $res=$AirShipStopsModel-> dostopairship() ;
        get_api_result(200, "执行成功",$res); 
    }
	
	 /*
     * 踢飞停靠飞船
     */
    public function delShipToFlyAgain() {
        $airship_id = I('airship_id');
        $airship_stops_id = I('airship_stops_id');
        $stops_userid = I('userid');
        $AirshipModel = D('Airship');
        $AirshipModel->startTrans();
        $res = $AirshipModel->changeAirShipStatus($airship_id);
        $row = $AirshipModel->changeAirStopsStatus($airship_id,$airship_stops_id);
        if($res && $row){
            $AirshipModel->commit();
            $AirShipStopsModel = D('AirShipStops');
            $result = $AirShipStopsModel->getStopsCountShip($stops_userid);
            get_api_result(200, "删除成功",$result);
        }else{
            $AirshipModel->rollback();
            get_api_result(401, "删除失败");
        }
    }
    /**
     * 保存飞船停靠聊天信息
     */
    public function save_air_ship_stop(){
        $data=I('post.');
        $airship_stops_id=$data['airship_stops_id'];
        unset($data['airship_stops_id']);
        if(!$airship_stops_id){
            get_api_result(300, '获取飞船停靠id错误');
        }
        if(!$data['msg']){
            get_api_result(300, '获取聊天信息错误');
        }
        $data['msg']=shielding($data['msg']);
        $data['msg_time']=time();
        $res=M('airship_stops')->where(['airship_stops_id'=>$airship_stops_id,'stops_status'=>1])->getField('airship_stops_id');
        if(!$res){
            get_api_result(300, '未找到飞船停靠信息');
        }
        $result=M('airship_stops')->where(['airship_stops_id'=>$airship_stops_id])->save($data);
        if($result){
            get_api_result(200, '保存飞船聊天信息成功');
        }else{
            get_api_result(200, '保存飞船聊天信息失败');
        }
    }

    /**
     * 获取停靠舱是否有未读消息
     */
    public function is_not_read(){
        $userid=I('userid');
        if(!$userid){
            get_api_result(300, '数据不完整');
        }
        $airship_stops=M('airship_stops')->where(['stops_userid'=>$userid])->field('airship_stops_id')->select();
        if(!$airship_stops){
            get_api_result(300, '该用户无停靠舱');
        }
        $airship_stops_id=[];
        foreach ($airship_stops as $item){
            $airship_stops_id[]=$item['airship_stops_id'];
        }
        $not_read=M('chat')->where(['airship_stops_id'=>['in',$airship_stops_id],'to_id'=>$userid,'read_weight'=>0])->count();
        if($not_read){
            get_api_result(200, '存在未读消息',['is_not_read'=>1]);
        }else{
            get_api_result(200, '无未读消息',['is_not_read'=>0]);
        }
    }
}