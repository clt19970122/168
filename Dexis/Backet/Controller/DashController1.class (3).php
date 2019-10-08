<?php

namespace Backet\Controller;

#use Think\Controller;

//use function Sodium\add;

class DashController extends CommController {

    public function index() {
        $account = M("account");
        $orders = M("orders");
        //

        #
//        $res =$account->field('count(id),level')->group('level')->select();
//        $this->assign("count_user", $res);

        $res_data  =$this->getAboutMoney();
        $num['outnum'] =0;
        $num  =$this->getOutGood(1);
        $num['outnum'] =$num['outnum']==null?0:$num['outnum'];
        $this->assign("money",$res_data);
        $this->assign("num",$num);
        $this->assign("user", $account->count());
        $this->assign("memb", $account->count());
        $this->assign("ords", $orders->count());
        $this->assign("mony", $orders->where("status=3")->sum("money"));
        $this->display();
    }
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
                if(count($reback)>1){
                    $other_remoney +=$reback[1]['money'];
                }
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
//        $get_time =1543381128;
        #1
        #//获取等级用户数量总计
        #
        $res =$account->field('count(id),level')->group('level')->order('level asc')->select();
        foreach ($res as $k=>$v){
            $level[] =getUserLevel($v['level']);
            $res[$k]['counts'] =$v['count(id)'];
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
        $time =strtotime(date('Y-m-d',$get_time))-6*24*3600;
        $one_day =24*3600;
       for($l=0;$l<7;$l++){
           $where['after'] =$l;
           $res_data =$acc_record->where($where)->select();
           //初始化各各等级的数量
           $res_recode[$l] =array();
           foreach ($res_data as $k=>$v){
               for($d=0;$d<7;$d++){
                   $this_day =date('m-d',$time+$one_day*$d);
                   $start_time =strtotime(date('Y-m-d',$time+($one_day*$d)));
                   $end_time =strtotime(date('Y-m-d',$time+($one_day*($d+1))));
                   if($v['time']<=$end_time&&$v['time']>=$start_time) {
                       $res_recode[$l][$this_day] +=1;
                   }else{
                       $res_recode[$l][$this_day] +=0;
                   }
                   $where1['level']=0;
                   $where1['times'] = array('between',[$time+$one_day*$d,$time+($one_day*($d+1))]);
                   $trans =$account->where($where1)->count();
                   $res_recode[0][$this_day] =(int)$trans;
                   $times[$this_day] =$this_day;
               }
           }
       }


        #3
        #获取等级出货总计
        #每日
        //用户出 提和转
        //当天
        $out_where['time'] =array('between',[$get_time,$get_time+$one_day]);
        //出售条件
        $out_where['type'] =array('in','21,23');
        //获取当日用户出货数量
        $change_out_nums =$acc_num->where($out_where)->field('sum(nums),uid')->group('uid')->select();
        //初始化数据
        $out_num=array(
            0=>0, 1=>0, 2=>0, 3=>0, 4=>0, 5=>0, 6=>0,
        );
        //获取等级出货数量
        foreach ($change_out_nums as $k=>$v){
            if($v['uid'] !=0) {
                $user_level = $account->where(['id' => $v['uid']])->field('level')->find();
                $out_num[$user_level['level']] = $v['sum(nums)'];
            }
        }
        //出售 和后台调整
        // 同上逻辑
        $out_where['type'] =array('in','11,12,13');
        $change_out_nums1 =$acc_num->where($out_where)->field('sum(nums),aboutid')->group('aboutid')->select();
        foreach ($change_out_nums1 as $k=>$v){
            if($v['aboutid']!=0){
                $user_level =$account->where(['id'=>$v['aboutid']])->field('level')->find();
                $out_num[$user_level['level']] =$v['sum(nums)'];
            }

        }
        #4
        #进货统计
        #每日
        #
        $in_where['time'] =array('between',[$get_time,$get_time+$one_day]);
        //条件
        $in_where['type'] =array('in','21,23');
        //获取当日用户进货数量
        $change_in_nums =$acc_num->where($in_where)->field('sum(nums),aboutid')->group('aboutid')->select();
        //初始化数据
        $in_num=array(
            0=>0, 1=>0, 2=>0, 3=>0, 4=>0, 5=>0, 6=>0,
        );
        //获取等级进货数量
        foreach ($change_in_nums as $k=>$v){
            if($v['aboutid']!=0){
                $user_level =$account->where(['id'=>$v['aboutid']])->field('level')->find();
                $in_num[$user_level['level']] =$v['sum(nums)'];
            }
        }
        //进 购买 后台系统调整
        // 同上逻辑
        $in_where['type'] =array('in','11,12,13');
        $change_in_nums1 =$acc_num->where($in_where)->field('sum(nums),uid')->group('uid')->select();
        foreach ($change_in_nums1 as $k=>$v){
            if($v['uid']!=0) {
                $user_level = $account->where(['id' => $v['uid']])->field('level')->find();
                $in_num[$user_level['level']] += $v['sum(nums)'];
            }
        }
//        var_dump($res_recode);
        //去除数据的键
        foreach ($res_recode as $k=>$v){
            $res_data[$k] =array_values($res_recode[$k]);
        }
        $this->ajaxReturn(['level_count'=>$res,'level'=>$level,'time'=>$times,'time_data'=>$res_data,'daliy_out'=>$out_num,'daliy_in'=>$in_num]);
    }

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


    //备份数据库设置
    public function dump_MysqL(){
        $shell ="mysqldump -u".C('DB_USER')." -p".C('DB_PWD')." ".C('DB_NAME')."> F:\database/cmd".date("YmdHis").'.sql';
        exec($shell);
    }
}
