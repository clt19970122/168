<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/19
 * Time: 12:17
 */
namespace Backet\Controller;

use Backet\Model\GoodsModel;

vendor("PHPExcel.PHPExcel");
vendor("PHPExcel.PHPExcel.Reader.Excel2007");

class DocsvController extends CommController {
    /**
     *导出大数据 全部用户资料
     */
    public function exportData()
    {
        set_time_limit(0);
        ini_set('memory_limit', '1024M');
        $get = I("get.");
        #
        $where=array();
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
            $where['recid'] = $get["up_nickname"];
        }
        $columns = [
            'id',
//            '上级姓名',
            '昵称',
            '真实姓名',
            '电话',
            '会员等级',
//'库存','进货量','账户余额','销售量','销售额','利润',
            '加入时间'
            //需要几列，定义好列名
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
//        $cellName=array('id','upname','nickname','name','phone','level','stock','point','money','sale_num','salemoney','person','time');
//        $data =$account->order('id desc')->select();
        //获取总数，分页循环处理
        $accessNum = $account->where($where)->count();
        $perSize = 1000;
        $pages   = ceil($accessNum / $perSize);
        for($i = 1; $i <= $pages; $i++) {
            $db_data = $account->where($where)->page($i,$perSize)->select();
            foreach($db_data as $k => $v) {
                $datas['id'] =$v['id'];
//                $parent_info =$account->where(array('sysid'=>$v['recid']))->find();
//                $datas['upname'] =$parent_info['nickname'];
                // $datas[$k]['uplevel'] =getUserLevel($parent_info['level']);
                $datas['nickname'] =getUserInf($v['id'],'nickname');
                $datas['name'] =(string)$v['name'];
                $datas['phone'] =(string)$v['phone'];
//                $datas['money'] =(string)$v['money'];
//                $datas['stock']=(string)$v['stock'];
//                $datas['point']=(string)$v['totalpoints'];
                $datas['level'] =getUserLevel($v['level']);
                $datas['time'] =date('Y-m-d H:i:s',$v['times']);
//                $datas['sale_num'] =$v['totalpoints']-$v['stock'];
//                $datas['person'] =(string)$v['person'];
                //统计销售金额
//                $summary=getUserProductAndMoneyInfo($v['id']);
//                $datas['salemoney'] =$summary['salemoney'];
//                $datas['rebackmoney'] =$summary['reback_money'];
//                $rowData = [];                //获取每列数据，转换处理成需要导出的数据
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
        $user_info =session("admin_info");
        addExcel_list($user_info['ch_name'],'用户数据导出');
        exit();
    }

    /**
     *导出大数据 导出提货发货
     */
    public function export_userIdauth()
    {
        set_time_limit(0);
        ini_set('memory_limit', '1024M');
        $get = I("get.");
        #
        $where=array();
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
        $get["name"] != null ? $where["name"] = array("like", "%" . $get["name"] . "%") : null;

        $columns = [
            '真实姓名','电话','身份证','加入时间'
            //需要几列，定义好列名
        ];        //设置好告诉浏览器要下载excel文件的headers
        header('Content-Description: File Transfer');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="用户认证数据-'.time().'.csv"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        $fp = fopen('php://output', 'a');//打开output流
        mb_convert_variables('GBK', 'UTF-8', $columns);
        fputcsv($fp, $columns);//将数据格式化为CSV格式并写入到output流中
        //添加查询条件，获取需要的数据
        $account =M('acc_idauth');
        //获取总数，分页循环处理
        $accessNum = $account->where($where)->count();
        $perSize = 1000;
        $pages   = ceil($accessNum / $perSize);
        for($i = 1; $i <= $pages; $i++) {
            $db_data = $account->where($where)->page($i,$perSize)->select();
            foreach($db_data as $k => $v) {
                $datas['nickname'] =$v['name'];
                $datas['phone'] =(string)$v['phone'];
                $datas['idcard']='``'.(string)$v['idcard'];
                $datas['time'] =date('Y-m-d H:i:s',$v['times']);
//                $rowData = [];        //获取每列数据，转换处理成需要导出的数据
                //需要格式转换，否则会乱码
                mb_convert_variables('GBK', 'UTF-8', $datas);
                fputcsv($fp, $datas);
            }            //释放变量的内存
            unset($db_data);     //刷新输出缓冲到浏览器
            ob_flush();         //必须同时使用 ob_flush() 和flush() 函数来刷新输出缓冲。
            flush();
        }
        fclose($fp);
        //添加记录
        $user_info =session("admin_info");
        addExcel_list($user_info['ch_name'],'用户信息数据导出');
        exit();
    }

    /**
     *导出大数据 导出0原计划
     */
    public function export_plan()
    {
        set_time_limit(0);
        ini_set('memory_limit', '1024M');

        $get = I("get.");
        #
        $where=array();
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
        $columns = [
//            '姓名','手机号','身份证','金额','状态','时间'
            'id','申请用户','联创','出货人','推荐用户','电话','身份证','会员等级','库存','申请金额','申请时间','状态','逾期时间','逾期多久'
            //需要几列，定义好列名
        ];        //设置好告诉浏览器要下载excel文件的headers
        header('Content-Description: File Transfer');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="导出数据-'.time().'.csv"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        $fp = fopen('php://output', 'a');//打开output流
        mb_convert_variables('GBK', 'UTF-8', $columns);
        fputcsv($fp, $columns);//将数据格式化为CSV格式并写入到output流中
        //添加查询条件，获取需要的数据
        $account =M('order_sr');
//        $cellName=array('id','upname','nickname','name','phone','level','stock','point','money','sale_num','salemoney','person','time');
//        $data =$account->order('id desc')->select();
        //获取总数，分页循环处理
        $accessNum = $account->where($where)->count();
        $perSize = 1000;
        $pages   = ceil($accessNum / $perSize);
        for($i = 1; $i <= $pages; $i++) {
            $db_data = $account->where($where)
                ->page($i,$perSize)->select();

            foreach($db_data as $k => $v) {
                $datas['id'] =$v['id'];
                $user_info =M('account')->where(['id'=>$v['uid']])->find();
                $datas['name'] =$user_info['name'];
                $datas['name'] =(string)$v['name'];
                $top_user=gettop($user_info['sysid'],6);
                $datas['topname']=$top_user['name'].'('.$top_user['phone'].')';
                $parent_info =M('account')->where(array('sysid'=>$user_info['recid']))->find();
                //统计数量出货
                $out_info =M('acc_nums')->where(['sn'=>$v['sn']])->find();
                $out_infos =M('account') ->where(['id'=>$out_info['aboutid']])->find();
                $datas['outname'] =$out_infos['name'].'('.$out_infos['phone'].')';
                $datas['upname'] =$parent_info['name'].'('.$parent_info['phone'].')';
                $datas['phone'] =(string)$user_info['phone'];
                $datas['phone'] =(string)$v['phone'];
                $datas['idcard'] ='`'.(string)$v['idcard'];
                $datas['level'] =getUserLevel($user_info['level']);
                $datas['stock']=(string)$user_info['stock'];
                $datas['money'] =(string)$v['money'];
                $datas['status'] =getplansStatus($v['status']);
                $datas['time'] =date('Y-m-d H:i:s',$v['times']+24 * 3600 * 33);
//                $datas['pass_time'] =date('Y-m-d H:i:s',$v['times']+(3600*24*30));
//                $datas['long'] = round((time()-$v['times'])/3600/24);
//                $rowData = [];//获取每列数据，转换处理成需要导出的数据
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
        $user_info =session("admin_info");
        addExcel_list($user_info['ch_name'],'0元计划数据导出');
        exit();
    }

    /**
     *导出大数据 导出提货发货
     */
    public function export_ordersr()
    {
        set_time_limit(0);
        ini_set('memory_limit', '1024M');
        $get = I("get.");
        #
        $where=array();
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

        $columns = [
            '申请人昵称','申请时间','申请提货盒数（盒）','手机','收货人','收货地址','发货状态'
            //需要几列，定义好列名
        ];        //设置好告诉浏览器要下载excel文件的headers
        header('Content-Description: File Transfer');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="提货导出数据-'.time().'.csv"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        $fp = fopen('php://output', 'a');//打开output流
        mb_convert_variables('GBK', 'UTF-8', $columns);
        fputcsv($fp, $columns);//将数据格式化为CSV格式并写入到output流中
        //添加查询条件，获取需要的数据
        $account =M('order_drs');
        //获取总数，分页循环处理
        $accessNum = $account->where($where)->count();
        $perSize = 1000;
        $pages   = ceil($accessNum / $perSize);
        for($i = 1; $i <= $pages; $i++) {
            $db_data = $account->where($where)->page($i,$perSize)->select();
            foreach($db_data as $k => $v) {
                $datas['nickname'] =getUserInf($v['uid'],'nickname');
                $datas['time'] =date('Y-m-d H:i:s',$v['times']);
                $datas['nums']=(string)$v['nums'];
                $datas['phone'] =(string)$v['phone'];
                $datas['name'] =$v['name'];
                $datas['address'] =$v['address'];
                $datas['status'] =$v['status']==0?'审核中':($v['status']==1?'已通过':($v['status']==2?'未通过':($v['status']==3?'已发货':($v['status']==4?'自提':'取消支付'))));

//                $rowData = [];        //获取每列数据，转换处理成需要导出的数据
                //需要格式转换，否则会乱码
                mb_convert_variables('GBK', 'UTF-8', $datas);
                fputcsv($fp, $datas);
            }            //释放变量的内存
            unset($db_data);     //刷新输出缓冲到浏览器
            ob_flush();         //必须同时使用 ob_flush() 和flush() 函数来刷新输出缓冲。
            flush();
        }
        fclose($fp);
        //添加记录
        $user_info =session("admin_info");
        addExcel_list($user_info['ch_name'],'提货数据导出');
        exit();
    }

    /**
     *导出大数据 导出提货发货
     */
    public function export_fina()
    {
        set_time_limit(0);
        ini_set('memory_limit', '1024M');
        $where=array();
        #
        $get=I('get.');
        #
        if($get['type']!= 11) {
            $where["status"] = $get['type'];
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
            }
            if ($ends == $start){
                $where["times"] = array(array("gt", $start), array("lt", $ends+86400));
            }
        }

        $columns = [
            '用户id','出货人','订单编号','上级用户','上级等级','提款用户','用户等级','金额','持卡人','提款银行','银行卡号','提款时间','打款状态','备注'
            //需要几列，定义好列名
        ];        //设置好告诉浏览器要下载excel文件的headers
        header('Content-Description: File Transfer');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="提现导出数据-'.time().'.csv"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        $fp = fopen('php://output', 'a');//打开output流
        mb_convert_variables('GBK', 'UTF-8', $columns);
        fputcsv($fp, $columns);//将数据格式化为CSV格式并写入到output流中
        //添加查询条件，获取需要的数据
        $account =M('money_draw');
        //获取总数，分页循环处理
        $accessNum = $account->where($where)->count();
        $perSize = 1000;
        $pages   = ceil($accessNum / $perSize);
        for($i = 1; $i <= $pages; $i++) {
            $db_data = $account->where($where)->page($i,$perSize)->select();
            foreach($db_data as $k => $v) {
                $user_level =getUserInf($v['usid'],'level');
                $sysid =getUserInf($v['usid'],'sysid');
                $levels =$user_level==6?6:$user_level+1;
                $up_user=gettop($sysid,$levels);
                $datas['uid']=$v['usid'];
                $datas['outinfo']=$up_user['name'].'('.$up_user['phone'].')';
                $datas['sn']=(string)$v['sn'].'\t';
                $recid=getUserInf($v['usid'],'recid');
                $parent_info =M('account')->where(array('sysid'=>$recid))->find();
                $datas['uname'] =$parent_info['nickname'];
                $datas['ulevel'] =getUserLevel($parent_info['level']);
                $datas['nickname'] =getUserInf($v['usid'],'nickname');
                $datas['level'] =getUserLevel(getUserInf($v['usid'],'level'));
                $datas['money'] =(string)$v['money'];
                $datas['name'] =$v['name'];
                $datas['bank'] =getBankName($v['bank']);
                $datas['bankcode'] =','.(string)$v['bankcode'];
                $datas['time'] =date('Y-m-d H:i:s',$v['times']);
                $datas['status'] =$v['status']==0?'未支付':($v['status']==1?'已支付':'已退回');
                $datas['remake'] =payRemake($v['sn']);

//                $rowData = [];                //获取每列数据，转换处理成需要导出的数据
                //需要格式转换，否则会乱码
                mb_convert_variables('GBK', 'UTF-8', $datas);
                fputcsv($fp, $datas);
            }            //释放变量的内存
            unset($db_data);     //刷新输出缓冲到浏览器
            ob_flush();         //必须同时使用 ob_flush() 和flush() 函数来刷新输出缓冲。
            flush();
        }
        fclose($fp);
        //添加记录
        $user_info =session("admin_info");
        addExcel_list($user_info['ch_name'],'提现数据导出');
        exit();
    }


    /**
     *导出大数据 导出转货
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
            $datas['tname'] =getUserInf($v['aboutid'],'nickname');
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

       public function export_plan2()
    {
        $account = M("acc_idauthb");
        $get = I("get.");
        $msg ='';
        $columns = [
            '姓名','电话','身份证','金额','状态','时间'
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
        $get["phone"] != null ? $where["phone"] = array("like", "%" . $get["phone"] . "%") : null;
        $get["name"] != null ? $where["name"] = array("like", "%" . $get["name"] . "%") : null;

        // $title=array('真实姓名','电话','身份证','加入时间');
        // $cellName=array('name','phone','idcard','time');
        $data =$account->where($where)->select();

        foreach ($data as $k=>$v){
            $datas['name'] =(string)$v['name'];
            $datas['phone'] =(string)$v['phone'];
            $datas['idcard'] ='`'.(string)$v['idcard'];
            $datas['money'] =(string)$v['money'];
            $datas['status'] ='已还款';
            $datas['time'] =date('Y-m-d H:i:s',$v['times']);
            mb_convert_variables('GBK', 'UTF-8', $datas);
            fputcsv($fp, $datas);
        }
//            var_dump($datas);die;
/*        unset($db_data);
        ob_flush();
        flush();
        fclose($fp);*/
        //添加记录
       // $user_info =session("admin_info");
      //  addExcel_list($user_info['ch_name'],'数据导出');
        exit();
    }

}