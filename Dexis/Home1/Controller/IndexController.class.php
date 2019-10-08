<?php

namespace Home\Controller;

#use Think\Controller;

class IndexController extends CommController {

    private $conf;

    public function _initialize() {
        $domain = strpos($_SERVER['PATH_INFO'], 'schol');
        if($domain==false){
            parent::_initialize();
            layout("layout");
            $get = I("get.");
            #
            $ssid = session("ssid_dexis");
    //        if ($ssid == null && CONTROLLER._NAME != "Login" && ACTION_NAME != "index") {
            if ($ssid == null && CONTROLLER_NAME != "Login") {
                if ($get["redi"] == 1) {
                    return get_op_put(0, "您还未登录");
                }
                $this->redirect("Login/index");
            }
            #
            $sysconfig = M("sysconfig");
            $list = $sysconfig->where("type in ('draw','base')")->select();
            foreach ($list as $k => $v) {
                $this->conf[$v["models"]] = $v["value"];
            }
        }
    }

    /**
     * 商城首页
     */
    public function index($type='') {
        $goods = M("goods");
        $sysads = M("sysads");

        #
        $where["ind"] = 1;
        $where["status"] = 1;
        $list = $goods->where($where)->select();
        foreach ($list as $k => $v) {
            $list[$k]["price"] = doPrice($v["gsn"], $this->user["level"]);
        }
        #
        $volist = array("index_banner", "index_middle");
        foreach ($volist as $k => $v) {
            $cond["models"] = $v;
            $cond["status"] = 1;
            $put[$v] = $sysads->where($cond)->order("id desc")->select();
        }

        #获取到最新的下单用户信息
        $buy_sr=M('order_sr')->where(['status'=>5])->limit(10)->select();
        $this->assign("put", $put);
        $this->assign("buyer", $buy_sr);

        #获取系统公告
        $notice =M('school')->where(['type'=>1])->select();

        $list1 =M('school')->where(['type'=>4])->select();
        foreach ($list1 as $k=>$v){
            $list1[$k]['times']=date('Y-m-d H:i:s',$v['times']);
        }

        //增加团队人数

        $account = M("account");
        $sysid=$this->user['sysid'];
        $team =$account->where(['recid'=>$sysid])->count();
        $team_num =$account->where(['recid'=>$sysid])->sum('teams');
        $all_team =$team +$team_num;
        $account->where(['sysid'=>$sysid])->save(['teams'=>$all_team]);
        #

        $this->assign("list", $list);
        $this->assign("data", $notice);
        $this->assign("list1", $list1);
        $this->assign("config", $this->conf);
        if($type=='buildHtml'){
            $this->buildHtml('index',HTML_PATH_INDEX,'index/index');
        }else{
            $this->display();
        }
    }

    public function build_html(){
        $this->index('buildHtml');
    }

    public function checks() {
        return get_op_put(1, null);
    }

    /**
     * 皇家金库
     */
    public function money() {
        $acc_money = M("acc_money");
        $acc_bank = M("acc_bank");
        $money_draw =M('money_draw');
        $p =I('p');
        #
        $where["uid"] = $this->ssid;
//        $where["models"] = array("neq", "DRAW");
//        $list = poPage($acc_money, $where);
//        $where["models"] = "DRAW";
//        $draw = poPage($acc_money, $where);
        $draw_where['usid']=$this->ssid;
        $draw = poPage($money_draw, $draw_where);
        #
        $bank = $acc_bank->where("uid='" . $this->ssid . "'")->select();
        #
//        $teamMoney = $this->getTeamMoney($this->user['sysid']);
        #库存变化记录
        $numsChange =$this->getNumsChange('',$p);
        //获取销售量

//        $this->user['groups'] =$this->user['groups'] +$teamMoney;
//        $this->assign("list", $list);
        //冻结金额
        $frozen_money =M('frozen_money')->where(['uid'=>$this->ssid,'status'=>0])->sum('frozen_money');
        $frozenMoney=accountFrozen($this->ssid);//用户冻结金额
        $frozen_money=bcadd($frozen_money,$frozenMoney,2);
        $frozen_money =$frozen_money ==null?'0':$frozen_money;
        $this->assign("frozen", $frozen_money);
        $this->assign("nums", $numsChange['data']);
        $this->assign("page", $numsChange['page']);
        $this->assign("draw", $draw);
        $this->assign("bank", clearBankList($bank));
        $this->assign("user", $this->user);
        $this->display();
    }

