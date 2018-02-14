<?php

namespace Home\Controller;

use Think\Controller;

class ILeavemsgController extends CommonController {

    /**
     * 添加留言 
     */
    public function addpost() {

        $data["userid"] = I("userid");
        $data["message"] = shielding(I("message"));
		
        $LeavemsgModel = D("Leavemsg");
        $res = $LeavemsgModel->addpost($data);
      
        if (!$res) {
            $msg = "反馈失败！";
            get_api_result(300, $msg);
        }else{
       		 $msg = "反馈成功！";
       		 get_api_result(200, $msg);
		}
    }

   

}
