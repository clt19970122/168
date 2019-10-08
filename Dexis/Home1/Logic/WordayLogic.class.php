<?php

/**
 * 工作日判断
 */

namespace Home\Logic;

class WordayLogic {

    const l1 = "http://www.easybots.cn/api/holiday.php?d=";
    const l2 = "http://tool.bitefu.net/jiari/?d=";

    private $data;

    public function runs() {
        $this->data = date("Ymd");
        return $this->getLevelRes();
    }

    /**
     *  首次获取
     * @return type
     */
    private function getLevelRes() {
        $url = self::l1 . $this->data;
        $tmp = poCurl($url, null);
        $res = json_decode($tmp, true);
        if ($res == null) {
            return $this->getLevelRcs();
        }
        return $res[$this->data] == 0 ? true : false;
    }

    /**
     * 再次判断
     */
    private function getLevelRcs() {
        $url = self::l2 . $this->data;
        $res = poCurl($url, null);
        return $res == 0 ? true : false;
    }

}
