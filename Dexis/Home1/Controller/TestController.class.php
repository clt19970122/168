<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/2/14
 * Time: 19:29
 */
namespace Home\Controller;

#use Think\Controller;

use Think\Controller;

class TestController extends Controller {
    public function aaa(){
        die;
    }

    //留言
    public function addMessage(){
    header("Access-Control-Allow-Origin: *"); // 允许任意域名发起的跨域请求
    header('Access-Control-Allow-Headers: X-Requested-With,X_Requested_With');
    $data=$_GET;
    $res = M('zz_mess')->add($data);
    if($res){
    	$this->ajaxReturn(['status'=>1,'callback'=>$data['collback']]);
    }
    $this->ajaxReturn(['status'=>0,'callback'=>$data['collback']);
}
}