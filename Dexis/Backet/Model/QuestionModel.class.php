<?php

namespace Backet\Model;

use Think\Model;

class QuestionModel extends Model {

    protected $_validate = array(
        array('title', 'require', '请输入标题'),
        array('content', 'require', '请填写内容'),
    );
    #
    protected $_auto = array(
        array('times', 'time', 3, 'function'),
    );

}
