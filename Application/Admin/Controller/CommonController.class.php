<?php
namespace Admin\Controller;
use Think\Controller;
class CommonController extends Controller {
	
     public function _initialize(){
	 header("Content-Type:text/html; charset=utf-8");
      if (!session('?admin_key_id')){
				$this->redirect('Login/index');
			}
    }
}