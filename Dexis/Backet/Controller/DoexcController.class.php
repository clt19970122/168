<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-08-17
 * Time: 16:51
 */

namespace Backet\Controller;



vendor("PHPExcel.PHPExcel");
vendor("PHPExcel.PHPExcel.Reader.Excel2007");

class DoexcController extends CommController
{
    /**财务提款导出
     * @param int $type
     */
    public function doExcel_fina($type=10){
        set_time_limit(0);
        $money_draw = M("money_draw");
        $get=I('get.');
        $msg='';
        #
        if($type!= 10) {
            $where["status"] = $type;
        }
        $get["name"] != "" ? $where["name"] = array("like", "%" . $get["name"] . "%") : null;
        if($get['start']||$get["ends"]){
            $start = strtotime($get["start"]);
            $ends = strtotime($get["ends"]);
            if(!$ends){
                $where["times"] = array("gt", $start);
            }if(!$start){
                $where["times"] = array("lt", $ends);
            }
            if ( $ends > $start) {
                $where["times"] = array(array("gt", $start), array("lt", $ends+86400));
                $msg .=$get["start"].'到'.$get["ends"];
            }
            if ($ends == $start){
                $where["times"] = array(array("gt", $start), array("lt", $ends+86400));
                $msg .=$get["start"].'到'.$get["ends"];
            }
        }
        $title=array('订单编号','上级用户','上级等级','提款用户','用户等级','金额','持卡人','提款银行','银行卡号','提款时间','打款状态');
        $cellName=array('sn','uname','ulevel','nickname','level','money','name','bank','bankcode','time','status');
        $data =$money_draw->where($where)->select();
        $datas=array();
        foreach ($data as $k=>$v){
            $datas[$k]['sn']=(string)$v['sn'];
            $recid=getUserInf($v['usid'],'recid');
            $parent_info =M('account')->where(array('sysid'=>$recid))->find();
            $datas[$k]['uname'] =$parent_info['name'];
            $datas[$k]['ulevel'] =getUserLevel($parent_info['level']);
            $datas[$k]['nickname'] =getUserInf($v['usid'],'nickname');
            $datas[$k]['level'] =getUserLevel(getUserInf($v['usid'],'level'));
            $datas[$k]['money'] =(string)$v['money'];
            $datas[$k]['name'] =$v['name'];
            $datas[$k]['bank'] =getBankName($v['bank']);
            $datas[$k]['bankcode'] =(string)$v['bankcode'];
//            $datas[$k]['time'] =date('Y-m-d H:i:s',$v['times']);
            $datas[$k]['time'] =date('Y-m-d H:i:s',$v['times']);
            $datas[$k]['status'] =$v['status']==0?'未支付':($v['status']==1?'已支付':'已退回');
        }
        //添加记录
        $user_info =session("admin_info");
        addExcel_list($user_info['ch_name'],$msg.'提现数据导出');
        $this->exportOrderExcel('提现数据',$title,$cellName,$datas);
//        $this->exportExcel($datas,date(Y-m-d).'导出数据',$cellName,'sheetname');
    }


    /**
     *0元计划导出
     */
    public function doExcel_plans(){
        set_time_limit(0);
        $order_sr = M("order_sr");
        $account = M("account");
        $acc_num =M('acc_nums');
        $get = I("get.");
        $msg ='';
        #
        if($get['start']||$get["ends"]){
            $start = strtotime($get["start"]);
            $ends = strtotime($get["ends"]);
            if(!$ends){
                $where["times"] = array("gt", $start);
                $msg .=$get["start"].'后';
            }if(!$start){
                $where["times"] = array("lt", $ends);
                $msg .=$get["ends"].'前';
            }
            if( $ends > $start) {
                $where["times"] = array(array("gt", $start), array("lt", $ends+86400));
                $msg .=$get["start"].'到'.$get["ends"];
            }
            if ($ends == $start){
                $where["times"] = array(array("gt", $start), array("lt", $ends+86400));
                $msg .=$get["start"].'到'.$get["ends"];
            }
        }
        $get['status']==''? null: $where["status"] =$get['status'];
        $get["sn"] != "" ? $where["sn"] = array("like", "%" . $get["sn"] . "%") : null;
        $get["name"] != "" ? $where["name"] = array("like", "%" . $get["name"] . "%") : null;
        $get["phone"] != "" ? $where["phone"] = array("like", "%" . $get["phone"] . "%") : null;
        $get["status"] != "" ? $where["status"] = $get["status"] : null;
        $title=array('序号','联创','出货人','出货人等级','出货人库存','出货人余额','返利人','返利人余额','返利人库存','推荐用户','下单用户','下单用户等级','订单编号','姓名','身份证','手机','购买金额','打款状态','申请时间','库存','已提现金额','账户余额','转入人','转入人库存','提货数','转入时间');
        $cellName=array('no','topuser','outname','outlive','outnum','outmoney','rebackname','rebackmoney','rebacknum','low_user','nickname','nicklevel','sn','name','idcard','phone','money','status','time','stock','draw','user_money','tname','stocks','pick','times');
        $data =$order_sr->where($where)->select();
        //var_dump($data);exit;
        $datas=array();

        foreach ($data as $k=>$v){
            //$user_info =$account->where(['id'=>$v['usid']])->find();
            //$user_info['nickname'];
            $userlevel =getUserInf($v['uid'],'level');
            /*if($userlevel!= 3){
                unset($data[$k]);
                continue;
            }*/

            //获取返利人
            $money_count =M('acc_money')->where(['sn'=>$v['sn'],'models'=>'REBACK'])->order('money desc')->select();
            //返利数据
            if(count($money_count)>1 && $money_count[1]['money']>0){
                $user_idss =$money_count[1]['uid'];
                $out_infos =M('account') ->where(['id'=>$user_idss])->find();
                $datas[$k]['rebackname'] =$out_infos['name'].'('.$out_infos['phone'].')';
                $datas[$k]['rebackmoney'] =$out_infos['money'];
                $datas[$k]['rebacknum'] =$out_infos['stock'];
            }
            $count='';
            $dataes=order_sr_pick($v['uid'],$count);
            $datas[$k]['tname']=$dataes['tname'];
            $datas[$k]['stocks']=$dataes['stocks'];
            $datas[$k]['times']=$dataes['times'];
            $datas[$k]['pick']=$dataes['pick'];
            /*$order_sr_where['type'] ='21';
            $order_sr_where['uid'] =$v['uid'];//aboutid
            $order_sr_data =$acc_num->where($order_sr_where)->order('time desc')->find();
            $user_count =$account->where(['id'=>$order_sr_data['aboutid']])->find();
            $datas[$k]['tname']=$user_count['name'] ==null?'':$user_count['name'];
            $datas[$k]['stocks']=$user_count['stock'] ==null?'':$user_count['stock'];
            $datas[$k]['times']=$order_sr_data ==null?'':date('Y-m-d H:i:s',$order_sr_data['time']);
            $order_sr_pick['type'] ='23';
            $order_sr_pick['uid'] =$user_count['id'];;
            $datas[$k]['pick'] =$acc_num->where($order_sr_pick)->sum("nums");*/
            //var_dump($datas[$k]['pick']);exit;
            $sysid =getUserInf($v['uid'],'sysid');
            $top_user=gettop($sysid,6);
            $datas[$k]['topuser']=$top_user['name'];
            $datas[$k]['low_user'] =getRecUser($v['uid']);
            $datas[$k]['nickname'] =getUserInf($v['uid'],'nickname');
            $datas[$k]['nicklevel'] =getUserLevel(getUserInf($v['uid'],'level'));
            $datas[$k]['user_money'] =getUserInf($v['uid'],'money');
            $datas[$k]['stock'] =getUserInf($v['uid'],'stock');
            //统计数量出货
            $out_info =M('acc_nums')->where(['sn'=>$v['sn']])->find();
            $out_infos =M('account') ->where(['id'=>$out_info['aboutid']])->find();
            $datas[$k]['outname'] =$out_infos['name'].'('.$out_infos['phone'].')';
            $datas[$k]['outlive'] =getUserLevel($out_infos['level']);
            $datas[$k]['outnum'] =$out_infos['stock'];
            $datas[$k]['outmoney'] =$out_infos['money'];
            $datas[$k]['sn']=(string)$v['sn'];
            $datas[$k]['name'] =$v['name'];
            $datas[$k]['money'] =$v['money'];
            $datas[$k]['no'] =$k+1;
            $datas[$k]['idcard'] =(string)($v['idcard']);
            $datas[$k]['phone'] =(string)$v['phone'];
//            $datas[$k]['time'] =date('Y-m-d H:i:s',$v['times']);
            $datas[$k]['time'] =date('Y-m-d H:i:s',$v['times']);
            $datas[$k]['status'] =getplansStatus($v['status']);
            $havedraw =M('money_draw')->where(['usid'=>$v['uid'],'status'=>1])->sum('money');
            $datas[$k]['draw'] =$havedraw;
        }

        //添加记录
        $user_info =session("admin_info");
        addExcel_list($user_info['ch_name'],$msg.'提现数据导出');
        $datas= array_merge($datas);
        $this->exportOrderExcel('0元计划数据',$title,$cellName,$datas);
    }


    /**
     *赚呗计划申请列表导出
     * 2019年7月17日14:36:33 add
     */
    public function doExcel_apply(){
        $get =I('get.');
        $model =M('order_zb');
        $where=[];
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
        $get["sn"] != "" ? $where["sn"] = array("like", "%" . trim($get["sn"]) . "%") : null;
        $get["phone"] != "" ? $where["phone"] = array("like", "%" . trim($get["phone"]) . "%") : null;
        $list= $model->where($where)->select();
        foreach($list as $k=>$v){
            $list[$k]['username'] =getUserInf($v['uid'],'name');
            $list[$k]['userphone'] =getUserInf($v['uid'],'phone');
            $list[$k]['times'] =date('Y-m-d H:i:s',$v['add_time']);
        }

        $title=array('系统编号','申请人系统名称','系统电话','收货人电话','收货人','收货地址','申请时间');
        $cellName=array('id','username','userphone','phone','name','address','times');
//添加记录
        $user_info =session("admin_info");

        addExcel_list($user_info['ch_name'],'赚呗数据导出');

        $this->exportOrderExcel('提货数据',$title,$cellName,$list);
    }

