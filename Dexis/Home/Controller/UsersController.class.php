<?php

namespace Home\Controller;

#use Think\Controller;

class UsersController extends CommController {

    public function _initialize() {
        parent::_initialize();
        layout("laynot");
        #
        $ssid = session("ssid_dexis");
        if ($ssid == null && CONTROLLER_NAME != "Login") {
            $this->redirect("Login/index");
        }
    }

    /**
     * 个人中心
     */
    public function index() {
        layout("layout");
        #
        $level = M("acc_level");
        $orders = M("orders");
        $account = M("account");
        $where["uid"] = $this->ssid;
        $where["status"] = 1;
        $count = $orders->where($where)->sum("gnums");
        //获取用户信息
        $info =$account->where(['id'=>$this->ssid])->find();
        $upuser=$account->where(['sysid'=>$info['recid']])->find();
        //会员成长值
        $lever_where["id"] = array('gt',$info['level']);
        $reclevel=$level->where($lever_where)->find();
        //减去还差多少分
        if($reclevel!=null){
            $reclevel['cat']=bcsub($reclevel['months'],$info['totalpoints']);
            $reclevel['loader']=floor($info['totalpoints']/$reclevel['months']*100);
        }else{
            $reclevel['cat']=0;
            $reclevel['loader']=100;
        }
        //等级的图表
        $icon =get_levelIcon($info['level']);
        $this->assign("icon", $icon);
        //等级的背景色
        $bg_class=getLevelBgcolor($info['level']);
        $this->assign("class", $bg_class);

       // $reclevel['loader']=round($reclevel['loader']);
        #
        $info_arr =getUserInfo($where["uid"]/*442*/);
//        $info_arr['out_nums'] = $this->user['totalpoints']- $this->user['stock'];
        //获取团队的人数
//        $res_data =getChilds($html,$info['sysid'],$all_account,0);
//        $this->assign("team", count($res_data));
        $this->assign("reclevel", $reclevel);
        $this->assign("count", $count);
        $this->assign("info", $info);
        $this->assign("saleinfo", $info_arr);
        $this->assign("user", $this->user);
        $this->assign("parent", $upuser);
        $this->display();
    }

    /**
     * 地址列表
     */
    public function addr($gsn = null, $nums = null) {
        $acc_addr = M("acc_addr");
        $view =I('view');
        if($view){
            session('turnView',$view);
            $this->assign("view", $view);
        }
        #
        $where["uid"] = $this->ssid;
        $where["status"] =1;
        $list = $acc_addr->where($where)->order("id desc")->select();
        #
        $this->assign("list", $list);
        $this->assign("sn", $gsn);
        $this->assign("nums", $nums);
        $this->display();
    }

    /**
     * 地址列表
     */
    public function addr_add() {
        $get = I("get.");
        $this->assign("uid", $this->ssid);
        $this->assign("get", $get);
        //获取跳转页 然后删除
        $view =session('turnView');
        if($view){
            $this->assign("view", $view);
            session('turnView',null);
        }
        $this->display();
    }

    public function addr_edit($id) {
        $acc_addr = M("acc_addr");
        #
        $where["id"] = $id;
        $info = $acc_addr->where($where)->find();
        $address =explode(',',$info['street']);
//        $getaddress =strstr(end($address),'县')==false?strstr(end($address),'区'):strstr(end($address),'县');
        $res_add =strstr($info['street'],end($address),true);
        $info['pro'] =substr($res_add,0,-1);
        $info['city'] =end($address);
        $this->assign("info", $info);
        //获取跳转页 然后删除
        $view =session('turnView');
        if($view){
            $this->assign("view", $view);
            session('turnView',null);
        }
        $this->display();
    }

    public function dels(){
        $acc_addr = M("acc_addr");
        #
        $data =I('post.');
        $res =$acc_addr->where(['id'=>$data['id']])->save(['status'=>$data['status']]);
        $arr['status'] =0;
        if($res){
            $arr['status'] =1;
        }
        $this->ajaxReturn($arr);
    }

