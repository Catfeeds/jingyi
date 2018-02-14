<?php

namespace Admin\Controller;

use Think\Controller;

class OfficialController extends CommonController
{

    public function _initialize()
    {
        parent::_initialize();
        if (session('admin_key_id') != 1) {
            if (!in_array('9', session('admin_key_auth'))) {
                session('[destroy]');
                $this->redirect('Login/index', array(), 1, '无权限...');
            }
        }

    }

    public function index()
    {
        //$where=" type=1 ";
        //$where.=$_GET["status"]?" and status=".$_GET["status"]:"";
        $field=$where=$join='';
        $Model = M("official_posts");
        $order = "addtime desc";
        $list = $Model->field($field)->where($where)->order($order)->join($join)->select();
        $this->assign('list', $list);
        $this->display();
    }

    public function add()
    {
        $this->display();
    }

    public function addpost()
    {
        $Model = M('official_posts');
        $Model1 = M('official_posts_file');
        $data = $Model->create();
        $data["adminid"] = session("admin_key_id");
        $data["addtime"] = time();
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 1024 * 1024 * 20;
        if ($data["type"] == 1) {
            $upload->exts = array('jpg', 'gif', 'png', 'jpeg');  //图片
        } else if ($data["type"] == 2) {
            $upload->exts = array('mp3', 'wma', 'flac', 'ape', 'wmv', 'mp4', 'jpg', 'gif', 'png', 'jpeg'); //视频
        } else {
            $upload->exts = array('mp3', 'mp4', 'wav', 'jpg', 'gif', 'png', 'jpeg');  //音频
        }

        $upload->rootPath = './'; // 设置附件上传目录
        $upload->savePath = './Public/upload/official/'; // 设置附件上传目录
        $upload->saveName = 'com_create_guid';
        //$upload-> saveName  =   array('uniqid','');
        $upload->ischeckfile = true;  //必须上传文件

        $info = $upload->upload(array('images' => $_FILES['images']));
        if ($data["type"] == 1) {
            $info1 = $upload->upload(array('imgfiles' => $_FILES['imgfiles']));
        } else if ($data["type"] == 2) {
            $info3 = $upload->upload(array('vedioimages' => $_FILES['vedioimages']));
            $info4 = $upload->upload(array('shipin' => $_FILES['shipin']));
        } else {
            $info2 = $upload->upload(array('yinpin' => $_FILES['yinpin']));
        }

        if (!$info) { // 上传错误提示错误信息
            //$this->error($upload->getError());
        } else { //上传成功获取上传文件信息
            foreach ($info as $file) {
                $data[$file['key']] = $file['savepath'] . $file['savename'];
            }
        } // 保存表单数据包括附件数据<br />

        if (!$info1) { // 上传错误提示错误信息
            //$this->error($upload->getError());
        } else { //上传成功获取上传文件信息
            $i = 0;
            foreach ($info1 as $file) {
                $data1[$file['key']][$i] = $file['savepath'] . $file['savename'];
                $i++;
            }
        } // 保存表单数据包括附件数据<br />
        if (!$info2) { // 上传错误提示错误信息
            //$this->error($upload->getError());
        } else { //上传成功获取上传文件信息
            foreach ($info2 as $file) {
                $data1[$file['key']] = $file['savepath'] . $file['savename'];
            }
            $data["vedioimages"] = "./Public/defaultimg/official/img_sound_recording.png";
        } // 保存表单数据包括附件数据<br />
        if (!$info3) { // 上传错误提示错误信息
            //$this->error($upload->getError());
        } else { //上传成功获取上传文件信息
            foreach ($info3 as $file) {
                $data[$file['key']] = $file['savepath'] . $file['savename'];
            }
        } // 保存表单数据包括附件数据<br />
        if (!$info4) { // 上传错误提示错误信息
            //$this->error($upload->getError());
        } else { //上传成功获取上传文件信息
            foreach ($info4 as $file) {
                $data1[$file['key']] = $file['savepath'] . $file['savename'];
            }
        } // 保存表单数据包括附件数据<br />

        $res = $Model->add($data); //添加数据
        if ($data["type"] == 1) {
            for ($i = 0; $i < count($data1["imgfiles"]); $i++) {
                $data2["uploadurl"] = $data1["imgfiles"][$i];
                $data2["type"] = $data["type"];
                $data2["posts_id"] = $res;
                $data2["addtime"] = time();
                $res1 = $Model1->add($data2); //添加数据
            }

        } else if ($data["type"] == 2) {
            $data2["uploadurl"] = $data1["shipin"];
            $data2["type"] = $data["type"];
            $data2["posts_id"] = $res;
            $data2["addtime"] = time();
            $res1 = $Model1->add($data2); //添加数据

        } else {
            $data2["uploadurl"] = $data1["yinpin"];
            $data2["type"] = $data["type"];
            $data2["posts_id"] = $res;
            $data2["addtime"] = time();
            $res1 = $Model1->add($data2); //添加数据
        }

        if ($res) {

            $opdata["content"] = "创建了官方贴。贴子id为:" . $res . "。";
            admin_log($opdata["content"]);
            echo "<div id='kk' style='display:none'>1</div>";
            $this->display("Official/add");
        } else {

            echo "<div id='kk' style='display:none'>2</div>";
            $this->display("Official/add");
        }


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
        if ($info["pid"] > 0) {
            $puserid = $UserModel->where("userid=" . $info["pid"])->find();
            $info["ptel"] = $puserid["tel"];
        }
        $this->assign("info", $info);
        $this->display();
    }


    public function del()
    {
        $id = $_POST["id"];
        $OfficialPostsModel = D("OfficialPosts");
        $res = $OfficialPostsModel->delpost($id);
        if ($res) {
            admin_log('删除官方帖，编号为：'.$id);
            $this->ajaxReturn(1);
        } else {
            $this->ajaxReturn(0);
        }

    }

    public function dostop()
    {
        $id = I('post.id');
        $result = M('official_posts')->where('posts_id=' . $id)->save(array('status' => 0));

        $opdata["content"] = "取消推荐官方贴，贴子id为：" . $id . "。";
        admin_log($opdata["content"]);

        $this->ajaxReturn($result);
    }

    public function dostart()
    {
        $id = I('post.id');
        $result = M('official_posts')->where('posts_id=' . $id)->save(array('status' => 1));

        $opdata["content"] = "推荐官方贴，，贴子id为：" . $id . "。";
        admin_log($opdata["content"]);

        $this->ajaxReturn($result);
    }


}