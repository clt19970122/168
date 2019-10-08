<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/26
 * Time: 16:11
 */
/**
 * PHP递归实现无限级分类并展示
 */
namespace Backet\Controller;
class TeamController extends CommController
{
    //主要方法

    public function teamTree(){
        $ssid =I('ssid');
        session('ssid',$ssid);
        $this->display('index');
    }

    public function index(){
        set_time_limit(200);
        $ssid = session('ssid');
//        if(!S('Tree_data'.$ssid)) {
            $acc_level = M("acc_level");
            $account = M('account');
//        $ssid =I('ssid');
//        $ssid ='15be93013ec77d';
            $user = $account->where(['sysid' => $ssid])->field('id,recid,sysid,nickname,name,phone,status,level,times,stock,totalpoints,money')->find();
            $this->getMyTop($user['id']);
            exit;
            $alldata = $account->field('id,recid,sysid,nickname,name,phone,status,level,times,stock,totalpoints,money')->select();

            $arr = $this->getList($alldata, $ssid);
//        $user['list']=$arr;
//        $this->ajaxReturn($user);
            $res['id'] = $user['id'];
            $res_money = '0';
            // 获取联创团队的团队管理奖
            if ($user['level'] == 6) {
                $res_money = $this->getTopTeamAward($user['id']);
            }
            $user['team_money'] = $res_money['team_money'];
            $user['team_num'] = $res_money['team_num'];
            //获取的是用户本月的进货量
            $nums = getUserMonthIn($user['id']);
            $user['month_in'] = $nums;
            $user['level'] = getUserLevel($user['level']);
            $res['data'] = $user;
            $res['children'] = $arr;
            S('Tree_data'.$ssid, $res,4*60*60);
        /*}else{
            $res =S('Tree_data'.$ssid);
        }*/
        $this->ajaxReturn($res);
//        var_dump($arr);
//        $this->showList($arr);
    }


    /**
     * 组装成树形结构
     * @param $data
     * @param $pid sysid
     * @return array
     */
    private function getList($data, $pid){
        $list = [];
        foreach ($data as $val) {
            //查询上级
            //初始给到下一级
            $val['depth']=1;
            if ($val['recid'] == $pid) {
                $res_money='0';
                if($val['level'] ==6) {
                    $res_money = $this->getTopTeamAward($val['id']);
                    $user['team_money'] = $res_money['team_money'];
                    $user['team_num'] = $res_money['team_num'];
                    $nums = getUserMonthIn($val['id']);
                    $val['month_in'] = $nums;
                    $val['level'] = getUserLevel($val['level']);
                    $val['data'] = $val;
                }
                $val['children'] = $this->getList($data,$val['sysid']);

//                unset($val['level']);
//                unset($val['name']);
//                unset($val['money']);
//                unset($val['nickname']);
//                unset($val['phone']);
//                unset($val['recid']);
//                unset($val['status']);
//                unset($val['stock']);
//                unset($val['sysid']);
//                unset($val['times']);
//                unset($val['totalpoints']);
//                unset($val['depth']);
//                unset($val['team_money']);
                if (empty($val['children'])) {
//                    unset($val['children']);
                }else{
                    foreach($val['children'] as $key=>$value){
                        $val['children'][$key]['depth'] = $this->getdepth($data,$value['recid'],0);
                    }
                }
                $list[] = $val;
            }
        }
//        $res_arr =self::aaa($list);
        return $list;
    }

    /**去除多维数组中的空值 ---  no use
     * @param $arr
     * @return array
     */
    static function aaa($arr){
        if(is_array($arr)){
            foreach($arr as $key => $value){
                if(is_array($value)&&empty($value)){
                    unset($arr[$key]);
                }else{
                    if(is_array($value)){
                        $arr[$key] = self::aaa($value);
                    }
                }
            }
        }
        return $arr;
    }

