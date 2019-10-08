<?php

namespace Home\Logic;

class TicketLogic {

    const tokenurl = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=";

    //
    private $appid;
    private $appsecret;

    /**
     * 初始化参数
     */
    public function __construct() {
        $sysconfig = C("WEC_CONFIG");
        $this->appid = $sysconfig["APPID"];
        $this->appsecret = $sysconfig["APPSECRET"];
    }

    /**
     * 开放入口
     */
    public function entrance() {
        $wcticket = M("wcticket");

        $where["times"] = array("gt", time() - 6400);
        $info = $wcticket->where($where)->order("id desc")->find();
        if ($info != null) {
            return $this->judgeOldTicket($info);
        }
        return $this->getNewTicket();
    }

    /**
     * 判断旧Token是否有效
     * @param type $token
     * @return type
     */
    private function judgeOldTicket($token) {
        if ($token["times"] > (time() - $token["expires_in"])) {
            return $token["ticket"];
        }
        return $this->getNewTicket();
    }

    /**
     * 获取信息Token
     * @return int
     */
    private function getNewTicket() {
        $token = D("Home/Token", "Logic")->entrance();
        $url = self::tokenurl . $token . "&type=jsapi";
        $tmp = poCurl($url, null);
        $res = json_decode($tmp, true);
        if ($res["ticket"] != null) {
            return $this->logToken($res);
        }
        return 403;
    }

    /**
     * 记录Token
     * @param array $data
     * @return int
     */
    private function logToken($data) {
        $data["times"] = time();
        $wcticket = M("wcticket");
        if ($wcticket->add($data)) {
            return $data["ticket"];
        }
        return 423;
    }

}