    /**
     * 提款
     */
    public function money_draw() {
        $account = M("account");
        $post = I("post.");
        #
        $where["id"] = $this->ssid;
        $info = $account->where($where)->find();
        if ($info["money"] < $this->conf["CONF_MDRAW"]) {
            return get_op_put(0, "您的余额低于最低提款额" . $this->conf["CONF_MDRAW"] . "元");
        }

        //不能使用余额支付金额
        $order_sr_where['status'] =array('in','5,7');
        $order_sr_where['uid'] = $this->ssid;
        $order_sr_data =M('order_sr')->where($order_sr_where)->find();
        if($order_sr_data){
            return get_op_put(0, "您存在金融订单未还款，暂不支持余额支付");
        }
        
        if ($info["stock"] < 0) {
            return get_op_put(0, "您的账户库存不足，暂时无法提款，请补充库存后再尝试");
        }


        #
        $sn = microtime(true);
        if (!cgUserMoney($this->ssid, $info["money"], 0, "DRAW", $sn)) {
            return get_op_put(0, "提款失败，网络异常[0XCEIL]");
        }
        #
        if (!$this->money_draw_op($sn, $post["bank"], $info)) {
            return get_op_put(0, "提款失败，网络异常[0XADDL]");
        }
        return get_op_put(1, null);
    }

    /**
     * 提款记录
     * @param type $sn
     * @param type $data
     */
    private function money_draw_op($sn, $bank, $data) {
        $acc_bank = M("acc_bank");
        $money_draw = M("money_draw");
        #
        $where["id"] = $bank;
        $info = $acc_bank->where($where)->find();
        #
        $put["usid"] = $this->ssid;
        $put["money"] = $data["money"];
        $put["sn"] = $sn;
        $put["wechat"] = $this->wecs["openid"];
        $put["name"] = $info["name"];
        $put["bank"] = $info["bank"];
        $put["bankcode"] = $info["card"];
        $put["status"] = 0;
        $put["times"] = time();
        return $money_draw->add($put);
    }

    /**
     * 我的团队
     */
    public function rider() {
        $sys_id = I('sysid');
//        $sys_id = '15b4c14af1075f';
         $account = M("account");
		 $acc_level = M("acc_level");
        #
		$level = $acc_level->order('id desc')->select();
        $sysid =$sys_id?$sys_id:$this->user["sysid"];
        $where["recid"] = $sysid;
        //var_dump($where);
//        $listNum = $account->where($where)->order("id desc")->select();
//        $all_account =$account->select();
//        $res_data =getChilds($html,$sysid,$all_account,0);
//        $count_rid=count($res_data);

       //用户信息
        $userinfo =$account->where(array('sysid'=>$sysid))->find();
        if ($userinfo["headimgurl"] == null) {
            $userinfo["headimgurl"] = __ROOT__ . "/Public/home/imgs/head.png";
        } else {
            $loc = strpos($userinfo["headimgurl"], "ttp:");
            if (!$loc) {
                $userinfo["headimgurl"] = __ROOT__ . "/Public/uploads/head/" . $userinfo["headimgurl"];
            }
        }

        //zhi
      /*  $myteam =$account->where(array('recid'=>$sysid))->select();
        foreach ($myteam as $k=>$v){
            $buydata =getuserProductAndMoneyinfo($v['id']);
//            $info_arr['out_nums'] = $this->user['totalpoints']- $this->user['stock'];
            $myteam[$k]['level']=getUserLevel($v['level']);
            $myteam[$k]['out_nums']=$buydata['out_nums'];
            $myteam[$k]['reback_money']=$buydata['reback_money'];
            $myteam[$k]['salemoney']=$buydata['salemoney'];
//            $res_count=$this->category($alldata,$v['sysid']);
//            $myteam[$k]['count_child']=count($res_count);
        }*/

        #
//		var_dump($userinfo);
        $this->assign("list2", $level);
//        $this->assign("count", $count_rid);
        $this->assign("user", $userinfo);
        // $this->assign("myteam", $myteam);
        $this->display();
    }