    /**
     * 计算深度
     * @param $data
     * @param $pid
     * @param $depth
     * @return int
     */
    private function getdepth($data,$pid,$depth){
        foreach($data as $value){
            if(empty($pid)){
                break;
            }
            if($value['sysid'] == $pid){
                $depth+=1;
                $depth = $this->getDepth($data,$value['recid'],$depth);
            }

        }
        return $depth;
    }


    /**展示列表
     * show
     * @param $list
     */
    private function showList($list){
        foreach ($list as $val) {
            if(isset($val['depth'])){
                for ($i = 0; $i < $val['depth']; $i++) {
                    echo "——";
                }
            }
            echo $val['nickname'].'-'.$val['name'].'('. getUserLevel($val['level']).')'. "<br/>";
            if (isset($val['list'])) {
                $this->showList($val['list']);
            }
        }
    }


    /**
     *h联创月度管理奖
     *条件：
     * 一：新联创要4000盒以后的进货记为个人奖励
     */
    public function getTopTeamAward($uid){
        set_time_limit(0);
        //当前获取的管理奖励
//        $getmoney_arr =getTopTeamMoney($uid);
        $getmoney_arr =getTopTeamMoney_new($uid);
        $getmoney=$getmoney_arr['money'];
        #
        //获取用户信息
//        $user_sysid =getUserInf($uid,'sysid');
//        //获取我下面的一级用户
//        $user_data =$account->where(['recid'=>$user_sysid])->select();
        //获取我的团队
        $user_data =getmyTopTeam($uid,6);
        $myNest =array();
        //获取下和有直接关系的联创
        foreach ($user_data as $k=>$v){
            $top_user =gettop($v['recid'], $v['level']);
            if($top_user['id'] ==$uid){
                $myNest[]=$v['id'];
            }
        }
        $child_money=0;
//        foreach($user_data as $k=>$v){
//             getmyTopTeam($v['id']);
//            $child_money+=getTopTeamMoney($v['id']);
//        }
        //获取团队的金额
        foreach($myNest as $k=>$v){
//            $money =getTopTeamMoney($v);
            $money =getTopTeamMoney_new($v);
            $child_money +=$money['money'];
//          $child_money+=getTopTeamMoney($v);
        }
        $own_get =$getmoney-$child_money;
        return ['team_money'=>$own_get,'team_num'=>$getmoney_arr['team_in']];
    }

    public function getMyTop($uid){

        if(!S('team'.$uid)) {
            $account = M('account');
            $user_data = getmyTopTeam($uid, 6);
            $user = $account->where(['id' => $uid])->field('id,recid,sysid,nickname,name,phone,status,level,times,stock,totalpoints,money')->find();
            $arr = $this->Topteam($user_data, $uid);
//        var_dump($arr);
//        $user['list']=$arr;
//        $this->ajaxReturn($user);
            $res['id'] = $user['id'];
            $res_money = '0';
            // 获取联创团队的团队管理奖
            if ($user['level'] == 6) {
                $res_money = $this->getTopTeamAward($user['id']);
            }
            $user['team_money'] = $res_money['team_money'];
            $user['team_num'] = $res_money['team_num'];
            //获取的是用户本月的进货量
            $nums = getUserMonthIn($user['id']);
            $user['month_in'] = $nums;
            $user['level'] = getUserLevel($user['level']);
            $res['data'] = $user;
            $res['children'] = $arr;
            S('team' . $uid, $res,4*60*60);
        }else{
            $res =S('team' . $uid);
        }
//      var_dump($res);
        $this->ajaxReturn($res);
    }

