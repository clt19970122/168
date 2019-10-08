<?php

namespace Home\Controller;

use Think\Controller;

class CommController extends Controller {

    public $ssid;
    public $wecs;
    public $user;

    public function _initialize() {
        session("ssid_history", get_url());
        $ssid = session("ssid_dexis");
        //session("ssid_dexis_wechat", array("openid" => "orMnrwBDN8x6N_fQYvaBPUU4TTWE"));
        $wechat = session("ssid_dexis_wechat");
        if ($wechat == null) {
            $this->redirect("Wcix/getInfos");
        }
        
        set_time_limit(100);
        #
        $this->wecs = $wechat;
        $this->ssid = $ssid;
        $this->getUser();
        $this->assign("version", time());
        $this->assign("tabbar", CONTROLLER_NAME);
        #
        $title = C(CONTROLLER_NAME . "/" . ACTION_NAME);
        $this->assign("conf_title", $title == null ? "我的商城" : $title);
    }

    /**
     * 获取用户信息
     */
    public function getUser() {
        $account = M("account");
        #
        $where["id"] = $this->ssid;
        $info = $account->where($where)->find();
        #
        if ($info["headimgurl"] == null) {
            $info["headimgurl"] = __ROOT__ . "/Public/home/imgs/head.png";
        } else {
            $loc = strpos($info["headimgurl"], "ttp:");
            if (!$loc) {
                $info["headimgurl"] = __ROOT__ . "/Public/uploads/head/" . $info["headimgurl"];
            }
        }
        $this->user = $info;
        $this->assign("conf_user", $info);
    }

}
