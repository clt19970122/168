<?php

/**
 * 微信企业支付
 */

namespace Home\Logic;

class WcDraLogic {

    //统一下单接口
    const coomUrl = "https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers";

    //
    private $param;
    //
    private $config;
    //
    private $data;

    public function __construct() {
        $sysconfig = C("WEC_CONFIG");
        $this->config = $sysconfig;
    }

    /**
     * 接口入口
     */
    public function entrance($data) {
        $this->param = $data;
        return $this->coverDatas();
    }

    /**
     * 创建数据
     */
    public function coverDatas() {
        $this->data["mch_appid"] = $this->config["APPID"];
        $this->data["mchid"] = $this->config["MCHID"];
        $this->data["nonce_str"] = md5(time());
        $this->data["partner_trade_no"] = $this->param["sn"];
        $this->data["openid"] = $this->param["openid"];
        $this->data["check_name"] = "NO_CHECK";
        $this->data["amount"] = intval($this->param["money"]) * 100;
        $this->data["desc"] = "账户管理";
        $this->data["spbill_create_ip"] = $_SERVER["REMOTE_ADDR"];
        return $this->signToData();
    }

    /**
     * 生成SIGN
     */
    private function signToData() {
        $this->data["sign"] = $this->DataToSign($this->data);
        $xml = "<xml>" . data_to_xml($this->data, "xml") . "</xml>";
        return $this->payToBills($xml);
    }

    /**
     * 支付
     * @param type $params
     */
    private function payToBills($params) {
        $res = wcPoCurl(self::coomUrl, $params);
        $tmp = xmlToArray($res);
        if ($tmp["return_code"] != "SUCCESS" || $tmp["result_code"] != "SUCCESS") {
            #
            $syslog = M("syslog");
            $syslog->name = "WXPAY";
            $syslog->send = $params;
            $syslog->back = $res;
            $syslog->times = date("Y-m-d H:i:s");
            $syslog->add();
            #
            return false;
        }
        return $tmp;
    }

    ##################公有方法######################

    /**
     * 生成签名戳
     * @param type $param
     * @return type
     */
    private function DataToSign($param) {
        ksort($param);
        foreach ($param as $key => $value) {
            if ($key != "sign" && $value != null) {
                $tmp.=$key . "=" . $value . "&";
            }
        }
        $key = $tmp . "key=" . $this->config["PAYKEY"];
        return strtoupper(md5($key));
    }

}
