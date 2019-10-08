<?php

/**
 * 0元计划
 */

namespace Backet\Controller;
use Backet\Model\GoodsModel;
use Think\Session\Driver\Db;

class PlansController extends CommController {

    /**
     * 
     */
    public function index($type = 10) {
        $order_sr = M("order_sr");
        $get = I("get.");
        $where_no['status'] = 7;
        $all_left_money =$order_sr->where($where_no)->sum('money');
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
      //  $get["name"] != "" ? $where["name"] = array("like", "%" . $get["name"] . "%") : null;
        $get["phone"] != "" ? $where["phone"] = array("like", "%" . $get["phone"] . "%") : null;
        $get["status"] != "" ? $where["status"] = $get["status"] : null;
        #
        //$list = poPage($orders, $where, "id asc");
//        $this->assign("list", $list);
        $this->assign("type", $type);
        $this->assign("page", I("get.p"));
        $this->assign("sn", I("get.sn"));
        $this->assign("name", I("get.name"));
        $this->assign("aboutid", I("get.aboutid"));
        $this->assign("phone", I("get.phone"));
        $this->assign("start", I("get.start"));
        $this->assign("ends", I("get.ends"));
        $this->assign("all_left_money", $all_left_money);
        #
        //$where["status"] = $type != 10 ? array("neq", 0) : 0;
        if($get["aboutid"] != ""){
            $list= poPages($order_sr,$get);
        }else{
            $get["name"] != "" ? $where["name"] = array("like", "%" . $get["name"] . "%") : null;
            $list= poPage($order_sr, $where);
        }
        foreach ($list['list'] as $k=>$v){
            $list['list'][$k]['is_send'] =0;

            if($v['status'] == 5 ||$v['status'] == 7) {
                $reback_time = strtotime(date('Y-m-d ', $v['times'] + 24 * 3600 * 25));
                if (time() >= $reback_time) {
                    $list['list'][$k]['is_send'] = 1;
                }
                $list['list'][$k]['newTime'] = strval($v['times']+ 24 * 3600 * 33);
                $list['list'][$k]['payTime'] = strval($v['times']+ 24 * 3600 * 63);
                $list['list'][$k]['leftTime'] = strval($v['times']+ 24 * 3600 * 63);
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
            // 发送模板消息
            $order_info =$order_sr->where(['id'=>$id])->find();
            $user_info =$account->where(['id'=>$order_info['uid']])->find();
            $re_time = date('Y-m-d ', strtotime('+1 month -1 day',$order_info['times']));
            $wctemp = D("Home/Wctemp", "Logic");
            $user = ["openid" => $user_info["openid"], "nickname" => $user_info["nickname"]];
            $data = ["type" => "reBackMess", "money" => $order_info["money"],"time"=>$re_time];
            $wctemp->entrance($user, $data);

            if ($res) {
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
        $wctemp = D("Home/Wctemp", "Logic");
        $id =I('id');
        $where['id'] =$id;
        $where['status'] =array('in','5,7');
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
                $account->where($where_user)->save(['level'=>0]);
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

                     $nums=count($money_datas);
                    foreach ($money_datas as $k => $v) {
                        //获取返利出货的账户
                        $user_datas =$account->where(['id' => $v['uid']])->find();
                        //更新退款金额
                        /*if($nums>1){
                            $one_user =$account->where(['id' => $money_datas[0]['uid']])->find();
                            //判断金额
                            if($one_user['money'] < $money_datas[0]['money']){
                                //第一个
                                if($k==0){
                                    $v['money'] =0;
                                }else{
                                    $v['money'] = $money_datas[0]['money'] + $money_datas[1]['money'];
                                }
                            }
                        }*/

                        //如果处理退款金额大于0
                        if($v['money']>0) {

                            //收款人信息--- 加记录
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
                                //发送模板消息
                                $user = ["openid" => $v["openid"], "nickname" => $v["name"]];
                                $data = [
                                    "type" => "reback",
                                    "first" => '下级用户:'.$user_info['name'].'申请退单处理',
                                    "money" => '-'.$v['money'],
                                    "info"=>'0元计划',
                                    "sn"=>$data['sn'],
                                    "remake"=>'',
                                ];
                                $wctemp->entrance($user, $data);
                            }
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
                            //添加记录
                             $res =$acc_num->add($add_num);
                             $account->where(['id' => $v['uid']])->setInc('stock',$nums_data['nums']);//归还盒数
                        }
                    }
                    //退货
                    $account->where(['id' =>$nums_data['uid']])->setDec('stock',$nums_data['nums']);//减少盒数
                    $account->where(['id' =>$nums_data['uid']])->setDec('points',$nums_data['nums']);//减少jifen
                    $account->where(['id' =>$nums_data['uid']])->setDec('totalpoints',$nums_data['nums']);//减少积分
                    $account->commit();
                    //状态为9 就是已退款
                    $order_sr->where($where)->save(['status'=>9]);
                    //发送模板消息
                    $user = ["openid" => $user_info["openid"], "nickname" => $user_info["name"]];
                    $data = [
                        "type" => "reback",
                        "first" => '申请退单处理结果',
                        "money" => $data['money'],
                        "info"=>'0元计划',
                        "sn"=>$data['sn'],
                        "remake"=>'您的退单申请处理完成',
                    ];
                    $wctemp->entrance($user, $data);
                    $this->success('退还成功');
                }
            }
        }
        $this->error('处理失败');
    }


    /**
     *获取已经提货的金融用户信息
     */
    public function getHavedPickPlanUser(){
        $order_sr =M('order_sr');
        $get =I('get.');
        //金融状态
        if($get['status1']){
            $sr_where['s.status'] =$get['status1'];
        }else{
            $sr_where['s.status'] = array('in','5,7');
        }
        //发货状态
        if($get['status2']){
            $sr_where['d.status'] =$get['status2'];
        }
        //申请时间
        if($get['start']||$get['ends']){
            $start = strtotime($get["start"]);
            $ends = strtotime($get["ends"]);
            if(!$ends){
                $where["s.times"] = array("gt", $start);
            }if(!$start){
                $where["s.times"] = array("lt", $ends);
            }
            if( $ends >= $start) {
                $where["s.times"] = array('between',[$start,$ends]);
            }
        }

        $count = $order_sr->alias('s')
            ->where($sr_where)
            ->join('RIGHT join order_drs d on d.uid =s.uid')
            ->count();// 查询满足要求的总记录数
        $Page       = new \Think\Page($count,25);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show       = $Page->show();// 分页显示输出
        $sr_id =$order_sr->alias('s')
            ->where($sr_where)
            ->join('left join order_drs d on d.uid =s.uid')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->order('d.id  desc')
            ->field('s.uid uid,s.sn ssn,s.name na1,d.name na2,d.phone dphone,d.address dadd,d.trsn dtrsn,s.money smoney,s.times stim,d.times dtime,s.status sta ,d.status dsta,d.nums dnum')
            ->select();
        $datas=array();
        foreach ($sr_id as $k=>$v){
            $datas[$k]['id'] =$v['id'];
            $datas[$k]['name'] =$v['na1'];
            $datas[$k]['sn'] =$v['ssn'];
            $datas[$k]['getname'] =$v['na2'];
            $datas[$k]['getphone'] =(string)$v['dphone'];
            $datas[$k]['getaddress'] =$v['dadd'];
            $datas[$k]['trans']=(string)$v['dtrsn'];
            $datas[$k]['status']=getplansStatus($v['sta']);
            $datas[$k]['getstatus']=$v['dsta'] ;
            $datas[$k]['getnums']=$v['dnum'];
            $datas[$k]['money'] =(string)$v['smoney'];
            $datas[$k]['times'] =date('Y-m-d H:i:s',$v['stim']);
            $datas[$k]['gettime'] =date('Y-m-d H:i:s',$v['dtime']);
            $datas[$k]['stock'] =getUserInf($v['uid'],'stock');
            $datas[$k]['level'] =getUserLevel(getUserInf($v['uid'],'level'));
        }
        //存储信息
        $this->assign('page',$show);
        $this->assign('data',$datas);

        $this->display('Goods/pickplan');
    }
    //发送短信提醒用户提货
    public function smsToPick($lenth){
        $order_sr =M('order_sr');
        $account =M('account');
          //发送短信
        $verify = D("Backet/Verif", "Logic");
        #
        
        $arr_ids =S('plans_id_all');
        if(!$arr_ids){
            $where_all['status'] =array('in','5,7');
            $sr_all=$order_sr->where($where_all)->field('id')->select();
            $arr_ids =array_column($sr_all,'id');
            S('plans_id_all',$arr_ids,2*3600);
        }
        if($lenth>=count($arr_ids)){
            $this->ajaxReturn(['status'=>0,'msg'=>'发送完成']);
        }
        //id
        $where['id']=$arr_ids[$lenth];
        //获取0元计划申请详情
        $sr_plan =$order_sr->where($where)->find();
        $user_info =$account->where(['id'=>$sr_plan['uid']])->find();
        $lenth++;
        //用户库存量
        if($user_info['stock']>=40){
            //获取数量
            $nums=getplanNum($sr_plan['money']);
            //发送短信
            $res = $verify->pickSms($sr_plan['phone'],$sr_plan['money'], $nums);
            if($res){
                $this->ajaxReturn(['status'=>1,'lenth'=>$lenth]);
            }
        }
         $this->ajaxReturn(['status'=>2,'lenth'=>$lenth]);
    }

      /**支付0元计划方法
     * @param $sn
     */
    public function Pay_plan($sysid,$sn){
        $order_sr =M('order_sr');
        $account =M('account');
        $sr_where['sn']= $sn;
        // $sr_where['status']= array('in','5,7');
        $sr_info =$order_sr->where($sr_where)->find();
        $user_where['sysid']=$sysid;
        $user_info =$account->where($user_where)->find();
        //存在用户和订单
        $account->startTrans();
        if($sr_info && $user_info){
            //用户的金额大于 订单金额
            if($user_info['money']>=$sr_info['money']){
                //扣钱
                $res =cgUserMoney($user_info['id'],$sr_info['money'],0,'ORPAY',$sr_info['sn']);
                //更变订单状态
                // $res =$order_sr->where($sr_info)->save(['status'=>9]);
                //处理成功
                if($res){
                    $account->commit();
                    $status=1;
                    $msg ='处理成功';
                }else{
                    $account->rollback();
                    $status=0;
                    $msg ='更新失败';
                }
            }else{
                $account->rollback();
                $status=0;
                $msg ='用户金额不足';
            }
        }else{
            $account->rollback();
            $status=0;
            $msg ='转入失败，获取信息失败';
        }
        $this->ajaxReturn(['status'=>$status,'msg'=>$msg]);
    }
        /**
     * 身份证和手机显示
     */
    public function idcardExamine() {
        $order_sr = M("order_sr");
        $id = I("id");
        $pwd = I("pwd");
        $pwd=md5($pwd);
        if($pwd!='8b5ec81d55775af34b980a3a9b8b1260'){
            $status=2;
            $msg ='密码错误';
            $this->ajaxReturn(['status'=>$status,'msg'=>$msg]);
        }
        $list= $order_sr->where(['id'=>$id])->find();
        if($list){
            $status=1;
            $this->ajaxReturn(['status'=>$status,'phone'=>$list['phone'],'idcard'=>$list['idcard']]);
        }

    }
    //导出转货数据
     public function cart(){
        $categoryModel=new GoodsModel();
        $rows = $categoryModel->getList('0','10055');
        //var_dump($rows);exit;
        $docsv=new DocsvController();
        $docsv->cart_Excel($rows);
       // $this->assign('rows',$rows);
       // $this->display();
    }
}