    /**
     * 实名验证
     */
    public function idauth() {
        $acc_idauth = D("acc_idauth");
        #
        $where["uid"] = $this->ssid;
        $info = $acc_idauth->where($where)->find();
        $this->assign("info", $info);
        $this->assign("uid", $this->ssid);
        //绑定地址
        $acc_addr=M('acc_addr');
        $where["status"] =1;
        $where["def"] =1;
        $res_addr =$acc_addr->where($where)->find();
        $url ='Users/idauth';
        session('turnView','idauth');
        if(!$res_addr){
            $url ="Users/addr";
        }
        $this->display($url);
    }
    /**
     * 实名验证
     */
    public function idcode() {
        $acc_idauth = D("acc_idauth");
        #
        $type=I('type');
        $where["uid"] = $this->ssid;
        $info = $acc_idauth->where($where)->find();
        $info['type']=$type;
        //$this->assign("type", $type);
        $this->assign("info", $info);
        $this->assign("uid", $this->ssid);
        //绑定地址
        $url ='Users/idauth';
        session('turnView','idauth');
        $this->display($url);
    }
    /**
     * 实名认证管理
     */
    public function idauth_op() {
        $acc_idauth = D("acc_idauth");
        $post = I("post.");
        //var_dump($post);exit;
        #
        $where["uid"] = $this->ssid;
        $count = $acc_idauth->where($where)->count();
        $type = $count > 0 ? 2 : 1;
        if (!$acc_idauth->create($post, $type)) {
            return get_op_put(0, $acc_idauth->getError());
        }
        if($post['type']==3){
            $url="http://icard.casc168.com/pay/dis2/?providerId=1904083154&mobile=".$post["phone"]."&realName=".$post['name']."&idCard=".$post["idcard"];
        }elseif($post['type']==6){
            $url=U('index/financial_card');
        }else{
            $url =U('users/index');
        }

       /* //绑定地址+
        $acc_addr=M('acc_addr');
        $res_addr =$acc_addr->where($where)->find();
        if($res_addr){
            $url =U('users/addr');
        }*/
        #
        if ($count > 0) {
            if (!$acc_idauth->where($where)->save()) {
                return get_op_put(0, "没有修改");
            }
            return get_op_put(1, "修改成功",$url);
        }
        #
        if (!$acc_idauth->add()) {
            return get_op_put(0, "添加失败");
        }

        return get_op_put(1, "添加成功",$url);
    }

    ////////////////////////////////////////////////////////////////////////////

    public function bank() {
        $acc_bank = M("acc_bank");
        #
        $where["uid"] = $this->ssid;
        $list = $acc_bank->where($where)->order("id desc")->select();
        #
        $this->assign("list", $list);
        $this->display();
    }

    public function bank_add() {
        $sysbank = M("sysbank");
        #
        $where["status"] = 1;
        $list = $sysbank->where($where)->select();
        #
        $this->assign("list", $list);
        $this->assign("uid", $this->ssid);
        $this->display();
    }
	
    public function bank_edit($id) {
        $acc_bank = M("acc_bank");
        $sysbank = M("sysbank");
        #
        $where["id"] = $id;
        $info = $acc_bank->where($where)->find();
        $this->assign("info", $info);
        #
        $cond["status"] = 1;
        $list = $sysbank->where($cond)->select();
        $this->assign("list", $list);
        $this->display();
    }

    ////////////////////////////////////////////////////////////////////////////

    public function plans() {
        layout("layout");
        #
        $order_sr = M("order_sr");
        #
        $where["uid"] = $this->ssid;

        $where["status"] = array('neq',0);
        $list = $order_sr->where($where)->select();
        foreach ($list as $k => $v) {
            $tmp = getGoodsInfo($v["gsn"]);
            $list[$k]["gimgs"] = $tmp["imgs"];
            $list[$k]["gid"] = $tmp["id"];
            $list[$k]["gname"] = $tmp["name"];
        }
        $this->assign("list", $list);
        #
        $this->display();
    }

    /**
     * 0元计划-申请
     */
    public function plans_op() {
        $acc_idauth = M("acc_idauth");
        $order_sr = M("order_sr");
        $post = I("post.");
        #
        $goods = getIDGoodsInfo($post["gsn"]);
        $where["uid"] = $this->ssid;
        $info = $acc_idauth->where($where)->find();
        if ($info == null) {
            return get_op_put(0, "请完善您的实名信息后再次申请",U('Users/idauth'));
        }
        #
        $put["uid"] = $this->ssid;
        $put["sn"] = getSysOrder();
        $put["gsn"] = $goods["gsn"];
        $put["name"] = $info["name"];
        $put["idcard"] = $info["idcard"];
        $put["phone"] = $info["phone"];
        $put["money"] = $goods["price"];
        $put["status"] = 0;
        $put["times"] = time();
        #
        if (!$order_sr->add($put)) {
            return get_op_put(0, "网络异常[0X001]");
        }
        return $this->plans_op_check($put);
    }

