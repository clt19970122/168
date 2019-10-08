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
/**
 * 订单状态判断
 */
function orderStatus($status,$gid) {
    if($status==0){
       return '待支付';
    }elseif($status==1 && $gid==8){
        return '已入库';
    }elseif($status==3){
        return '已完成';
    }elseif($status==4){
        return '已取消';
    }elseif($status==5){
        return '自提';
    }elseif($status==1){
        return '待发货';
    }
}

function orderClass($status,$gid) {
    if($status==0){
        return 'waits';
    }elseif($status==1 && $gid==8){
        return 'saves';
    }elseif($status==3){
        return '';
    }elseif($status==4){
        return 'cancel';
    }elseif($status==5){
        return 'cancel';
    }elseif($status==1){
        return 'saves';
    }
}
//用户ip地址
function information($sysid,$live){
    $area=get_area();
    $user_code=[
        'sysid'=>$sysid,
        'live'=>$live,
        'ip'=>$area['data']['ip'],
        'region'=>$area['data']['region'],
        'city'=>$area['data']['city'],
        'area'=>$area['data']['area'],
        'time'=>time(),
    ];
    return $user_code;
}

