<?php

namespace Common\Model;

use Think\Model;

/* * 用户信息* */

class UserModel extends Model
{


    /*
     * 通过用户手机号获取用户详情
     * @param  $tel  电话号码
     */

    public function getUserinfoByTel($tel, $countrynum = "+86")
    {
        if ($countrynum == "") {
            $countrynum = "+86";
        }
        $Model = M("user");
        $where = "tel='" . $tel . "' and countrynum='" . $countrynum . "'";
        $res = $Model->where($where)->find();
        if ($res) {
            $UserPlanetModel = D("UserPlanet");
            $res["userplanetinfo"] = $UserPlanetModel->getUserPlanetInfo($res["userid"]);
            $res["userplanetstatus"] = $UserPlanetModel->checkUserPlanet($res["userid"]);

        }
        if (!$res) {
            $res = array();
        }  //查询不到 返回空数组

        return $res;
    }


    /*
    * 通过用户手机号判断是否能修改
    * @param  $tel  电话号码
     * @param  $countrynum  电话号码国际编码
     * @param  $userid  修改用户id
     * @return  bool  ture 不可修改，重复 false 为可修改
    */

    public function ischeckeditaccount($tel, $countrynum = "+86", $userid)
    {
        if ($countrynum == "") {
            $countrynum = "+86";
        }
        $Model = M("user");
        $where = "tel='" . $tel . "' and countrynum='" . $countrynum . "' and userid <>" . $userid;
        $info = $Model->where($where)->find();

        if (!$info) {
            $res = false;
        } else {
            $res = true;
        }

        return $res;
    }


    /*
     * 用户添加
     * @param  $data  包括 电话号码tel  密码 password 
     */

    public function registerpost($data)
    {
        $Model = M("user");
        $data["status"] = 1;
        $data["addtime"] = time();
        $res = $Model->add($data);
        return $res;
    }

    /*
     * 用户信息更新
     * @param  $data   
     */

    public function editpostbytel($tel, $data, $countrynum)
    {
        if (empty($countrynum)) {
            $countrynum = "+86";
        }
        $Model = M("user");
        $where = "tel='" . $tel . "' and countrynum='" . $countrynum . "'";
        $res = $Model->where($where)->save($data);
        if ($res === 0) {  // 更新数据和原始数据一样 默认更新成功
            $res = true;
        }
        return $res;
    }

    /*
     * 用户信息更新
     * @param  $data  
     */

    public function editpostbyuserid($userid, $data)
    {
        $Model = M("user");
        $where = "userid=" . $userid;
        $res = $Model->where($where)->save($data);
        if ($res === 0) {  // 更新数据和原始数据一样 默认更新成功
            $res = true;
        }
        return $res;
    }

    /*
     * 获取用户个人信息
     * @param  $data  、
     */

    public function getusermsgbyuserid($userid)
    {
        $Model = M("user");
//        $where = "userid=" . $userid;
        $where = ['userid'=>$userid];
        $res = $Model->where($where)->find();
        if (!$res) {
            $res = array();
            return $res;
        }  //查询不到 返回空数组

        $UserairshipsetModel = D("Userairshipset");
        $res["userairshipstatus"] = $UserairshipsetModel->getstatus($userid);   //空间站匿名状态


        $UserPlanetModel = D("UserPlanet");
        if ($res) {
            if ($res["pid"] != 0) {
                $res["parentinfo"] = $Model->field("username,tel,userid")->where("userid=" . $res["pid"])->find();
                $res["parentinfo"]["userplanetstatus"] = $UserPlanetModel->checkUserPlanet($res["pid"]);
            }
            $res["headimg"] = imgpath($res["headimg"]);
            $hobbyidstr = substr(str_replace('|', ",", str_replace('#', "", $res["hobbyid"])), 0, -1);
            if (empty($hobbyidstr) || $hobbyidstr == "") {
                $res["hobbyname"] = "";  //爱好名称
                $res["hobbyid"] = "";
            } else {
                $hobby = M("hobby")->where("hobbyid in (" . $hobbyidstr . ")")->getField('hobbyname', true);
                $res["hobbyname"] = implode('/', $hobby);  //爱好名称
                $res["hobbyid"] = $hobbyidstr;
            }

            if (!empty($res["starsignid"])) {
                $res["starsignid"] = substr(str_replace('|', ",", str_replace('#', "", $res["starsignid"])), 0, -1);
                $StarSignModel = D("StarSign");
                $res["starsignname"] = $StarSignModel->getStarSignnamebyidstr($res["starsignid"]);
            } else {

                $res["starsignname"] = "";
            }
            $FriendStarSignModel = D("FriendStarSign");
            $res["otherstarsignname"] = $FriendStarSignModel->getFriendStarSign($userid);
            $ProfessionSignModel = D("ProfessionSign");
            $res["professionsign_name"] = $ProfessionSignModel->getnamebyid($res["professionsign_id"]); //职业名称

        }
        $res['follownum'] = $this->getPlanetFollowNum($userid);
        if ($res) {

            $res["userplanetinfo"] = $UserPlanetModel->getUserPlanetInfo($userid);
            $res["userplanetstatus"] = $UserPlanetModel->checkUserPlanet($userid);

        }

        return $res;
    }

