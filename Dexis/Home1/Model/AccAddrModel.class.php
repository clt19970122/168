<?php

namespace Home\Model;

use Think\Model;

class AccAddrModel extends Model {

    protected $_validate = array(
        array('name', 'require', '请输入收货人姓名'),
        array('phone', 'require', '请输入联系人电话'),
        array('phone', 'checkPhone', '手机号码格式不正确', 1, 'callback'),
        //array('region', 'require', '请选择省市区'),
        array('street', 'require', '请输入收货人地址'),
        //array('idcard', 'require', '请输入身份证号'),
    );
    #
    protected $_auto = array(
        //array('province', 'getProv', 3, 'callback'),
        //array('city', 'getCity', 3, 'callback'),
        //array('label', 'getLabel', 3, 'callback'),
        array('def', 'getDefaut', 3, 'callback'),
        array('status', 1),
        array('times', 'time', 1, 'function'),
    );

    /**
     * 检测手机
     */
    protected function checkPhone($data) {
        if (!preg_match("/^1[345789]{1}\d{9}$/", $data)) {
            return false;
        }
        return true;
    }

    protected function getDefaut() {
        $def = I("post.def");
        if ($def == null) {
            return 0;
        }
        #
        $acc_addr = M("acc_addr");
        $ssid = session("ssid_homelc");
        $where["uid"] = $ssid;
        $save["def"] = 0;
        $acc_addr->where($where)->save($save);
        return 1;
    }

}
