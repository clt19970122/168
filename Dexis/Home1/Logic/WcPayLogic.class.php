<?php

/**
 * 微信支付
 */

namespace Home\Logic;

class WcPayLogic {

    //统一下单接口
    const coomUrl = "https://api.mch.weixin.qq.com/pay/unifiedorder";

    //
    private $param;
    //
    private $config;
    private $mode;
    //
    private $data;

    public function __construct() {
        $sysconfig = C("WEC_CONFIG");
        $this->config = $sysconfig;
        $this->mode = C("PAY_MODE");
    }

    /**
     * 接口入口
     */
    public function entrance($data) {
        return $this->checkPaySn($data);
    }

    /**
     * 创建唯一订单
     * @param type $data
     * @return boolean
     */
    private function checkPaySn($data) {
        $syspaysn = M("syspaysn");
        #
        $where["resn"] = $data["sn"];
        $info = $syspaysn->where($where)->find();
        if ($info != null) {
            return $this->checkPaySnExsit($info, $data);
        }
        #
        $where["sn"] = mt_rand(1, 9) . getOrdSn();
        $where["models"] = $data["alt"];
        $where["body"] = "微信支付";
        $where["money"] = $data["money"];
        $where["status"] = 0;
        $where["times"] = time();
        if (!$syspaysn->add($where)) {
            return false;
        }
        #
        $this->param = $where;
        $this->param["openid"] = $data["openid"];
        return $this->coverDatas();
    }

    /**
     * 订单已存在
     * @param array $info
     * @param type $data
     * @return boolean
     */
    private function checkPaySnExsit($info, $data) {
        $syspaysn = M("syspaysn");
        #
        $where["resn"] = $data["sn"];
        $save["sn"] = mt_rand(1, 9) . getOrdSn();
        if (!$syspaysn->where($where)->save($save)) {
            return false;
        }
        $info["sn"] = $save["sn"];
        $this->param = $info;
        $this->param["openid"] = $data["openid"];
        return $this->coverDatas();
    }

    ////////////////////////////////////////////////////////////////////////////

    /**
     * 创建支付数据
     */
    public function coverDatas() {
        $this->data["appid"] = $this->config["APPID"];
        $this->data["mch_id"] = $this->config["MCHID"];
        $this->data["device_info"] = "web";
        $this->data["nonce_str"] = md5(time());
        $this->data["body"] = $this->param["sn"];
        $this->data["attach"] = trimall($this->param["body"]);
        $this->data["out_trade_no"] = $this->param["sn"];
        $tmp = $this->param["money"] * 100;
        $this->data["total_fee"] = intval($tmp);
        //$this->data["total_fee"] = '1';
        $this->data["spbill_create_ip"] = "127.0.0.1";
        $this->data["notify_url"] = $this->config["notifyUrl"];
        $this->data["trade_type"] = "JSAPI";
        $this->data["openid"] = $this->param["openid"];
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
     * 下单
     * @param type $params
     */
    private function payToBills($params) {
        $res = poCurl(self::coomUrl, $params);
        $tmp = xmlToArray($res);
        if ($tmp["return_code"] != "SUCCESS") {
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
        $newPut["appId"] = $this->config["APPID"];
        $time = time();
        $newPut["timeStamp"] = "$time";
        $newPut["nonceStr"] = $tmp["nonce_str"];
        $newPut["package"] = "prepay_id=" . $tmp["prepay_id"];
        $newPut["signType"] = "MD5";
        $newPut["paySign"] = $this->DataToSign($newPut);
        return $newPut;
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
        $key = $tmp . "key=" . $this->config["PAYKEY"];
        return strtoupper(md5($key));
    }

}
