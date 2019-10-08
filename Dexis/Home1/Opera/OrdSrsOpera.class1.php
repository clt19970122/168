<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Home\Opera;

/**
 * Description of OrdSrsOpera 分期支付
 *
 * @author jiklee
 */
class OrdSrsOpera {

    private $fees;
    //
    private $data;
    private $arrs;
    private $orde;
    private $nums;  //库存数量
    //
    private $user;

    public function __construct() {
        $sysconfig = M("sysconfig");
        #
        $where["models"] = "CONF_VISITER";
        $info = $sysconfig->where($where)->find();
        $this->fees = $info["value"];
    }

    ////////////////////////////////////////////////////////////////////////////

    /**
     * 入口
     * @param type $data
     */
    public function runs($data) {
        $this->data = $data;
        return $this->doDataParam();
    }

    /**
     * 组合数据
     */
    private function doDataParam() {
        $ret = explode("&", $this->data);
        foreach ($ret as $k => $v) {
            $info = explode("=", $v);
            $this->arrs[$info[0]] = urldecode($info[1]);
        }
        return $this->signVerify();
    }

    /**
     * 分期数据验证
     * @param type $param
     * @return type
     */
    private function signVerify() {
        $param = $this->arrs;
        ksort($param);
        foreach ($param as $key => $value) {
            if ($key != "sign" && $value != "" && !is_array($value)) {
                $tmp .= $key . "=" . $value . "&";
            }
        }
        $key = $tmp . "key=" . C("PUM_CONF")["REKEY"];
        $sign = strtoupper(md5($key));
        #
        if ($sign != $param["sign"]) {
            return get_op_res(0, "密钥不匹配", null);
        }
        return $this->updateOrder();
    }

    /**
     * 更新数据
     */
    private function updateOrder() {
        $order_sr = M("order_sr");
        //

        $where["sn"] = $this->arrs["orderNo"];
        $save["status"] = $this->arrs["status"];
        if($this->arrs["status"] ==5){
            $save["passtime"] = time();
            $temps = $order_sr->where($where)->find();
            if($temps['status']==5){
                return get_op_res(0, "验证失败[UPTWOFAIL]", null);
            }
        }
        //修改信息
        if (!$order_sr->where($where)->save($save)) {
            return get_op_res(0, "验证失败[UPERR]", null);
        }
        //未通过申请
        if($this->arrs["status"] == 2){
            $account = M("account");
            #
            $where["id"] = $temps["uid"];
            $info = $account->where($where)->find();
            $wctemp = D("Home/Wctemp", "Logic");
            //发送模版消息
            if ($save["stock"] < 0) {
                $user = ["openid" => $info["openid"], "nickname" => $info["nickname"]];
                $data = ["type" => "failed", "info" => '抱歉，您申请的0元创业未通过，请稍后重新申请',"time"=>date('Y-m-d H:i:s')];
                $wctemp->entrance($user, $data);
            }
        }
        if ($this->arrs["status"] != 5) {
            return get_op_res(1, "验证失败[ST]", null);
        }
        //减少每日抢购库存
        if(!$this->toLessNums($temps['gsn'])){
            return get_op_res(0, "验证失败[DECERR]", null);
        }

        //发送短信
        $plan =M('goods')->where(['gsn'=>$temps['gsn']])->find();
        $verify = D("Verif", "Logic");
        $res =$verify->PlanSms($temps['phone'],$plan['name'],$temps['money'],date('Y-m-d H:i:s'),date('Y-m-d H:i:s',strtotime('+1 month -1day')));//电话  计划  价格  时间 还款时间
        #
        $this->orde = $temps;
        // $this->nums = $temps["money"] > 356 ? 40 : 1;
        if($temps["money"]==168){
            $this->nums = 1;
        }elseif($temps["money"]==600||$temps["money"]==480){
            $this->nums = 4;
        }elseif($temps["money"]==1560||$temps["money"]==1200){
            $this->nums = 12;
        }elseif($temps["money"]==3800||$temps["money"]==3000){
            $this->nums = 40;
        }
        //处理逻辑
        if (!$this->getUserInfo($temps["uid"])) {
            return get_op_res(0, "验证失败[CEILERR]", null);
        }

        return get_op_res(1, "验证成功", null);
    }

