<?php

namespace Common\Model;

use Think\Model;

/* * 标签* */

class StarSignModel extends Model {
	  protected $tableName = 'star_sign'; 
    
	
	
	
	/*
     *  根据标签id串获取标签名称
	  * @param  $starsignidstr   标签id
	*/

    public function getStarSignnamebyidstr($starsignidstr) {
		$where="star_id in (".$starsignidstr.")";
		$starsign=$this->where($where)->getField('name',true);
		$res= implode(',',$starsign);  //标签名称
        return $res;
       
    }
	


}
