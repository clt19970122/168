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
/*function getTransNm($id) {
    $systrans = M("systrans");
    $where["id"] = $id;
    $info = $systrans->where($where)->find();
    return $info["ename"];
}*/
/**
 * 获取物流公司名称
 * @param type $id
 * @return type
 */
function getTransNm($id,$name ='ename') {
    $systrans = M("systrans");
    $where["id"] = $id;
    $info = $systrans->where($where)->find();
    return $info[$name];
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
    return $info["name"] == null ? '游客' : $info["name"];
}
/**
 * 根据订单用户下级
 * @param type $id 反钱单号sn
 */
function getLowUser($sn,$field='nickname') {
    $orders = M("orders");
	$account = M("account");
    #
    $where["sn"] = $sn;
    $info = $orders->where($where)->find();
	$where['id'] = $info['uid'];
	$name = $account->where($where)->find();
    return $name[$field];
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

//根据购买数量计算价格---old
function getPrice($nums) {   //上帝之泪
    $price = 0;
    /*if ($nums >=1 && $nums <4) {
        $price = '168.00';
    } else if ($nums >=4 && $nums <12) {
        $price = '120.00'; //月体验 白银
    }  else if ($nums >=12 && $nums <40) {
        $price = '100.00'; //月体验 白银
    } else if ($nums >=40 && $nums <400) {
        $price = '75.00';
    } else if ($nums >=400 && $nums <4000) {
        $price = '65.00';
    } else if ($nums >=4000 && $nums <40000) {
        $price = '55.00';
    } else if ($nums >=40000) {
        $price = '45.00';
    }*/ if ($nums >=1 && $nums <4) {
        $price = '168.00';
    } else if ($nums >=4 && $nums <12) {
        $price = '120.00'; //月体验 白银
    }  else if ($nums >=12 && $nums <40) {
        $price = '100.00'; //月体验 白银
    } else if ($nums >=40 && $nums <240) {
        $price = '75.00';
    } else if ($nums >=240 && $nums <500) {
        $price = '65.00';
    } else if ($nums >=500 && $nums <2800) {
        $price = '55.00';
    } else if ($nums >=2800) {
        $price = '45.00';
    }
    return $price;
}

/**根据数量获取价格 ----new
 * @param $nums
 * @param $gsn
 * @return mixed
 */
function getPayprice($nums, $gsn){
    $acc_level =M('acc_level');
    $good_level =M('goods_level');
    $where['months'] =array('elt',$nums);
    $getprice_level =$acc_level->where($where)->order('id desc')->find();
    if($getprice_level) {
        $get_level = $getprice_level['id'];
        $g_where['gsn'] = $gsn;
        $g_where['lid'] = $get_level;
        $get_price = $good_level->where($g_where)->find();
        if($get_price){
            $price =$get_price['price'];
        }else{
            $price =168;
        }
    }else{
        $price=168;
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


/** 用户金额变动金额记录
 * @param $id
 * @param $money
 * @param $type
 * @param $res
 * @param int $sn
 * @param bool $iswx
 * @param string $drs_sn
 * @param bool $is_plan
 * @return bool|mixed
 */
function cgUserMoney($id, $money, $type, $res, $sn = 0, $iswx=true,$is_plan=false,$drs_sn ='') {
    $account = M("account");
    $acc_money = M("acc_money");
    $frozen_money = M("frozen_money");
    #
    $where["id"] = $id;
    $info = $account->where($where)->find();
    $save['money']=$info['money'];
    $save['uptimes']=time();
    //判断是否金融
    if(!$is_plan) {
        if ($iswx) {
            $save["money"] = $type == 1 ? $info["money"] + $money : $info["money"] - $money;
            if ($save["money"] < 0) {
                return false;
            }
            if (!$account->where($where)->save($save)) {
                return false;
            }
        }
    }else{
        //走金融的用户
        $add_data =[
            'uid'=>$id,
            'sr_sn'=>$sn,
            'frozen_money'=>$money,
            'status'=>0,
            'add_time'=>time(),
            'drs_sn'=>$drs_sn,
        ];
        $have_add =$frozen_money->add($add_data);
        //添加冻结失败
        if(!$have_add){
            return false;
        }
    }
    #
    $put = array(
        "uid" => $id, "sn" => $sn, "models" => $res, "type" => $type, "befores" => $info["money"],
        "afters" => $save["money"], "money" => $money, "times" => time()
    );
    return $acc_money->add($put);
}

/**
 * 变动用户积分
 * @param type $id
 * @param type $point
 */
function cgUserPoint($id, $point, $sn = 0) {
    $account = M("account");
    $acc_points = M("acc_points");
    #
    $where["id"] =$id;

    $info = $account->where($where)->find();
    #
    $save["points"] = $info["points"] + $point;
    $save["totalpoints"] = $info["totalpoints"]+$point;
    if (! $account->where($where)->save($save)) {
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
 * 变动用户金额---old
 * @param type $id
 * @param type $point
 */
function cgUserLevel($id, $nums) {
    $account = M("account");
    $where = array();
    $where["id"] = $id;
    $level = 0;
    /*if ($nums >=4 && $nums <12) {
        $level = 1;
    } elseif ($nums >=12 && $nums <40) {
        $level = 2;
    } elseif ($nums >=40 && $nums <400) {
        $level = 3;
    } elseif ($nums >=400 && $nums <4000) {
        $level = 4;
    } elseif ($nums >=4000 && $nums <40000) {
        $level = 5;
    } elseif ($nums >=40000) {
        $level = 6;
    }*/if ($nums >=4 && $nums <12) {
        $level = 1;
    } elseif ($nums >=12 && $nums <40) {
        $level = 2;
    } elseif ($nums >=40 && $nums <200) {
        $level = 3;
    } elseif ($nums >=200 && $nums <400) {
        $level = 4;
    } elseif ($nums >=400 && $nums <2000) {
        $level = 5;
    } elseif ($nums >=2000) {
        $level = 6;
    }

    //----
    $res =$account->where($where)->find();
    if($res['level']>=$level){
        return true;
    }
    //---
    $where["level"] = array('lt', $level);
    $save = array();
    $save["level"] = $level;
    $ress =$account->where($where)->save($save);
    $nums =getNumsByLevel($level);
    //等级变动后
    if($ress){
        $arr =[
            'uid'=>$id,
            'dotype'=>'用户升级',
            'before'=>$res['level'],
            'after'=>$level,
            'time'=>time(),
            'level_num'=>$nums,
        ];
        addLevelRecode($arr);
        return true;
    }else{
        return false;
    }
}

/**根据数量修改用户等级---new
 *time  2018-10-17 10:58:15 add
 * @param $id
 * @param $nums
 */
function changeUserLevel($id, $nums,$act=''){
    $account = M("account");
    $acc_level =M('acc_level');
    $where['months'] =array('elt',$nums);
    $getLevel =$acc_level->where($where)->order('id desc')->find();
    $level=$getLevel['id'];
    $wheres["id"] = $id;
    $userdata =$account->where($wheres)->find();//获取用户信息
    if($userdata['level']>=$level){
        return true;
    }
    $wheres["level"] = array('lt', $level);
    $save = array();
    $save["level"] = $level;
    $ress =$account->where($wheres)->save($save);//储存信息
    $nums =getNumsByLevel($level);
    //等级变动后
    if($ress){
        $arr =[
            'uid'=>$id,
            'dotype'=>'用户升级'.$act,
            'before'=>$userdata['level'],
            'after'=>$level,
            'time'=>time(),
            'level_num'=>$nums,
        ];
        addLevelRecode($arr);
        return true;
    }else{
        return false;
    }
}

//------------2018-07-26  add 查询比当前账户高的上级---
/**获取比当前等级高的账户
 * @param $sysid
 * @param $nowLevel
 */
function getHigherThanNow($sysid, $nowLevel,$i=-1){
    $i++;
    global $temps ;
    $acocunt =M('account');
    $res_info =$acocunt->where(['sysid'=>$sysid])->find();
    if(!$res_info){
        return false;
    }
    $temps[] =$res_info;
    if($res_info['level'] <=$nowLevel){
        getHigherThanNow($res_info['recid'],$nowLevel,$i);
    }
    ob_flush();
    return end($temps);
}

//联创等级上级获取
function gettop($sysid,$nowLevel,$i=-1){
    $i++;
    global $temps ;
    $acocunt =M('account');
    $res_info =$acocunt->where(['sysid'=>$sysid])->find();
    if(!$res_info){
        return false;
    }
    $temps[] =$res_info;
    if($res_info['level'] <$nowLevel){
        gettop($res_info['recid'],$nowLevel,$i);
    }
    return end($temps);
}


/**
 *获取操作的分类
 */
function getModels($models){
    switch ($models){
        case 'ORDER':return '订单支出';break;
        case 'DRAW':return '提现' ;break;
        case 'DRAWR':return '提现驳回' ;break;
    }
}

//给数据表添加字段
function addField($table,$addfield,$decoum){
    $allfield =M($table)->getDbFields();
    if(!in_array($addfield,$allfield)){
        $sql ='ALTER table '.$table.' ADD '.$addfield.' '.$decoum;
        M()->execute($sql);
        return true;
    }
    return true;
}


/** 购买记录
 * @param $arr array
 * @return mixed
 */
function addAcc_nums($arr){
   return M('acc_nums')->add($arr);
}


/**
 *获取购买类型
 */
function getnumtype($type,$isthis=1){
    if($isthis==1) {
        switch ($type) {
            case 11:
                return '金融';
                break;
            case 12:
                return '购买';
                break;
            case 13:
                return '管理添加';
                break;
            case 21:
                return'库存转出';
                break;
            case 22:
                return '卖出';
                break;
            case 23:
                return '提货';
                break;
        }
    }else{

        switch ($type) {
            case 11:
                return '被买';
                break;
            case 12:
                return '被买';
                break;
            case 13:
                return'被买';
                break;
            case 21:
                return'库存转入';
                break;
        }
    }
}

/**获取0元计划的状态值
 * @param $status
 * @return string
 */
function getplansStatus($status){
    switch ($status){
        case 0: return '已提交';break;
        case 1: return '等待电话确认';break;
        case 2: return '未通过';break;
        case 3: return '支付中';break;
        case 4: return '已还款';break;
        case 5: return '已确认办理';break;
        case 6: return '取消办理';break;
        case 7: return '已逾期';break;
    }
}

/**获取地址信息
 * @param $id
 * @param string $field
 * @return mixed
 */
function getAddrInfo($id, $field=''){
    $res_info =M('acc_addr')->where(['id'=>$id])->find();
    if($field !=''){
        return $res_info[$field];
    }else{
        return $res_info;
    }
}


/**地区支付物流费用
 * @param $address string
 * @param $where_place array
 */
function getAddressTranPay($address,$where_place=''){
    $firstone = trim($address,',');
    $firstTwo =mb_substr($firstone, 0, 2,'utf-8');
   /* $zero=array('四川');
    $one=array('北京','天津','广东','广汉,','福建','上海','江苏','浙江');
    $two=array('重庆');
    $three=array('安徽','广西','海南','河北','河南','湖北','湖南','江西','山东','山西');
    $fore=array('贵州','陕西');
    $five=array('云南');
    $six=array('甘肃','辽宁','内蒙古','宁夏','青海');
    $seven=array('黑龙江','吉林','新疆');
    switch ($firstTwo){
        case in_array($firstTwo,$zero);return 0;break;
        case in_array($firstTwo,$one);return 1;break;
        case in_array($firstTwo,$two);return 2;break;
        case in_array($firstTwo,$three);return 3;break;
        case in_array($firstTwo,$fore);return 4;break;
        case in_array($firstTwo,$five);return 5;break;
        case in_array($firstTwo,$six);return 6;break;
        case in_array($firstTwo,$seven);return 7;break;
    }*/
    if($where_place){
        $where=$where_place;
    }
    $where['price'] =array('like','%'.$firstTwo.'%');
    $isout =M('trans_addr_price')->where($where)->find();
   if($firstTwo=='四川'){
       $firstTwo =mb_substr($address, 4, 2,'utf-8');
       $where['price'] =array('like','%'.$firstTwo.'%');

       $isout2 =M('trans_addr_price')->where($where)->find();
   }

   $id=$isout2==null?$isout['id']:$isout2['id'];
   return $id;
}


/**
 *获取用户的销量 利润金额 返利
 */
function getUserProductAndMoneyInfo($uid){

    #
    #统计出货量
    $acc_nums =M('acc_nums');
    $outnum_where['aboutid'] =$uid;
    //    $outNum =$acc_nums->where($outnum_where)->sum('nums');
//    $outNum=$outNum==null?0:$outNum;
    //出
    $where3['uid']= $uid;
    $where3['type']= array('gt',20);
    //出
    $where4['aboutid']=$uid;
    $where4['type']= array('lt',20);
    $outsum2 =$acc_nums->where($where3)->sum('nums');
    $outsum1 =$acc_nums->where($where4)->sum('nums');
    $outNum=$outsum1+$outsum2;

    $userinfo =M('account')->where(['id'=>$uid])->find();
    $out_nums= $userinfo['totalpoints']- $userinfo['stock'];

    #统计差价 返利金额
    $acc_money =M('acc_money');
    $reback_where['uid'] =$uid;
    $reback_where['models'] ='REBACK';
    $get_money_arr =$acc_money->where($reback_where)->select();
    foreach ($get_money_arr as $k =>$v){
        $same =$acc_money->where(['sn'=>$v['sn'],'models'=>'REBACK'])->order('money desc')->select();
        if($v['sn']!=0) {
            $nums_change = $acc_nums->where(['sn' => $v['sn']])->find();
            if ($nums_change) {//购买用户
                if($nums_change['aboutid']!=$uid){//出货人
                    $reback_money[] = $v['money'];
                }else{
                    if(count($same)<=2) {
                        if ($same[0]['money'] = $v['money']) {
                            $salemoney[] = $v['money'];
                        }
                    }
                }
            }
        }
        /*$count_reback =count($same);
        if($count_reback<=2){
            if($count_reback==2){
                if($same[0]['money']>$v['money']){
                    //返利
                    $reback_money[]=$v['money'];
                }else{
                    //统计获得的差价
//                    $nums =$acc_nums->where(['sn'=>$v['sn']])->field('nums')->find();
//                    $getmoney[] =$v['money'] -($cost*$nums['nums']);
                    $salemoney[] =$v['money'];
                }
            }else{
                //统计获得的差价
//                $nums =$acc_nums->where(['sn'=>$v['sn']])->field('nums')->find();
//                $getmoney[] =$v['money'] -($cost*$nums['nums']);
                $salemoney[] =$v['money'];
            }
        }else{
            //返利
            $reback_money[]=$v['money'];
        }*/
    }
    #返利金额
    $all_reback_money =array_sum($reback_money)==null?0:array_sum($reback_money);
    #差价金额
//    $all_getmoney =array_sum($getmoney)==null?0:array_sum($getmoney);
//    var_dump($getmoney);
    #销售额
    $all_salemoney =array_sum($salemoney)==null?0:array_sum($salemoney);
    return array('nums'=>$outNum,'reback_money'=>$all_reback_money,'salemoney'=>$all_salemoney,'out_nums'=>$out_nums);
}

//根据等级获取数量  2018-9-21 14:49:53 add
function getNumsByLevel($level){

    #change by 2018-10-17 11:41:01
    $acc_level =M('acc_level');
    $count =$acc_level->where(['id'=>$level])->find();
    return $count['months'];

   /*switch ($level){
       case 1 : return 4 ;break;
       case 2 : return 12 ;break;
       case 3 : return 40 ;break;
       case 4 : return 240 ;break;
       case 5 : return 500 ;break;
       case 6 : return 2800 ;break;
   }*/
}

/**目前的转货数量 2018-10-17 17:10:39 add
 * @param $uid
 * @param $level
 */
function nowTrunNum($uid){
    $acc_num =M('acc_nums');
    $time =strtotime(date('Y-m',time()));
    $end =strtotime(date("Y-m",strtotime('+1 month')));
    $where['time']=array('between',[$time,$end]);
    $where['uid']=$uid;
    $where['type']=21;
    $allout_num =$acc_num->where($where)->sum('nums');
    return $allout_num;
}

/**
 *获取用户数量和金额的列表
 */
function getUserRebackMoneyList($uid,$models='REBACK'){
    $acc_money =M('acc_money');
    $acc_num =M('acc_nums');
    $reback_where['uid'] =$uid;
    $reback_where['models'] =$models;
    $get_money_arr =$acc_money->where($reback_where)->field('id,uid,money,sn,models,times')->order('id desc')->select();
    foreach ($get_money_arr as $k =>$v) {
        $same = $acc_money->where(['sn' => $v['sn'], 'models' => 'REBACK'])->order('money desc')->select();
//        $buy_per=$acc_money->where(['sn' => $v['sn'], 'models' => 'ORDER'])->find();
        if($v['sn']!=0) {
            $nums_change = $acc_num->where(['sn' => $v['sn']])->find();
            if ($nums_change) {//购买用户
                $get_money_arr[$k]['buyer'] =getUserInf($nums_change['uid'], "nickname");
                $get_money_arr[$k]['buy_nums'] = $nums_change['nums'];
                $get_money_arr[$k]['up_buyer'] = getRec($nums_change['uid']);
                if($nums_change['aboutid']!=$uid){//出货人
                    $get_money_arr[$k]['money']='+'.$v['money'];
                    $reback_money[] = $get_money_arr[$k];
                }else{
                    if(count($same)>1) {
                        if ($same[0]['money'] <= $v['money']) {
                            $get_money_arr[$k]['money'] = '-' . $same[1]['money'];
                            $reback_money[] = $get_money_arr[$k];
                        }
                    }
                }
            }
        }
    }


    return $reback_money;
}

/**获取用户出货列表详情
 * @param $uid
 */
function getUserNumsList($uid,$type=1){
    $acc_num =M('acc_nums');
    $acc_money =M('acc_money');
    //转
    if($type==1){
        //卖
        $where4['aboutid']=$uid;
        $where4['type']= array('lt',20);
        $sale =$acc_num->where($where4)->order('time desc')->select();
        foreach ($sale as $k=>$v){
            $sale[$k]['buyer'] =getUserInf($v['uid'], "nickname");
            $order_info =$acc_money->where(['sn'=>$v['sn'],'models'=>'REBACK'])->find();
            $sale[$k]['pay'] =$order_info['money'];
            //2018-11-1 13:41:46 注释
            /*if($v['type'] ==11){//金融
                $buy_money =$acc_money->where(['sn'=>$v['sn'],'models'=>'REBACK'])->sum('money');
                $sale[$k]['pay'] =$buy_money;
            }else{
                $order_info =$acc_money->where(['sn'=>$v['sn'],'models'=>'ORDER'])->find();
                $sale[$k]['pay'] =$order_info['money'];
            }*/

        }
        $rest=$sale;
    }else{
        $where3['uid']= $uid;
//        $where3['type']= array('gt',20);
        $where3['type']=21;
        $where3['status']= 1;
        $turn=$acc_num->where($where3)->order('id desc')->select();
        foreach ($turn as $k=>$v){
            $turn[$k]['buyer'] =getUserInf($v['aboutid'], "nickname");
            $turn[$k]['payer'] =getUserInf($v['uid'], "nickname");
            $turn[$k]['pay'] =0;
        }
        $rest=$turn;
    }


    return $rest;
}

/**
 * 获取推荐用户
 * @param type $uid
 */
function getRec($uid) {
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
        return $user["nickname"] ;
    }else{
        return '-无推荐人-';
    }
}

/**
 *获取某个人同地区的其他订单
 */
function getsameAddress($uid,$address){
    $orders=M('orders');
    $start =strtotime('-7 hours',strtotime(date('Y-m-d')));
    $end =strtotime('+17 hours',strtotime(date('Y-m-d')));
    $wheres['uid'] =$uid;
    $wheres['addr'] =$address;
    $wheres['status'] =1;
    $wheres['gid'] =array('neq',8);
    $wheres['times'] =array('between',$start,$end);
    $sameorder =$orders->where($wheres)->select();
    return $sameorder;
}


/** 添加经销存的记录
 * @param $uid
 * @param $gid
 * @param $where  0公司  1厂库
 * @param $num
 * @param int $type 1提 2进
 * @return bool
 */
function AddStockAndSale($uid, $gid, $where, $num, $type=1,$remark='',$supper=''){

    $ware =M('goods_ware');
    $outList =M('goods_outlist');
    $goods =M('goods');
    $ware->startTrans();
    $goods_info =$goods->where(['id'=>$gid])->field('name,gsn')->find();
    $All_stock =M('account')->sum('stock');

    if(!$goods_info){
        return false;
    }
    $wa_wherer['goods_id']=$gid;
    $ware_info =$ware->where($wa_wherer)->find();
    /*
     * 处理进出货逻辑判断
     * in - from company add , subtract warehouse, all_save not add
     * add warehouse,all_save add
     * out- need to  subtract both
     * start do
     * */
    if($ware_info) {
        if ($type == 2) {//进货
            if ($where==0) {//公司
                $save['company_save'] =array('',);
                $update["company_save"]= array("exp", "company_save+" .$num);
                $update["ware_save"]= array("exp", "ware_save-" .$num);
                $update["all_save"]=$ware_info['all_save'];
            } else {
                $update["ware_save"]= array("exp", "ware_save+" .$num);
                $update["all_save"]= $ware_info['all_save'] +$num;
                $update["all_in"]= $ware_info['all_in'] +$num;
//                array("exp", "all_save+" .$num);
            }
        }else{//出
            if ($where==0) {//公司
                $update["company_save"]= array("exp", "company_save-" .$num);
            } else{
                $update["ware_save"]= array("exp", "ware_save-" .$num);
            }
            $update["all_save"]= $ware_info['all_save'] - $num;
            $update["all_sale"]= $ware_info['all_sale'] + $num;
//                array("exp", "all_save-" .$num);
        }
        $update["have_sale"] =$All_stock;
        $res =$ware->where($wa_wherer)->save($update);
    }else{
        $update["all_save"] =$num;
        $datas =[
            'goods'=>$goods_info['name'],
            'goods_id'=>$gid,
            'all_save'=>$num,
            'company_save'=>0,
            'ware_save'=>$num,
            'have_sale'=>$All_stock,
            'all_in'=>$num,
            'all_sale'=>0,
        ];
        $res =$ware->add($datas);
    }
    #
    /*添加记录
     * when change ware need add recode list
     * */
    if($res) {
        $add = [
            'uid' => $uid,
            'name' => $goods_info['name'],
            'g_id' => $gid,
            'nums' => $num,
            'type' => $type,
            'where_in' => $where,
            'addtime' => time(),
            'rest_save' => $update["all_save"],
            'remake' => $remark,
            'supplier' => $remark,
        ];
        $retu= $outList->add($add);
        if(!$retu){
            $ware->rollback();
            return false;
        }
        $ware->commit();
        return true;
    }else{
        $ware->rollback();
        return false;
    }
}

/**获取下级的数量
 * @param string $sysid
 * @param int $t
 * @return int
 */
function getChlidLevel($sysid='', $t = -1){
    $t++;
    global $temps;
    global $all_counts;
    $getzero=$t.'d';
    if($getzero=='0d'){
        $all_counts=$all_counts-$all_counts;
    }
//    var_dump($temps);

    //$where['recid']=arrray('in',implode(',',$sysid));
    $where['recid']=$sysid;
    $data =M('account')->where($where)->select();
    if (!empty($data)) {
//        var_dump(count($data));
        if(!in_array($data,$temps)){
            $all_counts +=count($data);
            $temps[] = $data;
        }
        foreach ($data as $k=>$v) {
//            var_dump($all_counts.'b');
            getChlidLevel($v['sysid'],$t);
        }
    }
//    var_dump($all_counts.'a');
    return $all_counts;
}

/**
 *添加用户等级变化的记录
 */
function addLevelRecode($array){
    $acc_recode =M('acc_record');
    $res =$acc_recode->add($array);
    if($res){
        //处理更变的等级
        $contect=M('acc_contect')->where(['ancestor_id'=>$array['uid']])->save(['user_level'=>$array['after']]);
    }
    return $res;
}

/**获取两个等级的理论差价
 * @param $id @转
 * @param $trunid @被转
 * @param $p_sn @产品的sn
 * @return  int @被转
 */
function getTheOryMoney($id, $trunid,$p_sn='415244730815416283'){

    $goods_level = M("goods_level");
    $where["gsn"] = $p_sn;
    //转的用户
    $user =M('account')->where(['id'=>$id])->find();
    //被转用户
    $trun_info  =M('account')->where(['id'=>$trunid])->find();
    if($trun_info["level"]>=$user["level"]){
        $trun_info['level'] =$user['level'] -1;
    }
    //转
    $where["lid"] = $user["level"];
    $user_money = $goods_level->where($where)->find();
    //被转
    $where["lid"] = $trun_info["level"];
    $trun_money = $goods_level->where($where)->find();
    $theMoney =$trun_money['price']-$user_money['price'];
    return abs($theMoney);
}
//获取出货的类型
function getOutType($type){
    switch ($type){
        case 0: return'公司仓库'; break;
        case 1: return'中通仓库'; break;
        case 11: return'赠送'; break;
        case 12: return'活动'; break;
        case 13: return'员工福利'; break;
        case 14: return'送人'; break;
        case 15: return'其他'; break;
    }
}

/** 上传图片
 * @param $value
 * @return string
 */
function uploadimg($value,$where){
    $base64_image_content = $value;
    //匹配出图片的格式
    if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)) {
        $type = $result[2];
        $new_file = "./Public/uploads/".$where. '/'.date('Ymd', time()) . "/";
        if (!file_exists($new_file)) {
            //检查是否有该文件夹，如果没有就创建，并给予最高权限
            mkdir($new_file, 0700,true);
        }
        $imgname =date('YmdHis').rand(1000,9999);
        $new_file = $new_file.$imgname.".{$type}";
        file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_image_content)));
        return $new_file;
    }
}

