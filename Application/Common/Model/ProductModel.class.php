<?php
namespace Common\Model;
use Think\Model;

/**商品信息**/
class ProductModel extends Model{
	
	
	/*
	*获取商品列表
	*@param  $where  条件
	*/
     public function getlist($where,$order,$limit){
		
		 $Model=M("product");
		 $res= $Model->where($where)->order($order)->limit($limit)->select();
		 if(!$res){$res=array();}
		 if($res){
			 $UserModel=D("User");
			  for($i=0;$i<count($res);$i++){
				  $res[$i]["product_img"]=imgpath($res[$i]["product_img"]);
				  unset( $res[$i]["product_content"]);
				  $res[$i]["usermsg"]=$UserModel-> getusermsg1byuserid($res[$i]["userid"]);
				  }
				 
			 }
		return  $res;
	}
	
	/*
	*获取商品详情
	*/
     public function getproductbyid($id){
		 $Model=M("product");
		 $where="product_id=".$id;
		 $res= $Model->where($where)->find();
		 $res["product_img_y"]=$res["product_img"];
		 $res["product_img"]=imgpath($res["product_img"]);
		 unset($res["product_content"]);
		 $UserModel=D("User");
		 $res["usermsg"]=$UserModel-> getusermsg1byuserid($res["userid"]);
		 $ProductImgModel=D("ProductImg");
		 $res["imageslist"]= $ProductImgModel->getlist($id);
		return  $res;
	}
	
	/*
	*获取商品详情内容
	*@param  $data  
	*/
     public function getproductcontentbyid($id){
		 $where="product_id=".$id;
		 $res= $this->where($where)->find();
		return $res["product_content"];
	}
	
	/*
	*商品添加
	*@param  $data  
	*/
     public function addpost($data){
		 $Model=M("product");
		  $res= $Model->add($data);
		return $res;
	}
	
	/*
	*商品修改
	*@param  $data   
	*/
     public function editpostbyid($id,$data){
		 $Model=M("product");
		 $where="product_id=".$id;
		 $res= $Model->where($where)->save($data);
		 if($res===0){  // 更新数据和原始数据一样 默认更新成功
			 $res=true;			 
			 }
		return $res;
	}
	
	/*
	*商品销量修改
	*@param  $data   
	*/
     public function editsalesnumpostbyid($product_id,$product_num){
		 $Model=M("product");
		 $where="product_id=".$product_id;
		 $res= $Model->where($where)->setInc("salesnum",$product_num);
		return $res;
	}

//-------------------------------华丽的分割线---------------------------------------------------//

    /**
     * 获取商品详情
     */
    public function getGoodsInfo($product_id){
        $Model = M("product");
        $res = $Model->alias('a')
            ->field('a.product_id,a.product_name,a.summary,a.userid,b.username,b.headimg,a.addtime')
            ->join('left join user as b on b.userid = a.userid')
            ->where(['a.product_id' => $product_id, 'a.status' => 1, 'a.is_del' => 0])
            ->find();
        $res['headimg'] = imgpath($res['headimg']);
        return  $res;
    }

    /**
     * 获取商品图片
     * @param $product_id
     * @return array|mixed
     */
    public function getGoodsImgList($product_id)
    {
        $Model = M("product_img");
        $res = $Model->field('proimg_id,imgs')->where(['product_id' => $product_id])->select();
        if (!$res) {
            $res = [];
        }
        if ($res) {
            foreach ($res as &$v){
                $v['imgs'] = imgpath($v['imgs']);
            }
        }
        return $res;
    }
	
	/**
     * 获取商品规格
     */
    public function getNorms($product_id){
        $Model = M("product_norms");
        $res = $Model->field('norms,num,price,sale_num,norms_id')
            ->where(['product_id' => $product_id, 'is_del' => 0])
            ->select();
        return $res;
    }

    /**
     * 根据用户id获取商品信息列表
     */
    public function getUserRelevantGoodsList($goods_user_id,$user_id,$pageIndex,$pageSize){
        $res['user_info'] = M('user')->field('userid,headimg,username')->where(['userid'=>$goods_user_id])->find();
        $res['user_info']['headimg'] = imgpath($res['user_info']['headimg']);
        //查看是否关注
        $res['is_follow'] = $this->getUseridByFollow($goods_user_id,$user_id);
        //查看关注数
        $res['follow_number'] = $this->getUseridFollowNumber($goods_user_id);
        //查看阅读数
        $res['browse_number'] = $this->getUserBerowse($goods_user_id,$user_id);
        //商品列表
        $res['goods_list'] = M('product')->field('product_id,product_name,product_img,summary,userid,group_model_class_id')
            ->where(['userid' => $goods_user_id, 'status' => 1, 'is_del' => 0])
            ->order('addtime desc')
            ->page($pageIndex . "," . $pageSize)
            ->select();
        foreach ($res['goods_list'] as &$v) {
            $v['product_img'] = imgpath($v['product_img']);
            $v['price'] = $this->getGoodsByMinPrice($v['product_id']);
        }
        return $res;
    }

    /**
     * 查看是否关注
     */
    public function getUseridByFollow($planet_userid, $user_id)
    {
        $UserPlanetModel = D("UserPlanet");
        $row = $UserPlanetModel->judgeIsFollow($user_id, $planet_userid);
        if ($row) {
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * 查看关注数
     */
    public function getUseridFollowNumber($user_id){
        $follow_number = M('user_planet_follow')->where(['planet_userid'=>$user_id])->count();
        return $follow_number;
    }

    /**
     *  查看阅读数
     */
    public function getUserBerowse($goods_user_id,$user_id){
        $Model = M('product_browse_record');
        if ($goods_user_id != $user_id) {
            $info = $Model->where(['user_id'=>$goods_user_id])->find();
            if (!$info){//没有记录增加记录
                $data['user_id'] = $goods_user_id;
                $data['number'] = 1;
                $data['create_time'] = time();
                $Model->add($data);
            }
        }
        //查询
        $res = $Model->where(['user_id' => $goods_user_id])->getField('number');
        return $res;
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
}