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
        $get["level"] != null ? $where["level"] = $get["level"] : null;
        if( $get["up_nickname"] != null ){
            $up_where['nickname'] =array("like", "%" . $get["up_nickname"] . "%");
            $up_arr=$account->where($up_where)->select();
            foreach ($up_arr as $v){
                $up_ids[]=$v['sysid'];
            }
            $where['recid'] =array('in',implode(',',$up_ids));
        }
        #
        $list = poPage($account, $where);
        foreach ($list['list'] as $k=>$v){
            $list['list'][$k]['parent_name'] ='';
            $list['list'][$k]['parent_level'] ='';
            if($v['recid']!=null||$v['recid']!=0){
                $parent_info =$account->where(array('sysid'=>$v['recid']))->find();
                $list['list'][$k]['parent_name'] =$parent_info['nickname'];
                $list['list'][$k]['parent_level'] =$parent_info['level'];
            }
        }
        $this->assign("list", $list);
        $this->assign("select", $get);
        $this->assign("level", $acc_level->select());
        $this->display();
    }

    /**
     *获取用户金额数量变动记录
     */
    public function getList(){
        $acc_money = M('acc_money');
        $acc_nums = M('acc_nums');
        $account = M('account');
        $order= M('orders');
        $id =I('id');
        //查询金额
        $where_money['uid'] =$id;
        $money_data =$acc_money->where($where_money)->order('id desc')->select();
        $res_data=[];
        foreach ($money_data as $k=>$v){
            $res_data[$k]['sn'] =$v['sn'];
            $res_data[$k]['change_money'] =$v['models']!='REBACK'? '<span style="color: #b2282c;font-size: 18px">-' .$v['money'].'</span>': '<span style="color: #3ab22c;font-size: 18px">+' .$v['money'].'</span>';
            $res_data[$k]['before_money'] =$v['befores'];
            $res_data[$k]['after_money'] =$v['afters'];
            $res_data[$k]['times'] =$v['times'];
            $res_data[$k]['do'] =$v['models']=='ORDER'?'订单':($v['models']=='REBACK'?'返现':($v['models']=='DRAW'?'提现':'返现'));
            if($v['models']=="ORDER"){
                $reback =$acc_money->where(['models'=>'REBACK','sn'=>$v['sn']])->select();
                $order_info =$order->where(['sn'=>$v['sn']])->find();
                $res_data[$k]['order_type'] =$order_info['paytype'];
                foreach ($reback as $v){
                    $reback_user = $account->where(['id' => $v['uid']])->find();
                    $rebk[]='给:'.$reback_user['nickname'].'-返:'.$v['money'];
                }
                $res_data[$k]['reback_info'] =implode('/',$rebk);
                unset($rebk);
            }
            if($v['sn']!=0){
                $nums_data =$acc_nums->where(['sn'=>$v['sn']])->find();
                if($nums_data) {
                    $res_data[$k]['change_nums'] =$v['models']=='ORDER'?'<span style="color: #3ab22c;font-size: 18px">+' .$nums_data['nums'].'</span>': '<span style="color: #b2282c;font-size: 18px">-' .$nums_data['nums'].'</span>';
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
            }
        }

        //提货记录
        $nums_where['uid']= $id;
        $nums_where['aboutid']= $id;
        $nums_where['_logic']= 'or';
        $nums_data =$acc_nums->where($nums_where)->select();
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
                    $nums_res[$k]['out_name'] = $uinfo['nickname'];
                    $nums_res[$k]['out_level'] = $uinfo['level'];
                    $about_info = $account->where(['id' => $v['uid']])->find();
                    $nums_res[$k]['in_name'] = $about_info['nickname'];
                }
            }
            if($v['type']>20) {
                $nums_res[$k]['sn'] =$v['sn'];
                $nums_res[$k]['nums'] =$v['uid']==$id?'<span style="color: #b2282c;font-size: 18px">-'.$v['nums'].'</span>':'<span style="color: #3ab22c;font-size: 18px">+'.$v['nums'].'</span>';
                $nums_res[$k]['sn'] =$v['sn'];
                $nums_res[$k]['times'] =$v['time'];
                $nums_res[$k]['do'] =$v['type']==21?'转货':($v['type']==23?'提货':'扣除');
                $nums_res[$k]['after_nums'] = $v['uid'] == $id? $v['uafter'] : $v['after'];
                $uinfo = $account->where(['id' => $v['uid']])->find();
                $nums_res[$k]['out_name'] = $uinfo['nickname'];
                $nums_res[$k]['out_level'] = $uinfo['level'];
                if ($v['type'] != 23) {
                    $about_info = $account->where(['id' => $v['aboutid']])->find();
                    $nums_res[$k]['in_name'] = $about_info['nickname'];
                }
            }
        }
        $this->assign('data',$res_data);
        $this->assign('nums',$nums_res);
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
                #  //返利到上一级
                $acc_level = M("acc_level");
                #等级查询
                $leSel["id"] = $up_user["level"];
                //查询上家等级信息
                $lev = $acc_level->where($leSel)->find();
                $level_money=$lev["first"];
                $res_money =$get['stock']*$level_money;
                //增加金额
                cgUserMoney($up_user["id"], $res_money, 1, "REBACK", $sn);
                //增个人金额
                $account->where(['id'=>$up_user["id"]])->setInc('person',$res_money);
                //增团队额
                $account->where(['id'=>$up_user["id"]])->setInc('groups',$res_money);

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
                    //增团队额
                    $account->where(['id'=>$up_user["id"]])->setInc('groups',$addmoney);
                    $two_data = gettop($up_user['recid'], $get['level']);
                    if($two_data &&$two_data['level']==$get['level']) {
                        $addmoney = $get['stock'] * 2.5;
                        //增加金额
                        cgUserMoney($two_data["id"], $addmoney, 1, "REBACK", $sn);
                        //增个人金额
                        $account->where(['id' => $two_data["id"]])->setInc('person', $addmoney);
                        //增团队额
                        $account->where(['id' => $two_data["id"]])->setInc('groups', $addmoney);
                    }
                }else{
                    #  //返利到上一级
                    $res_money =$get['stock']*2.5;
                    //增加金额
                    cgUserMoney($up_user["id"], $res_money, 1, "REBACK", $sn);
                    //增个人金额
                    $account->where(['id'=>$up_user["id"]])->setInc('person',$res_money);
                    //增团队额
                    $account->where(['id'=>$up_user["id"]])->setInc('groups',$res_money);
                  # 上级的联创
                    $two_data = gettop($up_user['recid'], $get['level']);
                    if($two_data &&$two_data['level']==$get['level']) {
                        //增加金额
                        cgUserMoney($two_data["id"], $res_money, 1, "REBACK", $sn);
                        //增个人金额
                        $account->where(['id' => $two_data["id"]])->setInc('person', $res_money);
                        //增团队额
                        $account->where(['id' => $two_data["id"]])->setInc('groups', $res_money);
                        $three_data = gettop($two_data['recid'], $get['level']);
                        if($three_data &&$three_data['level']==$get['level']) {
                            //增加金额
                            cgUserMoney($three_data["id"], $res_money, 1, "REBACK", $sn);
                            //增个人金额
                            $account->where(['id' => $three_data["id"]])->setInc('person', $res_money);
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
        //修改记录
        $update["stock"]= array("exp", "stock+" . $get["stock"]);
        $update["points"]= array("exp", "points+" . $get["stock"]);
        $update["totalpoints"]= array("exp", "totalpoints+" . $get["stock"]);
        $update["level"]= $get['level'];
        $res =$account->where(['id'=>$get['ids']])->save($update);
        if($res){
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
     *获取父级账户信息
     */
    public function  getParentInfo(){
        $sysid =I('sysid');
    }
}
