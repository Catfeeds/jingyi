<?php

namespace Common\Model;

use Think\Model;

/* * 邀请问题* */

class QuestionModel extends Model {
	
    /*
     * 获取用户邀请问题信息列表
     * @param  $tel 
     */

    public function getlist($tel) {
		$where="tel='".$tel."'";
		$info=$this->where($where)->find();
        for($i=0;$i<42;$i++){   //问题有多少 就设置多少 数量
			$key=$i+1;
			if($key<42){    //婚姻在APP中不显示
				$k=$i;
			 }else if($key>42){
				$k=$i-1;
			}else{
			   continue ;
				}
		    $res[$k]["question_id"]=$key;
			$res[$k]["question_title"]=C("question".$key);
			if(!$info){
				$res[$k]["answer"]="";
				}else{
					$res[$k]["answer"]=$info["question".$key];
					}

		}
		return $res;
    }
	
	/*
     * 获取用户邀请问题信息列表
     * @param  $tel 
     */

    public function addpostq() {
		
        for($i=0;$i<42;$i++){   //问题有多少 就设置多少 数量   
			$key=$i+1;
			if($key<42){    //婚姻在APP中不显示
				$k=$i;
			 }else if($key>42){
				$k=$i-1;		 
			}else{
			   continue ;	
				}
			$data[$k]["question_title"]=C("question".$key);	
			
		}
		$QuestionBankModel=D("QuestionBank");
		$res=$QuestionBankModel-> addAllpost($data);
		return $res;
    }
	
	 /*
     * 修改邀请问题答案
     * @param  $tel 
     */

    public function editpost($tel,$data) {
		$where="tel='".$tel."'";
		$res=$this->where($where)->save($data);
		if($res===0){
			$res=true;
			}
      return $res;
		
    }
	
	 /*
     * 添加邀请问题答案
     * @param  $tel 
     */

    public function addpost($data) {
		
		$res=$this->add($data);
		
      return $res;
		
    }
	
		 /*
     * 判断是否存在信息
     */

    public function ischeck($tel) {
		$where="tel='".$tel."'";
		$res=$this->where($where)->find();
      return $res;
		
    }
	
	

}
