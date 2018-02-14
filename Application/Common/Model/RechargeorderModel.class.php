<?php

namespace Common\Model;

use Think\Model;

/* * 用户充值* */

class RechargeorderModel extends Model
{
    /*
     * 添加用户充值订单信息
     * @param  $data  
     */

    public function addpost($data)
    {
        $data["addtime"] = time();
        $data["status"] = 0;
        $res = $this->add($data);
        return $res;

    }


    /*
    *支付成功回调操作
    * @param $order_no
    * @param $trade_no
    */
    public function updatesuccess($order_no, $trade_no = "", $nowmoney)
    {
        $Model = M("rechargeorder");
        $data["trade_no"] = $trade_no;
        $data["status"] = 1;
        $data["backtime"] = time();
        $data["paymoneynum"] = $nowmoney;
        $where = "order_no='" . $order_no . "' and status=0";
        $msg = $Model->where($where)->find();
        if (!$msg) {
            return false;
        }
        $Model->startTrans();

        $res = $Model->where($where)->save($data);   //修改订单状态
        $data1["userid"] = $msg["userid"];
        $data1["moneynum"] = $data["paymoneynum"];
        $data1["type"] = 1;
        $WalletWaterModel = D("WalletWater");
        $res1 = $WalletWaterModel->addpost($data1);   //用户流水增加
        //用户余额增加
        $UserModel = D('User');
        $res2 = $UserModel->where(['userid'=>$data1['userid']])->setInc('balance',$data1['moneynum']);

        if (!($res1 && $res && $res2)) {
            $Model->rollback();
            return false;
        } else {
            $Model->commit();
            return true;
        }
    }

}
