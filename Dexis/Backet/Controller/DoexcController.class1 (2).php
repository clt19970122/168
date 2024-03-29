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
        $money_draw = M("money_draw");
        $get=I('get.');
        #
        $where["status"] = $type != 10 ? array("neq", 0) : 0;
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
            }
            if ($ends == $start){
                $where["times"] = array(array("gt", $start), array("lt", $ends+86400));
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

            $datas[$k]['uname'] =$parent_info['nickname'];
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
        $this->exportOrderExcel('提现数据',$title,$cellName,$datas);
//        $this->exportExcel($datas,date(Y-m-d).'导出数据',$cellName,'sheetname');
    }


    /**
     *0元计划导出
     */
    public function doExcel_plans(){
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
                $where["times"] = array(array("gt", $start), array("lt", $ends+86400));
            }
            if ($ends == $start){
                $where["times"] = array(array("gt", $start), array("lt", $ends+86400));
            }
        }
         $get['status']==''? null: $where["status"] =$get['status'];
        $get["sn"] != "" ? $where["sn"] = array("like", "%" . $get["sn"] . "%") : null;
        $get["name"] != "" ? $where["name"] = array("like", "%" . $get["name"] . "%") : null;
        $get["phone"] != "" ? $where["phone"] = array("like", "%" . $get["phone"] . "%") : null;
        $get["status"] != "" ? $where["status"] = $get["status"] : null;
        $title=array('推荐用户','下单用户','订单编号','姓名','身份证','手机','金额','打款状态','申请时间');
        $cellName=array('low_user','nickname','sn','name','idcard','phone','money','status','time');
        $data =$order_sr->where($where)->select();
        $datas=array();
        foreach ($data as $k=>$v){
            //$user_info =$account->where(['id'=>$v['usid']])->find();
            //$user_info['nickname'];
            $datas[$k]['low_user'] =getRecUser($v['uid']);
            $datas[$k]['nickname'] =getUserInf($v['uid'],'nickname');
            $datas[$k]['sn']=(string)$v['sn'];
            $datas[$k]['name'] =$v['name'];
            $datas[$k]['money'] =$v['money'];
            $datas[$k]['idcard'] =(string)($v['idcard']);
            $datas[$k]['phone'] =(string)$v['phone'];
//            $datas[$k]['time'] =date('Y-m-d H:i:s',$v['times']);
            $datas[$k]['time'] =date('Y-m-d H:i:s',$v['times']);
            $datas[$k]['status'] =getplansStatus($v['status']);
        }
        $this->exportOrderExcel('0元计划数据',$title,$cellName,$datas);
    }


    /**
     *提货数据导出
     */
    public function doExcel_ordersr(){
        $order_dsr = M("order_drs");
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
                $where["times"] = array(array("gt", $start), array("lt", $ends+86400));
            }
            if ($ends == $start){
                $where["times"] = array(array("gt", $start), array("lt", $ends+86400));
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
        $title=array('申请人昵称','申请时间','申请提货盒数（盒）','手机','收货人','收货地址','发货状态','物流公司','物流单号','发货时间');
        $cellName=array('nickname','time','nums','phone','name','address','status');
        $data =$order_dsr->where($where)->select();
        $datas=array();
        foreach ($data as $k=>$v){
            $datas[$k]['nickname'] =getUserInf($v['uid'],'nickname');
            $datas[$k]['nums']=(string)$v['nums'];
            $datas[$k]['name'] =$v['name'];
            $datas[$k]['address'] =$v['address'];
            $datas[$k]['phone'] =(string)$v['phone'];
            $datas[$k]['time'] =date('Y-m-d H:i:s',$v['times']);
            $datas[$k]['status'] =$v['status']==0?'审核中':($v['status']==1?'已通过':($v['status']==2?'未通过':($v['status']==3?'已发货':($v['status']==4?'自提':'取消支付'))));
        }
        $this->exportOrderExcel('提货数据',$title,$cellName,$datas);
    }
    /**
     *用户数据导出
     */
    public function doExcel_user(){
        $account = M("account");
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
        $title=array('id','上级昵称','上级等级','昵称','真实姓名','电话','会员等级','	库存','积分','账户余额','加入时间');
        $cellName=array('id','upname','uplevel','nickname','name','phone','level','stock','point','money','time');
        $data =$account->where($where)->order('totalpoints desc')->select();
        $datas=array();
        foreach ($data as $k=>$v){
            $datas[$k]['id'] =$v['id'];
            $parent_info =$account->where(array('sysid'=>$v['recid']))->find();
            $datas[$k]['upname'] =$parent_info['nickname'];
            $datas[$k]['uplevel'] =getUserLevel($parent_info['level']);
            $datas[$k]['nickname'] =getUserInf($v['id'],'nickname');
            $datas[$k]['name'] =(string)$v['name'];
            $datas[$k]['phone'] =(string)$v['phone'];
            $datas[$k]['money'] =(string)$v['money'];
            $datas[$k]['stock']=(string)$v['stock'];
            $datas[$k]['point']=(string)$v['totalpoints'];
            $datas[$k]['level'] =getUserLevel($v['level']);
            $datas[$k]['time'] =date('Y-m-d H:i:s',$v['times']);
        }
        $this->exportOrderExcel('用户数据',$title,$cellName,$datas);
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
            }if(!$start){
                $where["paytime"] = array("lt", $ends);
            }
            if ($ends > $start) {
                $where["paytime"] = array(array("gt", $start), array("lt", $ends));
            }
            if ($ends == $start){
                $where["paytime"] = array(array("gt", $start), array("lt", $ends+86400));
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
        $title=array('订单号','商品名','上级昵称','下单昵称','手机','会员等级','购买数量','金额','状态','支付时间','支付方式','购买时等级');
        $cellName=array('sn','goods','upname','nickname','phone','level','nums','money','status','time','payway','buy_level');
        $data =$account->where($where)->order('id desc')->select();
        $datas=array();
        foreach ($data as $k=>$v){
            $datas[$k]['sn'] =$v['sn'];
            $datas[$k]['goods'] =$v['gname'];
            $datas[$k]['upname'] =getRecUser($v['uid']);
            $datas[$k]['nickname'] =getUserInf($v['uid'],'nickname');
            $datas[$k]['phone'] =getUserInf($v['uid'],'phone');
            $datas[$k]['level']=getUserLevel(getUserInf($v['uid'],'level'));
            $datas[$k]['buy_level']=getUserLevel($v['buy_level']);
            $datas[$k]['nums']=$v['gnums'];
            $datas[$k]['money']=$v['money'];
            $datas[$k]['status'] =$v['status']==0?'待支付':($v['status']==1?'已支付':($v['status']==3?'待收获':($v['status']==4?'已取消':'')));
            $datas[$k]['time'] =date('Y-m-d H:i:s',$v['paytime']);
            $datas[$k]['payway'] =$v['paytype']==1?'微信':($v['paytype']==4?'余额':'其他');
        }
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
            }if(!$start){
                $where["paytime"] = array("lt", $ends);
            }
            if ($ends > $start) {
                $where["paytime"] = array(array("gt", $start), array("lt", $ends));
            }
            if ($ends == $start){
                $where["paytime"] = array(array("gt", $start), array("lt", $ends+86400));
            }
        }
