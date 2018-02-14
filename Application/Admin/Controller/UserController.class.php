<?php

namespace Admin\Controller;

use Common\Model\Question2Model;
use Think\Controller;

class UserController extends CommonController
{

    public function _initialize()
    {
        parent::_initialize();
        if (session('admin_key_id') != 1) {
            if (!in_array('7', session('admin_key_auth'))) {
                session('[destroy]');
                $this->redirect('Login/index', array(), 1, '无权限...');
            }
        }

    }

    public function index()
    {
        $where = $_GET["status"] ? "status=" . $_GET["status"] : "";
        $Model = M("user");
        $order = "user.userid desc";
        $field = "user.countrynum,user.tel,user.black,user.userid,user.addtime,user.status,real_name,user_planet.planet_name,user_planet.planet_id,user.number";
        $join = "left JOIN  user_planet on user.userid=user_planet.userid";
        $list = $Model->field($field)->where($where)->order($order)->join($join)->select();
        $this->assign('list', $list);
        $this->display();
    }

    /**
     * 拉黑用户
     */
    public function black()
    {
        $userid = I('post.userid');
        M()->startTrans();
        if ($userid) {
            //返还推荐人的推荐码使用次数
            $number_user_id = M('user')->where(['userid' => $userid])->getField('number_user_id');
            if ($number_user_id) {
                $number = M('user')->where(['userid' => $number_user_id])->setInc('number_user', 1);
                if (!$number) {
                    M()->rollback();
                    echo json_encode(['code' => 201, 'msg' => '操作失败']);
                    exit;
                }
            }
            //拉黑用户
            $res = M('user')->where(['userid' => $userid])->save(['black' => 1]);
            if (!$res) {
                M()->rollback();
                echo json_encode(['code' => 201, 'msg' => '操作失败']);
                exit;
            } else {
                M()->commit();
                if($number_user_id){
                    D('UserLevel')->addUserGrow($number_user_id,8);//减去推荐人的成长值
                }
                $msg["type"] = 2;  //对别名推送消息
                $msg["alias"] = $userid;
                $msg["Alert"] = "您的账号已被拉黑";
                $msg["msg_content"] = "您的账号已被拉黑";
                $msg["extras"]["messtype"] = 4;     //消息类型  1|消息模块 2|空间站模块  3|我的星球模块 4 强制下线
                $JpushModel = D("Jpush");
                $JpushModel->sendmsg($msg["type"], $msg["alias"], $msg["Alert"], $msg["msg_content"], $msg["extras"]);

                admin_log('拉黑用户,编号：' . $userid);
                echo json_encode(['code' => 200, 'msg' => '操作成功']);
                exit;
            }

        } else {
            M()->rollback();
            echo json_encode(['code' => 201, 'msg' => '数据错误']);
            exit;
        }


    }

    public function add()
    {
        $userid=I('get.userid');
        $tel='';
        if($userid){
            $tel=M('user')->where(['userid'=>$userid])->getField('tel');
            $this->assign('userid', $userid);
        }
        $this->assign('tel', $tel);
        $Model = M("hobby");
        $hobbylist = $Model->select();
        $this->assign('hobbylist', $hobbylist);
        $profession_signModel = M("profession_sign");
        $profession_signlist = $profession_signModel->select();
        $this->assign('profession_signlist', $profession_signlist);

        $countryModel = M("country");
        $countrylist = $countryModel->select();
        $this->assign('countrylist', $countrylist);


        $this->display();
    }

