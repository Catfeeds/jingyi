<?php

namespace Admin\Controller;

use Think\Controller;

class ReportController extends CommonController
{

    public function _initialize()
    {
        parent::_initialize();
        if (session('admin_key_id') != 1) {
            if (!in_array('12', session('admin_key_auth'))) {
                session('[destroy]');
                $this->redirect('Login/index', array(), 1, '无权限...');
            }
        }
    }

    public function index()
    {
        $Model = M("user_report");
        $order = "a.addtime asc";
        $field = "a.*,b.tel as usertel,c.tel as beusertel";
        $join = "user as b ON a.report_userid=b.userid";
        $join1 = "user as c ON a.report_beuserid=c.userid";
        $list = $Model->alias("a")->field($field)->order($order)->join($join)->join($join1)->select();

//        foreach ($list as &$item){
//            $item['user_black']=0;
//            $result=M('user')->where(['userid'=>['in',[$item['report_userid'],$item['report_beuserid']]]])->where(['black'=>0])->select();
//            if($result){
//                $item['user_black']=1;
//            }
//        }
        foreach ($list as &$item){
            $item['user_status']=M('user')->where(['userid'=>$item['report_userid']])->getField('status');
            $item['base_status']=M('user')->where(['userid'=>$item['report_beuserid']])->getField('status');

        }

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
                ->setCellValue('B1', '举报人')
                ->setCellValue('C1', '被举报人')
                ->setCellValue('D1', '举报原因')
                ->setCellValue('E1', '举报贴子类型')
                ->setCellValue('F1', '举报时间')
                ->setCellValue('G1', '状态');

            foreach ($res as $key => $val) {
                $num = $key + 2;
                $report_type = '群活动';
                if ($val['report_type'] == 1) {
                    $report_type = '个人贴';
                } elseif ($val['report_type'] == 2) {
                    $report_type = '星球贴';
                }
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $num, $val['report_id'])
                    ->setCellValue('B' . $num, $val['usertel'])
                    ->setCellValue('C' . $num, $val['beusertel'])
                    ->setCellValue('D' . $num, $val['report_msg'])
                    ->setCellValue('E' . $num, $report_type)
                    ->setCellValue('F' . $num, date('Y-m-d H:i:s', $val['addtime']))
                    ->setCellValue('G' . $num, $val['status'] == 1 ? '待处理' : '已处理');
            }

            $objPHPExcel->getActiveSheet()->setTitle('Simple');
            $objPHPExcel->setActiveSheetIndex(0);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="举报信息.xlsx"');
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

