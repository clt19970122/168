<?php

/**
 * 微信模板
 */

namespace Home\Logic;

class WctempLogic {

    const tempUrl = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=";

    #

    private $user;
    private $token;
    private $data;

    #
    private $push;

    /**
     * 系统入口
     * @param type $user
     * @param type $token
     * @param type $data
     */
    public function entrance($user, $data) {
        $this->user = $user;
        $this->data = $data;
        return $this->combDatas();
    }

    /**
     * 组合数据
     */
    private function combDatas() {
        $token = D("Home/Token", "Logic");
        $res = $token->entrance();
        if ($res == 403 || $res == 423) {
            return false;
        }
        $this->token = $res;
        #
        $this->push["touser"] = $this->user["openid"];
        $this->push["url"] = "";
        return $this->judgeTemps();
    }

    /**
     * 判断发送模板
     */
    private function judgeTemps() {
        //账户二维码激活
        if ($this->data["type"] == "tips") {
            // $this->push["template_id"] = "h7fZFOxa4II1lXLJ6nGSlSx-jlqIZc4wd1m7CnOT45E";
            $this->push["template_id"] = "HyH605F1kVpRQugNbCVUUjTzstedBZ0L_kEdzdktN7Y";
            return $this->accTips();
        }
        return true;
    }

    #############消息处理############

    /**
     * 账户激活
     * @return type
     */
    private function accTips() {
        $this->push["data"]["first"] = array("value" => "代理购买返利", "color" => "#2da94c");
        $this->push["data"]["keyword1"] = array("value" => $this->user["nickname"], "color" => "#2da94c");
        $this->push["data"]["keyword2"] = array("value" => $this->data["count"] . "套", "color" => "#2da94c");
        $this->push["data"]["remark"] = array("value" => "感谢您的使用，请及时登录系统查看", "color" => "#2da94c");
        return $this->sentTempMsg();
    }

    ////////////////////////////////////////////////////////////////////////////

    /**
     * 发送模板消息
     */
    private function sentTempMsg() {
        $url = self::tempUrl . $this->token;
        $params = json_encode($this->push);
        $tmp = poCurl($url, $params);
        $res = json_decode($tmp, true);
        #
        if ($res["errcode"] == 0) {
            return true;
        }
        #
        $syslog = M("syslog");
        $syslog->name = "WX_TEMP_MODEL";
        $syslog->send = $params;
        $syslog->back = $tmp;
        $syslog->times = date("Y-m-d H:i:s");
        $syslog->add();
        #
        return false;
    }

}
