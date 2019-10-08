<?php

namespace Backet\Model;

use Think\Model;

class SyscatModel extends Model {

    protected $_validate = array(
        array('name', 'require', '请填写分类名称'),
        //array('name', '', '分类名称已经存在', 0, 'unique', 1),
        //array('sort', 'checkUnique', '排序已经存在,请重新输入', 1, 'callback'),
    );
    #
    protected $_auto = array(
        array('time', 'time', 3, 'function'),
        array('parentid',0),
        array('listorder',1),
        array('m','backet'),
        array('v','goods'),
        array('f','catlist'),
        array('type',1),
    );

/*    protected function checkUnique($data) {
        if ($data <= 0) {
            return true;
        }
        #
        $syscat = M("syscat");
        $where["sort"] = $data;
        $count = $syscat->where($where)->count();
        return $count > 0 ? false : true;
    }*/

}
