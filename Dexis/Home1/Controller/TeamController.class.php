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
namespace Home\Controller;
class TeamController extends CommController
{
    //主要方法

    public function teamTree(){
        $ssid =I('ssid');
        session('ssid',$ssid);
        $this->display('index');
    }

    public function index(){
        $ssid = session('ssid');
//        if(!S('Tree_data'.$ssid)) {
        $acc_level = M("acc_level");
        $account = M('account');
//        $ssid =I('ssid');
//        $ssid ='15be93013ec77d';
        $user = $account->where(['sysid' => $ssid])->field('id,recid,sysid,nickname,name,phone,status,level,times,stock,totalpoints,money')->find();
//        $this->getMyTop($user['id']);
//        exit;
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
//        $account =M('account');
//        $uid =I('uid');
//        $uid =4770;
        //当前获取的管理奖励
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
            $money =getTopTeamMoney_new($v);
            $child_money +=$money['money'];
//          $child_money+=getTopTeamMoney($v);
        }
        $own_get =$getmoney-$child_money;
        return ['team_money'=>$own_get,'team_num'=>$getmoney_arr['team_in']];
    }

    /**我的一级联创
     * @param $uid
     */
//    public function getMyTop($uid){
//
////        if(!S('team'.$uid)) {
//        $account = M('account');
//        $user_data = getmyTopTeam($uid, 6);
//        $user = $account->where(['id' => $uid])->field('id,recid,sysid,nickname,name,phone,status,level,times,stock,totalpoints,money')->find();
//        $arr = $this->Topteam($user_data, $uid);
////        var_dump($arr);
////        $user['list']=$arr;
////        $this->ajaxReturn($user);
//        $res['id'] = $user['id'];
//        $res_money = '0';
//        // 获取联创团队的团队管理奖
//        if ($user['level'] == 6) {
//            $res_money = $this->getTopTeamAward($user['id']);
//        }
//        $user['team_money'] = $res_money['team_money'];
//        $user['team_num'] = $res_money['team_num'];
//        //获取的是用户本月的进货量
//        $nums = getUserMonthIn($user['id']);
//        $user['month_in'] = $nums;
//        $user['level'] = getUserLevel($user['level']);
//        $res['data'] = $user;
//        $res['children'] = $arr;
//        S('team' . $uid, $res,4*60*60);
//        /*}else{
//            $res =S('team' . $uid);
//        }*/
////      var_dump($res);
//        $this->ajaxReturn($res);
//    }


    /**
     *我的联创团队
     */
    public function getMyTopTeam(){
        set_time_limit(300);
//        $uid =I('uid');
        $uid =$this->ssid;
        $user_data = getmyTopTeam($uid, 6);
        if(!S('teamtree'.$uid)) {
            foreach ($user_data as $k=>$v){
                $res_money = $this->getTopTeamAward($v['id']);
                $user_data[$k]['team_money'] = $res_money['team_money'];
                $user_data[$k]['team_num'] = $res_money['team_num'];
                $user_data[$k]['level'] = getUserLevel($v['level']);
                $nums = getUserMonthIn($v['id']);
                $user_data[$k]['month_in'] = $nums;
            }
            S('teamtree' . $uid, $user_data,2*60*60);
        }else{
            $user_data =S('teamtree' . $uid);
        }
        $this->ajaxReturn($user_data);
    }
//////////////////////
    /**
     *获取团队下的人数
     */
    public function teamLevel(){
        $uid =$this->ssid;
        $acc_level = M("acc_level");
        $acc_contect= M("acc_contect");
        $account =M('account');
        $user =$account->where(['id'=>$uid])->field('sysid,nickname,name')->find();
        //算所有用户是否是下
//        $alldata=$account->field('id,recid,sysid,nickname,name,phone,status,level,times,stock,totalpoints,money')->select();
//        $arrr=category($alldata,$ssid,0);
//        $arrr=getChilds($html,$user['sysid'],$alldata,0);
        $where['c.ancestor_id'] =$uid;
        $res_data =$acc_contect->alias('c')
            ->join('Left join account a on a.id=c.rold_id')
            ->where($where)
            ->field('a.level,count(a.id)')
            ->group('a.level')
            ->select();
        foreach ($res_data as $k =>$v){
            $level_data[$v['level']] =$v['count(a.id)'] ;
        }
//        var_dump($level_data);
//        $this->assign("level", $acc_level->select());
//        $this->assign("levelData", $level_data);
        $this->ajaxReturn(['num'=>$level_data,'level'=>$acc_level->select()]);
    }

    /**
     *从我这里出货的统计往前7天
     */
    public function saleOut(){
        $acc_nums=M('acc_nums');
        $today=strtotime(date('Y-m-d',strtotime('-6 days')));

        $uid =$this->ssid;
        $one_day =24*3600;
        for($l=0;$l<7;$l++){
            $start =$today+($one_day*$l);
            $end =$today+($one_day*($l+1))-1;
            $where['time'] =array('between',[$start,$end]);
            $where['uid'] =$uid;
            $this_day =date('m-d',$start);
            $dayarr[]=$this_day;
            $res_data =$acc_nums->where($where)->sum('nums');
            $res_num=$res_data==null?0:$res_data;
            $day[$this_day]=$res_num;
        }
        $this->ajaxReturn(['num'=>$day,'day'=>$dayarr]);
    }
}