    /**
     *提货数据导出
     */
    public function doExcel_ordersr(){
        $order_dsr = M("order_drs");
        $get = I("get.");
        $msg ='';
        #
        if($get['start']||$get["ends"]){
            $start = strtotime($get["start"]);
            $ends = strtotime($get["ends"]);
            if(!$ends){
                $where["times"] = array("gt", $start);
                $msg .=$get["start"].'前';
            }if(!$start){
                $where["times"] = array("lt", $ends);
                $msg .=$get["start"].'后';
            }
            if( $ends > $start) {
                $where["times"] = array(array("gt", $start), array("lt", $ends+86400));
                $msg .=$get["start"].'到'.$get["ends"];
            }
            if ($ends == $start){
                $where["times"] = array(array("gt", $start), array("lt", $ends+86400));
                $msg .=$get["start"].'到'.$get["ends"];
            }
        }
        $username =$get['name'];
        if($username){
            $where_acc['nickname']=array("like", "%" .$username . "%");
            $acc_info=M('account')->where($where_acc)->select();
            foreach ($acc_info as $v){
                $uid[]=$v['id'];
            }
            $where['uid']=array('in',implode(',',$uid));
        }
        $get['status']==''? null: $where["status"] =$get['status'];
        $get["phone"] != "" ? $where["phone"] = array("like", "%" . $get["phone"] . "%") : null;
        $get["status"] != "" ? $where["status"] = $get["status"] : null;

        $title=array('申请人昵称','用户名称','申请时间','申请提货盒数（盒）','手机','收货人','收货地址','发货状态','备注','物流公司','物流单号','发货时间');
        $cellName=array('nickname','username','time','nums','phone','name','address','status','remakes');
        $data =$order_dsr->where($where)->select();
        $datas=array();
        foreach ($data as $k=>$v){
            $datas[$k]['nickname'] =getUserInf($v['uid'],'nickname');
            $datas[$k]['username'] =getUserInf($v['uid'],'name');
            $datas[$k]['nums']=(string)$v['nums'];
            $datas[$k]['name'] =$v['name'];
            $datas[$k]['address'] =$v['address'];
            $datas[$k]['phone'] =(string)$v['phone'];
            $datas[$k]['time'] =date('Y-m-d H:i:s',$v['times']);
            $datas[$k]['status'] =$v['status']==0?'审核中':($v['status']==1?'已通过':($v['status']==2?'未通过':($v['status']==3?'已发货':($v['status']==4?'自提':'取消支付'))));
            $datas[$k]['remakes'] =$v['remakes'];
            //获取上级用户信息
            $getoutUser =get_out_user($v['uid']);
            if(!$getoutUser){
                $datas[$k]['remakes'] .='出货人是金融未还款已提货用户，暂不发货';
            }
        }
        //添加记录
        $user_info =session("admin_info");
        if($get["status"]){
            $msg .= $datas[0]['status'];
        }
        addExcel_list($user_info['ch_name'],$msg.'提货数据导出');

        $this->exportOrderExcel('提货数据',$title,$cellName,$datas);
    }
    /**
     *用户数据导出
     */
    public function doExcel_user(){
        set_time_limit(0);
        $account = M("account");
        $get = I("get.");
        $msg ='';
        #
        if($get['start']||$get["ends"]){
            $start = strtotime($get["start"]);
            $ends = strtotime($get["ends"]);
            if(!$ends){
                $where["times"] = array("gt", $start);
                $msg .=$get["start"].'后';
            }if(!$start){
                $where["times"] = array("lt", $ends);
                $msg .=$get["ends"].'前';
            }
            if( $ends > $start) {
                $where["times"] = array(array("gt", $start), array("lt", $ends+86400));
                $msg .=$get["start"].'到'.$get["ends"];
            }
            if ($ends == $start){
                $where["times"] = array(array("gt", $start), array("lt", $ends+86400));
                $msg .=$get["start"].'到'.$get["ends"];
            }
        }
        $get["phone"] != null ? $where["phone"] = array("like", "%" . $get["phone"] . "%") : null;
        $get["nickname"] != null ? $where["nickname"] = array("like", "%" . $get["nickname"] . "%") : null;
        $get["level"] != null ? $where["level"] = $get["level"] : null;
        if($get["level"]){
            $msg +=getUserLevel($get["level"]);
        }
        if( $get["up_nickname"] != null ){
            $where['recid'] = $get["up_nickname"];
        }
        $title=array('id','上级姓名','昵称','真实姓名','电话','地址','会员等级','库存','进货量','账户余额','销售量','销售额','利润','加入时间','提现金额');
        $cellName=array('id','upname','nickname','name','phone','addr','level','stock','point','money','sale_num','salemoney','person','time','draw_money');
        $data =$account->where($where)->order('totalpoints desc')->select();
        $datas=array();
        foreach ($data as $k=>$v){
            $datas[$k]['id'] =$v['id'];
           $parent_info =$account->where(array('sysid'=>$v['recid']))->find();
           $datas[$k]['upname'] =$parent_info['nickname'];
           // $datas[$k]['uplevel'] =getUserLevel($parent_info['level']);
           $datas[$k]['nickname'] =getUserInf($v['id'],'nickname');
            $datas[$k]['name'] =(string)$v['name'];
            $datas[$k]['phone'] =(string)$v['phone'];
            $datas[$k]['addr'] =address($v['id']);
            $datas[$k]['money'] =(string)$v['money'];
            $datas[$k]['stock']=(string)$v['stock'];
            $datas[$k]['point']=(string)$v['totalpoints'];
            $datas[$k]['level'] =getUserLevel($v['level']);
            $datas[$k]['time'] =date('Y-m-d H:i:s',$v['times']);
            $datas[$k]['sale_num'] =$v['totalpoints']-$v['stock'];
            $datas[$k]['person'] =(string)$v['person'];
            //统计销售金额
            $summary=getUserProductAndMoneyInfo($v['id']);
            $datas[$k]['salemoney'] =$summary['salemoney'];
            $datas[$k]['rebackmoney'] =$summary['reback_money'];
            //提现
            $havedraws =M('money_draw')->where(['usid'=>$v['id'],'status'=>1])->sum('money');
            $datas[$k]['draw_money'] =$havedraws;
        }
        //添加记录
        $user_info =session("admin_info");
        addExcel_list($user_info['ch_name'],$msg.'用户数据导出');

        $this->exportOrderExcel('用户数据',$title,$cellName,$datas);
    }
    /**
     *认证用户导出
     */
    public function doExcel_userIdauth(){
        // set_time_limit(0);
        $account = M("acc_idauth");
        $get = I("get.");
        $msg ='';
        #
/*        if($get['start']||$get["ends"]){
            $start = strtotime($get["start"]);
            $ends = strtotime($get["ends"]);
            if(!$ends){
                $where["times"] = array("gt", $start);
                $msg .=$get["start"].'后';
            }if(!$start){
                $where["times"] = array("lt", $ends);
                $msg .=$get["ends"].'前';
            }
            if( $ends > $start) {
                $where["times"] = array(array("gt", $start), array("lt", $ends+86400));
                $msg .=$get["start"].'到'.$get["ends"];
            }
            if ($ends == $start){
                $where["times"] = array(array("gt", $start), array("lt", $ends+86400));
                $msg .=$get["start"].'到'.$get["ends"];
            }
        }*/
        // $get["phone"] != null ? $where["phone"] = array("like", "%" . $get["phone"] . "%") : null;
        // $get["name"] != null ? $where["name"] = array("like", "%" . $get["name"] . "%") : null;

        $title=array('真实姓名','电话','身份证','加入时间');
        $cellName=array('name','phone','idcard','time');
        $data =$account->where($where)->select();
        foreach ($data as $k=>$v){
            $datas[$k]['name'] =$v['name'];
            $datas[$k]['phone'] =$v['phone'];
            $datas[$k]['idcard'] =$v['idcard'];
            $datas[$k]['time'] =date('Y-m-d H:i:s',$v['times']);
        }
       // var_dump($datas);
              $data1 =array_merge($datas);
             // $user_info =session("admin_info");
         // addExcel_list('用户数据导出');
         // var_dump($data1);die;
        $this->exportOrderExcel('用户数据',$title,$cellName,$data1);
    }
    /**
     *团队导出
     */
    public function doExcel_team(){
        $account =M('account');
        $ssid =I('ssid');
        $level =I('level');
        $user =$account->where(['sysid'=>$ssid])->field('nickname,name')->find();
        $alldata=$account->field('id,recid,sysid,nickname,name,phone,status,level,times,stock,totalpoints,money')->select();
//        $arrr=category($alldata,$ssid,0);
        $arrr=getChilds($html,$ssid,$alldata,0);
        $list=$arrr;
        $level_data=array();
        foreach ($list as $k=>$v){
            $level_data[$v['level']] +=1;
            //判断等级
            if($level==$v['level']){
                if ($v['recid'] != null || $v['recid'] != 0) {
                    $recid = str_replace('.html', '', $v['recid']);
                    $parent_info = $account->where(array('sysid' => $recid))->find();
                    $list[$k]['parent_name'] = $parent_info['nickname'];
                    $list[$k]['parent_level'] = getUserLevel($parent_info['level']);
                    $list[$k]['level'] = getUserLevel($v['level']);
                    $list[$k]['time'] =date('Y-m-d H:i:s',$v['times']);
                }
            }else{
                unset($list[$k]);
            }
        }
        $data =array_merge($list);
        $title=array('id','上级昵称','上级等级','昵称','真实姓名','电话','会员等级','库存','积分','账户余额','加入时间');
        $cellName=array('id','parent_name','parent_level','nickname','name','phone','level','stock','totalpoints','money','time');
        //添加记录
        $user_info =session("admin_info");
        addExcel_list($user_info['ch_name'],$user['nickname'].'团队数据导出');
        $this->exportOrderExcel($user['nickname'].'团队数据',$title,$cellName,$data);
    }

    /**
     *订单信息到处
     */
    public function doExcel_Order(){
        $account = M("orders");
        $get = I("get.");
        $type=$get['type'];
        //获取推荐人信息
        $tjid=$get['tjid'];

        $msg ='';
        if($tjid ) {
            $select_acc = M('account')->where(['id' => $tjid])->find();
            $low_user = M('account')->where(['recid' => $select_acc['sysid']])->select();
            if(count($low_user)>0){
                foreach ($low_user as $v) {
                    $low_id[] = $v['id'];
                }
                $where['uid'] =array('in',implode(',',$low_id));
            }else{
                $where['uid'] =$low_user[0]['uid'];
            }
        }
        #
        #下单用户
        $username =$get['username'];
        if($username){
            $where_acc['nickname']=array("like", "%" .$username . "%");
            $acc_info=M('account')->where($where_acc)->select();
            foreach ($acc_info as $v){
                $uid[]=$v['id'];
            }
            $where['uid']=array('in',implode(',',$uid));
        }
        #时间
        if($get['start']||$get["ends"]){
            $start = strtotime($get["start"]);
            $ends = strtotime($get["ends"]);
            if(!$ends){
                $where["paytime"] = array("gt", $start);
                $msg .=$get["start"].'后';
            }if(!$start){
                $where["paytime"] = array("lt", $ends);
                $msg .=$get["ends"].'前';
            }
            if ($ends > $start) {
                $where["paytime"] = array(array("gt", $start), array("lt", $ends+86400));
                $msg .=$get["start"].'到'.$get["ends"];
            }
            if ($ends == $start){
                $where["paytime"] = array(array("gt", $start), array("lt", $ends+86400));
                $msg .=$get["start"].'到'.$get["ends"];
            }
        }
        $type != 10 ? $where["status"] = $type : null;
        $get["sn"] != "" ? $where["sn"] = array("like", "%" . $get["sn"] . "%") : null;
        //$get["name"] != "" ? $where["name"] = array("like", "%" . $get["name"] . "%") : null;
        $get["phone"] != "" ? $where["phone"] = array("like", "%" . $get["phone"] . "%") : null;
        $get["status"] != "" ? $where["status"] = $get["status"] : null;
        $get["paytype"] != "" ? $where["paytype"] = $get["paytype"] : null;
        $get["buy_level"] != "" ? $where["buy_level"] = $get["buy_level"] : null;

        //根据不同的id获取商品信息
        if($get['gid']) {
            $where["gid"] = $get["gid"];
        }
        $title=array('订单号','商品名','上级昵称','下单昵称','用户','手机','会员等级','购买数量','金额','状态','支付时间','支付方式','购买时等级');
        $cellName=array('sn','goods','upname','nickname','name','phone','level','nums','money','status','time','payway','buy_level');
        $data =$account->where($where)->order('id desc')->select();
        $datas=array();
        foreach ($data as $k=>$v){
            $datas[$k]['sn'] =$v['sn'];
            $datas[$k]['goods'] =$v['gname'];
            $datas[$k]['upname'] =getRecUser($v['uid']);
            $datas[$k]['nickname'] =getUserInf($v['uid'],'nickname');
            $datas[$k]['nickname'] =getUserInf($v['uid'],'name');
            $datas[$k]['phone'] =getUserInf($v['uid'],'phone');
            $datas[$k]['level']=getUserLevel(getUserInf($v['uid'],'level'));
            $datas[$k]['buy_level']=getUserLevel($v['buy_level']);
            $datas[$k]['nums']=$v['gnums'];
            $datas[$k]['money']=$v['money'];
            $datas[$k]['status'] =$v['status']==0?'待支付':($v['status']==1?'已支付':($v['status']==3?'待收获':($v['status']==4?'已取消':'')));
            $datas[$k]['time'] =date('Y-m-d H:i:s',$v['paytime']);
            $datas[$k]['payway'] =$v['paytype']==1?'微信':($v['paytype']==4?'余额':'其他');
        }
        //添加记录
        $user_info =session("admin_info");
        $msg .=$datas[0]['status'];
        addExcel_list($user_info['ch_name'],$msg.'订单数据导出');

        $this->exportOrderExcel('订单数据',$title,$cellName,$datas);
    }