    public function addpost()
    {
        $Model = M('user');
        $data = $Model->create();
        $number_use = 0;
        if ($data['number']) {
            $number_use = M('user')->where(['number' => $data['number']])->getField('userid');
            $data['number_user_id'] = $number_use;
            $data['pid'] = $number_use;
            unset($data['number']);
        }

        if (empty($_POST["hobbyname"])) {
            echo "<div id='kk' style='display:none'>2</div>";
            $this->display("User/add");
        }
        for ($i = 0; $i < count($_POST["hobbyname"]); $i++) {
            $data["hobbyid"] .= "#" . $_POST["hobbyname"][$i] . "|";
        }
        if(!($data['username'])){
            $data['username']=$data['real_name'];
        }
        $data["status"] = 1;
        $data["phonecountrynum"] = $data["countrynum"];
        $data["phone"] = $data["tel"];
        $data["password"] = "123456";
        $data["addtime"] = time();
        $data["headimg"] = C("defaultheadimg");
        //生成推荐码
        $data['number'] = \uuid();
        if (empty($data["password"])) {
            echo "<div id='kk' style='display:none'>4</div>";
            $this->display("User/add");
        }
        $data["password"] = md5($data["password"]);

        $Model->startTrans();
        $res=$data['userid'];
        unset($data['userid']);
        $res3=true;
        if(!$res){//添加
            $ischeck = $Model->where("tel='" . $data["tel"] . "'")->find();
            if ($ischeck) {
                echo "<div id='kk' style='display:none'>3</div>";
                $this->display("User/add");
                exit;
            }
            $res = $Model->add($data); //添加数据
        }else{//修改
            $data['apply']=2;
            $res3=$Model->where(['userid'=>$res])->save($data);
        }

        //修改推荐者的使用次数
        $res2 = true;
        if ($number_use > 0) {
            $res2 = M('user')->where(['userid' => $number_use])->setInc('number_user', 1);
        }

        $EasemobModel = D("Easemob");
        $username = C("hx_app_name") . $res;
        $username1 = "kj" . C("hx_app_name") . $res;
        $password = C("hx_user_password");
        $hxres = $EasemobModel->register_hx($username, $username1, $password);
        if(!$hxres){
            $EasemobModel->delete_user($username);
            $hxres = $EasemobModel->register_hx($username, $username1, $password);
        }

        //$data1["user_hx_airship"]=$username1;
        $data1["user_hx"] = $username;
        $data1["user_hx_password"] = $password;
        $data1["user_hx_id"] = $hxres["request_str"]["entities"][0]["uuid"];
        //$data1["user_hx_airship_id"]=$hxres["request_kj_str"]["entities"][0]["uuid"];;
        $res1 = $Model->where("userid=" . $res)->save($data1);
        if ($res && $hxres["status"] && $res1 && $res2 && $res3) {
            $Model->commit();
            $opdata["content"] = "创建了用户。账号为:" . $data["tel"] . "。";
            admin_log($opdata["content"]);
            echo "<div id='kk' style='display:none'>1</div>";
            $this->display("User/add");
        } else {
            $Model->rollback();
            echo "<div id='kk' style='display:none'>2</div>";
            $this->display("User/add");
        }


    }


//ajax 验证
    public function checktel()
    {
        $msg["status"] = 'n';
        $msg["info"] = '账户已存在！';

        $tel = $_POST["param"];
        $Model = M("User");
        $where = "tel='" . $tel . "'";
        $res = $Model->where($where)->find();
        if (!$res) {
            $msg["status"] = 'y';
            $msg["info"] = '可注册！';
        }
        $this->ajaxReturn($msg);
    }

