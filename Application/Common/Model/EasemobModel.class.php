<?php

namespace Common\Model;

use Think\Model;

/* * 环信* */

class EasemobModel extends Model
{


    public function __construct()
    {
        vendor("huanxin.Easemob");
        $options['client_id'] = C("hx_client_id");
        $options['client_secret'] = C("hx_client_secret");
        $options['org_name'] = C("hx_org_name");
        $options['app_name'] = C("hx_app_name");
        $this->Easemob = new \Easemob($options);

    }


    /**
     *  注册环信会员信息
     * @param string $password 密码
     * @param string $username 账号
     * @param string $username1 空间站账号
     */
    public function register_hx($username, $username1, $password)
    {

        $res["status"] = false;
        $request_str = $this->Easemob->createUser($username, $password); //注册用户账号
        if (empty($request_str['entities'])) {
            return false;
        }
        if ($request_str) {
            //$request_kj_str = $this->Easemob->createUser($username1,$password);  //注册用户空间站账号
            //if(!$request_kj_str){
            //$this->Easemob-> deleteUser($username); //回滚
            //}else{
            //$res["request_kj_str"]=$request_kj_str;
            $res["request_str"] = $request_str;
            $res["status"] = true;
            //}
        }
        return $res;
    }

    /**
     *  注册环信群
     * $options ['groupname'] = "group001";
     * $options ['desc'] = "this is a love group";
     * $options ['public'] = true;
     * $options ['owner'] = "zhangsan";
     * $options['members']=Array("wangwu","lisi");
     */
    public function register_hx_group($data)
    {
        $data["public"] = true;
        $request_str = $this->Easemob->createGroup($data); //注册用户账号

        if ($request_str) {
            $res["hx_groupid"] = $request_str["data"]["groupid"];
        }
        return $res;
    }


    /**
     *  环信群加人
     */
    public function register_hx_group_add($group_id, $usernames)
    {

        $res = $this->Easemob->addGroupMembers($group_id, $usernames);
        return $res;
    }


    /**
     *  环信群踢人
     * username: ['asdfghj', 'wjy6'],
     * group_id: '1480841456167'
     */
    public function register_hx_group_del($group_id, $usernames)
    {
        $res = $this->Easemob->deleteGroupMembers($group_id, $usernames);
        return $res;
    }

    /**
     *  环信群修改信息
     * subject: 'ChangeTest',    // 群组名称
     * description: 'Change group information test',  // 群组简介
     */
    public function register_hx_group_edit($group_id, $data)
    {
        $res = $this->Easemob->modifyGroupInfo($group_id, $data);
        return $res;
    }

    /**
     *  环信群解散
     */
    public function register_hx_group_destroy($group_id)
    {
        $res = $this->Easemob->deleteGroup($group_id);
        return $res;
    }

    /*
    群组单个减人
      */
    public function register_hx_group_leave($group_id, $username)
    {
        $res = $this->Easemob->deleteGroupMember($group_id, $username);
        return $res;
    }

    /**
     *  发送消息
     */
    public function sendText($from, $target_type, $target, $content, $ext)
    {
        $res = $this->Easemob->sendText($from, $target_type, $target, $content, $ext);
        return $res;
    }

    /**
     * @param $username   用户名
     * @return bool
     * 删除环信用户
     */
    public function delete_user($username)
    {
        $result = $this->Easemob->deleteUser($username);
//        var_dump($result);exit;
        if (empty($result['entities'])) {
            return false;
        }
        return true;
    }


}
