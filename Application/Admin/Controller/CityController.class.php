<?php

namespace Admin\Controller;

use Think\Controller;

class CityController extends CommonController
{

    public function _initialize()
    {
        parent::_initialize();
        if (session('admin_key_id') != 1) {
            if (!in_array('17', session('admin_key_auth'))) {
                session('[destroy]');
                $this->redirect('Login/index', array(), 1, '无权限...');
            }
        }
    }

    public function index()
    {
        $Model = M("city");
        $list = $Model->select();
        $this->assign('list', $list);
        $this->display();
    }

    //设为/取消上架
    public function set_hot()
    {
        $Model = M("city");
        $id = I('id', 0, 'int');
        $status = I('status', 0, 'int');
        $msg='';
        if ($status) {
            $data["status"] = 0;
            $msg='非热门';
        } else {
            $data["status"] = 1;
            $msg='热门';
        }
        $res = $Model->where(array('city_id' => $id))->save($data);
        if ($res) {
            admin_log('设置为'.$msg.'城市，编号：'.$id);
            $this->ajaxReturn(1);
        } else {
            $this->ajaxReturn(0);
        }
    }


    public function indexprovince()
    {
        $Model = M("province");
        $join = "`country` on `province`.`country_id`=`country`.`country_id`";
        $list = $Model->join($join)->select();
        $this->assign('list', $list);
        $this->display();
    }

    public function addprovince()
    {
        $Model = M("country");
        $countrylist = $Model->select();
        $this->assign('countrylist', $countrylist);

        $this->display();
    }

    public function addprovincepost()
    {
        $Model = M('province');
        $data = $Model->create();
        $data["created_time"] = time();
        $res = $Model->add($data);
        if ($res) {
            admin_log('添加省份，编号为：'.$res);
            echo "<div id='kk' style='display:none'>1</div>";
            $this->display("City/addprovince");
        } else {
            echo "<div id='kk' style='display:none'>2</div>";
            $this->display("City/addprovince");
        }
    }


    public function editprovince()
    {
        $id = $_GET["id"];
        $Model = M("province");
        $where = "province_id=" . $id;
        $res = $Model->where($where)->find();
        $this->assign('info', $res);

        $Model = M("country");
        $countrylist = $Model->select();
        $this->assign('countrylist', $countrylist);
        $this->display();
    }


    public function editprovincepost()
    {
        $Model = M('province');
        $data = $Model->create();
        if ($Model->where("province_id=" . $_POST["id"])->save($data)) {
            $opdata["content"] = "修改了省份信息。编号为:" . $_POST["id"] . "。";
            admin_log($opdata["content"]);
            echo "<div id='kk' style='display:none'>1</div>";
            $this->display("City/editprovince");
        } else {
            echo "<div id='kk' style='display:none'>2</div>";
            $this->display("City/editprovince");
        }
    }


    public function indexcountry()
    {
        $Model = M("country");
        $list = $Model->select();
        $this->assign('list', $list);
        $this->display();
    }

    public function addcountry()
    {
        $this->display();
    }


    public function addcountrypost()
    {
        $Model = M('country');
        $data = $Model->create();
        $res = $Model->add($data);
        if ($res) {
            admin_log('添加国家，编号：'.$res);
            echo "<div id='kk' style='display:none'>1</div>";
            $this->display("City/addcountry");
        } else {
            echo "<div id='kk' style='display:none'>2</div>";
            $this->display("City/addcountry");
        }
    }


    public function editcountry()
    {
        $id = $_GET["id"];
        $Model = M("country");
        $where = "country_id=" . $id;
        $res = $Model->where($where)->find();
        $this->assign('info', $res);
        $this->display();
    }


    public function editcountrypost()
    {
        $Model = M('country');
        $data = $Model->create();
        if ($Model->where("country_id=" . $_POST["id"])->save($data)) {
            $opdata["content"] = "修改了国家信息。编号为:" . $_POST["id"] . "。";
            admin_log($opdata["content"]);
            echo "<div id='kk' style='display:none'>1</div>";
            $this->display("City/editcountry");
        } else {
            echo "<div id='kk' style='display:none'>2</div>";
            $this->display("City/editcountry");
        }
    }


    public function addcity()
    {
        $countryModel = M("country");
        $countryinfo = $countryModel->select();
        $this->assign('countrylist', $countryinfo);
        $this->display();
    }


