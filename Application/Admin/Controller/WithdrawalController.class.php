<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/11
 * Time: 13:46
 */

namespace Admin\Controller;

use Think\Controller;

class WithdrawalController extends CommonController
{
    public function _initialize()
    {
        parent::_initialize();
        if (session('admin_key_id') != 1) {
            if (!in_array('14', session('admin_key_auth'))) {
                session('[destroy]');
                $this->redirect('Login/index', array(), 1, '无权限...');
            }
        }
    }

    public function index()
    {
        $Model = M('withdrawal');
        $where = "a.id>0";
        $where .= $_GET["begintime"] ? " and a.addtime>=" . strtotime($_GET["begintime"]) : "";
        $where .= $_GET["endtime"] ? " and a.addtime<=" . (strtotime($_GET["endtime"]) + 60 * 60 * 24) : "";
        $order = "addtime desc";

        $field = "a.cardbank,a.cardnum,a.cardusername,a.moneynum,a.addtime,b.tel,a.id,a.result,a.result_msg";
        $join = "user as b ON a.userid=b.userid";
        $count = $Model->alias("a")->field($field)->where($where)->join($join)->count();
        $limit = 10;

        $Page = new \Think\Page($count, $limit);// 实例化分页类 传入总记录数和每页显示的记录数(25)

        $Page->setConfig('header', '共%TOTAL_ROW%条');
        $Page->setConfig('first', '首页');
        $Page->setConfig('last', '共%TOTAL_PAGE%页');
        $Page->setConfig('prev', '上一页');
        $Page->setConfig('next', '下一页');
        $Page->setConfig('link', 'indexpagenumb');//pagenumb 会替换成页码
        $Page->setConfig('theme', '%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
        $show = $Page->show();// 分页显示输出
        $limit1 = $Page->firstRow . ',' . $Page->listRows;
        $list = $Model->alias("a")->field($field)->where($where)->join($join)->limit($limit1)->order($order)->select();
        $this->assign('list', $list);
        $this->assign('page', $show);// 赋值分页输出

        $this->assign('begintime', $_GET["begintime"]);
        $this->assign('endtime', $_GET["endtime"]);
        $this->assign('business_name', $_GET["business_name"]);
        $this->display();
    }

    // 导出学校信息，并且生成Excel文件
    public function excel_to()
    {
        $where='';
        $Model = M('withdrawal');
        $where .= $_GET["begintime"] ? " and addtime>=" . strtotime($_GET["begintime"]) : "";
        $where .= $_GET["endtime"] ? " and addtime<=" . strtotime($_GET["endtime"]) : "";
        $order = "addtime desc";

        $field = "cardbank,cardnum,cardusername,moneynum,FROM_UNIXTIME(addtime,'%Y年%m月%d')";
        $data = $Model->field($field)->where($where)->limit($limit1)->order($order)->select();

        $name = "用户提现信息表";  //导出的excel文件的名称
        $title = [
            '开户银行', '银行卡号', '开户人名称', '金额(￥)', '申请时间'
        ];
        $this->excelExport($data, $title, $name);
    }

    // 数字转字母
    function getLetter($num)
    {
        $str = "$num";
        $num = intval($num);
        if ($num <= 26) {
            $ret = chr(ord('A') + intval($str) - 1);
        } else {
            $first_str = chr(ord('A') + intval(floor($num / 26)) - 1);
            $second_str = chr(ord('A') + intval($num % 26) - 1);
            if ($num % 26 == 0) {
                $first_str = chr(ord('A') + intval(floor($num / 26)) - 2);
                $second_str = chr(ord('A') + intval($num % 26) + 25);
            }
            $ret = $first_str . $second_str;
        }
        return $ret;
    }


// 数据表导出生成Excel文件
    function excelExport($data, $title = null, $name = null)
    {
        import("Org.Util.PHPExcel");
        $PHPExcel = new \PHPExcel();
        if (!is_null($title)) {
            array_unshift($data, $title);
        }

        if (is_null($name)) {
            $name = time();
        }

        foreach ($data as $k => $v) {
            for ($i = 1; $i <= count($v); $i++) {
                $tr = $this->getLetter($i) . ($k + 1);
                if ($value == null) {
                    $value = '';
                }

                $buffer[$tr] = array_values($v)[$i - 1];

                $PHPExcel->getActiveSheet()->setTitle('Simple')->setCellValue($tr, array_values($v)[$i - 1]);

            }
        }

        $PHPExcel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $name . '.xls"'); //文件名称
        header('Cache-Control: max-age=0');
        $result = \PHPExcel_IOFactory::createWriter($PHPExcel, 'Excel2007');
        $result->save('php://output');
    }

    /**
     * 审核
     */
    public function examine()
    {
        $id = I('get.id');
        if (IS_POST) {
            $data = I('post.');
            if (!$data['id']) {
                echo json_encode(['code' => 201, 'msg' => '数据提交错误']);
                exit;
            }
            if (!$data['result']) {
                echo json_encode(['code' => 201, 'msg' => '请选择审核操作']);
                exit;
            }
            $id = $data['id'];
            unset($data['id']);
            $data['dotime'] = time();
            $data['adminid'] = Session('admin_key_id');
            $res = M('withdrawal')->where(['id' => $id])->save($data);
            if ($res) {
                $data['result']==1?$type='通过':$type='驳回';
                admin_log('提现申请审核'.$type.',编号为：'.$id);
                echo json_encode(['code' => 200, 'msg' => '操作成功']);
                exit;
            } else {
                echo json_encode(['code' => 201, 'msg' => '操作失败']);
                exit;
            }
        }
        $this->assign('id', $id);
        $this->display();
    }


}