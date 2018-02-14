<?php
namespace Common\Model;
use Think\Model;

/**订单评价**/
class OrderEvaluateModel extends Model{
	protected $tableName = 'order_evaluate'; 
	
	
	/*
	*添加评论
	*/
     public function addpost($data){
		$data["addtime"]=time();
		$res=$this->add($data);
		return  $res;
	}
	
	/*
	*批量添加评论
	*/
     public function addAllpost($data){
		$res=$this->addAll($data);
		return  $res;
	}
	
	
	
	
	/**
     * 根据商品id获取评论列表
	  *@param  $product_id   订单编号 
     */
    public function getevaluatebyproid($product_id,$limit){
		$where="product_id=".$product_id;
		$order="addtime desc";
		$res=$this->where($where)->order($order)->limit($limit)->select(); 
		if(!$res){ 
		$res=array();
		}else{
			$UserModel=D("User");
			
			for($i=0;$i<count($res);$i++){
				$res[$i]["usermsg"]=$UserModel->getusermsg1byuserid($res[$i]["userid"]);
			}
		}
	
		
		return $res;	
    }
	
	
	/**
     * 根据商品id商品好评度
	  *@param  $product_id   商品i 
     */
    public function getgoodevaluatebyproid($product_id){
		$where="product_id=".$product_id;
		$where1="product_id=".$product_id." and evaluate_star>=3";
		
		$res=$this->where($where)->count(); 
		$res1=$this->where($where1)->count(); 
		if($res==0){ 
			$msg="无";
		}else{
			$num=round($res1*100/$res);
			$msg=$num."%";
		}
	
		
		return $msg;	
    }
	
	
	//------------------------------------华丽的分割线------------------------//

    /**
     * 根据商品id获取商品评价信息
     */
    public function getProductIdByEvaluateList($product_id,$limit = ''){
        $res = $this->alias('a')
            ->field('a.evaluate_star,a.evaluate_msg,a.userid as comment_userid,a.addtime,b.username as comment_username,b.headimg as comment_headimg')
            ->join('left join user as b on b.userid = a.userid')
            ->where(['product_id'=>$product_id])
            ->order('addtime desc')
            ->limit($limit)
            ->select();
        if (!$res){
            $res = [];
        }else{
            foreach ($res as &$v) {
                $v['addtime'] = put_time($v['addtime']);
                $v['comment_headimg'] = imgpath($v['comment_headimg']);
            }
        }
        return $res;
    }

    /**
     *  根据商品id 获取评论列表
     */
    public function getEvaluateList($product_id,$pageIndex,$pageSize){
        $res = $this->alias('a')
            ->field('a.evaluate_star,a.evaluate_msg,a.userid as comment_userid,a.addtime,b.username as comment_username,b.headimg as comment_headimg')
            ->join('left join user as b on b.userid = a.userid')
            ->where(['product_id'=>$product_id])
            ->order('addtime desc')
            ->page($pageIndex . "," . $pageSize)
            ->select();
        if (!$res){
            $res = [];
        }else{
            foreach ($res as &$v) {
                $v['comment_headimg'] = imgpath($v['comment_headimg']);
            }
        }
        return $res;
    }
}