<?php

namespace Admin\Controller;

use Think\Controller;

class OrderController extends CommonController
{

    public function _initialize()
    {
        parent::_initialize();
        if (session('admin_key_id') != 1) {
            if (!in_array('10', session('admin_key_auth'))) {
                session('[destroy]');
                $this->redirect('Login/index', array(), 1, '无权限...');
            }
        }
    }

    public function lst()
    {
        $Model = M("order_main");
        $where = "order_main.id<>0";
        $where .= $_GET["begintime"] ? " and order_main.addtime>=" . strtotime($_GET["begintime"]) : "";
        $where .= $_GET["endtime"] ? " and order_main.addtime<=" . (strtotime($_GET["endtime"]) + 60 * 60 * 24) : "";
        $where .= $_GET["subcode"] ? " and order_main.subcode='" . $_GET["subcode"] . "'" : "";
        $where .= $_GET["status"] ? " and order_main.status='" . $_GET["status"] . "'" : "";//print_r($_GET["status"]);die;
        $order = "order_main.addtime desc";
        $group = "order_main.subcode";//主订单号


        // 查询满足要求的总记录数 $map表示查询条件
        $subsql = $Model->where($where)->select(false);

        $join = "user as c ON c.userid=a.userid";
        $field = "a.*,c.tel,c.userid";
        $model1 = M();
        $list = $model1->table("(" . $subsql . ') as a')->field($field)->join($join)->select();

        $OrderProductModel = D("OrderProduct");
        for ($i = 0; $i < count($list); $i++) {
            $list[$i]["productmsg"] = $OrderProductModel->getproductlistbycode($list[$i]["subcode"]);
            $list[$i]["allpricemsg"] = $OrderProductModel->getproductpricebycode($list[$i]["subcode"]);
        }
        //echo $Model->getLastSql();die;
        $this->assign('list', $list);// 赋值数据集
        $this->assign('begin', I('begintime'));
        $this->assign('end', I('endtime'));
        $this->assign('status', $_GET["status"]);

        $this->display();
    }

    public function add()
    {
        $id = I('id');
        $data = M('Send')->select();
        $this->assign('id', $id);
        $this->assign('data', $data);
        $this->display();
    }

    public function addpost()
    {
        $model = M('Order_main');
        $id = I('id');
        $send_id = I('send_id');
        $send_no = I('send_no');
        $data = [
            "send_id" => $send_id,
            "send_no" => $send_no,
            "status" => 3,
        ];
        $row = $model->where(array('id' => $id))->save($data);
        if ($row) {
            admin_log('订单发货,订单编号：'.$id);
            echo "<div id='kk' style='display:none'>1</div>";
            $this->display("Order/add");
        } else {
            echo "<div id='kk' style='display:none'>2</div>";
            $this->display("Order/add");
        }

    }

    //订单详情
    public function detail()
    {
        $subcode = I('id');
        $orderModel = D("Order");
        $res = $orderModel->getOrderInfoBySubcode($subcode);
        $this->assign('res', $res);
        $this->display();
    }

    public function delete()
    {
        $model = M('Order_main');
        $id=I('id');
        //$tdata = $tmodel->join($join)->find($id);
        if ($model->delete(I('id', 0)) !== FALSE) {
            admin_log('删除订单，订单编号：'.$id);
            $row = 1;
        } else {
            $row = 2;
        }
        $this->ajaxReturn($row);
    }

    /**
     * 订单退款
     */
    public function refund()
    {
        $id = I('post.id');
        //修改订单状态
        $OrderModel = M('order_main');
        $where = "id=" . $id;
        $info = $OrderModel->where($where)->find();
        $data['status'] = 7;
        $res = $OrderModel->where($where)->save($data);
        //订单金额查询
        $money = $this->money($info['subcode']);
        //退款记录
        $WalletWaterModel = M('wallet_water');
        $price['type'] = 4;                 //   4|商品退款
        $price['moneynum'] = $money;        //金额
        $price['userid'] = $info['userid']; //用户id
        $price['addtime'] = time();          //添加时间
        $res2 = $WalletWaterModel->add($price);
        if ($res && $res2) {
            admin_log('订单退款，订单编号：'.$id);
            $this->ajaxReturn(1);
        } else {
            $this->ajaxReturn(0);
        }
    }

