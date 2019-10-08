<?php

/**
 * 快递插件V2
 */

namespace Home\Logic;

class FmsLogic {

    const URL = "http://www.kuaidi100.com/query?type=";

    private $name;
    private $com;
    private $isn;
    private $finalink;

    public function getorder($name, $order) {
        $this->name = $name;
        $this->isn = $order;
        #
        return $this->print_kdi();
    }

    /**
     * 读取物流参数
     * @param type $str
     */
    private function print_kdi() {
        switch ($this->name) {
            case '顺丰物流': $this->com = "shunfeng";
                break;
            case '圆通快递': $this->com = "yuantong";
                break;
            case '中通快递': $this->com = "zhongtong";
                break;
            case '申通快递':$this->com = "shentong";
                break;
            case '韵达快递': $this->com = "yunda";
                break;
            case '百世汇通': $this->com = "huitongkuaidi";
                break;
            case '天天快递': $this->com = "tiantian";
                break;
            case '包裹_平邮_挂号信': $this->com = "youzhengguonei";
                break;
            case 'EMS': $this->com = "ems";
                break;
        }
        return $this->create_url();
    }

    /**
     * 构造链接
     * @return type
     */
    private function create_url() {
        $this->finalink = self::URL . $this->com . "&postid=" . $this->isn . "&id=1";
        return $this->curl_data();
    }

    /**
     * 获取数据
     * @return type
     */
    private function curl_data() {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->finalink);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        if (curl_errno($ch)) {
            $result = curl_errno($ch) . "-" . curl_error($ch);
        }
        curl_close($ch);
        #
        $result = json_decode($output, true);
        return $result;
    }

}
