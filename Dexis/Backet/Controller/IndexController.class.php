<?php

namespace Backet\Controller;

use Think\Controller;

class IndexController extends Controller {

    /**
     * 主页
     */
    public function index() {
        layout(false);
        $this->display();
    }

    /**
     * 登陆
     * @return type
     */
    public function logon() {
        $sysmang = M("sysmang");
        #
        $post = I("post.");
        if ($post["name"] == null || $post["passwd"] == null) {
            return get_op_put(0, "请输入用户名和密码");
        }
        $post["passwd"] = md5($post["passwd"]);
        //var_dump($post["passwd"] );
        $info = $sysmang->where($post)->find();
        if ($info == null) {
            return get_op_put(0, "用户名或密码错误");
        }
        session("homelc_ssid", $info["id"]);
        session("admin_info", $info);
        $url="Dash/index";
        //销售
        if(session('admin_info')['groups']==4){
            $url="Users/index";
        }elseif(session('admin_info')['groups']==3){
            $url="Goods/order_sr";
        }elseif(session('admin_info')['groups']==6){
            $url="dash/tableimg";
        }
        return get_op_put(1, null, U($url));
    }

    /**
     * 登出
     */
    public function out() {
        session("homelc_ssid", null);
        if (session("homelc_ssid") != null) {
            session_destroy();
        }
        $this->redirect("Index/index");
    }

}