    /**
     * 0元计划-核验
     * @param type $put
     * @return type
     */
    private function plans_op_check($put) {
        $params = array();
        $params["params"]["orderNo"] = $put["sn"];
        $params["params"]["name"] = $put["name"];
        $params["params"]["certNo"] = $put["idcard"];
        $params["params"]["phone"] = $put["phone"];
        $params["params"]["prodNo"] = $put["gsn"];
        $params["params"]["amt"] = $put["money"];
        #
        $data = json_encode($params);
        $output = jsonCurl(C("PUM_CONF")["LINKS"], $data);
        $tmp = json_decode($output, true);
        if ($tmp["meta"]["code"] != "0000") {
            return get_op_put(0, $tmp["meta"]["message"]);
        }
        if ($tmp["data"]["status"] != "0") {
            return get_op_put(0, $tmp["data"]["desc"]);
        }
        return get_op_put(1, "success", $tmp["data"]["url"]);
    }

    ////////////////////////////////////////////////////////////////////////////
    public function havesrout(){
        //团队存在金融订单未处理的情况
        $upuser=haveSrOut($this->ssid);
        if(!$upuser){
            return get_op_put(0, "您的团队存在金融订单未还款，暂不支持提货货");
        }
        return get_op_put(1,'',U('users/stock'));
    }
	//用户提货
    public function stock() {
        layout('layout');

        $acc_addr = M("acc_addr");
        //地址
        $cond["uid"] = $this->ssid;
        $cond["status"] =1;
        $addr = $acc_addr->where($cond)->select();
        $cond["def"] = 1;
        $deft = $acc_addr->where($cond)->order('id desc')->find();
        $userinfo  = M('account')->where(['id'=>$this->ssid])->find();



        $this->assign("deft", $deft);
        $this->assign("addr", json_encode($addr));
        $this->assign("addrshow", clearAddrList($addr));
//        $this->assign("count", count($addr));
        $this->assign("uid", $this->ssid);
        $this->assign("userdata", $userinfo);
        $this->display();
    }

    /**
     *获取提货列表
     */
    public function getStockList(){
        layout("layout");
        $sysbank = M("order_drs");
        $where['uid']=$this->ssid;
        $status =I('st');
        if($status){
            $where['status'] =$status;
        }
        $return_data =$sysbank->where($where)->order('id desc')->select();
        foreach ($return_data as $k =>$v){
            $return_data[$k]['trans_pay'] =$v['trans_pay'] ==''?0:$v['trans_pay'];
        }
        $this->assign('data',$return_data);
        $this->display('Users/getStocklist');
   }

    /**
     *确认收货
     */
    public function sureGetGood(){
        $order_drs =M('order_drs');
        $frozen_money =M('frozen_money');
        $account =M('account');

        $uid =$this->ssid;
        $order_id=I('id');

        $drs_where['uid'] =$uid;
        $drs_where['id'] =$order_id;
        $drs_where['status'] =3;
        $res_data =$order_drs->where($drs_where)->find();
        //存在这个提货单号
        if($res_data){
            $order_drs->startTrans();
            //确认收到货
            if(!$order_drs->where($drs_where)->save(['status'=>6])){
                $order_drs->rollback();
                $this->ajaxreturn(['status'=>0,'msg'=>'网络错误，请稍后重试【INERROR5001】']);
            }
            //把金额解冻
            $money_data =$frozen_money->where(['drs_sn'=>$res_data['sn']])->select();
            //存在冻结金额
            if($money_data) {
                foreach ($money_data as $k => $v) {
                    $user_id = $v['uid'];
                    //处理成功
                    $add_res  =$account->where(['id' => $user_id])->setInc('money',$v['frozen_money']);
                    if(!$add_res){
                        $order_drs->rollback();
                        $this->ajaxreturn(['status'=>0,'msg'=>'网络错误，请稍后重试【INER5103】']);
                    }
                    $frozen_money->where(['id'=>$v['id']])->save(['status'=>1]);
                }
            }
            $order_drs->commit();
            $this->ajaxreturn(['status'=>1,'msg'=>'已确认']);
        }
        $this->ajaxreturn(['status'=>0,'msg'=>'订单错误【ERROR4301】']);
   }

	
	///////////////////////////////////////////////////////////////////////////
    /**
     * 推荐码
     */
    public function codes() {
        $this->assign("user", $this->user);
        $this->display();
    }