    /**
     *订单摇摇杯信息
     */
    public function doExcel_cup(){
        $account = M("orders");
        $get = I("get.");
        $type=$get['type'];
        //获取推荐人信息
        $tjid=$get['tjid'];
        $msg='';
        if($tjid ) {
            $select_acc = M('account')->where(['id' => $tjid])->find();
            $low_user = M('account')->where(['recid' => $select_acc['sysid']])->select();
            if(count($low_user)>0){
                foreach ($low_user as $v) {
                    $low_id[] = $v['id'];
                }
                $where['uid'] =array('in',implode(',',$low_id));
            }else{
                $where['uid'] =$low_user[0]['uid'];
            }
        }
        #
        #下单用户
        $username =$get['username'];
        if($username){
            $where_acc['nickname']=array("like", "%" .$username . "%");
            $acc_info=M('account')->where($where_acc)->select();
            foreach ($acc_info as $v){
                $uid[]=$v['id'];
            }
            $where['uid']=array('in',implode(',',$uid));
        }
        #时间
        if($get['start']||$get["ends"]){
            $start = strtotime($get["start"]);
            $ends = strtotime($get["ends"]);
            if(!$ends){
                $where["paytime"] = array("gt", $start);
                $msg .=$get["start"].'后';
            }if(!$start){
                $where["paytime"] = array("lt", $ends);
                $msg .=$get["start"].'前';
            }
            if ($ends > $start) {
                $where["paytime"] = array(array("gt", $start), array("lt", $ends));
                $msg .=$get["start"].'到'.$get["ends"];
            }
            if ($ends == $start){
                $where["paytime"] = array(array("gt", $start), array("lt", $ends+86400));
                $msg .=$get["start"].'到'.$get["ends"];
            }
        }
//        $type != 10 ? $where["status"] = $type : null;
        $get["sn"] != "" ? $where["sn"] = array("like", "%" . $get["sn"] . "%") : null;
        //$get["name"] != "" ? $where["name"] = array("like", "%" . $get["name"] . "%") : null;
        $get["phone"] != "" ? $where["phone"] = array("like", "%" . $get["phone"] . "%") : null;
        $get["status"] != "" ? $where["status"] = $get["status"] : null;
        $get["paytype"] != "" ? $where["paytype"] = $get["paytype"] : null;
        $get["gid"] != "" ? $where["gid"] = $get["gid"] : null;


        $title=array('订单号','上级昵称','下单昵称','姓名','手机','地址','会员等级','商品单价','金额','状态','支付时间','支付方式');
        $cellName=array('sn','upname','nickname','name','phone','address','level','single','money','status','time','payway');
        $data =$account->where($where)->order('id desc')->select();
        $datas=array();
        $acc_addr =M('acc_addr');
        foreach ($data as $k=>$v){
            $addr_info =$acc_addr->where(['id'=>$v['addr']])->find();
            $datas[$k]['name'] =$addr_info['street'];
            $datas[$k]['phone'] =$addr_info['name'];
            $datas[$k]['address'] =$addr_info['phone'];

            $datas[$k]['sn'] =$v['sn'];
            $datas[$k]['upname'] =getRecUser($v['uid']);
            $datas[$k]['nickname'] =getUserInf($v['uid'],'nickname');;
            $datas[$k]['level']=getUserLevel(getUserInf($v['uid'],'level'));
            $datas[$k]['single']=$v['gprice'];
            $datas[$k]['money']=$v['money'];
            $datas[$k]['status'] =$v['status']==0?'待支付':($v['status']==1?'已支付':($v['status']==3?'待收获':($v['status']==4?'已取消':'')));
            $datas[$k]['time'] =date('Y-m-d H:i:s',$v['paytime']);
            $datas[$k]['payway'] =$v['paytype']==1?'微信':($v['paytype']==4?'余额':'');
        }
        //添加记录
        $user_info =session("admin_info");
        if($get["status"]){
            $msg .=$datas[0]['status'];
        }
        addExcel_list($user_info['ch_name'],$msg.'摇摇杯订单导出');

        $this->exportOrderExcel('摇摇杯订单信息导出',$title,$cellName,$datas);
    }

    /**
     *物流费用
     */
    public function doExcel_trans_pay(){
        $account = M("order_drs");
        $get = I("get.");
        $msg='';
        #时间
        if($get['start']||$get["ends"]){
            $start = strtotime($get["start"]);
            $ends = strtotime($get["ends"]);
            if(!$ends){
                $where["times"] = array("gt", $start);
                $msg .=$get["start"].'后';
            }if(!$start){
                $where["times"] = array("lt", $ends);
                $msg .=$get["start"].'前';
            }
            if ($ends > $start) {
                $where["times"] = array(array("gt", $start), array("lt", $ends));
                $msg .=$get["start"].'到'.$get["ends"];
            }
            if ($ends == $start){
                $where["times"] = array(array("gt", $start), array("lt", $ends+86400));
                $msg .=$get["start"].'到'.$get["ends"];
            }
        }
        $where['have_pay'] =1;
        $title=array('系统号','下单用户','收货人','支付运费','下单时间');
        $cellName=array('sn','nickname','name','pay_money','time');
        $data =$account->where($where)->order('id desc')->select();
        $datas=array();
        foreach ($data as $k=>$v){
            $datas[$k]['sn'] =$v['sn'];
            $datas[$k]['nickname'] =getUserInf($v['uid'],'nickname');
            $datas[$k]['name'] =$v['name'];
            $datas[$k]['pay_money'] =$v['trans_pay'];
            $datas[$k]['time'] =date('Y/m/d H:i:s',$v['times']);
        }
        //添加记录
        $user_info =session("admin_info");
        if($get["status"]){
            $msg .=$datas[0]['status'];
        }
        addExcel_list($user_info['ch_name'],$msg.'运费信息导出');
        $this->exportOrderExcel('运费信息导出',$title,$cellName,$datas);
    }

    /**
     *用户出货导出
     */
    public function doExcel_out(){
        $get =I('get.');
        $account =M('account');
        $acc_nums =M('acc_nums');
        $where['level'] =$get['buy_level']==null?6:$get['buy_level'];
        if($get['name']){
            $where['name'] =$get['name'];
        }
        $p =I('page');
        //
        $start=$get['start']==null?strtotime(date('Y-m')):strtotime($get['start']);
        $end =$get['ends']==null?strtotime(date('Y-m',strtotime('+1 month'))):strtotime($get['ends']);
        $num_where = array('between', [$start, $end]);
        $where['status'] =array('neq',0);
        $user_info =$account->where($where)->order('stock desc')->select();
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
            if($v['level'] !=6){
                 $topuser =gettop($v['sysid'],6);
                $user_info[$k]['topuser'] =$topuser['name'];
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
            $user_info[$k]['level'] =getUserLevel($v['level']);
            $user_info[$k]['get_time'] =date('Y-m-d',$start).'到'.date('Y-m-d',$end);
        }
        //排序
        array_multisort($arrs['innum'], SORT_DESC, $user_info);
        $title=array('联创','用户昵称','真实姓名','等级','进货量','出货量','库存量','时间段','总进货');
        $cellName=array('topuser','nickname','name','level','innums','outnums','stock','get_time','totalpoints');
//添加记录
        $user_infos =session("admin_info");
        $msg='';
        if($get["level"]){
            $msg .=$user_infos[0]['level'];
        }
        addExcel_list($user_infos['ch_name'],$msg.'用户进货量数据导出');

        $this->exportOrderExcel('用户进货量数据',$title,$cellName,$user_info);
    }

    /**
     *进销存的列表统计
     */
    public function doexcel_saleList(){
        $ware = M('goods_ware');
        $outList = M('goods_outlist');
        $get = I('get.');
        $gid =$get['gid'] ==null?8:$get['gid'];
        //产品总计
        $data_info = $ware->where(['goods_id'=>$gid])->find();
        $where['g_id'] = $gid;
        //判断进出货类型
        if(isset($get['type'])){
            $where['type'] = $get['type'];
        }
        //判断出的
        if($get['where']!=''){
            $where['where_in'] = $get['where'];
        }
        //时间
        if($get['start']||$get["ends"]){
            $start = strtotime($get["start"]);
            $ends = strtotime($get["ends"]);
            if(!$ends){
                $where["addtime"] = array("gt", $start);
            }if(!$start){
                $where["addtime"] = array("lt", $ends);
            }
            if( $ends > $start) {
                $where["addtime"] = array(array("gt", $start), array("lt", $ends));
            }
            if ($ends == $start){
                $where["addtime"] = array(array("gt", $start), array("lt", $ends+86400));
            }
        }
        //
        //产品列表
        $list =$outList->where( $where)->select();
//        $list = $outList->where($where)->select();
        foreach ($list as $k => $v) {
            $list[$k]['time'] = date('Y-m-d H:i:s', $v['addtime']);
            $list[$k]['inOrOut'] = $v['type'] ==1?'出货':'进货';
            $list[$k]['inwhere'] = getOutType($v['where_in']);
            if($v['uid']!=0){
                $list[$k]['inname'] = getUserInf($v['uid'],'nickname');
            }else{
                $list[$k]['inname'] ='系统线下出';
            }

        }
        //添加记录
        $user_info =session("admin_info");
        $msg='';
        //判断
        if($where['type'] ==2){
            $title=array('产品名称','进货数量','进货方式','剩余总库存','添加时间','供应商','备注');
            $cellName=array('name','nums','inwhere','rest_save','time','supplier','remake');

            addExcel_list($user_info['ch_name'],$msg.'进销存进货数据导出');

            $this->exportOrderExcel('进销存进货数据列表',$title,$cellName,$list);
        }else{
            $title=array('产品名称','出库数量','出货方式','剩余总库存','添加时间','提货人','备注');
            $cellName=array('name','nums','inwhere','rest_save','time','inname','remake');
            //添加记录
            addExcel_list($user_info['ch_name'],$msg.'进销存出货数据导出');

            $this->exportOrderExcel('进销存出货数据列表',$title,$cellName,$list);
        }

    }

    /**
     *进销存的报表统计
     */
    public function doExcel_saleTableList(){
        $get=I('get.');
        $goods_outlist = M('goods_outlist');
        if($get['where']!=null){
            $out_where =$get['where']!=1?array('neq',1):$get['where'];
            $where['where_in'] =$out_where;
        }
        if($get['start']||$get['ends']) {
            $where['addtime'] = array('between', [strtotime($get['start']), strtotime($get['ends'])+86400]);
        }
        if($get['gid']) {
            $where['g_id'] = $get['gid'];
        }
        if($get['table']==2){
            $where['type'] =1;
        }
        if($get['user']!=null){
            $accounr["name"] = array("like", "%" . $get["user"] . "%");
            $id =M('account')->where($accounr)->field('id')->select();
            $get_id=array_column($id,'id');
            $where['uid'] =array('in',implode(',',$get_id));
        }
        $resData =$goods_outlist->where($where)->order('id asc')->select();
        foreach ($resData as $k=>$v){
            //获取产品信息
            $goods_info =getIDGoodsInfo($v['g_id']);
            $res_data =explode('/',$goods_info['net']);
            $resData[$k]['model'] = $res_data[0];//规格型号
            $resData[$k]['unit'] = $res_data[1];//计量单位
            $resData[$k]['time'] = date('Y-m-d H:i:s', $v['addtime']);
            //获取价格信息 --联创
            $prices =doPrice($goods_info['gsn'],6);
            $resData[$k]['prices'] = $prices;
            //获取用户信息
            $username =getUserInf($v['uid'],'username');
            $username ==null?'线下出货':$username;
            $resData[$k]['username'] = $username;
            $resData[$k]['where'] = $v['where_in']==1?'中通仓库':'公司仓库';
            $resData[$k]['outnums']=0;
            $resData[$k]['innums']=0;
            if($v['type'] ==1){
                $resData[$k]['outnums'] = $v['nums'];
            }elseif($v['type'] ==2){
                $resData[$k]['innums'] = $v['nums'];
            }

            $resData[$k]['no'] = $k+1;
            $resData[$k]['paymoney'] = $v['prices']*$v['nums'];

        }
        //判断
        if($get['table']==2){
            $title=array('序号','产品名称','规格型号','计量单位','出库仓库','客户名称','出库时间','出库数量','销售金额','备注');
            $cellName=array('no','name','model','unit','where','username','time','nums','paymoney','remake');
            //添加记录
            $user_info =session("admin_info");
            $msg='';
            addExcel_list($user_info['ch_name'],$msg.'销售明细统计导出');

            $this->exportOrderExcel('销售明细统计表',$title,$cellName,$resData);
        }else{
            $title=array('序号','时间','仓库','产品名称','规格型号','计量单位','购进数量','出库数量','剩余库存','备注');
            $cellName=array('no','time','where','name','model','unit','innums','outnums','rest_save','remake');
            //添加记录
            $user_info =session("admin_info");
            $msg='';
            addExcel_list($user_info['ch_name'],$msg.'进销存汇总表导出');

            $this->exportOrderExcel('进销存汇总表',$title,$cellName,$resData);
        }
    }

