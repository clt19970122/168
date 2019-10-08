<?php

/**
 * 0元计划
 */

namespace Backet\Controller;

use Think\Session\Driver\Db;

class PlansController extends CommController {

    /**
     * 
     */
    public function index($type = 10) {
        $order_sr = M("order_sr");
        $get = I("get.");
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
                $where["times"] = array(array("gt", $start), array("lt", $ends));
            }
            if ($ends == $start){
                $where["times"] = array(array("gt", $start), array("lt", $ends+86400));
            }
        }
        $type != 10 ? $where["status"] = $type : null;
        $get["sn"] != "" ? $where["sn"] = array("like", "%" . $get["sn"] . "%") : null;
        $get["name"] != "" ? $where["name"] = array("like", "%" . $get["name"] . "%") : null;
        $get["phone"] != "" ? $where["phone"] = array("like", "%" . $get["phone"] . "%") : null;
        $get["status"] != "" ? $where["status"] = $get["status"] : null;
        #
        //$list = poPage($orders, $where, "id asc");
//        $this->assign("list", $list);
        $this->assign("type", $type);
        $this->assign("page", I("get.p"));
        $this->assign("sn", I("get.sn"));
        $this->assign("name", I("get.name"));
        $this->assign("phone", I("get.phone"));
        $this->assign("start", I("get.start"));
        $this->assign("ends", I("get.ends"));
        #
        //$where["status"] = $type != 10 ? array("neq", 0) : 0;
        $list = poPage($order_sr, $where);
        foreach ($list['list'] as $k=>$v){
            $list['list'][$k]['is_send'] =0;
            if($v['status'] == 5 ||$v['status'] == 7) {
                $reback_time = strtotime(date('Y-m-d ', $v['times'] + 24 * 3600 * 25));
                if (time() >= $reback_time) {
                    $list['list'][$k]['is_send'] = 1;
                }
            }
        }

        $this->assign("list", $list);
        $this->display();
    }

    /**
     * @param $id
     * @param int $type
     * @return bool
     */
    public function sendSms($id, $type =0){
        //发送短信
        $verify = D("Backet/Verif", "Logic");
        $order_sr = M("order_sr");
//        $id =I('id');
        $where['id'] =$id;
        $where['times'] =array('gt',strtotime(''));
        $where['status'] =array('in','5,7');
        $info =$order_sr->where($where)->find();
        if($info) {
            $reback_time =strtotime(date('Y-m-d ',$info['times']+24*3600*25));
            if(time() >=$reback_time) {

                /*$plan_type = $info['money'] == 3000 ? '-0元创业' : ($info['money'] == 1200 ? '-月体验' : ($info['money'] == 480 ? '-周体验' : ($info['money'] == 168 ? '-单盒体验' : '')));
                $product = '0元计划' . $plan_type;
                $re_time = date('Y-m-d ',  strtotime('+1 month',$info['times']));
                if (time() >= strtotime($re_time)) {
                    $product .= '已逾期';
                }*/
                $re_time = date('Y-m-d ', strtotime('+1 month -1 day',$info['times']));
                $res = $verify->planSms($info['phone'], '', $info['money'].',', $re_time);
                if($res){
                    $order_sr->where(['id'=>$info['id']])->setInc('smstimes');
                    if($type==0){
                        $this->success('处理成功');
                    }else{
                        return true;
                    }
                }
            }else{
                return false;
            }
        }else{
            return false;
        }
        return false;
    }

    /**
     *一键发送即将逾期的短信
     */
    public function oneSendSms(){
        $i=I('lenth');
        $order_sr = M("order_sr");
        $account = M("account");
        $where['times'] =array('lt',strtotime(date('Y-m-d ', time() - 24 * 3600 * 25)));
        $where['status'] =array('in','5,7');//已确认和逾期的
        $arr_id = session('send_ids');
        if(!$arr_id) {
            $res = $order_sr->where($where)->select();
            $arr_id = array_column($res, 'id');
//            $add_data = date('Y-m-d H:i;s') . '发送' . implode(',', array_column($res, 'id'));
            session('send_ids', $arr_id);
        }
        if($i<count($arr_id)){
            $id = $arr_id[$i];
            //发送短信
            $res = $this->sendSms($id, 1);
            

            if ($res) {
                // 发送模板消息
            
                $order_info =$order_sr->where(['id'=>$id])->find();
                $user_info =$account->where(['id'=>$order_info['uid']])->find();
                $re_time = date('Y-m-d ', strtotime('+1 month -1 day',$order_info['times']));
                $wctemp = D("Home/Wctemp", "Logic");
                $user = ["openid" => $user_info["openid"], "nickname" => $user_info["nickname"]];
                $data = ["type" => "reBackMess", "money" => $order_info["money"],"time"=>$re_time];
                $wctemp->entrance($user, $data);
                //添加入记录
                $user_info = $order_sr->where(['id' => $id])->field('name')->find();
                $add_data = date('Y-m-d H:i;s') . ':发送给' . $id . '-' . $user_info['name'];
                file_put_contents('send.txt',$add_data,2);
                $i++;
                $this->ajaxReturn( ['status'=>0,'lenth'=>$i]);
            }
        }
        return ['status'=>1];
//        file_put_contents('send.txt',$add_data);
    }

    /**
     *发送模板消息提醒
     */
    public function sendWcTemp(){
        $i=I('lenth');
        $order_sr = M("order_sr");
        $account = M("account");
        $where['times'] =array('lt',strtotime(date('Y-m-d ', time() - 24 * 3600 * 25)));
        $where['status'] =array('in','5,7');//已确认和逾期的
        $arr_id = S('send_ids');
        if(!$arr_id) {
            $res = $order_sr->where($where)->select();
            $arr_id = array_column($res, 'id');
//            $arr_uid = array_column($res, 'uid');
//            $add_data = date('Y-m-d H:i;s') . '发送' . implode(',', array_column($res, 'id'));
            S('send_ids', $arr_id,4*3600);
        }

        if($i<count($arr_id)) {
            $id = $arr_id[$i];
            // var_dump($id);die;
            $order_info =$order_sr->where(['id'=>$id])->find();
            $user_info =$account->where(['id'=>$order_info['uid']])->find();
            $re_time = date('Y-m-d ', strtotime('+1 month -1 day',$order_info['times']));
//        发送模板消息
            $wctemp = D("Home/Wctemp", "Logic");
            $user = ["openid" => $user_info["openid"], "nickname" => $user_info["nickname"]];
            $data = ["type" => "reBackMess", "money" => $order_info["money"],"time"=>$re_time];
            $wctemp->entrance($user, $data);
            $i++;
            //增加发送次数
            $order_sr->where(['id'=>$id])->setInc('smstimes');
            $this->ajaxReturn(['status' => 0, 'lenth' => $i]);
        }
        $this->ajaxReturn( ['status'=>1]);
    }

    /**
     *金融退款
     */
    public function payForPlan(){
        $order_sr =M('order_sr');
        $account =M('account');
        $acc_num =M('acc_nums');
        $acc_money =M('acc_money');
        $id =I('id');
        $where['id'] =$id;
        $where['status'] =5;
        //开启事务
        $account->startTrans();
        //查找订单
        $data =$order_sr->where($where)->find();
        if($data){
            $uid =$data['uid'];
            $where_user['id'] =$uid;
            //存在用户 -购买人
            $user_info = $account->where($where_user)->find();
            if($user_info){
                /*
                 * 处理退款 退货逻辑
                 */

                //当库存量充足和上级的金额足够事

                //查询数量
                $acc_sn['sn']=$data['sn'];

                $nums_data= $acc_num ->where($acc_sn)->find();
                //判断库存之后
                if($user_info['stock']>=$nums_data['nums']) {
                    //获取支付信息
                    $acc_sn['models']='REBACK';
                    $money_datas = $acc_money->where($acc_sn)->field('uid,sn,money')->select();

                    foreach ($money_datas as $k => $v) {
                        //收款人信息
                        $add_info=[
                            'money'=>$v['money'],
                            'models'=>'RETURN',
                            'sn'=>$v['sn'],
                            'uid'=>$v['uid'],
                            'type'=>3,
                            'times'=>time(),
                        ];
                        $res =$acc_money->add($add_info);
                        //添加记录成功
                        if($res){
                            $account->where(['id' => $v['uid']])->setDec('money',$v['money']);//退钱
                        }
                        //出货人
                        if($nums_data['aboutid']==$v['uid']){
                            $add_num =[
                                'nums'=>$nums_data['nums'],
                                'aboutid'=>$nums_data['aboutid'],
                                'type'=>25,
                                'uid'=>$nums_data['uid'],
                                'time'=>time(),
                                'sn'=>$nums_data['sn'],
                            ];
                             $res =$acc_num->add($add_num);
                             $account->where(['id' => $v['uid']])->setInc('stock',$nums_data['nums']);//归还盒数
                             $account->where(['id' =>$nums_data['uid']])->setDec('stock',$nums_data['nums']);//减少盒数
                             $account->where(['id' =>$nums_data['uid']])->setDec('points',$nums_data['nums']);//减少jifen
                             $account->where(['id' =>$nums_data['uid']])->setDec('totalpoints',$nums_data['nums']);//减少积分
                        }
                    }
                    $account->commit();
                    $order_sr->where($where)->save(['status'=>6]);
                    $this->success('退还成功');
                }
            }
        }
        $this->error('处理失败');
    }

}