    /**
     * @param $data @全部联创的数据
     * @param $pid @上级id
     * @return array
     */
    private function Topteam($data,$pid){
        $list = [];
        foreach ($data as $k=>$val) {
            //查询上级
            //初始给到下一级
            $val['depth']=1;
            //获取上级联创
            $user_data = gettop($val['recid'], $val['level']);
//            var_dump($user_data);
//            var_dump($user_data['id']);
//            var_dump($pid.'----111');
            if ($user_data['id'] == $pid) {
//                var_dump($val['id'].'----v');
                $res_money = $this->getTopTeamAward($val['id']);
                $val['team_money'] = $res_money['team_money'];
                $val['team_num'] = $res_money['team_num'];
                $nums = getUserMonthIn($val['id']);
                $val['month_in'] = $nums;
                $val['level'] = getUserLevel($val['level']);
                $val['data'] = $val;
                $val['children'] = $this->Topteam($data,$val['id']);

            }else{
                unset($val);
            }

              /*  unset($val['dep']);
                unset($val['month_in']);
                unset($val['parent_id']);
                unset($val['parent_name']);
                unset($val['parent_level']);
                unset($val['level']);
                unset($val['name']);
                unset($val['money']);
                unset($val['nickname']);
                unset($val['phone']);
                unset($val['recid']);
                unset($val['status']);
                unset($val['stock']);
                unset($val['sysid']);
                unset($val['times']);
                unset($val['totalpoints']);
                unset($val['depth']);
                unset($val['team_money']);*/

            if (empty($val['children'])) {
//                    unset($val['children']);
            }else{
                foreach($val['children'] as $key=>$value){
                    $val['children'][$key]['depth'] = $this->getdepth($data,$value['recid'],0);
                }
            }
                $list[] = $val;
            }
            $tes =$this->array_remove_empty($list);
//        $res_arr =self::aaa($list);
        return $tes;
    }
    //移除空值
    public function array_remove_empty($arr)
    {
        $narr  = array();
        while(list($key, $val) = each($arr)){
            if (is_array($val)){
                $val  = $this->array_remove_empty($val);
//                 count($val)<=0||$narr[$key] = $val;
                 $narr[$key] = $val;
            }else {
                if (trim($val) != ""){
                    $narr[$key]  = $val;
                }
            }
        }
        unset($arr);
        return $narr;
    }


    /**
     *展示所有联创的月入价值
     */
    public function showAllUnite(){
        if(IS_POST) {
            $account = M('account');
            $top_where['level'] = 6;
            $top_where['status'] = 1;
//        $top_where['id'] =108;
            $user_data = $account->where($top_where)->select();
            foreach ($user_data as $k => $v) {
                $res_arr = $this->getTopTeamAward($v['id']);
                $user_data[$k]['team_money'] = $res_arr['team_money'];
                $user_data[$k]['team_in'] = $res_arr['team_num'];
            }
        }else{
            $this->display('list');
        }
    }

    /**
     *添加获得团队管理奖的记录
     */
    public function addTeam_getMoney(){
        set_time_limit(0);
        $account = M('account');
        $acc_getTeam = M('acc_getteam');
        $length =I('length')==null?0:I('length');
        $top_user =S('top_users');
        if(!$top_user) {
            unset($_SESSION['sesstopid']);
            $top = $account->where(['level' => 6, 'status' => 1])->order('times desc')->select();
            $top_user =array_column($top, 'id');
            S('top_users', $top_user, 4 * 3600);
        }
        if($length <count($top_user)){
            $id =$top_user[$length];
            //获取团队金额
//            $res_info =$this->getTopTeamAward($id);
            $res_info =$this->GetTeamNum($id);
            $month =date('Y-m',time());
            //添加数据
            $add_data =[
                'uid'=>$id,
                'money'=>$res_info['money'],
                'time'=>time(),
                'month'=>$month,
                'team_num'=>$res_info['team_num'],
                'myin'=>$res_info['myin'],
                'team_money'=>$res_info['team_money'],
            ];
            $have_data =$acc_getTeam->where(['uid'=>$id,'month'=>$month])->find();
            if($have_data){
                $res =$acc_getTeam->where(['uid'=>$id,'month'=>$month])->save($add_data);
            }else{
                $res =$acc_getTeam->add($add_data);
            }
            $length++;
            $this->ajaxReturn(['status' => 1, 'length' => $length]);
        }
        S('top_users',null);
        $this->ajaxReturn(['status' => 0]);
    }

