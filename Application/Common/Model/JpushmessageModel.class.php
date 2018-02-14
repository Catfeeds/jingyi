<?php
namespace Common\Model;
use Think\Model;

/**消息推送**/
class JpushmessageModel extends Model{
	protected $tableName = 'user'; 
	
	
	/*推送空间站聊天消息	
	*@param to_userid 发送人id
	*/
     public function setkjchatmsgdate($to_userid){
		$res["type"]=2;  //对别名推送消息
		$res["alias"]=$to_userid;
		$res["Alert"]="您的空间站有一条新的聊天消息，快去查看吧";
		$res["msg_content"]="您的空间站有一条新的聊天消息，快去查看吧";
		$res["extras"]["messtype"]=2;     //消息类型  1|消息模块 2|空间站模块  3|我的星球模块
		$res["extras"]["messcontent"]="空间站聊天消息";
		
		$JpushModel=D("Jpush");
		$messinfo=$JpushModel->sendmsg($res["type"],$res["alias"],$res["Alert"],$res["msg_content"],$res["extras"]);  
		return $messinfo;
	}
	
	/*推送空间站停靠后消息	
	*@param to_userid 停靠者id
	*@param userid 飞船发布者id
	*/
     public function setkjstopmsgdate($to_userid,$userid){
		$res["touser"]["type"]=2;  //对别名推送消息
		$res["touser"]["alias"]=$to_userid;
		$res["touser"]["Alert"]="您的空间站有飞船停靠了，快去查看吧";
		$res["touser"]["msg_content"]="您的空间站有飞船停靠了，快去查看吧";
		$res["touser"]["extras"]["messtype"]=2;     //消息类型  1|消息模块 2|空间站模块  3|我的星球模块
		
		$res["user"]["type"]=2;  //对别名推送消息
		$res["user"]["alias"]=$userid;
		$res["user"]["Alert"]="您的空间站中飞船已经停靠了，快去查看吧";
		$res["user"]["msg_content"]="您的空间站中飞船已经停靠了，快去查看吧";
		$res["user"]["extras"]["messtype"]=2;     //消息类型  1|消息模块 2|空间站模块  3|我的星球模块
		
		$JpushModel=D("Jpush");
		$messinfo["stopuser"]=$JpushModel->sendmsg($res["touser"]["type"],$res["touser"]["alias"],$res["touser"]["Alert"],$res["touser"]["msg_content"],$res["touser"]["extras"]); 
		$messinfo["owner"]=$JpushModel->sendmsg($res["user"]["type"],$res["user"]["alias"],$res["user"]["Alert"],$res["user"]["msg_content"],$res["user"]["extras"]); 
		return $messinfo;  
	}
	
	
	/*推送消息模块中的消息	
	*@param $alias 消息接收者别名 
	*@param userid 飞船发布者id
	*/
     public function setxxmsgdate($alias){
		$res["type"]=2;  //对别名推送消息
		$res["alias"]=$alias;
		$res["Alert"]="您的消息模块中有新信息，快去查看吧";
		$res["msg_content"]="您的消息模块中有新信息，快去查看吧";
		$res["extras"]["messtype"]=1;     //消息类型  1|消息模块 2|空间站模块  3|我的星球模块
		
			
		$JpushModel=D("Jpush");
		$messinfo=$JpushModel->sendmsg($res["type"],$res["alias"],$res["Alert"],$res["msg_content"],$res["extras"]); 
		return $messinfo;  
	}
	
	
	/*推送星球模块中的消息	
	*@param $alias 消息接收者别名 
	*@param userid 飞船发布者id
	*/
     public function setplanetmsgdate($alias){
		$res["type"]=2;  //对别名推送消息
		$res["alias"]=$alias;
		$res["Alert"]="您的星球中有新信息，快去查看吧";
		$res["msg_content"]="您的星球中有新信息，快去查看吧";
		$res["extras"]["messtype"]=3;     //消息类型  1|消息模块 2|空间站模块  3|我的星球模块
		
			
		$JpushModel=D("Jpush");
		$messinfo=$JpushModel->sendmsg($res["type"],$res["alias"],$res["Alert"],$res["msg_content"],$res["extras"]); 
		return $messinfo;  
	}
	
	
	
 
	
	

}