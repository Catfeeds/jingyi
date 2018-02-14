<?php

namespace Home\Controller;

use Common\Model\Question2Model;
use Think\Controller;

class IUserController extends CommonController
{

    /**
     *  获取用户信息
     */
    public function getUserinfoByUserid()
    {
        $userid = I('userid');
        $UserModel = D("User");
//        $QuestionModel = D("Question");
        $QuestionModel = new Question2Model();
        $res = $UserModel->getusermsgbyuserid($userid);
        $WalletWaterModel = D("WalletWater");
        $res["moneynum"] = $WalletWaterModel->getMoneynumByuserid($userid);
        $UserLevelModel = D("UserLevel");
        $res["levelmsg"] = $UserLevelModel->getuserlevelinfobyuserid($userid);
        $UserPlanetFollowModel = D("UserPlanetFollow");
        $UserPostsCollectionModel = D("UserPostsCollection");
        $res["fansnum"] = $UserPlanetFollowModel->getuserFansnum($userid);    //粉丝数量
        $res["collectionnum"] = $UserPostsCollectionModel->getCollectionnumbyuserid($userid);     //收藏数量
        $res["attentionnum"] = $UserPlanetFollowModel->getuserAttentionnum($userid);     //关注数量
        $res["questionlist"] = $QuestionModel->getlist($res['tel']);     //邀请问题信息
        if ($res) {   //获取成功
            $msg = "获取成功";
            get_api_result(200, $msg, $res);
        } else {  //获取失败
            $msg = "获取失败";
            get_api_result(300, $msg);
        }
    }

    /**
     *  获取用户推荐信息
     */
    public function getTuiinfoByUserid()
    {
        $userid = I('userid');
        $WalletWaterModel = D("WalletWater");
        $UserModel = D("User");
        $res["allpeople"] = $UserModel->getTuinumByuserid($userid);                   //总推荐人数
        $res["alltuimoney"] = $WalletWaterModel->getAllReturnMoneynumByuserid($userid);          //累计返利
        $res["searchyear"] = date("Y");                      //默认查询年
        $res["searchmonth"] = date("m");                     //默认查询月
        $res["searchlist"]["peoplenum"] = $UserModel->getTuinumByuseridAndMonth($userid, $res["searchyear"], $res["searchmonth"]); //查询结果(当月推荐人数)
        $res["searchlist"]["moneynum"] = $WalletWaterModel->getReturnMoneynumByuseridAndMonth($userid, $res["searchyear"], $res["searchmonth"]); //查询结果(当月返利)

        if ($res) {   //获取成功
            $msg = "获取成功";
            get_api_result(200, $msg, $res);
        } else {  //获取失败
            $msg = "获取失败";
            get_api_result(300, $msg);
        }
    }

    /**
     *  根据年月获取用户推荐信息
     * @param  $userid  用户id
     * @param  $searchyear  年
     * @param  $searchmonth  月
     */
    public function getSearchTuiinfo()
    {
        $userid = I('userid');
        $searchyear = I('searchyear');
        $searchmonth = I('searchmonth');
        $WalletWaterModel = D("WalletWater");
        $UserModel = D("User");
        $res["searchlist"]["peoplenum"] = $UserModel->getTuinumByuseridAndMonth($userid, $searchyear, $searchmonth); //查询结果(当月推荐人数)
        $res["searchlist"]["moneynum"] = $WalletWaterModel->getReturnMoneynumByuseridAndMonth($userid, $searchyear, $searchmonth); //查询结果(当月返利)

        if ($res) {   //获取成功
            $msg = "获取成功";
            get_api_result(200, $msg, $res);
        } else {  //获取失败
            $msg = "获取失败";
            get_api_result(300, $msg);
        }
    }

    /**
     *  修改手机号码
     */
    public function editaccount()
    {
        $userid = I('userid');
        $data["tel"] = I("phone");   //新手机号码
        $data["countrynum"] = I("countrynum");   //新手机号码国际编码
        $code = I("code");   //验证码

        $UserModel = D("User");
        $ischecktel = $UserModel->ischeckeditaccount($data["tel"], $data["countrynum"], $userid);

        if ($ischecktel) {
            $msg = "账号已存在，不可修改！";
            get_api_result(300, $msg);
        }

        $TelverifyModel = D("Telverify");
        $user = M('user')->where(['userid' => $userid])->field('tel,countrynum')->find();
        $ischeck = $TelverifyModel->checkcode($user["tel"], $code, $user["countrynum"]);  //验证码
//        $ischeck = $TelverifyModel->checkcode($data["tel"], $code, $data["countrynum"]);  //验证码

        if ($ischeck == 2) {   //验证码错误
            $msg = "验证码错误";
            get_api_result(300, $msg);
        }
        if ($ischeck == 3) {   //验证码超时
            $msg = "验证码超时";
            get_api_result(300, $msg);
        }

        $res = $UserModel->editpostbyuserid($userid, $data);
        if ($res) {   //获取成功
            $res1 = $UserModel->getusermsgbyuserid($userid);
            $msg = "手机号码修改成功,请重新登陆";
            get_api_result(200, $msg, $res1);
        } else {  //获取失败
            $msg = "修改失败";
            get_api_result(300, $msg);
        }
    }


