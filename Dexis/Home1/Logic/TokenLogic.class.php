<?php

namespace Home\Logic;

class TokenLogic {

    const tokenurl = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&";

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
        $wctoken = M("wctoken");

        $where["times"] = array("gt", time() - 6400);
        $info = $wctoken->where($where)->order("id desc")->find();
        if ($info != null) {
            return $this->judgeOldToken($info);
        }
        return $this->getNewToken();
    }

    /**
     * 判断旧Token是否有效
     * @param type $token
     * @return type
     */
    private function judgeOldToken($token) {
        if ($token["times"] > (time() - $token["expires_in"])) {
            return $token["access_token"];
        }
        return $this->getNewToken();
    }

    /**
     * 获取信息Token
     * @return int
     */
    private function getNewToken() {
        $url = self::tokenurl . "appid=" . $this->appid . "&secret=" . $this->appsecret;
        $tmp = poCurl($url, null);
        $res = json_decode($tmp, true);
        if ($res["access_token"] != null) {
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
        $wctoken = M("wctoken");
        if ($wctoken->add($data)) {
            return $data["access_token"];
        }
        return 423;
    }

}
