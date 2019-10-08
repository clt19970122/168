<?php

/**
 * json数据测试
 * @param type $link
 * @param type $data
 * @return type
 */
function jsonCurl($link, $data) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $link);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data))
    );
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

/**
 * 获取订单编号
 */
function getOrdSn() {
    $uniqud = substr(uniqid(), 0, 3);
    $ret = $uniqud . date("m") . mt_rand(0, 9);
    $ret .= date("d") . mt_rand(0, 9);
    $ret .= date("His") . mt_rand(0, 9);
    return $ret;
}

/**
 * 获取系统序号
 * @param type $model  模块
 * @param type $type   账户类型
 * @param type $uid    账户ID
 * @param type $status
 */
function getSysLn($model, $type, $uid) {
    $syscode = M("syscode");
    #
    $where["models"] = $model;
    $where["type"] = $type;
    $where["uid"] = $uid;
    $where["status"] = 0;
    $info = $syscode->where($where)->find();
    if ($info != null) {
        return $info["sn"];
    }
    #
    $tmp = explode(".", microtime(true));
    $length = mt_rand(1, 9) . $tmp[0] . sprintf("%04d", $tmp[1]);
    $where["sn"] = $length . mt_rand(1, 9) . mt_rand(1, 9) . mt_rand(1, 9);
    if (!$syscode->add($where)) {
        return false;
    }
    return $where["sn"];
}

/**
 * 关闭系统序号
 * @param type $sn
 */
function closeSysLn($model, $type, $uid) {
    $syscode = M("syscode");
    #
    $where["models"] = $model;
    $where["type"] = $type;
    $where["uid"] = $uid;
    $save["status"] = 1;
    if (!$syscode->where($where)->save($save)) {
        return false;
    }
    return true;
}

/**
 * 获取分类名称
 */
function getCatName($id) {
    $syscat = M("syscat");

    $where["id"] = $id;
    $info = $syscat->where($where)->find();
    return $info["name"];
}

/**
 * 获取地区名称
 */
function getRegsName($id) {
    $sysregion = M("sysregion");

    $where["id"] = $id;
    $info = $sysregion->where($where)->find();
    return $info["name"];
}

/**
 * 地区名称转ID
 */
function regNamToID($name) {
    $sysregion = M("sysregion");
    #
    $name = str_replace("省", "", $name);
    $name = str_replace("市", "", $name);
    #
    $where["name"] = array("like", "%" . $name . "%");
    $info = $sysregion->where($where)->find();
    return $info["id"] == null ? 0 : $info["id"];
}

/**
 * 获取银行名称
 */
function getBankName($id, $alt = null) {
    $sysbank = M("sysbank");

    $where["id"] = $id;
    $info = $sysbank->where($where)->find();
    if ($alt != null) {
        return $info[$alt];
    }
    return $info["name"];
}

/**
 * 获取物流公司名称
 * @param type $id
 * @return type
 */
function getTransNm($id) {
    $systrans = M("systrans");
    $where["id"] = $id;
    $info = $systrans->where($where)->find();
    return $info["name"];
}

/**
 * 获取用户信息
 * @param type $uid
 * @param type $alt
 * @return type
 */
function getUserInf($uid, $alt) {
    $account = M("account");

    $where["id"] = $uid;
    $info = $account->where($where)->find();
    if ($info["headimgurl"] == null) {
        $info["headimgurl"] = __ROOT__ . "/Public/home/imgs/head.png";
    } else {
        $loc = strpos($info["headimgurl"], "ttp:");
        if (!$loc) {
            $info["headimgurl"] = __ROOT__ . "/Public/uploads/head/" . $info["headimgurl"];
        }
    }
    if ($alt == null) {
        return $info;
    }
    return $info[$alt];
}

/**
 * 用户等级
 * @param type $id
 */
function getUserLevel($id) {
    $acc_level = M("acc_level");
    #
    $where["id"] = $id;
    $info = $acc_level->where($where)->find();
    return $info["name"] == null ? '游客' : $info["name"] . "会员";
}

/**
 * 获取用户推荐信息
 * @param type $uid
 * @param type $alt
 * @return type
 */
