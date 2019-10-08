<?php

namespace Backet\Model;

use Think\Model;

class SysadsModel extends Model {

    protected $_validate = array(
        array('links', 'require', '请填写链接地址'),
        array('sort', 'require', '请填写排序'),
        array('file', 'checkImgs', '请上传图片', 1, 'callback', 1),
    );
    #
    protected $_auto = array(
        array('imgs', 'set_imgs', 3, 'callback'),
        array("status", 1),
        array('times', 'time', 3, 'function'),
    );

    /**
     * 检测图片
     * @return type
     */
    protected function checkImgs() {
        if ($_FILES["file"]["name"] == null) {
            return false;
        }
        return true;
    }

    /**
     * 设置图片
     */
    protected function set_imgs() {
        if ($_FILES["file"]['name'] != null) {
            $tmp = uploadFile("ads");
            return $tmp["file"]["savename"];
        }
        $sysads = M("sysads");
        $where["id"] = I("post.ids");
        $info = $sysads->where($where)->find();
        if ($info == null) {
            return 0;
        }
        return $info["imgs"];
    }

}
