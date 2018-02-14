<?php

namespace Admin\Controller;

use Think\Controller;

class ProductController extends CommonController
{
//    public function _initialize(){
//        parent::_initialize();
//        if(session('admin_key_id') !=1){
//            if(!in_array('8',session('admin_key_auth'))){
//                session('[destroy]');
//                $this->redirect('Login/index',array(), 1, '无权限...');
//            }
//        }
//
//    }

    public function index()
    {
        $goods = I('get.goods');
        $where = 'is_del = 0';
        if ($goods != ""){
            $where .= " and product.product_name LIKE '%" . $goods . "%' ";
        }
        $Model = M("product");
        $field = "product.*,user.username";
        $join = "left join user ON user.userid=product.userid";
        $list = $Model->field($field)->where($where)->join($join)->select();
        $this->assign('list', $list);
        $this->display();
    }

    //设为/取消首页推荐
    public function set_preferential()
    {
        $Model = M("product");
        $id = I('id', 0, 'int');
        $status = I('status', 0, 'int');
        $msg = '';
        if ($status) {
            $data["hotstatus"] = 0;
            $msg = '设为';
            $data["hot_addtime"] = time();

        } else {
            $data["hotstatus"] = 1;
            $msg = '取消';
            $data["hot_addtime"] = time();
        }
        $res = $Model->where(array('product_id' => $id))->save($data);
        if ($res) {
            admin_log($msg . '推荐，编号为：' . $id);
            $this->ajaxReturn(1);
        } else {
            $this->ajaxReturn(0);
        }
    }

    //设为/取消上架
    public function set_hot()
    {
        $Model = M("product");
        $id = I('id', 0, 'int');
        $status = I('status', 0, 'int');
        $userid=$Model->where(['product_id'=>$id])->getField('userid');
        if(!$userid){
            $this->ajaxReturn(0);
        }
        $msg = '';
        if ($status) {
            $data["status"] = 0;
            $msg = '下架';
        } else {
            $data["status"] = 1;
            $msg = '上架';
        }
        $res = $Model->where(array('product_id' => $id))->save($data);
        if ($res) {
            admin_log('商品' . $msg);
            $this->ajaxReturn(1);
        } else {
            $this->ajaxReturn(0);
        }
    }


    public function show()
    {
        $id = I('id', 0, 'int');
        $info = M('product')->where('product_id=' . $id)->find();
        $this->assign("info", $info);//print_r($info);die;
        $this->display();
    }

    public function picshow()
    {
        $id = I('id', 0, 'int');
        $count = M('product_img')->where('product_id=' . $id)->count();
        $list = M('product_img')->where('product_id=' . $id)->select();
        $this->assign("count", $count);//print_r($info);die;
        $this->assign("list", $list);
        $this->assign("id", $id);
        $this->display();
    }


    public function dostop()
    {
        $id = I('post.id');
        $result = M('product')->where('product_id=' . $id)->save(array('tui' => 0));

        $opdata["content"] = "将商品加入非常购商品，商品id：" . $id . "。";
        admin_log($opdata["content"]);

        $this->ajaxReturn($result);
    }

    public function dostart()
    {
        $id = I('post.id');
        $result = M('product')->where('product_id=' . $id)->save(array('tui' => 1));

        $opdata["content"] = "将商品加入常购商品，商品id：" . $id . "。";
        admin_log($opdata["content"]);

        $this->ajaxReturn($result);
    }

    public function add()
    {
        $model_list = M('group_model_class')->where(['group_model_id' => 2])->select();
        $this->assign('model_list',$model_list);
        $this->display();
    }