function getRecInf($uid, $alt) {
    $account = M("account");

    $where["sysid"] = $uid;
    $info = $account->where($where)->find();
    return $info[$alt];
}

/**
 * 用户地址
 * @param type $addr
 */
function getUserAddr($addr, $alt = null) {
    $acc_addr = M("acc_addr");

    $where["id"] = $addr;
    $info = $acc_addr->where($where)->find();
    if ($info == null) {
        return "";
    }
    if ($alt != null) {
        return $info[$alt];
    }
    $str = getRegsName($info["province"]) . "省" . getRegsName($info["city"]) . "市";
    $str .= getRegsName($info["label"]);
    return $str;
}

/**
 * 用户地区
 * @param type $addr
 */
function getUserArea($addr, $alt = 0) {
    $acc_addr = M("acc_addr");

    $where["id"] = $addr;
    $info = $acc_addr->where($where)->find();
    if ($info == null) {
        return "";
    }
    $str = getRegsName($info["province"]) . "," . getRegsName($info["city"]) . ",";
    $str .= getRegsName($info["label"]);
    if ($alt > 0) {
        $str = str_replace(",", " ", $str);
    }
    return $str;
}

/**
 * 获取用户银行信息
 * @param type $id
 * @param type $alt
 */
function getUserBank($id, $alt) {
    $acc_bank = M("acc_bank");

    $where["id"] = $id;
    $info = $acc_bank->where($where)->find();
    if ($alt == null) {
        return $info;
    }
    return $info[$alt];
}

/**
 * 获取商品首图
 * @param type $gsn
 */
function getGoodsImg($gsn) {
    $goods_img = M("goods_img");
    #
    $where["gsn"] = $gsn;
    $info = $goods_img->where($where)->order("id asc")->find();
    return $info["imgs"];
}

/**
 * 价格计算
 * @param type $gsn
 * @param type $level
 */
function doPrice($gsn, $level) {
    $goods_level = M("goods_level");
    $goods = M("goods");
    #
    $where["gsn"] = $gsn;
    $ginfo = $goods->where($where)->find();
    $price = $ginfo["price"];
    if ($level <= 0) {
        return $price;
    }
    $where["lid"] = $level;
    $info = $goods_level->where($where)->find();
    return $info["price"] == null ? $price : $info["price"];
}

//根据购买数量计算价格
function getPrice($nums) {   //上帝之泪
    $price = -1;
    if ($nums >=4 && $nums <20) {
        $price = 218.00;
    } elseif ($nums >=20 && $nums <50) {
        $price = 198.00;
    } elseif ($nums >=50 && $nums <200) {
        $price = 178.00;
    } elseif ($nums >=200 && $nums <850) {
        $price = 148.00;
    } elseif ($nums >=850 && $nums <5000) {
        $price = 118.00;
    } elseif ($nums >=5000) {
        $price = 98.00;
    }

    return $price;
}


/**
 * 获取商品信息
 */
function getGoodsInfo($gid, $alt = null) {
    $goods = M("goods");
    #
    $where["gsn"] = $gid;
    $info = $goods->where($where)->find();
    if ($alt != null) {
        return $info[$alt];
    }
    $info["imgs"] = getGoodsImg($info["gsn"]);
    return $info;
}

/**
 * 获取商品信息
 */
function getIDGoodsInfo($gid, $alt = null) {
    $goods = M("goods");
    #
    $where["id"] = $gid;
    $info = $goods->where($where)->find();
    if ($alt != null) {
        return $info[$alt];
    }
    $info["imgs"] = getGoodsImg($info["gsn"]);
    return $info;
}

/**
 * 获取运费
 */
function getTransFees($gid, $province, $city, $label) {
    $shops_trans = M("shops_trans");
    #
    $ginfo = getGoodsInfo($gid, "sid");
    $where = array("sid" => $ginfo["sid"], "province" => $province, "city" => $city, "label" => $label);
    #
    $first = $shops_trans->where($where)->find();
    if ($first["fees"] != null) {
        return $first["fees"];
    }
    #
    unset($where["label"]);
    $second = $shops_trans->where($where)->find();
    if ($second["fees"] != null) {
        return $second["fees"];
    }
    #
    unset($where["city"]);
    $third = $shops_trans->where($where)->find();
    if ($third["fees"] != null) {
        return $third["fees"];
    }
    return 0;
}

