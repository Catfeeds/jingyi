<?php

namespace Home\Controller;

use Think\Controller;

class IProductController extends CommonController
{


    /**
     *  获取商品列表
     */
    public function getproductlist()
    {
        $where = "status=1";
        $where .= I("product_name") ? " and product_name like '%" . I("product_name") . "%'" : "";
        $order = "addtime desc";

        $page = I("page") ? I("page") : 1;
        $limit = I("limit") ? I("limit") : 10;
        $limit1 = ($page - 1) * $limit . "," . $limit;

        $ProductModel = D("Product");
        $res = $ProductModel->getlist($where, $order, $limit1);
        $msg = "获取成功！";
        get_api_result(200, $msg, $res);

    }


    /**
     *  获取商品详情
     */
    public function getproductInfo()
    {
        $product_id = I("product_id");
        $ProductModel = D("Product");
        $res["productmsg"] = $ProductModel->getproductbyid($product_id); //获取商品详情

        $OrderEvaluateModel = D("OrderEvaluate");
        $res["productevaluate"] = $OrderEvaluateModel->getevaluatebyproid($product_id, 5); //评价列表
        $res["productgoodevaluate"] = $OrderEvaluateModel->getgoodevaluatebyproid($product_id); //好评度
        $res["productmsg"]["productgoodevaluate"] = $res["productgoodevaluate"];//好评度
        $msg = "获取成功！";
        get_api_result(200, $msg, $res);
    }


    /**
     *  获取商品详情web页面
     */
    public function getproductcontent()
    {
        $product_id = I("product_id");
        $ProductModel = D("Product");
        $res = $ProductModel->getproductcontentbyid($product_id); //获取商品详情
        $this->assign("content", $res);
        $this->display("Product/show");
    }

    //--------------------------------华丽的分割线-----------------------------------------------//

    /**
     *  获取二级模块下所有商品列表
     */
    public function getGroupModelGoodsList()
    {
        $model_class_id = I("model_class_id");
        $pageIndex = I('pageIndex') ? I('pageIndex') : 1;
        $pageSize = I('pageSize') ? I('pageSize') : 10;
        $ProductModel = M("product");
        $res['goods_list'] = $ProductModel->field('product_id,product_name,product_img,summary,userid,group_model_class_id')
            ->where(['group_model_class_id'=>$model_class_id,'status'=>1,'is_del'=>0])
            ->order('addtime desc')
            ->page($pageIndex . "," . $pageSize)
            ->select();
        foreach ($res['goods_list'] as &$v){
            $v['product_img'] = imgpath($v['product_img']);
            $v['price'] = $this->getGoodsByMinPrice($v['product_id']);
        }
        get_api_result(200, '获取成功', $res);
    }

    /**
     * 根据商品id 获取最低的价格
     */
    public function getGoodsByMinPrice($product_id){
        $Model = M('product_norms');
        $info = $Model->where(['product_id'=>$product_id])->order('price asc')->getField('price');
        if(!$info){
            $info = [];
        }
        return $info;
    }

    /**
     * 根据商品id  获取商品详情
     */
    public function getGoodsInfo(){
        $product_id = I('product_id');
        $ProductModel = D("Product");
        //获取商品信息
        $res['product_info'] = $ProductModel->getGoodsInfo($product_id);
        //获取最低价格
        $res['product_info']['price'] = $this->getGoodsByMinPrice($res['product_info']['product_id']);
        //获取商品图片
        $res['product_info']["product_imglist"] = $ProductModel->getGoodsImgList($product_id);
        //获取评价列表
        $OrderEvaluateModel = D("OrderEvaluate");
        $res["product_evaluate"] = $OrderEvaluateModel->getProductIdByEvaluateList($product_id, 5); //评价列表
        get_api_result(200, '获取成功', $res);
    }

    /**
     * 根据商品id 获取商品规格
     */
    public function getGoodsNorms(){
        $product_id = I('product_id');
        $ProductModel = D("Product");
        $res = $ProductModel->getNorms($product_id);
        get_api_result(200, '获取成功', $res);
    }

    /**
     * 根据商品id 获取评论列表
     */
    public function getGoodsEvaluate(){
        $product_id = I('product_id');
        $pageIndex = I('pageIndex') ? I('pageIndex') : 1;
        $pageSize = I('pageSize') ? I('pageSize') : 10;
        $OrderEvaluateModel = D("OrderEvaluate");
        $res = $OrderEvaluateModel->getEvaluateList($product_id,$pageIndex,$pageSize);
        get_api_result(200, '获取成功', $res);
    }

    /**
     * 根据商品id  获取详情
     */
    public function getGoodsShow(){
        $product_id = I('product_id');
        $ProductModel = M("product");
        $res = $ProductModel->where(['product_id'=>$product_id, 'status' => 1, 'is_del' => 0])->getField('product_content');
        if (!$res){
            get_api_result(300, '获取失败', $res);
        }else{
            get_api_result(200, '获取成功', $res);
        }
    }

    /**
     * 根据用户id  获取用户相关商品列表
     */
    public function getUserIdByGoodsList(){
        $goods_user_id = I('goods_user_id');
        $user_id = I('user_id');
        $pageIndex = I('pageIndex') ? I('pageIndex') : 1;
        $pageSize = I('pageSize') ? I('pageSize') : 10;
        $ProductModel = D("Product");
        $res = $ProductModel->getUserRelevantGoodsList($goods_user_id,$user_id,$pageIndex,$pageSize);
        if (!$res){
            get_api_result(300, '获取失败', $res);
        }else{
            get_api_result(200, '获取成功', $res);
        }
    }

    /**
     * 增加阅读数
     */
    public function getUserIdByAddBrowse(){
        $goods_user_id = I('goods_user_id');
        $res = M('product_browse_record')->where(['user_id'=>$goods_user_id])->setInc('number');
        if ($res){
            get_api_result(200, '增加阅读数成功', $res);
        }else{
            get_api_result(300, '增加阅读数失败', $res);
        }
    }


    /**
     * 根据用户id  获取用户是否有星球仓
     */
    public function getUserIdByStarStorehouse(){
        $user_id = I('user_id');
        $res = M('product')->where(['userid'=>$user_id, 'status' => 1, 'is_del' => 0])->count();
        if ($res == 0){
            get_api_result(200, '获取成功,没有星球仓', 0);
        }else{
            get_api_result(200, '获取成功,有星球仓', 1);
        }
    }
}