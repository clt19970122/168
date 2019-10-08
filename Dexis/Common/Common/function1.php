<?php

/**
 * 输出json
 * @param type $status
 * @param type $msg
 * @param type $data
 */
function get_op_put($status, $msg, $data = null) {
    $array = array(
        "status" => $status,
        "msg" => $msg,
        "data" => $data
    );
    echo json_encode($array);
    exit;
}

/**
 * 输出数组
 * @param type $status
 * @param type $msg
 * @param type $data
 */
function get_op_res($status, $msg, $data = null) {
    $array = array(
        "status" => $status,
        "msg" => $msg,
        "data" => $data
    );
    return $array;
}

/**
 * array_column函数兼容方法
 * @param type $input
 * @param type $columnKey
 * @param type $indexKey
 * @return type
 */
function i_array_column($input, $columnKey, $indexKey = null) {
    if (!function_exists('array_column')) {
        $columnKeyIsNumber = (is_numeric($columnKey)) ? true : false;
        $indexKeyIsNull = (is_null($indexKey)) ? true : false;
        $indexKeyIsNumber = (is_numeric($indexKey)) ? true : false;
        $result = array();
        foreach ((array) $input as $key => $row) {
            if ($columnKeyIsNumber) {
                $tmp = array_slice($row, $columnKey, 1);
                $tmp = (is_array($tmp) && !empty($tmp)) ? current($tmp) : null;
            } else {
                $tmp = isset($row[$columnKey]) ? $row[$columnKey] : null;
            }
            if (!$indexKeyIsNull) {
                if ($indexKeyIsNumber) {
                    $key = array_slice($row, $indexKey, 1);
                    $key = (is_array($key) && !empty($key)) ? current($key) : null;
                    $key = is_null($key) ? 0 : $key;
                } else {
                    $key = isset($row[$indexKey]) ? $row[$indexKey] : 0;
                }
            }
            $result[$key] = $tmp;
        }
        return $result;
    } else {
        return array_column($input, $columnKey, $indexKey);
    }
}

/**
 * CURL扩展方法
 * @param type $url
 * @param type $params
 * @return type
 */
function poCurl($url, $params) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

/**
 * CURL-JPUSH扩展方法
 * @param type $url
 * @param type $params
 * @return type
 */
function poCurlHed($url, $head, $auth) {
    $ch = curl_init();
    $options = array(
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => true,
        CURLOPT_HTTPHEADER => $head,
        CURLOPT_USERAGENT => 'JMessage-Api-PHP-Client',
        CURLOPT_CONNECTTIMEOUT => 20,
        CURLOPT_TIMEOUT => 120,
        CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
        CURLOPT_USERPWD => $auth,
        CURLOPT_URL => $url,
        CURLOPT_CUSTOMREQUEST => "GET"
    );
    curl_setopt_array($ch, $options);
    $output = curl_exec($ch);
    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $body = substr($output, $header_size);
    curl_close($ch);
    return $body;
}

/**
 * 微信证书CURL扩展方法
 * @param type $url
 * @param type $params
 * @return type
 */
function wcPoCurl($url, $params) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
    curl_setopt($ch, CURLOPT_SSLCERT, getcwd() . '/Public/cert/apiclient_cert.pem');
    curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
    curl_setopt($ch, CURLOPT_SSLKEY, getcwd() . '/Public/cert/apiclient_key.pem');
    curl_setopt($ch, CURLOPT_CAINFO, 'PEM');
    curl_setopt($ch, CURLOPT_CAINFO, getcwd() . '/Public/cert/rootca.pem');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

/**
 * 上传文件
 * @param type $file
 * @return boolean
 */
function uploadFile($file) {
    $upload = new \Think\Upload();
    $upload->maxSize = 5242880;
    $upload->exts = array('jpg', 'gif', 'png', 'jpeg');
    $upload->rootPath = './././Public/uploads/' . $file . "/";
    $upload->savePath = '';
    $upload->autoSub = false;
    $info = $upload->upload();
    if (!$info) {
        return false;
    }
    return $info;
}

/**
 * 数据分页
 * @param type $db
 * @param type $where
 * @param type $order
 * @param type $limit
 * @return type
 */
