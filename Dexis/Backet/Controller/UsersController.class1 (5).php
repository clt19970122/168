<?php

namespace Backet\Controller;

#use Think\Controller;

class UsersController extends CommController {

    /**
     * 用户列表
     */
    public function index() {
        $account = M("account");
        $get = I("get.");
        $acc_level = M("acc_level");
        #
        if($get['start']||$get["ends"]){
            $start = strtotime($get["start"]);
            $ends = strtotime($get["ends"]);
            if(!$ends){
                $where["times"] = array("gt", $start);
            }if(!$start){
                $where["times"] = array("lt", $ends);
            }
            if( $ends > $start) {
                $where["times"] = array(array("gt", $start), array("lt", $ends+86400));
            }
            if ($ends == $start){
                $where["times"] = array(array("gt", $start), array("lt", $ends+86400));
            }
        }
        $get["phone"] != null ? $where["phone"] = array("like", "%" . $get["phone"] . "%") : null;
        $get["nickname"] != null ? $where["nickname"] = array("like", "%" . $get["nickname"] . "%") : null;
        $get["name"] != null ? $where["name"] = array("like", "%" . $get["name"] . "%") : null;
        $get["level"] != null ? $where["level"] = $get["level"] : null;
        //上级系统号
        if( $get["up_nickname"] != null ){
            $where['recid'] = $get["up_nickname"];
        }
        #
        $list = poPage($account, $where);
        foreach ($list['list'] as $k=>$v){
            $list['list'][$k]['parent_name'] ='';
            $list['list'][$k]['parent_level'] ='';
            if($v['recid']!=null||$v['recid']!=0){
                //修改带有。html的recid
                $recid =str_replace('.html','',$v['recid']);
                $account->where(['id'=>$v['id']])->save(['recid'=>$recid]);
                $parent_info =$account->where(array('sysid'=>$recid))->find();
                $list['list'][$k]['parent_name'] =$parent_info['nickname'];
                $list['list'][$k]['parent_level'] =$parent_info['level'];
                $list['list'][$k]['parent_real'] =$parent_info['name'];
                $list['list'][$k]['parent_sysid'] =$parent_info['sysid'];
            }
        }
        $this->assign("list", $list);
        $this->assign("select", $get);
        $this->assign("level", $acc_level->select());
        $this->display();
    }

    /**
     *获取团队人员 2018-11-26 13:59:26 add
     */
    public function getTeam(){
        ini_set('memory_limit', '-1');
        $acc_level = M("acc_level");
        $account =M('account');
        $ssid =I('ssid');
        $level =I('level');
        $get =I('get.');

        $user =$account->where(['sysid'=>$ssid])->field('nickname,name')->find();
//        $alldata=$account->field('id,recid,sysid,nickname,name,phone,status,level,times,stock,totalpoints,money')->where(['recid'=>$ssid])->select();
        $alldata=$account->field('id,recid,sysid,nickname,name,phone,status,level,times,stock,totalpoints,money')->where(['recid'=>$ssid])->select();
//        $arrr=category($alldata,$ssid,0);
//        $arrr=getChilds($html,$ssid,$alldata,0);
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
        $level_data=array();
        foreach ($list['list'] as $k=>$v){
            $level_data[$v['level']] +=1;
            $list['list'][$k]['parent_name'] ='';
            $list['list'][$k]['parent_level'] ='';
            //判断等级
            if($level==$v['level']){
                if ($v['recid'] != null || $v['recid'] != 0) {
                    //修改带有。html的recid
                    $recid = str_replace('.html', '', $v['recid']);
                    $account->where(['id' => $v['id']])->save(['recid' => $recid]);
                    //
                    $parent_info = $account->where(array('sysid' => $recid))->find();
                    $list['list'][$k]['parent_name'] = $parent_info['nickname'];
                    $list['list'][$k]['parent_level'] = $parent_info['level'];
//                    $list['list'][$k]['parent_level'] = getUserLevel(['level']);
                }
            }else{
                unset($list['list'][$k]);
            }
        }

        $this->assign("list", $list);
        $this->assign("level", $acc_level->select());
        $this->assign("user", $user['nickname']);
        $this->assign("count_nums",$count_nums);
        $this->assign("get",$get);
        $this->assign("levelData", $level_data);
        $this->display();
    }

