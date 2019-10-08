<?php

namespace Home\Controller;

#use Think\Controller;

class CoreController extends CommController {

    /**
     * 添加
     * @param type $model
     */
    public function addon($model) {
        $db = D($model);
        $data=I('post.');
        addField('order_drs','sn','varchar(255)');
        addField('order_drs','addr','int(11)');
        $data['sn'] =getOrdSn();
        //添加提货
        if($model=='order_drs') {
            $acc_addr = M("acc_addr");
            $cond["id"] = $data['addr'];
            //$cond["def"] = 1;
            $deft = $acc_addr->where($cond)->find();
            if($data['self']==1){
                $data['status']=4;
                $data['have_pay']=3;
            }

            //判断上级出货人信息
            /*$res_sta =get_out_user($this->user['id']);
            if(!$res_sta){
                $data['remakes']='出货人是金融且没有库存，暂不发货';
            }*/


            //获取地区支付费用
//            $isout=getAddressTranPay($deft['street']);
//            //获取运费 --支付等级
//            $trans_price = M('trans_price');
//            $trans_where["gsn"] = '415244730815416283';
//            $trans_where["isout"] = $isout;
//            $trans_where["nums"] = $data['nums'];
//            $level_pay = $trans_price->where($trans_where)->order('nums desc')->select();
////            $allcount = $data['nums'];
////            $pay = 0;
//            //快递费用支付 ----暂注 2018-8-15 16:29:54
//            /*foreach ($level_pay as $k => $v) {
//                $xiang = floor($allcount / $v['nums']) * $v['price'];//整箱
//                if ($k == count($level_pay) - 1) {
//                    $xiang = ceil($allcount / $v['nums']) * $v['price'];
//                }
//                $rest = fmod($allcount, $v['nums']);
//                $pay += $xiang;
//                $allcount = $rest;
//            }*/
//            $data['trans_pay'] = $level_pay['price'];
        }
        //添加地址
        if(isset($data['province'])) {
            $data['street']=$data['province'].','.$data['street'];
            unset($data['province']);
        }
        if (!$db->create($data, 1)) {
            return get_op_put(0, $db->getError());
        }
        if (!$db->add()) {
            return get_op_put(0, "添加失败");
        }
        $this->addon_check($model);
        //如果是自提
        if($data['self']==1) {
                $user_info=M('account')->where(['id'=>$data['uid']])->find();
                if($user_info['stock']>=$data["nums"]) {
                    M('account')->where(['id' => $data['uid']])->setDec('stock', $data['nums']);
                    ###添加记录----2018-8-15 add
                    $add_nums = [
                        'uid' => $this->user['id'],
                        'aboutid' => 0,
                        'sn' => $data['sn'],
                        'nums' => $data["nums"],
                        'after' => 0,
                        'uafter' => $user_info['stock'] - $data['nums'],
                        'time' => time(),
                        'type' => 23, //提货
                    ];
                    addAcc_nums($add_nums);
                }

            return get_op_put(1, "添加成功");
        }
        return get_op_put(1, "添加成功",$data['sn']);
    }

    /**
     * 添加检测
     * @param type $model
     */
    private function addon_check($model) {
        $model == "acc_elcs" ? closeSysLn($model, 4, $this->ssid) : null;
    }

    /**
     * 编辑客户信息
     * @param type $model
     */
    public function edits($model) {
        $db = D($model);
        #
        $data=I('post.');
        //添加地址
        if(isset($data['province'])) {
            $data['street']=$data['province'].','.$data['street'];
            unset($data['province']);
        }
        if (!$db->create($data, 2)) {
            return get_op_put(0, $db->getError());
        }
        if (!$db->where("id='" . I("post.ids") . "'")->save()) {
            return get_op_put(0, "没有修改");
        }
        return get_op_put(1, "修改成功");
    }

    /**
     * 删除客户
     * @return type
     */
    public function dels($model) {
        $db = D($model);

        $post = I("post.");
        if (!$db->where($post)->delete()) {
            return get_op_put(0, "删除失败");
        }
        return get_op_put(1, $post);
    }

}
