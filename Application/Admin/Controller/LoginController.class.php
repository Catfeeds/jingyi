<?php
namespace Admin\Controller;
use Think\Controller;
class LoginController extends Controller {
	
    public function _initialize(){
        header("Content-Type:text/html; charset=utf-8");
    }

	 //登陆
    public function index(){
		
    	$this->display();
    }

    //登陆验证
    public function check(){
		$account=$_POST["account"];
		$password=md5($_POST["password"]);
		$code=$_POST["code"];
		if(!$this->verifyCheck($code, $id = '')){
               $this->error("亲，验证码输错了哦！");
        }
		$Modle=M("admin");
//		$where1="admin='".$account."'";
		$where1=['admin'=>$account];
		$ischeckaccount=$Modle->where($where1)->find();
		if(!$ischeckaccount){
			 $this->error("账号不存在！");  
        }

        $ischeck = $Modle->where(['account' => $account, 'password' => $password, 'status' => 1])->find();
		if(!$ischeck){
			 $this->error("密码输入错误！");  
        }
		
		if($ischeck["status"]==2){
			 $this->error("账号被冻结，请联系管理员！");  
        }
			
		session('[start]');
		session('admin_key_id',$ischeck["id"]);  //设置session
		session('admin_key_name',$ischeck["name"]);  //设置session
		session('admin_key_admin',$ischeck["admin"]);  //设置session
		session('admin_key_auth',explode(",",$ischeck["auth"]));  //设置session
		
		/*$option=D("Option");
		$opdata["content"]="登陆了后台管理系统。";
		$option->add($opdata);*/
		
		$this->redirect('Index/index',array(), 1, '登陆成功，页面跳转中...');
    }
	
	
	//退出登录
	 public function loginout(){
	    session('[destroy]'); // 销毁session
		$this->redirect('Login/index',array(), 1, '登出成功，页面跳转中...');
	}
	
	// 生成验证码  
 	public function verifycode()//这是一个固定的格式
 	{  
	    ob_clean();
  		$Verify = new \Think\Verify();
		$Verify->fontSize = 30;
		$Verify->length   = 4;
		$Verify->useNoise = false;
        $Verify->entry();
 	}
	
 	//检验验证码是否正确  
 	public function verifyCheck($code, $id = ''){     
  		 $verify = new \Think\Verify();
          return $verify->check($code, $id);
 	}
 	public function test(){
// 	    set_time_limit(0);
// 	    $country=M('tblcountryabbr')->field('Cname,Abbr')->select();
// 	    $res=[];
// 	    foreach ($country as $item){
// 	        $res[]=['country'=>$item['cname'],'country_code'=>$item['abbr']];
//        }
//        $num=M('country2')->addAll($res);
//        var_dump($num);
//        $tbl=M('tbl_city')->field('DISTINCT state_zh,country_code')->select();
//        $num=0;
//        foreach ($tbl as $item){
//            if($item['state_zh']){
//                $country_id=M('country2')->where(['country_code'=>$item['country_code']])->getField('country_id');
//                if($country_id){
//                    $num++;
//                    M('province2')->add(['province'=>$item['state_zh'],'country_id'=>$country_id,'created_time'=>time()]);
//                }
//            }
//
//        }
//        echo $num;exit;

//        $tbl=M('tbl_city')->select();
//        foreach ($tbl as $item){
//            if(!$item['city_zh']){
//                M('tbl_city')->where(['id'=>$item['id']])->save(['city_zh'=>$item['state_zh']]);
//            }
//        }
//        echo '123';exit;
//        $num=0;
//        $tbl=M('tbl_city')->field('city_zh,state_zh')->select();
//        foreach ($tbl as &$item){
//            if($item['city_zh']){
//                $province_id=M('province2')
//                    ->where(['province'=>$item['state_zh']])
//                    ->getField('province_id');
//                M('city2')->add(['city'=>$item['city_zh'],'province_id'=>$province_id]);
//                $num++;
//            }
//
//        }
//        echo $num;
//        $num=0;
//        $county=M('county')
//            ->join('left join city on city.city_id=county.city_id')->select();
//        foreach ($county as $item){
//            $item['city']=preg_replace('/市/', "", $item['city']);
//            $city_id=M('city2')->where(['city'=>['like','%'.$item['city'].'%']])->getField('city_id');
//            if($city_id){
//                M('county2')->add(['city_id'=>$city_id,'county'=>$item['county'],'code'=>$item['code']]);
//            }
//
//        }
//        echo $num;

    }
}