    /**
     *获取团队人数的
     */
    public function getTeamNums(){

        /*$sys_id = I('sysid');
//        $sys_id = '15b4c14af1075f';
        $account = M("account");

        $sysid =$sys_id?$sys_id:$this->user["sysid"];
        $user_count =S('user_num_'.$sysid);
        if($user_count ==null) {
            $all_account = $account->select();
            $res_data = getChilds($html, $sysid, $all_account, 0);
            $user_count = count($res_data);
        }
        S('user_num_'.$sysid, $user_count,4*60*60);
        $this->ajaxReturn( $user_count);*/

        // set_time_limit(0);
        $sys_id = I('sysid');
       // $sys_id = '4522';
        $account = M("account");
        $acc_count  = M("acc_contect");
        $sysid =$sys_id?$sys_id:$this->user["id"];   
        $res =$acc_count->where(['ancestor_id'=>$sysid])->count();
        //数据表
        $user_team =$account->where(['id'=>$sysid])->field('teams')->find();
        $res = $user_team['teams']>$res?$user_team['teams']:$res;
           
        $this->ajaxReturn( $res);
    }
    /**
     * 皇家爵位
     */
    public function sorts() {
        $acc_level = M("acc_level");
        $acc_points = M("acc_points");
        #
        $where["id"] = array("gt", $this->user["level"]);
        $level = $acc_level->where($where)->order("months asc")->find();
        #
        $cond["uid"] = $this->ssid;
        $list = $acc_points->where($cond)->order("id desc")->select();
        #
        $this->assign("user", $this->user);
        $this->assign("items", $acc_level->order("months asc")->select());
        $this->assign("level", $level);
        $this->assign("list", $list);
        $this->display();
    }

    /**
     * 皇家功勋
     */
    public function tious() {
       //  set_time_limit(300);
       //  $account = M("account");
       //  #
       // /* $where["recid"] = $this->user["sysid"];
       //  $list = $account->where($where)->order("level desc")->select();
       //  $this->assign("list", $list);
       //  unset($list[0]);
       //  unset($list[1]);
       //  unset($list[2]);
       //  $this->assign("next", $list);*/

       //  $uid =$this->ssid;
       //  $team = new TeamController();

       //  $uid_data  =$account ->where(['id'=>$uid])->find();

       //  $res_money = $team->getTopTeamAward($uid);
       //  $uid_data['team_money'] = $res_money['team_money'];
       //  $uid_data['team_num'] = $res_money['team_num'];
       //  $uid_data['level'] = getUserLevel($uid_data['level']);
       //  $nums = getUserMonthIn($uid_data['id']);
       //  $uid_data['month_in'] = $nums;
       //  $this->assign('data',$uid_data);

       //  $this->display();

        set_time_limit(300);
        $account = M("account");
        $getteam = M("acc_getteam");
        #
       /* $where["recid"] = $this->user["sysid"];
        $list = $account->where($where)->order("level desc")->select();
        $this->assign("list", $list);
        unset($list[0]);
        unset($list[1]);
        unset($list[2]);
        $this->assign("next", $list);*/

        /*$uid =$this->ssid;
        $team = new TeamController();
        $uid_data  =$account ->where(['id'=>$uid])->find();

        $res_money = $team->getTopTeamAward($uid);
        $uid_data['team_money'] = $res_money['team_money'];
        $uid_data['team_num'] = $res_money['team_num'];
        $uid_data['level'] = getUserLevel($uid_data['level']);
        $nums = getUserMonthIn($uid_data['id']);
        $uid_data['month_in'] = $nums;*/
        $uid =$this->user['id'];
        //上月
//        $get_time =  date("Y-m-d H:i:s",mktime(0, 0 , 0,date("m")-1,1,date("Y")));
//        $end_time =  date("Y-m-d H:i:s",mktime(23,59,59,date("m") ,0,date("Y")));
        $last_month =date('Y-m',strtotime('-1 month'));
        $month =date('Y-m');
        //上月的
        $teammoney= $getteam->where(['uid'=>$uid,'month'=>$last_month])->find();
        //本月
        $this_month= $getteam->where(['uid'=>$uid,'month'=>$month])->find();
        if(!$this_month){
            $this_month['money'] ='等待统计中';
        }
        $user_info =$this->user;
        //获取我的联创团队
//        $user_data =getmyTopTeam($uid,6);
//        var_dump($user_data);
        $this->assign('data',$teammoney);
        $this->assign('thismonth',$this_month);
        $this->assign('user',$user_info);
        $this->display();
    }

