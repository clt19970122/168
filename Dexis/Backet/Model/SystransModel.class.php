<?php

namespace Backet\Model;

use Think\Model;

class SystransModel extends Model {

    protected $_validate = array(
        array('name', 'require', '请填写物流公司'),
    );
    #
    protected $_auto = array(
        array('times', 'time', 3, 'function'),
    );

}