    /**
     *获取用户金额数量变动记录
     */
    public function getList(){
        $acc_money = M('acc_money');
        $acc_nums = M('acc_nums');
        $account = M('account');
        $syspansn = M('syspaysn');
        $order= M('orders');
        $id =I('id');
        $p1 =I('p');
        $p2 =I('p2');
//        $start=I('start');
//        $ends=I('ends');
        //查询金额
        $where_money['uid'] =$id;
//        $where_money['times'] =array(array('egt',strtotime($start)),array('elt',strtotime($ends)));
        $count      = $acc_money->where($where_money)->count();// 查询满足要求的总记录数
        $Page       = new \Think\Page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show1       = $Page->show();// 分页显示输出
        $money_data =$acc_money->where($where_money)->page($p1,20)->order('id desc')->select();
        $res_data=[];
        foreach ($money_data as $k=>$v){
            $order_info =$order->where(['sn'=>$v['sn']])->find();
            $have_pay=$syspansn->where(['resn'=>$v['sn']])->find();
            $nums_data =$acc_nums->where(['sn'=>$v['sn']])->find();
            $res_data[$k]['sn'] =$v['sn'];
            $res_data[$k]['in_name'] =getUserInf($nums_data['uid'],'name');
            $res_data[$k]['change_money'] =$v['models']!='REBACK'? '<span style="color: #b2282c;font-size: 18px">-' .$v['money'].'</span>': '<span style="color: #3ab22c;font-size: 18px">+' .$v['money'].'</span>';
            $res_data[$k]['before_money'] =$v['befores'];
            $res_data[$k]['after_money'] =$v['afters'];
            $res_data[$k]['times'] =$v['times'];
            $res_data[$k]['do'] =$v['models']=='ORDER'?'订单':($v['models']=='REBACK'?'返现':($v['models']=='DRAW'?'提现':'退单'));
            if($nums_data) {
                $res_data[$k]['change_nums'] = $v['models'] == 'ORDER' ? '<span style="color: #3ab22c;font-size: 18px">+' . $nums_data['nums'] . '</span>' : '<span style="color: #b2282c;font-size: 18px">-' . $nums_data['nums'] . '</span>';
            }
            if($v['models']=='DRAW'){
                $money_draw =M('money_draw');
                $money_data =$money_draw->where(['sn'=>$v['sn']])->find();
                $res_data[$k]['money_do'] =$money_data['status']==0?"待支付":($money_data['status']==1?"已支付":'已退回');
            }
            if($v['models']=="ORDER"){
                $reback =$acc_money->where(['models'=>'REBACK','sn'=>$v['sn']])->select();
                $res_data[$k]['order_type'] =$order_info['paytype'];
                foreach ($reback as $v){
                    $reback_user = $account->where(['id' => $v['uid']])->find();
                    $rebk[]='给:'.$reback_user['name'].'-返:'.$v['money'];
                }
                $res_data[$k]['reback_info'] =implode('/',$rebk);
                unset($rebk);
            }
            if($v['sn']!=0){
//                $nums_data =$acc_nums->where(['sn'=>$v['sn']])->find();
                if($nums_data) {
//                    $res_data[$k]['change_nums'] =$v['models']=='ORDER'?'<span style="color: #3ab22c;font-size: 18px">+' .$nums_data['nums'].'</span>': '<span style="color: #b2282c;font-size: 18px">-' .$nums_data['nums'].'</span>';
                    if($id!=$nums_data['uid']&&$id!=$nums_data['aboutid']){
                        $numswhere['uid|aboutid']= $id;
                        $numswhere['id']= array('lt',$nums_data['id']);
                        $nums_data2 =$acc_nums->where($numswhere)->order('id desc')->find();
                        $res_data[$k]['after_nums'] = $nums_data2['uid'] ==$id ? $nums_data2['uafter'] : $nums_data2['after'];
                    }else{
                        $res_data[$k]['after_nums'] = $nums_data['uid'] ==$id ? $nums_data['uafter'] : $nums_data['after'];
                    }
                    //进货的账户
                    if ($nums_data['aboutid'] != 0) {
                        $up_user = $account->where(['id' => $nums_data['aboutid']])->find();
                    }else{
                        $up_user['nickname'] = '<span style="color: red">公司</span>';
                    }
                    $res_data[$k]['up_user'] = $up_user['nickname'];
                    $res_data[$k]['up_level'] = $up_user['level'];
                }
 #
                #
                //查询金融里面是否存在这个订单
                //这个订单是否发货
                $order_sr =M('order_sr');
                $order_drs =M('order_drs');
                $order_info =$order_sr->where(['sn'=>$v['sn']])->find();
                //存在这个单号  查询是否提货
                if($order_info){
                    $drs_where['uid'] =$order_info['uid'];
                    $drs_where['status'] =array('in','3,4,6');
                    $have_pick =$order_drs->where($drs_where)->select();
                    $res_good =array();
                    if($have_pick) {
                       /* if($have_pick['status'] ==4){
                            $res_good = '自提';
                        }else{
                            //存在发货单号
                            if ($have_pick['trans'] != 0) {
                                $res_good = '已发货';
                            }else{
                                $res_good = '未发货';
                            }
                        }*/
                        foreach ($have_pick as $kkk => $vvv) {
                            if ($vvv['status'] == 4) {
                                $res_good[] = '自提';
                            } else{
                                //存在发货单号
                                if ($vvv['trans'] != 0) {
                                    $res_good[] = '已发货';
                                }else{
                                    $res_good[] = '未发货';
                                }
                            }
                        }
                    }else{
                        $res_good[]= '未提货';
//                        $res_good= '未提货';
                    }
                    $res_data[$k]['pick_goods'] =implode(',',$res_good);
//                    $res_data[$k]['pick_goods'] =$res_good;
                    unset($res_good);
                }
                
            }
            if($v['models']=="ORDER") {
                if ($have_pay['status'] == 0) {
                    unset($res_data[$k]);
                }
            }
        }


        //提货记录
        $nums_where['uid']= $id;
        $nums_where['aboutid']= $id;
        $nums_where['_logic']= 'or';
        $count      = $acc_nums->where($where_money)->count();// 查询满足要求的总记录数
        $Page       = new \Think\Page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show2       = $Page->show();// 分页显示输出
        $nums_data =$acc_nums->where($nums_where)
        // ->page($p2,20)
        ->order('id desc')->select();

        $theory_money =0;
        foreach ($nums_data as $k=>$v){
            if($v['uid']==$id){
                if($v['type']==11) {
                    $nums_res[$k]['sn'] =$v['sn'];
                    $nums_res[$k]['nums'] =$v['uid']==$id?'<span style="color: #3ab22c;font-size: 18px">+'.$v['nums'].'</span>':'<span style="color: #b2282c;font-size: 18px">-'.$v['nums'].'</span>';
                    $nums_res[$k]['sn'] =$v['sn'];
                    $nums_res[$k]['times'] =$v['time'];
                    $nums_res[$k]['do'] =$v['type']='金融';
                    $nums_res[$k]['after_nums'] = $v['uid'] == $id? $v['uafter'] : $v['after'];
                    $uinfo = $account->where(['id' => $v['aboutid']])->find();
                    $nums_res[$k]['out_name'] = $uinfo['name'];
                    $nums_res[$k]['out_level'] = $uinfo['level'];
                    $about_info = $account->where(['id' => $v['uid']])->find();
                    $nums_res[$k]['in_name'] = $about_info['name'];
                    $nums_res[$k]['dostatus'] = $v['status'] ==1?"":"已取消";
                    //查询返利
                    $reback =$acc_money->where(['models'=>'REBACK','sn'=>$v['sn']])->select();
                    foreach ($reback as $vv){
                        $reback_user = $account->where(['id' => $vv['uid']])->find();
                        $rebks[]='给:'.$reback_user['name'].'-返:'.$vv['money'];
                    }
                    $nums_res[$k]['reback_infos'] =implode('/',$rebks);
                    unset($rebks);
                }
                if($v['type']==13) {
                    $nums_res[$k]['sn'] =$v['sn'];
                    $nums_res[$k]['nums'] =$v['uid']==$id?'<span style="color: #3ab22c;font-size: 18px">+'.$v['nums'].'</span>':'<span style="color: #b2282c;font-size: 18px">-'.$v['nums'].'</span>';
                    $nums_res[$k]['sn'] =$v['sn'];
                    $nums_res[$k]['times'] =$v['time'];
                    $nums_res[$k]['do'] =$v['type']='现金支付';
                    $nums_res[$k]['after_nums'] = $v['uid'] == $id? $v['uafter'] : $v['after'];
                    $uinfo = $account->where(['id' => $v['aboutid']])->find();
                    $nums_res[$k]['out_name'] = $uinfo['nickname'];
                    $nums_res[$k]['out_level'] = $uinfo['level'];
                    $about_info = $account->where(['id' => $v['uid']])->find();
                    $nums_res[$k]['in_name'] = $about_info['nickname'];
                    $nums_res[$k]['dostatus'] = $v['status'] ==1?"":"已取消";
                }
            }
            if($v['type']>20) {
                $nums_res[$k]['sn'] =$v['sn'];
                $nums_res[$k]['nums'] =$v['uid']==$id?'<span style="color: #b2282c;font-size: 18px">-'.$v['nums'].'</span>':'<span style="color: #3ab22c;font-size: 18px">+'.$v['nums'].'</span>';
                $nums_res[$k]['sn'] =$v['sn'];
                $nums_res[$k]['times'] =$v['time'];
//                $nums_res[$k]['do'] =$v['type']==21?'转货':($v['type']==23?'提货':'扣除');
                $nums_res[$k]['do'] =$v['type']==21?'转货':($v['type']==23?'提货':($v['type']==24?'后台转':'退单'));//21 23 24
               /* if($v['type']==21){
                    if($v['uid'] == $id){
                        $theory_money =getTheOryMoney($v['uid'],$v['aboutid']) *$v['nums'];
                    }
                }*/
                $nums_res[$k]['after_nums'] = $v['uid'] == $id? $v['uafter'] : $v['after'];
                $uinfo = $account->where(['id' => $v['uid']])->find();
                $nums_res[$k]['out_name'] = $uinfo['nickname'];
                $nums_res[$k]['out_level'] = $uinfo['level'];
                if ($v['type'] != 23) {
                    $about_info = $account->where(['id' => $v['aboutid']])->find();
                    $nums_res[$k]['in_name'] = $about_info['nickname'];
                }
                $nums_res[$k]['dostatus'] = $v['status'] ==1?"":"已取消";

            }
        }

        //获取用户的详情
//        $sum_where['start']=I('start');
//        $sum_where['ends']=I('ends');
        $summary=getUserProductAndMoneyInfo($id);
        $res_user =$account->where(['id'=>$id])->find();
        $summary['rest_nums'] =$res_user['stock'];
        $trun_theory_money =$theory_money;//转货理论的价格

        $this->assign('summary',$summary);//账户总结
        $this->assign('data',$res_data);
        $this->assign('nums',$nums_res);
        $this->assign('user',$res_user);
        $this->assign('theory_money',$trun_theory_money);
        $this->assign('id',$id);
        $this->assign('pg1',$show1);
        $this->assign('pg2',$show2);
        $this->display();
    }
    /**
     * 用户详情
     */
    public function indexview($id) {
        $account = M("account");
        $orders = M("orders");
        $acc_level = M("acc_level");
        #
        $where["id"] = $id;
        $info = $account->where($where)->find();

        //联创
        $top=gettop($info['recid'],6);
        $info['top_name']  =$top['name'];
        //砖石
        $two=gettop($info['recid'],5);
        $info['two_name'] ='-无-';
        if($two['level']==5){
            $info['two_name'] =$two['name'];
        }
        $this->assign("info", $info);
        //
        $cond["openid"] = $info["openid"];
        $list = $orders->where($cond)->select();
        $this->assign("list", $list);
        $this->assign("level", $acc_level->select());
        $this->display();
    }


