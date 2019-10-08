<?php

/**
 * 输出json
 * @param type $status
 * @param type $msg
 * @param type $data
 */
function get_op_put($status, $msg, $data = null,$usl=null) {
    $array = array(
        "status" => $status,
        "msg" => $msg,
        "data" => $data,
        "url" => $usl
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
    $upload->rootPath = 'Public/uploads/' . $file . "/";
    $upload->savePath = '';
    $upload->autoSub = false;
    $info = $upload->upload();
    if (!$info) {
        return false;
    }
    return $info;
}

//图像上传
function uploadify(){
    if (!empty($_FILES)) {
        //图片上传设置
        $config = array(
            'maxSize'    =>    3145728,
            'savePath'   =>    '',
            'saveName'   =>    array('uniqid',''),
            'exts'       =>    array('jpg', 'gif', 'png', 'jpeg'),
            'autoSub'    =>    true,
            'subName'    =>    array('date','Ymd'),
        );

        $upload = new \Think\Upload($config);// 实例化上传类
        $upload->rootPath = './Public/uploads/imgDbs/' ;
        mkdir('Public/uploads/imgDbs');
        $images = $upload->upload();
        //判断是否有图
        if($images){
            $data =[];
            foreach($images as $k=>$v){
                $info='Public/uploads/imgDbs/'.$v['savepath'].$v['savename'];
//                $miinfo='Public/uploads/imgDbs/'.$v['savepath'].'mi_'.$v['savename'];
//                $image = new \Think\Image();
                    //生成缩略图
//                $image->open('./'.$info)->thumb(150, 150,\Think\Image::IMAGE_THUMB_SCALE)->save('./'.$miinfo);
                //添加图片水印
//                $image->open('./'.$info)->water('./public/static/imgs/xttg.png',\Think\Image::IMAGE_WATER_SOUTHEAST,40)->save('./'.$info);
                //添加文字水印
                //$image->open('./'.$info)->text('姜医生','./Data/1.ttf',20,'#000000',\Think\Image::IMAGE_WATER_SOUTHEAST)->save($info);
                $data[$k]['yt']= $info;
//                $data[$k]['mi']= $miinfo;
            }
//            $this->ajaxReturn($data);
            return $data;


        }
        else{
//            $this->error($upload->getError());//获取失败信息
            return $upload->getError();
        }
    }
}

/** 多图上传
 * @param $data 用于装数据
 * @param $file file信息
 * @return mixed
 */
function uploaldPic($data,$file){
    $upload = new \Think\Upload();// 实例化上传类
    $upload->maxSize   =     5242880 ;// 设置附件上传大小
    $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
    $upload->rootPath = './././Public/uploads/img/' ;
    $upload->savePath  =     ''; // 设置附件上传（子）目录
    $upload->thumb              = true;
    // 设置引用图片类库包路径
    $upload->imageClassPath     = '@.ORG.Image';
    //设置需要生成缩略图的文件后缀
    $upload->thumbPrefix        = 'm_';
    //设置缩略图最大宽度
    $upload->thumbMaxWidth      = '200,100';
    //设置缩略图最大高度
    $upload->thumbMaxHeight     = '200,100';
    //设置上传文件规则
    $upload->saveRule           = 'uniqid';
    $upload->replace           = true;
    //删除原图
    $upload->thumbRemoveOrigin  = true;
    // 上传文件
    $info=array();
    $a = '';
    //  var_dump($file);exit;
    //通过遍历把刚刚存入的图片。依次拼成图片路径，你们可以通过var_dump去查看输去内容
    foreach ($file as $key=>$value){
        $file1=array();
        $file1["intro_pic"]['name']=$value;
        $file1["intro_pic"]['type']=$file['intro_pic']["type"][$key];
        $file1["intro_pic"]['tmp_name']=$file['intro_pic']["tmp_name"][$key];
        $file1["intro_pic"]['error']=$file['intro_pic']["error"][$key];
        $file1["intro_pic"]['size']=$file['intro_pic']["size"][$key];
        $info   =   $upload->upload($file1);
        foreach ($info as $key=>$value)
        {
            $a.=",".$value['savepath'].$value['savename'];//我用符号把图片路径拼起来
        }
    }
    //把第一个,去掉，同时写进data数据库里面的intro_pic字段
    //  $data["intro_pic"]=substr($a,1);
    $info1   =  $upload->upload();
    foreach ($info1 as $key=>$value)
    {
        $data["$key"]=$value['savepath'].$value['savename'];
    }
    return $data;
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

function SidType($sid,$userId,$self){
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
        if($userId !=$self){
            unset($list[$k]['phone']);
        }
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

/*0元计划购买发货
 * @param $uid
 * @param string $sr_id
 * @param string $order_sn
 * @return bool
 */
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
function GoldActivity($sn ,$pay_type=null){
    $now =time();
    $order =M('orders');
    $account =M('account');
    $acc_recode =M('acc_record');
    $order_info =$order->where(['sn'=>$sn])->field('uid,gnums')->find();
    $user_info=$account->where(['id'=>$order_info['uid']])->field('id,level')->find();
    //活动时间
    if(strtotime('2019-5-20 12:0:0')<$now && $now<strtotime('2019-6-1 24:00:00') || $pay_type==1){
        //进货量大于4
        if($order_info['gnums']>=4 && $user_info['level']<3){
            $res =changeUserLevel($user_info['id'],40,'黄金雨');
            return $res;
        }
    }

    //钻石雨 twice
    if(strtotime('2019-4-8 0:0:0')<$now && $now<strtotime('2019-4-14 24:00:00') || $pay_type==1){
        //升级记录
        $last_code =$acc_recode->where(['uid'=>$order_info['uid'],'after'=>3])->order('id desc')->find();
        if($last_code){
            if ($now - 10 <= $last_code['time']) {
                return false;
            }
            //进货量大于40 且为黄金
            if ($order_info['gnums'] >= 40 && $user_info['level'] = 3) {
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

//-----------设置用户购买便发货的方法--start--

/** 获取我的上级是否设置了购买发货的团队
 * @param $sysid
 * @return bool
 */
function IsHaveSendParent($sysid){
    $account =M('account');
    $user_recid =$account->where(['sysid'=>$sysid])->find();
    if(!$user_recid){
        return false;
    }
    //获取购买发货
    if($user_recid['buy_send']==1){
        return true;
    }
    if($sysid ==$user_recid['recid'] ||$user_recid['recid'] ==0 ||$user_recid['recid'] =='null'){
        return false;
    }
    return IsHaveSendParent($user_recid['recid']);

}

/**购买便发货
 * @param $buy_id 购买人id
 * @param null $order_sn 订单编号
 * @return bool
 */
function buy_send($buy_id,$order_sn =null){
    $account =M('account');
    $acc_addr =M('acc_addr');
    $orders =M('orders');
    $order_drs =M('order_drs');
    $trans_price =M('trans_price');
    $acc_nums =M('acc_nums');

    $where['uid'] =$buy_id;
    //存在用户订单
    $order_info =$orders->where($where)->order('id desc')->find();
    $user_info =$account->where(['id'=>$buy_id])->find();
    //提货盒数
    $pick_num =$order_info['gnums'];
//    $pick_num =$user_info['stock']>$plan_nums ?$plan_nums:$user_info['stock'];
    //获取提货地址
    $addr_where['id'] =$order_info['addr'];
    $addr_data =$acc_addr->where($addr_where)->find();
    M()->startTrans();
    //不存在地址的话
    //存在地址  -- 提货发货
    #
    //扣除账户的库存
    $account->where(['id'=>$buy_id])->setDec('stock',$pick_num);
    //实际支付运费数量
    $real_pay_num= $pick_num%40;
    $resmoney = $trans_price->where($where)->select();
    $is_out=[];
    if ($resmoney) {
        foreach ($resmoney as $k => $v) {
            $is_out[] = $v['isout'];
        }
    }
    $where_place['id'] = array('in', implode(',', $is_out));
    //扣除出货人的应收金额
    $price =getTranbynums($addr_data['street'],$real_pay_num,$where_place);
    #
    if($addr_data) {
        //添加提货发货的订单
        $send_sn =getOrdSn();
        $add_data = [
            'uid' => $buy_id,
            'name' =>$addr_data['name'],
            'phone' =>$addr_data['phone'],
            'address' =>$addr_data['street'],
            'nums' => $pick_num,
            'sn' =>$send_sn,
            'times' =>time(),
            'addr' =>$addr_data['id'],
            'have_pay' =>1,
            'trans_pay' =>$price,
            'status' =>1,
            'remakes' =>'团队申请购买成功后直接发货',
        ];
        $res =$order_drs->add($add_data);
        if($res){
            //获取购买出货人
            $about_id=$acc_nums ->where(['sn'=>$order_sn])->find();
            //扣除出货人的支付运费金额
            cgUserMoney($about_id["aboutid"], $price, 0, "paytran", $send_sn);
            M()->commit();
            return true;
        }
    }
    return false;
}


//-----end----
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
    if($code==null){
        $code =$money_code->where(['drop_id'=>$sn])->field($data)->find();
    }
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
/**
 * 计算用户被冻结的金额
 * @param $uid 用户id
 * @return string 被冻结的金额
 */
function accountFrozen($uid){
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
}

//状态 时间
function money_time($time,$type){
    if($type==1){
        return date('Y-m-d',money_code($time['sn'],'time'));
    }elseif ($type==2){
        return '被驳回';
    }elseif($type==0){
        return '审核中';
    }

}
//----------后台添加优惠---start----------------
/**获取优惠的数量和价格
 * @param $uid
 * @return mixed
 */
function getDiscount($uid){
//    $stage =M('acc_stages');
    $discount =M('acc_st_discount');
    $time =date('Y-m-d',time());
    $where['uid'] =$uid;
    $where['start_time'] =array('lt',$time);
    $where['end_time'] =array('gt',$time);
    $where['discount_num'] =array('gt',0);
    //分期记录
//    $getstage_list =$stage->where($where)->select();
    //优惠记录
    $getstage_list =$discount->where($where)->order('discount_var desc')->select();
    return $getstage_list;
}

/**
 *获取订单详情
 */
function getorders_info($sn){
    $order =M('orders');
    //查询订单信息
    $order_info =$order->where(['sn'=>$sn])->find();
    return $order_info;
}
/**进行优惠 --运行
 * @param $sn 订单编号
 * @return null
 */
function doDiscount($sn){
    //订单信息
    $order_info =getorders_info($sn);
    //获取上面人的信息
    $top_user =gettop(getUserInf($order_info['uid'],'sysid'),5);
    if($top_user){
        //获取优惠
        $discount =getDiscount($top_user['id']);
        //找到上面联创都没有获取到优惠 --失败
        if(!$discount &&$top_user['level'] ==6){
            return null;
        }
        //当返回上面的是钻石
        //获取联创
        $top_user =gettop(getUserInf($order_info['uid'],'sysid'),6);
        //获取优惠
        $discount =getDiscount($top_user['id']);
        //找到上面联创都没有获取到优惠 --失败
        if(!$discount &&$top_user['level'] ==6){
            return null;
        }
        $res=array();
        //循环获取优惠
        foreach($discount as  $k=>$v){
            $res=doActivity($discount[$k],$order_info);
        }
        return $res;
    }else{
        return null;
    }


}
/**执行活动操作
 * @param $uid 申请人
 * @param $st_data 优惠
 * @param $order_info 订单
 * @return bool
 */
function doActivity($st_data,$order_info){
    $now =time();
    $account =M('account');
//    $order_info =$order->where(['sn'=>$sn])->field('uid,gnums')->find();
    $user_info=$account->where(['id'=>$order_info['uid']])->field('id,level')->find();
    //活动时间
    if(strtotime($st_data['start_time'])<$now && $now<strtotime($st_data['end_time'])){
        //进货的价格在之上 且 等级小于
//        if($order_info['money']>=$st_data['discount_var'] &&$user_info['level']<$st_data['change_level']){
        if($order_info['gnums']>=$st_data['discount_buy_num'] &&$user_info['level']<$st_data['change_level']){
            $level_nums = M('acc_level')->where(['id'=>$st_data['change_level']])->find();
            $top_user_info =$account->where(['id'=>$st_data['uid']])->find();
            $res =changeUserLevel($order_info['uid'],$level_nums['months'],$top_user_info['name'].$st_data['discount_name']);

            //优惠量扣除
            $res =delDisCount($st_data['id']);
            return $res;
        }
    }
    return null;
}

/**
 *减少这个活动的优惠量
 */
function delDisCount($st_id){
    $st_discount =M('acc_st_discount');
    $discount=$st_discount->where(['id'=>$st_id])->setDec('discount_num');
    return $discount;
}
/**
 * 用户团队搜索
 * @param $data //用于储存数据
 * @param $channels  //数据
 * @param $wheres //条件
 * @return array
 */
function allSum(&$data,$channels,$wheres){
    $account =M('account');
    $arr = array_column($channels, 'sysid');
    $comma = implode(",", $arr);
    $where['recid'] = array('in',$comma);

    $arrs=$account->where($where)->select();
    if($wheres==null){
        $name=$account->where($where)->select();
    }else{
        $name=$account->where($where)->where($wheres)->select();
    }
    if($arrs!=null){
        foreach($name as $v){
            $data[]=$v;
        }
        return allSum($data,$arrs,$wheres);
    }else{
        return $data;
    }
}
//----------------end-------------




/**
 *使用户购买后不升级
 */
function outUser($uid){
    if($uid ==4700){
        M('account')->where(['id'=>$uid])->save(['level'=>0]);
    }
}
function yc_phone($str)
{
    $resstr = substr_replace($str, '****', 3, 4);
    return $resstr;
}
//510222196308102123
function yc_idcard($str)
{
    $resstr = substr_replace($str, '********', 6, 8);
    return $resstr;
}

/**
 *获取等级的图标
 */
function get_levelIcon($level){
    switch ($level){
        case 0: return '&#xe648;';break;
        case 1: return '&#xe649;';break;
        case 2: return '&#xe64a;';break;
        case 3: return '&#xe64b;';break;
        case 5: return '&#xe64c;';break;
        case 6: return '&#xe64d;';break;

    }
}


/**获取用户等级的背景色
 * @param $level
 * @return string
 */
function getLevelBgcolor($level){
    switch ($level){
        case 0: return 'visitor';break;
        case 1: return 'week';break;
        case 2: return 'month';break;
        case 3: return 'golden';break;
        case 5: return 'diamond';break;
        case 6: return 'founder';break;

    }
}

/**获取下面用户逾期
 * @param $uid
 */
function haveSrOut($uid){
    $acc_num =M('acc_nums');
    $order_sr_where['status'] =array('in','5,7');
    $order_sr_data =M('order_sr')->where($order_sr_where)->select();
    $sn_strr =implode(',',array_column($order_sr_data,'sn'));
    $nums_where['sn'] =array('in',$sn_strr);
    $nums_data =$acc_num->where($nums_where)->select();
    $res =in_array($uid,array_column($nums_data,'aboutid'));
    if(!$res){
        return true;
    }
    return false;
}
//---------------------------

/**
 * 逾期用户转库存
 * @param $data //用于储存数据
 * @param $channels  //数据
 * @param $wheres //条件
 * @return array
 */
function order_sr_pick($uid,$count){
    $acc_num =M('acc_nums');
    $account =M('account');
    $order_sr_where['type'] ='21';
    $order_sr_where['uid'] =$uid;//aboutid
    $order_sr_data =$acc_num->where($order_sr_where)->order('time desc')->find();
    $user_count =$account->where(['id'=>$order_sr_data['aboutid']])->find();
    $order_sr_where2['uid'] =$order_sr_data['aboutid'];
    $order_sr =$acc_num->where(['type'=>'21'])->where($order_sr_where2)->order('time desc')->find();
    $order_sr_pick['type'] ='23';
    $order_sr_pick['uid'] =$user_count['id'];
    $data =$acc_num->where($order_sr_pick)->find();
    //var_dump($order_sr_data,$user_count);exit;
    if($data==null && $order_sr!=null){
         $count.=$user_count['name'].'->';
        return order_sr_pick($order_sr_data['aboutid'],$count);
    }else{
        $user_count =$account->where(['id'=>$order_sr_data['aboutid']])->find();
        $datas['pick'] =$acc_num->where($order_sr_pick)->sum("nums");
        $datas['pick']=$datas['pick'] ==null?'':$datas['pick'];
        $count.=$user_count['name'];
        $datas['tname']=$count ==null?'':$count;
        $datas['stocks']=$user_count['stock'] ==null?'':$user_count['stock'];
        $datas['times']=$order_sr_data ==null?'':date('Y-m-d H:i:s',$order_sr_data['time']);
      // var_dump($datas);exit;
        return $datas;
    }
}
//------------------stages-status------

/** 分期的状态
 * @param $status
 * @return string
 */
function stageStatus($status){
    switch($status){
        case 0: return '未确认'; break;
        case 1: return '分期中'; break;
        case 2: return '已完成'; break;
        case 3: return '已取消'; break;
//        case 4: return ''; break;
    }
}

/**
 * 用户转库存
 * @param $uid //用户id
 * @return array
 */
function order_pick($uid,$arr){
    $acc_num =M('acc_nums');
    $order_sr_where['type'] ='21';
    $pieces = explode(",", $uid);
    if($pieces<=1 || $pieces==null){
        $order_sr_where['uid'] =$uid;//aboutid
    }else{
        $uid=implode(',',$pieces);
        $order_sr_where['uid'] =array('in',$uid);//aboutid
    }
    $order_sr_data =$acc_num->where($order_sr_where)->select();//这个人所有的
    $arrs=second_array_unique_bykey($order_sr_data,'aboutid');
    // var_dump($arr);exit;
    foreach ($arrs as $k=>$v){
        $arr[]=$v;
    }
    //他下面有没有
    $order_srid=array_column($order_sr_data,'aboutid');
    if($order_srid==null){
        $order_srid=implode(",",$order_srid);
        $order_sr_where2['uid'] =$order_srid;//aboutid
        $order_sr ='';

    }else{
        $order_srid=implode(",",$order_srid);
        $order_sr_where2['uid'] =array('in',$order_srid);//aboutid
        $order_sr =$acc_num->where(['type'=>'21'])->where($order_sr_where2)->select();
    }
    $arrs1=second_array_unique_bykey($order_sr,'aboutid');
    foreach ($arrs1 as $k=>$v){
        $arr[]=$v;
    }
    $order_srid2=array_column($order_sr,'aboutid');
    $orderuid=implode(",",$order_srid2);
    if($order_sr!=null){
        return order_pick($orderuid,$arr);
    }else{
        //  $datas['times']=$order_sr_data ==null?'':date('Y-m-d H:i:s',$order_sr_data['time']);
        return $arr;
    }
}
function second_array_unique_bykey($arr, $key){
    $tmp_arr = array();
    foreach($arr as $v){
        if(!isset($tmp_arr[$v['aboutid']])) $tmp_arr[$v['aboutid']] = $v;
        else $tmp_arr[$v['aboutid']]['nums'] += $v['nums'];
    }

    return $tmp_arr;

}
function tocurl($url, $header, $content)
{
    $ch = curl_init();
    if (substr($url, 0, 5) == 'https') {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);  // 从证书中检查SSL加密算法是否存在
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($content));
    $response = curl_exec($ch);
    if ($error = curl_error($ch)) {
        die($error);
    }
    curl_close($ch);
    return $response;
}
//原生分页
/**
 * @param $arr 二维数组
 * @param $p  第几页
 * @param $count 每页多少
 * @return mixed
 */
function arr_page($arr,$p,$count){
    if(empty($p)){
        $p=1;
    }
    if(empty($count)){
        $count=2;
    }
    $stary=($p-1)*$count;
    for($i=$stary;$i<$stary+$count;$i++){
        if(!empty($arr[$i])){
            $new_arr[$i]=$arr[$i];
        }
    }
    return $new_arr;
}
function multidimensional_search($parents, $searched) {
    if (empty($searched) || empty($parents)) {
        return false;
    }

    foreach ($parents as $key => $value) {
        $exists = true;
        foreach ($searched as $skey => $svalue) {
            $exists = ($exists && IsSet($parents[$key][$skey]) && $parents[$key][$skey] == $svalue);
        }
        if($exists){ return $key; }
    }


    return false;
}


/** 获取团队人数
 * @param $uid
 * @param $where
 * @return int
 */
function TeamNewAddNum($uid,$where){
    $account =M('account');
    $user =$account->where($where)->select();
    $nums=0;
    foreach($user as $k=>$v){
        $res =isMyTeam($uid,$v['sysid']);
        if($res ==1){
            $nums++;
        }
    }
    return $nums;
}
/**判断是否是我的团队里的
 * @param $usysid 父级sysid
 * @param $child 子集sysid
 */
function isMyTeam($usysid,$child){
    $account =M('account');
    $user =$account->where(['sysid'=>$child])->find();
    //没有上级
    if($user['recid'] ==0||$user['recid'] ==' '||$user['recid'] ==null){
        return 0;
    }
    if($user!=$usysid){
        isWhoTeam($usysid,$user['sysid']);
    }else{
        return 1;
    }
}
function upload_image($url){
    //  $url = 'http://akk028wkf.oss-cn-beijing.aliyuncs.com/auth/20190522/MWSC-448158_full.png';
    $curl = curl_init($url);
    curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
    $imageData = curl_exec($curl);
    curl_close($curl);
    $imgname=basename($url);
    $tp = fopen( "./././Public/uploads/img/".$imgname,"a");
    fwrite($tp, $imageData);
    fclose($tp);
    return "/Public/uploads/img/".$imgname;
}

/**用户下逾期人数
 * @param $uid
 */
function getUserOutTime($uid){
    $acc_nums =M('acc_nums');
    $order_sr =M('order_sr');
    $where['aboutid']=$uid;
    $where['type']=11;
    $nums_data =$acc_nums->where($where)->select();
    //该用户已逾期的数据
    $sr_where['sn']=array('in',implode(',',array_column($nums_data,'sn')));
    $sr_where['status'] = 7;
    $res_data =$order_sr->where($sr_where)->select();
    return $res_data;
}
//获取地址
function get_area($ip = ''){
    if($ip == ''){
        $ip = GetIp();
    }
    $url = "http://ip.taobao.com/service/getIpInfo.php?ip={$ip}";
    $ret = https_request($url);
    $arr = json_decode($ret,true);
    return $arr;
}
//POST请求函数
function https_request($url,$data = null){
    $curl = curl_init();

    curl_setopt($curl,CURLOPT_URL,$url);
    curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,false);
    curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,false);

    if(!empty($data)){//如果有数据传入数据
        curl_setopt($curl,CURLOPT_POST,1);//CURLOPT_POST 模拟post请求
        curl_setopt($curl,CURLOPT_POSTFIELDS,$data);//传入数据
    }

    curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);
    $output = curl_exec($curl);
    curl_close($curl);

    return $output;
}
// 获取ip
function GetIp(){
    $realip = '';
    $unknown = 'unknown';
    if (isset($_SERVER)){
        if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']) && strcasecmp($_SERVER['HTTP_X_FORWARDED_FOR'], $unknown)){
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            foreach($arr as $ip){
                $ip = trim($ip);
                if ($ip != 'unknown'){
                    $realip = $ip;
                    break;
                }
            }
        }else if(isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP']) && strcasecmp($_SERVER['HTTP_CLIENT_IP'], $unknown)){
            $realip = $_SERVER['HTTP_CLIENT_IP'];
        }else if(isset($_SERVER['REMOTE_ADDR']) && !empty($_SERVER['REMOTE_ADDR']) && strcasecmp($_SERVER['REMOTE_ADDR'], $unknown)){
            $realip = $_SERVER['REMOTE_ADDR'];
        }else{
            $realip = $unknown;
        }
    }else{
        if(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), $unknown)){
            $realip = getenv("HTTP_X_FORWARDED_FOR");
        }else if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), $unknown)){
            $realip = getenv("HTTP_CLIENT_IP");
        }else if(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), $unknown)){
            $realip = getenv("REMOTE_ADDR");
        }else{
            $realip = $unknown;
        }
    }
    $realip = preg_match("/[\d\.]{7,15}/", $realip, $matches) ? $matches[0] : $unknown;
    return $realip;
}

//---------------------end------------
require 'local.php';

