<?php

/**
 * 财务管理
 */

namespace Backet\Controller;

class FinaController extends CommController {

    /**
     * 
     */
    public function index($type = 11) {
        $money_draw = M("money_draw");
        $get=I('get.');
        #
        if($type!= 11) {
            $where["status"] = $type;
        }

        if($get["realname"]){
            $user_info =M('account')->where(['name'=>$get["realname"]])->field('id')->select();
            $res_data =array_column($user_info,'id');
            $where['usid'] =array('in',implode(',',$res_data));
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
        $list = poPage($money_draw, $where);
        $this->assign("list", $list);
        $this->assign("get", $get);
        $this->assign("type", $type);
        $this->display();
    }

    ////////////////////////////////////////////////////////////////////////////

    public function doRolls() {
        $money_draw = M("money_draw");
        $post = I("post.");
        #
        $where["id"] = $post["ids"];
        $info = $money_draw->where($where)->find();
        #
        $money = $info["money"] + $info["fees"];
        if (!cgUserMoney($info["usid"], $money, 1, "DRAWR", $info["sn"])) {
            return false;
        }
        #
        $save["status"] = 2;
        if (!$money_draw->where($where)->save($save)) {
            return get_op_put(0, "退款失败[0XSF]");
        }
        return get_op_put(1, null, U('Fina/index'));
    }

    /**
     *获取一条数据
     */
    public function getOneData(){
        $id =I('id');
        $money_draw = M("money_draw");
        $res =$money_draw->where(['id'=>$id])->find();
        $this->ajaxReturn(['data'=>$res]);
    }

    /**
     *使用银行卡线下支付 打款
     */
    public function doByCard(){
        $id =I('id');
        $money_draw = M("money_draw");
        $money_code = M("money_code");
        $sn ='Y'.getOrdSn();
        $draw_info =$money_draw->where(['id'=>$id])->find();
        $add = [
            'uid' => $draw_info['usid'],
            'sn' =>  $sn,
            'drop_id' => $draw_info['sn'],
            'type' => 2,
            'money' => $draw_info['money'],
            'wc_sn' => '--',
            'time' => time(),
            'status' => 1,
        ];
        $add_res =$money_code->add($add);
        if($add_res){
            $res =$money_draw->where(['id'=>$id])->save(['status'=>1]);
            if($res){
                return get_op_put(1, '处理打款成功',U('Fina/index'));
            }else{
                return get_op_put(0, '处理失败【NOCHANGE】', U('Fina/index'));
            }
        }else{
            return get_op_put(0, '处理失败【NOADD】', U('Fina/index'));
        }
    }

    /**
     *获取打款记录
     */
    public function getDrawCode(){
        $draw_code =M('money_code');
        $data =I('get.');
        $where = $data;
        $list = poPage($draw_code, $where);
        foreach ($list['list'] as $k=>$v){
            $list['list'][$k]['sys_name'] =getUserInf($v['uid'],'name');
        }
        $this->assign('list',$list);
        $this->display('Fina/getdrawcode');
    }

    /**
     *微信入账
     */
    public function moneyIn(){
        $paysn=M('syspaysn');
        $data =I('get.');
        if($data['start']||$data['end']){
            $where['times'] = array('between',[strtotime($data['start']),strtotime($data['end'])]);
        }
        $where['body'] ='微信支付';
        $where['status'] = 1;
        $list =poPage($paysn,$where);
        foreach($list['list'] as $k=>$v){
            $list['list'][$k]['time'] =date('Y-m-d H:i:s',$v['times']);
            $list['list'][$k]['inway'] =$v['models'] =='TRAN'?'提货支付':'购买支付';
            $list['list'][$k]['status'] =$v['status'] ==1?'已支付':' ';
        }
        $this->assign('list',$list);
        $this->display();
    }
}