function poPage($db, $where, $order = "id desc", $limit = 10) {
    $count = $db->where($where)->count();
    $Page = new \Think\Page($count, $limit);
    $data["show"] = $Page->show();
    $setLimit = $Page->firstRow . ',' . $Page->listRows;
    $data["list"] = $db->where($where)->order($order)->limit($setLimit)->select();
    return $data;
}

/**
 * 数组分页
 * @param type $data
 * @param type $limit
 * @return type
 */
function arrPage($data, $limit = 10) {
    $count = count($data);
    $Page = new \Think\Page($count, $limit);
    $data["list"] = array_slice($data, $Page->firstRow, $Page->listRows);
    $data["show"] = $Page->show();
    return $data;
}

/**
 * dataTable数据分页
 * @param type $data
 * @return type
 */
function ongxData($data) {
    $ret = array();
    foreach ($data["columns"] as $key => $val) {
        if ($val["search"]["value"] != null && $val["data"] != "function") {
            $tmp = "%" . $val["search"]["value"] . "%";
            $ret["where"][$val["data"]] = array("like", $tmp);
        }
        if ($data["search"]["value"] != null && $val["data"] != "function") {
            $tol = "%" . $data["search"]["value"] . "%";
            $ret["where"][$val["data"]] = array("like", $tol);
        }
    }
    if ($ret["where"] != null) {
        $ret["where"]['_logic'] = 'or';
    }
    foreach ($data["order"] as $key => $val) {
        $tmp = $data["columns"][$val["column"]];
        $ret["order"] = $ret["order"] . "," . $tmp["data"] . " " . $val["dir"];
    }
    $ret["start"] = $data["start"] == 0 ? 1 : $data["start"] / $data["length"] + 1;
    $ret["order"] = trim($ret["order"], ",");
    $ret["limit"] = $data["length"];
    return $ret;
}

/**
 * XML转化为数组
 * @param type $xml
 * @return type
 */
function xmlToArray($xml) {
    libxml_disable_entity_loader(true);
    $xmlstring = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
    $val = json_decode(json_encode($xmlstring), true);
    return $val;
}

/**
 * 互易无限
 * @param type $xml
 * @return type
 */
function xml_to_array($xml) {
    $reg = "/<(\w+)[^>]*>([\\x00-\\xFF]*)<\\/\\1>/";
    if (preg_match_all($reg, $xml, $matches)) {
        $count = count($matches[0]);
        for ($i = 0; $i < $count; $i++) {
            $subxml = $matches[2][$i];
            $key = $matches[1][$i];
            if (preg_match($reg, $subxml)) {
                $arr[$key] = xml_to_array($subxml);
            } else {
                $arr[$key] = $subxml;
            }
        }
    }
    return $arr;
}

/**
 * 转义汉字
 * @param type $str
 * @return type
 */
function url_encode($str) {
    if (is_array($str)) {
        foreach ($str as $key => $value) {
            $str[urlencode($key)] = url_encode($value);
        }
    } else {
        $str = urlencode($str);
    }
    return $str;
}

/**
 * 解析汉字
 * @param type $str
 * @return type
 */
function phpescape($str) {
    $ret = '';
    $len = strlen($str);
    for ($i = 0; $i < $len; $i++) {
        if ($str[$i] == '%' && $str[$i + 1] == 'u') {
            $val = hexdec(substr($str, $i + 2, 4));
            if ($val < 0x7f)
                $ret .= chr($val);
            else if ($val < 0x800)
                $ret .= chr(0xc0 | ($val >> 6)) . chr(0x80 | ($val & 0x3f));
            else
                $ret .= chr(0xe0 | ($val >> 12)) . chr(0x80 | (($val >> 6) & 0x3f)) . chr(0x80 | ($val & 0x3f));
            $i += 5;
        }
        else if ($str[$i] == '%') {
            $ret .= urldecode(substr($str, $i, 3));
            $i += 2;
        } else
            $ret .= $str[$i];
    }
    return $ret;
}

/**
 * 清除空格
 * @param type $str
 * @return type
 */
