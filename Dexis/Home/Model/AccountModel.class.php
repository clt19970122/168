<?php

namespace Home\Model;

use Think\Model;

class AccountModel extends Model {

    protected $_validate = array(
        array('phone', 'require', '手机号码不能为空'),
        array('phone', 'verifyPhone', '手机号码格式不正确', 1, 'callback'),
        array('phone', '', '手机号码已经注册', 0, 'unique', 1),
        array('code', 'require', '验证码不能为空'),
        array('passwd', 'require', '密码不能为空'),
        array('repasswd', 'require', '确认密码不能为空'),
        array('repasswd', 'passwd', '确认密码不正确', 0, 'confirm'),
    );
    protected $_auto = array(
        array('sysid', 'getsysid', 1, 'callback'),
        array('passwd', 'md5', 3, 'function'),
        array('status', '1'),
        array('times', 'time', 1, 'function'),
    );

    /**
     * 手机监测
     * @param type $data
     * @return boolean
     */
    protected function verifyPhone($data) {
        if (!preg_match("/1[3,5,7,8,9][0-9]\d{8}$/", $data)) {
            return false;
        }
        return true;
    }

    protected function getsysid() {
        return uniqid(true);
    }

}