    /**
     *  修改联系手机号码
     */
    public function editphone()
    {
        $userid = I('userid');
        $data["phone"] = I("phone");   //修改手机号码
        $data["phonecountrynum"] = I("phonecountrynum");   //修改手机号码
        $UserModel = D("User");
        $res = $UserModel->editpostbyuserid($userid, $data);
        if ($res) {   //获取成功
            $res1 = $UserModel->getusermsgbyuserid($userid);
            $msg = "联系方式修改成功！";
            get_api_result(200, $msg, $res1);
        } else {  //获取失败
            $msg = "修改失败";
            get_api_result(300, $msg);
        }
    }


    /**
     *  修改支付密码
     */
    public function editpaypassword()
    {
        $userid = I('userid');
        $data["paypassword"] = md5(I("paypassword"));   //支付密码
        $UserModel = D("User");
        $res = $UserModel->editpostbyuserid($userid, $data);
        if ($res) {   //获取成功
            $res1 = $UserModel->getusermsgbyuserid($userid);
            $msg = "修改成功";
            get_api_result(200, $msg, $res1);
        } else {  //获取失败
            $msg = "修改失败";
            get_api_result(300, $msg);
        }
    }

    /**
     *  判断支付密码是否正确
     */
    public function checkpaypassword()
    {
        $userid = I('userid');
        $password = md5(I("paypassword"));   //支付密码
        $UserModel = D("User");
        $res = $UserModel->checkpayPassword($userid, $password);
        if ($res) {   //获取成功
            $res1 = $UserModel->getusermsgbyuserid($userid);
            $msg = "支付密码正确";
            get_api_result(200, $msg, $res1);
        } else {  //获取失败
            $msg = "支付密码错误";
            get_api_result(300, $msg);
        }
    }

    /**
     *  修改个人信息
     */
    public function editUserinfo()
    {
        $userid = I('userid');
        if (I("tel")) {
            $data["tel"] = I("tel");   //修改手机号码
        }
        if (I("paypassword")) {
            $data["paypassword"] = I("paypassword");
        }


        $UserModel = D("User");
        $res = $UserModel->editpostbyuserid($userid, $data);
        if ($res) {   //获取成功
            $res1 = $UserModel->getusermsgbyuserid($userid);
            $msg["code"] = 2006;
            get_api_result(200, $msg, $res1);
        } else {  //获取失败
            $msg["code"] = 3013;
            get_api_result(300, $msg);
        }
    }

    /**
     *  添加收货地址
     */
    public function addUserAddress()
    {
        $UseraddressModel = D("Useraddress");
        $data["userid"] = I('userid');
        $data["name"] = shielding(I('name'));
        $data["tel"] = I('tel');
        $data["provinceid"] = I('provinceid');
        $data["cityid"] = I('cityid');
        $data["countyid"] = I('countyid');
        $data["address"] = shielding(I('address'));

        $ischeck = $UseraddressModel->checkUseraddress($data["userid"]);
        if (!$ischeck) {
            $data["status"] = 1;
        } else {
            $data["status"] = 0;
        }
        $res = $UseraddressModel->addpost($data);
        if ($res) {   //添加成功
            $res1 = $UseraddressModel->getUseraddressinfoByid($res);
            $msg = "添加成功";
            get_api_result(200, $msg, $res1);
        } else {  //添加失败
            $msg = "添加失败";
            get_api_result(300, $msg);
        }
    }

