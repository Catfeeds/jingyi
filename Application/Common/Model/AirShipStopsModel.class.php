<?php
namespace Common\Model;
use Think\Model;

/**飞船停靠站点**/
class AirShipStopsModel extends Model{
	
	protected $tableName = 'airship_stops'; 
	 
    /*
    * 获取我的飞船停靠信息列表
    */
    public function getMyShipStops($airshipid) {
        $where="airship_id=".$airshipid;
        $order="addtime asc";
        $Model=M('Airship_stops');
        $ship = $Model->where($where)->order('addtime desc')->select();
        $UserPlanetModel=D("UserPlanet");
		$UserModel=D("User");
        if($ship){
            for($x=0;$x<count($ship);$x++){
                 $userid=$ship[$x]["stops_userid"];
                 $ship[$x]['planetmsg'] =  $UserPlanetModel-> getUserPlanetInfo($userid);
                 $ship[$x]['stopsusermsg']= $UserModel->getusermsgbyuserid($userid);
				 $ship[$x]['hx_groupid']= $ship[$x]["hx_groupid"];
				 
             }
         }
        return $ship;
    }

    /*
     * 根据stops_userid获取停靠仓飞船信息（数量）
     */
    public function getStopsCountShip($stops_userid) {
        $model = M('Airship_stops');
        $where = " stops_status = 1 and stops_userid =".$stops_userid;
        $result = $model->where($where)->order('addtime desc')->select();
        for($i=0;$i<count($result);$i++){
            //获取最后一次的聊天信息
            $msg=M('chat')->where(['airship_stops_id'=>$result[$i]['airship_stops_id']])->order('addtime desc')->getField('content');
            $result[$i]['content']=$msg?$msg:'';
            $UserModel = D('User');
            $result[$i]['usermsg'] = $UserModel->getusermsgbyuserid($result[$i]["userid"]);
            $result[$i]['airshipmsg'] = $this->getStopsShipDetail($result[$i]['airship_id']);

            //将停靠舱里面的消息已读
            M('chat')->where(['airship_stops_id'=>$result[$i]['airship_stops_id'],'to_id'=>$stops_userid])->save(['read_weight'=>1]);
        }
        if(!$result){
            $result=array();
            //$result=(object)array();   
        }

        return $result;
    }
    
    /*
     * 获取停靠仓飞船详情
     */
    public function getStopsShipDetail($airship_id) {
        $model = M('Airship');
        $where = " airship_id =".$airship_id;
        $result = $model->where($where)->find();
        return $result;
    }
	
    
    /*
     * 根据airship_stops_id获取停靠仓飞船信息
     */
    public function getStopsShipMsg($airship_stops_id) {
        $model = M('Airship_stops');
        $where = " stops_status = 1 and airship_stops_id =".$airship_stops_id;
        $result = $model->where($where)->select();
        for($i=0;$i<count($result);$i++){
            $UserModel = D('User');
            $result[$i]['usermsg'] = $UserModel->getusermsgbyuserid($result[$i]['stops_userid']);
            $result[$i]['airshipmsg'] = $this->getStopsShipDetail($result[$i]['airship_id']);
        }
        if(!$result){
            $result=array();
            //$result=(object)array();   
        }
        return $result;
    }
	
	
	 /*
     * 添加停靠信息
     */
    public function addpost($data) {
        $Model = M('airship_stops');
		$data["addtime"]=time();
		$data["stops_status"]=1;
		$data["leavetime"]=0;
        $res = $Model->add($data);
        return $res;
    }
	
