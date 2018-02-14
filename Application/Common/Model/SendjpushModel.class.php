<?php
/**
 * Created by PhpStorm.
 * User: 丿灬小疯
 * Date: 2017/6/1
 * Time: 16:03
 */

namespace Common\Model;
use Think\Model;
// 推送消息（环信推送）
class SendjpushModel extends Model
{
    protected $tableName = "system_chat";
	
	
    /**
     * 添加信息
     */
    public function addpost($data){
        $data['addtime'] = time();          //更新时间
        $res = $this->add($data);
        return $res;
    }
	
	 /**
     * 修改信息
     */
    public function editpost($where,$data){
        $res = $this->where($where)->save($data);
        return $res;
    }
	
	 /**
     * 获取用户未读的系统消息数量
     */
    public function getNoReadnum($userid){
		$where="to_userid=".$userid." and is_del=0 and is_read=0";
        $res = $this->where($where)->count();
        return $res;
    }
 

}