function trimall($str) {
    $qian = array(" ", "　", "\t", "\n", "\r");
    $hou = array("", "", "", "", "");
    return str_replace($qian, $hou, $str);
}

/**
 * 获取当前页面URL
 * @return type
 */
function get_url() {
    $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
    $php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
    $path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
    $relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self . (isset($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : $path_info);
    return $sys_protocal . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '') . $relate_url;
}

/**
 * 经纬度距离
 * @param type $lng       经度  104.066164
 * @param type $lat       纬度  30.650085
 * @param type $distance  该点所在圆的半径，该圆与此正方形内切，默认值为0.5千米
 * @return type           正方形的四个点的经纬度坐标
 */
function returnSquarePoint($lng, $lat, $distance = 50) {
    $dlng = 2 * asin(sin($distance / (2 * 6370)) / cos(deg2rad($lat)));
    $dlng = rad2deg($dlng);
    $dlat = $distance / 6370;
    $dlat = rad2deg($dlat);
    $res = array(
        'left-top' => array('lat' => $lat + $dlat, 'lng' => $lng - $dlng),
        'right-top' => array('lat' => $lat + $dlat, 'lng' => $lng + $dlng),
        'left-bottom' => array('lat' => $lat - $dlat, 'lng' => $lng - $dlng),
        'right-bottom' => array('lat' => $lat - $dlat, 'lng' => $lng + $dlng)
    );
    return $res;
}

/**
 * 银行卡号省略
 * @param type $sn
 */
function bankCard($sn) {
    $pre = substr($sn, 0, 4);
    $after = substr($sn, -4);
    return $pre . " **** **** " . $after;
}

/**
 * 手机号省略
 * @param type $sn
 */
function phoneNumb($sn) {
    $pre = substr($sn, 0, 4);
    $after = substr($sn, -4);
    return $pre . "****" . $after;
}

/**
 * 获取系统编号
 */
function getSysOrder() {
    $tmp = explode(".", microtime(true));
    $length = mt_rand(1, 9) . $tmp[0] . sprintf("%04d", $tmp[1]);
    return $length . mt_rand(1, 9) . mt_rand(1, 9) . mt_rand(1, 9);
}

function SidType($sid,$userId){
    $account=M('account');
    $acc_nums=M('acc_nums');
	$where["recid"] = $userId;
    $where['level']=$sid;
    $list=$account->where($where)->select();
    //2018-8-20 16:09 add
    foreach ($list as $k=>$v){
        //进
        /*$where1['uid']= $v['id'];
        $where1['type']=array('lt',20);
        //进
        $where2['aboutid']= $v['id'];
        $where2['type']= array('gt',20);*/
        //出
//        $where3['uid']= $v['id'];
//        $where3['type']= array('gt',20);
//        //出
//        $where4['aboutid']= $v['id'];
//        $where4['type']= array('lt',20);
//        $insum1=$acc_nums->where($where1)->sum('nums');
//        $insum2=$acc_nums->where($where2)->sum('nums');
//        $list[$k]['innums']=$insum1+$insum2 ;
//        $outsum2 =$acc_nums->where($where3)->sum('nums');
//        $outsum1 =$acc_nums->where($where4)->sum('nums');
//        $list[$k]['outnums']=$outsum1+$outsum2;
        $list[$k]['innums']=$v['totalpoints'];
        $list[$k]['outnums']=$v['totalpoints']-$v['stock'];
        //获取购买量
        $buydata =getuserProductAndMoneyinfo($v['id']);
//            $info_arr['out_nums'] = $this->user['totalpoints']- $this->user['stock'];
        $list[$k]['level']=getUserLevel($v['level']);
        $list[$k]['out_nums']=$buydata['out_nums'];
        $list[$k]['reback_money']=$buydata['reback_money'];
        $list[$k]['salemoney']=$buydata['salemoney'];
    }
    return $list;
}


//添加获取利润的记录
/**
 * @param $money @直接记录获取返利的金额
 * @param $uid #@返利用户id
 * @param $sn #$订单号
 * @param $type 1利润  2返利利润 3 线下利润
 * @param $add_person @是否增加到个人 0否  1是
 * @return mixed
 */