//        $type != 10 ? $where["status"] = $type : null;
        $get["sn"] != "" ? $where["sn"] = array("like", "%" . $get["sn"] . "%") : null;
        //$get["name"] != "" ? $where["name"] = array("like", "%" . $get["name"] . "%") : null;
        $get["phone"] != "" ? $where["phone"] = array("like", "%" . $get["phone"] . "%") : null;
        $get["status"] != "" ? $where["status"] = $get["status"] : null;
        $get["paytype"] != "" ? $where["paytype"] = $get["paytype"] : null;
        $get["gid"] != "" ? $where["gid"] = $get["gid"] : null;


        $title=array('订单号','上级昵称','下单昵称','手机','会员等级','	商品单价','金额','状态','支付时间','支付方式');
        $cellName=array('sn','upname','nickname','phone','level','single','money','status','time','payway');
        $data =$account->where($where)->order('id desc')->select();
        $datas=array();
        foreach ($data as $k=>$v){
            $datas[$k]['sn'] =$v['sn'];
            $datas[$k]['upname'] =getRecUser($v['uid']);
            $datas[$k]['nickname'] =getUserInf($v['uid'],'nickname');
            $datas[$k]['phone'] =getUserInf($v['uid'],'phone');
            $datas[$k]['level']=getUserLevel(getUserInf($v['uid'],'level'));
            $datas[$k]['single']=$v['gprice'];
            $datas[$k]['money']=$v['money'];
            $datas[$k]['status'] =$v['status']==0?'待支付':($v['status']==1?'已支付':($v['status']==3?'待收获':($v['status']==4?'已取消':'')));
            $datas[$k]['time'] =date('Y-m-d H:i:s',$v['paytime']);
            $datas[$k]['payway'] =$v['paytype']==1?'微信':($v['paytype']==4?'余额':'');
        }
        $this->exportOrderExcel('摇摇杯订单信息导出',$title,$cellName,$datas);
    }

    /**
     *物流费用
     */
    public function doExcel_trans_pay(){
        $account = M("order_drs");
        $get = I("get.");
        #时间
        if($get['start']||$get["ends"]){
            $start = strtotime($get["start"]);
            $ends = strtotime($get["ends"]);
            if(!$ends){
                $where["times"] = array("gt", $start);
            }if(!$start){
                $where["times"] = array("lt", $ends);
            }
            if ($ends > $start) {
                $where["times"] = array(array("gt", $start), array("lt", $ends));
            }
            if ($ends == $start){
                $where["times"] = array(array("gt", $start), array("lt", $ends+86400));
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
        $this->exportOrderExcel('运费信息导出',$title,$cellName,$datas);
    }

    /**
     *用户出货导出
     */
    public function doExcel_out(){
        $get =I('get.');
        $account =M('account');
        $acc_nums =M('acc_nums');
        $where['level'] =$get['level']==null?6:$get['level'];
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
            $user_info[$k]['level'] =getUserLevel($v['level']);
            $user_info[$k]['get_time'] =date('Y-m-d',$start).'到'.date('Y-m-d',$end);
        }
        //排序
        array_multisort($arrs['innum'], SORT_DESC, $user_info);
        $title=array('用户昵称','验证名称','等级','进货量','出货量','时间段');
        $cellName=array('nickname','name','level','innums','outnums','get_time');
        $this->exportOrderExcel('用户进货量数据',$title,$cellName,$user_info);
    }



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
        $topNumber = 2;//表头有几行占用
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
        $objPHPExcel->getActiveSheet()->mergeCells('A1:'.$cellKey[count($cellName)-1].'1');//合并单元格（如果要拆分单元格是需要先合并再拆分的，否则程序会报错）
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1','订单信息');
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(18);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);


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


}