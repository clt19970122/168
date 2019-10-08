<?php

/**
 * 快递插件
 */

namespace Home\Logic;

class ExpresLogic {

    const URL = "http://www.kuaidi100.com/query?type=";
//    const URL = "https://www.kuaidi100.com/chaxun?com=";

    private $com;
    private $isn;

    public function getorder($name, $order) {
        $this->print_isn($order);
//        $this->print_kdi($name);
        $this->com=$name;

        return $this->final_out();
    }

    private function print_isn($str) {
        $this->isn = $str;
    }

    /*private function print_kdi($str) {
        switch ($str) {
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
            case '百世汇通': $this->com = "baishiwuliu";
                break;
            case '天天快递': $this->com = "tiantian";
                break;
            case '包裹_平邮_挂号信': $this->com = "youzhengguonei";
                break;
            case '德邦': $this->com = "debangwuliu";
                break;
            case 'EMS': $this->com = "ems";
                break;
            case '国通快递': $this->com = "ems";
                break;
        }
    }*/

    private function create_url() {
        return self::URL . $this->com . "&postid=" . $this->isn.'&temp='. lcg_value() ;
    }

    private function curl_data() {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->create_url());
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

    private function data_array() {
        $first = $this->curl_data();
        $data = array();
        if ($first['status'] == "200") {
            return $first['data'];
        } else {
            $data[0]['time'] = date("Y-m-d H:i:s");
            $data[0]['context'] = $first['message'];
            return $data;
        }
    }

    private function final_out() {
        $array = $this->data_array();

        $result = array();
        for ($i = 0; $i < count($array); $i++) {
            $temp = (array) $array[$i];
            $result[$i]['acceptTime'] = $temp['time'];
            $result[$i]['remark'] = $temp['context'];
        }
        return $result;
    }

}