    /**
     * 用户推荐码
     */
    public function codes_qr() {
        vendor('phpqrcode.phpqrcode');
        $links = C("WEBURL") . "Home/Wcix/share/sysid/" . $this->user["sysid"] . ".html";
        \QRcode::png($links, null, "M", 10, 1);
    }

    ////////////////////////////////////////////////////////////////////////////

    /**
     * 注销登录
     */
    public function out() {
        session("ssid_dexis", null);
        if (session("ssid_dexis") != null) {
            session_destroy();
        }
        $this->redirect("Index/index");
    }


    /**
     *获取提货单的物流信息
     */
    public function getdrs_trans(){
        $sn =I('sn');
        $orderres =M('order_drs');
        $info =$orderres->where(['sn'=>$sn])->find();
        $expres = D("Home/Expres", "Logic");
        //获取快递的英名称
        $name = getTransNm($info["trans"],'bridno');
//        $trans = $expres->getorder($name, $info["trsn"]);
        $bird = D("Home/Trbrid", "Logic");
        $trans =$bird->orderTracesSubByJson($name,$info["trsn"]);
//        var_dump($trans);
        #
        $info['vosn'] =$info['trsn'];
        $this->assign("info", $info);
        $this->assign("trans", $trans);
        $this->display('users/trans');
    }
    //------------转库存 上--转-->下  2018年7月30日16 add-----

    /**
     *转库存
     */
    public function  turnstock(){
        #
        $this->assign("uid", $this->ssid);
        $this->display();
    }

    /**
     *转移操作
     */
    public function doturn(){
        $data =I('post.');
        $account =M('account');
        $num=$data['num'];
        if($num<=0){
            return get_op_put(0, "至少转移1盒");
        }

        //团队存在金融订单未处理的情况
        $upuser=haveSrOut($this->ssid);
        if(!$upuser){
            return get_op_put(0, "您的团队存在金融订单未还款，暂不支持转货");
        }
        //转账用户的信息
        $user_info =$account->where(['id'=>$this->ssid])->find();

        //被转用户的信息
        $resinfo =$account->where(['sysid'=>$data['code']])->find();
        if($user_info['sysid']!='BOSS') {

            if($num>$user_info['stock']){
                return get_op_put(0, "转移数量超过您的上限");
            }

            if (!$resinfo) {
                return get_op_put(0, "请检查系统账号是否输入正确");
            }
            if ($resinfo['sysid'] == $user_info['sysid']) {
                return get_op_put(0, "库存不能转给自己");
            }
            if ($resinfo['level'] >= $user_info['level']) {
                return get_op_put(0, "转入失败了，不能转给高等级的");
            }
            //判断是否是联创
            //是联创的话可以转给团队下面的所有人
            if ($user_info['level'] == 6) {
                if (!$this->ismyteam($user_info['sysid'], $resinfo['sysid'])) {
                    return get_op_put(0, "只能转给自己的团队");
                }
            } else {
                if ($resinfo['recid'] != $user_info['sysid']) {
                    return get_op_put(0, "只能转给自己的直属团队");
                }
            }

            //黄金等级以下的限制
            /*$maxtrun = getNumsByLevel($user_info['level']);
            if(($num+$resinfo['totalpoints'])>=$maxtrun){
                return get_op_put(0, "转移数量超过了您等级上限");
            }*/
            //黄金以上
            if ($user_info['level'] > 3) {
                $maxtrun = getNumsByLevel($user_info['level']) / 2;
//        $maxtrun =getNumsByLevel($user_info['level']);
                if (($num + nowTrunNum($user_info['id'])) > $maxtrun) {
//        if(($num+$resinfo['totalpoints'])>$maxtrun){
                    return get_op_put(0, "转移数量超过了您等级50%(" . $maxtrun . "盒)的上限");
//            return get_op_put(0, "转移数量超过了您等级上限");
                }
            } else {
                //转移盒数限制
                if ($num >= getNumsByLevel($user_info['level'])) {
                    return get_op_put(0, "转移数量超过了您等级上限");
                }
            }
        }
        $save['stock']=$user_info['stock']-$num;

        $account->where(['id'=>$this->ssid])->save($save);
        //检查积分是否足够升级
        /*$saves['stock']=$resinfo['stock']+$num;
        $saves['points']=$resinfo['points']+$num;
        $saves['totalpoints']=$resinfo['totalpoints']+$num;
        $rs=$account->where(['sysid'=>$data['code']])->save($saves);*/
        //检查积分是否足够升级
        //$code_data=$account->where(['sysid'=>$data['code']])->find();
        //根据积分来修改等级
        //changeUserLevel($code_data['id'],$code_data['totalpoints']);
        $sn =getOrdSn();
        ###添加记录----2018-8-15 add
        $add_nums=[
            'uid'=>$this->ssid,
            'aboutid'=>$resinfo['id'],
            'sn'=>$sn,//add at 2018-9-26 17:56:58
            'nums'=>$num,
            //'after'=>$saves["stock"],
            'uafter'=>$user_info["stock"]-$num,
            'status'=>2,
            'time'=>time(),
            'type'=>21, //库存转让
        ];
       $i= addAcc_nums($add_nums);
        if($i) {
           /* $spread_price =getTheOryMoney($this->ssid,$resinfo['id']);
            //单盒*数量
            $getpro=$spread_price *$num;
            addProfitList($getpro,$this->ssid,$sn,3);*/
            return get_op_put(1, "转入完成等待管理确认", U('users/index'));
        }
        else {
            return get_op_put(0, "转入失败");
        }

    }

