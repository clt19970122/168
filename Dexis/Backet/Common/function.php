<?php

/**
 * 获取订单状态
 * @param type $status
 * @return string
 */
function getOrdSta($status) {
    $str = "待支付";
    $status == 0 ? $str = "待支付" : null;
    $status == 1 ? $str = "待发货" : null;
    $status == 2 ? $str = "待确认" : null;
    $status == 3 ? $str = "已完成" : null;
    return $str;
}

/**
 * 泛搜索商户
 */
function searchInShop($name) {
    $shops = M("shops");
    #
    $where["name"] = array("like", "%" . $name . "%");
    $list = $shops->where($where)->select();
    $tmp = i_array_column($list, "id");
    return implode(",", $tmp);
}

/**
 * 获取商品分类名称
 */
function getProductName($id) {
    $syscat = M("syscat");
    #
    $where["id"] = $id;
    $info = $syscat->where($where)->find();
    return $info["name"];
}
/**
 * 获取分类名称
 */
function getQusName($id) {
    $ques_cat = M("ques_cat");
    #
    $where["id"] = $id;
    $info = $ques_cat->where($where)->find();
    return $info["name"];
}

/**
 * 获取推荐用户
 * @param type $uid
 */
function getRecUser($uid) {
    $account = M("account");
    #
    $where["id"] = $uid;
    $info = $account->where($where)->find();
    if ($info["recid"] == null) {
        return "";
    }
    $cond["sysid"] = $info["recid"];
    $user = $account->where($cond)->find();
    if($user){
        return $user["name"] . "(" .  getUserLevel($user["level"]). ")";
    }else{
        return '-无推荐人-';
    }
}

/** 支付方式
 * @param $type
 * @return string
 */
function getpaytype($type){
    switch ($type) {
        case 1 :
            return '微信';
            break;
        case 2 :
            return '支付宝';
            break;
        case 3 :
            return '银联';
            break;
        case 4 :
            return '余额';
            break;
    }
}

/**
 *根据用户id获取用户名字
 */
function getNameById($uid){
    $userinfo =M('account')->where(['id'=>$uid])->find();
    return $userinfo['name'];
}
/**获取订单
 * @param $uid @用户id
 * @param $type @购买类型
 * @return mixed
 */
function  getOrder($uid,$type){
    $order =M('orders');
    $acc_nums =M('acc_nums');
    $acc_recode =M('acc_record');
    //获取用户等级
    $reocrd_where['dotype'] =$type;
    $reocrd_where['uid'] =$uid;
    $user_level =$acc_recode->where($reocrd_where)->find();
    $where['buy_level'] =$user_level['before'];
    $where['uid'] =$uid;
    //获取订单
    $data=$order->where($where)->order('id desc')->find();
    //获取出货人
    $nums_info  =$acc_nums -> where(['sn'=>$data['sn']])->find();
    return $nums_info;
}

/**
 * 计算用户被冻结的金额
 * @param $uid 用户id
 * @return string 被冻结的金额
 */
/*function accountFrozen($uid){
    $frozen = M("frozen");
    $account = M("account");
    $where['uid']=$uid;
    $where['type']=2;
    $wheres['uid']=$uid;
    $wheres['type']=1;
    $unfreeze =$frozen->where($where)->sum('frozen_money');//解冻金额
    $frozen_money =$frozen->where($wheres)->sum('frozen_money');//冻结金额
    $money=bcsub($frozen_money,$unfreeze,2);
    return $money;
}*/
/** 操作方式
 * @param $type
 * @return string
 */
function FrozenType($type){
    switch ($type) {
        case 1 :
            return '冻结';
            break;
        case 2 :
            return '解冻';
            break;
    }

}

/** 备注显示
 * @param $sn 订单号
 * @return mixed
 */
function payRemake($sn){
    $moneyCode =M('money_code');
    $where['drop_id'] =$sn;
    $where['status'] =1;
    $data=$moneyCode->where($where)->find();
    return $data['remake'];
}


function getMonth($time = '', $format='Y-m-d'){
    $time = $time != '' ? $time : time();
    //获取当前周几
    $week = date('d', $time);
    $date = [];
    for ($i=1; $i<= date('t', $time); $i++){
        $date[$i] = date($format ,strtotime( '+' . $i-$week .' days', $time));
    }
    return $date;
}

function get_week($time = '', $format='Y-m-d')
{
    $time = $time != '' ? $time : time();
    //获取当前周几
    $week = date('w', $time);
    $date = [];
    for ($i = 1; $i <= 7; $i++) {
        $date[$i] = date($format, strtotime('+' . $i - $week . ' days', $time));
    }
    return $date;
}
function get_weekinfo($month){
    $weekinfo = array();
    $end_date = date('d',strtotime($month.' +1 month -1 day'));
    for ($i=1; $i <$end_date ; $i=$i+7) {
        $w = date('N',strtotime($month.'-'.$i));

        $weekinfo[] = array(date('Y-m-d',strtotime($month.'-'.$i.' -'.($w-1).' days')),date('Y-m-d',strtotime($month.'-'.$i.' +'.(7-$w).' days')));
    }
    return $weekinfo;
}

function getTeamtop($ssid){
    ini_set('memory_limit', '-1');
    $account =M('account');
    $user =$account->where(['sysid'=>$ssid])->field('nickname,name')->find();
    $alldata=$account->field('id,recid,sysid,nickname,name,phone,status,level,times,stock,totalpoints,money')->where(['recid'=>$ssid])->select();
    $html=$alldata;
    $arrr=getSum($html,$alldata);
    foreach($arrr as $k=>$v){
        $arrr[$k]['id']=$v['id'];
        $arrr[$k]['name']=$v['name'];
        $arrr[$k]['nickname']=$v['nickname'];
        $arrr[$k]['phone']=$v['phone'];
        $arrr[$k]['money']=$v['money'];
        $arrr[$k]['totalpoints']=$v['totalpoints'];
        $arrr[$k]['stock']=$v['stock'];
        $arrr[$k]['status']=$v['status'];
        $arrr[$k]['level']=$v['level'];
        $arrr[$k]['recid']=$v['recid'];
        $arrr[$k]['sysid']=$v['sysid'];
        $arrr[$k]['headimg']=$v['headimgurl'];
    }
//        var_dump($alldata);
    $list['list']=$arrr;
    $count_nums =count($arrr);
    return $count_nums;
    //var_dump($count_nums);exit;
}


function dx($id){
    $acc_nums =M('acc_nums');
    $where['type']=array('in','22,21,23');
    $where['uid']=$id;
    $date=$acc_nums->where($where)->sum('nums');
   // var_dump($date);exit;
    return $date;
}
 function uniquArr($array){
    $result = array();
    foreach($array as $k=>$val){
        $code = false;
        foreach($result as $_val){
            if($_val['uid'] == $val['uid']){
                $code = true;
                break;
            }
        }
        if(!$code){
            $result[]=$val;
        }
    }
    return $result;
}

function address($id){
    $acc_addr=M('acc_addr');
    $info=$acc_addr->where(['uid'=>$id])->find();
    $addr=explode(",",$info['street']);
    $datas =$addr['0'].','.$addr['1'];
    return $datas;
}

//判断值是否为数组拼接
function is_arr_objk($data){
    if(is_array($data)){
        return implode(',',$data);
    }else{
        return $data;
    }
}