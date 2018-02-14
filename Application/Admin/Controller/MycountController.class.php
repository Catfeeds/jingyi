<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/8
 * Time: 11:21
 */
namespace Admin\Controller;
use Think\Controller;
class MycountController extends CommonController
{

    public function _initialize()
    {
        parent::_initialize();
        if (session('admin_key_id') != 1) {
            if (!in_array('16', session('admin_key_auth'))) {
                session('[destroy]');
                $this->redirect('Login/index', array(), 1, '无权限...');
            }
        }
    }

    public function mycount(){
        $airshipModel=M("airship");
		$info["a"]=$airshipModel->count();   //飞船发射数
		$activityModel=M("activity");
		$info["b"]=$activityModel->count();   //组织活动数量
		$where1="type=1";  //官方活动
		$where2="type=2";  //群活动
		$info["c"]=$activityModel->where($where1)->count();  
		$info["d"]=$activityModel->where($where2)->count(); 
		$personal_postsModel=M("personal_posts");
		$info["e"]=$personal_postsModel->count();   //个人贴
		$user_planet_postsModel=M("user_planet_posts");
		$info["f"]=$user_planet_postsModel->count();   //星球贴
		$order_mainModel=M("order_main");
		$info["g"]=$order_mainModel->count();   //下单数量
		$where3="status=0";
		$where4="status=1";
		$where5="status=2";
		$where6="status=3";
		$where7="status=4";
		$where8="status=5";
		$where9="status=6";
		$where10="status=7";
		$info["h"]=$order_mainModel->where($where3)->count();   //下单数量
		$info["i"]=$order_mainModel->where($where4)->count();   //下单数量
		$info["j"]=$order_mainModel->where($where5)->count();   //下单数量
		$info["k"]=$order_mainModel->where($where6)->count();   //下单数量
		$info["l"]=$order_mainModel->where($where7)->count();   //下单数量
		$info["m"]=$order_mainModel->where($where8)->count();   //下单数量
		$info["n"]=$order_mainModel->where($where9)->count();   //下单数量
		$info["o"]=$order_mainModel->where($where10)->count();   //下单数量
		$userModel=M("user");
		$info["p"]=$userModel->count();   //注册用户
		$this->assign('info',$info);
        $type=$_GET["type"];
        if($type==1){
            $res=$info;
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
                ->setCellValue('A1', '组织活动数量')
                ->setCellValue('B1', '官方活动数量')
                ->setCellValue('C1', '群活动数量')
                ->setCellValue('D1', '个人贴数量')
                ->setCellValue('E1', '星球贴数量')
                ->setCellValue('F1', '下单数量')
                ->setCellValue('G1', '订单总数')
                ->setCellValue('H1', '注册用户')

                ->setCellValue('A2', $res['a'])
                ->setCellValue('B2', $res['b'])
                ->setCellValue('C2', $res['c'])
                ->setCellValue('D2', $res['d'])
                ->setCellValue('E2', $res['e'])
                ->setCellValue('F2', $res['f'])
                ->setCellValue('G2', $res['g'])
                ->setCellValue('H2', $res['h'])
                ->setCellValue('I2', $res["p"]);

            $objPHPExcel->getActiveSheet()->setTitle('Simple');
            $objPHPExcel->setActiveSheetIndex(0);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="统计管理.xlsx"');
            header('Cache-Control: max-age=0');
            header('Cache-Control: max-age=1');
            header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
            header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header ('Pragma: public'); // HTTP/1.0
            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $objWriter->save('php://output');
            exit;
        }
        $this->display();
    }

  
}