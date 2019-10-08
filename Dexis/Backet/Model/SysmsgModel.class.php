<?php

namespace Backet\Model;

use Think\Model;

class SysmsgModel extends Model {

    protected $_validate = array(
        array('title', 'require', '请输入标题'),
        array('context', 'require', '请填写内容'),
    );
    #
    protected $_auto = array(
        array('times', 'time', 3, 'function'),
    );

}
