<?php

/**
 * 0元计划
 */

namespace Backet\Controller;

class PlansController extends CommController {

    /**
     * 
     */
    public function index($type = 10) {
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
                $where["times"] = array(array("gt", $start), array("lt", $ends));
            }
            if ($ends == $start){
                $where["times"] = array(array("gt", $start), array("lt", $ends+86400));
            }
        }
        $type != 10 ? $where["status"] = $type : null;
        $get["sn"] != "" ? $where["sn"] = array("like", "%" . $get["sn"] . "%") : null;
        $get["name"] != "" ? $where["name"] = array("like", "%" . $get["name"] . "%") : null;
        $get["phone"] != "" ? $where["phone"] = array("like", "%" . $get["phone"] . "%") : null;
        $get["status"] != "" ? $where["status"] = $get["status"] : null;
        #
        //$list = poPage($orders, $where, "id asc");
//        $this->assign("list", $list);
        $this->assign("type", $type);
        $this->assign("page", I("get.p"));
        $this->assign("sn", I("get.sn"));
        $this->assign("name", I("get.name"));
        $this->assign("phone", I("get.phone"));
        $this->assign("start", I("get.start"));
        $this->assign("ends", I("get.ends"));
        #
        //$where["status"] = $type != 10 ? array("neq", 0) : 0;
        $list = poPage($order_sr, $where);
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

    public function sendSms(){
        addField('order_sr','smstimes','int(11) DEFAULT 0');
        //发送短信
        $verify = D("Backet/Verif", "Logic");
        $order_sr = M("order_sr");
        $id =I('id');
        $where['id'] =$id;
        $where['times'] =array('gt',strtotime(''));
        $where['status'] =array('in','5,7');
        $info =$order_sr->where($where)->find();
        if($info) {
            $reback_time =strtotime(date('Y-m-d ',$info['times']+24*3600*25));
            if(time() >=$reback_time) {
                $plan_type = $info['money'] == 3000 ? '-0元创业' : ($info['money'] == 1200 ? '-月体验' : ($info['money'] == 480 ? '-周体验' : ($info['money'] == 168 ? '-单盒体验' : '')));
                $product = '0元计划' . $plan_type;
                $re_time = date('Y-m-d ',  strtotime('+1 month',$info['times']));
                if (time() >= strtotime($re_time)) {
                    $product .= '已逾期';
                }
                $re_time = date('Y-m-d ', strtotime('+1 month -1 day',$info['times']));
                $res = $verify->planSms($info['phone'], $product, $info['money'].',', $re_time);
                if($res){
                    $order_sr->where(['id'=>$info['id']])->setInc('smstimes');
                    $this->success('处理成功');
                }

            }else{
                return false;
            }
        }else{
            return false;
        }
    }

}