    //---------------------绑定手机 2018-7-31 15:07:58add--------
    public function bingphone(){
        $where['id']=$this->ssid;
        $usrerinfo =M('account')->where($where)->find();

       $this->assign('phone',$usrerinfo['phone']);

        $this->display('Login/bindphone');
    }

    /**
     *绑定
     */
    public function bindphone_op(){
        $verify = D("Verif", "Logic");
        #
        $post = I("post.");
        $res = $verify->checkCode($post["phone"], $post["code"]);
        if ($res != 200) {
            return get_op_put(0, "验证码错误");
        }
        $res =M('account')->where(['id'=>$this->ssid])->save(['phone'=>$post['phone']]);
        if($res){
            return get_op_put(1, "绑定完成",U('users/index'));
        }else{
            return get_op_put(0, "绑定失败");
        }
    }

    /**
     *补货申请
     */
    public function getMoreStock(){//添加字段
        addField('orders','is_add','int(1) DEFAULT 0');
        $this->redirect('Malls/goods',['sn'=>'415244730815416283','status'=>9]);
    }

    /**
     *补货列表
     */
    public function getbuhuolist(){
        layout("layout");
        $orders = M("orders");
        #
        $where["uid"] = $this->ssid;
        $where['is_add'] =1;
        $status =I('st');
        if($status!=''){
            $where['status'] =$status;
        }
        $list = $orders->where($where)->order("id desc")->select();
        foreach ($list as $k => $v) {
            $tmp = getIDGoodsInfo($v["gid"]);
            $list[$k]["gsn"] = $tmp["gsn"];
        }
        #
        $this->assign("list", $list);
        $this->display();
    }

    /**
     *根据系统id获取用户信息  08-21 add
     */
    public function  getuserbysys(){
        $id =I('srsid');
        $account =M('account');
        $user_info=$account->where(['sysid'=>$id])->find();
        $this->ajaxReturn($user_info);
    }

    /**
     *是否是我的团队 2018-8-22 11:01 add
     */
    public function ismyteam($now_sysid,$turn_sysid){
        global $arr ;
        $account=M('account');
        $where['sysid']=$turn_sysid;
        $res_info =$account->where($where)->field('recid')->find();
        $arr[]=$res_info['recid'];
        if($res_info['recid']!=null) {
            $this->ismyteam($now_sysid, $res_info['recid']);
        }
//        $arr[]=$res_info['recid'];
        if(in_array($now_sysid,$arr)){
//        if($res_info['recid']==$now_sysid){
            return true;
        }else{
            return false;
        }
    }