    public function addpost()
    {
        $Model = M('product');
        $ImgModel = M('product_img');
        $data = $Model->create();
        $filenamearray = $_POST["imgfile"];
        if (count($filenamearray) == 0) {
            $this->error('请选择上传图片');
            $this->ajaxReturn(0);
        }

        $data["status"] = 0;
        $data["addtime"] = time();
        $data['product_content'] = replace_video(replace_img(shielding(htmlspecialchars_decode($_POST['product_content']))));
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 1024 * 1024 * 20;
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');
        $upload->rootPath = './'; // 设置附件上传目录
        $upload->savePath = '/Public/upload/product/'; // 设置附件上传目录
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
        for ($i = 0; $i < count($filenamearray); $i++) {
            $data1[$i]["imgs"] = $filenamearray[$i];
            $data1[$i]["product_id"] = $res;
            $data1[$i]["addtime"] = $data["addtime"];
        }
        $ImgModel->addall($data1);
        if ($res) {
            $opdata["content"] = "添加了商品信息。id为:" . $res . "。";
            admin_log($opdata["content"]);
            $this->ajaxReturn(1);
        } else {
            $this->ajaxReturn(0);
        }
    }


    public function Imgupload()
    {
        $userid = $_GET["userid"];
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 1024 * 1024 * 20;
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');
        $upload->rootPath = './'; // 设置附件上传目录
        $upload->savePath = './Public/upload/product/' . $userid . "/"; // 设置附件上传目录
        $upload->saveName = array('uniqid', '');
        $upload->ischeckfile = true;  //必须上传文件
        $info = $upload->upload();
        if (!$info) { // 上传错误提示错误信息
            // $this->error($upload->getError());
            $error = $upload->getError();
            $data['error_info'] = $error;
            echo json_encode($data);
        } else { //上传成功获取上传文件信息
            foreach ($info as $file) {
                $data[$file['key']] = $file['savepath'] . $file['savename'];
                $this->ajaxReturn($data);
            }
        } // 保存表单数据包括附件数据<br />


    }


    public function edit()
    {
        $Model = M("product");
        $id = $_GET["id"];
        $where = "product_id=" . $id;
        $info = $Model->where($where)->find();
        $this->assign("info", $info);
        $this->display();
    }

    public function editpost()
    {
        $Model = M('product');
        $data = $Model->create();
        $data['product_content'] = stripslashes($_POST['product_content']);
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize = 1024 * 1024 * 20;
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');
        $upload->rootPath = './'; // 设置附件上传目录
        $upload->savePath = './Public/upload/product/'; // 设置附件上传目录
        $upload->saveName = array('uniqid', '');
        //$upload-> ischeckfile  =   true;  //必须上传文件
        $info = $upload->upload();
        if (!$info) { // 上传错误提示错误信息
            // $this->error($upload->getError());
        } else { //上传成功获取上传文件信息
            foreach ($info as $file) {
                $data[$file['key']] = $file['savepath'] . $file['savename'];
            }
            $res = $Model->where("product_id=" . $_POST["id"])->find();
            unlink($res["images"]);
        } // 保存表单数据包括附件数据<br />

        if ($Model->where("product_id=" . $_POST["id"])->save($data)) {
            $opdata["content"] = "修改了商品信息。商品id为:" . $_POST["id"] . "。";
            admin_log($opdata["content"]);
            echo "<div id='kk' style='display:none'>1</div>";
            $this->display("Product/edit");
        } else {
            echo "<div id='kk' style='display:none'>2</div>";
            $this->display("Product/edit");
        }
    }


    public function del()
    {
        $id = $_POST["id"];
        $Model = M("banner");
        $where = "id=" . $id;
        $res = $Model->where($where)->find();
        unlink($res["images"]);
        $res1 = $Model->where($where)->delete();
        if ($res1) {
            $opdata["content"] = "删除了商品信息。编号为:" . $id . "。";
            admin_log($opdata["content"]);

            $this->ajaxReturn(1);
        } else {
            $this->ajaxReturn(0);
        }

    }

    public function addpic()
    {
        $id = I('id', 0, 'int');
        $this->assign("id", $id);
        $this->display();
    }

