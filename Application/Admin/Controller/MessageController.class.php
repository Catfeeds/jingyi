<?php

namespace Admin\Controller;

use Think\Controller;

class MessageController extends CommonController
{

    public function _initialize()
    {
        parent::_initialize();
        if (session('admin_key_id') != 1) {
            if (!in_array('13', session('admin_key_auth'))) {
                session('[destroy]');
                $this->redirect('Login/index', array(), 1, '无权限...');
            }
        }

    }

    public function index()
    {

        $this->display("add_all");
    }

    public function addpost()
    {

        $userModel = M("user");
        $useridlist = $userModel->where(['status'=>1,'system_msg'=>0])->getField('userid', true);

        if (count($useridlist) == 0) {
            echo "<div id='kk' style='display:none'>3</div>";
            $this->display("add_all");

            die;
        }
        $str = "";
        $user_id=[];
        for ($i = 0; $i < count($useridlist); $i++) {
            $str .= "[" . $useridlist[$i] . "]";
            $user_id[]=$useridlist[$i];
        }
        $data["to_userid"] = $str;
        $data["content"] = $_POST["msg"];
        $data["title"] = $_POST["title"];
        $data['content']=shielding($data['content']);
        $data['title']=shielding($data['title']);

        $SystemMessageModel = D("SystemMessage");
        $res = $SystemMessageModel->addpost($data);
        $ChatWindowModel = D("ChatWindow");
        $type = 4;
        $option = "";
        $ischeck = $ChatWindowModel->ischeckwindow($type, $option);
        if (!$ischeck) {
            $data1["to_userid"] = $str;
            $data1["type"] = 4;
            $data1["content"] = shielding($_POST["title"]);
            $data1["is_reads"] = "";
            $data1["is_dels"] = "";
            $res1 = $ChatWindowModel->addpost($data1);
        } else {
            $data1["to_userid"] = $str;
            $data1["type"] = 4;
            $data1["content"] = shielding($_POST["title"]);
            $data1["is_reads"] = "";
            $data1["is_dels"] = "";
            $where1 = "type=4";
            $res1 = $ChatWindowModel->editpost($where1, $data1);
        }

        if ($res1 && $res) {
            $JpushModel = D("Jpush");
//            $jpushdata["type"] = 1;  //对全体推送消息
            $jpushdata["type"] = 2;

            $jpushdata["alias"]=$user_id;

            $jpushdata["Alert"] = "您的消息模块中有新信息，快去查看吧";
            $jpushdata["msg_content"] = "您的消息模块中有新信息，快去查看吧";
            $jpushdata["extras"]["messtype"] = 2;     //消息类型  1|消息模块 2|空间站模块  3|我的星球模块


            $JpushModel = D("Jpush");
            $messinfo = $JpushModel->sendmsg($jpushdata["type"], $jpushdata["alias"], $jpushdata["Alert"], $jpushdata["msg_content"], $jpushdata["extras"]);
            admin_log('群推系统消息');
            echo "<div id='kk' style='display:none'>1</div>";
            $this->display("add_all");
        } else {
            echo "<div id='kk' style='display:none'>2</div>";
            $this->display("add_all");
        }


    }


}