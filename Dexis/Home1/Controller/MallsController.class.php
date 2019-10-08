<?php

namespace Home\Controller;

#use Think\Controller;

class MallsController extends CommController {

    public function _initialize() {
        parent::_initialize();
        layout("laynot");
        $ssid = session("ssid_dexis");
        if ($ssid == null && CONTROLLER_NAME != "Login") {
            $this->redirect("Login/index");
        }
    }

    /**
     * 商城首页
     */
    public function index() {
        layout("layout");
        $goods = M("goods");
        #
        $where["ind"] = 1;
        $where["status"] = 1;
        $list = $goods->where($where)->select();
        foreach ($list as $k => $v) {
            $list[$k]["price"] = doPrice($v["gsn"], $this->user["level"]);
        }
        $this->assign("list", $list);
        $this->display();
    }

    /**
     * 商品详情
     * @param type $sn
     */
    public function goods($sn) {
        $goods = M("goods");
        $goods_img = M("goods_img");
        $acc_level = M("acc_level");
        #
        $where["gsn"] = $sn;
        $info = $goods->where($where)->find();
        $info["price"] = doPrice($info["gsn"], $this->user["level"]);
        #
        $list = $goods_img->where($where)->select();
        //是否添加优惠
        $giveDiscount=1;
        if($this->user['recid']==0||$this->user['recid']==null||$this->user['recid']==''){
            if($this->user['level']==0){
                $giveDiscount =0;
            }
        }
        $userinfo =M('account')->where(['id'=>$this->ssid])->find();
        $userinfo['getlevel'] =getUserLevel($userinfo['level']);
        //检测最低购买数量
        //判断是 168
        if($info['id'] ==8){
            $buynums =$acc_level->where(['id'=>$userinfo['level']])->field('buynum')->find();
        }else{
            $buynums['buynum'] =1;
        }
        $this->assign("buynums", $buynums['buynum']);
        $this->assign("info", $info);
        $this->assign("list", $list);
        $this->assign("nowpoint", $userinfo['totalpoints']);
        $this->assign("buylevel", $userinfo['getlevel']);
        $this->assign("discount", $giveDiscount);
        $this->assign("status", $_GET['status']);
        $this->display();
    }

    /**
     *获取价格
    //2018-10-8 17:29:45 add
     */
    public function  getBuyprice ($nums,$gsn,$sta=false){
        $price = doPrice($gsn, $this->user["level"]);
        $price1 = getPayprice($nums,$gsn);
        if($this->user['recid']==0||$this->user['recid']==null||$this->user['recid']==''){
            //是否是游客
            if($this->user['level']==0){
                $price = '168.00';
            }else{
//                    $price1 = getPrice($get["nums"]+$this->user['totalpoints']);
//                    $price1 = getPrice($get["nums"]);
//                $price1 = getPayprice($num,$gsn);
                if($price >= $price1) {
                    $price = $price1;
                }
            }
        }else{
//          $price1 = getPrice($get["nums"]+$this->user['totalpoints']);
//          $price1 = getPrice($get["nums"]);
//          $price1 = getPayprice($num,$gsn);
            if ($price >= $price1) {
                $price = $price1;
            }
        }
        if($sta==2){
//            return $price;
            echo $price;
        }else{

            return $price;
        }
    }

