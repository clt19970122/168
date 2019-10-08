<?php

/**
 * 微信消息解析
 */

namespace Home\Logic;

class WcMsgLogic {

    //
    private $token;
    //
    private $data;

    /**
     * 初始化参数
     */
    public function __construct() {
        $sysconfig = C("WEC_CONFIG");
        $this->token = $sysconfig["TOKEN"];
    }

    /**
     * 校验方法
     */
    public function valid($data) {
        $this->data = $data;
        if (!$this->checkSignature()) {
            return false;
        }
        return $data["echostr"];
    }

    /**
     * 响应方法
     */
    public function respond() {
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        if (empty($postStr)) {
            return false;
        }
        $tmp = xmlToArray($postStr);
        return $tmp;
    }

    ##################公有方法######################

    /**
     * 校验参数
     * @return boolean
     */
    private function checkSignature() {
        $signature = $this->data["signature"];
        $timestamp = $this->data["timestamp"];
        $nonce = $this->data["nonce"];
        $token = $this->token;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        if ($tmpStr == $signature) {
            return true;
        }
        return false;
    }

}
