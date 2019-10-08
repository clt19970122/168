<?php

namespace Home\Opera;

/**
 * 订单处理
 */
class OrdResOpera {

    private $fees;
    //
    private $sn;
    private $data;
    private $user;

    public function __construct() {
        $sysconfig = M("sysconfig");
        #
        $where["models"] = "CONF_VISITER";
        $info = $sysconfig->where($where)->find();
		//获取游客返利
        $this->fees = $info["value"];
    }

    ////////////////////////////////////////////////////////////////////////////

    /**
     * 入口
     * @param type $sn
     */
    public function runs($sn) {
        $this->sn = $sn;
        return $this->getOrder();
    }

    /**
     * 获取订单
     * @return type
     */
    private function getOrder() {
        $syspaysn = M("syspaysn");
        $orders = M("orders");
        #
        $where["sn"] = $this->sn;
        $paysn = $syspaysn->where($where)->find();
        if ($paysn == null) {
            return $this->wechatRollback("FAIL", "PAYSN_NULL");
        }
        $where["sn"] = $paysn["resn"];
        $where["status"] = 0;
        $info = $orders->where($where)->find();
        if ($info == null) {
            return $this->wechatRollback("FAIL", "ORDER_NULL");
        }
        /*if($info['gprice'] * $info['gnums'] != $info['money']){
            return $this->wechatRollback("FAIL", "订单错误");
        }*/
        $this->data = $info;
//        var_dump($info);
        return $this->updateOrder();
    }

    /**
     * 更新订单
     * @return type
     */
    private function updateOrder() {
        $orders = M("orders");
        #
        $where["sn"] = $this->data["sn"];
        $where["status"] = 0;
        $save["status"] = 1;
        $save["paytime"] = time();
        if (!$orders->where($where)->save($save)) {
            return $this->wechatRollback("FAIL", "ORDER_UPFAIL");
        }
        #
        //变动积分
        //sleep(1);
        #
        if (!cgUserPoint($this->data["uid"], $this->data["gnums"], $where["sn"])) {
            return $this->wechatRollback("FAIL", "USER_UPFAIL");
        }
        //===
        //获取下单用户信息 --删除2019年3月5日10:42:48
        /*$user_info =  M("account")->where(['id'=>$this->data["uid"]])->find();
        if($user_info['recid']==0||$user_info['recid']==null||$user_info['recid']==''){
            //是否是游客
            if($user_info['level']!=0){
                if (!cgUserPoint($this->data["uid"], $this->data["gnums"], $where["sn"])) {
                    return $this->wechatRollback("FAIL", "USER_UPFAIL");
                }
            }
        }else {
            if (!cgUserPoint($this->data["uid"], $this->data["gnums"], $where["sn"])) {
                return $this->wechatRollback("FAIL", "USER_UPFAIL");
            }
        }*/
        #
//       增加用户库存
        $account = M("account");
        $cond["id"] = $this->data["uid"];
        $update["stock"]= array("exp", "stock+" . $this->data["gnums"]);
        //$account->stock= array("exp", "stock+" . $this->data["gnums"]);
        //$update["stock"] = array("exp", "stock+" . $this->data["gnums"]);
        if (!$account->where($cond)->save($update)) {
            return $this->wechatRollback("FAIL", "USER_UPFAIL[02]");
        }
        // 如果是分期-就需要先变动等级
        if(substr($this->data["sn"],0,2) =="ST"){
            //变动等级
            changeUserLevel($this->data["uid"],800,'-分期');
        }
        //积分足够进行升级 --18/07/26 add
        if(!$this->haveEnoughtTotalPointToUpLevel($this->data["uid"])){
            return $this->wechatRollback("FAIL", "USER_UPFAIL[03]");
        }
        #
        return $this->getUserInfo();
    }

    ////////////////////////////////////////////////////////////////////////////

