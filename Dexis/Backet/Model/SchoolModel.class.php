<?php

namespace Backet\Model;

use Think\Model;

class SchoolModel extends Model {

    protected $_validate = array(
        array('title', 'require', '请输入标题'),
        array('file', 'checkImg', '请上传封面图片', 1, 'callback', 1),
        array('video', 'require', '请上传视频链接'),
    );
    #
    protected $_auto = array(
        array('imgs', 'getUpload', 1, 'callback'),
        array('imgs', 'imgUpdate', 2, 'callback'),
        array('times', 'time', 3, 'function'),
    );

    protected function checkImg() {
        if ($_FILES["file"]["name"] == null) {
            return false;
        }
        return true;
    }

    protected function getUpload() {
        $imgs = uploadFile("school");
        if (!$imgs) {
            return false;
        }
        return $imgs["file"]["savename"];
    }

    protected function imgUpdate() {
        $imgs = uploadFile("school");
        if ($imgs) {
            return $imgs["file"]["savename"];
        }
        #
        $school = M("school");
        #
        $where["id"] = I("post.ids");
        $info = $school->where($where)->find();
        return $info["imgs"];
    }

}
