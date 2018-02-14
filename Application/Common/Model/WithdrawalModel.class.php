<?php

namespace Common\Model;

use Think\Model;

/* * 用户提现* */

class WithdrawalModel extends Model {
    /*
     * 添加用户提现信息
     * @param  $data  提现表信息
     */

    public function addpost($data) {
        $Model = M("Withdrawal");
        $Model->startTrans();
        $res = $Model->add($data);
        //记录流水
        $WalletWaterModel = D("WalletWater");
        $data1["type"] = 2;
        $data1["moneynum"] = -$data["moneynum"];
        $data1["userid"] = $data["userid"];
        $res1 = $WalletWaterModel->addpost($data1);
        //扣去用户余额
        $res2 = M('user')->where(['userid'=>$data1["userid"]])->setDec('balance',$data1["moneynum"]);
        if (!($res && $res1 && $res2)) {
            $Model->rollback();
            return false;
        } else {
            $Model->commit();
            return true;
        }
    }

}
