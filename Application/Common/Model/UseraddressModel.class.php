<?php
namespace Common\Model;
use Think\Model;

/**用户收货信息**/
class UseraddressModel extends Model{
	
	
	/*
	*获取用户收货信息
	*@param  $userid  用户id
	*/
     public function getUseraddressinfo($userid){
		 $Model=M("useraddress");
		 $where="useraddress.userid=".$userid;
		 $field="useraddress.*,useraddress.id as addressid,`province`.`province` as province_name,`city`.`city` as city_name,`county`.`county` as county_name";
		 $join1="left join province ON useraddress.provinceid=`province`.`province_id`";
		 $join2="left join city ON useraddress.cityid=`city`.`city_id`";
		 $join3="left join county ON useraddress.countyid=`county`.`county_id`";
		 $res= $Model->field($field)->join($join1)->join($join2)->join($join3)->where($where)->select();
		 if(!$res){
			 $res=array();
			 }
		return  $res;
	}
	
	/*
	*根据id获取收货地址信息
	*@param  $id  用户id
	*/
     public function getUseraddressinfoByid($id){
		 $Model=M("useraddress");
		 $where="useraddress.id=".$id;
		 $field="useraddress.*,useraddress.id as addressid,`province`.`province` as province_name,`city`.`city` as city_name,`county`.`county` as county_name";
		 $join1="left join province ON useraddress.provinceid=`province`.`province_id`";
		 $join2="left join city ON useraddress.cityid=`city`.`city_id`";
		 $join3="left join county ON useraddress.countyid=`county`.`county_id`";
		 $res= $Model->field($field)->join($join1)->join($join2)->join($join3)->where($where)->find();
		return  $res;
	}
	
	
	/*
	*修改收货地址
	*@param  $id  地址id
	*@param  $data  数据
	*/
     public function editpost($id,$data){
		 $Model=M("useraddress");
		 $where="id=".$id;
		 $res= $Model->where($where)->save($data);
		  if($res===0){  // 更新数据和原始数据一样 默认更新成功
			 $res=true;			 
			 }
	
		return  $res;
	}
	
	/*
	*添加收货地址
	*@param  $data  数据
	*/
     public function addpost($data){
		 $Model=M("useraddress");
		 $res= $Model->add($data);		 
		return  $res;
	}
	
	/*
	*删除收货地址
	*@param  $id  地址id
	*/
     public function delpost($id){
		 $Model=M("useraddress");
		 $where="id=".$id;
		 $addressinfo=$this->where($where)->find();
		 if($addressinfo["status"]==1){
			 $where1="userid=".$addressinfo["userid"];
			 $num=$this->where($where1)->count();
			 if($num>1){
				 $where2="userid=".$addressinfo["userid"]." and status=0";
				 $nextaddress=$this->where($where2)->find();
				  $data["status"]=1;
				  $where3="id=". $nextaddress["id"];
				  $res= $Model->where($where3)->save($data);
				 }
			}
		 $res= $Model->where($where)->delete();
		return  $res;
	}
	
	/*
	*设置默认收货地址
	*@param  $id  地址id
	*@param  $data  数据
	*/
     public function editstatuspost($id,$userid){
		 $Model=M("useraddress");
		 $where="id=".$id;
		 $where1="userid=".$userid;
		 $data1["status"]=0;
		 $data["status"]=1;
		 $res1= $Model->where($where1)->save($data1);
		 $res= $Model->where($where)->save($data);
		  if($res===0){  // 更新数据和原始数据一样 默认更新成功
			 $res=true;			 
			 }
	
		return  $res;
	}
	
	
   /*
	*判断是否为此用户的收货信息
	*@param  $userid  用户id
	*@param  $id  地址id
	*/
     public function checkUseraddressByid($userid,$id){
		 $Model=M("useraddress");
		 $where="userid=".$userid." and id=".$id;
		 $res= $Model->where($where)->find();
		 if($res){
			 $res=true;
			 }
		return  $res;
	}
	
   /*
	*获取用户默认地址
	*@param  $userid  用户id
	*/
     public function getTrueUseraddress($userid){
		 $Model=M("useraddress");
		 $where="useraddress.userid=".$userid." and useraddress.status=1";
		 $field="useraddress.*,useraddress.id as addressid,`province`.`province` as province_name,`city`.`city` as city_name,`county`.`county` as county_name";
		 $join1="left join province ON useraddress.provinceid=`province`.`province_id`";
		 $join2="left join city ON useraddress.cityid=`city`.`city_id`";
		 $join3="left join county ON useraddress.countyid=`county`.`county_id`";
		 $res= $Model->field($field)->join($join1)->join($join2)->join($join3)->where($where)->find();
		 if(!$res){
			 $res=(object)array();
			 }
		return  $res;
	}
	
	 /*
	*判断用户是否存在收货地址
	*@param  $userid  用户id
	*/
     public function checkUseraddress($userid){
		 $Model=M("useraddress");
		 $where="userid=".$userid;
		 $res= $Model->where($where)->find();
		 if(!$res){
			 $msg=false;
			 }else{
				 $msg=true;
				 }
		return  $res;
	}
	

}