    /**
     *获取运费支付 2018年8月31日15:32:08 add
     */
    public function getTranPay(){
        $data=I('get.');
        //获取地区支付费用
       /* if($data['nums']<=4){
            $data['nums']=4;
        }*/
        //清除session
        if(session('tran_money_pay')){
            session('tran_money_pay',null);
        }
        $trans_price =M('trans_price');
        $where['nums'] =$data['nums'];

        $resmoney =$trans_price->where($where)->select();
        if($resmoney){
            foreach ($resmoney as $k=>$v){
                $is_out[]=$v['isout'];
            }

        }
        $where_place['id']=array('in',implode(',',$is_out));
        $isout=getAddressTranPay($data['address'],$where_place);

        $where['isout'] =$isout;
        $where['nums'] =$data['nums'];
        $resmoney =$trans_price->where($where)->find();
        session('tran_money_pay',$resmoney['price']);
        $this->ajaxReturn(['data'=>$resmoney['price']]);
    }

    /**
     *获取运费价格  修改 2018年9月11日10:24:36 sta
     */
    public function gettranprice(){
        $data=I('get.');

        //清除session
        if(session('tran_money_pay')){
            session('tran_money_pay',null);
        }
        $trans_price =M('trans_price');
        $payPrice =$trans_price->order('nums desc')->select();
        //获取运费 --支付等级
        $allcount =$data['nums']%40;
        $price=0;
        if($allcount!=0) {
            if ($allcount >= 40) {
                $where['tran'] = 15;
                $resmoney = $trans_price->where($where)->select();
                if ($resmoney) {
                    foreach ($resmoney as $k => $v) {
                        $is_out[] = $v['isout'];
                    }
                }
                $where_place['id'] = array('in', implode(',', $is_out));
                $isout = getAddressTranPay($data['address'], $where_place);
//                var_dump($isout);
                $trans_where["isout"] = $isout;
                $trans_where["tran"] = $where['tran'];
//            $level_pay =$trans_price->where($trans_where)->order('nums desc')->select();
//            var_dump($level_pay);
//            foreach ($level_pay as $k=>$v){
//                $xiang=$allcount-$v['nums'];//首重
//                $price =$v['price'];
//                if($xiang>0){
//                    $price = $price + ceil($xiang/$v['connetnum']) *$v['connetpay'];//首重/续重*支付
//                }
//            }
                $level_pay = $trans_price->where($trans_where)->order('nums desc')->find();

                $price = $level_pay['price'];
                $xiang = $allcount - $level_pay['nums'];//首重
                if ($xiang > 0) {
                    $price = $price + ceil($xiang / $level_pay['connetnum']) * $level_pay['connetpay'];//首重/续重*支付
                }
            } else {
                $where['tran'] = 11;
                $resmoney = $trans_price->where($where)->select();
                if ($resmoney) {
                    foreach ($resmoney as $k => $v) {
                        $is_out[] = $v['isout'];
                    }
                }
                $where_place['id'] = array('in', implode(',', $is_out));
                $price = getTranbynums($data['address'], $allcount, $where_place);
            }
        }
        session('tran_money_pay',$price);
        $this->ajaxReturn(['data'=>$price]);

    }

    /**
     *支付提货费用
     */
    public function toPayTrans(){
        $wcpay = D("Home/WcPay", "Logic");
        $info=I('sn');
        $res_data =M('order_drs')->where(['sn'=>$info])->find();
        if($res_data['trans_pay']!=null){
            $money =$res_data['trans_pay'];
        }else{
            $money=session('tran_money_pay');
        }
        if($money!=0) {
            $put["sn"] = $info;
            $put["alt"] = "TRAN";
            $put["money"] = $money;
            $put["openid"] = $this->wecs["openid"];
            $res = $wcpay->entrance($put);
            if (!$res) {
                // $this->redirect('users/getStocklist');
                return get_op_put(1, "支付异常,请稍后再试");
            }
            addField('order_drs', 'have_pay', 'int(1) default 0');
            M('order_drs')->where(['sn' => $info])->save(['trans_pay' => $money]);
            session('tran_money_pay', null);
            //  $this->redirect('users/getStocklist');
            return get_op_put(1, null, $res);
        }else {
            M('account')->where(['id' => $res_data['uid']])->setDec('stock', $res_data['nums']);
            M('order_drs')->where(['sn'=>$info])->save(['status'=>1,'have_pay'=>1,'money'=>$money]);
            /*###添加记录----2018-8-15 add
            $add_nums = [
                'uid' => $this->user['id'],
                'aboutid' => 0,
                'sn' => $data['sn'],
                'nums' => $data["nums"],
                'after' => 0,
                'uafter' => $user_info['stock'] - $data['nums'],
                'time' => time(),
                'type' => 23, //提货
            ];
            addAcc_nums($add_nums);*/
            return get_op_put(2, null);
        }
    }