    /**
     * 皇家功勋
     */
    public function tious_mo() {
        $account = M("account");
        #
        $where["recid"] = $this->user["sysid"];
        $list = $account->where($where)->order("level desc")->select();
        $this->assign("list", $list);
        unset($list[0]);
        unset($list[1]);
        unset($list[2]);
        $this->assign("next", $list);
        $this->display();
    }

    /**
     * 皇家功勋-排序
     */
    public function tious_sr() {
        $account = M("account");
        #
        $where["recid"] = $this->user["sysid"];
        $data = $account->where($where)->order("level desc")->select();
        foreach ($data as $k => $v) {
            $where["recid"] = $v["sysid"];
            $data[$k]["nums"] = $account->where($where)->count();
        }
        #
        usort($data, function($a, $b) {
            return ($a["nums"] > $b["nums"]) ? -1 : 1;
        });
        $this->assign("list", $data);
        unset($data[0]);
        unset($data[1]);
        unset($data[2]);
        $this->assign("next", $data);
        $this->display();
    }

    ////////////////////////////////////////////////////////////////////////////

    /**
     * 0元计划
     */
    public function plans() {
        #layout("laynot");
        $goods = M("goods");
        #
        $where["zplan"] = 1;
        $where["status"] = 1;
        $list = $goods->where($where)->order("id desc")->select();
        $this->assign("list", $list);
        $this->display();
    }

    public function plans_info($sn) {
        layout("laynot");
        #
        $goods = M("goods");
        $where["gsn"] = $sn;
        $list = $goods->where($where)->find();
        $this->assign("list", $list);
        #
        $this->display("plans_" . $sn);
    }

    /**
     * 限时抢购
     */
    public function limit() {
        $goods = M("goods");
        $where["gsn"] = '815264414134609747';  //0元创业
        $where2["gsn"] = '815270623423588968';  //月体验
        $where3["gsn"] = '715270469154287144';  //周体验
        $where4["gsn"] = '315264382127443659';  //单盒
        #
        $today =date('Y/m/d ');
        $starttime =$today.' '. '10:00:00';
        $endtime =$today.' '. '16:00:00';
        $update_time=$today.' '.'00:00:00';
        $nowTime =time();/*date('Y/m/d H:i:s');*/
        //如果处于这个时间段之内,重置抢购数量
        if(strtotime($update_time)<=$nowTime&&$nowTime<strtotime($starttime)){
             $goods->where($where )->save(['nums'=>168 ,'sales'=>0]);
             $goods->where($where2)->save(['nums'=>168 ,'sales'=>0]);
             $goods->where($where3)->save(['nums'=>168 ,'sales'=>0]);
             $goods->where($where4)->save(['nums'=>1680,'sales'=>0]);
        }
//        915205680107839887
        $list = $goods->where($where)->order("id desc")->select();
        $list2 = $goods->where($where2)->order("id desc")->select();
        $list3 = $goods->where($where3)->select();
        $list4 = $goods->where($where4)->select();
        //echo $goods->getLastSql();

        $this->assign("list", $list);
        $this->assign("list2", $list2);
        $this->assign("list3", $list3);
        $this->assign("list4", $list4);
        $this->assign("starttime", $starttime);
        $this->assign("endtime", $endtime);
        $this->display();
    }


