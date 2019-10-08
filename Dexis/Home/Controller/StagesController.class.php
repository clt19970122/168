<?php
/**
 * Created by tnb.
 * User: uuu
 * Date: 2019/5/10
 * Time: 14:07
 */


namespace Home\Controller;

#use Think\Controller;

class StagesController extends CommController {
    public $info;
    public $gsn;

    public function _initialize() {
        parent::_initialize();
        layout("laynot");
        $ssid = session("ssid_dexis");
        if ($ssid == null && CONTROLLER_NAME != "Login") {
            $this->redirect("Login/index");
        }
    }

    /**
     *用户端进入申请得步骤
     * data['gsn',level,nums]
     */
    public function index(){
        $data =I('post.');
        $uid =$this->ssid;
        $this->gsn =$data['gsn'];
        $stages =M('acc_stages');
        $acc_level =M('acc_level');
        $goods_level =M('goods_level');
        //查询是否存在分期
        $sta_info =$stages->where(['uid'=>$uid,'status'=>array('in','0,1'),'type'=>2])->find();
        if($sta_info){
            return get_op_put(0, '现存在分期正在处理,暂时不能重复申请');
        }
        //获取等级的数量
        $level_info=$acc_level->where(['id'=>$data['level']])->find();
        //支付金额
        $goods_info =$goods_level->where(['gsn'=>$data['gsn'],'lid'=>$data['level']])->find();
        $all_pay =$goods_info['price'] * $level_info['months'];
        //生成订单号
        $order_sn ='ST'.date('YmdHis',time()).rand(1000,9999);
        $every_pay =(int)$all_pay/$data['nums'];
        $save =[
            'uid'=>$uid,
            'level'=>$data['level'],
            'payway'=>$data['nums'],
            'every_pay'=>$every_pay,
            'pay_time'=>date('Y-m-d H:i:s',time()),
            'add_time'=>time(),
            'status'=>0,
            'type'=>2,
            'nums'=>2,
            'sn'=>$order_sn,
        ];
        $res =$stages->add($save);
//        如果添加成功并且是钻石;执行支付
        if($res ){
            if( $data['level']==5){
                $this->toPay($res,$every_pay,1,$goods_info['price'],$data['level']);
            }else{
                return get_op_put(0, "已提交申请,稍后客服(16445111)会联系您");
            }
        }else{
            return get_op_put(0, "网络错误,请刷新重试[ERROR_INT01]");
        }
    }

    //进行支付
    /**支付逻辑
     * @param $id @申请分期的id
     * @param $money @支付的金额
     * @param $times @支付的期数
     * @param $price @支付的单价
     * @param $level @申请的等级
     */
    public function toPay($id,$money,$times,$price,$level){
        //执行支付之前--添加分期的支付数据
        $acc_st_pay =M('acc_st_pay');
        $sn ='ST'.getOrdSn();
        $nums =ceil($money /$price);
        //添加到分期支付中去
        $add_data =[
            'uid'=>$this->ssid,
            's_id'=>$id,
            'status'=>0,
            'time'=>time(),
            'money'=>$money,
            'no'=>$times,
            'sn'=>$sn,
            'nums'=>$nums,
        ];
        $add_res =$acc_st_pay->add($add_data);
        if(!$add_res){
            return get_op_put(0, "支付异常,请稍后再试[ERR_099]");
        }
        #生成一条订单
        $orders =M('orders');

        $goods = getGoodsInfo($this->gsn);
        $post["gid"] = $goods["id"];
        $post["gname"] = $goods["name"];
        $post["gimgs"] = $goods["imgs"];
        $post["gprice"] = $price;
//        $post["trans_pay"] = 0;
        $post["buy_level"] = $level;
        $post["money"] =$money;
        $post["fees"] = 0;
        $post["status"] = 0;
        $post["times"] = time();
        $post["sn"] = $sn;
        $post["uid"] = $this->ssid;
        $post["gnums"] =$nums;

        if (!$orders->add($post)) {
            return get_op_put(0, "网络异常，请稍候再试[0X002]");
        }
        return get_op_put(1, null, U('Malls/payer', 'sn=' . $post["sn"]));

//        $mall =new MallsController();
//        $mall->payer($post["sn"]);
//        $this->redirect(U('Malls/payer','sn='.$post["sn"]));
//        $this->redirect('home/Malls/payer/sn/'.$post["sn"]);

        #支付
        /*$wcpay = D("Home/WcPay", "Logic");
        $info=I('sn');
        $res_data =M('acc_stages')->where(['id'=>$id])->find();
        if($res_data['trans_pay']=null){
            return get_op_put(1, "支付异常,请稍后再试[ERR_01]");
        }
        //支付金额
        $money =$res_data['every_pay'];
        if($money!=0) {
            $put["sn"] = $info;
            $put["alt"] = "ST_ORDER";
            $put["money"] = $money;
            $put["openid"] = $this->wecs["openid"];
            $res = $wcpay->entrance($put);
            if (!$res) {
                // $this->redirect('users/getStocklist');
                return get_op_put(1, "支付异常,请稍后再试");
            }
            //  $this->redirect('users/getStocklist');
            return get_op_put(1, null, $res);
        }else {
            return get_op_put(1, "支付异常,请检查网络[ERR_03]");
        }*/
    }