    /**
     *返利明细
     */
    public function doExcel_Reback(){
        $get =I('get.');
        $acc_money =M('acc_money');
        $acc_num =M('acc_nums');
        $where['models'] ="REBACK";
        if($get['start']||$get['ends']){
            $start = strtotime($get["start"]);
            $ends = strtotime($get["ends"]);
            if(!$ends){
                $where["times"] = array("gt", $start);
            }if(!$start){
                $where["times"] = array("lt", $ends);
            }
            if( $ends > $start) {
                $where["times"] = array('between',[$start,$ends]);
            }
            if ($ends == $start){
                $where["times"] = array(array("gt", $start), array("lt", $ends+86400));
            }
        }
        $all_reback =$acc_money->where($where)->order('id desc')->select();
        $all_reback_money=0;
        $i =1;
        foreach ($all_reback as $k=>$v){

            if($v['money'] ==0){
                unset($all_reback[$k]);
            }else{
                //获取出货的用户
                $out_user =$acc_num ->where(['sn'=>$v['sn']])->field('aboutid')->find();
                //出货人为公司的话
                if($out_user['aboutid']!=0){
                    unset($all_reback[$k]);
                }else{
                    $all_reback[$k]['nickname'] =getUserInf($v['uid'],'nickname');
                    $all_reback[$k]['name'] =getUserInf($v['uid'],'name');
                    $all_reback[$k]['upuser'] =getRecUser($v['uid']);
                    $all_reback[$k]['level'] =getUserLevel(getUserInf($v['uid'],'level'));
                    $all_reback[$k]['time'] =date('Y-m-d H:i:s',$v['times']);
                    $all_reback_money+=$v['money'];
                    $all_reback[$k]['no'] =$i;
                    $i++;
                }
            }

        }
        $data =array_merge($all_reback);
        $title=array('序号','订单号','上级名称','用户等级','客户昵称','客户名称','返利金额','时间');
        $cellName=array('no','sn','upuser','level','nickname','name','money','time');
        //添加记录
        $user_info =session("admin_info");
        $msg='';
        addExcel_list($user_info['ch_name'],$msg.'返利明细表导出');
        $this->exportOrderExcel('返利明细表',$title,$cellName,$data);
    }
    /**
     *导出现金流明细
     */
    public function doExcel_money(){
        //根据类型获取时间
        $type =I('type');
        $in_type =I('in');
        $msg='';
        if($type =='undefined'){
            $type =null;
        }
        $type=$type==null?1:$type;

        $orders=M('orders');
        $acc_money=M('acc_money');
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
        if($in_type ==1) {
            $online_where['paytime'] = array('between', [$get_time, $end_time]);
            $online_info = $orders->where($online_where)->select();
            $datas  = $online_info == null ? 0 : $online_info;
            $msg  .='系统';
        }
        //后台添加
        if($in_type ==2) {
            $offline_where['models'] = 'ORDER';
            $offline_where['times'] = array('between', [$get_time, $end_time]);
            $offline_where['sn'] = array('like', '%2018A%');
            $offine_info = $acc_money->where($offline_where)->select();
            $datas = $offine_info == null ? 0 : $offine_info;
            $msg  .='后台添加';
        }
        //金融
        if($in_type ==3) {
            $jinrong_where['status'] = array('in', '5,4,7');
            $jinrong_where['passtime'] = array('between', [$get_time, $end_time]);
            $jr_data = $order_sr->where($jinrong_where)->select();
            $datas = $jr_data == null ? 0 : $jr_data;
            $msg  .='金融';
        }

       /* foreach($datas  as $k=>$v){
            $datas[$k]['no'] =getUserInf($v['uid'],'name');
            $datas[$k]['name'] =getUserInf($v['uid'],'name');
            $datas[$k]['nickname'] =getUserInf($v['uid'],'nickname');
            $datas[$k]['level'] =getUserLevel(getUserInf($v['uid'],'level'));
            $datas[$k]['time'] =date('Y-m-d H:i:s',$v['times']);
            $founder =gettop(getUserInf($v['uid'],'recid'),6);
            $datas[$k]['founder'] =$founder['name'];
            $out_info =M('acc_nums')->where(['sn'=>$v['sn']])->find();
            $datas[$k]['out_user'] =getUserInf($out_info['aboutid'],'name');
            $datas[$k]['time'] =date('Y-m-d H:i:s',$v['times']);
        }
        $title=array('序号','订单号','联创','出货人','用户等级','客户昵称','客户名称','收款金额','时间');
        $cellName=array('no','sn','founder','out_user','level','nickname','name','money','time');*/
        foreach($datas  as $k=>$v){
            $datas[$k]['no'] =$k+1;//序号
            $datas[$k]['name'] =getUserInf($v['uid'],'name');//客户名称
            $datas[$k]['nickname'] =getUserInf($v['uid'],'nickname');//客户昵称
            $datas[$k]['level'] =getUserLevel(getUserInf($v['uid'],'level'));//用户等级
            $founder =gettop(getUserInf($v['uid'],'recid'),6);
            $datas[$k]['founder'] =$founder['name'];//联创
            $out_info =M('acc_nums')->where(['sn'=>$v['sn']])->find();
            if($v['gid']==16){
                $datas[$k]['out_user'] ='公司';//出货人
            }else{
                $datas[$k]['out_user'] =getUserInf($out_info['aboutid'],'name');//出货人
            }
            
            $datas[$k]['time'] =date('Y-m-d H:i:s',$v['paytime']);//支付时间
            $datas[$k]['paytype'] =getpaytype($v['paytype']);//时间
        }
      //  $title=array('序号','订单号','联创','出货人','用户等级','客户昵称','客户名称','收款金额','时间');
        //$cellName=array('no','sn','founder','out_user','level','nickname','name','money','time');
        $title=array('序号','上级联创','出货人','商品名称','商品单价','购买数量','订单号','真实姓名','用户等级','支付金额','支付方式','支付时间');
        $cellName=array('no','founder','out_user','gname','gprice','gnums','sn','name','level','money','paytype','time');

        //添加记录
        $user_info =session("admin_info");
        addExcel_list($user_info['ch_name'],$msg.'现金流明细表导出');

        $this->exportOrderExcel('现金流明细表',$title,$cellName,$datas);
    }

    /** 导出返利金额
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     * @throws \PHPExcel_Writer_Exception
     */
    public function Doexcel_money_two(){

        $orders=M('orders');
        $acc_money=M('acc_money');
        $acc_num=M('acc_nums');
        $order_sr=M('order_sr');

        $type =I('type');
        $timestamp=time();
        $one_day = 24 * 3600-1;
        $get_time = strtotime(date('Y-m-d',$timestamp));
        $end_time = $get_time + $one_day;
        //由订单号分类
        $reback_money['times']=array('between',[$get_time,$end_time]);
//        $reback_money['models']='REBACK';
        $reback_money['models']='ORDER';
        $all_reback =$acc_money->where($reback_money)->group('sn')->select();
        $top_reback_money =0;
        $other_remoney =0;
//        $res_data =array_unique(array_column($all_reback,'sn'));

        foreach($all_reback as $k=>$v){
//            //获取出货的用户
//            $out_user =$acc_num ->where(['sn'=>$v['sn']])->field('aboutid')->find();
//            //出货人为公司的话
//            $reback =$acc_money->where(['sn'=>$v['sn'],'models'=>'REBACK','times'=>array('between',[$get_time,$end_time])])->order('money desc')->select();
//            if($out_user['aboutid']!=0){
//                //返利数据大于1条 && 出货不是公司
//                /*if(count($reback)>1){
//                    $other_remoney +=$reback[1]['money'];
//                }*/
//                $res =$acc_money->where(['sn'=>$v['sn'],'models'=>'ORDER'])->find();
//                $other_remoney +=$res['money'];
//            }else{
//                foreach ($reback as $kk=>$vv){
//                    $top_reback_money+=$vv['money'];
//                }
//            }
            $out_user =$acc_num ->where(['sn'=>$v['sn']])->field('aboutid')->find();
            if($out_user['aboutid']!=0) {
                $order_where['sn'] = $v['sn'];
                $res_data = $orders->where($order_where)->find();
                $return[$k]['no'] = $k + 1;
                $return[$k]['sn'] = $v['sn'];
                $return[$k]['name'] = getUserInf($res_data['uid'], 'name');
                $return[$k]['nickname'] = getUserInf($res_data['uid'], 'nickname');
                $return[$k]['time'] = date('Y-m-d H:i:s', $res_data['paytime']);
                $return[$k]['money'] = $res_data['money'];
                $return[$k]['level'] = getUserLevel($res_data['buy_level']);
            }
        }
        if($type==1){
            $datas =$return;
        }
        $title=array('序号','订单号','用户等级','客户昵称','客户名称','收款金额','支付时间');
        $cellName=array('no','sn','level','nickname','name','money','time');
        //添加记录
        $user_info =session("admin_info");
        addExcel_list($user_info['ch_name'],'资金返利明细导出');
        $this->exportOrderExcel('资金返利明细',$title,$cellName,$datas);
    }

    /**导出数量
     * @param int $levels
     * @param int $types
     * @param int $gid
     */
    public function  doexcel_nums($levels=6,$types=0,$gid=8){
        $orders = M('orders');
        $acc_nums = M('acc_nums');

        $orde_where['buy_level'] =$levels;
        $orde_where['gid'] =$gid;
//        $orde_where['paytime'] =array('gt',1);
        $orde_where['status'] =1;
        $order_num =$orders->where($orde_where)->select();
        foreach ($order_num as $k=>$v){
            $order_num[$k]['name'] =getUserInf($v['uid'],'name');
            $order_num[$k]['time'] =date('Y-m-d H:i:s',$v['paytime']);
            $order_num[$k]['num'] =$v['gnums'];
        }
        //后台添加
        $bac_where['type']=13;
        $bac_where['aboutid']=0;
        $bac_add_num =$acc_nums->where($bac_where)->select();
        $next =count($order_num);
        foreach ($bac_add_num as $k=>$v){
            $order_num[$next+$k]['sn'] =$v['sn'];
            $order_num[$next+$k]['name'] =getUserInf($v['uid'],'name');;
            $order_num[$next+$k]['time'] =date('Y-m-d H:i:s',$v['time']);
            $order_num[$next+$k]['num'] =$v['nums'];
        }
        $title=array('订单号','客户名称','数量','支付时间');
        $cellName=array('sn','name','num','time');
        //添加记录
        $user_info =session("admin_info");
        addExcel_list($user_info['ch_name'],'数量明细导出');

        $this->exportOrderExcel('数量明细',$title,$cellName,$order_num);
    }

    public function Doexcel_outgoods($start='',$ends='',$levels=6,$types=0,$gid=8){
        $account = M('account');
        $acc_nums = M('acc_nums');
        $acc_money = M("acc_money");

        $start_time = $start ;/*==null?strtotime('2018-8-10'):$start;*/
        $end_time = $ends    ;/*==null?time():$ends;*/

        $where['level'] = $levels == "" ? 6 : $levels;
        $get_leveluser = $account->where($where)->field('id,totalpoints,stock')->order('id desc')->select();
        foreach ($get_leveluser as $k => $v) {
            $pointarr[] = $v['totalpoints'];
            $stockarr[] = $v['stock'];
            $idarr[] = $v['id'];
        }
        $ids = implode(',', $idarr);
        $where_nums['uid'] = array('in', $ids);
        $where_nums['time'] = array('between',[strtotime($start_time),strtotime($end_time)]);
        $where_nums['type'] =array('lt',20);
        $where_nums['aboutid'] =0;
        $where_nums['status'] =1;

        $get_list = $acc_nums->where($where_nums)->order('id desc')->select();

        foreach($get_list as $k=>$v){
            $list[$k]['no'] =$k+1;
            $list[$k]['nums'] =$v['nums'];
    //统计销售额
           /* if($v['time']<strtotime('2019-1-1')){
                $salemoney +=$v['nums']*45;
            }else{
                $pay_info =$acc_money->where(['sn'=>$v['sn'],'models'=>'ORDER'])->find();
                $salemoney +=$pay_info['money'];
            }*/
            $allmun[]=$v['nums'];
            $pay_info =$acc_money->where(['sn'=>$v['sn'],'models'=>'ORDER'])->find();
            $list[$k]['sale_money'] =$pay_info['money'];
            $list[$k]['sn'] =$v['sn'];
            $list[$k]['time'] =date('Y-m-d H:i:s',$v['time']);
            $list[$k]['nickname'] =getUserInf($v['uid'],'nickname');
            $list[$k]['name'] =getUserInf($v['uid'],'name');
            $list[$k]['level'] =getUserInf($v['uid'],'level');
            $list[$k]['stock'] =getUserInf($v['uid'],'stock');
            $list[$k]['type'] ='售出';
            if($v['aboutid']!=0){
                $list[$k]['out_name'] =getUserInf($v['aboutid'],'nickname');
            }else{
                $list[$k]['out_name'] ='公司';
            }
        }
        $title=array('序号','订单号','等级','客户昵称','客户名称','数量','库存','金额','时间');
        $cellName=array('no','sn','level','nickname','name','nums','stock','sale_money','time');

        //添加记录
        $user_info =session("admin_info");
        addExcel_list($user_info['ch_name'],'进货明细导出');

        $this->exportOrderExcel('进货明细',$title,$cellName,$list);
    }

/*
*导出团队管理奖数据
*/
    public function  doexcel_team_money(){
        $acc_getteam = M('acc_getteam');
        $orde_where =array();
        $order_num =$acc_getteam->where($orde_where)->select();
        foreach ($order_num as $k=>$v){
            $order_num[$k]['name'] =getUserInf($v['uid'],'name');
            $order_num[$k]['nickname'] =getUserInf($v['uid'],'nickname');
            $order_num[$k]['time'] =date('Y-m-d H:i:s',$v['time']);
        }
        $title=array('月份','客户昵称','客户名称','奖励金额','团队进货量');
        $cellName=array('month','nickname','name','money','team_num');

        //添加记录
        $user_info =session("admin_info");
        addExcel_list($user_info['ch_name'],'团队管理奖数据导出');

        $this->exportOrderExcel('团队管理奖数据',$title,$cellName,$order_num);
    }


