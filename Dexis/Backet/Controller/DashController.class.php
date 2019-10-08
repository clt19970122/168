<?php

namespace Backet\Controller;

#use Think\Controller;

//use function Sodium\add;

class DashController extends CommController {

    public function index() {
        $account = M("account");
        $orders = M("orders");
        $sysmang = M("sysmang")->select();
        //
        #
//        $res =$account->field('count(id),level')->group('level')->select();
//        $this->assign("count_user", $res);

        $res_data  =$this->getAboutMoney();
        $num['outnum'] =0;
        $num  =$this->getOutGood(1);
        $num['outnum'] =$num['outnum']==null?0:$num['outnum'];
        $num['rest'] =$num['rest']==null?0:$num['rest'];
        $num['wecate'] =$num['wecate']==null?0:$num['wecate'];
        $num['backet'] =$num['backet']==null?0:$num['backet'];
        $where_top['level'] =6;
        $where_no['level'] =array('neq',6);
        $all_rest_money =$account->where($where_no)->sum('money');
        $all_top_money =$account->where($where_top)->sum('money');
        $count =$account->count();
        $ordercount =$orders->count();
      //  $arr_data =$this->getGoldLevelNum();
//        var_dump($num);
//        
       	$info = array(
		'操作系统'=>PHP_OS,
		'运行环境'=>$_SERVER["SERVER_SOFTWARE"],
		'PHP运行方式'=>php_sapi_name(),
		'ThinkPHP版本'=>THINK_VERSION.' [ <a href="http://thinkphp.cn" target="_blank">查看最新版本</a> ]',
		'上传附件限制'=>ini_get('upload_max_filesize'),
		'执行时间限制'=>ini_get('max_execution_time').'秒',
		'服务器时间'=>date("Y年n月j日 H:i:s"),
		'北京时间'=>gmdate("Y年n月j日 H:i:s",time()+8*3600),
		'服务器域名/IP'=>$_SERVER['SERVER_NAME'].' [ '.gethostbyname($_SERVER['SERVER_NAME']).' ]',
		'剩余空间'=>round((disk_free_space(".")/(1024*1024)),2).'M',
		'register_globals'=>get_cfg_var("register_globals")=="1" ? "ON" : "OFF",
		'magic_quotes_gpc'=>(1===get_magic_quotes_gpc())?'YES':'NO',
		'magic_quotes_runtime'=>(1===get_magic_quotes_runtime())?'YES':'NO',
		);
   		 $this->assign("info",$info);
        //获取分期支付的订单数量
        $stages_data =$this->get_Stage_num();
        $this->assign("stages",$stages_data);

        //获取提货发货数量
        $out_nums =$this->get_pick_num();
        $this->assign("pick_nums",$out_nums);
        $this->assign("money",$res_data);
        $this->assign("num",$num);
        $this->assign("user", $count);
        $this->assign("memb", $count);
        $this->assign("ords", $ordercount);
        $this->assign("rest_money",$all_rest_money);
        $this->assign("all_top_money",$all_top_money);
//        $this->assign("gold_data",$arr_data);
        $this->assign("mony", $orders->where("status=3")->sum("money"));

        // $info = $this->info();
        $this->assign("sysmang",$sysmang);
        $this->display();
    }

    /**今日出货和待出货量
     * @return array
     */
    public function get_pick_num(){
        $order_drs =M('order_drs');
        $acc_nums =M('acc_nums');
        $where['status'] =1;
        $where['have_pay'] =1;
        //物流发货
        $need_out =$order_drs->where($where)->sum('nums');
//        $where2['status']=4;
//        $where2['status']=3;
//        $where2['sure']=0;
        //自提出货
//        $need_out1 =$order_drs->where($where2)->sum('nums');
//        $all_out =$need_out +$need_out1;
        //今日出货
        $out_where['type'] =23;
        $start =strtotime(date('Y-m-d'));
        $out_where['time'] =array('between',[$start,$start+86400]);
        $out_nums=$acc_nums->where($out_where)->sum('nums');
        return ['need_out'=>$need_out,'this_out'=>$out_nums];
    }