    /** 获取团队下的数量
     * @param $uid
     */
    public function GetTeamNum($uid){
        $acc_getTeam = M('acc_getteam');
        $account = M('account');
        $acc_ratio = M('acc_teammoney');
//       本月
        $get_time = mktime(0, 0, 0, date('m'), 1, date('Y'));
        $end_time = mktime(23, 59, 59, date('m'), date('t'), date('Y'));
        //获取已经获取过的账户id
        $where['time'] =array('between',[$get_time,$end_time]);
        //获取我本人本月的进货量 -and- 销售额
        $res_arr =getTeamAllMoney_new($uid,$where);
        //再获取团队的进货量
        $haveGetUser =$_SESSION['sesstopid'];
        $my_team =0;
        $my_money =0;
//        $teamdate['money']=0;
        $have_pay_money =0;
        //循环获取到的团队下面的用户数据
        foreach ($haveGetUser as $k=>$v){
            $userinfo =$account->where(['id'=>$v])->find();
            $where['uid']=$v;
            //获取上任推荐联创
            $istop =gettop($userinfo['recid'],6);
            //如果上级联创是我 才加入
            if($istop['id']==$uid){
                //是否是团队记录
                $teamdate=$acc_getTeam->where($where)->find();
                if($teamdate){
                    //返回超量部分 --算入到本人的业绩里
                    $over_num =getEnoughtPoint($v,$teamdate['myin']);
                    //团队的+ 个人的 -超量部分=团队量
                    $my_team +=$teamdate['team_num'] +$teamdate['myin']-$over_num;
                    //个人-超量 =没有进团队的金额
                    $team_moneys =$over_num<$teamdate['myin']?($teamdate['myin']-$over_num)*55:0;
                    //这个人所有的团队金额
                    $my_money +=$teamdate['team_money'] +$team_moneys;
//                    $have_pay_money +=$teamdate['money'];
                    $where_ratio['inmoney']=array('elt',$teamdate['team_money']);
                    //获取返利的表
                    $getratio =$acc_ratio->where($where_ratio)->order('id desc')->find();
                    if($getratio){
                        $have_pay_money +=$teamdate['team_money']*$getratio['ratio'];
                    }
                }
                $_SESSION['sesstopid'][$k] =null;
            }
        }
        $_SESSION['sesstopid'][] = $uid;
        //我的所有进货量 + 我的团队金额=团队数量
        //我超量的部分
        $my_over_num =getEnoughtPoint($uid,$res_arr['all_nums']);
        $myall_team_num =$my_over_num+$my_team;
        //获取到的团队金额
        $myall_team_money =($my_over_num*55)+$my_money;
        $where_ratio['inmoney']=array('elt',$myall_team_money);
        //获取返利的表
        $getratio =$acc_ratio->where($where_ratio)->order('id desc')->find();
        $rest_money=0;
        if($getratio){
            //获取到的金额
            $getmoney =$myall_team_money*$getratio['ratio'];
            $rest_money =$getmoney -$have_pay_money<0?0:$getmoney -$have_pay_money;
        }
        //返回  获取的金额 - 团队进货量  - 个人进货量 - 团队销售额
        return ['money'=>$rest_money,'team_num'=>$myall_team_num,'myin'=>$res_arr['all_nums'],'team_money'=>$myall_team_money];
    }

//跳转进入页面
    public function show_list(){
        $acc_getteam =M('acc_getteam');
        $month =I('month');
        $where['month'] =$month;

        $res_data =$acc_getteam->where($where)->select();
        foreach ($res_data as $k=>$v){
            $res_data[$k]['add_time'] =date('Y-m-d H:i:s',$v['time']);
            $res_data[$k]['nickname'] =getUserInf($v['uid'],'nickname');
            $res_data[$k]['name'] =getUserInf($v['uid'],'name');
        }
        for($i= -2;$i<3;$i++){
            $time =strtotime("".$i." month");
            $month_arr[] =date('Y-m',$time);
        }
        $this->assign('list',$res_data);
        $this->assign('month',$month_arr);
        $this->assign('get_month',$month);
        $this->display();
    }

}