/*//团队  2018年11月26日18:54:41 add
function category($arr,$pid=0,$level=0){
    //定义一个静态变量，存储一个空数组，用静态变量，
    //是因为静态变量不会被销毁，会保存之前保留的值，
    //普通变量在函数结束时，会死亡，生长周期函数开始到函数结束，再次调用重新开始生长
    //保存一个空数组
    static $list=array();
    //通过遍历查找是否属于顶级父类，pid=0为顶级父类，
    foreach($arr as $value){
        //进行判断如果pid=0，那么为顶级父类，放入定义的空数组里
        if($value['recid']==$pid){
            //添加空格进行分层
            $arr['level']=$level;
            $list[]=$value;
            //递归点，调用自身，把顶级父类的主键id作为父类进行再调用循环，空格+1
            category($arr,$value['sysid'],$level+1);
        }
    }
    return $list;
}*/


$html = array();
/**
  * 递归查找父id为$parid的结点
  * @param array $html  按照父-》子的结构存放查找出来的结点
  * @param int $parid  指定的父id
  * @param array $channels  数据数组
  * @param int $dep  遍历的深度，初始化为1
  * @return  array $dep  遍历的深度，初始化为1
  */
function getChilds(&$html,$parid,$channels,$dep=1){
    /*
      * 遍历数据，查找parId为参数$parid指定的id
      */
    for($i = 0;$i<count($channels);$i++){
        if($channels[$i]['recid'] == $parid){
            $html[] = array(
                'id'=>$channels[$i]['id'],
                'name'=>$channels[$i]['name'],
                'nickname'=>$channels[$i]['nickname'],
                'phone'=>$channels[$i]['phone'],
                'money'=>$channels[$i]['money'],
                'totalpoints'=>$channels[$i]['totalpoints'],
                'stock'=>$channels[$i]['stock'],
                'status'=>$channels[$i]['status'],
                'level'=>$channels[$i]['level'],
                'recid'=>$channels[$i]['recid'],
                'sysid'=>$channels[$i]['sysid'],
                'headimg'=>$channels[$i]['headimgurl'],
                'dep'=>$dep
            );
            getChilds($html,$channels[$i]['sysid'],$channels,$dep+1);
        }
    }
    return $html;
}