        $this->display();
    }

    public function show()
    {
        $type = $_GET["type"];
        $posts_id = $_GET["postsid"];
        $UserModel = D("User");
        if ($type == 1) {  //个人贴
            $PersonalPostsModel = D("PersonalPosts");
            $res = $PersonalPostsModel->getpostsinfobypostsid($posts_id);
        } else if ($type == 2) {  //星球贴
            $PostsModel = D("Posts");
            $res = $PostsModel->getpostsinfobypostsid($posts_id);
        } else {

        }
        $res["usermsg"] = $UserModel->getusermsg1byuserid($res["userid"]);
        $this->assign('info', $res);
        $this->display();
    }

    public function del()
    {
        $id = $_POST["id"];
        $ReportModel = D("Report");
        $res1 = $ReportModel->delpost($id);
        if ($res1) {
            admin_log('删除举报，编号：'.$id);
            $this->ajaxReturn(1);
        } else {
            $this->ajaxReturn(0);
        }

    }

    /**
     * 删除帖子
     */
    public function delposts()
    {
        $id = $_POST["id"];
        $ReportModel = D("Report");
        $where = "report_id=" . $id;
        $res = $ReportModel->where($where)->find();
        if ($res["report_type"] == 1) {    //	1|个人贴  2|星球贴
            $keyModel = D("PersonalPosts");
            $res1 = $keyModel->delpost($res["report_postsid"]);
            admin_log('删除个人贴，编号：'.$res["report_postsid"]);
        } else if ($res["report_type"] == 2) {
            $keyModel = D("Posts");
            $res1 = $keyModel->delpost($res["report_postsid"]);
            admin_log('删除星球贴，编号：'.$res["report_postsid"]);
        }
        if ($res1) {
            $data["status"] = 2;
            $res2 = $ReportModel->editpost($where, $data);
            $this->ajaxReturn(1);
        } else {
            $this->ajaxReturn(0);
        }

    }

    //处理
    public function set_hot()
    {
        $Model = M("user_report");
        $id = I('id', 0, 'int');
        $status = I('status', 0, 'int');
        $data["status"] = $status;
        $where = "report_id=" . $id;
        $res = $Model->where($where)->save($data);
        if ($res) {
            admin_log('举报处理，编号：'.$id);
            $this->ajaxReturn($data);
        } else {
            $this->ajaxReturn(0);
        }
    }

    /**
     * 发送系统消息
     */
    public function system_msg()
    {
        $report_id = I('get.report_id');
        $Model = M("user_report");
        $field = "a.*,b.tel as usertel,c.tel as beusertel";
        $join = "user as b ON a.report_userid=b.userid";
        $join1 = "user as c ON a.report_beuserid=c.userid";
        $list = $Model->alias("a")->field($field)->join($join)->where(['a.report_id' => $report_id])->join($join1)->find();
        $this->assign('list', $list);

        if (IS_POST) {
            $data = I('post.');
            if (!$data['msg']) {
                echo json_encode(['code' => 201, 'msg' => '请输入内容']);
                exit;
            }

            if (!$data['user1'] && !$data['user2']) {
                echo json_encode(['code' => 201, 'msg' => '请至少选择一个用户']);
                exit;
            }

            $str = '';
            $user_id = [];
            if ($data['user1'] && $data['user2']) {
                $str = $data['user1'] . ',' . $data['user2'];
                $user_id = [$data['user1'], $data['user2']];
            } elseif ($data['user1']) {
                $str = "{$data['user1']}";
                $user_id = [$data['user1']];
            } elseif ($data['user2']) {
                $str = "{$data['user2']}";
                $user_id = [$data['user2']];
            }

            $result = $this->send_msg($str, $user_id, $data['msg']);

            if ($result) {
                admin_log('发送系统消息，接收者：'.$data['user1'].' '.$data['user2']);
                echo json_encode(['code' => 200, 'msg' => '发送成功']);
                exit;
            } else {
                echo json_encode(['code' => 201, 'msg' => '发送失败']);
                exit;
            }
        }
        $this->display();
    }

    public function send_msg($str, $user_id, $content, $title = '系统消息')
    {
        $data["to_userid"] = $str;
        $data["content"] = $content;
        $data["title"] = $title;
        $SystemMessageModel = D("SystemMessage");
        $res = $SystemMessageModel->addpost($data);
        $ChatWindowModel = D("ChatWindow");
        $type = 4;
        $option = "";
        $ischeck = $ChatWindowModel->ischeckwindow($type, $option);
        if (!$ischeck) {
            $data1["to_userid"] = $str;
            $data1["type"] = 4;
            $data1["content"] = $_POST["title"];
            $data1["is_reads"] = "";
            $data1["is_dels"] = "";
            $res1 = $ChatWindowModel->addpost($data1);
        } else {
            $data1["to_userid"] = $str;
            $data1["type"] = 4;
            $data1["content"] = $_POST["title"];
            $data1["is_reads"] = "";
            $data1["is_dels"] = "";
            $where1 = "type=4";
            $res1 = $ChatWindowModel->editpost($where1, $data1);
        }
        if ($res1 && $res) {
            $jpushdata["type"] = 2;
            $jpushdata["alias"] = $user_id;
            $jpushdata["Alert"] = "您的消息模块中有新信息，快去查看吧";
            $jpushdata["msg_content"] = "您的消息模块中有新信息，快去查看吧";
            $jpushdata["extras"]["messtype"] = 2;     //消息类型  1|消息模块 2|空间站模块  3|我的星球模块

            $JpushModel = D("Jpush");
            $messinfo = $JpushModel->sendmsg($jpushdata["type"], $jpushdata["alias"], $jpushdata["Alert"], $jpushdata["msg_content"], $jpushdata["extras"]);
            return true;
        } else {
            return false;
        }
    }

    /**
     * 用户启用、停用
     */
    public function user_status()
    {
        $userid = I('post.userid');
        $status=I('post.status');
        $result = M('user')->where(['userid'=>$userid])->save(['status'=>$status]);
        if($result){
            $opres = M('user')->where('userid=' . $userid)->find();
            $status==2?$type='冻结':$type='恢复';
            if($status==2){
                $res["type"]=2;  //对别名推送消息
                $res["alias"]=$userid;
                $res["Alert"]="您的账号已被冻结";
                $res["msg_content"]="您的账号已被冻结";
                $res["extras"]["messtype"]=4;     //消息类型  1|消息模块 2|空间站模块  3|我的星球模块 4 强制下线
                $JpushModel=D("Jpush");
                $JpushModel->sendmsg($res["type"],$res["alias"],$res["Alert"],$res["msg_content"],$res["extras"]);
            }
            $opdata["content"] = $type."用户账号，账号为：" . $opres["tel"] . "。";
            admin_log($opdata["content"]);
            D('UserLevel')->addUserGrow($userid,7);//成长值
            echo json_encode(['code' => 200, 'msg' => '操作成功']);
            exit;
        }else{
            echo json_encode(['code' => 201, 'msg' => '操作失败']);
            exit;
        }
    }

}