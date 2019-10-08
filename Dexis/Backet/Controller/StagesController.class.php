<?php
/**
 * Created by PhpStorm.
 * User: uuu
 * Date: 2019/5/13
 * Time: 14:50
 */

namespace Backet\Controller;

class StagesController extends CommController {
    /**
     *获取列表
     */
    public function index(){
        $get =I('get.');
//        $where['page'] =$get['p'];
        $stages =M('acc_stages');
        //获取分期进行的列表
        $list =poPage($stages,$get,'id desc',10);
        foreach($list['list'] as $k=>$v){
            $list['list'][$k]['level'] =getUserLevel($v['level']);
            $list['list'][$k]['time'] =date('Y-m-d H:i:s',$v['add_time']);
            $list['list'][$k]['c_status'] =stageStatus($v['status']);
            $top=gettop(getUserInf($v['uid'],'recid'),6);
            $list['list'][$k]['top'] =$top['name'];
            //如果是成功办理
            if($v['status'] == 1){
                $havpay =$this->pay($v['id']);
                $list['list'][$k]['pay_count'] =count($havpay);
            }
        }
        $this->assign('list',$list);
        $this->display();
    }

    /**
     *获取分期支付记录
     */
    public function  pay($id,$type=null){
        $st_pay =M('acc_st_pay');
        $stages =M('acc_stages');
        //获取申请记录
        $stage_info =$stages->where(['id'=>$id])->find();
        $where['s_id'] =$id;
        $where['status'] =1;
        //分期支付记录
        $info =$st_pay->where($where)->select();
        if($type){
            $this->assign('pay',$info);
            $this->assign('info',$stage_info);
            $this->display();
        }else{
            return $info;
        }
    }

    /**
     *修改订单
     */
    public function edit(){
        $get =I('post.');
        $stages =M('acc_stages');
//        $st_pay =M('acc_st_pay');
//        $save[]=$get['status'];
        $res =$stages->where(['id'=>$get['id']])->save($get);
        if($res){
            return get_op_put(1, '修改成功','',1);
        }else{
            return get_op_put(0, '修改失败');
        }
    }

    /**
     *后台处理分期支付
     */
    public function  doPayBuyback(){
        $sid =I('sid');
        $return =I('res');
        $stages=M('acc_stages');
        $st_pay=M('acc_st_pay');
        $stage_where['id']=$sid;
        $sn ='ST'.getOrdSn();
        $stage_info =$stages->where($stage_where)->find();
        //修改后返回
        if($return){
            var_dump($return);
            $s_sn =S('stage_sn');
            $save['status'] =1;
            if($stage_info['status'] ==1){
                $stage_cg =$stages->where($stage_where)->save($save);
            }
            $cg_res =$st_pay->where(['sn'=>$s_sn])->save($save);
            if($cg_res){
                return get_op_put(1, '修改成功','',1);
            }
            return get_op_put(0, '修改失败');
        }
        //支付金额
        $goods_info =M('goods_level')->where(['gsn'=>'415244730815416283','lid'=>$stage_info['level']])->find();
        //获取上次支付的分期
        $pay_info =$st_pay->where(['s_id'=>$sid,'status'=>1])->order('no desc')->find();
        //处理价格
        $nums =ceil($stage_info['every_pay']/$goods_info['price']);
        //添加记录
        $add=[
            'uid'=>$stage_info['uid'],
            's_id'=>$sid,
            'status'=>0,
            'time'=>time(),
            'money'=>$stage_info['every_pay'],
            'no'=>$pay_info['no']+1,
            'sn'=>$sn,
            'nums'=>$nums,
        ];
        $res =$st_pay->add($add);
        S('stage_sn',$sn);
        if($res){
            return get_op_put(3, '跳转更新记录',['uid'=>$stage_info['uid'],'level'=>$stage_info['level'],'nums'=>$nums,'sn'=>$sn],U('users/editsSelf'));
        }else{
            return get_op_put(0, '修改失败');
        }

    }
}
