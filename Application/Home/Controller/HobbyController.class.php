<?php
namespace Home\Controller;
use Think\Controller;
//会员管理
class HobbyController extends CommonController {

     /**
     *  获取全部爱好标签
     */
    public function all_hobby(){
	$model = M('Hobby');
        $hobby = $model->select();
        get_api_result(200, "修改成功", $hobby);
    }

    
    

}
	
	
	 