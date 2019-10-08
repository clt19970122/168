<?php

$domin = array("x", "localhost", "172.27.16.17", "140.143.90.20","171.216.70.208","47.52.196.91");
$status = array_search($_SERVER["SERVER_NAME"], $domin);

return array(
    //数据库配置
    'DB_TYPE' => 'mysql',
    'DB_HOST' => "localhost",
    'DB_NAME' => "dexis",
    'DB_USER' => "root",
    'DB_PWD' => "",
    'DB_PORT' => 3306,
    'DB_PREFIX' => '',
    //jacobhooychina
    'WEBURL' => 'http://www.jacobhooychina.cn/',
    'URL_MODEL' => 2,
    //模版
    'LAYOUT_ON' => true,
    'LAYOUT_NAME' => 'layout',
    //系统设置
    'WEC_CONFIG' => array(
        "APPID" => "wx35e214c1721ff689",
        "APPSECRET" => "c179b118a5033b01a0bca7724b15c497",
        "TOKEN" => "", #验证令
        "MCHID" => "1505023651", #商户号
        "PAYKEY" => "casc168sushichina20180530sushi66", #支付密钥
        "notifyUrl" => "http://www.jacobhooychina.cn/Home/Wcix/respay.html", #支付通知
        "redirectUri" => "http://www.jacobhooychina.cn/Home/Wcix/getInfos.html", #获取用户信息
    ),
    'SMS_CONF' => array(
        "ACCOUNT" => "C45393729",
        "PASSWD" => "508df989959e24b749b9c0a19cfe0105"
    ),
    'PUM_CONF' => array(
        "LINKS" => "http://221.236.20.216:810/pcl/services/loanCenter/jh/queryJhFilter",
        "RESPAY" => "http://221.236.20.216:810/pcl/services/loanCenter/jh/activeJhRepay",
        //"LINKS" => "http://test1.phkjcredit.com/pcl/services/loanCenter/jh/queryJhFilter",
        "REKEY" => "964d2026e854f464ae4f7dfb4bd4b488",
    ),
    'akk' => array(
        "LINKS" => "http://akk.028wkf.cn/gateway/app-api/api/member/getToken",
    ),
    'LOAD_EXT_CONFIG' => 'local',
    'HTML_FILE_SUFFIX' =>'.html'
);
