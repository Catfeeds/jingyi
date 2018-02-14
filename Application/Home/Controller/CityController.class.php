<?php
namespace Home\Controller;
use Think\Controller;
//会员管理
class CityController extends CommonController {

     /**
     *  获取全部城市
     */
    public function getAllCity(){
	$model = M('City');
        $city = $model->select();
        echoOk(200,"获取成功",$city);
    }

    /*
     * 获取全部省
     */
    public function getAllProvince() {
        $model = M('Province');
        $result = $model->select();
        echoOk(200,"获取成功",$result);
    }
    
    /*
     * 根据省id获取市
     */
    public function getCityFromProvinceid() {
        $province_id = I('province_id');
        $model = M('City');
        $where = " province_id =".$province_id;
        $result = $model->where($where)->select();
        echoOk(200,"获取成功",$result);
    }
    
    /*
     * 根据市id获取区
     */
    public function getCountyFromCityid() {
        $city_id = I('city_id');
        $model = M('County');
        $where = " city_id =".$city_id;
        $result = $model->where($where)->select();
        echoOk(200,"获取成功",$result);
    }
    
	
	  /**
     *  获取热门城市
     */
    public function gethotCity(){
	    $model = M('City');
		$where=I("search")?"city like '%".I("search")."%'":"";
		$allcity=$model->where($where)->select();
		if(!$allcity){$allcity=array();}
        $res["allcity"] = $allcity;
		$hotcity= $model->where("status=1")->select();
		if($hotcity){
			$res["hotcity"] =	$hotcity;
			}else{
				$res["hotcity"] =	array();
				}
		
        echoOk(200,"获取成功",$res);
    }
    

}