    /**
     * 0元计划-申请
     */
    public function plans_op() {
        $acc_idauth = M("acc_idauth");
        $order_sr = M("order_sr");
        $post = I("post.");
        #
        $goods = getIDGoodsInfo($post["ids"]);
        $where["uid"] = $this->ssid;
        $info = $acc_idauth->where($where)->find();
        if ($info == null) {
            return get_op_put(0, "请完善您的实名信息后再次申请",U('Users/idauth'));
        }
        #
        $put["uid"] = $this->ssid;
        $put["sn"] = getSysOrder();
        $put["gsn"] = $goods["gsn"];
        $put["name"] = $info["name"];
        $put["idcard"] = $info["idcard"];
        $put["phone"] = $info["phone"];
        $put["money"] = $goods["price"];
        $put["status"] = 0;
        $put["times"] = time();
        #
        if (!$order_sr->add($put)) {
            return get_op_put(0, "网络异常[0X001]");
        }
        return $this->plans_op_check($put);
    }

    /**
     * 0元计划-核验
     * @param type $put
     * @return type
     */
    private function plans_op_check($put) {
        $params = array();
        $params["params"]["orderNo"] = $put["sn"];
        $params["params"]["name"] = $put["name"];
        $params["params"]["certNo"] = $put["idcard"];
        $params["params"]["phone"] = $put["phone"];
        $params["params"]["prodNo"] = $put["gsn"];
        $params["params"]["amt"] = $put["money"];
        #
        $data = json_encode($params);
        $output = jsonCurl(C("PUM_CONF")["LINKS"], $data);
        $tmp = json_decode($output, true);
        if ($tmp["meta"]["code"] != "0000") {
            return get_op_put(0, $tmp["meta"]["message"]);
        }
        if ($tmp["data"]["status"] != "0") {
            return get_op_put(0, $tmp["data"]["desc"]);
        }
        return get_op_put(1, "success", $tmp["data"]["url"]);
    }
    /**
     * 0元计划-还款
     */
    public function plans_respay() {
        $acc_idauth = M("acc_idauth");
        $order_sr = M("order_sr");
        $post = I("post.");
        #
        $where["uid"] = $this->ssid;
        $info = $acc_idauth->where($where)->find();
        $put["sn"] = $post["ids"];
        $put["name"] = $info["name"];
        $put["idcard"] = $info["idcard"];
        #
        return $this->plans_respay_check($put);
    }

        /**
     * 0元计划-还款核验
     * @param type $put
     * @return type
     */
    private function plans_respay_check($put) {
        $params = array();
        $params["params"]["certNo"] = $put["idcard"];
        $params["params"]["custName"] = $put["name"];
        $params["params"]["orderNo"] = $put["sn"];
        //var_dump($params);die;
        #
        $data = json_encode($params);
        $output = jsonCurl(C("PUM_CONF")["RESPAY"], $data);
        $tmp = json_decode($output, true);
        //var_dump($tmp);die;
        if ($tmp["meta"]["code"] != "0000") {
            return get_op_put(0, $tmp["meta"]["message"]);
        }
        return get_op_put(1, "success", $tmp["data"]["repayUrl"]);
    }
    ////////////////////////////////////////////////////////////////////////////

