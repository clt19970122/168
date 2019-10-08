<?php

namespace Backet\Controller;

#use Think\Controller;

use function Sodium\add;

class DashController extends CommController {

    public function index() {
        $account = M("account");
        $orders = M("orders");
        #
        $this->assign("user", $account->count());
        $this->assign("memb", $account->count());
        $this->assign("ords", $orders->count());
        $this->assign("mony", $orders->where("status=3")->sum("money"));
        $this->display();
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

}