function getSum(&$data,$channels){
    $account =M('account');
    $arr = array_column($channels, 'sysid');
    $comma = implode(",", $arr);
    $where['recid'] = array('in',$comma);
    $arrs=$account->field('id,recid,sysid,nickname,name,phone,status,level,times,stock,totalpoints,money')->where($where)->select();
    if($arrs!=null){
        foreach($arrs as $v){
            $data[]=$v;
        }
        //$data[]=$arrs;
        return getSum($data,$arrs);
    }else{
        return $data;
    }
}

/* 团队下 add - 2019年2月15日18:08:06
*2.获取某个会员的无限下级方法
*$members是所有会员数据表,$mid是用户的id
*/
function GetTeamMember($members, $mid) {
    $Teams=array();//最终结果
    $mids=array($mid);//第一次执行时候的用户id
    do {
        $othermids=array();
        $state=false;
        foreach ($mids as $valueone) {
            foreach ($members as $key => $valuetwo) {
                if($valuetwo['recid']==$valueone){
                    $Teams[] = array(
                        'id'=>$valuetwo['id'],
                        'name'=>$valuetwo['name'],
                        'nickname'=>$valuetwo['nickname'],
                        'phone'=>$valuetwo['phone'],
                        'money'=>$valuetwo['money'],
                        'totalpoints'=>$valuetwo['totalpoints'],
                        'stock'=>$valuetwo['stock'],
                        'level'=>$valuetwo['level'],
                        'headimg'=>$valuetwo['headimgurl'],
                    );
//                    $Teams[]=$valuetwo['id'];//找到我的下级立即添加到最终结果中
                    $othermids[]=$valuetwo['sysid'];//将我的下级id保存起来用来下轮循环他的下级
                    array_splice($members,$key,1);//从所有会员中删除他
                    $state=true;
                }
            }
        }
        $mids=$othermids;//foreach中找到的我的下级集合,用来下次循环
    } while ($state==true);

    return $Teams;
}