    /** 获取优惠的数量
     * @return mixed
     */
    public function get_Stage_num(){
        $stages =M('acc_stages');
        $where_stage['status'] =array('neq',3);
        $where_stage['type'] =2;
        $res_data =$stages->where($where_stage)->group('level')->field('level,count(id) as apply_num')->order('level asc')->select();
        $res_nums['demon']=0;
        $res_nums['founder']=0;
        foreach($res_data as $k=>$v){
            if($v['level'] ==5){
                $res_nums['demon'] =$v['apply_num'];
            }
            if($v['level'] ==6){
                $res_nums['founder'] =$v['apply_num'];
            }
        }
        return $res_nums;
    }


    /**
     *获取黄金雨钻石雨用户的数量
     */
 /*   public function getGoldLevelNum(){
        $acc_record =M('acc_record');
        $time =strtotime(date('Y-m-d 0:0:0'),time());
        $gold_where['dotype'] ='用户升级黄金雨';
        $all_gold_nums =$acc_record->where($gold_where)->count();
        $gold_where['time'] =array('between',[$time,$time+86400]);
        $gold_this_nums =$acc_record->where($gold_where)->count();
         $gold_wheres['dotype'] ='用户升级钻石雨';
        $all_ston_nums =$acc_record->where($gold_wheres)->count();
        $gold_wheres['time'] =array('between',[$time,$time+86400]);
        $ston_this_nums =$acc_record->where($gold_wheres)->count();
        return ['all_gold'=>$all_gold_nums,'this_gold'=>$gold_this_nums,'all_ston'=>$all_ston_nums,'this_ston'=>$ston_this_nums];
    }*/
    //获取现金流数据  和返利金额 总计
    public function getAboutMoney(){
        //根据类型获取时间
        $type =I('type');
        $type=$type==null?1:$type;

        $orders=M('orders');
        $acc_money=M('acc_money');
        $acc_num=M('acc_nums');
        $order_sr=M('order_sr');

        $timestamp=time();
        $one_day = 24 * 3600-1;
        $get_time='';
        $end_time='';
        if($type==1) { //今天
            $get_time = strtotime(date('Y-m-d',$timestamp));
            $end_time = $get_time + $one_day;
        }elseif($type==2){//昨天
            $get_time =  mktime(0, 0, 0, date('m'), date('d') - 1, date('Y'));
            $end_time =  mktime(23, 59, 59, date('m'), date('d') - 1, date('Y'));
        }elseif($type==3){//本周
            $get_time= strtotime(date('Y-m-d', strtotime("this week Monday", $timestamp)));
            $end_time= strtotime(date('Y-m-d', strtotime("this week Sunday", $timestamp))) + $one_day;
        }elseif($type==4) {//上周
            $get_time =strtotime(date('Y-m-d', strtotime("last week Monday", $timestamp)));
            $end_time =strtotime(date('Y-m-d', strtotime("last week Sunday", $timestamp))) + $one_day;
        }elseif($type==5) {//本月
            $get_time =mktime(0, 0, 0, date('m'), 1, date('Y'));
            $end_time = mktime(23, 59, 59, date('m'), date('t'), date('Y'));
        }elseif($type==6) {//上月
            $get_time = mktime(0, 0, 0, date('m') - 1, 1, date('Y'));
            $end_time = mktime(23, 59, 59, date('m') - 1, date('t', $get_time), date('Y'));
        }
        #现金流
        //系统线上
        $online_where['paytime'] =array('between',[$get_time,$end_time]);
        $online_info =$orders->where($online_where)->sum('money');
        $online_info =$online_info==null?0:$online_info;
        //后台添加
        $offline_where['models'] ='ORDER';
        $offline_where['times'] =array('between',[$get_time,$end_time]);
        $offline_where['sn'] =array('like','%2018A%');
        $offine_info =$acc_money->where($offline_where)->sum('money');
        $offine_info =$offine_info==null?0:$offine_info;
        //金融
        $jinrong_where['status'] =array('in','5,4,7');
        $jinrong_where['passtime'] =array('between',[$get_time,$end_time]);
        $jr_data =$order_sr->where($jinrong_where)->sum('money');
        $jr_data =$jr_data==null?0:$jr_data;
        #返利总计
        //联创
        $reback_money['times']=array('between',[$get_time,$end_time]);
        $reback_money['models']='REBACK';
        //由订单号分类
        $all_reback =$acc_money->where($reback_money)->group('sn')->select();
        $top_reback_money =0;
        $other_remoney =0;
//        $res_data =array_unique(array_column($all_reback,'sn'));

        foreach($all_reback as $k=>$v){
            //获取出货的用户
            $out_user =$acc_num ->where(['sn'=>$v['sn']])->field('aboutid')->find();
            //出货人为公司的话
            $reback =$acc_money->where(['sn'=>$v['sn'],'models'=>'REBACK','times'=>array('between',[$get_time,$end_time])])->order('money desc')->select();
            if($out_user['aboutid']!=0){
                //返利数据大于1条 && 出货不是公司
                /*if(count($reback)>1){
                    $other_remoney +=$reback[1]['money'];
                }*/
                $res =$acc_money->where(['sn'=>$v['sn'],'models'=>'ORDER'])->find();
                $other_remoney +=$res['money'];
            }else{
                foreach ($reback as $kk=>$vv){
                    $top_reback_money+=$vv['money'];
                }
            }
        }
        if(IS_POST){
            $this->ajaxReturn(['online_money'=>$online_info,'offine_money'=>$offine_info,'jr_money'=>$jr_data,'other_money'=>$other_remoney,'top_reback_money'=>$top_reback_money]);
        }
        return ['online_money'=>$online_info,'offine_money'=>$offine_info,'jr_money'=>$jr_data,'other_money'=>$other_remoney,'top_reback_money'=>$top_reback_money];
    }
    //统计联创进货额
    public function getOutGood($type){
//        $type =I('type');
        $type=$type==null?1:$type;
        $timestamp=time();
        $one_day = 24 * 3600;
        if($type==1) { //今天
            $get_time = strtotime(date('Y-m-d',$timestamp));
            $end_time = $get_time + $one_day;
        }elseif($type==2){//昨天
            $get_time =  mktime(0, 0, 0, date('m'), date('d') - 1, date('Y'));
            $end_time =  mktime(0, 0, 0, date('m'), date('d') , date('Y'));
        }elseif($type==3){//本周
            $get_time=strtotime(date('Y-m-d', strtotime("this week Monday", $timestamp)));
            $end_time=strtotime(date('Y-m-d', strtotime("this week Sunday", $timestamp))) + $one_day;
        }elseif($type==4) {//上周
            $get_time =strtotime(date('Y-m-d', strtotime("last week Monday", $timestamp)));
            $end_time =strtotime(date('Y-m-d', strtotime("last week Sunday", $timestamp))) + $one_day;
        }elseif($type==5) {//本月
            $get_time = mktime(0, 0, 0, date('m'), 1, date('Y'));
            $end_time = mktime(23, 59, 59, date('m'), date('t'), date('Y'));
        }elseif($type==6) {//上月
            $get_time = mktime(0, 0, 0, date('m') - 1, 1, date('Y'));
            $end_time = mktime(23, 59, 59, date('m') - 1, date('t', $get_time), date('Y'));
        }
        $get_time=date('Y-m-d',$get_time);
        $end_time=date('Y-m-d',$end_time);
        $user =new UsersController();
        $res  =$user->outGoods($get_time,$end_time,6,1);
        $res['outnum']=$res['outnum']==null?0: $res['outnum'];
        if(IS_POST){
            $this->ajaxReturn($res);
        }
        return $res;
    }