    /**
     *取消支付
     */
    public function cancel(){
        $id =I('id');
        $order_drs =M('order_drs');
        $res =$order_drs->where(['id'=>$id])->find();
        if($res){
            $save['have_pay']=2;
            $save['status']=5;
            $change =$order_drs->where(['id'=>$id])->save($save);
            if($change){
                $arr['status'] =true;
                $arr['msg'] ='已取消支付';
            }else{
                $arr['status'] =false;
                $arr['msg'] ='网络错误';
            }

            $this->ajaxReturn($arr);
        }
    }

    /**
     *返利列表 add 2018-9-26 17:58:26
     */
    public function rebate_list(){
        layout('layout');
        $id =I('id');
        $type =I('type');
        $user['id'] =$id;
        $list =getUserRebackMoneyList($id);
        $this->assign('list',$list);
        $this->assign('type',$type);
        $this->assign("user", $this->user);
//        $this->display('number_list');
        $this->display();
    }

    /**
     *获取销量列表 add 2018-9-26 17:58:37
     */
    public function number_list(){
        layout('layout');
        $id =I('id');
        $type =I('type');
        $list =getUserNumsList($id,$type);
        $this->assign('list',$list);
        $this->assign("user", $this->user);
        $this->assign("type",$type);
        $this->display();
    }

    /**
     *修改个人信息
     */
    public function photochange(){
        $this->assign('user_info',$this->user);
        $this->display();
    }
// 修改头像
    public function editSelfinfo(){
        $data= I('post.');
        //判断传递的键是否存在
        if(preg_match('/^data:image\/(jpeg|png|gif|bpm|jpg);base64,/',$data['img'])){
            $res_img =uploadimg($data['img'],'userhead');
            $res_headUrl=C('WEBURL') . $res_img;
        }
        $model =M('account');
        $save['headimgurl'] =$res_headUrl;
        $res =$model->where(['id'=>$this->ssid])->save($save);
        if($res){
            $stats = 1;
            $msg = '修改成功';
        }else{
            $stats = 0;
            $msg = '修改失败';
        }
        return get_op_put($stats, $msg);
    }


    // 用户前端图表
    public function charts(){
        layout('layout');
        $this->display();
    }

    /**
     *获取数量变化的列表
     */
    public function getNumCg(){
        $p =I('p');
        #库存变化记录
        $index =new IndexController();
        $numsChange =$index->getNumsChange('',$p);
        $this->assign("nums", $numsChange['data']);
        $this->assign("page", $numsChange['page']);
        $this->display('numscg');
    }

    /**
     *获取团队的新增团队数,销售额和库存量
     */
    public function teamNMS(){
        if(IS_AJAX){
            $get=I('get.');
            $desc=$get['type'];
            $rank =$get['rank'];
            $account =M('account');
//        $acc_nomey =M('acc_money');
            $uid =$this->ssid;
            //获取子集数据
            $child_user =S('child_user_data');
            //不存在进入获取
            if(!$child_user) {
                $now_user = $account->where(['id' => $uid])->find();
                //我的团队成员
                $child_user_data = $account->where(['recid' => $now_user['sysid']])->select();
                //获取全部用户
                $alldata = $account->field('id,recid,sysid')->where(['recid' => $now_user['sysid']])->select();
                //条件时间
                $where_newuser['times'] = array('gt', strtotime('-7 days'));
                $child_user =[];
                foreach ($child_user_data as $k => $v) {
                    //团队新增人数
                    $isMemNo = TeamNewAddNum($now_user['sysid'], $where_newuser);
                    $child_user[$k]['name'] = $v['name'];
                    $child_user[$k]['uid'] = $v['id'];
                    $child_user[$k]['new_team'] = $isMemNo;
                    //团队数 --慢
                    $html = $alldata;
                    $arrr = getSum($html, $alldata);
                    $child_user[$k]['team_count'] = count($arrr);
                    //销售额
                    $sale_info = getUserInfo($v['id']);
                    $child_user[$k]['salemoney'] = $sale_info['salemoney'];
                    $child_user[$k]['stock'] = $v['stock'];
                }
                S('child_user_data', $child_user, 6*3600);
            }

            //升序
            if($rank ==1){
                switch($desc){
                    case 'new': asort(array_column($child_user,'new_team'));
                        break;
                    case 'num':  asort(array_column($child_user,'team_count'));
                        break;
                    case 'sale':  asort(array_column($child_user,'salemoney'));
                        break;
                    case 'stock': asort(array_column($child_user,'stock'));
                        break;
                }
            }else{
                switch($desc){
                    case 'new': arsort(array_column($child_user,'new_team'));
                        break;
                    case 'num':  arsort(array_column($child_user,'team_count'));
                        break;
                    case 'sale': arsort(array_column($child_user,'salemoney'));
                        break;
                    case 'stock': arsort(array_column($child_user,'stock'));
                        break;
                }
            }
            $this->ajaxReturn($child_user);

        }
        $this->display();
//       echo    json_encode($child_user);
    }