    /**
     *导出0元的黄金用户
     */
    public function  doexcel_gold_by_plan(){
        set_time_limit(0);
        $order_sr  =M('order_sr');
        $account =M('account');
        $sr_where['status'] = 5;
        $sr_id =$order_sr->where($sr_where)->select();
        $title=array('id','申请用户','联创','出货人','电话','会员等级','库存','申请金额','申请时间');
        $cellName=array('id','name','topname','outname','phone','level','stock','money','time');
        $datas=array();
        foreach ($sr_id as $k=>$v){
            $datas[$k]['id'] =$v['id'];
            $user_info =$account->where(['id'=>$v['uid']])->find();
            $datas[$k]['name'] =$user_info['name'];
            $top_user=gettop($user_info['sysid'],6);
            $datas[$k]['topname']=$top_user['name'].'('.$top_user['phone'].')';
            //统计数量出货
            $out_info =M('acc_nums')->where(['sn'=>$v['sn']])->find();
            $datas[$k]['outname'] =getUserInf($out_info['aboutid'],'name').'('.getUserInf($out_info['aboutid'],'phone').')';
            $datas[$k]['phone'] =(string)$user_info['phone'];
            $datas[$k]['level'] =getUserLevel($user_info['level']);
            $datas[$k]['stock']=(string)$user_info['stock'];
            $datas[$k]['money'] =(string)$v['money'];
            $datas[$k]['time'] =date('Y-m-d H:i:s',$v['times']);
        }
        //添加记录
        $user_info =session("admin_info");
        addExcel_list($user_info['ch_name'],'0元计划未还款用户数据导出');

        $this->exportOrderExcel('0元计划未还款用户数据',$title,$cellName,$datas);
    }

    /**
     *0元计划已提货部分
     */
    public function doexcel_drsByplan(){
        set_time_limit(0);
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
        //这个是金融提货的
        $sr_id =$order_sr->alias('s')
            ->where($sr_where)
            ->join('left join order_drs d on d.uid =s.uid')
            ->order('d.id  desc')
            ->field('s.uid uid ,s.sn ssn ,s.name na1,d.name na2,d.phone dphone,d.address dadd,d.trsn dtrsn,s.money smoney,s.times stim,d.times dtime,s.status sta ,d.status dsta,d.nums dnum')
            ->select();
        $title=array('序号','申请用户','申请盒数','申请订单号','申请金额','申请时间','申请状态','提货用户','提货电话','提货地址','提货数量','运单号','提货时间','发货状态','用户库存','用户等级','下单人电话');
        $cellName=array('id','name','buy_num','sn','money','times','status','getname','getphone','getaddress','getnums','trans','gettime','getstatus','stock','level','userphone');
        $datas=array();
        foreach ($sr_id as $k=>$v){
            $datas[$k]['id'] =$k;
            $datas[$k]['name'] =$v['na1'];
            $datas[$k]['sn'] =$v['ssn'];
            $datas[$k]['buy_num'] =$v['smoney']==3800?40:($v['smoney']==3000?40:($v['smoney']==1560?12:($v['smoney']==600?4:1)));
            $datas[$k]['getname'] =$v['na2'];
            $datas[$k]['getphone'] =(string)$v['dphone'];
            $datas[$k]['getaddress'] =$v['dadd'];
            $datas[$k]['trans']=(string)$v['dtrsn'];
            $datas[$k]['status']=getplansStatus($v['sta']);
            $datas[$k]['getstatus']=$v['dsta']==1?'待发货':($v['dsta']==2?'已取消':($v['dsta']==3?'已发货':($v['dsta']==4?'自提':'已取消')));
            $datas[$k]['getnums']=$v['dnum'];
            $datas[$k]['money'] =(string)$v['smoney'];
            $datas[$k]['times'] =date('Y-m-d H:i:s',$v['stim']);
            $datas[$k]['gettime'] =date('Y-m-d H:i:s',$v['dtime']);
            $datas[$k]['stock'] =getUserInf($v['uid'],'stock');
            $datas[$k]['level'] =getUserLevel(getUserInf($v['uid'],'level'));
            $datas[$k]['userphone'] =getUserInf($v['uid'],'phone');
        }

        //添加记录
        $user_info =session("admin_info");
        addExcel_list($user_info['ch_name'],'0元计划提货用户数据导出');

        $this->exportOrderExcel('0元计划提货用户数据',$title,$cellName,$datas);
    }

    /**
     *转货的计划记录
     * 未还款和已逾期的用户
     */
    public function turn_plan(){
        $order_sr =M('order_sr');
        $sr_where['s.status']=array('in','5,7');
        $sr_where['n.type']=21;
        $sr_id =$order_sr->alias('s')
            ->join('left join acc_nums n on  n.uid = s.uid')
            ->field('s.uid uid ,s.sn ssn , s.name sname,s.money smoney,n.aboutid aboutid , n.nums nnum,n.sn nsn ,n.time ntime')
            ->where($sr_where)->select();
        $title=array('序号','申请用户','申请盒数','申请订单号','申请金额','被转人','转盒数','转货单号','时间',);
        $cellName=array('id','name','buy_num','sn','money','getname','getnums','trans','gettime');
        $datas=array();
        foreach ($sr_id as $k=>$v){
            $datas[$k]['id'] =$k+1;
            $datas[$k]['name'] =$v['sname'];
            $datas[$k]['sn'] =$v['ssn'];
            $datas[$k]['money'] =$v['smoney'];
            $datas[$k]['buy_num'] =$v['smoney']==3800?40:($v['smoney']==3000?40:($v['smoney']==1560?12:($v['smoney']==600?4:1)));
            $datas[$k]['getname'] =getUserInf($v['aboutid'],'name');
            $datas[$k]['trans']=(string)$v['nsn'];
            $datas[$k]['getnums']=$v['nnum'];
            $datas[$k]['gettime'] =date('Y-m-d H:i:s',$v['ntime']);
        }
        //添加记录
        $user_info =session("admin_info");
        addExcel_list($user_info['ch_name'],'0元计划提货用户数据导出');

        $this->exportOrderExcel('0元计划转货用户数据',$title,$cellName,$datas);
    }

    /**
     *金融授信通过部分的发货详情
     */
    public function doexcel_out_plan(){
        set_time_limit(0);
        $order_sr  =M('order_sr');
        $order_dsr  =M('order_drs');

    }

    /**
     *导出部分能打款的用户
     */
    public function doexcel_by_plan_can_do(){
        set_time_limit(0);
        $money_draw =M('money_draw');
        $account=M('account');
        $acc_money=M('acc_money');
        $order_sr=M('order_sr');
        $acc_num=M('acc_nums');

        $get =I('get.');
        if($get['start']||$get["ends"]){
            $start = strtotime($get["start"]);
            $ends = strtotime($get["ends"]);
            if(!$ends){
                $where["times"] = array("gt", $start);
            }if(!$start){
                $where["times"] = array("lt", $ends);
            }
            if ( $ends > $start) {
                $where["times"] = array(array("gt", $start), array("lt", $ends+86400));
            }
            if ($ends == $start){
                $where["times"] = array(array("gt", $start), array("lt", $ends+86400));
            }
        }
        $where['status'] = 0;
        //提现数据
        $draw_data =$money_draw->where($where)->select();
        foreach ($draw_data as $k=>$v) {
            $draw_data[$k]['uid'] =$v['usid'];
            $draw_data[$k]['times'] =date('Y-m-d H:i:s',$v['times']);
            $draw_data[$k]['level'] =getUserLevel(getUserInf($v['usid'],'level'));
            $draw_data[$k]['sysname'] =getUserInf($v['usid'],'name');
            //查询本人是否金融
            $sr_where['uid'] = $v['usid'];
            $sr_where['status'] = array('in','5,7');
            $res_data =$order_sr->where($sr_where)->find();
            $user_info=$account->where(['id'=>$v['usid']])->find();
            //是金融
            if($res_data){
                //如果数量有变动 --转货和提货
                $nums_where['type'] =array('in','21,23');
                $nums_where['uid'] =$v['usid'];
                $nums =$acc_num->where($nums_where)->select();
                //售出
                $nums_where1['type'] =array('in','11,12');
                $nums_where1['aboutid'] =$v['usid'];
                $nums1 =$acc_num->where($nums_where1)->select();
                //存在提货
                if(!$nums && !$nums1){
                    unset($draw_data[$k]);
                    continue;
                }
            }
            //查询下面的用户的返利
                //if($user_info['level']==3){
                //返利到他的利润
                $last_draw['times'] =array('lt',$v['times']-20);
                $last_draw['uid'] =$v['usid'];
                $last_draw['models'] ='draw';
                //上次打款时间
                $last_data =$acc_money->where($last_draw)->order('id desc')->field('times')->find();
                if(!$last_data){
                    $last_data['times']=0;
                }
                $where_money['uid'] = $v['usid'];
                $where_money['times'] = array('between',[$last_data['times'],$v['times']]);
                $where_money['models'] ="REBACK";
                $have_reback =$acc_money->where($where_money)->select();
                $res_sta =array();
                //查询下面的人是什么走货
                foreach ($have_reback as $key=>$val){
                    //存在金融
                    $sr_res =$order_sr->where(['sn'=>$val['sn']])->find();
                    if($sr_res){
                        //是金融
                        $nums_where['type'] =array('in','21,23');
                        $nums_where['uid'] =$sr_res['uid'];
                        $nums =$acc_num->where($nums_where)->select();
                        //售出
                        $nums_where1['type'] =array('in','11,12');
                        $nums_where1['aboutid'] =$sr_res['uid'];
                        $nums1 =$acc_num->where($nums_where1)->select();
                        //不存在提货
                        if(!$nums && !$nums1){
                            $res_sta[] =1;
                        }
                    }
                }
                //存在不提货
//                echo 11;die;
                if(in_array(1,$res_sta)){
                    unset($draw_data[$k]);
                }
                unset($res_sta);
            /*}else{
                //删除不是黄金用户的提现
                unset($draw_data[$k]);
            }*/
        }
        $title=array('序号','用户id','系统名称','申请用户','订单号','金额','状态','时间','等级');
        $cellName=array('id','uid','sysname','name','sn','money','status','times','level');
        $draw_data =array_merge($draw_data);
//        addExcel_list($user_info['ch_name'],'0元计划提货用户数据导出');
        $this->exportOrderExcel('申请提现可打',$title,$cellName,$draw_data);
    }


    /**
     *导出可以退单
     */
    public function  doexcel_Order_ReBack(){
        $acc_num =M('acc_nums');
        $where['type']=25;
        $num_order=$acc_num->where($where)->select();
        foreach ($num_order as $k =>$v){
            $sr_where['sn'] =$v['sn'];
            $data[$k]['name'] =getUserInf($v['uid'],'name');
            $data[$k]['outname'] =getUserInf($v['aboutid'],'name');
            $data[$k]['sn'] =$v['sn'];
            $data[$k]['time'] =date('Y-m-d H:i:s',$v['time']);
            $data[$k]['no'] =$k+1;
        }
        $title=array('序号','出货人','退货人','订单号','时间');
        $cellName=array('no','outname','name','sn','time');
//        addExcel_list($user_info['ch_name'],'0元计划提货用户数据导出');
        $this->exportOrderExcel('退单',$title,$cellName,$data);
    }

