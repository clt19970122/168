<?php

/**
 * 微信红包支付
 */

namespace Home\Logic;

class WcRangeLogic {

    //红包接口
    const coomUrl = "https://api.mch.weixin.qq.com/mmpaymkttransfers/sendredpack";

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
        $this->data["nonce_str"] = md5(time());
        $this->data["mch_billno"] = time();
        //$this->data["mch_id"] = "1415695202";
        $this->data["mch_id"] = $this->config["MCHID"];
        //$this->data["sub_mch_id"] = $this->config["MCHID"];
        //$this->data["wxappid"] = "wx9dd3ee63329a5ac9";
        $this->data["wxappid"] = $this->config["APPID"];
        //$this->data["msgappid"] = $this->config["APPID"];
        #
        $this->data["send_name"] = "乐享云商";
        $this->data["re_openid"] = $this->param["openid"];
        $this->data["total_amount"] = intval($this->param["money"]) * 100;
        $this->data["total_num"] = 1;
        $this->data["wishing"] = "感谢您使用乐享云商";
        $this->data["client_ip"] = "127.0.0.1";
        $this->data["act_name"] = "乐享云商周年回馈活动";
        $this->data["remark"] = "愿与您一起成长!";
        $this->data["scene_id"] = "PRODUCT_1";
        //$this->data["consume_mch_id"] = $this->config["MCHID"];
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
        #
        $acc_draw = M("acc_draw");
        $where["id"] = $this->param["rid"];
        $save["sn"] = $tmp["mch_billno"];
        $save["wcsn"] = $tmp["send_listid"];
        return $acc_draw->where($where)->save($save);
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
