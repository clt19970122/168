<?php

/**
 * 清理地址格式
 */
function clearAddrList($list) {
    $ret = array();
    foreach ($list as $k => $v) {
        $ret[$k] = array(
            'label' => $v["name"] . "-" . $v["phone"],
            'value' => $k,
        );
    }
    return json_encode($ret, JSON_UNESCAPED_UNICODE);
}

/**
 * 清理银行格式
 */
function clearBankList($list) {
    $ret = array();
    foreach ($list as $k => $v) {
        $ret[$k] = array(
            'label' => $v["name"] . "-" . $v["card"],
            'value' => $v["id"],
        );
    }
    return json_encode($ret, JSON_UNESCAPED_UNICODE);
}

/**
 * 统计订单
 */
function countOrder($uid) {
    $orders = M("orders");
    #
    $where["uid"] = $uid;
    $where["status"] = array("in", "1,2,3");
    $count = $orders->where($where)->count();
    return $count;
}
