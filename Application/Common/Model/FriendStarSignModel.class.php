<?php

namespace Common\Model;

use Think\Model;

/* * 打标签* */

class FriendStarSignModel extends Model {
	  protected $tableName = 'user_friendstarsign'; 
    
	
	
	
	/*
     *  添加
	*/

    public function addpost($data) {
        $res = $this->add($data);
        return $res;
    }
	
	
	/*
     *  添加
	*/

    public function addallpost($data) {
		$Model=M("user_friendstarsign");
        $res = $Model->addAll($data);
        return $res;
    }
	
	/*
     *  获取好友标签（最多的三个）
	*/

    public function getFriendStarSign($userid) {
		$where="beuserid=".$userid;
		$group="starsignid";
		$field="count(*) as allnum ,beuserid,starsignid";
		
		$subQuery = $this->field($field)->group($group)->where($where)->select(false); 
		$Model=M();
		$order=" a.allnum desc";
		$field1="b.name";
		$join="star_sign as b on a.starsignid=b.star_id";
		$res=$Model->table("(".$subQuery.') as a')->field($field1)->order($order)->join($join)->limit(3)->getField("name",true) ;
		if(empty($res)){
			$res=array();
			}
        return implode(",",$res);
    }
	


}