function addProfitList($money, $uid, $sn, $type,$add_person=0){
    //利润记录表
    $order_pro =M('orders_pro');
    $account =M('account');
    $user_info=$account->where(['id'=>$uid])->find();
    $add=[
        'uid'=>$uid,
        'sn'=>$sn,
        'profit'=>$money,
        'time'=>time(),
        'type'=>$type,
        'level'=>$user_info['level'],
    ];
    $res =$order_pro->add($add);
    //添加个人利润总计
    if($add_person==1){
        $account->where(['id'=>$uid])->setInc('person',$money);
    }
    return $res;
}


/**统计获取-联创-团队管理奖,
 * @param $uid
 */
function getTopTeamMoney($uid,$level=6){
    //时间 --本月的
    $get_time = mktime(0, 0, 0, date('m'), 1, date('Y'));
    $end_time = mktime(23, 59, 59, date('m'), date('t'), date('Y'));
    //获取本人下面的团队联创
    $res_user =getmyTopTeam($uid,$level);
    $where['time'] =array('between',[$get_time,$end_time]);
    $team_num=0;
    //获取下面
    foreach ($res_user as $k =>$v){
        $nums_arr =getTeamMoney($v['id'],$where);
        $team_num +=$nums_arr['sum(nums)'];
    }

    //判断当前等级
    $now_user_Level=getUserInf($uid,'level');
    if($now_user_Level==6){
        //本人本月的进货量
        $res_user =getTeamMoney($uid,$where);
        //本人是否有能获得自己的积分 判断积分
        $res_sta=getEnoughtPoint($uid,$res_user['sum(nums)']);
    }
//    var_dump($res_user);
    //返回的非空
    if($res_sta){
        //有足够积分就加上自己的奖励
        $all_team_in =$team_num +$res_sta;
    }else{
        $all_team_in=$team_num;
    }
    //总进货量
//    var_dump($all_team_in);
    //根据获取到的进货量来计算获取利润
    $return_money =getTeamRatio($all_team_in);
    return ['money'=>$return_money,'team_in'=>$all_team_in];//返回利润
}


/**  add 2019年1月28日18:08:14
 * @param $uid
 * @param int $level
 */
function getTopTeamMoney_new($uid, $level=6){
    $acc_ratio =M('acc_teammoney');
    //时间 --本月的
    $get_time = mktime(0, 0, 0, date('m'), 1, date('Y'));
    $end_time = mktime(23, 59, 59, date('m'), date('t'), date('Y'));
    //上月
//    $get_time =  date("Y-m-d H:i:s",mktime(0, 0 , 0,date("m")-1,1,date("Y")));
//    $end_time =  date("Y-m-d H:i:s",mktime(23,59,59,date("m") ,0,date("Y")));

    //获取本人下面的团队联创
    $res_user =getmyTopTeam($uid,$level);
    $where['time'] =array('between',[$get_time,$end_time]);
    $team_num=0;
    $team_money=0;
    //获取下面
    foreach ($res_user as $k =>$v){
        //获取下面联创的个人进货量
        $res_arr =getTeamAllMoney_new($v['id'],$where);
        $team_num +=$res_arr['all_nums'];
        $team_money +=$res_arr['all_money'];
    }
    //判断当前等级
    $now_user_Level=getUserInf($uid,'level');
    if($now_user_Level==6){
        //本人本月的进货量
        $res_user =getTeamAllMoney_new($uid,$where,1);
        //本人是否有能获得自己的积分 判断积分
        $res_sta=getEnoughtPoint($uid,$res_user['all_nums']);

    }
    //返回的非空
    if($res_sta){
        //有足够积分就加上自己的奖励
        $all_team_in =$team_num +$res_sta;
        // $all_team_money =$team_money +$res_user['all_money'];
         $all_team_money =$team_money +($res_sta*55);
    }else{
        $all_team_in=$team_num;
        $all_team_money =$team_money ;
    }
    $where_ratio['inmoney']=array('elt',$all_team_money);
    $getratio =$acc_ratio->where($where_ratio)->order('id desc')->find();
    $getmoney=0;
    if($getratio){
        $getmoney =$all_team_money*$getratio['ratio'];
    }
    return ['money'=>$getmoney,'team_in'=>$all_team_in];//返回利润
}

