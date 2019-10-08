<?php

namespace Home\Model;

use Think\Model;

class OrderDrsModel extends Model {

    protected $_validate = array(
        array('name', 'require', '请输入收货人姓名'),
        array('phone', 'require', '请输入联系人电话'),
        array('phone', 'verifyPhone', '手机号码格式不正确', 1, 'callback'),
        array('address', 'require', '请输入收货人地址'),
        array('nums', 'require', '请输入提货件数'),
        array('nums', 'verifyNums', '提货数量不足', 1, 'callback'),
        array('nums', 'cgNopayOrder', '', 1, 'callback'),
//        array('nums', 'verifyHaveNotDoOrder', '订单有未支付处理，请处理后再申请', 1, 'callback'),
    );
    protected $_auto = array(
        array('uptimes', 'time', 3, 'function'),
        array('times', 'time', 1, 'function'),
    );

    /**
     * 手机监测
     * @param type $data
     * @return boolean
     */
    protected function verifyPhone($data) {
        /*if($data=='自提'){
            return true;
        }*/
        if (!preg_match("/1[1,2,3,4,5,6,7,8,9][0-9]\d{8}$/", $data)) {
            return false;
        }
        return true;
    }

    protected function verifyNums($data) {
        $ssid = session("ssid_dexis");
        $account = M("account");
        #
        $where["id"] = $ssid;
        $info = $account->where($where)->find();
        // return $info["stock"] > $data ? true : false;
        if($info["stock"]>=$data){
            return true;
        }else{
            return false;
        }
    }
//验证有没订单
   /* protected function verifyHaveNotDoOrder(){
        $ssid = session("ssid_dexis");
        $account = M("order_drs");
        $where['uid']=$ssid;
        $where['status']=0;
        $res =$account->where($where)->select();
        if($res){
            return false;
        }else{
            return true;
        }
    }*/

    /**修改未支付订单状态
     * @return bool
     */
    protected  function cgNopayOrder(){
        $ssid = session("ssid_dexis");
        $account = M("order_drs");
        $where['uid']=$ssid;
        $where['status']=0;
//        $res =$account->where($where)->select();
        $res =$account->where($where)->save(['have_pay'=>2,'status'=>5]);
        return true;
    }

}