    ////////////////////////////////////////////////////////////////////////////

    /**
     * 获取用户信息
     */
    public function getUserInfo($uid) {
        $account = M("account");
        #
        $where["id"] = $uid;
        $info = $account->where($where)->find();
        if ($info == null) {
            return false;
        }
        //
        $update["stock"] = $info["stock"] + $this->nums;
        if (!$account->where($where)->save($update)) {
            return false;
        }
        //立即更新用户的代理等级，积分。
//        cgUserLevel($uid, $this->nums);
        changeUserLevel($uid, $this->nums);
        cgUserPoint($uid, $this->nums, $this->orde["sn"]);
        #
        if ($info["recid"] == null || $info["recid"] == 0) {
            ###添加记录----2018-8-15 add
            $add_nums=[
                'uid'=>$uid,
                'aboutid'=>0,
                'sn'=>$this->orde["sn"],
                'nums'=>$this->nums,
                'after'=>0,
                'uafter'=>$update["stock"],
                'time'=>time(),
                'type'=>11, //系统购买
            ];
            addAcc_nums($add_nums);
            return true;
        }
        $info = $account->where($where)->find();
        $this->user = $info;
        return $this->doShare();
    }

    /**
     * 处理分成
     */
    private function doShare(){
        $account = M("account");
        #
        $cond["sysid"] = $this->user["recid"];
        //获取上级信息
        $upInfo = $account->where($cond)->find();
        if ($upInfo == null) {
            ###添加记录----2018-8-15 add
            $add_nums=[
                'uid'=>$this->user['id'],
                'aboutid'=>0,
                'sn'=>$this->orde["sn"],
                'nums'=>$this->nums,
                'after'=>0,
                'uafter'=>$this->user["stock"],
                'time'=>time(),
                'type'=>11, //金融购买
            ];
            addAcc_nums($add_nums);
            return true;
        }

//        if ($this->nums == 40) {
            //高拉低
            if ($upInfo['level'] > $this->user["level"]) {

                //修改上级分成
                $this->doShareFinish($upInfo, $this->orde['money'], $this->nums);
                //扣除上级的库存
                $this->doCeilStock($upInfo);
               /* //由系统发货，上级获得收益
                $up2Info = $account->where(array('sysid' => $upInfo['recid']))->find();
                if (isset($up2Info['id']) && $up2Info['level'] > 0) {
                    $this->doShareFinish($up2Info, 0, $this->nums);   //第二级分成。
                    $up3Info = $account->where(array('sysid' => $up2Info['recid']))->find();
                    if (isset($up3Info['id']) && $up3Info['level'] > 0) {
                        $this->doShareFinish($up3Info, 0, $this->nums);   //第三级分成。
                    }
                }*/
                 /* if ($this->nums == 40) {    //0元创业（3960）
                      if ($upInfo['level'] == 3) {    //上级为代理
                            $this->doShareFinish($upInfo, 7, $this->nums);   //上级为黄金返利，系统扣库存
                      }else{
                            $this->doShareFinish($upInfo, 75, $this->nums);   //第一级分成。
                            $this->doCeilStock($upInfo);
                      }

                  }elseif ($this->nums == 1) {
                    $this->doShareFinish($upInfo, 168, $this->nums);   //第一级分成。
                    $this->doCeilStock($upInfo);
                  }elseif ($this->nums == 4) {
                    $this->doShareFinish($upInfo, 120, $this->nums);   //第一级分成。
                    $this->doCeilStock($upInfo);
                  }elseif ($this->nums == 12) {
                    $this->doShareFinish($upInfo, 100, $this->nums);   //第一级分成。
                    $this->doCeilStock($upInfo);
                  }
            } else {    //上级为游客
                //由系统发货，上级没有收益
                  if ($upInfo['level'] == 1) {    //上级为代理
                    if ($this->nums == 12) {    //0元创业（3960）
                        $this->doShareFinish($upInfo, 10, $this->nums);   //第一级分成。
                    }elseif ($this->nums == 40) {
                        $this->doShareFinish($upInfo, 7, $this->nums);   //第一级分成。
                    }
                  }elseif ($upInfo['level'] == 2) {
                    if ($this->nums == 12) {    //0元创业（3960）
                        $this->doShareFinish($upInfo, 10, $this->nums);   //第一级分成。
                    }elseif ($this->nums == 40) {
                        $this->doShareFinish($upInfo, 7, $this->nums);   //第一级分成。
                    }
                  }elseif ($upInfo['level'] == 3) {
                    if ($this->nums == 12) {    //0元创业（3960）
                        $this->doShareFinish($upInfo, 7, $this->nums);   //第一级分成。
                    }
                  }
          
            }*/
        } else {
            //低拉高
//            if ($upInfo['level'] > 0) {    //上级为代理
                $acc_level = M("acc_level");
                #等级查询
                $leSel["id"] = $this->user["level"];
                //查询上家等级信息
                $lev = $acc_level->where($leSel)->find();
                //返利 --上级的等级大于月体验
                /*if($upInfo["level"]>2) {
                    $this->doShareFinishS($upInfo, $this->orde['money'], $this->nums);
                }*/

                // start 2018-11-26 18:51:42 add
                /*
                 * 上级小于黄金时 返利到0、1、2 其中游客要购买 每盒反10元
                 * 上级等于大于黄金时 不给小于黄金的返利
                 * */
                if ($this->user['level'] < 3) {
                    //上级等级大于黄金
                    if ($upInfo["level"] < 3) {
                        if ($upInfo['level'] == 0 && $upInfo['totalpoints'] == 0) {
                            //不给返利到未购买游客
                            $lev['first'] =0;
                        }else{
                            $this->doShareFinishS($upInfo, $this->orde['money'], $this->nums);
                        }
                    }
                }else{
                    if($upInfo["level"]>2) {
                        //返利给到上级
                        $this->doShareFinishS($upInfo, $this->orde['money'], $this->nums);
                    }else{
                        //如果上级没达到返利的条件
                        $should_pay =gettop($upInfo["recid"],3);
                        if($should_pay['level']<=$this->user["level"]){
                            //用户和这个获得返利人等级相同后进行返利的操作
                            //如果大于黄金的
                            if (!cgUserMoney($should_pay["id"], $lev['first']*$this->nums, 1, "REBACK", $this->orde["sn"])) {
                                return false;
                            }
                            //增加利润记录 2018年12月11日11:31:23
                            addProfitList( $lev['first']*$this->nums,$should_pay['id'],$this->orde["sn"],2,1);

                        }else{
                            //返利金额
                            $lev['first'] =0;
                        }
                    }
                }
                //end

                //扣除上级库存 寻找比他等级高的
                $res_info =getHigherThanNow($this->user["recid"],$this->user['level']);
                if($res_info) {
                    $this->doCeilStock($res_info,$lev['first']);
                }

//            } else {    //上级为游客
//                //由系统发货，上级获得收益90
//                $this->doShareFinishS($upInfo, 0);
//            }
        }

        return true;
    }

