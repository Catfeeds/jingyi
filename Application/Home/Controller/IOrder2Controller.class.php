<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/9
 * Time: 11:12
 * 订单
 */

namespace Home\Controller;


use Common\Model\OrderModel;

class IOrder2Controller extends CommonController
{
    /*******************************************************订单*******************************************************************/

    /**
     * 保存订单
     */
    public function buy_goods(){
//        var_dump(I('post.'));exit;
        $goods=json_decode(htmlspecialchars_decode(I('post.goods')),1);//商品数据
        $user_address_id=I('post.user_address_id');
        $user_id=I('post.user_id');
        if(!$goods || !$user_address_id || !$user_id){
            get_api_result(300, '数据不完整');
        }
//        $q=[['norms_id'=>1,'num'=>1],['norms_id'=>2,'num'=>2]];
//        echo json_encode($q);exit;
        $num=0;
        foreach ($goods as $item){
            $num+=$item['num'];
        }
        if ($num == 0) {
            get_api_result(300, '未选择商品');
        }

        $orderModel=new OrderModel();
        $orderdata=[
            'subcode'=>$orderModel->getordercode(),  //订单编号
            'addtime'=>time(),
            'user_address_id'=>$user_address_id,
            'status'=>0,
            'enddotime'=>time(),
            'user_id'=>$user_id,
        ];

        M()->startTrans();
        //添加订单
        $result=M('order_main')->add($orderdata);
        $er1=M()->getError();
        if(!$result){
            $orderModel->rollback();
            get_api_result(300, '下单失败');
        }
        $order_product_data=[];
        $money=0;
        foreach ($goods as $val){
            $norms=M('product_norms')
                ->join('product on product.product_id=product_norms.product_id')
                ->field('product.product_id,product.product_name,product.summary,product.product_img,product_norms.price,product_norms.norms,product.userid')
                ->where(['norms_id'=>$val['norms_id']])
                ->find();
            $money+=$norms['price']*$val['num'];
            $order_product_data[]=[
                'order_id'=>$result,
                'product_id'=>$norms['product_id'],
                'norms_id'=>$val['norms_id'],
                'user_id'=>$user_id,
                'product_name'=>$norms['product_name'],
                'summary'=>$norms['summary'],
                'product_img'=>$norms['product_img'],
                'norms_price'=>$norms['price'],
                'norms_num'=>$val['num'],
                'norms'=>$norms['norms'],
                'add_time'=>time(),
                'shop_user_id'=>$norms['userid'],
            ];
        }
        $result2=M('order_main')->where(['id'=>$result])->save(['money'=>$money]);
        $er2=M()->getError();
        $result3=M('order_product')->addAll($order_product_data);
        $er3=M()->getError();
        if(!$result2 || !$result3){
            $orderModel->rollback();
            get_api_result(300, '下单失败',[$er1,$er2,$er3]);
        }
        $orderModel->commit();
        get_api_result(200, '下单成功', $orderdata['subcode']);
    }
    /**
     * 获取订单详情
     */
    public function get_order_info(){
        $order_id = I('post.order_id');
        if(!$order_id){
            get_api_result(300, '未获取到订单id');
        }
        $order=M('order_main')
            ->join('left join useraddress on useraddress.id = order_main.user_address_id')
            ->join('left join send on send.id = order_main.send_id')
            ->field('useraddress.name,useraddress.tel,useraddress.address,order_main.send_no,order_main.addtime,order_main.pay_type,order_main.money,order_main.pay_money,order_main.status,send.sendname,order_main.id as order_id')
            ->where(['order_main.id'=>$order_id])->find();
        if(!$order['sendname']){
            $order['sendname']='';
        }
        //支付状态 0|待支付 1|取消订单 2|已支付（待收货）3|已发货  4|确认收货  5|已完成 6| 退款中 7|退款成功
        $pay_type='';
        switch ($order['pay_type']){
            case 0:
                $pay_type='待支付';break;
            case 1:
                $pay_type='支付宝';break;
            case 2:
                $pay_type='微信';break;
            case 3:
                $pay_type='钱包';break;
        }
        $order['pay_type']=$pay_type;
        $order['goods']=M('order_product')->field('product_name,norms,summary,norms_price,norms_num,product_img')->where(['order_id'=>$order['order_id']])->order('add_time desc')->select();
        foreach ($order['goods'] as &$val){
            $val['product_img']=imgpath($val['product_img']);
        }
        get_api_result(200, '获取成功', $order);
    }
    /**
     * 获取订单列表
     */
    public function get_order_list(){
        $user_id=I('post.user_id');
        $page = I('request.page',1);
        $limit = I('request.limit',10);
        $type = I('type',0);//0|全部 1|待支付  2|已支付（待发货）  3|完成
        if(!$user_id){
            get_api_result(300, '未获取到用户信息');
        }
        $where=['order_main.user_id'=>$user_id];
        if($type==1){
            $where=array_merge($where,['order_main.status'=>0]);
        }elseif ($type==2){
            $where=array_merge($where,['order_main.status'=>['in',[2,3,6]]]);
        }elseif ($type==3){
            $where=array_merge($where,['order_main.status'=>['in',[4,5,7]]]);
        }
        $list=M('order_main')
            ->join('left join useraddress on useraddress.id = order_main.user_address_id')
            ->field('order_main.id as order_id,order_main.money,order_main.status,order_main.status')
            ->where($where)->order('order_main.addtime desc')->page($page,$limit)->select();
        if($list){
            foreach ($list as &$item){
                $item['goods']=M('order_product')->field('product_name,norms,summary,norms_price,norms_num,product_img,shop_user_id')->where(['order_id'=>$item['order_id']])->order('add_time desc')->select();
                foreach ($item['goods'] as &$val){
                    $val['product_img']=imgpath($val['product_img']);
                    $shop=M('user')->where(['userid'=>$val['shop_user_id']])->field('headimg,username,real_name')->find();
                    if($shop){
                        $shop['real_name']?$val['shop_name']=$shop['real_name']:$val['shop_name']=$shop['username'];
                        $val['shop_img']=imgpath($shop['headimg']);
                    }
                }
            }
            get_api_result(200, '获取成功', $list);
        }
        get_api_result(200, '获取成功', []);
    }
}