/**
 * 获取下线
 */
function getChild($sysid) {
    $account = M("account");
    #
    $where["recid"] = $sysid;
    $list = $account->where($where)->select();
    if (count($list) <= 0) {
        return 0;
    }
    #
    $tmp = i_array_column($list, "sysid");
    $cond["sysid"] = array("neq", $sysid);
    $cond["recid"] = array("in", "" . implode(",", $tmp) . "");
    $count = $account->where($cond)->count();
    return count($list) + $count;
}

/**
 * 变动用户金额
 * @param type $openid
 * @param type $point
 * @param type $type
 * @param type $res
 */
function cgUserMoney($id, $money, $type, $res, $sn = 0) {
    $account = M("account");
    $acc_money = M("acc_money");
    #
    $where["id"] = $id;
    $info = $account->where($where)->find();
    #
    $save["money"] = $type == 1 ? $info["money"] + $money : $info["money"] - $money;
    if ($save["money"] < 0) {
        return false;
    }
    if (!$account->where($where)->save($save)) {
        return false;
    }
    #
    $put = array(
        "uid" => $id, "sn" => $sn, "models" => $res, "type" => $type, "befores" => $info["money"],
        "afters" => $save["money"], "money" => $money, "times" => time()
    );
    return $acc_money->add($put);
}

/**
 * 变动用户金额
 * @param type $id
 * @param type $point
 */
function cgUserPoint($id, $point, $sn = 0) {
    $account = M("account");
    $acc_points = M("acc_points");
    #
    $where["id"] = $id;
    $info = $account->where($where)->find();
    #
    $save["points"] = $info["points"] + $point;
    $save["totalpoints"] = $info["totalpoints"] + $point;
    if (!$account->where($where)->save($save)) {
        return false;
    }
    #
    $put = array(
        "uid" => $id, "sn" => $sn, "befores" => $info["points"],
        "afters" => $info["points"] + $point, "point" => $point, "times" => time()
    );
    return $acc_points->add($put);
}

/**
 * 时差计算
 * @param type $start
 * @param type $end
 */
function getTimerCeil($end) {
    $start = time();
    $ceil = $end - $start;
    if ($ceil < 0) {
        return 0;
    }
    return intval($ceil / 24 / 60 / 60);
}

/**
 * 检测提款金额
 * @param type $mon
 */
function checkMoneys($mon) {
    $res = $mon / 10;
    return is_float($res);
}

/**
 * 检查名称
 * @param type $uid
 * @param type $type
 */
function checkNames($uid, $type) {
    if ($type == '1') {
        return getUserInf($uid, "nickname");
    }
    $shops = M("shops");
    #
    $where["id"] = $uid;
    $info = $shops->where($where)->find();
    return $info["name"];
}

/**
 * 记录维修金额
 */
function logFixedMoney($fid, $money) {
    $money_fixed = M("money_fixed");
    #
    $put["fid"] = $fid;
    $put["money"] = $money;
    $put["status"] = 0;
    $put["times"] = time();
    return $money_fixed->add($put);
}

/**
 * 统计订单
 */
function countOrderNums($uid, $status) {
    $order = M("orders");
    #
    $where["uid"] = $uid;
    $where["status"] = $status;
    return $order->where($where)->count();
}

/**
 * 变动用户金额
 * @param type $id
 * @param type $point
 */
function cgUserLevel($id, $nums) {
    $account = M("account");
    $where = array();
    $where["id"] = $id;

    $level = 0;
    if ($nums >=4 && $nums <20) {
        $level = 1;
    } elseif ($nums >=20 && $nums <50) {
        $level = 2;
    } elseif ($nums >=50 && $nums <200) {
        $level = 3;
    } elseif ($nums >=200 && $nums <850) {
        $level = 4;
    } elseif ($nums >=850 && $nums <5000) {
        $level = 5;
    } elseif ($nums >=5000) {
        $level = 5;
    }
    $where["level"] = array('lt', $level);

    $save = array();
    $save["level"] = $level;
    return $account->where($where)->save($save);
}