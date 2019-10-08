<?php

namespace Home\Controller;

use Think\Controller;

class WcixController extends Controller {

    public function _initialize() {
        $this->getTicket();
        $this->assign("version", time());
    }

    /**
     * 获取ticket-sign
     */
    private function getTicket() {
        $tocket = D("Home/Ticket", "Logic");
        #
        $data["jsapi_ticket"] = $tocket->entrance();
        $data["noncestr"] = md5(mt_rand(0, 999));
        $data["timestamp"] = time();
        $data["url"] = get_url();
        ksort($data);
        $string = "";
        foreach ($data as $k => $v) {
            $string .= $k . "=" . $v . "&";
        }
        $string = trim($string, "&");
        $data["signature"] = sha1($string);
        $data["appid"] = C("WEC_CONFIG")["APPID"];
        #
        $this->assign("ticket", $data);
    }

    //************************************************************************//

    /**
     * 分享页面
     * @param type $sysid
     */
    public function share($sysid) {
        layout("laynot");
        $account = M("account");
        #
        $where["sysid"] = $sysid;
        $info = $account->where($where)->find();
        #
        $this->assign("id", $sysid);
        $this->assign("name", $info["nickname"]);
        $this->assign("weburl", C("WEBURL"));
        $this->display();
    }

    /**
     * 用户推荐码
     */
    public function share_qr($sysid) {
        vendor('phpqrcode.phpqrcode');
        $links = C("WEBURL") . "Home/Login/index/recid/" . $sysid;
        \QRcode::png($links, null, "Mssid_dexis_wechat", 10, 1);
    }

    //************************************************************************//

    /**
     * 获取用户个人信息
     */
    public function getInfos() {
        $wcinf = D("Home/WcInf", "Logic");
        #
        $res = $wcinf->entrance();
        if ($res["nickname"] == null) {
            $this->redirect("Wcix/getInfos");
        }
        session("ssid_dexis_wechat", $res);
        $this->redirect(session("ssid_history"));
    }

    /**
     * 支付返回
     */
    public function respay() {
        set_time_limit(0);
        $postStr = file_get_contents("php://input");
        #
        $wcres = D("WcRes", "Logic");
        $ordres = D("Home/OrdRes", "Opera");
        $res = $wcres->entrance($postStr);
        if (!$res) {
            return false;
        }

        if($res ==='buycup'){

            echo  $this->wechatRollback("SUCCESS", "OK");
           die;
        }
        if($res ==='success'){
            
            echo  $this->wechatRollback("SUCCESS", "OK");die;
        }else {
            $tmp = xmlToArray($postStr);
            $do = $ordres->runs($tmp["out_trade_no"]);
            //活动的方案------------------------------2019-3-8 20:00:43
            $paysn = M('syspaysn')->where(['sn'=>$tmp["out_trade_no"]])->find();
            GoldActivity($paysn["resn"]);
            //end-------------------------------------
            echo $do;
        }
    }

    ////////////////////////////////////////////////////////////////////////////

    /**
     * 分期数据返回
     */
    public function stages() {
        $post = file_get_contents("php://input");
        $ordsrs = D("Home/OrdSrs", "Opera");
        #
        $res = $ordsrs->runs($post);
        //
        $syslog = M("syslog");
        $syslog->name = "FQLOG";
        $syslog->send = $post;
        $syslog->back = $res["status"] . "-" . $res["msg"];
        $syslog->times = date("Y-m-d H:i:s");
        $syslog->add();
        //
        return get_op_put($res["status"], $res["msg"]);
    }

    /**
     * 分期数据返回-测试
     */
    public function stages_op() {
        $temp["phone"] = "13666200691";
        $temp["orderNo"] = "915332825491752712";//订单号
        $temp["times"] = time();
        $temp["status"] = 5;
        $temp["certNo"] = "715270469154287144";//
        $temp["nonceStr"] = "5225450250737460";
        $temp["sign"] = $this->signVerify($temp);
        foreach ($temp as $key => $value) {
            $tmp .= $key . "=" . $value . "&";
        }
        $urlData = trim($tmp, "&");
        #
        $ordsrs = D("Home/OrdSrs", "Opera");
        #
        $res = $ordsrs->runs($urlData);
        dump($res);
    }

    public function signVerify($param) {
        ksort($param);
        $tmp = "";
        foreach ($param as $key => $value) {
            $tmp .= $key . "=" . $value . "&";
        }
        $key = $tmp . "key=964d2026e854f464ae4f7dfb4bd4b488";
        return strtoupper(md5($key));
    }

    //************************************************************************//

    /**
     * 物流扫描
     */
    public function scann_log() {
        $order_trans = M("order_trans");
        $orders = M("orders");
        $fms = D("Home/Fms", "Logic");
        #
        $where["status"] = 0;
        $list = $order_trans->where($where)->select();
        if (count($list) <= 0) {
            return 0;
        }
        #
        set_time_limit(0);
        foreach ($list as $k => $v) {
            $trans = getTransNm($v["transid"]);
            $res = $fms->getorder($trans, $v["vosn"]);
            if ($res["status"] != 200) {
                continue;
            }
            $mode["sn"] = $v["sn"];
            $update["tag"] = $res["data"][0]["context"];
            $orders->where($mode)->save($update);
            #
            if ($res["state"] == 3) {
                $cond["id"] = $v["id"];
                $save["status"] = 1;
                $order_trans->where($cond)->save($save);
                #
                $save["status"] = 3;
                $save["comtime"] = time();
                $orders->where($mode)->save($save);
            }
        }
    }

    //************************************************************************//

    /**
     * 用户等级
     */
    public function userLevel($code) {
        $account = M("account");
        $acc_level = M("acc_level");
        #
        $where["points"] = array("gt", 0);
        $list = $account->where($where)->select();
        if (count($list) <= 0) {
            return get_op_put(0, "ACC_NULL");
        }
        #
        set_time_limit(0);
        foreach ($list as $k => $v) {
            $cond["months"] = array("elt", $v["points"]);
            $info = $acc_level->where($cond)->order("months desc")->find();
            if ($info == null || $info["id"] <= $v["level"]) {
                continue;
            }
            #
            $save["level"] = $info["id"];
            $account->where("id='" . $v["id"] . "'")->save($save);
        }
        #
        $account->where($where)->save(array("points" => 0));
    }

    /**
     * 微信返回
     */
    private function wechatRollback($code, $msg) {
        $string = "<xml>";
        $string .= "<return_code><![CDATA[" . $code . "]]></return_code>";
        $string .= "<return_msg><![CDATA[" . $msg . "]]></return_msg>";
        $string .= "</xml>";
        return $string;
    }
}