    /**
     * 获取用户信息
     */
    private function getUserInfo() {
        $account = M("account");
        #
        $where["id"] = $this->data["uid"];
		//获取下单用户信息
        $info = $account->where($where)->find();
        //\Think\Log::record(json_encode($info));
        #
        if ($info == null) {
            return $this->wechatRollback("FAIL", "USER_NOT_FOUND");
        }
        #
        #模板消息推送
        //当购买成功之后 用户库存增加 给用户发送模板消息
        $wctemp = D("Home/Wctemp", "Logic");
        $user = ["openid" => $info["openid"], "nickname" => $info["nickname"]];
        $data = ["type" => "proin", "product" => '168太空素食', "nums" => $this->data["gnums"], "stock" => $info["stock"]];
        $wctemp->entrance($user,$data);
        #
        #没有上级
        if ($info["recid"] == null || $info["recid"] == 0) {
            ###添加记录----2018-8-15 add
            $add_nums=[
                'uid'=>$info['id'],
                'aboutid'=>0,
                'sn'=>$this->data["sn"],
                'nums'=>$this->data["gnums"],
                'after'=>0,
                'uafter'=>$info["stock"],
                'time'=>time(),
                'type'=>12, //系统购买
            ];
            addAcc_nums($add_nums);
            return $this->wechatRollback("SUCCESS", "OK");
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
		//获取上家id
        $cond["sysid"] = $this->user["recid"];
        for ($i = 0; $i < 1; $i++) {
			//获取上家信息
            $tmp = $account->where($cond)->find();
            if ($tmp == null) {
                ###添加记录----2018-8-15 add
                $add_nums=[
                    'uid'=>$this->user['id'],
                    'aboutid'=>0,
                    'sn'=>$this->data["sn"],
                    'nums'=>$this->data["gnums"],
                    'after'=>0,
                    'uafter'=>$this->user["stock"],
                    'time'=>time(),
                    'type'=>12, //系统购买
                ];
                addAcc_nums($add_nums);

                return $this->wechatRollback("SUCCESS", "OK");
            }
            if (!$this->doShareFinish($tmp, $i)) {
                return $this->wechatRollback("FAIL", "RE_FAIL");
            }
            $cond["sysid"] = $tmp["recid"];
        }

        return $this->wechatRollback("SUCCESS", "OK");
    }

    /**
     * 完成分成
     * @param type $tmp
     * @return boolean
     */
    private function doShareFinish($tmp, $level) {
        #积分返点---高拉低不存在返点
        /*if($tmp['level']<=$this->user['level']){
            if (!cgUserPoint($tmp["id"], $this->data["gnums"], $this->data["sn"])) {
                return false;
            }
        }
        //积分足够进行升级 --18/07/26 add
        if(!$this->haveEnoughtTotalPointToUpLevel($tmp["id"])){
            return false;
        }*/
        #现金返点
		//$account = M("account");
        //$cond["sysid"] = $this->user["sysid"];
        //$tmp1 = $account->where($cond)->find();
        //团队销售额
//        $roll = $this->getLevMon($tmp, $level) *  $this->data["gnums"];//根据用户等级和数量判断价格 --2018-10-30 13:36:04 注释

        //团队销售额
        $roll = $this->data["money"];//用户支付金额
        //个人利润
        $get_reback=$this->getLevMon2($tmp, $level);
        //判断个人返利是不是数组
        if(is_array($get_reback)){
            $roll2=$get_reback['money']*$this->data["gnums"];
            //返利到推荐人
            if (!cgUserMoney($get_reback['user']["id"],$roll2, 1, "REBACK", $this->data["sn"])) {
                return false;
            }
            //增加利润记录 2018年12月11日11:31:23 返利
            addProfitList($roll2,$get_reback['user']['id'],$this->data["sn"],2,1);
            $tmp["id"]=$get_reback['user']["id"];
        }else{
            $roll2 = $get_reback *  $this->data["gnums"];//价格*数量
            //返利金额
            $rolls =$tmp['level']<=$this->user['level']?$roll2:$roll;
            //记录返利到上级
            if (!cgUserMoney($tmp["id"], $rolls, 1, "REBACK", $this->data["sn"])) {
                return false;
            }
            $type_profit =$tmp['level']<=$this->user['level']?2:1;
            //增加利润记录 2018年12月11日11:31:23 返利
            addProfitList($roll2,$tmp['id'],$this->data["sn"],$type_profit);
        }
        #业绩计算
        $account = M("account");
        //扣除上级产品的数量
        ##
        $where["id"] = $tmp["id"];
        //上一级的账号信息
        $info = $account->where($where)->find();
        ##
//        $level == 0 ? $this->doCeilStock2($info["sysid"],$roll-$roll2) : null;
        $this->doCeilStock2($info["sysid"],$roll-$roll2) ;
        ##2018-11-12 18:16:16 change
        if ($info["level"] > 0) {
            $save["groups"] = $info["groups"] + $roll;
			$save["person"] = $info["person"] + $roll2;
            // 返利提醒模板消息（个人利润）
            // if ($save["person"] < 0) {
            //     $user = ["openid" => $info["openid"], "nickname" => this->$user["nickname"]];
            //     $data = ["type" => "tips", "person" => $save["person"]];
            //     $wctemp1->entrance($user, $data);
            // }
            $save["uptimes"] = time();
            return $account->where($where)->save($save);
        }else{
            return true;
        }
        //
       // $level == 0 ? $this->doCeilStock($info["sysid"]) : null;


    }

    //------------------------------------------------------------------------//

    /**
     * 团队销售额统计 ---下级的买价
     */
    private function getLevMon($tmp, $v) {
        $acc_level = M("acc_level");
        #等级查询
        //$leSel["id"] = $tmp["level"];
		/*$leSel["id"] = $this->user["level"];
		//查询上家等级信息
        $lev = $acc_level->where($leSel)->find();
        if ($lev == null) {
            $share = array($this->fees, $this->fees, $this->fees);
        } else {
            $share = array($lev["first"], $lev["second"], $lev["third"]);
        }*/
        #
		//如果低级拉高级或者同级，售卖就是高的价格
        if ($tmp["level"] <= $this->user["level"]) {

            $samePrice = $this->getCeil($tmp["level"], $v);
            //$samePrice =$share[$v];
            //return $share[$v];
            return $samePrice;
        }
		//高级拉低级返差价,返上级销售额
        return $this->getCeil($tmp["level"], $v);
    }


	//返价差到个人利润
	   private function getLevMon2($tmp, $v) {
        $acc_level = M("acc_level");
        #等级查询
        //$leSel["id"] = $tmp["level"];
		$leSel["id"] = $this->user["level"];
		//查询上家等级信息
        $lev = $acc_level->where($leSel)->find();
        if ($lev == null) {
            $share = array($this->fees, $this->fees, $this->fees);
        } else {
            $share = array($lev["first"], $lev["second"], $lev["third"]);
        }
        #
		//如果低级拉高级，返利
        if ($tmp["level"] <= $this->user["level"]) {
            //如果推荐等级到了联创
            if($this->user['level']==6){
               //添加返利上级的上级联创
                $topTwo =gettop($tmp["recid"],$this->user['level']);// 慢
                $addmoney=2.5*$this->data["gnums"];
                if($topTwo['level']==6){
                    M('account')->where(['id'=>$topTwo['id']])->setInc('person',$addmoney);
                    //增加利润记录 2018年12月11日11:31:23 返利
                    addProfitList($addmoney,$topTwo['id'],$this->data["sn"],2);
//                    M('account')->where(['id' => $topTwo['id']])->setInc('money', $addmoney);
                    /*记录金额信息*/
                    if (!cgUserMoney($topTwo["id"], $addmoney, 1, "REBACK", $this->data["sn"])) {
                        return false;
                    }
                    if($tmp['level']!=6){
                        //上非 -》上是 -》上级联创
                        $topThree =gettop($topTwo["recid"],$this->user['level']);//慢
                        if($topThree['level']==6) {
                            //增加个人利润统计
                            M('account')->where(['id' => $topThree['id']])->setInc('person', $addmoney);
                            //增加利润记录 2018年12月11日11:31:23  返利
                            addProfitList($addmoney,$topThree['id'],$this->data["sn"],2);
//                          M('account')->where(['id' => $topThree['id']])->setInc('money', $addmoney);
                            /*记录金额信息*/
                            if (!cgUserMoney($topThree["id"], $addmoney, 1, "REBACK", $this->data["sn"])) {
                                return false;
                            }
                        }
                    }
                }
                if($tmp["level"]==6){
                    return 5;
                }
                //黄金以下 推荐一个联创 不返利
                if($tmp['level']<3){
                    //找到一个等级大于黄金的中间人
                    //--暂定
                    $should_get =gettop($tmp["recid"],3);
                    //如果大于黄金的
                    if (!cgUserMoney($should_get["id"], $addmoney, 1, "REBACK", $this->data["sn"])) {
                        return false;
                    }
                    //增加利润记录 2018年12月11日11:31:23  返利
                    addProfitList($addmoney,$should_get['id'],$this->data["sn"],2);
                    //给直推上级返利 0元
                     return 0;
                    //判断上级是否是联创
//                    if($should_get['id']==$topTwo['id']){
//                        return 0;
//                    }else{
//                        //给到返利到上级的上级
//                        /*记录金额信息*/
//                        if (!cgUserMoney($should_get["id"], $addmoney, 1, "REBACK", $this->data["sn"])) {
//                            return false;
//                        }
//                        return 0;
//                    }
                }
                return 2.5;
            }
//            if($tmp['level']<3&&$tmp['level']>0){
//            低推高 下级等级不是联创
            //上级等级小于黄金的话 就进行返利操作
            if($tmp['level']<3){
                //购买等级大于黄金
                if($this->user["level"]>2){

                    //找到一个等级大于等于购买人的人
                    //supet 2 判断这个人的等级是不是等于购买人的 是就返利 不是的话就是出货人
//                    $should_pay =gettop($tmp["recid"],$this->user["level"]);
                    $should_pay =gettop($tmp["recid"],3);
                    if($should_pay['level']<=$this->user["level"]){
                        //用户和这个获得返利人等级相同后进行返利的操作
                        return ['user'=>$should_pay,'money'=>$share[0]];
                    }
                    return 0;
                }else{
                    //如果上级的等级是游客 积分为0的话 就不反利润
                    if($tmp['level']==0&&$tmp['totalpoints']==0){
                        return 0;
                    }else{
                        return  $share[0];
                    }
                }
            }
            return $share[$v];
        }
		//返价差到个人利润;高拉低。差价
		return $this->getCeil2($tmp["level"], $v);
    }
    /**
     * 获取购物等级差价
	 * 参数$V上级等级
	 * $loc三级
     */
    private function getCeil($v, $loc) {
        $goods_level = M("goods_level");
        #
        $goods = getIDGoodsInfo($this->data["gid"]);
        $where["gsn"] = $goods["gsn"];
        $where["lid"] = $this->user["level"];
        $user = $goods_level->where($where)->find();
        if ($user == null) {
            $user["price"] = $goods["price"];
        }
        #
		//获取上级等级的价格
        /*$where["lid"] = $v;
        $parent = $goods_level->where($where)->find();
        if ($parent == null) {
            return 0;
        }*/
        #
		//获取下级购买费用
        //$ceil = $user["price"] - $parent["price"];
		$ceil = $user["price"];
        if ($loc > 0 && $v > 0) {
            $ceil = $ceil - $this->fees;
        }
        return abs($ceil);
    }


		//获取价差
	    private function getCeil2($v, $loc) {
        $goods_level = M("goods_level");
        #
        $goods = getIDGoodsInfo($this->data["gid"]);
        $where["gsn"] = $goods["gsn"];
        $where["lid"] = $this->user["level"];
        $user = $goods_level->where($where)->find();
        if ($user == null) {
            $user["price"] = $goods["price"];
        }
        #
		//获取上级等级的价格
        $where["lid"] = $v;
        $parent = $goods_level->where($where)->find();
        if ($parent == null) {
            return 0;
        }
        #
		//获取下级购买费用
        $ceil = $user["price"] - $parent["price"];
		//$ceil = $user["price"];
        if ($loc > 0 && $v > 0) {
            $ceil = $ceil - $this->fees;
        }
        return abs($ceil);
    }

    /**
     * 扣减B端库存 -------old
     */
    private function doCeilStock($id) {

    $account = M("account");
    $wctemp = D("Home/Wctemp", "Logic");
    #
    $where["sysid"] = $id;
    for ($i = 0; $i < 1; $i++) {
        $info = $account->where($where)->find();
        if ($info["level"] <= 0) {
            $where["sysid"] = $info["recid"];
            continue;
        }
        $save["stock"] = $info["stock"] - $this->data["gnums"];
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
 //--------------扣除上高级的库存 2018726----//
    private function doCeilStock2($id,$to_on_money) {

        $account = M("account");
        $wctemp = D("Home/Wctemp", "Logic");
        $goodlevel =M("goods_level");
        #
        $where["sysid"] = $id;
        //上级的信息
        $info = $account->where($where)->find();
        //当前用户的
        $now_user=$account->where(['id'=>$this->user['id']])->find();
        $uafter =$now_user['stock'];

        $goods = getIDGoodsInfo($this->data["gid"]);

        //上级的等级比当前登录的等级要低或者相同
        if($info['level']<=$this->user['level']){
            $res_info =getHigherThanNow($id,$this->user['level']);
            if($res_info){
                if($res_info['level']>$this->user['level']) {
                    //等级高的人的价格
                    $wheress["gsn"] = $goods["gsn"];
                    $wheress["lid"] = $res_info['level'];
                    $hegher_price = $goodlevel->where($wheress)->find();
                    //利润减成本
                    $ceil_money = $to_on_money ;//- $hegher_price['price'] * $this->data["gnums"];
                    $save["stock"] = $res_info["stock"] - $this->data["gnums"];//减去数量
//                    $save["money"] = $res_info["money"] + $to_on_money;//增加金额
                    //记录返利
                    if (!cgUserMoney($res_info["id"], $to_on_money, 1, "REBACK", $this->data["sn"])) {
                        return false;
                    }
                    $save["person"] = $res_info["person"] + ($ceil_money-($hegher_price['price'] * $this->data["gnums"]));//增加利润
                    $wheres['sysid'] = $res_info['sysid'];
                    if (!$account->where($wheres)->save($save)) {
                        return false;
                    }
                    //增加利润记录 2018年12月11日11:31:23
                    addProfitList($ceil_money-($hegher_price['price'] * $this->data["gnums"]),$res_info['id'],$this->data["sn"],1);
                    //发送模板消息
                    if ($save["stock"] < 0) {
                        $user = ["openid" => $res_info["openid"], "nickname" => $res_info["nickname"]];
                        $data = ["type" => "tips", "count" => $save["stock"]];
                        $wctemp->entrance($user, $data);
                    }

                    ###添加记录----2018-8-15 add

                    $add_nums=[
                        'uid'=>$this->user['id'],
                        'aboutid'=>$res_info['id'],
                        'sn'=>$this->data["sn"],
                        'nums'=>$this->data["gnums"],
                        'after'=>$save["stock"],
                        'uafter'=>$uafter,
                        'time'=>time(),
                        'type'=>12, //系统购买
                    ];
                    addAcc_nums($add_nums);
                }else{
                    /*
                     * 没有比当前账户等级跟高的用户 走公司
                     *
                     * */
                    ###添加记录----2018-8-15 add
                    $add_nums=[
                        'uid'=>$this->user['id'],
                        'aboutid'=>0,
                        'sn'=>$this->data["sn"],
                        'nums'=>$this->data["gnums"],
                        'after'=>0,
                        'uafter'=>$uafter,
                        'time'=>time(),
                        'type'=>12, //系统购买
                    ];
                    addAcc_nums($add_nums);
                }
            }
            return true;
        } else{
//            $save["money"] = $info["money"] +$to_on_money;//增加金额
            //记录返利
            /*if (!cgUserMoney($info["id"], $to_on_money, 1, "REBACK", $this->data["sn"])) {
                return false;
            }*/
            ###添加记录----2018-8-15 add
            $save["stock"] = $info["stock"] - $this->data["gnums"];//减去数量
            $add_nums=[
                'uid'=>$this->user['id'],
                'aboutid'=>$info['id'],
                'sn'=>$this->data["sn"],
                'nums'=>$this->data["gnums"],
                'after'=>$save["stock"],
                'uafter'=>$uafter,
                'time'=>time(),
                'type'=>12, //系统购买
            ];
            addAcc_nums($add_nums);
            if (!$account->where($where)->save($save)) {
                return false;
            }
            if ($save["stock"] < 0) {
                $user = ["openid" => $info["openid"], "nickname" => $info["nickname"]];
                $data = ["type" => "tips", "count" => $save["stock"]];
                $wctemp->entrance($user, $data);
            }
        }
        return true;
    }

    ////////////////////////////////////////////////////////////////////////////

    /**
     * 微信返回
     */
    private function wechatRollback($code, $msg) {
        $string = "<xml>";
        $string .= "<return_code><![CDATA[" . $code . "]]></return_code>";
        $string .= "<return_msg><![CDATA[" . $msg . "]]></return_msg>";
        $string .= "</xml>";
        return $string;
    }
     //-------------------------------2018年7月26日13:45:49 增加总积分到达多少升级-----------------------------------//

    /**当前账户有足够的积分升级
     * @param $total
     */
    private function haveEnoughtTotalPointToUpLevel($id){
       $account =M('account');
       $res_data=$account->where(['id'=>$id])->find();
       if(!$res_data){
           return false;
       }
       //修改用户等级
//       return cgUserLevel($id,$res_data['totalpoints']);
       return changeUserLevel($id,$res_data['totalpoints']);
    }
}