/**
 * //根据盒数获取价格
//价格是盒数的区间
 * @param $address 地区
 * @param $count 数量
 * @param int $tran 运输方式
 * @return mixed
 */
function getTranbynums($address, $count, $where_place){
    //如果第一个是符号 去除掉
    $firstone =mb_substr($address, 0, 1,'utf-8');
    if($firstone ==','){
        $address =ltrim($address,',');
    }
    $firstTwo =mb_substr($address, 0, 2,'utf-8');
    $where =$where_place;
    $where['price'] =array('like','%'.$firstTwo.'%');
    $isout =M('trans_addr_price')->where($where)->find();
    if($firstTwo=='四川'){
        $firstTwo =mb_substr($address, 4, 2,'utf-8');
        $where['price'] =array('like','%'.$firstTwo.'%');
        $isout2 =M('trans_addr_price')->where($where)->find();
    }
    $id=$isout2==null?$isout['id']:$isout2['id'];

    $wheres['nums'] =array('egt',$count);//大于等于数量
    $wheres['isout'] =$id;
    $wheres['tran'] =11;
    $getprice =M('trans_price')->where($wheres)->order('id asc')->find();
    return $getprice['price'];
}

/**获取我团队下面的所有联创
 * @param $uid
 * @param int $level
 * @return array
 */
