<?php

/**
 * 微信支付返回结果
 */

namespace Home\Logic;

class WcResLogic {

    //
    private $config;
    //
    private $data;
    //
    private $order;

    public function __construct() {
        $sysconfig = C("WEC_CONFIG");
        $this->config = $sysconfig["PAYKEY"];
    }

    /**
     * 接口入口
     */
    public function entrance($data) {
        return $this->deCodeXml($data);
    }

    /**
     * 解析XML记录日志
     */
    private function deCodeXml($data) {
        $tmp = xmlToArray($data);
        if (!$tmp) {
            return false;
        }
        $log = M("wcpaylog");
        #$where["out_trade_no"] = $tmp["out_trade_no"];
        #$count = $log->where($where)->count();
        #if ($count <= 0) {
        $tmp["json_code"] = json_encode($tmp);
        $tmp["times"] = time();
        $log->add($tmp);
        #}
        unset($tmp["json_code"]);
        unset($tmp["times"]);
        $this->data = $tmp;
        return $this->verifySign();
    }

    /**
     * 验证参数
     * @return boolean
     */
    private function verifySign() {
        $mySign = $this->DataToSign($this->data);
        if ($mySign != $this->data["sign"]) {
            return false;
        }
        return $this->verifyOrds();
    }

    /**
     * 订单验证
     * @return boolean
     */
    private function verifyOrds() {
        $syspaysn = M("syspaysn");
        #
        $where["sn"] = $this->data["out_trade_no"];
        //$where["money"] = $this->data["total_fee"] / 100;
        $where["status"] = 0;
        $info = $syspaysn->where($where)->find();
        if ($info == null) {
            return false;
        }
        $this->order = $info;
        #
        return $this->veridyStaty();
    }

    /**
     * 订单状态验证
     */
    private function veridyStaty() {
        if ($this->data["result_code"] != "SUCCESS") {
            return $this->wechatRollback();
        }
        $syspaysn = M("syspaysn");
        #
        $where["sn"] = $this->data["out_trade_no"];
        //$where["money"] = $this->data["total_fee"] / 100;
        $where["status"] = 0;
        #
        $save["status"] = 1;
        if (!$syspaysn->where($where)->save($save)) {
            return false;
        }
        //更新提货支付状态
        $sn_res =$syspaysn->where(["sn"=>$where["sn"]])->find();
        $dre_res =M('order_drs')->where(['sn'=>$sn_res['resn']])->find();
        $ord_res =M('orders')->where(['sn'=>$sn_res['resn']])->find();
        if($ord_res &&$ord_res['gid']==14){
            M('orders')->where(["sn"=>$sn_res["resn"]])->save(['status'=>1,'paytime'=>time()]);
            return 'buycup';
        }
        if($dre_res){
            $drs_save['have_pay'] =1;
//            M('order_drs')->where(['sn'=>$sn_res['resn']])->save($drs_save);

            if(!M('order_drs')->where(['sn'=>$sn_res['resn']])->save(['status'=>1,'have_pay'=>1])) {
                return false;
            }else{
                M('account')->where(['id'=>$dre_res['uid']])->setDec('stock',$dre_res['nums']);

                /*$user_info =M('account')->where(['id'=>$dre_res['uid']])->find();
                ###添加记录----2018-8-15 add
                $add_nums=[
                    'uid'=>$dre_res['uid'],
                    'aboutid'=>0,
                    'sn'=>$sn_res['resn'],
                    'nums'=>$dre_res['nums'],
                    'after'=>0,
                    'uafter'=>$user_info["stock"],
                    'time'=>time(),
                    'type'=>21 //库存转让
                ];
                addAcc_nums($add_nums);*/
                return 'success';
            }
        }else{
            return true;
        }
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
            if ($key != "sign" && $key != "times" && $value != "" && !is_array($value)) {
                $tmp .= $key . "=" . $value . "&";
            }
        }
        $key = $tmp . "key=" . $this->config;
        return strtoupper(md5($key));
    }

    /**
     * 微信返回
     */
    private function wechatRollback() {
        $string = "<xml>";
        $string .= "<return_code><![CDATA[SUCCESS]]></return_code>";
        $string .= "<return_msg><![CDATA[OK]]></return_msg>";
        $string .= "</xml>";
        return $string;
    }

}