    //获取可视化数据图
    public function getusercount(){

        $account = M("account");
        $acc_record= M('acc_record');
        $acc_num= M('acc_nums');
//        $get_time =time();
        $get_time =strtotime(date('Y-m-d',time()));

        $res=S('sd_level_count');
       $level=S('sd_level');
         $times=S('sd_time');
        $res_data=S('sd_time_data');
        $out_num=S('sd_daliy_out');
        $in_num=S('sd_daliy_in');
       if(!$out_num){
//        $get_time =1543381128;
            #1
            #//获取等级用户数量总计
            #
            $res = $account->field('count(id),level')->group('level')->order('level asc')->select();
            foreach ($res as $k => $v) {
                $level[] = getUserLevel($v['level']);
                $res[$k]['counts'] = $v['count(id)'];
            }
            #2
            #获取等级变动时间明细
            #最近7天
            /*$time =strtotime(date('Y-m-d',$get_time))-6*24*3600;
            $one_day =24*3600;
            for($i=0;$i<7;$i++){
                $this_day =date('m-d',$time+$one_day*$i);
                $where['time'] = array('between',[$time+$one_day*$i,$time+($one_day*($i+1))]);//循环7天
                //初始化各各等级的数量
                $res_recode[$this_day]=array(
                    0=>0, 1=>0, 2=>0, 3=>0, 4=>0, 5=>0, 6=>0,
                );
                $res_data =$acc_record->where($where)->select();
                foreach($res_data as $k=>$v){
                    $res_recode[$this_day][$v['after']] +=1;
                }
                //获取新增的游客
                $where1['level']=0;
                $where1['times'] = array('between',[$time+$one_day*$i,$time+($one_day*($i+1))]);
                $trans =$account->where($where1)->count();
                $res_recode[$this_day][0] =(int)$trans;
                $times[] =date('m-d',$time+$one_day*$i);
            }*/

            //等级
            $time = strtotime(date('Y-m-d', $get_time)) - 6 * 24 * 3600;
            $one_day = 24 * 3600;
            for ($l = 0; $l < 7; $l++) {
                $where['after'] = $l;
                $res_data = $acc_record->where($where)->select();
                //初始化各各等级的数量
                $res_recode[$l] = array();
                foreach ($res_data as $k => $v) {
                    for ($d = 0; $d < 7; $d++) {
                        $this_day = date('m-d', $time + $one_day * $d);
                        $start_time = strtotime(date('Y-m-d', $time + ($one_day * $d)));
                        $end_time = strtotime(date('Y-m-d', $time + ($one_day * ($d + 1))));
                        if ($v['time'] <= $end_time && $v['time'] >= $start_time) {
                            $res_recode[$l][$this_day] += 1;
                        } else {
                            $res_recode[$l][$this_day] += 0;
                        }
//                        $where1['level'] = 0;
//                        $where1['times'] = array('between', [$time + $one_day * $d, $time + ($one_day * ($d + 1))]);
//                        $trans = $account->where($where1)->count();
//                        $res_recode[0][$this_day] = (int)$trans;
//                        $times[$this_day] = $this_day;
                    }
                }
                for ($d = 0; $d < 7; $d++) {
                    $this_day = date('m-d', $time + $one_day * $d);
                    $where1['level'] = 0;
                    $where1['times'] = array('between', [$time + $one_day * $d, $time + ($one_day * ($d + 1))]);
                    $trans = $account->where($where1)->count();
                    $res_recode[0][$this_day] = (int)$trans;
                    $times[$this_day] = $this_day;
                }
            }

            #3
            #获取等级出货总计
            #每日
            //用户出 提和转
            //当天
            $out_where['time'] = array('between', [$get_time, $get_time + $one_day]);
            //出售条件
            $out_where['type'] = array('in', '21,23');
            //获取当日用户出货数量
            $change_out_nums = $acc_num->where($out_where)->field('sum(nums),uid')->group('uid')->select();
            //初始化数据
            $out_num = array(
                0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0,
            );
            //获取等级出货数量
            foreach ($change_out_nums as $k => $v) {
                if ($v['uid'] != 0) {
                    $user_level = $account->where(['id' => $v['uid']])->field('level')->find();
                    $out_num[$user_level['level']] = $v['sum(nums)'];
                }
            }
            //出售 和后台调整
            // 同上逻辑
            $out_where['type'] = array('in', '11,12,13');
            $change_out_nums1 = $acc_num->where($out_where)->field('sum(nums),aboutid')->group('aboutid')->select();
            foreach ($change_out_nums1 as $k => $v) {
                if ($v['aboutid'] != 0) {
                    $user_level = $account->where(['id' => $v['aboutid']])->field('level')->find();
                    $out_num[$user_level['level']] = $v['sum(nums)'];
                }

            }
            #4
            #进货统计
            #每日
            #
            $in_where['time'] = array('between', [$get_time, $get_time + $one_day]);
            //条件
            $in_where['type'] = array('in', '21,23');
            //获取当日用户进货数量
            $change_in_nums = $acc_num->where($in_where)->field('sum(nums),aboutid')->group('aboutid')->select();
            //初始化数据
            $in_num = array(
                0 => 0, 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0,
            );
            //获取等级进货数量
            foreach ($change_in_nums as $k => $v) {
                if ($v['aboutid'] != 0) {
                    $user_level = $account->where(['id' => $v['aboutid']])->field('level')->find();
                    $in_num[$user_level['level']] = $v['sum(nums)'];
                }
            }
            //进 购买 后台系统调整
            // 同上逻辑
            $in_where['type'] = array('in', '11,12,13');
            $change_in_nums1 = $acc_num->where($in_where)->field('sum(nums),uid')->group('uid')->select();
            foreach ($change_in_nums1 as $k => $v) {
                if ($v['uid'] != 0){
                    $user_level = $account->where(['id' => $v['uid']])->field('level')->find();
                    $in_num[$user_level['level']] += $v['sum(nums)'];
                }
            }

            #5
            #根据周获取数据
            #
            ##
//        $arr_data  =$this->getLevelChange();
//        $week_data =$arr_data['code_nums'];
//        $week =$arr_data['weeks'];

            #6
            #获取本月的每天的用户升级数据
            ##
//        $daliy_data =$this->getThisMonDay();
//        $month_data =$daliy_data['data'];
//        $month_day =$daliy_data['day'];
//--
            //去除数据的键
            foreach ($res_recode as $k => $v) {
                $res_data[$k] = array_values($res_recode[$k]);
            }
//        foreach ($week_data as $k=>$v){
//            $week_datas[$k] =array_values($week_data[$k]);
//        }
//        foreach ($month_data as $k=>$v){
//            $month_datas[$k] =array_values($month_data[$k]);
//        }
            S('sd_level_count', $res, 8 * 3600);
            S('sd_level', $level, 8 * 3600);
            S('sd_time', $times, 8 * 3600);
            S('sd_time_data', $res_data, 8 * 3600);
            S('sd_daliy_out', $out_num, 8 * 3600);
            S('sd_daliy_in', $in_num, 8 * 3600);
       }
        $this->ajaxReturn([
            'level_count'=>$res,
            'level'=>$level,
            'time'=>$times,
            'time_data'=>$res_data,
            'daliy_out'=>$out_num,
            'daliy_in'=>$in_num,
//            'week_data'=>$week_datas,
//            'week'=>$week,
//            'mon_data'=>$month_datas,
//            'month_day'=>$month_day,
        ]);
    }