    /**
     * 皇家学院
     * @param type $id
     */
    public function schol($id = 1) {
        $school = M("school");
        $scho_type = M("scho_type");
        #
        $where["type"] = $id;
        $where["status"] = 1;
        $list = $school->where($where)->order("id desc")->select();
        $banner_list = $scho_type->where(['status'=>0])->select();
        $this->assign("list", $list);
        $this->assign("banner", $banner_list);
        $this->assign("id", $id);
        $this->display();
    }

    /**获取新闻
     * @param int $id
     */
    public function school($id = 4) {
        $school = M("school");
        #
        $where["type"] = $id;
        $where["status"] = 1;
        $list = $school->where($where)->order("id desc")->select();
        foreach ($list as $k=>$v){
            $list[$k]['times']=date('Y-m-d H:i:s',$v['times']);
        }
        $this->ajaxReturn($list);
    }

    /**
     * 皇家学士详情
     */
    public function schol_infos($id) {
        $school = M("school");
        #
        $where["id"] = $id;
        $info = $school->where($where)->find();
        $this->assign("info", $info);
        $this->display();
    }

    /**
     * 皇家学士
     */
    public function learn() {
        $question = M("question");
        $ques_cat = M("ques_cat");
        $get = I("get.");
        #
        $get["type"] != null ? $where["type"] = $get["type"] : null;
        $get["keys"] != null ? $where["title"] = array("like", "%" . $get["keys"] . "%") : null;
        $where["status"] = 1;
        $list = $question->where($where)->order("id desc")->select();
        $this->assign("list", $list);
        #
        $cat = $ques_cat->where("status=1")->select();
        $this->assign("cat", $cat);
        #
        $this->assign("get", $get);
        $this->display();
    }

    /**
     * 皇家学士详情
     */
    public function learn_infos($id) {
        $question = M("question");
        #
        $where["id"] = $id;
        $info = $question->where($where)->find();
        $this->assign("info", $info);
        $this->display();
    }


    /**获取下级的数量 ---太慢 没用
     * @param string $sysid
     * @param int $t
     * @return int
     */
    public function getChlidLevel($sysid='', $t = -1,$temp=0) {
        $t++;
        global $temp;
        global $all_count;
        //$where['recid']=arrray('in',implode(',',$sysid));
        $where['recid']=$sysid;
        $data =M('account')->where($where)->select();
        if (!empty($data)) {
            if(!in_array($data,$temp)){
                $all_count  +=count($data);
                $temp[] = $data;
            }
            foreach ($data as $k=> $v) {
                $this->getChlidLevel($v['sysid'],$t);
            }
        }
        return $all_count;
    }


    /**获取所有下级的下级
     * @param string $sysid
     * @param int $t
     * @return array
     */
    public  function  GetTeamMember($sysid='', $t = -1) {
        $t++;
        global $temp;
        global $all_count;
        $where['recid']=$sysid;
        $data =M('account')->where($where)->field('id,sysid,recid,nickname')->select();
        if (!empty($data)) {
            if(!in_array($data,$temp)){
                $temp[] = $data;
            }
            foreach ($data as $k=> $v) {
                $this->GetTeamMember($v['sysid'],$t);
            }
        }
        return count($temp);
    }
//获取所有有关的用户--返回json数据
    public function getalluser(){
        $ssid='15b6d601f86afa';
        $categories =$this->GetTeamMember($ssid);
        $tree = array();
        //第一步，将分类id作为数组key,并创建children单元
        foreach($categories as $category){
            foreach ($category as $v) {
                $tree[$v['sysid']] = $v;
                $tree[$v['sysid']]['children'] = array();
            }
        }
        foreach ($tree as $k=>$item) {
            if ($item['recid'] != 0 && $item['recid'] !=null) {
                $tree[$item['recid']]['children'][] = &$tree[$k];
            }
        }

      /*$getthree =$tree[$ssid];
        $alldata=M('account')->field('id,recid,sysid,headimgurl')->select();
        $arrr=$this->category($alldata,$ssid,0);
        foreach ($arrr as $k=>$v){
            $arrr[$k][] = getUserProductAndMoneyInfo($v['id']);
        }
        $p =I('page');
        $data =$p*10;
        for($i =$data-10;$i<$data;$i++){
            $arrr1[] =$arrr[$i];
        }
        var_dump($arrr1);die;
        */

        echo json_encode($tree);
    }


