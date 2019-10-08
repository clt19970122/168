<?php

namespace Home\Model;

use Think\Model;

class AccIdauthModel extends Model {

    protected $_validate = array(
        array('name', 'require', '请填写姓名'),
        array('phone', 'require', '请填写手机号码'),
        array('phone', 'verifyPhone', '手机号码格式不正确', 1, 'callback'),
        array('phone', '', '手机号码已经注册', 0, 'unique', 1),
        array('idcard', 'verifyIDcard', '证件号码格式不正确', 1, 'callback'),
        //array('myear', 'require', '请填写出生年月'),
    );
    #
    protected $_auto = array(
        array('times', 'time', 1, 'function'),
    );

    ////////////////////////////////////////////////////////////////////////////

    /**
     * 手机监测
     * @param type $data
     * @return boolean
     */
    protected function verifyPhone($data) {
        if (!preg_match("/1[3,4,5,6,7,8,9][0-9]\d{8}$/", $data)) {
            return false;
        }
        return true;
    }

    /**
     * 身份证
     * @param type $data
     * @return type
     */
    protected function verifyIDcard($data) {
        $strlen = strlen($data);
        if ($strlen != 15 && $strlen != 18) {
            return false;
        }
        return true;
    }

}
