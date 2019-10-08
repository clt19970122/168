<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/1/8
 * Time: 18:15
 */

namespace Home\Controller;
use Think\Controller;
// 定时任务配合 Common/Cron/CliCron.php
class CronController extends Controller{
    public function _initialize(){
        /*if(!defined('CLI_CRON'){
          die;
        }*/
        if (! defined('CLI_CRON')){
            die;
        }
    }

    public function index(){
        // 实际的业务逻辑处理部分
        echo '123';
    }
}