function getmyTopTeam($uid, $level =6){

    $account =M('account');
    $user =$account->where(['id'=>$uid])->find();
    $alldata=$account->field('id,recid,sysid,nickname,name,phone,status,level,times,stock,totalpoints,money,headimgurl')->select();
//        $arrr=category($alldata,$ssid,0);
    $list=getChilds($html,$user['sysid'],$alldata,0);
//    $list=array();
    //所有用户量
//    $count_nums =count($arrr);
    $level_data=array();
    foreach ($list as $k=>$v){
        $level_data[$v['level']] +=1;
        $list[$k]['parent_name'] ='';
        $list[$k]['parent_level'] ='';
        //判断等级
        if($level==$v['level']){
            if ($v['recid'] != null || $v['recid'] != 0) {
                //修改带有。html的recid
                $recid = str_replace('.html', '', $v['recid']);
                $account->where(['id' => $v['id']])->save(['recid' => $recid]);
                //
                $parent_info = $account->where(array('sysid' => $recid))->find();
                $list[$k]['parent_name'] = $parent_info['nickname'];
                $list[$k]['parent_level'] = $parent_info['level'];
                $list[$k]['parent_id'] = $parent_info['id'];
//                    $list['list'][$k]['parent_level'] = getUserLevel(['level']);
            }
        }else{
            unset($list[$k]);
        }
    }
//    $list[count($list)]=$user;
    return $list;
}


