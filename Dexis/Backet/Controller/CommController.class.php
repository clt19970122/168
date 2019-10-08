<?php

namespace Backet\Controller;

use Think\Controller;

class CommController extends Controller {

    public $ssid;

    public function _initialize() {
        if (session("homelc_ssid") == null) {
            $this->redirect("Index/index");
        }
        $this->ssid = session("homelc_ssid");
        set_time_limit(200);
        
        $baseMemory = memory_get_usage();
    }

}