    /**
     *现金打款
     */
    public function doexcel_cash_pay(){
        $money_draw =M('money_draw');
        $acc_money=M('acc_money');
        $order_sr=M('order_sr');
        $get =I('get.');
        if($get['start']||$get["ends"]){
            $start = strtotime($get["start"]);
            $ends = strtotime($get["ends"]);
            if(!$ends){
                $where["times"] = array("gt", $start);
            }if(!$start){
                $where["times"] = array("lt", $ends);
            }
            if ( $ends > $start) {
                $where["times"] = array(array("gt", $start), array("lt", $ends+86400));
            }
            if ($ends == $start){
                $where["times"] = array(array("gt", $start), array("lt", $ends+86400));
            }
        }
        $where['status'] = 0;
        //提现数据
        $res_data =$money_draw->where($where)->select();
        foreach ($res_data as $k=>$v){
            $res_data[$k]['times'] =date('Y-m-d H:i:s',$v['times']);
            $res_data[$k]['level'] =getUserLevel(getUserInf($v['usid'],'level'));
            $res_data[$k]['sysname'] =getUserInf($v['usid'],'name');
            $res_data[$k]['uid'] =$v['usid'];
            //查询本人是否金融
            $sr_where['uid'] = $v['usid'];
            $sr_where['status'] = array('in','5,7');
            $res_datad =$order_sr->where($sr_where)->find();
            //是金融未还款的
            if($res_datad){
                unset($res_data[$k]);
                continue;
            }

            $last_draw['times'] =array('lt',$v['times']+20);
            $last_draw['uid'] =$v['usid'];
            $last_draw['models'] ='draw';
            //上次打款时间
            $last_data =$acc_money->where($last_draw)->order('id desc')->field('times')->find();
            if(!$last_data){
                $last_data['times']=0;
            }
            $where_money['uid'] = $v['usid'];
            $where_money['times'] = array('between',[$last_data['times'],$v['times']]);
            $where_money['models'] ="REBACK";
            $have_reback =$acc_money->where($where_money)->select();
            //查询下面的人是什么走货
            foreach ($have_reback as $key=>$val){
                //存在金融
                $next_where['sn'] =$val['sn'];
                $next_where['status'] =array('in','5,7');
                $sr_res = $order_sr->where($next_where)->find();
                if($sr_res){
                    $res_sta[] =1;
                }
            }
            if(in_array(1,$res_sta)){
                unset($res_data[$k]);
            }
            unset($res_sta);
        }
        $title=array('序号','用户id','系统名称','申请用户','订单号','金额','状态','时间','等级');
        $cellName=array('id','uid','sysname','name','sn','money','status','times','level');
        $draw_data =array_merge($res_data);
//        addExcel_list($user_info['ch_name'],'0元计划提货用户数据导出');
        $this->exportOrderExcel('申请现金提现可打',$title,$cellName,$draw_data);
    }

    /**
     *导出序号和用户名
     */
    public function doto_plan_status(){
        $get=I('get.');
        $where['status'] = $get['status'];
        $order_sr =M('order_sr');
        //提现数据
        $res_data =$order_sr->where($where)->select();
        $title=array('序号','申请用户','订单号');
        $cellName=array('id','name','sn');
        $draw_data =array_merge($res_data);
//      addExcel_list($user_info['ch_name'],'0元计划提货用户数据导出');
        $this->exportOrderExcel('申请订单',$title,$cellName,$draw_data);
    }

    /**
     *支付记录
     */
    public function Doexcel_draw_code(){
        $draw_code =M('money_code');
        $data =I('get.');
        if($data['start']){
            $where['time'] =array('between',[$data['start'],$data['end']]);
        }
        $list = $draw_code->where($where)->select();
        foreach ($list as $k=>$v){
            $list[$k]['user'] =getUserInf($v['uid'],'name');
            $list[$k]['time'] =date('Y-m-d H:i:s',$v['time']);
        }
        $title=array('序号','用户id','订单号','支付单号','打款用户','打款金额','处理状态','打款时间','未通过原因');
        $cellName=array('id','uid','sn','user','money','status','time','msg');
//        $draw_data =array_merge($res_data);
//      addExcel_list($user_info['ch_name'],'0元计划提货用户数据导出');
        $this->exportOrderExcel('打款提现订单',$title,$cellName,$list);
    }

    /**
     *赛选黄金雨的
     */
    public function doExcel_getGoldList($dotype){
        set_time_limit(0);
        $order  =M('orders');
        $accNums  =M('acc_nums');
        $acc_recode =M('acc_record');
        $account  =M('account');
        $where['dotype'] = $dotype;
        $info=$acc_recode->where($where)->select();
        $where['id'] =array('in',implode(',',array_column($info,'uid')));
        $title=array('id','联创','出货人','推荐人','昵称','真实姓名','电话','地址','会员等级','库存','进货量','账户余额','销售量','销售额','利润','加入时间');
        $cellName=array('id','topuser','aboutname','upname','nickname','name','phone','addr','level','stock','point','money','sale_num','salemoney','person','time');
        $data =$account->where($where)->order('id desc')->select();
        $datas=array();
        foreach ($data as $k=>$v){
            $datas[$k]['id'] =$v['id'];
            $parent_info =$account->where(array('sysid'=>$v['recid']))->find();
            //出货人
            //出货人
            $uorder=getOrder($v['id'],$dotype);
            $datas[$k]['aboutname'] =getUserInf($uorder['aboutid'],'name');

            $datas[$k]['upname'] =$parent_info['name'];
            $top_user =gettop($v['sysid'],6);
            $datas[$k]['topuser'] =$top_user['name'];
            // $datas[$k]['uplevel'] =getUserLevel($parent_info['level']);
            $datas[$k]['nickname'] =getUserInf($v['id'],'nickname');
            $datas[$k]['name'] =(string)$v['name'];
            $datas[$k]['phone'] =(string)$v['phone'];
            $datas[$k]['addr'] =address($v['id']);
            $datas[$k]['money'] =(string)$v['money'];
            $datas[$k]['stock']=(string)$v['stock'];
            $datas[$k]['point']=(string)$v['totalpoints'];
            $datas[$k]['level'] =getUserLevel($v['level']);
            $datas[$k]['time'] =date('Y-m-d H:i:s',$v['times']);
            $datas[$k]['sale_num'] =$v['totalpoints']-$v['stock'];
            $datas[$k]['person'] =(string)$v['person'];
            //统计销售金额
            $summary=getUserProductAndMoneyInfo($v['id']);
            $datas[$k]['salemoney'] =$summary['salemoney'];
            $datas[$k]['rebackmoney'] =$summary['reback_money'];
        }
        //添加记录
        $user_info =session("admin_info");
        addExcel_list($user_info['ch_name'],$where['dotype'].'用户数据导出');

        $this->exportOrderExcel('用户数据',$title,$cellName,$datas);
    }



    /**
     *导出用户流
     */
    public function doExcel_user_info(){
        $id =I('id');//用户id
        $acc_money = M('acc_money');
        $acc_nums = M('acc_nums');
        $account = M('account');
        $syspansn = M('syspaysn');
        $order = M('orders');

        $order_sr =M('order_sr');
        $order_drs =M('order_drs');

        $where_money['uid'] =$id;
        $money_data =$acc_money->where($where_money)->order('id desc')->select();
        $res_data=[];
        foreach($money_data as $k=>$v){
            //订单详情
            $order_info =$order->where(['sn'=>$v['sn']])->find();
            //是否支付
            $have_pay=$syspansn->where(['resn'=>$v['sn']])->find();
            $nums_data =$acc_nums->where(['sn'=>$v['sn']])->find();
            $res_data[$k]['sn'] =$v['sn'];
            $res_data[$k]['in_name'] =getUserInf($nums_data['uid'],'name');
            $res_data[$k]['change_money'] =$v['models']!='REBACK'?'-' .$v['money']: $v['money'];
            $res_data[$k]['before_money'] =$v['befores'];
            $res_data[$k]['after_money'] =$v['afters'];
            $res_data[$k]['times'] =date('Y-m-d H:i:s',$v['times']);
            $res_data[$k]['do'] =$v['models']=='ORDER'?'订单':($v['models']=='REBACK'?'返现':($v['models']=='DRAW'?'提现':'退单'));
            if($nums_data) {
                $res_data[$k]['change_nums'] = $v['models'] == 'ORDER' ? $nums_data['nums'] : '-'.$nums_data['nums'] ;
            }

            //订单
            $res_data[$k]['order_type'] =getpaytype($order_info['paytype']);
            if($v['models']=="ORDER"){
                $reback =$acc_money->where(['models'=>'REBACK','sn'=>$v['sn']])->select();
                foreach ($reback as $v){
                    $reback_user = $account->where(['id' => $v['uid']])->find();
                    $rebk[]='给:'.$reback_user['nickname'].'-返:'.$v['money'];
                }
                $res_data[$k]['reback_info'] =implode('/',$rebk);
                unset($rebk);
            }
            //提现
            if($v['models']=='DRAW'){
                $money_draw =M('money_draw');
                $money_data =$money_draw->where(['sn'=>$v['sn']])->find();
                $res_data[$k]['money_do'] =$money_data['status']==0?"待支付":($money_data['status']==1?"已支付":'已退回');
                $res_data[$k]['order_type'] ='提现';
            }
            if($v['sn']!=0){
//                $nums_data =$acc_nums->where(['sn'=>$v['sn']])->find();
                //存在订单
                if($nums_data){
//                    $res_data[$k]['change_nums'] =$v['models']=='ORDER'?'<span style="color: #3ab22c;font-size: 18px">+' .$nums_data['nums'].'</span>': '<span style="color: #b2282c;font-size: 18px">-' .$nums_data['nums'].'</span>';
                    //进货出货都不是uid
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
                        $up_user['nickname'] = '公司';
                    }
                    $res_data[$k]['out_name'] = $up_user['name'];
                    $res_data[$k]['up_level'] = $up_user['level'];
                }
                #
                //查询金融里面是否存在这个订单
                //这个订单是否发货
                $order_info =$order_sr->where(['sn'=>$v['sn']])->find();
                //存在这个单号  查询是否提货
                if($order_info){
                    //金融状态
                    $res_data[$k]['order_type'] ='金融';
                    $res_data[$k]['plan_status'] = getplansStatus($order_info['status']);

                    $drs_where['uid'] =$order_info['uid'];
                    $drs_where['status'] =array('in','3,4');
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
        #
        //提货记录
        $nums_where['uid']= $id;
        $nums_where['aboutid']= $id;
        $nums_where['_logic']= 'or';
        $nums_data =$acc_nums->where($nums_where)->order('id desc')->select();
        $countadd =count($res_data);
        foreach ($nums_data as $k=>$v){
            $add_key =$countadd+$k;
            if($v['uid']==$id){
                if($v['type']==11) {
                    $res_data[$add_key]['sn'] =$v['sn'];
                    $res_data[$add_key]['change_nums'] =$v['uid']==$id?$v['nums']:'-'.$v['nums'];
                    $res_data[$add_key]['times'] =date('Y-m-d H:i:s',$v['time']);
                    $res_data[$add_key]['do'] =$v['type']='金融';
                    $res_data[$add_key]['after_nums'] = $v['uid'] == $id? $v['uafter'] : $v['after'];
                    $uinfo = $account->where(['id' => $v['aboutid']])->find();
                    $res_data[$add_key]['out_name'] = $uinfo['name'];
                    $res_data[$add_key]['out_level'] = $uinfo['level'];
                    $about_info = $account->where(['id' => $v['uid']])->find();
                    $res_data[$add_key]['in_name'] = $about_info['name'];
                    $res_data[$add_key]['dostatus'] = $v['status'] ==1?"":"已取消";
                    //查询返利
                    $reback =$acc_money->where(['models'=>'REBACK','sn'=>$v['sn']])->select();
                    foreach ($reback as $vv){
                        $reback_user = $account->where(['id' => $vv['uid']])->find();
                        $rebks[]='给:'.$reback_user['nickname'].'-返:'.$vv['money'];
                    }
                    $res_data[$add_key]['reback_infos'] =implode('/',$rebks);
                    unset($rebks);

                    $order_info =$order_sr->where(['sn'=>$v['sn']])->find();
                    if($order_info) {
                        //金融状态
                        $res_data[$k]['plan_status'] = getplansStatus($order_info['status']);
                    }
                }
                if($v['type']==13) {
                    $res_data[$add_key]['sn'] =$v['sn'];
                    $res_data[$add_key]['change_nums'] =$v['uid']==$id?$v['nums']:'-'.$v['nums'];
                    $res_data[$add_key]['times'] =date('Y-m-d H:i:s',$v['time']);
                    $res_data[$add_key]['do'] =$v['type']='现金支付';
                    $res_data[$add_key]['after_nums'] = $v['uid'] == $id? $v['uafter'] : $v['after'];
                    $uinfo = $account->where(['id' => $v['aboutid']])->find();
                    $res_data[$add_key]['out_name'] = $uinfo['nickname'];
                    $res_data[$add_key]['out_level'] = $uinfo['level'];
                    $about_info = $account->where(['id' => $v['uid']])->find();
                    $res_data[$add_key]['in_name'] = $about_info['nickname'];
                    $res_data[$add_key]['dostatus'] = $v['status'] ==1?"":"已取消";
                }
            }
            if($v['type']>20) {
                $res_data[$add_key]['sn'] =$v['sn'];
                $res_data[$add_key]['change_nums'] =$v['uid']==$id?'-'.$v['nums']:$v['nums'];
                $res_data[$add_key]['sn'] =$v['sn'];
                $res_data[$add_key]['times'] =date('Y-m-d H:i:s',$v['time']);
//                $nums_res[$k]['do'] =$v['type']==21?'转货':($v['type']==23?'提货':'扣除');
                $res_data[$add_key]['do'] =$v['type']==21?'转货':($v['type']==23?'提货':($v['type']==24?'后台转':'退单'));//21 23 24
                /* if($v['type']==21){
                     if($v['uid'] == $id){
                         $theory_money =getTheOryMoney($v['uid'],$v['aboutid']) *$v['nums'];
                     }
                 }*/
                $res_data[$add_key]['after_nums'] = $v['uid'] == $id? $v['uafter'] : $v['after'];
                $uinfo = $account->where(['id' => $v['uid']])->find();
                $res_data[$add_key]['out_name'] = $uinfo['nickname'];
                $res_data[$add_key]['out_level'] = $uinfo['level'];
                if ($v['type'] != 23) {
                    $about_info = $account->where(['id' => $v['aboutid']])->find();
                    $res_data[$add_key]['in_name'] = $about_info['nickname'];
                }
                $res_data[$add_key]['dostatus'] = $v['status'] ==1?"":"已取消";
            }
        }

        $title=array('序号','支付方式','订单号','出货人','收货人','变动数量','变动金额','返利人-余额','订单状态','时间','订单类型','金融状态');
        $cellName=array('id','order_type','sn','out_name','in_name','change_nums','change_money','reback_infos','status','times','do','plan_status');
        $draw_data =array_merge($res_data);
//        addExcel_list($user_info['ch_name'],'0元计划提货用户数据导出');
        $this->exportOrderExcel('账户明细信息',$title,$cellName,$draw_data);

    }


    /**
     *导出金额进帐记录明细
     */
    public function doExcel_money_in(){
        $paysn=M('syspaysn');
        $data =I('get.');
        if($data['start']||$data['end']){
            $where['times'] = array('between',[strtotime($data['start']),strtotime($data['end'])]);
        }
        $where['body'] ='微信支付';
        $where['status'] = 1;
        $list =$paysn->where($where)->select();
        $datas =array();
        foreach($list as $k=>$v){
            $datas[$k]['no'] =$k +1;
            $datas[$k]['time'] =$v['times'];
            $datas[$k]['inway'] =$v['models'] =='TRAN'?'提货支付':'购买支付';
            $datas[$k]['syssn'] ='`'.$v['sn'];//微信支付单号
            $datas[$k]['order_sn'] ='`'.$v['resn'];//订单编号
            $datas[$k]['money'] =$v['money'];
            $datas[$k]['status'] =$v['status'] ==1?'已支付':' ';
            $order_info =M('orders')->where(['sn'=>$v['resn']])->find();

            $datas[$k]['g_name'] =$order_info['gname'];
            $datas[$k]['level'] =getUserLevel($order_info['buy_level']);
            $datas[$k]['u_name'] =getUserInf($order_info['uid'],'name');
            $datas[$k]['price'] =$order_info['gprice'];
            $datas[$k]['nums'] =$order_info['gnums'];
        }
        $title=array('序号','商品名称','关联微信支付单号','系统订单号','金额','单价','数量','用户名称','购买等级','收款方式','状态','时间');
        $cellName=array('no','g_name','syssn','order_sn','money','price','nums','u_name','level','inway','status','time');
//        addExcel_list($user_info['ch_name'],'0元计划提货用户数据导出');
        $this->exportOrderExcel('收款记录',$title,$cellName,$datas);
    }


//////////////////////////////////////////////////////
    /** excel 导出表格
     * @param $name @ 文件名
     * @param $title @表头
     * @param $cellName @列字段
     * @param $data @数据
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     * @throws \PHPExcel_Writer_Exception
     */
    private function exportOrderExcel($name, $title, $cellName, $data)
    {
        //引入核心文件
        //var_dump($data);die;
        $objPHPExcel = new \PHPExcel();
        //定义配置
        $cacheMethod = \PHPExcel_CachedObjectStorageFactory::cache_in_memory_serialized;

        $topNumber = 2;//表头有几行占用
        $xlsTitle = iconv('utf-8', 'gb2312', $name);//文件名称
        $fileName = $name.date('_YmdHis');//文件名称
        $cellKey = array(
            'A','B','C','D','E','F','G','H','I','J','K','L','M',
            'N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
            'AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM',
            'AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ'
        );
        //处理表头标题
        $objPHPExcel->getActiveSheet()->mergeCells('A1:'.$cellKey[count($cellName)-1].'1');//合并单元格（如果要拆分单元格是需要先合并再拆分的，否则程序会报错）
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1','订单信息');
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(18);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);

        //处理表头
        foreach ($title as $k=>$v)
        {/*
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellKey[$k].$topNumber, $v[1]);//设置表头数据
            $objPHPExcel->getActiveSheet()->freezePane($cellKey[$k].($topNumber+1));//冻结窗口*/
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellKey[$k].$topNumber, $v);//设置表头数据
            $objPHPExcel->getActiveSheet()->getStyle($cellKey[$k].$topNumber)->getFont()->setBold(true);//设置是否加粗
            $objPHPExcel->getActiveSheet()->getStyle($cellKey[$k].$topNumber)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);//垂直居中
            $objPHPExcel->getActiveSheet()->getColumnDimension($cellKey[$k])->setWidth(20);//设置列宽度

        }

