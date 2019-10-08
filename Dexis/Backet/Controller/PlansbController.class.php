<?php

/**
 * 0元计划
 */

namespace Backet\Controller;
use Backet\Model\GoodsModel;
use Think\Session\Driver\Db;

class PlansbController extends CommController {

    /**
     * 
     */

    public function index() {
        $order_sr = M("acc_idauthb");
//        $order_sr = M("acc_idauthb")->distinct(true)->field('id,name,phone,idcard,times,money,status')->group('name')->find();

        $get = I("get.");
        $all_rest_money =$order_sr->where()->sum('money');
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
        $get["sn"] != "" ? $where["sn"] = array("like", "%" . $get["sn"] . "%") : null;
      //  $get["name"] != "" ? $where["name"] = array("like", "%" . $get["name"] . "%") : null;
        $get["phone"] != "" ? $where["phone"] = array("like", "%" . $get["phone"] . "%") : null;
        $get["status"] != "" ? $where["status"] = $get["status"] : null;
        #
        //$list = poPage($orders, $where, "id asc");
//        $this->assign("list", $list);
        $this->assign("page", I("get.p"));
        $this->assign("sn", I("get.sn"));
        $this->assign("name", I("get.name"));
        $this->assign("aboutid", I("get.aboutid"));
        $this->assign("phone", I("get.phone"));
        $this->assign("start", I("get.start"));
        $this->assign("ends", I("get.ends"));
        $this->assign("all_rest_money", $all_rest_money);
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
            }
        }
        $this->assign("list", $list);
        $this->display();
    }


}