    public function getusersss(){
        $account=M('account');

        $acc_record= M('acc_record');
        $acc_num= M('acc_nums');
//        $get_time =time();
        $get_time =strtotime(date('Y-m-d',time()));
        $time = strtotime(date('Y-m-d', $get_time)) - 6 * 24 * 3600;
        $one_day = 24 * 3600;
        for ($l = 0; $l < 7; $l++) {
            $where['after'] = $l;
            $res_data = $acc_record->where($where)->select();
            //初始化各各等级的数量
            $res_recode[$l] = array();
            foreach ($res_data as $k => $v) {
                for ($d = 0; $d < 7; $d++) {
                    $this_day = date('m-d', $time + $one_day * $d);
                    $where1['level'] = 0;
                    $where1['times'] = array('between', [$time + $one_day * $d, $time + ($one_day * ($d + 1))]);
                    $trans = $account->where($where1)->count();
                    $res_recode[0][$this_day] = (int)$trans;
                    $times[$this_day] = $this_day;
                }
            }
        }
        var_dump($res_recode);
    }
    //获取用户升级的数据
    /**按周来获取数据
     * @return array
     */
    public function  getLevelChange(){
        $acc_record =M('acc_record');
        $account =M('account');
        //获取用户等级的分级
         $acc_level=M('acc_level');

        $res_recodes =S('week_data');
        $arr_week =S('weeks');

        if(!$res_recodes) {
            $level_count = $acc_level->count();
            //获取所有数据
            $res_data = $acc_record->field('time')->order('time asc')->select();
            $end_arr = end($res_data);
            //获取起始结束的时间
            $start = $res_data[0]['time'];
            $ends = $end_arr['time'];
            $arr_week = array();
            $res_recode = array();
            //获取周的周期
            $i = 1;
            do {
                $time = getWeek($start);
                $arr_week[] = '第' . $i . '周';
                for ($l = 0; $l < $level_count; $l++) {
                    $where['after'] = $l;
                    $res_datas = $acc_record->where($where)->select();
                    if ($res_datas) {
                        foreach ($res_datas as $k => $v) {
                            if ($v['time'] >= $time['start'] && $v['time'] <= $time['end']) {
                                $res_recode[$l][$i] += 1;
                            } else {
                                $res_recode[$l][$i] += 0;
                            }
                        }
                    } else {
                        $res_recode[$l][$i] = 0;
                    }
                    $where1['level'] = 0;
                    $where1['times'] = array('between', [$time['start'], $time['end']]);
                    $trans = $account->where($where1)->count();
                    $res_recode[0][$i] = (int)$trans;
                }
//          $code_nums[]=$acc_record->where($where)->field('after,count(id) as counts')->group('after')->select();
                $i++;
                $start = $time['end'] + 1;
            } while ($time['end'] <= $ends);
//        return ['weeks'=>$arr_week,'code_nums'=>$res_recode];
            foreach ($res_recode as $k => $v) {
                $res_recodes[$k] = array_values($res_recode[$k]);
            }
            S('week_data', $res_recodes, 8 * 3600);
            S('weeks', $arr_week, 8 * 3600);
        }
        $this->ajaxReturn(['weeks'=>$arr_week,'week_data'=>$res_recodes]);
    }