    /*
   * 获取用户个人信息
   * @param  $data  、
   */

    public function getusermsg1byuserid($userid)
    {
        $Model = M("user");
        $where = "userid=" . $userid;
        $res = $Model->where($where)->find();
        $UserLevelModel = D("UserLevel");
        $res["level"] = $UserLevelModel->getuserlevelinfobyuserid($userid)['level'];
        if (!$res) {
            $res = array();
        }  //查询不到 返回空数组
        if ($res) {
            $UserPlanetModel = D("UserPlanet");
            $res["planetstatus"] = $UserPlanetModel->checkUserPlanet($userid);
            $res["headimg"] = imgpath($res["headimg"]);
            if (!empty($res["starsignid"])) {
                $res["starsignid"] = substr(str_replace('|', ",", str_replace('#', "", $res["starsignid"])), 0, -1);
                $StarSignModel = D("StarSign");
                $res["starsignname"] = $StarSignModel->getStarSignnamebyidstr($res["starsignid"]);
            } else {

                $res["starsignname"] = "";
            }
            $FriendStarSignModel = D("FriendStarSign");
            $res["otherstarsignname"] = $FriendStarSignModel->getFriendStarSign($userid);
        }
        return $res;
    }

    /*
     * 获取星球用户关注数
     */
    public function getPlanetFollowNum($planet_userid)
    {
        $model = M('User_planet_follow');
        $where = "planet_userid =" . $planet_userid;
        $result = $model->where($where)->select();
        if (!is_array($result)) {
            $result = array();
        }
        if ($result > 99) {
            $count = "99+";
        }
        $count = count($result);
        return $count;
    }

    /*
     * 判断用户密码是否正确
     * @param  $data   
     */

    public function checkPassword($userid, $password)
    {
        $Model = M("user");
        $where = "userid=" . $userid . " and password='" . $password . "'";
        $res = $Model->where($where)->find();
        if ($res) {
            $res = true;
        } else {
            $res = false;
        }
        return $res;
    }

    /*
     * 判断支付密码是否正确
     * @param  $data   
     */

    public function checkpayPassword($userid, $password)
    {
        $Model = M("user");
        $res = $Model->where(['userid'=>$userid,'paypassword'=>$password])->find();
        if ($res) {
            $res = true;
        } else {
            $res = false;
        }
        return $res;
    }

    /*
   * 通过用户id获取用户总推荐人列表
   */

    public function getTuilistByuserid($userid)
    {
        $Model = M("user");
        $where = "pid=" . $userid;
        $list = $Model->where($where)->select();
        if (count($list) == 0) {
            $msg = array();
        }

        $count = count($list);
        if ($count <= 11) {
            $res = $list;
        }
        shuffle($list);
        $res = array_splice($list, 0, 11);
        if (!empty($res)) {
            for ($i = 0; $i < count($res); $i++) {
                $msg[$i] = $this->getusermsgbyuserid($res[$i]["userid"]);
                //$res[$i]["headimg"]=imgpath($res[$i]["headimg"]);
            }
        }
        return $msg;
    }


    /*
* 通过用户id获取用户父级推荐人列表
*/