    /**
     *修改个人的信息
     */
    public function editsSelf(){
        $get=I('post.');
        $gsn ='415244730815416283';
        $sn ='2018A'.getOrdSn();
        $account =M('account');
        //当前用户信息
        $userinfo =$account->where(['id'=>$get['ids']])->find();
        //获取上级
        $up_user =$account->where(['sysid'=>$userinfo['recid']])->find();

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
            return get_op_put(0, "支付失败，请稍后再试");
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
            return get_op_put(0, "处理成功");
        }
        else{
            return get_op_put(0, "处理失败");
        }
    }
    /**
     * 账户关系处理
     */
    public function accStatus() {
        $account = M("account");
        #
        $post = I("post.");
        $where["id"] = $post["ids"];
        $info = $account->where($where)->find();
        if ($info == null) {
            return get_op_put(0, "账户不存在");
        }
        #
        $save["status"] = $post["status"];
        if (!$account->where($where)->save($save)) {
            return get_op_put(0, "处理失败..");
        }
        return get_op_put(1, "处理成功", U("Users/index"));
    }


    /**
     *出货统计  write 2018-9-29 10:45:08
     */
    public function outGoods($start='',$ends='',$levels=6,$types=0,$gid=8)
    {
        $account = M('account');
        $orders = M('orders');
        $acc_nums = M('acc_nums');
        $acc_level = M("acc_level");
        $acc_money = M("acc_money");

        /*$level = I('level');
        $start_time = I('start');
        $end_time = I('ends');*/
        $salemoney =0;

        $level = $levels;
        $start_time = $start ;/*==null?strtotime('2018-8-10'):$start;*/
        $end_time = $ends    ;/*==null?time():$ends;*/
        $where['level'] = $level == "" ? 6 : $level;
        $get_leveluser = $account->where($where)->field('id,totalpoints,stock')->order('id desc')->select();
        foreach ($get_leveluser as $k => $v) {
            $pointarr[] = $v['totalpoints'];
            $stockarr[] = $v['stock'];
            $idarr[] = $v['id'];
        }
        #方案一
        //联创积分
//        $allOutNums = array_sum($pointarr);//等级总积分
//        //联创库存
//        $level_stockNums = array_sum($stockarr);//等级剩余库存货量
        #
        //所有用户的剩余库存量
        $stockNums =$account->sum('stock');
        #
        //方案二的总销售量 用户的库存加上用户提货的盒数

        if($start_time==null) {
            $pick_num = $acc_nums->where(['type' => 23])->sum('nums');
//        $pick_where['status'] =array('in','3,4');
//        $pick_num =M('order_drs')->where($pick_where)->sum('nums');
            
            //联创统计
            $get_leveluser = $account->where($where)->field('id,totalpoints,stock')->order('id desc')->select();
            foreach ($get_leveluser as $k => $v) {
                $pointarr[] = $v['totalpoints'];
                $stockarr[] = $v['stock'];
                $idarr[] = $v['id'];
            }
            $ids = implode(',', $idarr);

            $allOutNums = $pick_num + $stockNums;
            //计算金额
            $this_year = $acc_nums
                ->where(['type' => array('lt', 20),'uid'=> array('in', $ids),'time' => array('gt', strtotime('2019-1-1')), 'aboutid' => 0, 'status' => 1])
                ->field('sn,nums')->select();
//             echo  $acc_nums->getLastSql();
            $this_money =0;
            foreach ($this_year as $k => $v) {
                $nums += $v['nums'];
                $res_money = $acc_money->where(['sn'=>$v['sn'],'models'=>'ORDER'])->find();
                $this_money += $res_money['money'];
            }
            //去年的销售额
            $last_year_money = ($allOutNums - $nums) * 45;
            $salemoney = $last_year_money + $this_money;
            $last_year = $allOutNums  - $nums;
            $this_year = $nums;
        }
        //方案三  订单总计
        //系统里购买
        $orde_where['buy_level'] =$levels;
        $orde_where['gid'] =$gid;
//        $orde_where['paytime'] =array('gt',1);
        $orde_where['status'] =1;
        $order_num =$orders->where($orde_where)->sum('gnums');
        //金额
        $order_money =$orders->where($orde_where)->sum('money');
        //后台添加
        $bac_where['type']=13;
        $bac_where['aboutid']=0;
        $bac_add_num =$acc_nums->where($bac_where)->sum('nums');
        //后台添加金额
        $acc_data =$acc_nums->where($bac_where)->field('sn')->select();
        foreach ($acc_data as $k=>$v){
            $res_arr[] =$v['sn'];
        }
        $money_where['sn'] =array('in',implode(',',$res_arr));
        $money_where['models'] ='ORDER';
        $bac_money =$acc_money->where($money_where)->sum('money');
        $allOutNums =$order_num +$bac_add_num;
        $last_year = $allOutNums  - $nums;
        $this_year = $nums;


        $ids = implode(',', $idarr);

//        if($time=='1day'){//今日
//            $attime =strtotime(date('Y-m-d'));
//        }elseif($time=='1week'){//这周
//            $timestamp = time();
//            /*var_dump( [
//                strtotime(date('Y-m-d', strtotime("this week Monday", $timestamp))),
//                strtotime(date('Y-m-d', strtotime("this week Sunday", $timestamp))) + 24 * 3600 - 1
//            ]);*/
//            $attime =  strtotime(date('Y-m-d', strtotime("this week Monday", $timestamp)));
//        }elseif($time=='1month'){//本月
//            $attime = strtotime(date('Y-m'));
//        }elseif($time=='2month'){//上月
//            $attime = strtotime(date('Y-m',strtotime('-1month')));
//            $endtime = strtotime(date('Y-m'))-1;
//        }else{
//            $attime =strtotime(date('Y-m-d'));
//        }

//        $attime = strtotime(date('Y-m-d',strtotime('-'.$time)));
       /* $where_nums['time'] = array('gt', $attime);
        if($endtime){}*/
       $where_nums['time'] = array('between',[strtotime($start_time),strtotime($end_time)]);

        $where_nums['uid'] = array('in', $ids);
        if($where['level']!=6) {
            $where_nums['type'] =array('lt',20);
//            $where_nums2['time'] = array('gt', $attime);
            $where_nums2['time'] = array('between',[$start_time,$end_time]);
            $where_nums2['aboutid'] =array('in',$ids);
            $where_nums2['type'] =array('gt',20);
            $get_list2 = $acc_nums->where($where_nums2)->order('id desc')->select();
        }else{
            $where_nums['type'] =array('lt',20);
            $where_nums['aboutid'] =0;
            $where_nums['status'] =1;
        }
        $get_list = $acc_nums->where($where_nums)->order('id desc')->select();
        foreach($get_list as $k=>$v){
            $list[$k]['nums'] =$v['nums'];

            //统计销售额
            if($v['time']<strtotime('2019-1-1')){
                $salemoney +=$v['nums']*45;
            }else{
                $pay_info =$acc_money->where(['sn'=>$v['sn'],'models'=>'ORDER'])->find();
                $salemoney +=$pay_info['money'];
            }
            //入货分类
            $pay_info =$acc_money->where(['sn'=>$v['sn'],'models'=>'ORDER'])->find();
            if(substr($v['sn'],0,4)=='2018'){
                $pay_bac +=$pay_info['money'];
            }else{
                $order_info =$orders->where(['sn'=>$v['sn'],'status'=>1])->find();
                //
                if($order_info['paytype']==1){
                    $pay_wecate +=$pay_info['money'];
                }else{
                    $pay_rest +=$pay_info['money'];
                }
            }
            $allmun[]=$v['nums'];
            $list[$k]['sn'] =$v['sn'];
            $list[$k]['time'] =date('Y-m-d H:i:s',$v['time']);
            $list[$k]['name'] =getUserInf($v['uid'],'nickname');
            $list[$k]['level'] =getUserInf($v['uid'],'level');
            $list[$k]['type'] ='售出';
            if($v['aboutid']!=0){
                $list[$k]['out_name'] =getUserInf($v['aboutid'],'nickname');
            }else{
                $list[$k]['out_name'] ='公司';
            }
        }
        //非联创
        if($get_list2){
            foreach($get_list2 as $k=>$v){
                $num =count($get_list)+$k;
                $allmun[]=$v['nums'];
                $list[$num]['time'] =date('Y-m-d H:i:s',$v['time']);
                $list[$num]['out_name'] =getUserInf($v['uid'],'nickname');
                $list[$num]['name'] =getUserInf($v['aboutid'],'nickname');
                $list[$num]['level'] =getUserInf($v['aboutid'],'level');
                $list[$num]['nums'] =$v['nums'];
                $list[$num]['sn'] =$v['sn'];
                $list[$num]['type'] ='转给';
            }
        }
        if($start_time){
            $allOutNums =array_sum($allmun);
        }
        //用户总打款金额
        $drowmoney =M('money_draw')->where(['status'=>1])->sum('money');

        if($types==1) {
            return ['outnum'=>$allOutNums,'wecate'=>$pay_wecate,'rest'=>$pay_rest,'backet'=>$pay_bac,'paymoney'=>$salemoney];
        }else {
            $this->assign('outnums', $allOutNums);//出货
            $this->assign('lastyear', $last_year);//出货
            $this->assign('thisyear', $this_year);//出货
            $this->assign('stocknums', $stockNums);//用户库存
            $this->assign('drowmoney', $drowmoney);//用户总打款金额
            $this->assign('salemoney', $salemoney);//用户支付金额统计
            $this->assign('lastmoney', $last_year_money);//用户支付金额统计
            $this->assign('thismoney', $this_money);//用户支付金额统计
            $this->assign('data', $list);//列表
            $this->assign("level", $acc_level->select());
            $this->assign("setlevel", $level);
            $this->assign("start", $start_time);
            $this->assign("ends", $end_time);
            $this->display();
        }
    }

    /**
     *用户等级变动列表
     */
    public function userLlCgList(){
        $acc_record= M('acc_record');
        $acc_level= M('acc_level');
        $get =I('get.');
        $where=array();
        if($get['start']){
            $where['time'] = array('between',[strtotime($get['start']),strtotime($get['ends'])+24*3600]);
        }
        if($get['level']){
            $where['after'] =$get['level'];
        }

        $count      = $acc_record->where($where)->count();// 查询满足要求的总记录数
        $Page       = new \Think\Page($count,30);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show     = $Page->show();// 分页显示输出
        //获取用户列表数据
        $res_data =$acc_record->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
        
        $level_data =$acc_record->field('count(id),after')->group('after')->select();
        $level_data =array_column($level_data,'count(id)');
        foreach ($res_data as $k=>$v){
            $res_data[$k]['uname'] =getUserInf($v['uid'],'nickname');
            $res_data[$k]['u_name'] =getUserInf($v['uid'],'name');
            $up_userinfo=explode('(',getRecUser($v['uid']));
            $res_data[$k]['up_user'] =$up_userinfo[0];//上级
            $res_data[$k]['before'] =getUserLevel($v['before']);
            $res_data[$k]['after'] =getUserLevel($v['after']);
            $res_data[$k]['time'] =date('Y-m-d H:i:s',$v['time']);
            /*if($get['level']){
                if($v['after'] !=$get['level']){
                    unset($res_data[$k]);
                }
            }*/
        }
        $end_arr =end($res_data);
        if($get ==null){
            $get['start'] =$res_data[0]['time'];
            $get['ends'] =$end_arr['time'];
        }
        //获取等级
        $level =$acc_level->where(['id '=>['gt',0]])->select();
        $this->assign('list',$res_data);
        $this->assign('level',$level);
        $this->assign('levelData',$level_data);
        $this->assign('get',$get);
        $this->assign('show',$show);
        $this->display();
    }
    //用户的进和出 -- 联创
    public function getUserInAndOut($level= 6){
        $get =I('get.');
        $account =M('account');
        $acc_nums =M('acc_nums');
        $where['level'] =$level;
        if($get['name']){
            $where['name'] =$get['name'];
        }
        $p =I('page');
        //
        $start=$get['start']==null?strtotime(date('Y-m')):strtotime($get['start']);
        $end =$get['ends']==null?strtotime(date('Y-m',strtotime('+1 month'))):strtotime($get['ends']);
        $num_where = array('between', [$start, $end]);
        $user_info =$account->where($where)->select();
        $all_out=0;
        foreach ($user_info as $k=>$v){
            //出

            //进
            $where1['time']=$num_where;
            $where1['uid']= $v['id'];
            $where1['type']=array('lt',20);
            if($v['level'] ==6){
                $where1['aboutid']=0;
            }

            //进
//            $where2['time']=$num_where;
//            $where2['aboutid']= $v['id'];
//            $where2['type']= array('gt',20);
            //出
            $where3['time']=$num_where;
            $where3['uid']= $v['id'];
            $where3['type']= array('gt',20);
            //出
            $where4['time']=$num_where;
            $where4['aboutid']= $v['id'];
            $where4['type']= array('lt',20);

            $insum1=$acc_nums->where($where1)->sum('nums');
//            $insum2=$acc_nums->where($where2)->sum('nums');
            $arrs['innum'][$k] =$insum1;
            $user_info[$k]['innums']=$insum1 ;
//            $user_info[$k]['innums']=$insum1;
            $outsum2 =$acc_nums->where($where3)->sum('nums');
            $outsum1 =$acc_nums->where($where4)->sum('nums');
            $user_info[$k]['outnums']=$outsum1+$outsum2;
            //统计总出货
            $all_out +=$insum1;
        }
        if($get['start']==null){
            $getfidres =$get['start'] =date('Y-m-01');
            $get['ends'] =date('Y-m-d',strtotime("$getfidres +1 month -1day"));
        }
        //排序
        array_multisort($arrs['innum'], SORT_DESC, $user_info);
        $acc_level=M('acc_level');
        $this->assign('data',$user_info);
        $this->assign('setlevel',$level);
        $this->assign('all_out',$all_out);

        $this->assign('get',$get);
        $this->assign("level", $acc_level->select());
        $this->display();
    }

    /**
     *获取大联创奖
     * 有两个条件
     * 一：是获取下属团队里面有3个联创 3线并行
     * 二：团队销量在4000盒
     */
    public function getBigTeam(){

    }

    /**
     *
     */
    public function oldPrice(){
        $get=I('post.');
        $gsn ='415244730815416283';
        $sn ='2018A'.getOrdSn();
        $account =M('account');
        //当前用户信息
        $userinfo =$account->where(['id'=>$get['ids']])->find();
        //获取上级
        $up_user =$account->where(['sysid'=>$userinfo['recid']])->find();

        $buyer_price =$userinfo['level']==5?55:45;
        //高-》拉低
        if($up_user['level']>$get['level']){
            //获取价格差
//            $res_price =doPrice($gsn,$up_user['level']);
            $res_price =10;
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
//                    $lev = $acc_level->where($leSel)->find();
                    $level_money = 5;
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
            return get_op_put(0, "支付失败，请稍后再试");
        }
        $res =$account->where(['id'=>$get['ids']])->save($update);
        #
        #模板消息推送
        //当购买成功之后 用户库存增加 给用户发送模板消息
        $wctemp = D("Home/Wctemp", "Logic");
        $user = ["openid" => $userinfo["openid"], "nickname" => $userinfo["nickname"]];
        $data = ["type" => "proin", "product" => '168太空素食', "nums" => $get["stock"],"stock" => $userinfo["stock"]+$get["stock"]];
        $wctemp->entrance($user, $data);
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
            return get_op_put(0, "处理成功");
        }
        else{
            return get_op_put(0, "处理失败");
        }
    }


    /**
     *交换库存量
     *不加积分和返利金额
     * 增加变动记录
     */
    public function exchange(){

        $acc_num =M('acc_nums');
        $out_data =$acc_num->where(['type'=>24])->select();
        foreach ($out_data as $k=>$v){
            $out_data[$k]['inname'] =getUserInf($v['aboutid'],'name');
            $out_data[$k]['outname'] =getUserInf($v['uid'],'name');
            $out_data[$k]['time'] =date('Y-m-d H:i:s',$v['time']);
        }
        $this->assign('data',$out_data);
        $this->display();
    }

    public function exchangenums(){
        $out_uid =I('out');
        $in_uid =I('in');
        $num =I('nums');
        $account =M('account');
        $acc_num =M('acc_nums');
        $out_where['sysid']=$out_uid;
        $in_where['sysid']=$in_uid;
        //出货人信息
        $out_info =$account->where($out_where)->find();
        if($out_info['stock']>=$num) {
            $account->where($out_where)->setDec('stock',$num);//出货人减
            $account->where($in_where)->setInc('stock',$num);//进货人加
            //进货人的库存
            $in_info =$account->where($in_where)->find();
            //增加记录
            $add_data = [
                'sn' => '2018b' . date('YmdHis', time()) . rand(10, 100),
                'uid' => $out_info['id'],
                'aboutid' => $in_info['id'],
                'uafter' => $out_info['stock']-$num,
                'after' => $in_info['stock'],
                'nums' => $num,
                'time' => time(),
                'type' => 24,
                'status' => 1,
            ];
            $acc_num->add($add_data);
            $status=1;
            $msg ='转入完成';
        }else{
            $status=0;
            $msg ='转入失败，出库人库存不足';
        }
        $this->ajaxReturn(['status'=>$status,'msg'=>$msg]);
    }

    /**
     *获取用户信息
     */
    public function  getuserbysys(){
        $id =I('srsid');
        $account =M('account');
        $user_info=$account->where(['sysid'=>$id])->find();
        $this->ajaxReturn($user_info);
    }

    /**添加用户关系管理
     * @param int $uid
     * @param $now
     * @param int $level
     */
    public function addUser_contect($uid=1, $now, $level=0){
        $account =M('account');
        $acc_contect =M('acc_contect');
        if($now==1){
            $now =file_get_contents('contectuid.txt') -1;
        }

        $last_id =$account->order('id desc')->find();
        //最后一条数据 <现在数据
        if($last_id<$now){
            $this->ajaxReturn(['status'=>0,'msg'=>'更新完成']);
        }
        //当前用户
        $user_data =$account->where(['id'=>$uid])->field('id,recid,sysid')->find();
        if($user_data){
            //上级代理信息
            $ancestor=$account->where(['sysid'=>$user_data['recid']])->field('id,level')->find();
            //存在上级
            if($ancestor){
                $where_con['ancestor_id'] =$ancestor['id'];
                $where_con['rold_id'] =$now;
                $res_find =$acc_contect->where($where_con)->find();
                if(!$res_find) {
                    $add = array(
                        'ancestor_id' => $ancestor['id'],
                        'level' => $level,
                        'rold_id' => $now,
                        'user_level' => $ancestor['level'],
                    );
                    $acc_contect->add($add);
                }
                $level++;
//                $this->addUser_contect($ancestor['id'],$now,$level);
                $this->ajaxReturn(['status'=>1,'uid'=>$ancestor['id'],'now'=>$now,'level'=>$level]);
            }
        }
        $now++;
        file_put_contents('contectuid.txt',$now);
        $this->ajaxReturn(['status'=>1,'uid'=>$now,'now'=>$now,'level'=>1]);
    }

     /**
     *修改用户推荐人
     */
    public function parentLevel(){
        $userSuperior =M('user_superior');
        $out_data =$userSuperior->order('id desc')->select();
        foreach ($out_data as $k=>$v){
            $out_data[$k]['superior'] =getUserInf($v['superior'],'name');
            $out_data[$k]['uid'] =getUserInf($v['uid'],'name');
            $out_data[$k]['uafter'] =getUserInf($v['uafter'],'name');
            $out_data[$k]['time'] =date('Y-m-d H:i:s',$v['time']);
        }
        $this->assign("data", $out_data);
        $this->display();
    }

    public function exparentLevel(){
        $out_uid =I('out');//转入上级编号
        $in_uid =I('in');//用户编号
        $account =M('account');
        $userSuperior =M('user_superior');
        $out_where['recid']=$out_uid;
        $in_where['sysid']=$in_uid;
        $user=$account->where($in_where)->find();//用户信息
        $after =$account->where(['sysid'=>$out_uid])->find();//转入上级信息
        $in_info =$account->where(['sysid'=>$user['recid']])->find();//用户上级信息
        $out_info =$account->where($in_where)->save($out_where);
        if($out_info==1) {
            $add_data = [
                'superior' => $in_info['id'],//前上级id
                'uid' => $user['id'],//用户id
                'uafter' => $after['id'],//转入上级id
                'time' => time(),//时间
            ];
            $userSuperior->add($add_data);
            $status=1;
            $msg ='更改完成';
        }else{
            $status=0;
            $msg ='更改失败';
        }
        $this->ajaxReturn(['status'=>$status,'msg'=>$msg]);
    }

}
