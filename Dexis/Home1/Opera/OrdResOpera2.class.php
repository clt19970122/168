<?php

namespace Home\Opera;

/**
 * 订单处理
 */
class OrdResOpera {

    private $fees;
    //
    private $sn;
    private $data;
    private $user;

    public function __construct() {
        $sysconfig = M("sysconfig");
        #
        $where["models"] = "CONF_VISITER";
        $info = $sysconfig->where($where)->find();
        $this->fees = $info["value"];
    }

    ////////////////////////////////////////////////////////////////////////////

    /**
     * 入口
     * @param type $sn
     */
    public function runs($sn) {
        $this->sn = $sn;
        return $this->getOrder();
    }

    /**
     * 获取订单
     * @return type
     */
    private function getOrder() {
        $syspaysn = M("syspaysn");
        $orders = M("orders");
        #
        $where["sn"] = $this->sn;
        $paysn = $syspaysn->where($where)->find();
        if ($paysn == null) {
            return $this->wechatRollback("FAIL", "PAYSN_NULL");
        }
        $where["sn"] = $paysn["resn"];
        $where["status"] = 0;
        $info = $orders->where($where)->find();
        if ($info == null) {
            return $this->wechatRollback("FAIL", "ORDER_NULL");
        }
        $this->data = $info;
        return $this->updateOrder();
    }

    /**
     * 更新订单
     * @return type
     */
    private function updateOrder() {
        $orders = M("orders");
        #
        $where["sn"] = $this->data["sn"];
        $where["status"] = 0;
        $save["status"] = 1;
        $save["paytime"] = time();
        if (!$orders->where($where)->save($save)) {
            return $this->wechatRollback("FAIL", "ORDER_UPFAIL");
        }
        #
        if (!cgUserPoint($this->data["uid"], $this->data["gnums"], $where["sn"])) {
            return $this->wechatRollback("FAIL", "USER_UPFAIL");
        }
        //更新库存
        $account = M("account");
        $cond["id"] = $this->data["uid"];
        $account->stock = array("exp", "stock+" . $this->data["gnums"]);
        //$update["stock"] = array("exp", "stock+" . $this->data["gnums"]);
        if (!$account->where($cond)->save()) {
            return $this->wechatRollback("FAIL", "USER_UPFAIL[02]");
        }
        if ($this->data['gid'] == 5) {  //订单信息，购买的上帝之泪
            cgUserLevel($cond["id"], $this->data["gnums"]);
        }

        #
        return $this->getUserInfo();
    }

    ////////////////////////////////////////////////////////////////////////////

    /**
     * 获取用户信息
     */
    private function getUserInfo() {
        $account = M("account");
        #
        $where["id"] = $this->data["uid"];
        $info = $account->where($where)->find();
        //\Think\Log::record(json_encode($info));
        #
        if ($info == null) {
            return $this->wechatRollback("FAIL", "USER_NOT_FOUND");
        }
        if ($info["recid"] == null || $info["recid"] == 0) {
            return $this->wechatRollback("SUCCESS", "OK");
        }
        $this->user = $info;
        return $this->doShare();
    }

    /**
     * 处理分成
     */
    private function doShare() {
        $account = M("account");
        #
        $cond["sysid"] = $this->user["recid"];
        for ($i = 0; $i < 3; $i++) {
            $tmp = $account->where($cond)->find();
            if ($tmp == null) {
                return $this->wechatRollback("SUCCESS", "OK");
            }
            if (!$this->doShareFinish($tmp, $i)) {
                return $this->wechatRollback("FAIL", "RE_FAIL");
            }
            $cond["sysid"] = $tmp["recid"];
        }
        return $this->wechatRollback("SUCCESS", "OK");
    }

    /**
     * 完成分成
     * @param type $tmp
     * @return boolean
     */
    private function doShareFinish($tmp, $level) {
        #积分返点
        if (!cgUserPoint($tmp["id"], $this->data["gnums"], $this->data["sn"])) {
            return false;
        }
        #现金返点
        $roll = $this->getLevMon($tmp, $level) * $this->data["gnums"];
        if ($roll > 0) {
            if (!cgUserMoney($tmp["id"], $roll, 1, "REBACK", $this->data["sn"])) {
                return false;
            }
        }
        #业绩计算
        $account = M("account");
        #
        $where["id"] = $tmp["id"];
        $info = $account->where($where)->find();
        if ($this->user["level"] > 0) {
            $save["groups"] = $info["groups"] + $roll;
        } else {
            $save["person"] = $info["person"] + $roll;
        }
        $save["uptimes"] = time();
        //
        $level == 0 ? $this->doCeilStock($info["sysid"]) : null;
        return $account->where($where)->save($save);
    }

    //------------------------------------------------------------------------//

    /**
     * 获取升级金额
     */
    private function getLevMon($tmp, $v) {
        $acc_level = M("acc_level");
        #等级查询
        $leSel["id"] = $tmp["level"];
        $lev = $acc_level->where($leSel)->find();
        if ($lev == null) {
            $share = array($this->fees, $this->fees, $this->fees);
        } else {
            $share = array($lev["first"], $lev["second"], $lev["third"]);
        }
        #
        if ($tmp["level"] <= $this->user["level"]) {
            return $share[$v];
        }
        return $this->getCeil($tmp["level"], $v);
    }

    /**
     * 获取购物等级差价
     */
    private function getCeil($v, $loc) {
        $goods_level = M("goods_level");
        #
        $goods = getIDGoodsInfo($this->data["gid"]);
        $where["gsn"] = $goods["gsn"];
        $where["lid"] = $this->user["level"];
        $user = $goods_level->where($where)->find();
        if ($user == null) {
            $user["price"] = $goods["price"];
        }
        #
        $where["lid"] = $v;
        $parent = $goods_level->where($where)->find();
        if ($parent == null) {
            return 0;
        }
        #
        $ceil = $user["price"] - $parent["price"];
        if ($loc > 0 && $v > 0) {
            $ceil = $ceil - $this->fees;
        }
        return abs($ceil);
    }

    /**
     * 扣减B端库存
     */
    private function doCeilStock($id) {
        $account = M("account");
        $wctemp = D("Home/Wctemp", "Logic");
        #
        $where["sysid"] = $id;
        for ($i = 0; $i < 3; $i++) {
            $info = $account->where($where)->find();
            if ($info["level"] <= 0) {
                $where["sysid"] = $info["recid"];
                continue;
            }
            $save["stock"] = $info["stock"] - $this->data["gnums"];
            if (!$account->where($where)->save($save)) {
                return false;
            }
            if ($save["stock"] < 0) {
                $user = ["openid" => $info["openid"], "nickname" => $info["nickname"]];
                $data = ["type" => "tips", "count" => $save["stock"]];
                $wctemp->entrance($user, $data);
            }
            $where["sysid"] = $info["recid"];
        }
        return true;
    }

    ////////////////////////////////////////////////////////////////////////////

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