    #-----------直播
    /**
     *绑定直播地址
     */
    public function bindLive(){
        $where["uid"] = $this->ssid;
        $live =M('sys_live');
        $have_bind =$live->where($where)->find();
        if($have_bind){
//            echo "<script>window.location.href=".$have_bind['liveUrl']."</script>";exit;
            $uid =explode('=',$have_bind['liveurl']);
            $this->assign('live',$uid[1]);
        }
        if(IS_AJAX){
            $data  =I('post.');
            $liveurl ='https://mlive3.inke.cn/app/hot/live?uid='.$data['live_id'];
            if($have_bind){
                $res=$live->where($where)->save(['liveUrl'=>$liveurl]);
            }else{
                $add =[
                    'uid'=>$where["uid"],
                    'liveUrl'=>$liveurl,
                    'time'=>time(),
                    'live_type'=>'映客直播',
                ];
                $res=$live->add($add);
            }


            if($res){
                $stats = 0;
                $msg = '绑定成功';
            }else{
                $stats = 0;
                $msg = '绑定失败';
            }
            return get_op_put($stats, $msg);
        }else{
            $this->display();
        }


    }

    public function LiveList(){
        $live =M('sys_live');
        /*$where['type'] =I('type');
        $datelist =$live->order('id asc')->select();
        foreach($datelist as $k =>$v){
            $user_info =M('account')->where(['id'=>$v['uid']])->find();
            $datelist[$k]['user_name'] =$user_info['name'];
            $datelist[$k]['user_level'] =getUserLevel($user_info['level']);
            $datelist[$k]['user_img'] =$user_info['headimgurl'];
        }
        $this->assign('list',$datelist);*/
        $uid =$this->ssid;
        $user_info =M('account')->where(['id'=>$uid])->find();
        $company_data =$live->where(['type'=>0])->select();
        foreach($company_data as $k =>$v){
            $uid =explode('=',$v['liveurl']);
            $company_data[$k]['live_id'] =$uid[1];
            $company_data[$k]['user_img'] ='./Public/static/imgs/comp.jpg';
        }
        $this->assign('company',$company_data);
        if($user_info['level'] ==6){
            $have=$live->where(['uid'=>$user_info['id']])->select();
            foreach($have as $k =>$v){
                $user_info =M('account')->where(['id'=>$v['uid']])->find();
                $have[$k]['user_name'] =$user_info['name'];
                $have[$k]['user_level'] =getUserLevel($user_info['level']);
                $have[$k]['user_img'] =$user_info['headimgurl'];
                $uid =explode('=',$v['liveurl']);
                $have[$k]['live_id'] =$uid[1];
            }
        }else{
            $top_user =gettop($user_info['sysid'],6);
            $have =$live->where(['uid'=>$top_user['id']])->select();
            foreach($have as $k =>$v){
                $user_info =M('account')->where(['id'=>$v['uid']])->find();
                $have[$k]['user_name'] =$user_info['name'];
                $have[$k]['user_level'] =getUserLevel($user_info['level']);
                $have[$k]['user_img'] =$user_info['headimgurl'];
                $uid =explode('=',$v['liveurl']);
                $have[$k]['live_id'] =$uid[1];
            }
        }
        if($have){
            $this->assign('top',$have);
        }
        $this->display();
    }


}
