<?php

namespace Common\Model;

use Think\Model;

/**用户等级**/
class UserLevelModel extends Model
{
    protected $tableName = 'user';


    /*
    * 成长经验添加
    *@param  $data  数据
    */
    public function addpost($data)
    {
        $Model = M("userlevel_water");
        $data["addtime"] = time();
        $res = $Model->add($data);
        return $res;
    }

    /*
    * 批量成长经验添加
    *@param  $data  数据
    */
    public function addAllpost($data)
    {
        $Model = M("userlevel_water");
        $res = $Model->addAll($data);
        return $res;
    }


    /*
    * 用户等级计算
    *@param  $now_growth_value  数据
    */
    public function userlevel($now_growth_value)
    {

        if ($now_growth_value < 100) {
            $res = 1;
        } else if ($now_growth_value >= 1000) {
            $res = 11;
        } else {
            $res = floor($now_growth_value / 100) + 1;
        }

        return $res;
    }


    /*
    获取用户星球等级及经验
    *@param  $userid  用户id
    */
    public function getuserlevelinfobyuserid($userid)
    {
        $Model = M("userlevel_water");
        $where = "userid=" . $userid;
        $field = "sum(sroce) as sroce";
        $info = $Model->field($field)->where($where)->select();
        if (is_null($info[0]["sroce"])) {
            $res["level"] = 1;
            $res["experience"] = 0;
            $res["lastlevel"] = 2;
            $res["level_now"] = 0;
            $res["level_score"]=100;
            $res["level_ratio"]=0;
        } else {
            $res["level"] = $this->userlevel($info[0]["sroce"]);
            $res["experience"] = $info[0]["sroce"];
            $res["lastlevel"] = $res["level"] + 1;
            //level_now 当前等级拥有的分数  level_score 当前等级和下一等级的分数差  level_ratio=level_now/level_score  当前等级分数和当前等级与下一等级分数差的比例
            if($res["experience"]>=1000){
                $res["level_now"] = $res["experience"]-1000;
            }else{
                $res["level_now"]=$res["experience"]%100;
            }
            $res["level_score"]=100;
            $res["level_ratio"]=$res["level_now"]/$res["level_score"];
        }
        return $res;
    }

 //-----------------------------------华丽的分割线---------------------------------------------//

    /**
     *  添加用户成长值
     */
    public function addUserGrow($userid,$type){
        $Model = M("userlevel_water");
        $data["addtime"] = time();
        $data['userid'] = $userid;
        $data['type'] = $type;
        switch ($type){
            case 1://每日登陆 +1分
                $addtime = $Model->where(['userid'=>$userid,'type'=>$type])->order('addtime desc')->getField('addtime');
                $time = $this->CompareTime($addtime,$data['addtime']);
                if ($time == false){
                    $data['sroce'] = 1;
                    $data['msg'] = '今日首次登录，成长值+1';
                }else{
                    return true;
                }
                break;
            case 2://每日打卡 +3分（每日第1次）
                $addtime = $Model->where(['userid'=>$userid,'type'=>$type])->order('addtime desc')->getField('addtime');
                $time = $this->CompareTime($addtime,$data['addtime']);
                if ($time == false){
                    $data['sroce'] = 3;
                    $data['msg'] = '今日首次每日打卡，成长值+3';
                }else{
                    return true;
                }
                break;
            case 3://倒计时打卡 +3分（每日第1次）
                $addtime = $Model->where(['userid'=>$userid,'type'=>$type])->order('addtime desc')->getField('addtime');
                $time = $this->CompareTime($addtime,$data['addtime']);
                if ($time == false){
                    $data['sroce'] = 3;
                    $data['msg'] = '今日首次倒计时打卡，成长值+3';
                }else{
                    return true;
                }
                break;
            case 4://评论 +1分（每日上限10分）
                $getTimeCount = $this->getTimeCount($userid,$type);
                if ($getTimeCount < 10){
                    $data['sroce'] = 1;
                    $data['msg'] = '用户发表评论，成长值+1';
                }else{
                    return true;
                }
                break;
            case 5://发帖 +3（每日上限15分）
                $getTimeCount = $this->getTimeCount($userid,$type);
                if ($getTimeCount < 5){
                    $data['sroce'] = 3;
                    $data['msg'] = '用户发表帖子，成长值+3';
                }else{
                    return true;
                }
                break;
            case 6://投稿成功 +10分（包括所有我们后台审核添加的星球主推文）
                $data['sroce'] = 10;
                $data['msg'] = '用户投稿成功，成长值+10';
                break;
            case 7://发布类似违法乱纪帖子-100   手动在举报管理那儿停用账户
                $data['sroce'] = '-100';
                $data['msg'] = '用户发布违法乱纪帖子，成长值-100';
                break;
            case 8://推荐的好友违规被管理员加入黑名单，推荐人-300分；
                $data['sroce'] = '-300';
                $data['msg'] = '推荐的好友违规被管理员加入黑名单，成长值-300';
                break;
            case 9://购物+3分；
                $data['sroce'] = 3;
                $data['msg'] = '用户购物成功，成长值+3';
                break;
            case 10://卖掉商品+3分
                $data['sroce'] = 3;
                $data['msg'] = '用户卖掉商品，成长值+3';
                break;
            default:
                return false;
                break;
        }
        $res = $Model->add($data);
        return $res;
    }

    /**
     * 比较时间
     */
    private function CompareTime($used_time,$new_time){
        $used_time = date('Y-m-d',$used_time);
        $new_time = date('Y-m-d',$new_time);
        if ($used_time == $new_time){
            return true;    //是同一天
        }else{
            return false;   //不是同一天
        }
    }

    /**
     * 获取评论、发帖条数
     */
    private function getTimeCount($userid,$type){
        $time = strtotime(date('Y-m-d'));
        $Model = M('userlevel_water');
        $count = $Model->where(['userid'=>$userid,'type'=>$type,'addtime'=>['between'=>[$time,time()]]])->count();
        return $count;
    }
}