<?php
/**
 * Created by PhpStorm.
 * User: 丿灬小疯
 * Date: 2017/6/1
 * Time: 16:03
 */

namespace Common\Model;


use Think\Model;

class ChatSummaryModel extends Model
{
    protected $tableName = "chat_summary";
    /**
     * 消息汇总里添加消息
     */
    public function addpost($data){
        $data['addtime'] = time();          //更新时间
        $res = $this->add($data);
        return $res;
    }
 

}