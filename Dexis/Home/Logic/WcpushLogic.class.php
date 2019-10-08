<?php

/**
 * 微信群发
 * type  消息类型
 * context 消息内容(当mediaid为空时此项不能为空)
 * mediaid 资源ID(当context为空时此项不能为空)
 */

namespace Home\Logic;

class WcpushLogic {

    const sentUrl = "https://api.weixin.qq.com/cgi-bin/message/mass/sendall?access_token=";

    //
    private $data;

    /**
     * 系统入口
     * @param type $data
     */
    public function entrance($data) {
        $this->data = $data;
        return $this->getMsgType();
    }

    /**
     * 判断消息类型
     * @return type
     */
    private function getMsgType() {
        if ($this->data["type"] == 1) {
            return $this->msgText();
        }
        return $this->msgImgs();
    }

    /**
     * 文字消息
     */
    private function msgText() {
        $data["filter"]["is_to_all"] = true;
        $data["filter"]["group_id"] = "";
        $data["text"]["content"] = $this->data["context"];
        $data["msgtype"] = "text";
        return $this->doSentMsg($data);
    }

    /**
     * 图消息
     */
    private function msgImgs() {
        $data["filter"]["is_to_all"] = true;
        $data["filter"]["group_id"] = "";
        $data["mpnews"]["media_id"] = $this->data["mediaid"];
        $data["msgtype"] = "mpnews";
        return $this->doSentMsg($data);
    }

    /**
     * 发送消息
     */
    private function doSentMsg($json) {
        $token = D("Home/Token", "Logic")->entrance();
        $url = self::sentUrl . $token;
        #
        $data = json_encode($json, JSON_UNESCAPED_UNICODE);
        #
        $tmp = poCurl($url, $data);
        $log = M("syslog");
        $put = array(
            "name" => "PUSHMSG", "send" => $data,
            "back" => $tmp, "times" => date("Y-m-d H:i:s")
        );
        $log->add($put);
        #
        $res = json_decode($tmp, true);

        if ($res["errcode"] == 0) {
            return 200;
        }
        return 503;
    }

}