/** 获取用户的本月进货量
 *
 * @param $uid
 * @return int
 */
function getUserMonthIn($uid){
    $get_time = mktime(0, 0, 0, date('m'), 1, date('Y'));
    $end_time = mktime(23, 59, 59, date('m'), date('t'), date('Y'));
    $where['time'] =array('between',[$get_time,$end_time]);
    $acc_num =M('acc_nums');
    $where['uid']=$uid;//进货人
    $where['type']=array('in','11,12,13');//进货类型
    $user_num =$acc_num->where($where)->field('sum(nums),uid')->find();
    //转进
    //联创没有转进
    $where1['aboutid']=$uid;//进货人
    $where1['time'] =array('between',[$get_time,$end_time]);
    $where1['type']=array('in','21');//进货类型
    $user_num2 =$acc_num->where($where1)->field('sum(nums),uid')->find();
    $nums =$user_num['sum(nums)']+$user_num2['sum(nums)'];
    $month=$nums==null?0:$nums;
    return $month;
}

function  getTeamNum(){

}


/** 导出数据表格的记录
 * @param $uname
 * @param $excel_tab
 * @return mixed
 */
function  addExcel_list($uname, $excel_tab){
    $add =[
        'excel_user'=>$uname,
        'excel_tab'=>$excel_tab,
        'time'=>time(),
    ];
    return M('excel_log')->add($add);

}

function setPlanToSend($uid,$sr_id='',$order_sn=''){
    $account =M('account');
    $order_sr =M('order_sr');
    $order_drs =M('order_drs');
    $acc_addr =M('acc_addr');

   //存在用户订单
    if($sr_id){
        $where['id'] =$sr_id;
    }else{
        $where['uid'] =$uid;
        $where['status'] =5;
        //在 3-1号 后的
        $where['times'] =array('gt',1551369600);
    }
    $order_info =$order_sr->where($where)->order('id desc')->find();
    $user_info =$account->where(['id'=>$uid])->find();
    $plan_nums =getplanNum($order_info['money']);
    //提货盒数
    $pick_num =$user_info['stock']>$plan_nums ?$plan_nums:$user_info['stock'];
    //获取提货地址
    $addr_where['uid'] =$uid;
    $addr_where['status'] =1;
    $addr_where['def'] =1;
    $addr_data =$acc_addr->where($addr_where)->find();
    //不存在地址的话
    //存在地址  -- 提货发货
    if($addr_data) {
        //添加提货发货的订单
        if(!$order_sn){
            $order_sn =getOrdSn();
        }
        $add_data = [
            'uid' => $uid,
            'name' =>$addr_data['name'],
            'phone' =>$addr_data['phone'],
            'address' =>$addr_data['street'],
            'nums' => $pick_num,
            'sn' =>$order_sn,
            'times' =>time(),
            'addr' =>$addr_data['id'],
            'have_pay' =>1,
            'status' =>1,
            'remakes' =>'金融申请成功后系统发货',
        ];
        $res =$order_drs->add($add_data);
        if($res){
            //扣除账户的库存
            $account->where(['id'=>$uid])->setDec('stock',$pick_num);
            return true;
        }
    }
    return false;
}

/** 根据申请的金额来判断获取的盒数
 * @param $money
 * @return int
 */
function getplanNum($money){
    switch ($money){
        case 3800: return 40 ;break;
        case 3000: return 40 ;break;
        case 1560: return 12 ;break;
        case 1200: return 12 ;break;
        case 600: return 4 ;break;
        case 480: return 4 ;break;
        case 168: return 1 ;break;
    }
}

/**获取出货用户的信息
 * @param $uid
 * @return bool
 */
