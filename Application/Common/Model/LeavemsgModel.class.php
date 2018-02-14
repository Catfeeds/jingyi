<?php

namespace Common\Model;

use Think\Model;

/* * 留言反馈* */

class LeavemsgModel extends Model {
	
	
    /*
     * 添加
     * @param  $data  数据信息
     */

    public function addpost($data) {
		$data["addtime"]=time();
		$res=$this->add($data);
        return $res;
    }

    /*
     * 获取留言列表
     */

    public function getlist() {
        $order="a.addtime desc";
		$field="a.*,user.tel";
		$join ="user ON a.userid=user.userid";
        $res = $this->alias("a")->field($field)->join($join)->select();
        return $res;
    }
	
	 /*
     * 删除留言列表
     * @param  $leavemsgid  
     */

    public function delpost($leavemsgid) {
		$where="leavemsg_id=".$leavemsgid;
        $res = $this->where($where)->delete();
        return $res;
    }

   
    
}
