<?php

namespace Home\Controller;

use Think\Controller;

class IShoppingcarController extends CommonController
{


    /**
     *  获取我的购物车商品种类
     */
    public function getshipcartypenum()
    {
        $userid = I("userid");
        $ShoppingcarModel = D("Shoppingcar");
        $res["product_type_num"] = $ShoppingcarModel->getproducttypenumbyuserid($userid);
        $msg = "获取成功！";
        get_api_result(200, $msg, $res);

    }

    /**
     *  获取我的购物车
     */
    public function getshipcarlist()
    {
        $userid = I("userid");
        $ShoppingcarModel = D("Shoppingcar");
        $res = $ShoppingcarModel->getlistbyuserid($userid);
        $msg = "获取成功！";
        get_api_result(200, $msg, $res);

    }


    /**
     *  加入购物车
     */
    public function addshipcar()
    {
        $data["product_id"] = I("product_id");
        $data["userid"] = I("userid");
        $data["product_num"] = I("num");
        $ShoppingcarModel = D("Shoppingcar");
        $res = $ShoppingcarModel->addshipingcarpost($data);
        if ($res) {
            $msg = "成功加入购物车！";
            $res1["product_type_num"] = $ShoppingcarModel->getproducttypenumbyuserid($data["userid"]);
            get_api_result(200, $msg, $res1);
        } else {
            $msg = "加入购物车失败！";
            get_api_result(300, $msg);
        }

    }


    /**
     * 修改购物车商品数量
     */
    public function editproductnum()
    {
        $product_id = I("product_id");
        $userid = I("userid");
        $num = I("num");
        $ShoppingcarModel = D("Shoppingcar");
        $res = $ShoppingcarModel->editshipingcarnumpost($product_id, $userid, $num);
        if ($res) {
            $msg = "修改成功！";
            get_api_result(200, $msg);
        } else {
            $msg = "修改失败！";
            get_api_result(300, $msg);
        }

    }

    /**
     *  删除购物车商品
     */
    public function delshipcar()
    {
        $product_id = I("product_id");
        $userid = I("userid");
        $ShoppingcarModel = D("Shoppingcar");
        $res = $ShoppingcarModel->delpost($product_id, $userid);
        if ($res) {
            $msg = "删除成功！";
            get_api_result(200, $msg);
        } else {
            $msg = "删除失败！";
            get_api_result(300, $msg);
        }

    }

}