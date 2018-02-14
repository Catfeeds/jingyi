<?php

namespace Home\Controller;

use Think\Controller;

class IReportController extends CommonController
{


    /**
     *  添加举报
     * userid  举报人id
     * beuserid 被举报
     * postsid  贴子id
     * type    类型  1|个人贴2|星球贴3|群活动
     */
    public function addreport()
    {
        $data["report_userid"] = I("userid");
        $data["report_beuserid"] = I("beuserid");
        $data["report_postsid"] = I("postsid");
        $data["report_type"] = I("type");
        $data["report_msg"] = shielding(I("msg"));
        $data["addtime"] = time();
        if (empty($data["report_type"])) {
            $msg = "非法操作！";
            get_api_result(300, $msg);
        }
        if (empty($data["report_userid"])) {
            $msg = "非法操作！";
            get_api_result(300, $msg);
        }
        if (empty($data["report_beuserid"])) {
            $msg = "非法操作！";
            get_api_result(300, $msg);
        }
        if (empty($data["report_postsid"])) {
            $msg = "非法操作！";
            get_api_result(300, $msg);
        }

        $ReportModel = D("Report");

        $ischeck = $ReportModel->checkreport($data["report_postsid"], $data["report_userid"], $data["report_type"]);
        if ($ischeck) {
            $msg = "已经举报过，请等待处理！";
            get_api_result(300, $msg);
        }
        $res = $ReportModel->addpost($data);

        $msg = "举报成功！";
        get_api_result(200, $msg, $res);

    }


}