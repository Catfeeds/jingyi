<?php

namespace Common\Model;

use Think\Model;

/* * 举报* */

class ReportModel extends Model {
	  protected $tableName = 'user_report'; 
    
	
	/*
     * 添加举报信息
     * @param  $data  
     */

    public function addpost($data) {
        $res = $this->add($data);
        return $res;
       
    }
	
	/*
     * 修改举报信息
     * @param  $data  
     */

    public function editpost($where,$data) {
        $res = $this->where($where)->save($data);
        return $res;
       
    }
	
	/*
     * 判断是否已经举报过
	  * @param  $type  类型  1|个人贴2|星球贴3|群活动  
	  * @param  $postsid  贴子id
	  * @param  $userid  举报人id
     */

    public function checkreport($postsid,$userid,$type) {
		$where="report_postsid=".$postsid." and report_userid=".$userid." and report_type=".$type;
        $msg = $this->where($where)->find();
		$res=false;
		if($msg){
			$res=true;
			}
        return $res;
       
    }
	
	/*
      * 删除举报
     */

    public function delpost($report_id) {
		$where="report_id=".$report_id;
        $res = $this->where($where)->delete();
        return $res;
       
    }
	
	


}
