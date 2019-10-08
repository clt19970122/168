<?php

namespace Backet\Controller;

class MembsController extends CommController {

    /**
     * 
     */
    public function level() {
        $acc_level = M("acc_level");
        #
        addField('acc_level','buynum','int(11) DEFAULT 1');//添加字段
        $list = $acc_level->select();
        $this->assign("list", $list);
        $this->display();
    }

    /**
     * 
     */
    public function leveledit($id) {
        $acc_level = M("acc_level");
        #
        $where["id"] = $id;
        $info = $acc_level->where($where)->find();
        $this->assign("info", $info);
        $this->display();
    }

    /**
     * 
     */
    public function draws() {
        $sysconfig = M("sysconfig");
        #
        $where["type"] = "draw";
        $where["status"] = 1;
        $list = $sysconfig->where($where)->select();
        $this->assign("list", $list);
        $this->display();
    }

}