	 /*
     * 飞船停靠（任务计划）
     */
    public function dostopairship() {
		set_time_limit(0); 
        $AirshipModel = M('airship');
		$where="airship_status=0";
        $result = $AirshipModel->where($where)->select(); //获取全部飞行中的信息

		if(count($result)!==0){
		$AirshipModel=D("Airship");	
		
		for($i=0;$i<count($result);$i++){
			$stopsuseridarr=$this->getstopsuseridbyairshipid($result[$i]["airship_id"]);
			
		    $wherex="";
			if(count($stopsuseridarr)!=0){
				 $str=implode(",",$stopsuseridarr);    //不能停靠已经停靠过的
				 $endstr=$result[$i]["userid"].",".$str;  //不能停靠自己的
			     $wherex.="user.userid not in (".$endstr.")";  
				}else{
					 $wherex.="user.userid<>".$result[$i]["userid"];  //不能停靠自己的
					}                                               
			$wherex.=" and  user.kj_status=0 ";             //不能是拒接的
			
			$UseronlineModel=D("Useronline");
			$nowuserlist=$UseronlineModel-> getnowuserid();
			
			if(count($nowuserlist)==0){                               //只推在线人员
				break;                    //没有在线用户
				}else{
					$wherex.=" and user.userid in (".implode(",",$nowuserlist).")" ;
					}
			/*
			if($result[$i]["cityid"]!=0){
					$wherex.=" and user.city_id =".$result[$i]["cityid"];   //城市相符的
				}
			if($result[$i]["airship_hobby_id"]!=""&&$result[$i]["airship_hobby_id"]!=0){
				$gethobbywherestr=$this->getwherehobyystr($result[$i]["airship_hobby_id"]);
				$wherex.=" and ".$gethobbywherestr;    //爱好相符的 
				}
			
			*/
			$res=$this->finduser($wherex);  //找人
			unset($stopsuserid);
			unset($str);
			unset($endstr);
			unset($wherex);
		
			
			if(!empty($res)){    //找人停靠
			     
			    $option["groupname"]="飞船".$result[$i]["airship_id"];
				$option["desc"]= "飞船".$result[$i]["airship_id"] ;
				$option["owner"]="jingyi".$result[$i]["userid"];  
				$option["members"]=array("jingyi".$res["userid"]);
				 
				$EasemobModel=D("Easemob");
				$hxres=$EasemobModel->register_hx_group($option); 
				if(empty($hxres["hx_groupid"])){
					 return false; 
				} 
				$data["hx_groupid"]=$hxres["hx_groupid"];
				
				$data["airship_id"]=$result[$i]["airship_id"];
				$data["stops_userid"]=$res["userid"];
				$data["userid"]=$result[$i]["userid"];
				
				$UserairshipsetModel=D("Userairshipset");
				$stopuserstatus=$UserairshipsetModel-> getstatus($res["userid"]);  //停靠者匿名状态
				
				$data["user_nm_status"]=$result[$i]["niming_status"];   //发射飞船人匿名状态
				$data["stopuser_nm_status"]=$stopuserstatus["status"]; //停靠者匿名状态
				
			    $addmsg=$this->addpost($data);  //停靠信息添加
				
				
				$editwhere="airship_id=".$result[$i]["airship_id"];
				$editdata["airship_status"]=1;
				$editmsg=$AirshipModel->editpost($editwhere,$editdata);  //修改当前飞船状态
				
				$AirshipmessageModel=D("Airshipmessage");
				 $AirshipmessageModel->setpost($res["userid"]) ;
				 $AirshipmessageModel->setpost($result[$i]["userid"]) ;
				 
				$JpushmessageModel = D('Jpushmessage');
                $JpushmessageModel-> setkjstopmsgdate($res["userid"],$result[$i]["userid"]);
				unset($data);
				unset($editwhere);
				unset($editdata);
				
				
			 }
			
		  }
		}
        return true;
    }
	
	 /*
     * 寻找可停靠人信息
	 *f飞船id
     */
    public function finduser($where) {
        $Model = M('user_planet');
		$field="user.userid,user.kj_status,user_planet.city_id,user.hobbyid";
		$join="left JOIN  user  ON user_planet.userid=user.userid";
		$subQuery  = $Model->field($field)->join($join)->select(false);
		
		$NewModel=M();
		$order1="rand()";
		$field1="user.userid";
		$res=$NewModel->table("(".$subQuery.') as user')->field($field1)->where($where)->order($order1)->find() ;
		//$res = $Model->field($field)->where($where)->join($join)->order($order)->find();
        
        return $res;
    }
    
	
	private function getwherehobyystr($hobbyidstr){
		
		  $hobbyidarr=explode(',',$hobbyidstr);
			$gethobbywherestr="("; 
			for($j=0;$j<count($hobbyidarr);$j++){
				if($j===0){
					$gethobbywherestr.="user.hobbyid like '%#".$hobbyidarr[$j]."|%'"; 
					}else{
						$gethobbywherestr.=" or user.hobbyid like '%#".$hobbyidarr[$j]."|%'"; 
						}
				
				}
			$gethobbywherestr.=")"; 
		
		return $gethobbywherestr;
		}
    
     /*
     * 根据飞船id获取以停靠过的用户id
     */
    public function getstopsuseridbyairshipid($airship_id) {
        $Model = M('airship_stops');
		$where="airship_id=".$airship_id;
        $res = $Model->where($where)->getField("stops_userid",true);
		if(!$res){$res=array();}
        return $res;
    }


}