    /**
     *  修改收货地址
     */
    public function editUserAddress()
    {
        $id = I('id');
        $userid = I('userid');
        if (I('name')) {
            $data["name"] = shielding(I('name'));
        }
        if (I('tel')) {
            $data["tel"] = I('tel');
        }
        if (I('provinceid')) {
            $data["provinceid"] = I('provinceid');
        }
        if (I('cityid')) {
            $data["cityid"] = I('cityid');
        }
        if (I('address')) {
            $data["address"] = shielding(I('address'));
        }
        if (I('countyid')) {
            $data["countyid"] = I('countyid');
        }

        $UseraddressModel = D("Useraddress");
        $ischeck = $UseraddressModel->checkUseraddressByid($userid, $id);
        if (!$ischeck) {
            $msg = "非法操作！";
            get_api_result(300, $msg, $res);
        }
        $res = $UseraddressModel->editpost($id, $data);
        if ($res) {   //添加成功
            $res1 = $UseraddressModel->getUseraddressinfoByid($id);
            $msg = "修改成功！";
            get_api_result(200, $msg, $res1);
        } else {  //添加失败
            $msg = "修改失败！";
            get_api_result(300, $msg);
        }
    }

    /**
     *  设定默认收货地址
     */
    public function editUserAddressStatus()
    {
        $id = I('id');
        $userid = I('userid');
        $UseraddressModel = D("Useraddress");
        $ischeck = $UseraddressModel->checkUseraddressByid($userid, $id);
        if (!$ischeck) {
            $msg = "非法操作！";
            get_api_result(300, $msg, $res);
        }
        $res = $UseraddressModel->editstatuspost($id, $userid);
        if ($res) {   //添加成功
            $result = $UseraddressModel->getTrueUseraddress($userid);
            $msg = "修改成功！";
            get_api_result(200, $msg, $result);
        } else {  //添加失败
            $msg = "修改失败！";
            get_api_result(300, $msg);
        }
    }

    /**
     *  获取用户全部收货地址
     */
    public function getUserAddressByUserid()
    {
        $userid = I('userid');
        $UseraddressModel = D("Useraddress");
        $res = $UseraddressModel->getUseraddressinfo($userid);
        $msg = "获取成功";
        get_api_result(200, $msg, $res);
    }

    /**
     *  获取用户默认地址
     */
    public function getTrueUseraddress()
    {
        $userid = I('userid');
        $UseraddressModel = D("Useraddress");
        $res = $UseraddressModel->getTrueUseraddress($userid);
        $msg = "获取成功";
        get_api_result(200, $msg, $res);
    }

    /**
     *  删除收货地址
     */
    public function delUserAddress()
    {
        $id = I('id');
        $userid = I('userid');
        $UseraddressModel = D("Useraddress");
        $info = $UseraddressModel->checkUseraddressByid($userid, $id);
        if (!$info) {
            $msg = "操作失败，请刷新后再次操作";
            get_api_result(300, $msg);
        }

        $res = $UseraddressModel->delpost($id);
        if ($res) {   //删除成功
            $msg = "操作成功";
            get_api_result(200, $msg);
        } else {  //删除失败
            $msg = "操作失败";
            get_api_result(300, $msg);
        }
    }

    /**
     *  修改用户当前定位国家
     */
    public function setcountryidByuserid()
    {
        $userid = I('userid');
        $data["nowcountryid"] = I('nowcountryid');
        $UserModel = D("User");
        $res = $UserModel->editpostbyuserid($userid, $data);
        $msg["code"] = 2006;
        get_api_result(200, $msg);
    }

    /**
     *  获取中国省市json文本
     */
    public function text()
    {

        $ProvinceModel = D("Province");
        $res = $ProvinceModel->getlist();
        for ($i = 0; $i < count($res); $i++) {
            $res[$i]["cities"] = $ProvinceModel->getcitylist($res[$i]["province_id"]);
            for ($j = 0; $j < count($res[$i]["cities"]); $j++) {
                $res[$i]["cities"][$j]["counties"] = $ProvinceModel->getcountylist($res[$i]["cities"][$j]["city_id"]);
            }
        }

        for ($k = 0; $k < count($res); $k++) {
            for ($l = 0; $l < count($res[$k]["cities"]); $l++) {
                for ($ll = 0; $ll < count($res[$k]["cities"][$l]["counties"]); $ll++) {
                    $data[$res[$k]["province"]][$res[$k]["cities"][$l]["city"]][$ll] = $res[$k]["cities"][$l]["counties"][$ll]["county"];
                }
            }
        }
        $msg["code"] = 2006;
        get_api_result(200, $msg, $data);
    }