    public function addpicpost()
    {

        $Model1 = M('product_img');
        $product_id = $_POST["product_id"];
        $filenamearray = $_POST["imgfile"];
        $this->assign("id", $product_id);
        if ($filenamearray[0] == "") {
            echo "<div id='kk' style='display:none'>3</div>";
            $this->display("Product/addpic");
            die;
        }
        for ($i = 0; $i < count($filenamearray); $i++) {
            $data1[$i]["imgs"] = $filenamearray[$i];
            $data1[$i]["product_id"] = $product_id;
            $data1[$i]["addtime"] = time();
        }
        $res1 = $Model1->addall($data1);
        if ($res1) {
            $opdata["content"] = "添加了商品信息相册图片。";
            admin_log($opdata["content"]);

            echo "<div id='kk' style='display:none'>1</div>";
            $this->display("Product/addpic");
        } else {
            echo "<div id='kk' style='display:none'>2</div>";
            $this->display("Product/addpic");
        }
    }

    //删除商品图片
    public function delimg()
    {
        $Model = M("product_img");
        $id = $_POST["id"];
        $where['proimg_id'] = array('in', $id);
        $res = $Model->where($where)->delete();
        if ($res) {
            admin_log('删除商品图片,编号为：'.$id);
            $this->ajaxReturn(1);
        } else {
            $this->ajaxReturn(0);
        }
    }

    /**
     * 商品规格
     */
    public function norms(){
        $product_id = I('get.product_id');
        $name=M('product')->where(['product_id'=>$product_id])->getField('product_name');
        $count = M('product_norms')->where(['product_id'=>$product_id])->count();
        $Page = new \Think\Page($count, 10);
        $Page->setConfig('prev', '上一页');
        $Page->setConfig('next', '下一页');
        $show = $Page->show();
        $info = M('product_norms')->where(['product_id'=>$product_id])->order('add_time desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('page', $show);// 赋值分页输出
        $this->assign("list", $info);
        $this->assign("product_id", $product_id);
        $this->assign("name", $name);
        $this->display();
    }
    /**
     * 商品规格添加
     */
    public function norms_edit(){
        if(IS_POST){//修改或者添加
            $post=I('post.');
            if(!is_numeric($post['price'])){
                echo json_encode(['code' => 200, 'msg' =>'商品价格格式错误']);exit;
            }
            if($post['norms_id']){//修改
                $norms_id=$post['norms_id'];
                unset($post['product_id']);
                $res=M('product_norms')->where(['norms_id'=>$norms_id])->save($post);
                if($res){
                    echo json_encode(['code' => 200, 'msg' =>'编辑成功']);exit;
                }
                echo json_encode(['code' => 201, 'msg' =>'编辑失败']);exit;
            }elseif($post['product_id']){//添加
                unset($post['norms_id']);
                $post['add_time']=time();
                $res=M('product_norms')->add($post);
                if($res){
                    echo json_encode(['code' => 200, 'msg' =>'添加成功']);exit;
                }
                echo json_encode(['code' => 201, 'msg' =>'添加失败']);exit;
            }else{
                echo json_encode(['code' => 201, 'msg' =>'数据错误']);
                exit;
            }
        }else{//进入修改页面
            $product_id = I('get.product_id');
            $norms_id = I('get.norms_id');
            if($norms_id){
                $res=M('product_norms')->where(['norms_id'=>$norms_id])->find();
                $this->assign("norms", $res);
                $this->assign("norms_id", $norms_id);
            }
            $this->assign("product_id", $product_id);
            $this->display();
        }
    }

    /**
     * 发布
     */
    public function publish()
    {
        $id = I('get.product_id');
        $Model = M('product');
        $res = $Model->where(['product_id' => $id])->find();
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
        $id = I('post.product_id');
        $Model = M('product');
        $data = $Model->create();
        $data['status'] = 1;
        $res = $Model->where(['product_id' => $id])->save($data);
        if ($res) {
            admin_log('发布商品，编号：' . $id);
            $this->ajaxReturn(1);
        }else{
            $this->ajaxReturn(0);
        }
    }

    /**
     *  删除
     */
    public function delete(){
        $id = I('post.id');
        $Model = M('product');
        $res = $Model->where(['product_id' => $id])->save(['is_del' => 1]);
        if ($res) {
            admin_log('删除商品，编号：' . $id);
            $this->ajaxReturn(1);
        } else {
            $this->ajaxReturn(0);
        }
    }
}