    /**
     * 直接购买
     */
    public function buyer() {
        $goods = M("goods");
        $acc_addr = M("acc_addr");
        $get = I("get.");
        $get["times"] = time();
        $get["nsn"] = getSysLn("order", 4, $this->ssid);
        #
        $where["gsn"] = $get["sn"];
        $info = $goods->where($where)->find();
        $price = doPrice($info["gsn"], $this->user["level"]);
        if ($get["sn"] == '415244730815416283') {   //上帝之泪价格根据数量来计算
            //判断是否有上级
            /*if($this->user['recid']==0||$this->user['recid']==null||$this->user['recid']==''){
                //是否是游客
                if($this->user['level']==0){
                    $price = '168.00';
                }else{
//                    $price1 = getPrice($get["nums"]+$this->user['totalpoints']);
//                    $price1 = getPrice($get["nums"]);
                    $price1 = getPayprice($get["nums"],$get["sn"]);
                    if($price >= $price1) {
                        $price = $price1;
                    }
                }
            }else{
//                $price1 = getPrice($get["nums"]+$this->user['totalpoints']);
//                $price1 = getPrice($get["nums"]);
                $price1 = getPayprice($get["nums"],$get["sn"]);
                if ($price >= $price1) {
                    $price = $price1;
                }
            }*/
            //2018-10-8 17:29:45 add
            $price=$this->getBuyprice($get["nums"],$get["sn"]);
        }
        $pay=0;

        $info["price"] = $price;
        #
        //地址
        $cond["uid"] = $this->ssid;
        $cond["status"] =1;
        $addr = $acc_addr->where($cond)->select();
        $cond["def"] = 1;
        $deft = $acc_addr->where($cond)->find();
        //判断是补货还购买  ---2018-8-7 17:35:38
        if($get['status'] &&$get['status'] ==9){

        }else{
            //暂时注释
            /*if(mb_substr($deft['street'],0,2)=='四川'){ //省内
                $isout=0;
            }else{
                $isout=1;
            }
            //获取运费 --支付等级
            $trans_price =M('trans_price');
            $trans_where["gsn"] = $get["sn"];
            $trans_where["isout"] = $isout;
            $level_pay =$trans_price->where($trans_where)->order('nums desc')->select();
            $allcount =$get['nums'];

            foreach ($level_pay as $k=>$v){
                $xiang=floor($allcount/$v['nums'])*$v['price'];//整箱
                if($k==count($level_pay)-1){
                    $xiang =ceil($allcount/$v['nums'])*$v['price'];
                }
                $rest  =fmod($allcount,$v['nums']);
                $pay +=$xiang;
                $allcount =$rest;
            }*/
        }
        $price=$pay;

        if($get['price']){
            $onepay =$get['price']/$get['nums'];
            $info['price'] =$onepay;
        }
        #新增字段 2018-9-18 18:10:43 add
        addField('orders','remakes','varchar(255)');
        #
        $this->assign("info", $info);
        $this->assign("get", $get);
        $this->assign("addr", json_encode($addr));
        $this->assign("addrshow", clearAddrList($addr));
        $this->assign("deft", $deft);
        $this->assign("count", count($addr));
        $this->assign("price", $price);
        $this->assign("status", $get['status']);
        $this->display();
    }

    ////////////////////////////////////////////////////////////////////////////

    /**
     * 生成订单
     */
    public function doBuyerOpt() {
        $orders = M("orders");
        $post = I("post.");
        #
        $goods = getGoodsInfo($post["gsn"]);
        $price = doPrice($post["gsn"],$this->user["level"]);
        if ($post["gsn"] == '415244730815416283') {   //根据等级和价格来运算
//            $price1 = getPrice($post["gnums"]+$this->user['totalpoints']);
//            $price1 = getPrice($post["gnums"]);

           /* $price1 = getPayprice($post["nums"],$post["gsn"]);
            //判断是否有上级
            if($this->user['recid']==0||$this->user['recid']==null||$this->user['recid']=='') {
                //是否是游客
                if ($this->user['level'] == 0) {
                    $price = '168.00';
                } else {
                    if ($price1 <= $price) { //数量价格小于等级价格
                        $price = $price1;
                    }
                }
            }else {
                if ($price1 <= $price) { //数量价格小于等级价格
                    $price = $price1;
                }
            }*/

            $price=$this->getBuyprice($post["gnums"],$post["gsn"]);
        }
        #在订单处添加等级
        addField('orders','buy_level','varchar(20)');
        $now_point =$this->user['totalpoints'] + $post['gnums'];
        $where['months'] =array('elt',$now_point);
        $get_level =M('acc_level')->where($where)->order('id desc')->find();
        $level =$this->user["level"]>=$get_level['id']?$this->user["level"]:$get_level['id'];

        $goods["price"] = $price;
        $post["gid"] = $goods["id"];
        $post["gname"] = $goods["name"];
        $post["gimgs"] = $goods["imgs"];
        $post["gprice"] = $goods["price"];
        $post["trans_pay"] = $post["yunfei"];
        $post["buy_level"] = $level;
        #
        $post["uid"] = $this->ssid;
        $paymoney =$post['money'] <=$goods["price"] * $post["gnums"]+$post["yunfei"]?$post['money']:$goods["price"] * $post["gnums"]+$post["yunfei"];
        $post["money"] =$paymoney;
        $post["fees"] = 0;
        $post["status"] = 0;
        if($post['add_stock']&&$post['add_stock']==9){
            $post["is_add"] = 1;//是否是补货
        }
        $post["status"] = 0;
        $post["times"] = time();
        //删除相同未支付数据 2018-9-18 18:11:27 add
        $orderinfo =$orders->where(['sn'=>$post['sn'],'status'=>0])->find();
        if($orderinfo){
            $orders->where(['sn'=>$post['sn'],'status'=>0])->delete();
        }
        if (!$orders->add($post)) {
            return get_op_put(0, "网络异常，请稍候再试[0X002]");
        }
        closeSysLn("order", 4, $this->ssid);
        return get_op_put(1, null, U('Malls/payer', 'sn=' . $post["sn"]));
    }