    public function addcitypost()
    {
        $Model = M('city');
        $data = $Model->create();
        $res = $Model->add($data);
        if ($res) {
            admin_log('添加城市，编号：'.$res);
            echo "<div id='kk' style='display:none'>1</div>";
            $this->display("City/addcity");
        } else {
            echo "<div id='kk' style='display:none'>2</div>";
            $this->display("City/addcity");
        }
    }


    public function editcity()
    {


        $countryModel = M("country");
        $countryinfo = $countryModel->select();
        $this->assign('countrylist', $countryinfo);


        $id = $_GET["id"];
        $Model = M("city");
        $field = "city.*,province.country_id";
        $where = "city.city_id=" . $id;
        $join = 'province ON city.province_id = province.province_id';
        $res = $Model->field($field)->where($where)->join($join)->find();
        $this->assign('info', $res);


        $provinceModel = M("province");
        $where1 = "country_id=" . $res["country_id"];
        $provinceinfo = $provinceModel->where($where1)->select();
        $this->assign('provincelist', $provinceinfo);


        $this->display();
    }


    public function editcitypost()
    {
        $Model = M('city');
        $data = $Model->create();
        if ($Model->where("city_id=" . $_POST["id"])->save($data)) {
            $opdata["content"] = "修改了城市信息。编号为:" . $_POST["id"] . "。";
            admin_log($opdata["content"]);
            echo "<div id='kk' style='display:none'>1</div>";
            $this->display("City/editcity");
        } else {
            echo "<div id='kk' style='display:none'>2</div>";
            $this->display("City/editcity");
        }
    }


    //获取国家省市
    public function getprovinceinfo()
    {
        $provinceModel = M("province");
        $id = $_POST["id"];
        $where = "country_id=" . $id;
        $provincelist = $provinceModel->where($where)->select();

        $this->ajaxReturn($provincelist);

    }

    public function indexcounty()
    {
        $Model = M("county");
        $field = "county.*,city.city";
        // $where="city.city_id=".$id;
        $join = 'city ON county.city_id = city.city_id';
        $list = $Model->join($join)->select();
        $this->assign('list', $list);
        $this->display();
    }

    public function addcounty()
    {
        $countryModel = M("country");
        $countryinfo = $countryModel->select();
        $this->assign('countrylist', $countryinfo);
        $this->display();
    }

    public function addcountypost()
    {
        $Model = M('county');
        $data = $Model->create();
        $res = $Model->add($data);
        if ($res) {
            admin_log('添加区县，编号：'.$res);
            echo "<div id='kk' style='display:none'>1</div>";
            $this->display("City/addcity");
        } else {
            echo "<div id='kk' style='display:none'>2</div>";
            $this->display("City/addcity");
        }
    }

    public function editcounty()
    {


        $countryModel = M("country");
        $countryinfo = $countryModel->select();
        $this->assign('countrylist', $countryinfo);

        $id = $_GET["id"];
        $Model = M("county");
        $field = "county.*,province.country_id,province.province_id";
        $where = "county.county_id=" . $id;
        $join = 'city ON county.city_id = city.city_id';
        $join1 = 'province ON city.province_id = province.province_id';
        $res = $Model->field($field)->where($where)->join($join)->join($join1)->find();
        $this->assign('info', $res);

        $provinceModel = M("province");
        $where1 = "country_id=" . $res["country_id"];
        $provinceinfo = $provinceModel->where($where1)->select();
        $this->assign('provincelist', $provinceinfo);

        $cityModel = M("city");
        $where2 = "province_id=" . $res["province_id"];
        $cityinfo = $cityModel->where($where2)->select();
        $this->assign('citylist', $cityinfo);


        $this->display();
    }


    public function editcountypost()
    {
        $Model = M('county');
        $data = $Model->create();
        if ($Model->where("county_id=" . $_POST["id"])->save($data)) {
            $opdata["content"] = "修改了区县信息。编号为:" . $_POST["id"] . "。";
            admin_log($opdata["content"]);
            echo "<div id='kk' style='display:none'>1</div>";
            $this->display("City/editcounty");
        } else {
            echo "<div id='kk' style='display:none'>2</div>";
            $this->display("City/editcounty");
        }
    }

    //获取城市信息列表
    public function getcountyinfo()
    {
        $cityModel = M("city");
        $id = $_POST["id"];
        $where = "province_id=" . $id;
        $citylist = $cityModel->where($where)->select();

        $this->ajaxReturn($citylist);

    }
}