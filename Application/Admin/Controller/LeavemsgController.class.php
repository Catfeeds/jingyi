<?php

namespace Admin\Controller;

use Think\Controller;

class LeavemsgController extends CommonController
{

    public function _initialize()
    {
        parent::_initialize();
        if (session('admin_key_id') != 1) {
            if (!in_array('11', session('admin_key_auth'))) {
                session('[destroy]');
                $this->redirect('Login/index', array(), 1, '无权限...');
            }
        }

    }

    public function index()
    {
        $LeavemsgModel = D("Leavemsg");
        $list = $LeavemsgModel->getlist();
        $this->assign('list', $list);
        if (I('get.type') == 1) {
            $res = $list;
            vendor("PHPExcel.Classes.PHPExcel");
            $objPHPExcel = new \PHPExcel();
            $objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
                ->setLastModifiedBy("Maarten Balliauw")
                ->setTitle("Office 2007 XLSX Test Document")
                ->setSubject("Office 2007 XLSX Test Document")
                ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
                ->setKeywords("office 2007 openxml php")
                ->setCategory("Test result file");


            $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', '编号')
                ->setCellValue('B1', '用户账号')
                ->setCellValue('C1', '内容')
                ->setCellValue('D1', '时间');

            foreach ($res as $key => $val) {
                $num = $key + 2;
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $num, $val['leavemsg_id'])
                    ->setCellValue('B' . $num, $val['tel'])
                    ->setCellValue('C' . $num, $val['message'])
                    ->setCellValue('D' . $num, date('Y-m-d H:i:s', $val['addtime']));
            }

            $objPHPExcel->getActiveSheet()->setTitle('Simple');
            $objPHPExcel->setActiveSheetIndex(0);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="用户反馈.xlsx"');
            header('Cache-Control: max-age=0');
            header('Cache-Control: max-age=1');
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
            header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header('Pragma: public'); // HTTP/1.0
            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save('php://output');
            $this->display();
        }
        $this->display();
    }

    public function del()
    {
        $id = $_POST["id"];
        $LeavemsgModel = D("Leavemsg");
        $res1 = $LeavemsgModel->delpost($id);
        if ($res1) {
            admin_log('删除留言，编号：'.$id);
            $this->ajaxReturn(1);
        } else {
            $this->ajaxReturn(0);
        }

    }

}