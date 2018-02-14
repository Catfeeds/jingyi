<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/3/8
 * Time: 11:21
 */

namespace Admin\Controller;

use Think\Controller;

class GroupModelArticleController extends CommonController
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


    /**
     * CLUB文章列表
     */
    public function index()
    {
        //获取查询参数
        $group_model_class_id = I('get.group_model_class_id') ? I('get.group_model_class_id') : '';
        $this->assign('group_model_class_id', $group_model_class_id);
        $where = 'group_model_id = 1 and status = 0';
        if ($group_model_class_id != '') {
            $where .= " and group_model_class_id='" . $group_model_class_id . "'";
        }
        $class_list = M("group_model_class")->where(['group_model_id' => 1, 'status' => 1])->select();
        $this->assign('class_list', $class_list);
        $Model = M("group_model_article");

        $count = $Model->where($where)->count();
        $limit = 10;
        $Page = new \Think\Page($count, $limit);// 实例化分页类 传入总记录数和每页显示的记录数

        $Page->setConfig('header', '共%TOTAL_ROW%条');
        $Page->setConfig('first', '首页');
        $Page->setConfig('last', '共%TOTAL_PAGE%页');
        $Page->setConfig('prev', '上一页');
        $Page->setConfig('next', '下一页');
        $Page->setConfig('link', 'indexpagenumb'); //pagenumb 会替换成页码
        $Page->setConfig('theme', '%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
        $show = $Page->show();// 分页显示输出
        $limit1 = $Page->firstRow . ',' . $Page->listRows;
        $list = $Model->where($where)->limit($limit1)->select();
        $this->assign('list', $list);
        $this->assign('page', $show);// 赋值分页输出

        $this->display();
    }

    /**
     * 添加
     */
    public function add()
    {
        $article = M('group_model_article')->where(['group_model_class_id' => 1, 'status' => 0])->find();
        $Model = M('group_model_class');
        $where = 'group_model_id = 1 and type = 0 and status = 1';
        if (!$article) {
            $where = 'group_model_id = 1 and status = 1';
        }
        $model_list = $Model->where($where)->select();
        $this->assign('model_list', $model_list);
        $this->display();
    }

    public function addPost()
    {
        $Model = M('group_model_article');
        $data = $Model->create();
        $data['html_code'] = replace_video(replace_img(shielding(htmlspecialchars_decode($data["html_code"]))));
        $data['group_model_id'] = 1;
        $time = time();
        $data['create_time'] = $time;
        $data['update_time'] = $time;
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 1024 * 1024 * 20;
        $upload->exts = array('jpg', 'png', 'jpeg');
        $upload->rootPath = './'; // 设置附件上传目录
        $upload->savePath = '/Public/upload/article/'; // 设置附件上传目录
        $upload->saveName = array('uniqid', '');
        $upload->ischeckfile = true;  //必须上传文件
        $info = $upload->upload();
        if (!$info) { // 上传错误提示错误信息
            $this->error($upload->getError());
        } else { //上传成功获取上传文件信息
            foreach ($info as $file) {
                $data[$file['key']] = $file['savepath'] . $file['savename'];
            }
        } // 保存表单数据包括附件数据<br />
        $res = $Model->add($data);
        if ($res) {
            admin_log('添加文章，编号：' . $res);
            $this->ajaxReturn(1);
        } else {
            $this->ajaxReturn(0);
        }
    }

    /**
     * 修改
     */
    public function edit()
    {
        $id = I('get.id');
        $Model = M("group_model_article");
        $res = $Model->where(['id' => $id])->find();
        $this->assign('res', $res);

        $article = M('group_model_article')->where(['group_model_class_id' => 1, 'status' => 0])->find();
        $Model = M('group_model_class');
        if ($res['group_model_class_id'] == 1) {
            $where = 'group_model_id = 1 and status = 1';
        } elseif (!$article) {
            $where = 'group_model_id = 1 and status = 1';
        } else {
            $where = 'group_model_id = 1 and type = 0 and status = 1';
        }
        $model_list = $Model->where($where)->select();
        $this->assign('model_list', $model_list);
        $this->display();
    }

    /**
     * 修改文章
     */
    public function editPost()
    {
        $Model = M('group_model_article');
        $data = $Model->create();
        $data['html_code'] = replace_video(replace_img(shielding(htmlspecialchars_decode($data["html_code"]))));
        $data['update_time'] = time();
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 1024 * 1024 * 20;
        $upload->exts = array('jpg', 'png', 'jpeg');
        $upload->rootPath = './'; // 设置附件上传目录
        $upload->savePath = '/Public/upload/article/'; // 设置附件上传目录
        $upload->saveName = array('uniqid', '');
//        $upload->ischeckfile = true;  //必须上传文件
        $info = $upload->upload();
        if (!$info) { // 上传错误提示错误信息
//            $this->error($upload->getError());
        } else { //上传成功获取上传文件信息
            foreach ($info as $file) {
                $data[$file['key']] = $file['savepath'] . $file['savename'];
            }
        } // 保存表单数据包括附件数据<br />
        $res = $Model->where(['id' => $data['id']])->save($data);
        if ($res) {
            admin_log('编辑文章，编号：' . $res);
            $this->ajaxReturn(1);
        } else {
            $this->ajaxReturn(0);
        }
    }

    /**
     * 删除
     * 仅改变状态
     */
    public function del()
    {
        $id = I('post.id');
        $Model = M('group_model_article');
        $res = $Model->where(['id' => $id])->save(['status' => 1]);
        if ($res) {
            admin_log('删除文章，编号：' . $id);
            $this->ajaxReturn(1);
        } else {
            $this->ajaxReturn(0);
        }
    }

    /**
     * 详情
     */
    public function show()
    {
        $id = I('get.id');
        $Model = M('group_model_article');
        $info = $Model->alias('a')
            ->field('a.*,b.name as group_model_class_name')
            ->where(['a.id' => $id])
            ->join('left join group_model_class as b on b.id = a.group_model_class_id')
            ->find();

        $this->assign('info', $info);
        $this->display();
    }

    /**
     * 发布
     */
    public function publish()
    {
        $id = I('get.id');
        $Model = M('group_model_article');
        $res = $Model->where(['id' => $id])->find();
        $this->assign('res', $res);
        $user_list = M('user')->select();
        $this->assign('user_list', $user_list);
        $this->display();
    }

    /**
     * 发布提交
     */
    public function publishPost()
    {
        $id = I('post.id');
        $Model = M('group_model_article');
        $data = $Model->create();
        $data['author'] = M('user')->where(['userid'=>$data['author_id']])->getField('username');
        $data['is_publish'] = 1;
        $res = $Model->where(['id' => $id])->save($data);
        if ($res) {
            admin_log('发布文章，编号：' . $id);
            D('UserLevel')->addUserGrow($data['author_id'],6);//成长值
            $this->ajaxReturn(1);
        }else{
            $this->ajaxReturn(0);
        }
    }

    /**
     * 星系联盟文章列表
     */
    public function union_index(){
        //获取查询参数
        $where = 'group_model_id = 4 and status = 0';
        $class_list = M("group_model_class")->where(['group_model_id' => 4, 'status' => 1])->select();
        $this->assign('class_list', $class_list);
        $Model = M("group_model_article");

        $count = $Model->where($where)->count();
        $limit = 10;
        $Page = new \Think\Page($count, $limit);// 实例化分页类 传入总记录数和每页显示的记录数

        $Page->setConfig('header', '共%TOTAL_ROW%条');
        $Page->setConfig('first', '首页');
        $Page->setConfig('last', '共%TOTAL_PAGE%页');
        $Page->setConfig('prev', '上一页');
        $Page->setConfig('next', '下一页');
        $Page->setConfig('link', 'indexpagenumb'); //pagenumb 会替换成页码
        $Page->setConfig('theme', '%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
        $show = $Page->show();// 分页显示输出
        $limit1 = $Page->firstRow . ',' . $Page->listRows;
        $list = $Model->where($where)->limit($limit1)->select();
        $this->assign('list', $list);
        $this->assign('page', $show);// 赋值分页输出

        $this->display();
    }

    /**
     * 星系联盟添加
     */
    public function union_add(){
        $Model = M('group_model_class');
        $model_list = $Model->where(['group_model_id' => 4, 'type' => 1 , 'status' => 1])->select();
        $this->assign('model_list', $model_list);
        $this->display();
    }

    /**
     * 星系联盟添加提交
     */
    public function union_add_post(){
        $Model = M('group_model_article');
        $data = $Model->create();
        $data['html_code'] = replace_video(replace_img(shielding(htmlspecialchars_decode($data["html_code"]))));
        $data['group_model_id'] = 4;
        $time = time();
        $data['create_time'] = $time;
        $data['update_time'] = $time;
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 1024 * 1024 * 20;
        $upload->exts = array('jpg', 'png', 'jpeg');
        $upload->rootPath = './'; // 设置附件上传目录
        $upload->savePath = '/Public/upload/article/'; // 设置附件上传目录
        $upload->saveName = array('uniqid', '');
        $upload->ischeckfile = true;  //必须上传文件
        $info = $upload->upload();
        if (!$info) { // 上传错误提示错误信息
            $this->error($upload->getError());
        } else { //上传成功获取上传文件信息
            foreach ($info as $file) {
                $data[$file['key']] = $file['savepath'] . $file['savename'];
            }
        } // 保存表单数据包括附件数据<br />
        $res = $Model->add($data);
        if ($res) {
            admin_log('添加文章，编号：' . $res);
            $this->ajaxReturn(1);
        } else {
            $this->ajaxReturn(0);
        }
    }

    /**
     * 星系联盟修改
     */
    public function union_edit()
    {
        $id = I('get.id');
        $Model = M("group_model_article");
        $res = $Model->where(['id' => $id])->find();
        $this->assign('res', $res);

        $Model = M('group_model_class');
        $model_list = $Model->where(['group_model_id' => 4, 'type' => 1 , 'status' => 1])->select();
        $this->assign('model_list', $model_list);
        $this->display();
    }

    /**
     * 修改文章
     */
    public function union_edit_Post()
    {
        $Model = M('group_model_article');
        $data = $Model->create();
        $data['html_code'] = replace_video(replace_img(shielding(htmlspecialchars_decode($data["html_code"]))));
        $data['update_time'] = time();
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 1024 * 1024 * 20;
        $upload->exts = array('jpg', 'png', 'jpeg');
        $upload->rootPath = './'; // 设置附件上传目录
        $upload->savePath = '/Public/upload/article/'; // 设置附件上传目录
        $upload->saveName = array('uniqid', '');
//        $upload->ischeckfile = true;  //必须上传文件
        $info = $upload->upload();
        if (!$info) { // 上传错误提示错误信息
//            $this->error($upload->getError());
        } else { //上传成功获取上传文件信息
            foreach ($info as $file) {
                $data[$file['key']] = $file['savepath'] . $file['savename'];
            }
        } // 保存表单数据包括附件数据<br />
        $res = $Model->where(['id' => $data['id']])->save($data);
        if ($res) {
            admin_log('编辑文章，编号：' . $res);
            $this->ajaxReturn(1);
        } else {
            $this->ajaxReturn(0);
        }
    }

    /**
     * 论坛文章列表
     */
    public function forum_index(){
        //获取查询参数
        $where = 'group_model_id = 3 and status = 0';
        $class_list = M("group_model_class")->where(['group_model_id' => 3, 'status' => 1])->select();
        $this->assign('class_list', $class_list);
        $Model = M("group_model_article");

        $count = $Model->where($where)->count();
        $limit = 10;
        $Page = new \Think\Page($count, $limit);// 实例化分页类 传入总记录数和每页显示的记录数

        $Page->setConfig('header', '共%TOTAL_ROW%条');
        $Page->setConfig('first', '首页');
        $Page->setConfig('last', '共%TOTAL_PAGE%页');
        $Page->setConfig('prev', '上一页');
        $Page->setConfig('next', '下一页');
        $Page->setConfig('link', 'indexpagenumb'); //pagenumb 会替换成页码
        $Page->setConfig('theme', '%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
        $show = $Page->show();// 分页显示输出
        $limit1 = $Page->firstRow . ',' . $Page->listRows;
        $list = $Model->where($where)->limit($limit1)->select();
        $this->assign('list', $list);
        $this->assign('page', $show);// 赋值分页输出

        $this->display();
    }

    /**
     * 论坛文章添加
     */
    public function forum_add(){
        $Model = M('group_model_class');
        $model_list = $Model->where(['id'=>['in','11,12,13,14,15,16,17']])->field('id,name')->select();
        $four=$Model->where(['pid'=>17])->field('id,name')->select();
        $this->assign('model_list', $model_list);
        $this->assign('four', $four);
        $this->display();
    }

    /**
     * 论坛文章添加提交
     */
    public function forum_add_post(){
        $Model = M('group_model_article');
        $data = $Model->create();
        $data['html_code'] = replace_video(replace_img(shielding(htmlspecialchars_decode($data["html_code"]))));
        $data['group_model_id'] = 3;
        if($data['group_model_three_id']==11 || $data['group_model_three_id']==12 || $data['group_model_three_id']==13 || $data['group_model_three_id']==14 || $data['group_model_three_id']==15) {
            $data['group_model_class_id'] = 6;

        }
        if($data['group_model_three_id']==16 || $data['group_model_three_id']==17) {
            $data['group_model_class_id'] = 8;
        }
        if($data['group_model_three_id']!=17) {
            $data['group_model_four_id'] = 0;
        }
        $time = time();
        $data['create_time'] = $time;
        $data['update_time'] = $time;
        if($data['group_model_three_id']==11 || $data['group_model_three_id']==13 || $data['group_model_three_id']==14 || $data['group_model_three_id']==16 || $data['group_model_three_id']==17){
            $data['is_publish']=1;
            $data['author']=session('admin_key_name');
            $data['author_id']=session('admin_key_id');
            $data['type']=1;
        }
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 1024 * 1024 * 20;
        $upload->exts = array('jpg', 'png', 'jpeg');
        $upload->rootPath = './'; // 设置附件上传目录
        $upload->savePath = '/Public/upload/article/'; // 设置附件上传目录
        $upload->saveName = array('uniqid', '');
        $upload->ischeckfile = true;  //必须上传文件
        $info = $upload->upload();
        if (!$info) { // 上传错误提示错误信息
            $this->error($upload->getError());
        } else { //上传成功获取上传文件信息
            foreach ($info as $file) {
                $data[$file['key']] = $file['savepath'] . $file['savename'];
            }
        } // 保存表单数据包括附件数据<br />
        $res = $Model->add($data);
        if ($res) {
            admin_log('添加文章，编号：' . $res);
            $this->ajaxReturn(1);
        } else {
            $this->ajaxReturn(0);
        }
    }

    /**
     * 论坛文章修改
     */
    public function forum_edit()
    {
        $id = I('get.id');
        $Model = M("group_model_article");
        $res = $Model->where(['id' => $id])->find();
        $this->assign('res', $res);

        $Model = M('group_model_class');
        $model_list = $Model->where(['id'=>['in','11,12,13,14,15']])->select();
        $this->assign('model_list', $model_list);
        $this->display();
    }

    /**
     * 论坛文章修改操作
     */
    public function forum_edit_Post()
    {
        $Model = M('group_model_article');
        $data = $Model->create();
        $data['html_code'] = replace_video(replace_img(shielding(htmlspecialchars_decode($data["html_code"]))));
        $data['update_time'] = time();
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 1024 * 1024 * 20;
        $upload->exts = array('jpg', 'png', 'jpeg');
        $upload->rootPath = './'; // 设置附件上传目录
        $upload->savePath = '/Public/upload/article/'; // 设置附件上传目录
        $upload->saveName = array('uniqid', '');
//        $upload->ischeckfile = true;  //必须上传文件
        $info = $upload->upload();
        if (!$info) { // 上传错误提示错误信息
//            $this->error($upload->getError());
        } else { //上传成功获取上传文件信息
            foreach ($info as $file) {
                $data[$file['key']] = $file['savepath'] . $file['savename'];
            }
        } // 保存表单数据包括附件数据<br />
        $res = $Model->where(['id' => $data['id']])->save($data);
        if ($res) {
            admin_log('编辑文章，编号：' . $res);
            $this->ajaxReturn(1);
        } else {
            $this->ajaxReturn(0);
        }
    }

    /**
     * 置顶
     */
    public function set_top()
    {
        $Model = M("group_model_article");
        $id = I('id', 0, 'int');
        $top = I('top', 0, 'int');
        $msg = '';
        if ($top) {
            $data["top"] = 0;
            $msg = '设为';
            $data["update_time"] = time();
        } else {
            $data["top"] = 1;
            $msg = '取消';
            $data["update_time"] = time();
        }
        $res = $Model->where(['id'=>$id])->save($data);
        if ($res) {
            admin_log($msg . '置顶，编号为：' . $id);
            $this->ajaxReturn(1);
        } else {
            $this->ajaxReturn(0);
        }
    }
}