    /**
     * 订单拒绝退款
     */
    public function no_refund()
    {
        $id = I('post.id');
        //修改订单状态
        $OrderModel = M('order_main');
        $where = "id=" . $id;
        $data['status'] = 2;//代发货
        $res = $OrderModel->where($where)->save($data);
        if ($res) {
            admin_log('订单拒绝退款，订单编号：'.$id);
            $this->ajaxReturn(1);
        } else {
            $this->ajaxReturn(0);
        }
    }

    /**
     * 订单金额查询
     */
    private function money($subcode)
    {
        $Model = M('order_product');
        $where = "subcode='" . $subcode . "'";
        $res = $Model->where($where)->find();
        return $res['product_price'];
    }

    /**
     * 导出订单详情
     */
    public function dum_detail()
    {
        $subcode = I('subcode');
        $orderModel = D("Order");
        $res = $orderModel->getOrderInfoBySubcode($subcode);

        vendor("PHPExcel.Classes.PHPExcel");
        $objPHPExcel = new \PHPExcel();
        $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
            ->setLastModifiedBy("Maarten Balliauw")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Test result file");

        $status = '';
        if ($res['status'] == 0) {
            $status = '待支付';
        } elseif ($res['status'] == 1) {
            $status = '取消订单';
        } elseif ($res['status'] == 2) {
            $status = '待发货';
        } elseif ($res['status'] == 3) {
            $status = '收货中';
        } elseif ($res['status'] == 4) {
            $status = '确认收货';
        } elseif ($res['status'] == 5) {
            $status = '评价完成';
        } elseif ($res['status'] == 6) {
            $status = '退款中';
        } elseif ($res['status'] == 7) {
            $status = '退款完成';
        }
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '订单号')
            ->setCellValue('B1', '收货人名称')
            ->setCellValue('C1', '收货人电话')
            ->setCellValue('D1', '收货人地址')
            ->setCellValue('E1', '下单时间')
            ->setCellValue('F1', '物流信息')
            ->setCellValue('G1', '物流单号')
            ->setCellValue('H1', '应收价格')
            ->setCellValue('I1', '实际收入')
            ->setCellValue('J1', '状态')
            ->setCellValue('A2', $res['subcode'])
            ->setCellValue('B2', $res['username'])
            ->setCellValue('C2', $res['usertel'])
            ->setCellValue('D2', $res['provincename'] . '-' . $res['cityname'] . '-' . $res['countyname'] . '-' . $res['address'])
            ->setCellValue('E2', $res['addtime'] ? date('Y-m-d H:i:s', $res['addtime']) : '')
            ->setCellValue('F2', $res['sendname'])
            ->setCellValue('G2', $res['send_no'])
            ->setCellValue('H2', '￥' . $res["countmsg"]["allprice"])
            ->setCellValue('I2', '￥' . $res["countmsg"]["allprice"])
            ->setCellValue('J2', $status)
            ->setCellValue('A4', '商品详情')
            ->setCellValue('A5', '商品名称')
            ->setCellValue('B5', '购买价格')
            ->setCellValue('C5', '购买数量')
            ->setCellValue('D5', '总计')
            ->setCellValue('E5', '实收金额');

        foreach ($res['productmsg'] as $key => $val) {
            $num = $key + 6;
            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A' . $num, $val['product_name'])
                ->setCellValue('B' . $num, '￥' . $val['product_price'])
                ->setCellValue('C' . $num, $val['product_num'])
                ->setCellValue('D' . $num, '￥' . ($val['product_price'] * $val['product_num'] + $val['product_freight']))
                ->setCellValue('E' . $num, '￥' . ($val['product_price'] * $val['product_num'] + $val['product_freight']));
        }

        $objPHPExcel->getActiveSheet()->setTitle('Simple');
        $objPHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="订单详情.xlsx"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');
        exit;
    }
}