    /**
     * 高拉低
     * 完成分成 20180403 --------change at 2018-7-31 10:23:30
     * @param type $tmp
     * @return boolean
     */
    private function doShareFinish($tmp, $money, $num) {
        #积分返点
      /*  if($tmp['level']<$this->user['level']){
            if (!cgUserPoint($tmp["id"], $this->data["gnums"], $this->data["sn"])) {
                return false;
            }
        }
        //有足够的积分就进行升级
        if(!$this->haveEnoughtTotalPointToUpLevel($tmp["id"])){
            return false;
        }*/
        //
        $goods_level = M("goods_level");
        #
//        $goods = getIDGoodsInfo($this->data["gid"]);
        $where["gsn"] ='415244730815416283';
        $where["lid"] = $tmp["level"];
        $userprice = $goods_level->where($where)->find();
        //利润
        $rllmoney =$money-($userprice['price']*$num);
        #现金返点
        $roll = $money;
        /*if ($roll > 0) {
            if (!cgUserMoney($tmp["id"], $roll, 1, "REBACK", $this->orde["sn"])) {
                return false;
            }
        }*/
        #业绩计算
        $account = M("account");
        #
        $wheres["id"] = $tmp["id"];
        $info = $account->where($wheres)->find();
        if ($info["level"] > 0) {
            $save["groups"] = $info["groups"] + $roll;
            $save["person"] = $info["person"] + $rllmoney;
        } else {
            $save["person"] = $info["person"] +$rllmoney;
        }
        $save["uptimes"] = time();
        return $account->where($wheres)->save($save);
    }

