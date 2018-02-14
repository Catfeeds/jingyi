<?php
namespace Common\Model;
use Think\Model;

/**验证码**/
class TelverifyModel extends Model{
	
	
	/*
	*获取验证码
	*@param  $tel  电话号码
	*/
     public function getcode($tel,$country_code="+86"){
		 if(empty($country_code)){
			$country_code="+86";
			}
		$ischeck=$this->getcodebytel($tel,$country_code);
		$code=$this->setcode(6);
		if(!$ischeck){
			$res=$this->addcode($tel,$code,$country_code);
		}else{
			$res=$this->updatecode($tel,$code,$country_code);
		}
		if($res){
			if($country_code=="+86"){
				$message=rawurlencode("您的验证码是：" . $code . "。请不要把验证码泄露给其他人。如非本人操作，可不用理会！");
				$res1=$this->sendchinamsgbytel($tel,$message);
				}else{
					$message="Your verification code is ". $code ;
					$res1=$this->sendothermsgbytel($tel,$message,$country_code);
					}
			if($res1){
				 return true;
			}
			return true;
		}
		return false;
	}
	
	/*
	*判断验证码是否正确
	*@param  $tel  电话号码
	*@param  $code  验证码
	*@return  $res  1|验证码正确 2|验证码错误 3|验证码超时
	*/
     public function checkcode($tel,$code,$country_code="+86"){
		  if(empty($country_code)){
			$country_code="+86";
			}
		 $nowtime=time();
		 $Model=M("Telverify");
		 $where="tel='".$tel."' and countrycode='".$country_code."'";
		 $codemsg=$Model->where($where)->find();
		 if(!$codemsg){
			$res=2; 
		 }else{
			 if($codemsg["code"]!=$code){
				$res=2;  
			 }else{
				 if($codemsg["addtime"]+C("codetime")>=$nowtime){
					 $res=1;  
				 }else{
				 	$res=3; 
				 }
			 }	 
		}
		
		return $res;		 
	}
	
	
   /*
	*通过用户手机号获取验证码
	*@param  $tel  电话号码
	*/
     public function getcodebytel($tel,$country_code){
		 if(empty($country_code)){
			$country_code="+86";
			}
		
		$Model=M("Telverify");
		$where="tel='".$tel."' and countrycode='".$country_code."'";
		$res=$Model->where($where)->find();
		return $res;
	}
	
	/*
	*新增验证码
	*@param  $tel  电话号码
	*@param  $code  验证码
	*/
     private function addcode($tel,$code,$country_code){
		  if(empty($country_code)){
			$country_code="+86";
			}
		$Model=M("Telverify");
		$data["tel"]=$tel;
		$data["countrycode"]=$country_code;
		$data["code"]=$code;
		$data["addtime"]=time();
		$res=$Model->add($data);
		return $res;
	}
	
	
	
/*
	*更新用户验证码
	*@param  $tel  电话号码
	*@param  $code  验证码
	*/
     private function updatecode($tel,$code,$country_code){
		  if(empty($country_code)){
			$country_code="+86";
			}
		$Model=M("Telverify");
		$where="tel='".$tel."' and countrycode='".$country_code."'";
		$data["code"]=$code;
		$data["addtime"]=time();
		$res=$Model->where($where)->save($data);
		return $res;
	}
	
	 
	 
	 /**
	 * *
	 * 手机发送信息(中国)
	 */
	private function sendchinamsgbytel($tel,$message)
	{
	  
		$url = 'http://106.ihuyi.cn/webservice/sms.php?method=Submit&account='.C('TELCODE_ACCOUNTA').'&password='.C('TELCODE_PASSWORD').'&mobile='.$tel.'&content='.$message;
		$result = file_get_contents($url);
		$xmlDom = simplexml_load_string ( $result );
		if ($xmlDom->code == '2') {
			return true;
		} else {
		   return false;
		}
	}
	
	
	/**
	 * *
	 * 手机发送信息(国际)
	 */
	private function sendothermsgbytel($tel,$message,$country_code)
	{
		$target = "http://api.isms.ihuyi.com/webservice/isms.php?method=Submit";
		$mobile = $country_code.' '.$tel;//手机号码
		$post_data = "account=".C('TELCODE_ACCOUNTA_OTHER')."&password=".C('TELCODE_PASSWORD_OTHER')."&mobile=".$mobile."&content=".$message;
		$gets =  xml_to_array(Post($post_data, $target));
		if($gets['SubmitResult']['code']==2){
			return true;
		}else{
		return false;
		}
	}
	
	
	/**
	 * *
	 * 手机发送信息
	 */
	private function sendmsgbytel($tel,$message)
	{
	  
		$url = 'http://106.ihuyi.cn/webservice/sms.php?method=Submit&account='.C('TELCODE_ACCOUNTA').'&password='.C('TELCODE_PASSWORD').'&mobile='.$tel.'&content='.$message;
		$result = file_get_contents($url);
		$xmlDom = simplexml_load_string ( $result );
		if ($xmlDom->code == '2') {
			return true;
		} else {
		   return false;
		}
	}
	
	
	
	/**
     * 发送验证码
     *
    public function sendmsgbytel($tel,$message){
 
        

        $target = "http://106.ihuyi.cn/webservice/sms.php?method=Submit";

        $post_data = "account=".C('TELCODE_ACCOUNTA')."&password=".C('TELCODE_PASSWORD')."&mobile=" .$tel. "&content=" .$message ;
        $gets =  xml_to_array(Post($post_data, $target));
        if($gets['SubmitResult']['code']==2){
            return true;
        }else{
           return false;
        }
        
    }
	*/
	
	
	/*
	*验证码生成
	*/
     private function setcode($length = 6){
       return str_pad(mt_rand(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
	}
	
	/**
	 * *
	 * 手机发送信息
	 */
	public function sendmsgbyteltext($tel,$message)
	{
	  $target = "http://106.ihuyi.cn/webservice/sms.php?method=Submit";
	  $post_data = "account=".C('TELCODE_ACCOUNTA')."&password=".C('TELCODE_PASSWORD')."&mobile=".$tel."&content=".rawurlencode("您的验证码是：123456。请不要把验证码泄露给其他人。");
		//$url = 'http://106.ihuyi.cn/webservice/sms.php?method=Submit&account='.C('TELCODE_ACCOUNTA').'&password='.C('TELCODE_PASSWORD').'&mobile='.$tel.'&content='.$message;
		//$result = file_get_contents($url);
		//$xmlDom = simplexml_load_string ( $result );
		
		$result=$this->Post($post_data, $target);
		return $result;
		if ($xmlDom->code == '2') {
			return true;
		} else {
		   return false;
		}
	}
	
	
	private function Post($curlPost,$url){
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_NOBODY, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
		$return_str = curl_exec($curl);
		curl_close($curl);
		return $return_str;
}

}