        //处理数据
        foreach ($data as $k=>$v)
        {
            foreach ($cellName as $k1=>$v1)
            {
                $objPHPExcel->getActiveSheet()->setCellValue($cellKey[$k1].($k+1+$topNumber),' '. $v[$v1]);
                if($v['end'] > 0)
                {
                    if($v1[2] == 1)//这里表示合并单元格
                    {
                        $objPHPExcel->getActiveSheet()->mergeCells($cellKey[$k1].$v['start'].':'.$cellKey[$k1].$v['end']);
                        $objPHPExcel->getActiveSheet()->getStyle($cellKey[$k1].$v['start'])->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    }
                }
                if($v1[4] != "" && in_array($v1[4], array("LEFT","CENTER","RIGHT")))
                {
                    $v1[4] = eval('return PHPExcel_Style_Alignment::HORIZONTAL_'.$v1[4].';');
                    //这里也可以直接传常量定义的值，即left,center,right；小写的strtolower
                    $objPHPExcel->getActiveSheet()->getStyle($cellKey[$k1].($k+1+$topNumber))->getAlignment()->setHorizontal($v1[4]);
                }
            }
        }


        //导出execl
        ob_end_clean();//清除缓存
        header('pragma:public');
        header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$xlsTitle.'.xls"');
        header("Content-Disposition:attachment;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

    /**
     * 导出excel
     * @param array $data 导入数据
     * @param string $savefile 导出excel文件名
     * @param array $fileheader excel的表头
     * @param string $sheetname sheet的标题名
     */
//    public function exportExcel($data, $savefile, $fileheader, $sheetname){
//        //或者excel5，用户输出.xls，不过貌似有bug，生成的excel有点问题，底部是空白，不过不影响查看。
//        //import("Org.Util.PHPExcel.Reader.Excel5");
//        //new一个PHPExcel类，或者说创建一个excel，tp中“\”不能掉
//        $excel = new \PHPExcel();
//        if (is_null($savefile)) {
//            $savefile = time();
//        }else{
//            //防止中文命名，下载时ie9及其他情况下的文件名称乱码
//            iconv('UTF-8', 'GB2312', $savefile);
//        }
//        //设置excel属性
//        $objActSheet = $excel->getActiveSheet();
//        //根据有生成的excel多少列，$letter长度要大于等于这个值
//        $letter = array('A','B','C','D','E','F','F','G');
//        //设置当前的sheet
//        $excel->setActiveSheetIndex(0);
//        //设置sheet的name
//        $objActSheet->setTitle($sheetname);
//        //设置表头
//        for($i = 0;$i < count($fileheader);$i++) {
//            //单元宽度自适应,1.8.1版本phpexcel中文支持勉强可以，自适应后单独设置宽度无效
//            //$objActSheet->getColumnDimension("$letter[$i]")->setAutoSize(true);
//            //设置表头值，这里的setCellValue第二个参数不能使用iconv，否则excel中显示false
//            $objActSheet->setCellValue("$letter[$i]1",$fileheader[$i]);
//            //设置表头字体样式
//            $objActSheet->getStyle("$letter[$i]1")->getFont()->setName('微软雅黑');
//            //设置表头字体大小
//            $objActSheet->getStyle("$letter[$i]1")->getFont()->setSize(12);
//            //设置表头字体是否加粗
//            $objActSheet->getStyle("$letter[$i]1")->getFont()->setBold(true);
//            //设置表头文字垂直居中
//            $objActSheet->getStyle("$letter[$i]1")->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            //设置文字上下居中
//            $objActSheet->getStyle($letter[$i])->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
//            //设置表头外的文字垂直居中
//            $excel->setActiveSheetIndex(0)->getStyle($letter[$i])->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//        }
//        //单独设置D列宽度为15
//        $objActSheet->getColumnDimension('D')->setWidth(15);
//        //这里$i初始值设置为2，$j初始值设置为0，自己体会原因
//        for ($i = 2;$i <= count($data) + 1;$i++) {
//            $j = 0;
//            foreach ($data[$i - 2] as $key=>$value) {
//                //不是图片时将数据加入到excel，这里数据库存的图片字段是img
//                if($key != 'img'){
//                    $objActSheet->setCellValue("$letter[$j]$i",$value);
//                }
//                //是图片是加入图片到excel
//                if($key == 'img'){
//                    if($value != ''){
//                        $value = iconv("UTF-8","GB2312",$value); //防止中文命名的文件
//                        // 图片生成
//                        $objDrawing[$key] = new \PHPExcel_Worksheet_Drawing();
//                        // 图片地址
//                        $objDrawing[$key]->setPath('.\Uploads'.$value);
//                        // 设置图片宽度高度
//                        $objDrawing[$key]->setHeight('80px'); //照片高度
//                        $objDrawing[$key]->setWidth('80px'); //照片宽度
//                        // 设置图片要插入的单元格
//                        $objDrawing[$key]->setCoordinates('D'.$i);
//                        // 图片偏移距离
//                        $objDrawing[$key]->setOffsetX(12);
//                        $objDrawing[$key]->setOffsetY(12);
//                        //下边两行不知道对图片单元格的格式有什么作用，有知道的要告诉我哟^_^
//                        //$objDrawing[$key]->getShadow()->setVisible(true);
//                        //$objDrawing[$key]->getShadow()->setDirection(50);
//                        $objDrawing[$key]->setWorksheet($objActSheet);
//                    }
//                }
//                $j++;
//            }
//            //设置单元格高度，暂时没有找到统一设置高度方法
//            $objActSheet->getRowDimension($i)->setRowHeight('80px');
//        }
//        header('Content-Type: application/vnd.ms-excel');
//        //下载的excel文件名称，为Excel5，后缀为xls，不过影响似乎不大
//        header('Content-Disposition: attachment;filename="' . $savefile . '.xlsx"');
//        header('Cache-Control: max-age=0');
//        // 用户下载excel
//        $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
//        $objWriter->save('php://output');
//        // 保存excel在服务器上
//        //$objWriter = new PHPExcel_Writer_Excel2007($excel);
//        //或者$objWriter = new PHPExcel_Writer_Excel5($excel);
//        //$objWriter->save("保存的文件地址/".$savefile);
//    }

    /**
     *导出订单快递excel数据
     */
    public function dotranZop(){
        $order_dsr = M("order_drs");
        $orders = M("orders");
        $get = I("get.");
        #
        /*if($get['start']||$get["ends"]){
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
        }*/

        $where["status"]    =$get['status']==''? 1:$get['status'];
        $where["have_pay"]  =1;
        $where["sure"]      = 2;

        $start =strtotime('-7 hours',strtotime(date('Y-m-d')));
        $end =strtotime('+17 hours',strtotime(date('Y-m-d')));
//        $where['times'] =array('between',$start,$end);
//        $title=array('运单编号','寄件人','寄件电话','寄件省份','寄件城市','寄件区县','寄件详细地址','收件人','收件电话','收件省份','收件城市','收件区县','收件详细地址','录单备注');
        $title=array('运单编号','代收金额','收件人姓名','收件人手机','收件人电话','收件人地址','收件人单位','品名','数量','买家备注','卖家备注');
//        $cellName=array('trsn','send','send_phone','send_provice','send_city','send_country','send_address','get','get_phone','get_provice','get_city','get_country','get_address','buy_goods');
        $cellName=array('trsn','ooo','get','get_phone','get_phone','get_provice','ooo','buy_goods','nums','buy_remake','ooo');
        $data =$order_dsr->where($where)->select();
        $datas=array();
        foreach ($data as $k=>$v){
            $datas[$k]['trsn'] =(string)$v['trsn'];
            $datas[$k]['send']='熊先生';
            $datas[$k]['send_phone']='15102806891';
            $datas[$k]['send_provice']='四川省,成都市,双流区,双华路三段458号';
//            $datas[$k]['send_city']='成都市';
//            $datas[$k]['send_country'] ='双流区';
//            $datas[$k]['send_address'] ='双华路三段458号';
            $datas[$k]['get'] =$v['name'];
            $datas[$k]['get_phone'] =$v['phone'];
            /*$address =explode(',',$v['address']);
            $getplace =strstr(end($address),'县')==false?strstr(end($address),'区',true).'区':strstr(end($address),'县',true).'县';
            $getaddress =strstr(end($address),'县')==false?strstr(end($address),'区'):strstr(end($address),'县');
            if(count($address)<3){
                $address[1]=$address[0];
            }
            $datas[$k]['get_provice'] =$address[0];
            $datas[$k]['get_city'] =$address[1];
            $datas[$k]['get_country'] =$getplace;
            $datas[$k]['get_address'] =mb_substr($getaddress,1);*/

            $datas[$k]['get_provice'] =$v['address'];
            $datas[$k]['ooo'] =' ';
            $datas[$k]['nums'] =$v['nums'];
            $datas[$k]['buy_remake'] ='remakes';


            /*$wheres['uid'] =$v['uid'];
            $wheres['addr'] =$v['addr'];
            $wheres['status'] =1;
            $wheres['gid'] =array('neq',8);
            $wheres['times'] =array('between',$start,$end);
            $sameplace=$orders->where($wheres)->select();*/
            //获取同地区的数据
            $sameplace=getsameAddress($v['uid'],$v['addr']);
            $other='';
            $other .='168太空素食--'.$v['nums'].'盒';
            if ($sameplace){
                $arr_sn=array();
                $num=0;
                $num2=0;
                foreach ($sameplace as $key =>$val){
                    if($val['gid']==14){
                        $num +=$val['gnum'];
                        $buything ='摇摇杯';
                    }elseif($val['gid']==15){
                        $num2 += $val['gnum'];
                        $buything2 ='手提袋';
                    }
                }
            $other .='/'.$buything.'--'.$num.'个/'.$buything2.'--'.$num2.'个';
            }

            $datas[$k]['buy_goods'] =$other;
        }
        $this->exportTranExcel('发货数据',$title,$cellName,$datas);
    }



    /** 导出的表单属于订单的excel
     * @param $name
     * @param $title
     * @param $cellName
     * @param array $data
     * @throws \PHPExcel_Exception
     * @throws \PHPExcel_Reader_Exception
     * @throws \PHPExcel_Writer_Exception
     */
    private function exportTranExcel($name, $title, $cellName, $data)
    {
        //引入核心文件

        //var_dump($data);die;
        $objPHPExcel = new \PHPExcel();
        //定义配置
        $topNumber =1;//表头有几行占用
        $xlsTitle = iconv('utf-8', 'gb2312', $name);//文件名称
        $fileName = $name.date('_YmdHis');//文件名称
        $cellKey = array(
            'A','B','C','D','E','F','G','H','I','J','K','L','M',
            'N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
            'AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM',
            'AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ'
        );

        //写在处理的前面（了解表格基本知识，已测试）
//     $objPHPExcel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(20);//所有单元格（行）默认高度
//     $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);//所有单元格（列）默认宽度
//     $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(30);//设置行高度
//     $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);//设置列宽度
//     $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(18);//设置文字大小
//     $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);//设置是否加粗
//     $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->getColor()->setARGB(PHPExcel_Style_Color::COLOR_WHITE);// 设置文字颜色
//     $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);//设置文字居左（HORIZONTAL_LEFT，默认值）中（HORIZONTAL_CENTER）右（HORIZONTAL_RIGHT）
//     $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);//垂直居中
//     $objPHPExcel->getActiveSheet()->getStyle('A1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);//设置填充颜色
//     $objPHPExcel->getActiveSheet()->getStyle('A1')->getFill()->getStartColor()->setARGB('FF7F24');//设置填充颜色

        //处理表头标题
      /*  $objPHPExcel->getActiveSheet()->mergeCells('A1:'.$cellKey[count($cellName)-1].'1');//合并单元格（如果要拆分单元格是需要先合并再拆分的，否则程序会报错）
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1','订单信息');
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getFill()->getStartColor()->setARGB('CCCCFF');//设置填充颜色
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(18);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
*/

        // set table header content
        /*$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A2',  $title[0])
            ->setCellValue('B2',  $title[1])
            ->setCellValue('C2',  $title[2])
            ->setCellValue('D2',  $title[3])
            ->setCellValue('E2',  $title[4])
            ->setCellValue('F2',  $title[5])
            ->setCellValue('G2',  $title[6])
            ->setCellValue('H2',  $title[7]);*/
        //处理表头
        foreach ($title as $k=>$v)
        {/*
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellKey[$k].$topNumber, $v[1]);//设置表头数据
            $objPHPExcel->getActiveSheet()->freezePane($cellKey[$k].($topNumber+1));//冻结窗口*/
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellKey[$k].$topNumber, $v);//设置表头数据


//            $objPHPExcel->getActiveSheet()->getStyle($cellKey[$k].$topNumber)->getFill()->getStartColor()->setARGB('CCCCFF');//设置填充颜色
            $objPHPExcel->getActiveSheet()->getStyle($cellKey[$k].$topNumber)->getFont()->setBold(true);//设置是否加粗
            $objPHPExcel->getActiveSheet()->getStyle($cellKey[$k].$topNumber)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);//垂直居中
            $objPHPExcel->getActiveSheet()->getColumnDimension($cellKey[$k])->setWidth(20);//设置列宽度


        }
        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->getActiveSheet()->freezePane('A2');

        //处理数据
        foreach ($data as $k=>$v)
        {
            foreach ($cellName as $k1=>$v1)
            {
                $objPHPExcel->getActiveSheet()->setCellValue($cellKey[$k1].($k+1+$topNumber),' '. $v[$v1]);
                if($v['end'] > 0)
                {
                    if($v1[2] == 1)//这里表示合并单元格
                    {
                        $objPHPExcel->getActiveSheet()->mergeCells($cellKey[$k1].$v['start'].':'.$cellKey[$k1].$v['end']);
                        $objPHPExcel->getActiveSheet()->getStyle($cellKey[$k1].$v['start'])->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    }
                }
                if($v1[4] != "" && in_array($v1[4], array("LEFT","CENTER","RIGHT")))
                {
                    $v1[4] = eval('return PHPExcel_Style_Alignment::HORIZONTAL_'.$v1[4].';');
                    //这里也可以直接传常量定义的值，即left,center,right；小写的strtolower
                    $objPHPExcel->getActiveSheet()->getStyle($cellKey[$k1].($k+1+$topNumber))->getAlignment()->setHorizontal($v1[4]);
                }
            }
        }


        //导出execl
        ob_end_clean();//清除缓存
        header('pragma:public');
        header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$xlsTitle.'.xls"');
        header("Content-Disposition:attachment;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

    /**
     *导出大数据 导出提货发货
     */
    public function cart_Excel()
    {
        $account = M("account");
        $id=I('out');
        $uid =  $account->where(['sysid'=>$id])->find();
        // var_dump($uid['id']);exit;
        $categoryModel=new GoodsModel();
        $rows = $categoryModel->getList('0',$uid['id']);
        set_time_limit(0);
        ini_set('memory_limit', '1024M');
        $where=array();
        $columns = [
            '用户名','转货单号','被转入人','被转入人昵称','转入总盒数','转入时间'
            //需要几列，定义好列名
        ];        //设置好告诉浏览器要下载excel文件的headers
        header('Content-Description: File Transfer');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="转货导出数据-'.time().'.csv"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        $fp = fopen('php://output', 'a');//打开output流
        mb_convert_variables('GBK', 'UTF-8', $columns);
        fputcsv($fp, $columns);//将数据格式化为CSV格式并写入到output流中
        //获取总数，分页循环处理

        foreach($rows as $k => $v) {
            $datas['name'] =getUserInf($v['uid'],'name');
            $datas['sn'] =$v['sn'];
            $datas['aboutname'] =getUserInf($v['aboutid'],'name');
            $datas['uname'] =getUserInf($v['aboutid'],'nickname');
            $datas['nums'] =$v['nums'];
            $datas['time'] =date('Y-m-d H:i:s',$v['time']);
            mb_convert_variables('GBK', 'UTF-8', $datas);
            fputcsv($fp, $datas);
        }
        unset($db_data);
        ob_flush();
        flush();
        fclose($fp);
        //添加记录
        // $user_info =session("admin_info");
        //  addExcel_list($user_info['ch_name'],'数据导出');
        exit();
    }

    /**
     *导出大数据 全部用户资料
     */
     public function kqfx()
    {


        $acc_recode =M('acc_record');
        $account  =M('account');
        $where['dotype'] = '用户升级钻石雨';
        $info=$acc_recode->where($where)->join('account  ON account.id=acc_record.uid')->join('acc_addr  ON account.id=acc_addr.uid')->select();
        $date= uniquArr($info);
        set_time_limit(0);
        ini_set('memory_limit', '1024M');
        #
        /*$account =M('account');
        $date=$account->where(['level'=>5])->select();

        foreach ($date as $k=>$v){
            $where["recid"] =$v['sysid'];
            //$where["level"] =5;
            $date[$k]['count']=$account->where($where)->count();
        }
        //var_dump($date);exit;*/
        
        //array_multisort(array_column($date,'count'),SORT_DESC,$date);
       
        $columns = [
            '序号',
            '姓名',
            '昵称',
            '地址'

        ];        //设置好告诉浏览器要下载excel文件的headers
        header('Content-Description: File Transfer');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="导出数据-'.date('Y-m-d', time()).'.csv"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        $fp = fopen('php://output', 'a');//打开output流
        mb_convert_variables('GBK', 'UTF-8', $columns);
        fputcsv($fp, $columns);//将数据格式化为CSV格式并写入到output流中
        //添加查询条件，获取需要的数据
        $account =M('account');
        //获取总数，分页循环处理
        $accessNum = count($date);
        $perSize = 1000;
        $pages   = ceil($accessNum / $perSize);
        for($i = 1; $i <= $pages; $i++) {
            foreach($date as $k => $v) {
                $datas['id'] =$k+1;
                $datas['name'] =$v['name'];
                $datas['nickname'] =$v['nickname'];
                    $addr=explode(",",$v['street']);
                 $datas['cart'] =$addr['0'].','.$addr['1'];
                //需要格式转换，否则会乱码
                mb_convert_variables('GBK', 'UTF-8', $datas);
                fputcsv($fp, $datas);
            }            //释放变量的内存
            unset($db_data);            //刷新输出缓冲到浏览器
            ob_flush();            //必须同时使用 ob_flush() 和flush() 函数来刷新输出缓冲。
            flush();
        }
        fclose($fp);
        //添加记录
        exit();
    }
    /**
     *导出减肥营数据
     */

    public function doExcel_weight(){
        $weight = M("weight");
        $list = $weight->select();
        $arr=[];
        $count=[];
        foreach ($list as $k=>$v){
            $arr[$k]=json_decode($v['count']);
            $arr[$k]->times=date('Y-m-d H:i:s',$v['time']);
        }
        foreach ($arr as $k=>$v){
            $count[$k]['id']=$k+1;
            $count[$k]['name']=$v->name;
            $count[$k]['phone']=$v->phone;
            $count[$k]['contact']=$v->contact;
            $count[$k]['age']=$v->age;
            $count[$k]['sex']=$v->sex;
            $count[$k]['height']=$v->height;
            $count[$k]['weight']=$v->weight;
            $count[$k]['breakfast']=$v->breakfast;
            $count[$k]['stomach']=is_arr_objk($v->stomach);
            $count[$k]['problem']=is_arr_objk($v->problem);
            $count[$k]['habit']=is_arr_objk($v->habit);
            $count[$k]['taste']=is_arr_objk($v->taste);
            $count[$k]['job']=$v->job;
            $count[$k]['time']=$v->time;
            $count[$k]['diet']=$v->diet;
            $count[$k]['sport']=$v->sport;
            $count[$k]['way']=is_arr_objk($v->way);
            $count[$k]['effectLose']=is_arr_objk($v->effectLose);
            $count[$k]['loseNum']=$v->loseNum;
            $count[$k]['howlong']=$v->howlong;
            $count[$k]['times']=$v->times;
        }
        $data =array_merge($count);
        //  var_dump($data);exit;
        $title=array('序号','姓名','电话','联系时间','年龄','性别','身高','体重','职业','早餐时间','胃肠道健康异常','身体问题','饮食习惯','口味偏好','饮食偏好','晚餐时间','运动习惯','减脂方式','相关疾病','想减多少斤','多久实现','填写时间');
        $cellName=array('id','name','phone','contact','age','sex','height','weight','job','breakfast','stomach','problem','habit','taste','diet','time','sport','way','effectLose','loseNum','howlong','times');
        //添加记录
        $this->exportTranExcel('减肥营数据导出',$title,$cellName,$data);
    }


}