    ////////////////////////////////////////////////////////////////////////////

    public function payer($sn) {
        $orders = M("orders");
        #
        $sns= getSysLn("order", 4, $this->ssid);
        $where["sn"] =$sn;
        $info = $orders->where($where)->select();
        if(count($info)>1){
            foreach($info as $k=>$v){
                if($v['status']==0){
                   $orders->where(['id'=>$v['id']])->save(['sn'=>$sns]);
                    $infos=$info[$k];
                    $infos['sn']=$sns;
                }
            }
        }else{
            $infos=$info[0];
        }

        #
        $this->assign("info", $infos);
        $this->display();
    }

    /**
     * 执行支付
     */
    public function creatPay() {
        $orders = M("orders");
        $wcpay = D("Home/WcPay", "Logic");
        $post = I("post.");
        #
        $where = array("sn" => $post["sn"], "status" => 0);
        //订单信息
        $info = $orders->where($where)->find();

        //2018-8-1  add show paytype
        $orders->where($where)->save(['paytype'=>$post["paytype"]]);
        if ($info == null) {
            return get_op_put(0, "网络错误，请稍后再试");
        }
        //判断产品进行支付
        if($info['gid']!=8){
                $this->buyOtherThing($info,$post["paytype"] );
        }else {
            /*
             *执行出售168的逻辑
             * */

            #余额支付
            if ($post["paytype"] == 4) {
                return $this->walletPay($info);
            }
            //支付金额不相等
            if($info['gprice'] * $info['gnums'] !=$info['money']){
                return get_op_put(0, "订单异常,请稍后再试");
            }
            //执行微信支付
            $put["sn"] = $info["sn"];
            $put["alt"] = "ORDER";
            $put["money"] = $info["money"];
            $put["openid"] = $this->wecs["openid"];
            $res = $wcpay->entrance($put);
            if (!$res) {
                return get_op_put(0, "支付异常,请稍后再试");
            }
            //增记录不加金额 2018-10-10 18:32:57 change
            if (!cgUserMoney($this->ssid, $info["money"], 0, "ORDER", $info["sn"], false)) {
                return get_op_put(0, "支付失败，请稍后再试");
            }
            
            //判断是不是补货
            if ($info['is_add'] == 1) {
                return get_op_put(2, null,$res);
            }
            return get_op_put(1, null, $res);
        }
    }

    /**
     * 余额支付
     * @param type $data
     */
    private function walletPay($data) {
        $syspaysn = M("syspaysn");
        $account = M("account");
        $ordres = D("Home/OrdRes", "Opera");
        #生成支付序列号
        $paysn = array(
            "sn" => getOrdSn(), "resn" => $data["sn"], "models" => "ORDER",
            "body" => "余额支付", "money" => $data["money"], "status" => 0, "times" => time()
        );
        $pay = $syspaysn->where("resn='" . $data["sn"] . "'")->find();
        //var_dump($pay);
        if ($pay == null) {
            if (!$syspaysn->add($paysn)) {
                return get_op_put(0, "网络问题，订单异常");
            }
            $pay["sn"] = $paysn["sn"];
        }
        #判断余额
        $where["id"] = $this->ssid;
        $info = $account->where($where)->find();
        if ($info["money"] < $data["money"]) {
            return get_op_put(0, "账户余额不足，不足以支付本次费用");
        }
        //不能使用余额支付金额
        $order_sr_where['status'] =array('in','5,7');
        $order_sr_where['uid'] = $this->ssid;
        $order_sr_data =M('order_sr')->where($order_sr_where)->find();
        if($order_sr_data){
            return get_op_put(0, "您存在金融订单未还款，暂不支持余额支付");
        }
        #扣除款项
        $account->startTrans();
        if (!cgUserMoney($this->ssid, $data["money"], 0, "ORDER", $data["sn"])) {
            $account->rollback();
            return get_op_put(0, "支付失败，请稍后再试");
        }
        #完成支付
        $ores = $ordres->runs($pay["sn"]);
        $res = xmlToArray($ores);
        if ($res["return_code"] != "SUCCESS") {
            $account->rollback();
            return get_op_put(0, "支付失败，请稍后再试[0XSTUS]", $res);
        }

        $account->commit();
        //判断是否是自提
        /*if($data['addr']==0){
            //扣除个人的库存
            $account->where($where)->setDec('stock',$data['gnums']);
            $add_nums=[
                'uid'=>$this->ssid,
                'aboutid'=>0,
                'sn'=>$this->data["sn"],
                'nums'=>$data['gnums'],
                'after'=>0,
                'uafter'=>$info['stock']-$data['gnums'],
                'time'=>time(),
                'type'=>23, //自提
            ];
            addAcc_nums($add_nums);
            M('orders')->where(['id'=>$data['id']])->save(['status'=>5]);
        }*/
        //活动的方案------------------------------2019-3-8 20:00:43
        GoldActivity( $data["sn"]);
        //end-------------------------------------
        //判断是不是补货
        if($data['is_add']==1){
            return get_op_put(2, null);
        }
        return get_op_put(1, null);
    }