    /**
     *获取本月每一天的升级数据
     */
    public function  getThisMonDay(){
        $acc_record =M('acc_record');
        $acc_level=M('acc_level');
        $account=M('account');

        $res_recodes =S('mon_data');
        $times =S('month_day');

        if(!$res_recodes) {
            $level_count = $acc_level->count();
            $time = strtotime(date('Y-m-01', time()));
            $one_day = 24 * 3600 - 1;
            $day_nums = date('t');
            for ($l = 0; $l < $level_count; $l++) {
                $where['after'] = $l;
                $res_data = $acc_record->where($where)->select();
                //初始化各各等级的数量
                $res_recode[$l] = array();
                foreach ($res_data as $k => $v) {
                    for ($d = 0; $d <= $day_nums; $d++) {
                        $this_day = date('m-d', $time + $one_day * $d);
                        $start_time = strtotime(date('Y-m-d', $time + ($one_day * $d)));
                        $end_time = strtotime(date('Y-m-d', $time + ($one_day * ($d + 1))));
                        if ($v['time'] <= $end_time && $v['time'] >= $start_time) {
                            $res_recode[$l][$this_day] += 1;
                        } else {
                            $res_recode[$l][$this_day] += 0;
                        }
                        /*$where1['level']=0;
                        $where1['times'] = array('between',[$start_time,$end_time]);
                        $trans =$account->where($where1)->count();
                        $res_recode[0][$this_day] =(int)$trans;
                        unset($trans);*/
                        $times[$this_day] = $this_day;
                    }
                }
            }
//        return ['day'=>$times,'data'=>$res_recode];
            foreach ($res_recode as $k => $v) {
                $res_recodes[$k] = array_values($res_recode[$k]);
            }
            S('mon_data', $res_recodes, 8 * 3600);
            S('month_day', $times, 8 * 3600);
        }
        $this->ajaxReturn(['month_day'=>$times,'mon_data'=>$res_recodes]);
    }

