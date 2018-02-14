<?php

namespace Admin\Controller;

use Think\Controller;

class PasswordController extends CommonController
{

    //修改密码
    public function editpassword()
    {
        $this->display();
    }

    public function editpasswordpost()
    {
        $id = session("admin_key_id");
        $data["password"] = md5($_POST["password"]);
        $where = 'id=' . $id;
        $res = M('admin')->where($where)->save($data);
        if ($res) {
            admin_log('修改密码');
            echo "<div id='close' style='display:none;'>1</div>";
            $this->display('Password/editpassword');
        } else {
            echo "<div id='close' style='display:none;'>2</div>";
            $this->display('Password/editpassword');
        }
    }

}