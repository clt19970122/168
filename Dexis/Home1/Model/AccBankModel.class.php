<?php

namespace Home\Model;

use Think\Model;

class AccBankModel extends Model {

    protected $_validate = array(
        array('bank', 'require', '请选择开户行'),
        array('name', 'require', '请填写持卡人'),
        array('card', 'require', '请填写银行卡号'),
        array('card', 'checkBanks', '银行卡号格式不正确', 1, 'callback'),
    );
    #
    protected $_auto = array(
        array('times', 'time', 1, 'function'),
    );

    protected function checkBanks($data) {
        $lent = strlen($data);
        #
        if (!is_numeric($data)) {
            return false;
        }
        if ($lent < 16 || $lent > 19) {
            return false;
        }
        return true;
    }

}