    /**
     *  修改问题答案
     */
    public function editquestion()
    {
        $userid = I('userid');
        $question_id = I('question_id');
        if (empty($question_id)) {
            get_api_result(300, "非法操作");
        }
        $data["question" . $question_id] = shielding(I('answer'));

        $UserModel = D("User");
        $QuestionModel = D("Question");
        $info = $UserModel->getusermsgbyuserid($userid);
        $ischeck = $QuestionModel->ischeck($info["tel"]);
        if ($ischeck) {
            $res = $QuestionModel->editpost($info["tel"], $data);
        } else {
            $data["tel"] = $info["tel"];
            $res = $QuestionModel->addpost($data);
        }

        if ($res) {   //修改成功
            $msg = "修改成功";
            get_api_result(200, $msg);
        } else {  //删除失败
            $msg = "修改失败";
            get_api_result(300, $msg);
        }
    }


    /**
     *  修改用户声音一键屏蔽状态
     */
    public function setsoundstatus()
    {
        $userid = I('userid');
        $data["soundstatus"] = I('soundstatus');  // 0|不屏蔽  1|屏蔽
        $UserModel = D("User");
        $res = $UserModel->editpostbyuserid($userid, $data);
        $msg = "设置成功";
        get_api_result(200, $msg);
    }

    /**
     * 修改系统消息接收与否
     */
    public function system_msg()
    {
        $user_id = I('post.userid');
        $system_msg = I('system_msg') == 1 ? I('system_msg') : 0;
        if (!$user_id) {
            get_api_result(201, '用户id错误');
            exit;
        }

        $user = M('user')->where(['userid' => $user_id])->find();
        if (!$user) {
            get_api_result(201, '未找到该用户');
            exit;
        }
        $res = M('user')->where(['userid' => $user_id])->save(['system_msg' => $system_msg]);
        $msg = $system_msg == 1 ? '屏蔽' : '开启';
        if ($res) {
            get_api_result(200, '成功' . $msg . '系统消息');
            exit;
        } else {
            get_api_result(200, $msg . '系统消息失败');
            exit;
        }
    }

    /**
     * 修改星际语言开始关闭
     */
    public function air_language()
    {
        $user_id = I('post.userid');
        $air_language = I('air_language') == 1 ? I('air_language') : 0;
        if (!$user_id) {
            get_api_result(201, '用户id错误');
            exit;
        }
        $user = M('user')->where(['userid' => $user_id])->find();
        if (!$user) {
            get_api_result(201, '未找到该用户');
            exit;
        }
        $res = M('user')->where(['userid' => $user_id])->save(['air_language' => $air_language]);
        $msg = $air_language == 1 ? '开启' : '关闭';
        if ($res) {
            get_api_result(200, '成功'.$msg.'星际语言');
            exit;
        } else {
            get_api_result(200, $msg.'星际语言失败');
            exit;
        }
    }
    /**
     * 用户问题回答
     */
    public function question_answer(){
        $userid=I('post.userid');
        $question_id=I('post.question_id');
        $answer=shielding(I('post.question_answer'));
        if(!$userid || !$question_id || !$answer){
            get_api_result(300, '数据不完整');
            exit;
        }
        $question=M('question')->where(['question_id'=>$question_id])->find();
        if(!$question){
            get_api_result(301, '没有此问题');
            exit;
        }
        $question_answer=M('question_answer')->where(['userid'=>$userid,'question_id'=>$question_id])->find();
        if($question_answer){//修改
            $result=M('question_answer')->where(['userid'=>$userid,'question_id'=>$question_id])->save(['answer'=>$answer]);
            if($result){
                get_api_result(200, '操作成功');
                exit;
            }
        }else{//添加
            $result=M('question_answer')->add(['answer'=>$answer,'userid'=>$userid,'question_id'=>$question_id]);
            if($result){
                get_api_result(200, '操作成功');
                exit;
            }
        }
        get_api_result(200, '操作失败');
        exit;
    }
    /**
     * 用户成长值列表
     */
    public function user_level_water()
    {
        $userid = I('post.userid');
        $page = I('post.page', 1);
        $limit = I('post.limit', 10);
        $trek = M('user')->where(['userid' => $userid])->find();
        if (!$trek) {
            get_api_result(300, '没有此用户');
        }

        $result = M('userlevel_water')->where(['userid' => $userid])->page($page, $limit)->select();
        $all_sroce = M('userlevel_water')->where(['userid' => $userid])->getField('sum(sroce)');
        foreach ($result as &$item) {
            if($item['sroce']>0){
                $item['sroce']='+'.$item['sroce'];
            }
        }

        if (!$result) {
            get_api_result(300, '获取失败');
        }
        get_api_result(200, '获取成功', ['all_sroce'=>$all_sroce,'list'=>$result]);

    }

}
