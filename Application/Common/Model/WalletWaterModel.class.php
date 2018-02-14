<?php
namespace Common\Model;
use Think\Model;

/**用户流水**/
class WalletWaterModel extends Model{
	
	
	/*
	*添加用户流水明细
	*@param  $data  积分表信息
	*/
     public function addpost($data){
		 $Model=M("wallet_water");
		 $data["addtime"]=time();
		 $res= $Model->add($data);
		 
		return  $res;
	}
	
	/*
	*批量添加用户流水明细
	*@param  $data  积分表信息
	*/
     public function addAllpost($data){
		 $Model=M("wallet_water");
		 $res= $Model->addAll($data);
		return  $res;
	}


	/*
	*获取用户当前钱数
	*@param  $userid  用户id
	*/
     public function getMoneynumByuserid($userid){
		$Model=M("wallet_water");
		$where="userid=".$userid;
		$field="sum(moneynum) as money";
		$msg=$Model->field($field)->where($where)->select();
		if(!$msg[0]["money"]){
			$res=sprintf("%.2f",0);
			}else{
				$res=sprintf("%.2f",$msg[0]["money"]);
				}
		return  $res;
	}
	

  
	
	

}