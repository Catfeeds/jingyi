<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/16
 * Time: 16:05
 */

namespace Common\Model;



class Question2Model
{
    public function getlist($tel) {
        $userid=M('user')->where(['tel'=>$tel])->getField('userid');
        $question=M('question')->where(['status'=>0])->field('question_id,name as question_name')->select();
        $res=[];
        if($question){
            foreach ($question as &$item){
                $question_answer=M('question_answer')->where(['question_id'=>$item['question_id'],'question_answer.userid'=>$userid])->getField('answer');
                $question_answer?$item['question_answer']=$question_answer:$item['question_answer']='';

            }
        }
        return $question;
    }

}