<?php

namespace Home\Controller;

#use Think\Controller;

class OrderController extends CommController {

    public function _initialize() {
        parent::_initialize();
        layout("laynot");
        #
        $ssid = session("ssid_dexis");
        if ($ssid == null && CONTROLLER_NAME != "Login") {
            $this->redirect("Login/index");
        }
    }

    /**
     * 订单列表
     */
    public function index($st = 10,$p=1) {
        layout("layout");
        $orders = M("orders");
        $acc_nums = M("acc_nums");
        $account = M("account");
        #
        $where["uid"] = $this->ssid;
        $st != 10 ? $where["status"] = $st : null;
        $where['is_add'] =0;
        $list = $orders->where($where)->order("id desc")->page($p,10)->select();
        $pages = $orders->where($where)->order("id desc")->page($p+1,10)->find();
        if($pages==null){
            $page='none';
        }else{
            $page='block';
        }
        foreach ($list as $k => $v) {
            $tmp = getIDGoodsInfo($v["gid"]);
            $list[$k]["ajax_times"] = date('Y年m月d日 H:i:s',$v["times"]);
            $list[$k]["ajax_status"] = orderStatus($v["status"],$v['gid']);
            $list[$k]["status_class"] = orderClass($v["status"],$v['gid']);
            $list[$k]["gsn"] = $tmp["gsn"];
            $list[$k]["introduct"] = $tmp["subname"];
            //查询出货人
            $nums_data =$acc_nums->where(['sn'=>$v['sn']])->find();
            if ($nums_data['aboutid'] == null) {
                $up_user['name'] = null;
                $up_user['phone'] = null;
            }elseif($nums_data['aboutid'] == 0){
                $up_user['name'] = '公司';
                $list[$k]['phone'] ='';
            }else{
                $up_user = $account->where(['id' => $nums_data['aboutid']])->find();
                $list[$k]['phone'] =$up_user['phone'];
            }
            $list[$k]['up_user'] = $up_user['name'];
        }
        $arr=array();
        if(IS_AJAX) {
            if (empty($list)) {
                $arr['error'] = 0;
            } else {
                $arr['error'] = 1;
                $arr['data'] = $list;
                $arr['page'] = $pages;
              //  $arr['style'] = $arrs['style'];
            }
            $this->ajaxReturn($arr);
            exit;
        }
        #
         $this->assign("page", $page);
        $this->assign("list", $list);
        $this->assign("st", $st);
        $this->display();
    }

    /**
     * 订单详情
     * @param type $id
     */
    public function infos($id) {
        $orders = M("orders");
        #
        $where["id"] = $id;
        $info = $orders->where($where)->find();
        $tmp = getIDGoodsInfo($info["gid"]);
        $info["gsn"] = $tmp["gsn"];
        #
        $this->assign("info", $info);
        $this->display();
    }

    /**
     * 物流记录
     */
    public function trans($sn) {
        $order_trans = M("order_trans");
        $expres = D("Home/Expres", "Logic");
        #
        $where["sn"] = $sn;
        $info = $order_trans->where($where)->find();
        #
        $name = getTransNm($info["transid"]);
        $trans = $expres->getorder($name, $info["vosn"]);
        #
        $this->assign("info", $info);
        $this->assign("trans", $trans);
        $this->display();
    }

}