    #
    /**
     *支付成功后返利逻辑
     */
    public function PaySuccess1($sn){
        $gsn ='415244730815416283';
        $get =[];

        $acc_st_pay=M('acc_st_pay');
        $account=M('account');
        //订单信息
        $st_info =$acc_st_pay->where(['sn'=>$sn])->find();
        //当前用户信息
        $userinfo =$account->where(['id'=>$st_info['uid']])->find();
        //获取上级
        $up_user =$account->where(['sysid'=>$userinfo['recid']])->find();

        $get['level'] =$userinfo['level'];
        $get['stock'] =$st_info['nums'];

        $buyer_price =doPrice($gsn,$get['level']);
        //高-》拉低
        if($up_user['level']>$get['level']){
            //获取价格差
            $res_price =doPrice($gsn,$up_user['level']);
            $preson=($buyer_price-$res_price)*$get['stock'];
            $addmoney=$get['stock']*$buyer_price;
            //添加金额
            cgUserMoney($up_user["id"], $addmoney, 1, "REBACK", $sn);
            //扣除库存
            $account->where(['id'=>$up_user["id"]])->setDec('stock',$get['stock']);
            //增个人金额
            $account->where(['id'=>$up_user["id"]])->setInc('person',$preson);
//记录利润
            addProfitList($preson,$up_user["id"],$sn,1);
            //增团队额
            $account->where(['id'=>$up_user["id"]])->setInc('groups',$addmoney);
            //添加记录
            $add_nums = [
                'uid' => $userinfo['id'],
                'aboutid' =>$up_user['id'],
                'sn' => $sn,
                'nums' => $get["stock"],
                'after' =>$up_user['stock']-$get['stock'],
                'uafter' => $userinfo["stock"]+$get["stock"],
                'time' => time(),
                'type' => 13, //后台添加
            ];
            addAcc_nums($add_nums);
        }else { //低 --》高
            #
            if($get['level'] <6) {
                #
                #  //返利到上一级
                $acc_level = M("acc_level");
                #等级查询
//                $leSel["id"] = $up_user["level"];
//                $leSel["id"] = $userinfo["level"];
                //当用户的等级小于黄金的时候
                $res_money =0;

                if($up_user["level"]>=3) {
                    $leSel["id"] = $get['level'];
                    //查询上家等级信息
                    $lev = $acc_level->where($leSel)->find();
                    $level_money = $lev["first"];
                    $res_money = $get['stock'] * $level_money;
                    //增加金额
                    cgUserMoney($up_user["id"], $res_money, 1, "REBACK", $sn);
                    //增个人金额
                    $account->where(['id' => $up_user["id"]])->setInc('person', $res_money);
                    //记录利润
                    addProfitList($res_money,$up_user["id"],$sn,2);
                    //增团队额
                    $account->where(['id' => $up_user["id"]])->setInc('groups', $res_money);
                }

                #
                $res_data = getHigherThanNow($userinfo['recid'], $get['level']);
                if ($res_data&&$res_data['level'] > $get['level']) {
                    //返回的等级大于当前的等级
                    $addmoney = $get['stock']*$buyer_price-$res_money;
                    //获取价格差
                    $res_price =doPrice($gsn,$res_data['level']);
                    $preson=($buyer_price-$res_price)*$get['stock'];
                    //增加金额
                    cgUserMoney($res_data["id"], $addmoney, 1, "REBACK", $sn);
                    //扣除库存
                    $account->where(['id'=>$res_data["id"]])->setDec('stock',$get['stock']);
                    //增个人金额
                    $account->where(['id'=>$res_data["id"]])->setInc('person',$preson);
                    //记录利润
                    addProfitList($preson,$res_data["id"],$sn,2);
                    //增团队额
                    $account->where(['id'=>$res_data["id"]])->setInc('groups', $get['stock']*$buyer_price);
                    //添加记录
                    $add_nums = [
                        'uid' => $userinfo['id'],
                        'aboutid' => $res_data['id'],
                        'sn' => $sn,
                        'nums' => $get["stock"],
                        'after' => $res_data["stock"]-$get["stock"],
                        'uafter' => $userinfo["stock"]+$get["stock"],
                        'time' => time(),
                        'type' => 13, //后台添加
                    ];
                    addAcc_nums($add_nums);
                }
            }else {
                //上级是联创
                if($up_user['level']==$get['level']){

                    $addmoney = $get['stock']*5;
                    //增加金额
                    cgUserMoney($up_user["id"], $addmoney, 1, "REBACK", $sn);
                    //增个人金额
                    $account->where(['id'=>$up_user["id"]])->setInc('person',$addmoney);
                    //记录利润
                    addProfitList($addmoney,$up_user["id"],$sn,2);
                    //增团队额
                    $account->where(['id'=>$up_user["id"]])->setInc('groups',$addmoney);
                    $two_data = gettop($up_user['recid'], $get['level']);
                    if($two_data &&$two_data['level']==$get['level']) {
                        $addmoney = $get['stock'] * 2.5;
                        //增加金额
                        cgUserMoney($two_data["id"], $addmoney, 1, "REBACK", $sn);
                        //增个人金额
                        $account->where(['id' => $two_data["id"]])->setInc('person', $addmoney);
                        //记录利润
                        addProfitList($addmoney,$up_user["id"],$sn,2);
                        //增团队额
                        $account->where(['id' => $two_data["id"]])->setInc('groups', $addmoney);
                    }
                }else{
                    if($up_user["level"]>=3) {
                        #  //返利到上一级
                        $res_money = $get['stock'] * 2.5;
                        //增加金额
                        cgUserMoney($up_user["id"], $res_money, 1, "REBACK", $sn);
                        //增个人金额
                        $account->where(['id' => $up_user["id"]])->setInc('person', $res_money);
                        //记录利润
                        addProfitList($res_money,$up_user["id"],$sn,2);
                        //增团队额
                        $account->where(['id' => $up_user["id"]])->setInc('groups', $res_money);
                    }
                    else{
                        //上一级小于黄金 然后找上一级
                        $should_get  = gettop($up_user['recid'], 3);
                        #  //返利到上一级
                        $res_money = $get['stock'] * 2.5;
                        //增加金额
                        cgUserMoney($should_get["id"], $res_money, 1, "REBACK", $sn);
                        //增个人金额
                        $account->where(['id' => $should_get["id"]])->setInc('person', $res_money);
                        //记录利润
                        addProfitList($res_money,$should_get["id"],$sn,2);
                        //增团队额
                        $account->where(['id' => $should_get["id"]])->setInc('groups', $res_money);
                    }
                    # 上级的联创
                    $two_data = gettop($up_user['recid'], $get['level']);
                    if($two_data &&$two_data['level']==$get['level']) {
                        //增加金额
                        cgUserMoney($two_data["id"], $res_money, 1, "REBACK", $sn);
                        //增个人金额
                        $account->where(['id' => $two_data["id"]])->setInc('person', $res_money);
                        //记录利润
                        addProfitList($res_money,$two_data["id"],$sn,2);
                        //增团队额
                        $account->where(['id' => $two_data["id"]])->setInc('groups', $res_money);
                        $three_data = gettop($two_data['recid'], $get['level']);
                        if($three_data &&$three_data['level']==$get['level']) {
                            //增加金额
                            cgUserMoney($three_data["id"], $res_money, 1, "REBACK", $sn);
                            //增个人金额
                            $account->where(['id' => $three_data["id"]])->setInc('person', $res_money);
                            //记录利润
                            addProfitList($res_money,$three_data["id"],$sn,2);
                            //增团队额
                            $account->where(['id' => $three_data["id"]])->setInc('groups', $res_money);
                        }
                    }
                }
                //添加记录
                $add_nums = [
                    'uid' => $userinfo['id'],
                    'aboutid' => 0,
                    'sn' =>$sn,
                    'nums' => $get["stock"],
                    'after' => 0,
                    'uafter' => $userinfo["stock"]+$get["stock"],
                    'time' => time(),
                    'type' => 13, //后台添加
                ];
                addAcc_nums($add_nums);
            }

        }


        //增加积分记录
        cgUserPoint($userinfo['id'], $get["stock"], $sn);
        //修改记录
        $update["stock"]= array("exp", "stock+" . $get["stock"]);
//        $update["points"]= array("exp", "points+" . $get["stock"]);
//        $update["totalpoints"]= array("exp", "totalpoints+" . $get["stock"]);
        $update["level"]= $get['level'];
        //增记录 2018-10-10 18:32:57 add
        $paymoney =$buyer_price * $get["stock"];
        if (!cgUserMoney($userinfo['id'],$paymoney, 0, "ORDER", $sn, false)) {
//            return get_op_put(0, "支付失败，请稍后再试");
            return $this->wechatRollback("FAIL", "网络错误[INTER_ERR_02]");
        }
        $res =$account->where(['id'=>$get['ids']])->save($update);
        #
        #模板消息推送
        //当购买成功之后 用户库存增加 给用户发送模板消息
        //注释时间2019年3月25日15:12:29
        /*$wctemp = D("Home/Wctemp", "Logic");
        $user = ["openid" => $userinfo["openid"], "nickname" => $userinfo["nickname"]];
        $data = ["type" => "proin", "product" => '168太空素食', "nums" => $get["stock"],"stock" => $userinfo["stock"]+$get["stock"]];
        $wctemp->entrance($user, $data);*/
        #
        #
        if($res){
            //如果用户等级更变后--记录升级
            if($userinfo['level'] != $update["level"]){
                $nums =getNumsByLevel($update["level"]);
                $arr =[
                    'uid'=>$userinfo['id'],
                    'dotype'=>'用户升级',
                    'before'=>$userinfo['level'],
                    'after'=>$update["level"],
                    'time'=>time(),
                    'level_num'=>$nums,
                ];
                addLevelRecode($arr);
            }
//            return get_op_put(0, "处理成功");


            return $this->wechatRollback("FAIL", "网络错误[INTER_ERR]");
        }
        else{

            return $this->wechatRollback("FAIL", "订单错误");
//            return get_op_put(0, "处理失败");
        }
    }

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

    /**购买成功
     * @param $sn
     */
    public function paySuccess($sn){
        $stages =M('acc_stages');
        $acc_st_pay =M('acc_st_pay');
        $pay_info =$acc_st_pay->where(['sn'=>$sn])->find();
        if($pay_info){
            $save['status']=1;
            $res_data =$acc_st_pay->where(['sn'=>$sn])->save($save);
            if($res_data){
                $sta_info =$stages->where(['id'=>$pay_info['s_id']])->find();
                if($sta_info){
                    $sta_info =$stages->where(['id'=>$pay_info['s_id']])->save($save);
                }

            }
        }
    }
}