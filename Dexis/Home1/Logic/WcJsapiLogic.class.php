<?php

/**
 * 微信支付返回结果
 */

namespace Home\Logic;

class WcJsapiLogic {

    const tocketUrl = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=";

    private $appid;
    //
    private $url;

    public function __construct() {
        $sysconfig = C("WEC_CONFIG");
        $this->appid = $sysconfig["APPID"];
    }

    /**
     * 接口入口
     */
    public function entrance($url) {
        $this->url = $url;
        $ticket = $this->getTicket();
        if (!$ticket) {
            return false;
        }
        return $this->encodeTicket($ticket);
    }

    /**
     * 签名
     */
    private function encodeTicket($ticket) {
        $sig["noncestr"] = substr(md5(mt_rand(0, 999)), 0, 16);
        $sig["jsapi_ticket"] = $ticket;
        $sig["timestamp"] = time();
        $sig["url"] = $this->url;
        $sig["signature"] = $this->DataToSign($sig);
        $sig["appid"] = $this->appid;
        return $sig;
    }

    #############票据功能##############

    /**
     * 获取功能票据
     * @return type
     */
    private function getTicket() {
        $wcjsticket = M("wcjsticket");

        $where["times"] = array("gt", time() - 6400);
        $info = $wcjsticket->where($where)->find();
        if ($info == null) {
            return $this->getNewTicket();
        }
        if ($info["times"] > (time() - $info["expires_in"])) {
            return $info["ticket"];
        }
        return $this->getNewTicket();
    }

    /**
     * 获取新Token
     * @return boolean
     */
    private function getNewTicket() {
        $token = D("Home/Token", "Logic")->entrance();
        if ($token == 403 || $token == 423) {
            return false;
        }
        $url = self::tocketUrl . $token . "&type=jsapi";
        $tmp = poCurl($url, null);
        $res = json_decode($tmp, true);
        if ($res["ticket"] != null) {
            return $this->logTicket($res);
        }
        return false;
    }

    /**
     * 记录ticket
     * @param array $data
     * @return int
     */
    private function logTicket($data) {
        $data["times"] = time();
        $wcjsticket = M("wcjsticket");
        if ($wcjsticket->add($data)) {
            return $data["ticket"];
        }
        return false;
    }

    ############公有方法#################

    /**
     * 生成签名戳
     * @param type $param
     * @return type
     */
    private function DataToSign($param) {
        ksort($param);
        foreach ($param as $key => $value) {
            $tmp.=$key . "=" . $value . "&";
        }
        $str = trim($tmp, "&");
        return sha1($str);
    }

}