    /**
     * 验证推荐码
     */
    public function check_number()
    {
        $msg["status"] = 'n';
        $msg["info"] = '推荐码无效！';

        $number = $_POST["param"];
        $res = M("User")->where(['number' => $number])->where(['number_user' => ['lt', 11]])->find();
        if ($res) {
            $msg["status"] = 'y';
            $msg["info"] = '可使用！';
        }
        $this->ajaxReturn($msg);
    }


//流水明细
    public function wallet_water()
    {
        $id = I('get.id');
//        var_dump(I('get.'));exit;
        $info = M('wallet_water')->where('userid=' . $id)->select();
        $user_info = M('user')->where('userid=' . $id)->find();
        $this->assign("list", $info);
        $this->assign("userinfo", $user_info);
        $this->assign("userid", $id);

        if (I('get.type') == 1) {
            $res = $info;
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
                ->setCellValue('A1', '操作时间')
                ->setCellValue('B1', '流水类型')
                ->setCellValue('C1', '钱数');

            foreach ($res as $key => $val) {
                $num = $key + 2;
                $type = '未知';
                if ($val['type'] == 1) {
                    $type = '充值';
                } elseif ($val['type'] == 2) {
                    $type = '提现';
                } elseif ($val['type'] == 3) {
                    $type = '钱包支付';
                } elseif ($val['type'] == 4) {
                    $type = '商品退款';
                } elseif ($val['type'] == 5) {
                    $type = '系统赠送';
                }
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $num, date('Y-m-d H:i:s', $val['addtime']))
                    ->setCellValue('B' . $num, $type)
                    ->setCellValue('C' . $num, $val['moneynum']);


            }

            $objPHPExcel->getActiveSheet()->setTitle('Simple');
            $objPHPExcel->setActiveSheetIndex(0);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="用户流水信息.xlsx"');
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

    /**
     * 用户成长值列表
     */
    public function user_wallet_water()
    {
        $id = I('get.id');
        $User = M('userlevel_water'); // 实例化User对象
        $count = $User->where('userid=' . $id)->count();
        $Page = new \Think\Page($count, 10);
        $Page->setConfig('prev', '上一页');
        $Page->setConfig('next', '下一页');
        $show = $Page->show();
        $info = $User->where('userid=' . $id)->order('userlevel_id')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $sroce = $User->where('userid=' . $id)->getField('sum(sroce)');
        $this->assign('page', $show);// 赋值分页输出
        $user_info = M('user')->where('userid=' . $id)->find();
        $this->assign("list", $info);
        $this->assign("userinfo", $user_info);
        $this->assign("userid", $id);
        $this->assign("sroce", $sroce);
        $this->display();
    }

    /**
     * 调整成长值
     */
    public function user_wallet_water_edit()
    {
        $userid = I('get.id');
        $this->assign("userid", $userid);
//        var_dump($userid);exit;
        $res = false;
        if (IS_POST) {
            $data = I('post.');
            if (!$data['type']) {
                echo json_encode(['code' => 201, 'msg' => '请选择加/减']);
                exit;
            }
            if (!$data['msg']) {
                echo json_encode(['code' => 201, 'msg' => '请输入内容']);
                exit;
            }
            if (!is_numeric($data['value'])) {
                echo json_encode(['code' => 201, 'msg' => '请输入正确的分数']);
                exit;
            }
            if ($data['value'] <= 0) {
                echo json_encode(['code' => 201, 'msg' => '请输入正确的分数']);
                exit;
            }
            $data['msg'] = shielding($data['msg']);

            if ($data['type'] == 1) {
                unset($data['type']);
                $data['addtime'] = time();
                $data['sroce'] = $data['value'];
                unset($data['value']);
                unset($data['type']);
                $res = M('userlevel_water')->add($data);
            } elseif ($data['type'] == 2) {
                unset($data['type']);
                $data['addtime'] = time();
                $data['sroce'] = '-' . $data['value'];
                unset($data['value']);
                unset($data['type']);
                $res = M('userlevel_water')->add($data);
            }

            if ($res) {
                admin_log('调整用户成长值，用户编号：' . $userid);
                echo json_encode(['code' => 200, 'msg' => '添加成功']);
                exit;
            } else {
                echo json_encode(['code' => 201, 'msg' => '添加失败']);
                exit;
            }

        }
        $this->display();
    }

    public function dostop()
    {
        $id = I('post.id');
        $result = M('user')->where('userid=' . $id)->save(array('status' => 2));

        $opres = M('user')->where('userid=' . $id)->find();
        $opdata["content"] = "冻结了用户账号，账号为：" . $opres["tel"] . "。";
        admin_log($opdata["content"]);

        $this->ajaxReturn($result);
    }

    public function dostart()
    {
        $id = I('post.id');
        $result = M('user')->where('userid=' . $id)->save(array('status' => 1));

        $opres = M('user')->where('userid=' . $id)->find();
        $opdata["content"] = "恢复了用户账号，账号为：" . $opres["tel"] . "。";
        admin_log($opdata["content"]);

        $this->ajaxReturn($result);
    }


    public function show()
    {

        $Model = M("hobby");
        $hobbylist = $Model->select();
        $this->assign('hobbylist', $hobbylist);
        $profession_signModel = M("profession_sign");
        $profession_signlist = $profession_signModel->select();
        $this->assign('profession_signlist', $profession_signlist);

        $id = I('get.id');
        $userid = $id;
        $this->assign('userid', $userid);
        $type = $_GET["type"];

        $UserModel = M('User');
        $where = "user.userid=" . $id;
        $field = "user.*,profession_sign.name";
        $join = "left JOIN  profession_sign on user.professionsign_id=profession_sign.pro_id";


        $info = $UserModel->where($where)->join($join)->find();
        if (!empty($info["hobbyid"])) {
            $hobbyModel = M("hobby");
            $hobbyidstr = substr(str_replace('|', ",", str_replace('#', "", $info["hobbyid"])), 0, -1);
            $hobby = $hobbyModel->where("hobbyid in (" . $hobbyidstr . ")")->getField('hobbyname', true);
            $info["hobbyname"] = implode('/', $hobby);  //爱好名称
        }
        if (!empty($info["starsignid"])) {
            $starsignModel = M("star_sign");
            $starsignidstr = substr(str_replace('|', ",", str_replace('#', "", $info["starsignid"])), 0, -1);
            $starsign = $starsignModel->where("star_id in (" . $starsignidstr . ")")->getField('name', true);
            $info["starsignname"] = implode('/', $starsign);  //标签名称
        }
        if ($info["pid"] > 0) {
            $puserid = $UserModel->where("userid=" . $info["pid"])->find();
            $info["ptel"] = $puserid["tel"];
        }
        $QuestionModel = new Question2Model();
        $info["questionlist"] = $QuestionModel->getlist($info["tel"]);     //邀请问题信息
        $this->assign("info", $info);

        if ($type == 1) {
            $res = $info;
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
                ->setCellValue('A1', '昵称')
                ->setCellValue('B1', '账号')
                ->setCellValue('C1', '个性签名')
                ->setCellValue('D1', '性别')
                ->setCellValue('E1', '婚姻')
                ->setCellValue('F1', '联系手机')
                ->setCellValue('G1', '年龄')
                ->setCellValue('H1', '身高')
                ->setCellValue('I1', '体重')
                ->setCellValue('J1', '星座')
                ->setCellValue('K1', '职业')
                ->setCellValue('L1', '爱好')
                ->setCellValue('M1', '标签')
                ->setCellValue('N1', '注册时间')
                ->setCellValue('O1', '推荐者账号')
                ->setCellValue('P1', '引者语')
                ->setCellValue('A2', $res['username'] ? $res['username'] : '未填')
                ->setCellValue('B2', $res['countrynum'] . ' ' . $res['tel'])
                ->setCellValue('C2', $res['autograph'] ? $res['autograph'] : '这家伙很懒,什么也没写。')
                ->setCellValue('D2', $res['sex'] == 1 ? '男' : '女')
                ->setCellValue('E2', $res['marriage'])
                ->setCellValue('F2', $res['phone'] ? $res['countrynum'] . ' ' . $res['phone'] : $res['countrynum'] . ' ' . $res['tel'])
                ->setCellValue('G2', $res['age'] . '岁')
                ->setCellValue('H2', $res['height'] . 'cm')
                ->setCellValue('I2', $res["weight"] . 'KG')
                ->setCellValue('J2', $res["constellation"])
                ->setCellValue('K2', $res['name'])
                ->setCellValue('L2', $res['hobbyname'])
                ->setCellValue('M2', $res['starsignname'])
                ->setCellValue('N2', date('Y-m-d H:i:s', $res['addtime']))
                ->setCellValue('O2', $res['pid'] == 0 ? $res['ptel'] : '无')
                ->setCellValue('P2', $res['guide'])
                ->setCellValue('A4', '邀请问题')
                ->setCellValue('A5', '问题')
                ->setCellValue('B5', '回答');

            foreach ($res['questionlist'] as $key => $val) {
                $num = $key + 6;
                $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A' . $num, $val['question_name'])
                    ->setCellValue('B' . $num, $val['question_answer'] ? $val['question_answer'] : '无');
            }

            $objPHPExcel->getActiveSheet()->setTitle('Simple');
            $objPHPExcel->setActiveSheetIndex(0);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="用户信息.xlsx"');
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

//设置为会员
    public function member_set()
    {
        $id = I('post.id');
        $status = I('post.status');
        if ($status == 1) {
            $result = M('user')->where('userid=' . $id)->save(array('memberstatus' => 0));
        } else {
            $result = M('user')->where('userid=' . $id)->save(array('memberstatus' => 1));
        }

        $this->ajaxReturn($result);
    }

    public function doallstop()
    {
        $id = I('post.id');
        $result = M('user')->where('userid in (' . $id . ")")->save(array('status' => 2));

        $opres = M('user')->where('userid in (' . $id . ")")->getField('tel', true);
        $opresstr = implode('，', $opres);
        $opdata["content"] = "批量冻结了用户账号，账号为：" . $opresstr . "。";
        admin_log($opdata["content"]);
        $this->ajaxReturn($opres);
    }

    public function doallstart()
    {
        $id = I('post.id');
        $result = M('user')->where('userid in (' . $id . ")")->save(array('status' => 1));

        $opres = M('user')->where('userid in (' . $id . ")")->getField('tel', true);
        $opresstr = implode('，', $opres);
        $opdata["content"] = "批量恢复了用户账号，账号为：" . $opresstr . "。";
        admin_log($opdata["content"]);

        $this->ajaxReturn($result);
    }


    public function money()
    {
        $Model = M("userscoremsg");
        $order = "addtime desc";
        $where = "userid=1";//.$_GET["id"];
        $list = $Model->where($where)->order($order)->select();
        for ($i = 0; $i < count($list); $i++) {
            if ($list[$i]["score"] > 0) {
                $list[$i]["score"] = "+" . $list[$i]["score"];
            }
            $list[$i]["typename"] = $this->gettypename($list[$i]["type"]);
            $list[$i]["typename"] = $this->gettypename($list[$i]["type"]);
        }
        $this->assign('list', $list);
        $this->display();
    }

    private function gettypename($typeid)
    {
        switch ($typeid) {
            case 1:
                $typename = "下单奖励";
                break;
            case 2:
                $typename = "接单奖励";
                break;
            case 3:
                $typename = "分享";
                break;
            case 4:
                $typename = "评价";
                break;
        }
        return $typename;
    }


    public function planetshow()
    {

        $id = I('get.id');
        $Model = M('user_planet');
        $where = "a.planet_id=" . $id;
        $field = "a.*,c.images,b.province";
        $join = "left JOIN province as b on a.province_id=b.province_id";
        $join1 = "left JOIN backimg as c on a.backimg_id=c.id";
        $info = $Model->alias("a")->field($field)->where($where)->join($join)->join($join1)->find();
        $this->assign("info", $info);
        $this->display();
    }


    public function edit()
    {
        $Model = M("hobby");
        $hobbylist = $Model->select();
        $this->assign('hobbylist', $hobbylist);
        $profession_signModel = M("profession_sign");
        $profession_signlist = $profession_signModel->select();
        $this->assign('profession_signlist', $profession_signlist);
        $star_signModel = M("star_sign");
        $star_signlist = $star_signModel->select();
        $this->assign('star_signlist', $star_signlist);


        $countryModel = M("country");
        $countrylist = $countryModel->select();
        $this->assign('countrylist', $countrylist);

        $id = I('get.id');
        $UserModel = M('User');
        $where = "user.userid=" . $id;
        $field = "user.*,profession_sign.name";
        $join = "left JOIN  profession_sign on user.professionsign_id=profession_sign.pro_id";


        $info = $UserModel->where($where)->join($join)->find();
        if (!empty($info["hobbyid"])) {
            $hobbyModel = M("hobby");
            $hobbyidstr = substr(str_replace('|', ",", str_replace('#', "", $info["hobbyid"])), 0, -1);
            $info["hobbyid"] = explode(",", $hobbyidstr);
            $hobby = $hobbyModel->where("hobbyid in (" . $hobbyidstr . ")")->getField('hobbyname', true);
            $info["hobbyname"] = implode('/', $hobby);  //爱好名称
        }
        if (!empty($info["starsignid"])) {
            $starsignModel = M("star_sign");
            $starsignidstr = substr(str_replace('|', ",", str_replace('#', "", $info["starsignid"])), 0, -1);
            $info["starsignid"] = explode(",", $starsignidstr);
            $starsign = $starsignModel->where("star_id in (" . $starsignidstr . ")")->getField('name', true);
            $info["starsignname"] = implode('/', $starsign);  //标签名称
        }
        if ($info["pid"] > 0) {
            $puserid = $UserModel->where("userid=" . $info["pid"])->find();
            $info["ptel"] = $puserid["tel"];
        }
        $QuestionModel = M('question_answer');
        $info["questionlist"] = $QuestionModel->join('question on question.question_id=question_answer.question_id')->where(['question_answer.userid'=>$id])->select();     //邀请问题信息
        $this->assign("info", $info);

        $this->display();
    }


    public function editpost()
    {
        $Model = M('user');
        $questionModel = M("question_answer");
        $data = $Model->create();
        $dataques = I('post.');

        if (!empty($_POST["hobbyname"])) {
            for ($i = 0; $i < count($_POST["hobbyname"]); $i++) {
                $data["hobbyid"] .= "#" . $_POST["hobbyname"][$i] . "|";
            }
        }
        if (!empty($_POST["star_signname"])) {
            for ($i = 0; $i < count($_POST["star_signname"]); $i++) {
                $data["starsignid"] .= "#" . $_POST["star_signname"][$i] . "|";
            }
        }


        if (!empty($data["password"])) {
            $data["password"] = md5($data["password"]);
        } else {
            unset($data["password"]);
        }

        if (!empty($data["ptel"])) {

            $ischecktui = $Model->where("tel='" . $_POST["ptel"] . "'")->find();

            if ($ischecktui) {
                $data["pid"] = $ischecktui["userid"];
                $data["pcon"] = $ischecktui["pcon"] . "#" . $ischecktui["userid"] . "|";
            }
        }

        $where1 = "userid=" . $_POST["userid"];

        $res = $Model->where($where1)->save($data); //添加数据
        if ($res === 0) {
            $res = true;
        }
        $ischeckques = $questionModel->where(['userid'=>$_POST["userid"]])->find();
        if ($ischeckques) {
            $res1=0;
            foreach ($dataques as $key=>$item){
                if(is_numeric($key)){
                    $res1 += $questionModel->where(['userid'=>$_POST["userid"],'question_id'=>$key])->save(['answer'=>$item]);
                }
            }
            if ($res1 === 0) {
                $res1 = true;
            }
        } else {
            $res1 = true;
            $insert=[];
            foreach ($dataques as $key=>$item){
                if(is_numeric($key)) {
                    $insert[] = ['userid' => $_POST["userid"], 'question_id' => $key, 'answer' => $item];
                }
            }
            M('question_answer')->addAll($insert); //添加数据
        }
        if ($res && $res1) {
            $opdata["content"] = "修改了用户信息。账号为:" . $data["tel"] . "。";
            admin_log($opdata["content"]);
            echo "<div id='kk' style='display:none'>1</div>";
            $this->display("User/edit");
        } else {
            echo "<div id='kk' style='display:none'>2</div>";
            $this->display("User/edit");
        }


    }

    public function mycount()
    {
        $userid = $_GET["userid"];
        $type = $_GET["type"];

        $where = "userid=" . $_GET["userid"];

        $airshipModel = M("airship");
        $info["a"] = $airshipModel->where($where)->count();   //飞船发射数
        $activity_join_orderModel = M("activity_join_order");
        $info["b"] = $activity_join_orderModel->where($where)->count();   //用户参与活动数量
        $personal_postsModel = M("personal_posts");
        $info["c"] = $personal_postsModel->where($where)->count();   //个人贴
        $user_planet_postsModel = M("user_planet_posts");
        $info["d"] = $user_planet_postsModel->where($where)->count();   //星球贴
        $order_mainModel = M("order_main");
        $info["e"] = $order_mainModel->where($where)->count();   //下单数量
        $user_friendModel = M("user_friend");
        $where1 = "(userid=" . $_GET["userid"] . " or user_friend_id=" . $_GET["userid"] . ") and agree_status=1";
        $info["f"] = $user_friendModel->where($where1)->count();   //用户好友数量


        $user_planet_followModel = M("user_planet_follow");
        $where2 = "planet_userid=" . $_GET["userid"];
        $info["g"] = $user_planet_followModel->where($where2)->count();   //用户粉丝数量

        /*
        $user_planet_posts_likeModel=M("user_planet_posts_like");
        $where2="planet_userid=".$_GET["userid"];
        $info["h"]=$user_planet_posts_likeModel->where($where2)->count();   //星球贴点赞数量

        $user_planet_posts_likeModel=M("user_planet_posts_like");
        $where2="planet_userid=".$_GET["userid"];
        $info["i"]=$user_planet_posts_likeModel->where($where2)->count();   //个人贴点赞数量
        */
        $this->assign('info', $info);
        $this->assign('userid', $userid);

        if ($type == 1) {
            $res = $info;
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
                ->setCellValue('A1', '个人贴发帖数')
                ->setCellValue('B1', '星球贴发帖数')
                ->setCellValue('C1', '活动参与数')
                ->setCellValue('D1', '好友数量')
                ->setCellValue('E1', '粉丝数量')
                ->setCellValue('F1', '订单数')
                ->setCellValue('G1', '飞船发射数')
                ->setCellValue('A2', $res['c'])
                ->setCellValue('B2', $res['d'])
                ->setCellValue('C2', $res['b'])
                ->setCellValue('D2', $res['f'])
                ->setCellValue('E2', $res['g'])
                ->setCellValue('F2', $res['e'])
                ->setCellValue('G2', $res['a']);

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
        $this->display();
    }

    /**
     * 导出个人信息详情
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
        header('Content-Disposition: attachment;filename="个人信息.xlsx"');
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

    /**
     * 用户成长值列表
     */
    public function black_list()
    {
        $count = M('user')->where(['black' => 1])->count();
        $Page = new \Think\Page($count, 10);
        $Page->setConfig('prev', '上一页');
        $Page->setConfig('next', '下一页');
        $show = $Page->show();
        $info = M('user')->where(['black' => 1])->order('userid desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('page', $show);// 赋值分页输出
        $this->assign("list", $info);
        $this->assign("count", $count);
        $this->display();
    }

    /**
     * 用户申请
     */
    public function apply()
    {
        $Model = M('user');
        $where = ['add_status'=>1];
        $order = "userid asc";
        $count = $Model->where($where)->count();
        $limit = 10;
        $Page = new \Think\Page($count, $limit);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $Page->setConfig('prev','上一页');
        $Page->setConfig('next','下一页');
        $show = $Page->show();// 分页显示输出
        $limit1 = $Page->firstRow . ',' . $Page->listRows;
        $list = $Model->where($where)->limit($limit1)->order($order)->select();
        $this->assign('list', $list);
        $this->assign('page', $show);// 赋值分页输出
        $this->display();
    }
    /**
     * 用户申请驳回
     */
    public function apply_fail(){
        $userid=I('post.userid');
        $res=M('user')->where(['userid'=>$userid])->find();
        $str=M()->getLastSql();
        if(!$res){
            echo json_encode(['code'=>201,'msg'=>'获取数据失败',$str]);
            exit;
        }
        $result=M('user')->where(['userid'=>$userid])->save(['apply'=>1]);
        $str=M()->getLastSql();
        if($result){
            echo json_encode(['code'=>200,'msg'=>'操作成功']);
            exit;
        }else{
            echo json_encode(['code'=>201,'msg'=>'操作失败',$str]);
            exit;
        }
    }
    /**
     * 问卷详情
     */
    public function apply_info(){
        $userid=I('get.userid');
        if(!$userid){
            $this->error('错误');
        }
        $question=M('question')
            ->join('question_answer on question_answer.question_id=question.question_id')
            ->where(['question_answer.userid'=>$userid])->select();
        $this->assign('question', $question);
        $this->display();
    }
}