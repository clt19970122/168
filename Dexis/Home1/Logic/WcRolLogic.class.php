<?php

/**
 * 微信退款
 */

namespace Home\Logic;

class WcRolLogic {

    const rollUrls = "https://api.mch.weixin.qq.com/secapi/pay/refund";

    private $config;
    private $mode;
    //
    private $data;
    private $put;

    public function __construct() {
        $sysconfig = C("WEC_CONFIG");
        $this->config = $sysconfig;
        $this->mode = C("PAY_MODE");
    }

    /**
     * 接口入口
     */
    public function entrance($data) {
        $this->data = $data;
        return $this->coverDatas();
    }

    /**
     * 创建数据
     */
    public function coverDatas() {
        $this->put["appid"] = $this->config["APPID"];
        $this->put["mch_id"] = $this->config["MCHID"];
        $this->put["device_info"] = "web";
        $this->put["nonce_str"] = md5(time());
        $this->put["transaction_id"] = $this->data["transaction_id"];
        $this->put["out_refund_no"] = $this->data["out_trade_no"];
        $this->put["total_fee"] = $this->data["total_fee"];
        $this->put["refund_fee"] = intval($this->data["refund_fee"] * 100);
        //$this->put["refund_fee"] = 1;
        $this->put["op_user_id"] = $this->config["MCHID"];
        return $this->signToData();
    }

    /**
     * 生成SIGN
     */
    private function signToData() {
        $this->put["sign"] = $this->DataToSign($this->put);
        $xml = "<xml>" . data_to_xml($this->put, "xml") . "</xml>";
        return $this->payrollToBills($xml);
    }

    /**
     * 下单
     * @param type $params
     */
    private function payrollToBills($params) {
        $res = wcPoCurl(self::rollUrls, $params);
        $tmp = xmlToArray($res);
        if ($tmp["return_code"] != "SUCCESS") {
            #
            $syslog = M("syslog");
            $syslog->name = "PAYROLL";
            $syslog->send = $params;
            $syslog->back = $res;
            $syslog->times = date("Y-m-d H:i:s");
            $syslog->add();
            #
            return false;
        }
        return true;
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
                $tmp .= $key . "=" . $value . "&";
            }
        }
        $paykey = $this->mode == 1 ? $this->config["PAYKEY"] : $this->config["SPAYKEY"];
        $key = $tmp . "key=" . $paykey;
        return strtoupper(md5($key));
    }

}
