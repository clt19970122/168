<?php

namespace Home\Logic;

class QrcodeLogic {

    /**
     * 初始化参数
     */
    public function __construct() {
        vendor('phpqrcode.phpqrcode');
    }

    /**
     * 开放入口
     */
    public function entrance($phone, $url) {
        $level = 'L'; //容错级别   
        $size = 10; //生成图片大小
        #
        $root = $_SERVER["DOCUMENT_ROOT"] . __ROOT__ . "/Public/uploads/code/";
        $filename = $phone . ".png";
        $path = $root . $filename;
        \QRcode::png($url, $path, $level, $size, 2);
        return $this->checkFile($filename, $path);
    }

    /**
     * 检测文件状态
     * @param type $name
     * @param type $path
     * @return boolean
     */
    private function checkFile($name, $path) {
        if (!file_exists($path)) {
            return false;
        }
        return $name;
    }

}