/**获取联创用户个人的进货统计--总量
 * @param $uid
 * @param $where
 * @return  array
 */
function getTeamMoney($uid, $where){
    $acc_num =M('acc_nums');
    $where['uid']=$uid;//进货人
    $where['aboutid']=0;//出货方为公司
    $where['type']=array('in','11,12,13');//进货类型
    $user_in =$acc_num->where($where)->field('sum(nums),uid')->find();
    return $user_in;
}

/**获取联创用户个人的进货统计--金额 new add 2019年1月28日17:33:45
 * @param $uid
 * @param $where
 * @return  array
 */
function getTeamAllMoney_new($uid, $where,$self =0){
    $acc_num =M('acc_nums');
    $acc_money =M('acc_money');
//    $account =M('account');
//    $user_info =$account->where(['id'=>$uid])->find();
    $where['uid']=$uid;//进货人
    $where['aboutid']=0;//出货方为公司
    $where['type']=array('in','11,12,13');//进货类型
//    $user_in =$acc_num->where($where)->field('sum(nums),uid')->find();
    $all_data =$acc_num->where($where)->select();
    $user_in['all_nums']=0;
    $user_in['all_money']=0;
    foreach ($all_data as $k=>$v){
        $user_in['all_nums'] += $v['nums'];
        $res_money =$acc_money->where(['sn'=>$v['sn'],'models'=>'ORDER'])->find();
        $user_in['all_money'] += $res_money['money'];
    }
    return $user_in;
}