    /**
     *获取用户各个等级的库存量
     */
    public function getUserStock(){
        $account =M('account');
        //所有除去公司的
        $where['sysid'] =array('not in','168hr,168cilent,168model,168staff,15b90e80d2b573');
        $where['recid'] =array('not in','168hr,168cilent,168model,168staff');
        $where['_logic'] ='or';
        $res =$account->where($where)->field('sum(stock),level')->group('level')->select();
        foreach ($res as $k=>$v){
            $res[$k]['count'] =$v['sum(stock)'];
        }
        //所有公司的
        $where1['sysid'] =array('in','168hr,168cilent,168model,168staff,15b90e80d2b573');
        $where1['recid'] =array('in','168hr,168cilent,168model,168staff');
        $where1['_logic'] ='or';

        $company =$account->where($where1)->field('sum(stock),level')->group('level')->select();
        foreach ($company as $k=>$v){
            $company[$k]['count'] =$v['sum(stock)'];
        }

        $level =M('acc_level')->select();
        $this->ajaxReturn(['data'=>$res,'level'=>$level,'company'=>$company]);
    }

/////////////
    public function basic() {
        $sysconfig = M("sysconfig");
        #
        $where["type"] = "base";
        $list = $sysconfig->where($where)->select();
        $this->assign("list", $list);
        $this->display();
    }

    ////////////////////////////////////////////////////////////////////////////