    /**
     *执行购买其他产品的逻辑
     */
    private function buyOtherThing($order_info,$paytype){
        $wcpay = D("Home/WcPay", "Logic");
        if ($paytype== 4) {
            return $this->payBywall($order_info);
        }
        $put["sn"] = $order_info["sn"];
        $put["alt"] = "ORDER";
        $put["money"] = $order_info["money"];
        $put["openid"] = $this->wecs["openid"];
        //增记录
        if (!cgUserMoney($this->ssid, $order_info["money"], 0, "ORDER", $order_info["sn"], false)) {
            return get_op_put(0, "支付失败，请稍后再试");
        }
        $res = $wcpay->entrance($put);
        if (!$res) {
            return get_op_put(0, "支付异常,请稍后再试");
        }
        //判断是不是补货
        if ($order_info['is_add'] == 1) {
            return get_op_put(2, null,$res);
        }
        return get_op_put(3, null, $res);
     }

    /**余额支付
     * @param $order_info
     */
    private  function payBywall($data){
        $syspaysn = M("syspaysn");
        $account = M("account");
        $ordres = D("Home/OrdRes", "Opera");
        #生成支付序列号
        $paysn = array(
            "sn" => getOrdSn(), "resn" => $data["sn"], "models" => "ORDER",
            "body" => "余额支付", "money" => $data["money"], "status" => 0, "times" => time()
        );
        $pay = $syspaysn->where("resn='" . $data["sn"] . "'")->find();
        //var_dump($pay);
        if ($pay == null) {
            if (!$syspaysn->add($paysn)) {
                return get_op_put(0, "网络问题，订单异常");
            }
            $pay["sn"] = $paysn["sn"];
        }
        #判断余额
        $where["id"] = $this->ssid;
        $info = $account->where($where)->find();
        //支付金额为负数的话
        if($data["money"]<0){
            return get_op_put(0, "订单异常，请稍后再试");
        }
        //手提袋不在金额的限定
        if($data["gid"] ==15){
            if($data["gid"] !=58 ||$data["gid"] !=136 ){
                return get_op_put(0, "订单异常，请稍后再试");
            }
        }else {
            if ($data["gprice"] * $data["gnum"] != $data["money"]) {
                return get_op_put(0, "订单异常，请稍后再试");
            }
        }
        //金额不足
        if ($info["money"] < $data["money"]) {
            return get_op_put(0, "账户余额不足，不足以支付本次费用");
        }
        //不能使用余额支付金额
        $order_sr_where['status'] =array('in','5,7');
        $order_sr_where['uid'] = $this->ssid;
        $order_sr_data =M('order_sr')->where($order_sr_where)->find();
        if($order_sr_data){
            return get_op_put(0, "您存在金融订单未还款，暂不支持余额支付");
        }
        #扣除款项
        $account->startTrans();
        if (!cgUserMoney($this->ssid, $data["money"], 0, "ORDER", $data["sn"])) {
            $account->rollback();
            return get_op_put(0, "支付失败，请稍后再试");
        }
        #完成支付
        $res =$syspaysn->where("resn='" . $data["sn"] . "'")->save(['status'=>1]);
        $res =M('orders')->where("sn='" . $data["sn"] . "'")->save(['status'=>1,'paytime'=>time()]);
        if (!$res) {
            $account->rollback();
            return get_op_put(0, "支付失败，请稍后再试[0XSTUS]", $res);
        }
        $account->commit();


        //判断是不是补货
        if($data['is_add']==1){
            return get_op_put(2, null);
        }
        return get_op_put(3, null);
     }

    /**
     *购买成功页面
     */
    public function pay_success(){
        $this->display();
     }
}