    //团队  2018年9月21日18:54:41 add
    public function category($arr,$pid=0,$level=0){
        //定义一个静态变量，存储一个空数组，用静态变量，是因为静态变量不会被销毁，会保存之前保留的值，普通变量在函数结束时，会死亡，生长周期函数开始到函数结束，再次调用重新开始生长
        //保存一个空数组
        static $list=array();
        //通过遍历查找是否属于顶级父类，pid=0为顶级父类，
        foreach($arr as $value){
            //进行判断如果pid=0，那么为顶级父类，放入定义的空数组里
            if($value['recid']==$pid){
                //添加空格进行分层
                $arr['level']=$level;
                $list[]=$value;
                //递归点，调用自身，把顶级父类的主键id作为父类进行再调用循环，空格+1
                $this->category($arr,$value['sysid'],$level+1);
            }
        }
        return $list;
    }
/////////////团队金额////////////
    /**
     * @param $sysid
     * @param int $t
     * @return array
     */
    private function getTeamMoney($sysid, $t = -1){
        $t++;
        global $temp;
        global $all_money;
        $where['recid']=$sysid;
        $data =M('account')->where($where)->field('id,sysid,recid,groups')->select();
        $money =M('account')->where($where)->sum('groups');
        if (!empty($data)) {
            if(!in_array($data,$temp)){
                $temp[] = $data;
                $all_money += $money;
            }
            foreach ($data as $k=> $v) {
                $this->getTeamMoney($v['sysid'],$t);
            }
        }
        return $all_money==null?0:$all_money;
    }

    /**
     *获取数量变化的记录 --库存记录
     */
    public function getNumsChange($ssid='',$p=1){
        $acc_nums =M('acc_nums');
        $account =M('account');
        $uid=$ssid==''?$this->ssid:$ssid;
        $where['uid']= $uid;
        $where['aboutid']= $uid;
        $where['_logic']= 'or';
        $limit=15;
        $res_data =$acc_nums->where($where)->page($p,$limit)->order('id desc')->select();
        foreach ($res_data as $k=>$v){
            if($v['uid']==$where['uid']){
                $res_data[$k]['show_rest'] =$v['uafter'];
                $res_data[$k]['type'] =getnumtype($v['type']);
            }else{
                $res_data[$k]['show_rest'] =$v['after'];
                $res_data[$k]['type'] =getnumtype($v['type'],2);
            }
            $res_data[$k]['time']=date('Y/m/d ',$v['time']);
            $res_data[$k]['nums']=$v['uid']==$where['uid']?''.$v['nums']:'-'.$v['nums'];
            $res_data[$k]['lowuser']=getLowUser($v['sn'],'name');
        }

        $count = $acc_nums->where($where)->count();
        $Page = new \Think\Page($count, $limit);
        $list = $Page->show();
        return ['data'=>$res_data,'page'=>$list];
//       $this->ajaxReturn($res_data);
    }

    public function getinfos(){
        die;
    }
    /**
 *金融跳转
 */
    public function credit_card() {
        $acc_idauth = M("acc_idauth");
        $where["uid"] = $this->ssid;
        $info = $acc_idauth->where($where)->find();
        if ($info == null) {
            return get_op_put(0, "请完善您的实名信息后再次申请",U('Users/idcode',array('type'=>'3')));
        }else{
            $url="http://icard.casc168.com/pay/dis2/?providerId=1904083154&mobile=".$info["phone"]."&realName=".$info['name']."&idCard=".$info["idcard"];
            return get_op_put(1, "success", $url);
        }
    }
}