    /**
     * 低拉高
     * 完成分成 20180403  --change at 2018-7-31 10:42:37
     * @param type $tmp
     * @return boolean
     */
    private function doShareFinishS($tmp, $money,$num) {
       /* #积分返点
        if (!cgUserPoint($tmp["id"], $this->nums, $this->data["sn"])) {
            return false;
        }
        //有足够的积分就进行升级
        if(!$this->haveEnoughtTotalPointToUpLevel($tmp["id"])){
            return false;
        }*/

        //上级获得返利金额
        $acc_level = M("acc_level");
        #等级查询
        //$leSel["id"] = $tmp["level"];
        $leSel["id"] = $tmp["level"];
        //查询上家等级信息
        $lev = $acc_level->where($leSel)->find();
        if ($lev == null) {
            $share =$this->fees;
        } else {
            $share = $lev["first"];
        }

        #现金返点
        $roll = $share * $num;
        if ($roll > 0) {
            if (!cgUserMoney($tmp["id"], $roll, 1, "REBACK", $this->orde["sn"])) {
                return false;
            }
        }
        //增加利润记录 2018年12月11日11:31:23 返利
        addProfitList( $roll,$tmp['id'],$this->orde["sn"],2);

        #业绩计算
        $account = M("account");
        #
        $where["id"] = $tmp["id"];
        $info = $account->where($where)->find();
        if ($this->user["level"] > 0) {
            $save["groups"] = $info["groups"] + $money;
            $save["person"] = $info["person"] + $roll;
        } else {
            $save["person"] = $info["person"] + $roll;
        }
        $save["uptimes"] = time();
        return $account->where($where)->save($save);
    }

    /**
     * 完成分成
     * @param type $tmp
     * @return boolean
     */
    private function doShareFinish1($tmp, $level) {
        #积分返点
        if (!cgUserPoint($tmp["id"], 1, $this->orde["sn"])) {
            return false;
        }
        #现金返点
        $roll = $this->getLevMon($tmp, $level) * 1;
        if ($roll > 0) {
            if (!cgUserMoney($tmp["id"], $roll, 1, "REBACK", $this->orde["sn"])) {
                return false;
            }
        }
        #业绩计算
        $account = M("account");
        #
        $where["id"] = $tmp["id"];
        $info = $account->where($where)->find();
        if ($this->user["level"] > 0) {
            $save["groups"] = $info["groups"] + $roll;
        } else {
            $save["person"] = $info["person"] + $roll;
        }
        $save["uptimes"] = time();
        return $account->where($where)->save($save);
    }

    

    //------------------------------------------------------------------------//

    /**
     * 获取升级金额
     */
    private function getLevMon($tmp, $v) {
        $acc_level = M("acc_level");
        #等级查询
        $leSel["id"] = $tmp["level"];
        $lev = $acc_level->where($leSel)->find();
        if ($lev == null) {
            $share = array($this->fees, $this->fees, $this->fees);
        } else {
            $share = array($lev["first"], $lev["second"], $lev["third"]);
        }
        #
        if ($tmp["level"] <= $this->user["level"]) {
            return $share[$v];
        }
        return $this->getCeil($tmp["level"], $v);
    }