/**
 * @param $uid
 * @param int $nums 本月数量
 * @return bool
 */
function getEnoughtPoint($uid, $nums=0){
    $account =M('account');
    $acc_recode =M('acc_record');
    //获取用户信息 积分
    $user_info =$account->where(['id'=>$uid])->find();
    //用户升级的时候所需的盒数记录
    $record =$acc_recode->where(['uid'=>$uid,'after'=>6])->find();
    if($record && $record['level_num']!=0){
        if($user_info['totalpoints']>=$record['level_num']){
            $res_num =$user_info['totalpoints']-$record['level_num']>=$nums?$nums:$user_info['totalpoints']-$record['level_num'];
            return $res_num;
        }
    }else{
        if($user_info['totalpoints']>=4000){
            $res_num =$user_info['totalpoints']-4000>=$nums?$nums:$user_info['totalpoints']-4000;
            return $res_num;
        }
    }
    return false;
}

/**获取赚取的金额
 * @param $num @数量
 * @param string $gsn @产品
 */
function getTeamRatio($num, $gsn ='415244730815416283'){
    $acc_ratio =M('acc_teammoney');
    $price =doPrice($gsn,6);
    $all_money =$price*$num;
    $where['inmoney']=array('elt',$all_money);
    $getratio =$acc_ratio->where($where)->order('id desc')->find();
    $getmoney=0;
    if($getratio){
        $getmoney =$all_money*$getratio['ratio'];
    }
    return $getmoney;
}