    /**
     * 物流
     */
    public function trans() {
        $systrans = M("systrans");

        $list = $systrans->select();
        $this->assign("list", $list);
        $this->display();
    }

    ////////////////////////////////////////////////////////////////////////////

    /**
     * 广告
     */
    public function ads() {
        $sysads = M("sysads");

        $list = $sysads->select();
        $this->assign("list", $list);
        $this->display();
    }

    ////////////////////////////////////////////////////////////////////////////

    /**
     * 系统设置
     */
    public function sett() {
        $this->display();
    }

    /**
     * 系统设置
     */
    public function sett_op() {
        $sysmang = M("sysmang");
        $post = I("post.");
        #
        $where["id"] = $this->ssid;
        $where["passwd"] = md5($post["old"]);
        $count = $sysmang->where($where)->count();
        if ($count <= 0) {
            return get_op_put(0, "旧密码错误");
        }
        if ($post["new"] == null) {
            return get_op_put(0, "新密码不能为空");
        }
        if ($post["vim"] != $post["new"]) {
            return get_op_put(0, "两次密码不一致");
        }
        if ($post["old"] == $post["new"]) {
            return get_op_put(0, "新旧密码不能一致");
        }
        $save["passwd"] = md5($post["new"]);
        if (!$sysmang->where($where)->save($save)) {
            return get_op_put(0, "密码修改失败");
        }
        return get_op_put(1, null, U("Index/out"));
    }

    /**
     *运费管理
     */
    public function transPay(){
        $trans_pay =M('trans_price');
        $syatrans =M('systrans');
        $place_table =M('trans_addr_price');
        $res_data=$trans_pay->select();
        foreach ($res_data as $k=>$v){/*
            $trans_id[]=$v['tran'];
            $res_data[$v['tran']]=$trans_pay->where(['tran'=>$v['tran']])->select();*/
            $res_data[$k]['tran'] =getTransNm($v['tran'],'name');
            $plcae=$place_table->where(['id'=>$v['isout']])->find();
            $res_data[$k]['place'] =$plcae['price'];
        }
        #地区信息

        $plcae=$place_table->select();
        $this->assign('plcae',$plcae);
        $trans=$syatrans->select();
        $this->assign('tran',$trans);
        $this->assign('trans_data',$res_data);
        $this->display();
    }

    /**
     *添加记录
     */
    public function add_transPay(){
        $trans_pay =M('trans_price');
        $place_table =M('trans_addr_price');
        $data =I('get.');
        $place =$data['place'];
        $price_res =$place_table->where(['price'=>$place])->find();
        if($price_res){
            $data['isout']=$price_res;
        }else{
            $res_id =$place_table->add(['price'=>$place]);
            $data['isout']=$res_id;
        }
        $data['gsn']='415244730815416283';
        $res =$trans_pay->add($data);
        if($res){
            $status=true;
        }else{
            $status=false;
        }
        $this->ajaxReturn(['status'=>$status]);
    }

    /**
     *修改运费的价格或者地区
     */
    public function update_transPay(){
        $data=I('get.');
        $trans_pay =M('trans_price');
        $place_table =M('trans_addr_price');

        $res =$place_table->where(['id'=>$data['id']])->save(['price'=>$data['price']]);
        if($res){
            $status=true;
        }else{
            $status=false;
        }
        $res =$trans_pay->where(['id'=>$data['id']])->save(['price'=>$data['price']]);
        if($res){
            $status=true;
        }else{
            $status=false;
        }

        $this->ajaxReturn(['status'=>$status]);
    }


    /**
     *发送短信
     */
    public function sendSms(){
        sleep(1);
        //发送短信
        $verify = D("Backet/Verif", "Logic");
        $uid =session('send_id')==null?27:session('send_id');
        $last =M('account')->order('id desc')->field('id')->find();
        $user_phone =M('account')->where(['id'=>$uid])->field('phone')->find();
        session('send_id','');
        $new_uid =$uid+1;
        session('send_id',$new_uid);
        if($user_phone !=null){
            $res = $verify->hoildaySms($user_phone['phone']);
        }
        $status=1;
        if($uid>$last){
            $status=0;
        }
        $this->ajaxReturn(['id'=>$uid,'status'=>$status]);
    }