    public function getPTuilistByuserid($userid)
    {
        $Model = M("user");
        $where = "userid=" . $userid;
        $list = $Model->where($where)->find();
        if (count($list) == 0) {
            $msg = array();
        } else if ($list["pid"] == 0) {
            $msg = array();
        } else {
            $msg[0] = $this->getusermsgbyuserid($list["pid"]);
        }
        return $msg;
    }


    /*
     * 通过用户id获取用户总推荐人数
     * @param  $userid  用户id
     */

    public function getTuinumByuserid($userid)
    {
        $Model = M("user");
        $where = "userid=" . $userid;
        $mainuserinfo = $Model->where($where)->find();    //获取用户信息
        $keywords = $mainuserinfo["pcon"] . "#" . $mainuserinfo["userid"] . "|";
        $where1 = "pcon like '" . $keywords . "%'";
        $res = $Model->where($where1)->count();
        return $res;
    }

    /*
     * 通过用户id、年、月、获取用户某年某月推荐人数
     * @param  $userid  用户id
     * @param  $searchyear  年
     * @param  $searchmonth  月 
     */

    public function getTuinumByuseridAndMonth($userid, $searchyear, $searchmonth)
    {
        $Model = M("user");
        $where = "userid=" . $userid;
        $mainuserinfo = $Model->where($where)->find();    //获取用户信息
        $keywords = $mainuserinfo["pcon"] . "#" . $mainuserinfo["userid"] . "|";
        $begintime = $searchyear . "-" . $searchmonth . "-01 00:00:00";
        $endtime = $searchyear . "-" . ($searchmonth + 1) . "-01 00:00:00";
        $where1 = "pcon like '" . $keywords . "%' and addtime>=" . strtotime($begintime) . " and addtime<" . strtotime($endtime);
        $res = $Model->where($where1)->count();
        return $res;
    }

    /*
     * 用户账号是否存在
     */
    public function isTelBe($tel)
    {
        $model = M('User');
        $where = " tel =" . $tel;
        $result = $model->where($where)->find();
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * 获取用户余额
     * @param  $data  、
     */
    public function getUserBalance($userid)
    {
        $Model = M("user");
        $where = "userid=" . $userid;
        $res = $Model->field('balance')->where($where)->find();
        if (!$res) {
            $res = array();
        }  //查询不到 返回空数组
        return $res['balance'];
    }

    /*
     * 增加用户余额
     */
    public function addUserBalance($userid, $money)
    {
        $model = M('User');
        $where = " userid =" . $userid;
        $result = $model->where($where)->setInc("balance", $money);
        return $result;
    }


//    /**
//     * 获取用户好友列表
//     */
//    public function getUserFriendid($userid){
//        $Model = M('user');
//        //用户id,用户昵称,个性签名,头像,性别,用户等级
//        $field = "userid,username,autograph,headimg,sex,level";
//        $where = "userid=".$userid;
//        $res = $Model->field($field)->where($where)->find();
//        $res["headimg"]= imgpath($res["headimg"]);
//        if (!$res) {
//            $res = [];
//        }  //查询不到 返回空数组
//        return $res;
//    }

    /**
     * 通过用户昵称搜索用户列表
     */
    public function searchUser($keywords, $limit)
    {
        $Model = M('User');
        //用户id,用户昵称,个性签名,头像,性别,用户等级
        $field = "userid,username,autograph,headimg,sex,level,tel";
        $where = "((username like '%" . $keywords . "%') or tel='" . $keywords . "') and black=0 and status=1";
        $res = $Model->field($field)->where($where)->limit($limit)->select();
//        var_dump(M()->getLastSql());exit;
        if (!$res) {
            $res = array();
        }   //查询不到 返回空数组
        if ($res) {
            for ($i = 0; $i < count($res); $i++) {
                $res[$i]["headimg"] = imgpath($res[$i]["headimg"]);
            }
            shuffle($res);
        }
        return $res;
    }


    public function getuserbasemsgbyuserid($userid)
    {
        $Model = M("user");
        $where = "userid=" . $userid;
        $res = $Model->where($where)->find();
        $res["headimg"] = imgpath($res["headimg"]);
        return $res;
    }

}