    /**
     * 获取购物等级差价
     */
    private function getCeil($v, $loc) {
        $goods_level = M("goods_level");
        #
        $goods = getGoodsInfo($this->orde["gsn"]);
        $where["gsn"] = $goods["gsn"];
        $where["lid"] = $this->user["level"];
        $user = $goods_level->where($where)->find();
        if ($user == null) {
            $user["price"] = $goods["price"];
        }
        #
        $where["lid"] = $v;
        $parent = $goods_level->where($where)->find();
        if ($parent == null) {
            return 0;
        }
        #
        $ceil = $user["price"] - $parent["price"];
        if ($loc > 0 && $v > 0) {
            $ceil = $ceil - $this->fees;
        }
        return abs($ceil);
    }


    /**
     * @param $info
     * @param int $lev 给上级返的每盒多少钱
     * @return bool
     */
    private function doCeilStock($info, $lev=0) {
        $account = M("account");
        $wctemp = D("Home/Wctemp", "Logic");
        #
        //当前用户的
        $now_user=$account->where(['id'=>$this->user['id']])->find();
        $uafter =$now_user['stock'] ;
        #
        $goods_level = M("goods_level");
        #
//        $goods = getIDGoodsInfo($this->data["gid"]);
        $where["gsn"] ='415244730815416283';
        $where["lid"] = $info["level"];
        $userprice = $goods_level->where($where)->find();

        $where["sysid"] = $info['sysid'];
        $save["stock"] = $info["stock"] - $this->nums;
        $save["person"] = $info["person"] + ($this->orde['money']-($this->nums*($userprice['price']+$lev)));

        #现金返点
        $roll = $this->orde['money'] -($this->nums*$lev);
        if ($roll > 0) {
            if (!cgUserMoney($info["id"], $roll, 1, "REBACK", $this->orde["sn"])) {
                return false;
            }
        }
        //增加利润记录 2018年12月11日11:31:23 返利
        addProfitList( $save["person"],$info['id'],$this->orde["sn"],1);

//        $save["money"] = $info["money"] + $this->orde['money'];
        if (!$account->where($where)->save($save)) {
            return false;
        }
        ###添加记录----2018-8-15 add
        $add_nums=[
            'uid'=>$this->user['id'],
            'aboutid'=>$info['id'],
            'sn'=> $this->orde["sn"],
            'nums'=>$this->nums,
            'after'=>$save["stock"],
            'uafter'=>$uafter,
            'time'=>time(),
            'type'=>11, //金融购买
        ];
        addAcc_nums($add_nums);

        //发送模版消息
        if ($save["stock"] < 0) {
            $user = ["openid" => $info["openid"], "nickname" => $info["nickname"]];
            $data = ["type" => "tips", "count" => $save["stock"]];
            $wctemp->entrance($user, $data);
        }
        return true;
    }

    /**
     * 扣减B端库存
     */
    private function doCeilStock_bak($id) {
        $account = M("account");
        $wctemp = D("Home/Wctemp", "Logic");
        #
        $where["sysid"] = $id;
        for ($i = 0; $i < 3; $i++) {
            $info = $account->where($where)->find();
            if ($info["level"] <= 0) {
                $where["sysid"] = $info["recid"];
                continue;
            }
            $save["stock"] = $info["stock"] - $this->nums;
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
        return cgUserLevel($id,$res_data['totalpoints']);
    }
    ///------------2018-8-3 16:18 add ----------------
    //购买成功减少今日的套餐库存量
    public function  toLessNums($gsn){
        $goods=M('goods');
        $where['gsn'] =$gsn;
        $res =$goods->where($where)->setDec('nums',1);//库存减一
        $res =$goods->where($where)->setinc('sales',1);//销量加一
        if(!$res){
            return false;
        }
        $good_info =$goods->where($where)->find();
        if(!$good_info){
            return false;
        }
        if($good_info['nums']<=0){
            return true;
        }
        return true;
    }
}