    public function sendTempToAll(){
        //发送短信
        $length =I('length')==null?0:I('length');
       $all_openid =S('all_user_openid');
        if(!$all_openid){
            $allinfo =M('account')->field('openid')->select();
            $all_openid=array_column($allinfo,'openid');
            S('all_user_openid',$all_openid,4*3600);
        }
        if($length <count($all_openid)){
            #模板消息推送
            //当购买成功之后 用户库存增加 给用户发送模板消息
            $openid =$all_openid[$length];
            $wctemp = D("Home/Wctemp", "Logic");
            $user = ["openid" => $openid, "nickname" => '用户'];
            $data = [
                "type" => "notick",
                "head" => '168太空素食春节停运通知',
                "time" => '2019年1月26日下午两点',
                "types" =>'公告通知',
                "content" =>'春节来临之际，根据物流停运时间安排，168太空素食总部将于1月26日（本周六）下午两点至2月13日全天，停止物流发货，2019年2月14日恢复物流发货。 请各位会员提前备货，1月25日到1月29日下午五点前仅支持到总部提货。',
                "remake" =>'顺祝生活愉快，工作顺利。',
            ];
            $wctemp->entrance($user, $data);
            $length++;
            $this->ajaxReturn(['length'=>$length,'status'=>1]);
        }
        $this->ajaxReturn(['status'=>0]);
    }



//////////////////////////////////////
    //备份数据库设置
    public function dump_MysqL(){
        $shell ="mysqldump -u".C('DB_USER')." -p".C('DB_PWD')." ".C('DB_NAME')."> F:\database/cmd".date("YmdHis").'.sql';
        exec($shell);
    }
    ////////////////////
    ///  统计在线人数
    public function getOnLineUser(){
        header('Content-type: text/html; charset=utf-8');
        $online_log='count.txt';//保存在线人数数据的文件,
        if (!file_exists($online_log)) {
            mkdir($online_log, '0777');//linux下，某些文件缺少操作权限我们需要赋予操作权限
        }
        $entries=file('\\'.$online_log);//将文件作为一个数组返回，数组中的每个单元都是文件中相应的一行，包括换行符在内
        $temp=array();
        for($i=0;$i<count($entries);$i++){
            $entry=explode(',',trim($entries[$i]));
            if(count($entry)>1){
                if(($entry[0]!=getenv('REMOTE_ADDR'))&&($entry[1]>time())){
                    array_push($temp,$entry[0].','.$entry[1]."\n");//取出其他浏览者的信息,并去掉超时者,保存进$temp
                }
            }
        }
        array_push($temp,getenv('REMOTE_ADDR').','.(strtotime("+10second"))."\n");//更新浏览者的时间
        $users_online=count($temp);//计算在线人数
        $entries=implode('',$temp);
        //写入文件
        $fp=fopen('\\'.$online_log,'w');
        flock($fp,LOCK_EX);//注意 flock() 不能在NFS以及其他的一些网络文件系统中正常工作
        fputs($fp,$entries);
        flock($fp,LOCK_UN);
        fclose($fp);
        echo '当前有'.$users_online.'人在线';
    }


    public function info(){
		$info = array(
		'操作系统'=>PHP_OS,
		'运行环境'=>$_SERVER["SERVER_SOFTWARE"],
		'PHP运行方式'=>php_sapi_name(),
		'ThinkPHP版本'=>THINK_VERSION.' [ <a href="http://thinkphp.cn" target="_blank">查看最新版本</a> ]',
		'上传附件限制'=>ini_get('upload_max_filesize'),
		'执行时间限制'=>ini_get('max_execution_time').'秒',
		'服务器时间'=>date("Y年n月j日 H:i:s"),
		'北京时间'=>gmdate("Y年n月j日 H:i:s",time()+8*3600),
		'服务器域名/IP'=>$_SERVER['SERVER_NAME'].' [ '.gethostbyname($_SERVER['SERVER_NAME']).' ]',
		'剩余空间'=>round((disk_free_space(".")/(1024*1024)),2).'M',
		'register_globals'=>get_cfg_var("register_globals")=="1" ? "ON" : "OFF",
		'magic_quotes_gpc'=>(1===get_magic_quotes_gpc())?'YES':'NO',
		'magic_quotes_runtime'=>(1===get_magic_quotes_runtime())?'YES':'NO',
		);
		return $info;
		/*$this->assign('info',$info);
		$this->display();*/
	}
}