/**根据一个时间点获取这一周时间段
 * @param $time
 * @return array
 */
function getWeek($time){
    $one_day = 24 * 3600-1;
    $get_time= strtotime(date('Y-m-d', strtotime("this week Monday", $time)));
    $end_time= strtotime(date('Y-m-d', strtotime("this week Sunday", $time))) + $one_day;
    return ['start'=>$get_time,'end'=>$end_time];
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
/**
 *获取用户的销量 利润金额 返利
 */
function getUserInfo($uid){
    $acc_nums =M('acc_nums');
    //销量
    $userinfo =M('account')->where(['id'=>$uid])->find();
    $out_nums= $userinfo['totalpoints']- $userinfo['stock'];

    #统计差价 返利金额
    $acc_money =M('acc_money');
    $reback_where['uid'] =$uid;
    $reback_where['models'] ='REBACK';
    $get_money_arr =$acc_money->where($reback_where)->select();
    foreach ($get_money_arr as $k =>$v){
        $nums_change = $acc_nums->where(['sn' => $v['sn']])->find();
        if($nums_change['aboutid']!=$uid){//出货人
            $reback_money[] = $v['money'];
        }else{
            $same =$acc_money->where(['sn'=>$v['sn'],'models'=>'REBACK'])->order('money desc')->select();
            if ($same[0]['money'] = $v['money']) {
                $salemoney[] = $v['money'];
            }
        }
    }
    #销售额
    $all_salemoney =array_sum($salemoney)==null?0:array_sum($salemoney);
    return array('salemoney'=>$all_salemoney,'out_nums'=>$out_nums);
}
//get接口
function curl_get_http($url){
    $curl = curl_init(); // 启动一个CURL会话
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);  // 从证书中检查SSL加密算法是否存在
    $tmpInfo = curl_exec($curl);     //返回api的json对象
    //关闭URL请求
    curl_close($curl);
    return $tmpInfo;    //返回json对象
}