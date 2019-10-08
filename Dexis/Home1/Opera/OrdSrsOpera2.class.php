<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Home\Opera;

/**
 * Description of OrdSrsOpera 分期支付
 *
 * @author jiklee
 */
class OrdSrsOpera {

    private $fees;
    //
    private $data;
    private $arrs;
    private $orde;
    private $nums;  //库存数量
    //
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
     * @param type $data
     */
    public function runs($data) {
        $this->data = $data;
        return $this->doDataParam();
    }

    /**
     * 组合数据
     */
    private function doDataParam() {
        $ret = explode("&", $this->data);
        foreach ($ret as $k => $v) {
            $info = explode("=", $v);
            $this->arrs[$info[0]] = urldecode($info[1]);
        }
        return $this->signVerify();
    }

    /**
     * 分期数据验证
     * @param type $param
     * @return type
     */
    private function signVerify() {
        $param = $this->arrs;
        ksort($param);
        foreach ($param as $key => $value) {
            if ($key != "sign" && $value != "" && !is_array($value)) {
                $tmp .= $key . "=" . $value . "&";
            }
        }
        $key = $tmp . "key=" . C("PUM_CONF")["REKEY"];
        $sign = strtoupper(md5($key));
        #
        if ($sign != $param["sign"]) {
            return get_op_res(0, "密钥不匹配", null);
        }
        return $this->updateOrder();
    }

    /**
     * 更新数据
     */
    private function updateOrder() {
        $order_sr = M("order_sr");
        //
        $where["sn"] = $this->arrs["orderNo"];
        $save["status"] = $this->arrs["status"];
        $temps = $order_sr->where($where)->find();
        if (!$order_sr->where($where)->save($save)) {
            return get_op_res(0, "验证失败[UPERR]", null);
        }
        if ($this->arrs["status"] != 1) {
            return get_op_res(1, "验证失败[ST]", null);
        }
        #
        $this->orde = $temps;
        $this->nums = $temps["money"] > 356 ? 40 : 1;
		/*if($temps["money"]==168){
			$this->nums = 1;
		}elseif($temps["money"]==480){
			$this->nums = 4;
		}elseif($temps["money"]==1200){
			$this->nums = 12;
		}elseif($temps["money"]==3000){
			$this->nums = 40;
		}*/
        if (!$this->getUserInfo($temps["uid"])) {
            return get_op_res(0, "验证失败[CEILERR]", null);
        }
        return get_op_res(1, "验证成功", null);
    }

    ////////////////////////////////////////////////////////////////////////////

    /**
     * 获取用户信息
     */
    public function getUserInfo($uid) {
        $account = M("account");
        #
        $where["id"] = $uid;
        $info = $account->where($where)->find();
        if ($info == null) {
            return false;
        }
        //
        $update["stock"] = $info["stock"] + $this->nums;
        if (!$account->where($where)->save($update)) {
            return false;
        }
        //立即更新用户的代理等级，积分。
        cgUserLevel($uid, $this->nums);
        cgUserPoint($uid, $this->nums, $this->orde["sn"]);
        #
        if ($info["recid"] == null || $info["recid"] == 0) {
            return true;
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
        $upInfo = $account->where($cond)->find();
        if ($upInfo == null) {
            return true;
        }
        if ($this->nums > 0) {    //0元创业（3960）
            if ($upInfo['level'] > 0) {    //上级为代理
                $this->doShareFinish($upInfo, 0, $this->nums);   //第一级分成。
                //由系统发货，上级获得收益
                $up2Info = $account->where(array('sysid' => $upInfo['recid']))->find();
                if (isset($up2Info['id']) && $up2Info['level'] > 0) {
                    $this->doShareFinish($up2Info, 0, $this->nums);   //第二级分成。
                    $up3Info = $account->where(array('sysid' => $up2Info['recid']))->find();
                    if (isset($up3Info['id']) && $up3Info['level'] > 0) {
                        $this->doShareFinish($up3Info, 0, $this->nums);   //第三级分成。
                    }
                }
            } else {    //上级为游客
                //由系统发货，上级没有收益
            }
        } else {    //0元体验（356）
            if ($upInfo['level'] > 0) {    //上级为代理
                //由上级发货，上级获得收益158
                $this->doShareFinishS($upInfo, 0);
                $this->doCeilStock($upInfo);
            } else {    //上级为游客
                //由系统发货，上级获得收益90
                $this->doShareFinishS($upInfo, 0);
            }
        }

        return true;
    }

    /**
     * 完成分成 20180403
     * @param type $tmp
     * @return boolean
     */
    private function doShareFinish($tmp, $money, $num) {
        #积分返点
        if (!cgUserPoint($tmp["id"], $num, $this->orde["sn"])) {
            return false;
        }
        #现金返点
        $roll = $money * $num;
        if ($roll > 0) {
            if (!cgUserMoney($tmp["id"], $roll, 1, "REBACK", $this->orde["sn"])) {
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
        return $account->where($where)->save($save);
    }

    /**
     * 完成分成 20180403
     * @param type $tmp
     * @return boolean
     */
    private function doShareFinishS($tmp, $money) {
        #积分返点
        if (!cgUserPoint($tmp["id"], 1, $this->orde["sn"])) {
            return false;
        }
        #现金返点
        $roll = $money * 1;
        if ($roll > 0) {
            if (!cgUserMoney($tmp["id"], $roll, 1, "REBACK", $this->orde["sn"])) {
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
        return $account->where($where)->save($save);
    }

    /**
     * 完成分成
     * @param type $tmp
     * @return boolean
     */
    private function doShareFinish1($tmp, $level) {
        #积分返点
        if (!cgUserPoint($tmp["id"], 1, $this->orde["sn"])) {
            return false;
        }
        #现金返点
        $roll = $this->getLevMon($tmp, $level) * 1;
        if ($roll > 0) {
            if (!cgUserMoney($tmp["id"], $roll, 1, "REBACK", $this->orde["sn"])) {
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
        $goods = getGoodsInfo($this->orde["gsn"]);
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
     * 扣减上级库存
     */
    private function doCeilStock($info) {
        $account = M("account");
        $wctemp = D("Home/Wctemp", "Logic");
        #
        $where["sysid"] = $info['sysid'];
        $save["stock"] = $info["stock"] - $this->nums;
        if (!$account->where($where)->save($save)) {
            return false;
        }
        if ($save["stock"] < 0) {
            $user = ["openid" => $info["openid"], "nickname" => $info["nickname"]];
            $data = ["type" => "tips", "count" => $save["stock"]];
            $wctemp->entrance($user, $data);
        }
        return true;
    }

    /**
     * 扣减B端库存
     */
    private function doCeilStock_bak($id) {
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
            $save["stock"] = $info["stock"] - $this->nums;
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
}