function get_out_user($uid){
    $acc_num =M('acc_nums');
    $account =M('account');
    $order_sr =M('order_sr');
    //用户购买
    $where['uid'] =$uid;
    $where['type'] =12;
    $last_buy =$acc_num->where($where)->find();
    //申请人存在金融订单
    $sr_where['uid'] =$last_buy['aboutid'];
    $sr_where['status'] =array('in','5,7');
    $have_sr =$order_sr->where($sr_where)->find();
    if($have_sr){
        //账户库存小于0
        $user_info = $account->where(['id'=>$sr_where['uid']])->find();
        if($user_info['stock']<0){
            return false;
        }
    }
    return true;
}


//---------------------黄金活动部分---买4盒升黄金----add 2019-3-8 19:01:21-----------------------
function GoldActivity($sn){
    $now =time();
    $order =M('orders');
    $account =M('account');
    $acc_recode =M('acc_record');
    $order_info =$order->where(['sn'=>$sn])->field('uid,gnums')->find();
    $user_info=$account->where(['id'=>$order_info['uid']])->field('id,level')->find();
    //活动时间
    if(strtotime('2019-4-8 0:0:0')<$now && $now<strtotime('2019-4-14 24:00:00')){
        //进货量大于4
        if($order_info['gnums']>=12 && $user_info['level']<3){
            $res =changeUserLevel($user_info['id'],40,'黄金雨');
            return $res;
        }
    }

    //钻石雨 twice
    if(strtotime('2019-4-8 0:0:0')<$now && $now<strtotime('2019-4-14 24:00:00')){
        //升级记录
        $last_code =$acc_recode->where(['uid'=>$order_info['uid'],'after'=>3])->order('id desc')->find();
        if($last_code){
            if ($now - 10 <= $last_code['time']) {
                return false;
            }
            //进货量大于40 且为黄金
            if ($order_info['gnums'] >= 80 && $user_info['level'] = 3) {
                $res = changeUserLevel($user_info['id'], 532, '钻石雨');
                return $res;
            }
        }
    }
    //second
    if(strtotime('2019-4-15 0:0:0')<$now && $now<strtotime('2019-4-18 24:00:00')){
        //升级记录
        $last_code =$acc_recode->where(['uid'=>$order_info['uid'],'after'=>3])->order('id desc')->find();
        if($last_code){
            if ($now - 10 <= $last_code['time']) {
                return false;
            }
            //进货量大于40 且为黄金
            if ($order_info['gnums'] >= 120 && $user_info['level'] = 3) {
                $res = changeUserLevel($user_info['id'], 532, '钻石雨');
                return $res;
            }
        }
    }
}
//----------------------end------------------
////////////////////////////////////////////////////////////////////////////////

function getAccessToken($appId,$appSecret) {

// access_token 应该全局存储与更新，以下代码以写入到文件中做示例

    $data = json_decode(file_get_contents("access_token.json"));

    if ($data->expire_time < time()) {

        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appId&secret=$appSecret";

        $res = json_decode(httpGet($url));
        var_dump($res);exit;
        $access_token = $res->access_token;

        if ($access_token) {

            $data->expire_time = time() + 7000;

            $data->access_token = $access_token;

            $fp = fopen("access_token.json", "w");

            fwrite($fp, json_encode($data));

            fclose($fp);

        }

    }
    else {

        $access_token = $data->access_token;

    }
    return $access_token;
}

function httpGet($url) {

    $curl = curl_init();

    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($curl, CURLOPT_TIMEOUT, 500);

    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

    curl_setopt($curl, CURLOPT_URL, $url);

    $res = curl_exec($curl);

    curl_close($curl);

    return $res;

}
//查询有没有付款给用户
function money_code($sn,$data){
    $money_code =M('money_code');
    $array=explode('.', $sn);
    // $where["drop_id"] = array("like", "%" . $sn . "%");
    $where["drop_id"] = $array[0];
    $code =$money_code->where($where)->field($data)->find();
    return $code[$data];
}
//查询有没有付款给用户
function back_type($type){
   if($type==1){
        return '微信零钱';  
   }elseif($type==2){
       return '银行卡';
   }
   else{
       return '';
   }


}
//
function money_time($time,$type){
    if($type==1){
        return date('Y-m-d',money_code($time['sn'],'time'));
    }elseif ($type==2){
        return '被驳回';
    }elseif($type==0){
        return '审核中';
    }

}
require 'local.php';
