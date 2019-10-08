<?php

namespace Backet\Controller;

#use Think\Controller;

class GoodsController extends CommController {

    /**
     * 商品列表
     */
    public function index() {
        $goods = M("goods");
        $get = I("get.");
        #
        $start = strtotime($get["start"]);
        $ends = strtotime($get["ends"]);
        if ($start && $ends && $ends > $start) {
            $where["times"] = array(array("gt", $start), array("lt", $ends));
        }
        $get["name"] != null ? $where["name"] = array("like", "%" . $get["name"] . "%") : null;
        $get["shop"] != null ? $where["sid"] = array("in", searchInShop($get["shop"])) : null;
        $get["catid"] != 0 ? $where["catid"] = $get["catid"] : null;
        $get["rec"] != null ? $where["rec"] = $get["rec"] : null;
        $get["status"] != null ? $where["status"] = $get["status"] : null;
        $list = poPage($goods, $where);
        #
        $this->assign("list", $list);
        #
        $this->assign("page", $get["p"]);
        $this->assign("get", $get);
        $this->display();
    }

    /**
     * 添加商品
     */
    public function indexadd() {
        $acc_level = M("acc_level");
        $syscat = M("syscat");
        #
        $list = $acc_level->select();
        $catelist = $syscat->select();
        #
        $this->assign("list", $list);
        $this->assign("catlist", $catelist);
        $this->assign("gsn", getSysLn("goods", 1, $this->ssid));
        $this->display();
    }

    /**
     * 添加商品图片
     * @return type
     */
    public function indexadd_imgs() {
        $post = I("post.");
        $uplod = uploadFile("goods");
        if (!$uplod) {
            return get_op_put(0, "添加图片失败");
        }
        #
        $goods_img = M("goods_img");
        #
        $new["gsn"] = $post["sn"];
        $new["imgs"] = $uplod["file"]["savename"];
        $new["sort"] = 0;
        $new["status"] = 1;
        $new["times"] = time();
        $new["id"] = $goods_img->add($new);
        if (!$new["id"]) {
            return get_op_put(0, "添加图片失败.");
        }
        $new["times"] = date("Y/m/d H:i:s", $new["times"]);
        return get_op_put(1, $new);
    }

    public function indexedit($id) {
        $goods = M("goods");
        $goods_img = M("goods_img");
        $acc_level = M("acc_level");
        $goods_level = M("goods_level");
        $syscat = M("syscat");
        #
        $where["id"] = $id;
        $info = $goods->where($where)->find();
        $this->assign("info", $info);
        #
        $cond["gsn"] = $info["gsn"];
        $imgs = $goods_img->where($cond)->select();
        $this->assign("imgs", $imgs);
        #
        $catelist = $syscat->select();
        $this->assign("catlist", $catelist);
        #
        $list = $acc_level->select();
        foreach ($list as $k => $v) {
            $mode = array("lid" => $v["id"], "gsn" => $info["gsn"]);
            $tmp = $goods_level->where($mode)->find();
            $list[$k]["price"] = $tmp["price"];
        }
        $this->assign("list", $list);
        $this->display();
    }

    ////////////////////////////////////////////////////////////////////////////

    /**
     * 分类列表
     */
/*    public function catlist() {
        $syscat = M("syscat");
        #
        //$list = poPage($syscat, $where);
        $this->assign("list", $list);
        $this->display();
    }*/

    public function catlist(){
        $syscat =M("syscat");
        $list = poPage($syscat, $where);
        $this->assign("list", $list);
        $this->display();
    }
    /**
     * 添加分类
     */
    public function addcat(){
        $this->display();
    }
    /**
     * 删除分类
     */
    public function catdels() {
        $syscat = M("syscat");
        $goods = M("goods");
        $post = I("post.");
        #
        $where["catid"] = $post["id"];
        $count = $goods->where($where)->count();
        if ($count > 0) {
            return get_op_put(0, "分类下还有商品，暂时无法删除");
        }
        #
        $cond["id"] = $post["id"];
        if (!$syscat->where($cond)->delete()) {
            return get_op_put(0, "删除失败");
        }
        return get_op_put(1, "删除成功", U("Goods/catlist"));
    }

    /**
     * 修改分类
     * @param type $id
     */
    public function editcat($id) {
        $syscat = M("syscat");

        $where["id"] = $id;
        $info = $syscat->where($where)->find();
        $this->assign("info", $info);
        $this->display();
    }

    ////////////////////////////////////////////////////////////////////////////

    /**
     * 订单列表
     * @param type $type
     */
    public function order($type = 10) {
        $orders = M("orders");
        $get = I("get.");
        //获取推荐人信息
        $tjid=$get['tjid'];
        if($tjid ) {
            $select_acc = M('account')->where(['id' => $tjid])->find();
            $low_user = M('account')->where(['recid' => $select_acc['sysid']])->select();
            if(count($low_user)>0){
                foreach ($low_user as $v) {
                    $low_id[] = $v['id'];
                }
                $where['uid'] =array('in',implode(',',$low_id));
            }else{
                $where['uid'] =$low_user[0]['uid'];
            }
        }
        #
        #下单用户
        $username =$get['username'];
        if($username){
            $where_acc['nickname']=array("like", "%" .$username . "%");
            $acc_info=M('account')->where($where_acc)->select();
            foreach ($acc_info as $v){
                $uid[]=$v['id'];
            }
            $where['uid']=array('in',implode(',',$uid));
        }
        #时间
        if($get['start']||$get["ends"]){
            $start = strtotime($get["start"]);
            $ends = strtotime($get["ends"]);
            if(!$ends){
                $where["paytime"] = array("gt", $start);
            }if(!$start){
                $where["paytime"] = array("lt", $ends);
            }
            if ($ends > $start) {
                $where["paytime"] = array(array("gt", $start), array("lt", $ends));
            }
            if ($ends == $start){
                $where["paytime"] = array(array("gt", $start), array("lt", $ends+86400));
            }
        }
        $type != 10 ? $where["status"] = $type : null;
        $get["sn"] != "" ? $where["sn"] = array("like", "%" . $get["sn"] . "%") : null;
        //$get["name"] != "" ? $where["name"] = array("like", "%" . $get["name"] . "%") : null;
        $get["phone"] != "" ? $where["phone"] = array("like", "%" . $get["phone"] . "%") : null;
        $get["status"] != "" ? $where["status"] = $get["status"] : null;
        $get["paytype"] != "" ? $where["paytype"] = $get["paytype"] : null;

        //获取摇摇杯的的订单
        if($get['gid']) {
            $where["gid"] = $get["gid"];
        }


        #
        $list = poPage($orders, $where, "id desc");
        $this->assign("list", $list);
        $this->assign("type", $type);
        $this->assign("page", I("get.p"));
        $this->assign("name", I("get.name"));
        $this->assign("phone", I("get.phone"));
        $this->assign("start", I("get.start"));
        $this->assign("end", I("get.ends"));
        $this->assign("tjid",$tjid);
        $this->assign("username",$username);
        $this->assign("get",$get);
        $this->display();
    }

    /**
     * 订单详情
     * @param type $id
     */
    public function orderinfo($id) {
        $orders = M("orders");
        $systrans = M("systrans");
        $order_trans = M("order_trans");
        //
        $where["id"] = $id;
        $info = $orders->where($where)->find();
        $this->assign("info", $info);
        #
        $cond["sn"] = $info["sn"];
        $this->assign("trans", $systrans->where("status=1")->select());
        $this->assign("th", $order_trans->where($cond)->find());
        $this->display();
    }

    /**
     * 摇摇杯订单列表
     * @param type $type
     */
    public function order_cup($type = 10) {
        $orders = M("orders");
        $get = I("get.");
        //获取推荐人信息
        $tjid=$get['tjid'];
        if($tjid ) {
            $select_acc = M('account')->where(['id' => $tjid])->find();
            $low_user = M('account')->where(['recid' => $select_acc['sysid']])->select();
            if(count($low_user)>0){
                foreach ($low_user as $v) {
                    $low_id[] = $v['id'];
                }
                $where['uid'] =array('in',implode(',',$low_id));
            }else{
                $where['uid'] =$low_user[0]['uid'];
            }
        }
        #
        #下单用户
        $username =$get['username'];
        if($username){
            $where_acc['nickname']=array("like", "%" .$username . "%");
            $acc_info=M('account')->where($where_acc)->select();
            foreach ($acc_info as $v){
                $uid[]=$v['id'];
            }
            $where['uid']=array('in',implode(',',$uid));
        }
        #时间
        if($get['start']||$get["ends"]){
            $start = strtotime($get["start"]);
            $ends = strtotime($get["ends"]);
            if(!$ends){
                $where["paytime"] = array("gt", $start);
            }if(!$start){
                $where["paytime"] = array("lt", $ends);
            }
            if ($ends > $start) {
                $where["paytime"] = array(array("gt", $start), array("lt", $ends));
            }
            if ($ends == $start){
                $where["paytime"] = array(array("gt", $start), array("lt", $ends+86400));
            }
        }
        $type != 10 ? $where["status"] = $type : null;
        $get["sn"] != "" ? $where["sn"] = array("like", "%" . $get["sn"] . "%") : null;
        //$get["name"] != "" ? $where["name"] = array("like", "%" . $get["name"] . "%") : null;
        $get["phone"] != "" ? $where["phone"] = array("like", "%" . $get["phone"] . "%") : null;
        $get["status"] != "" ? $where["status"] = $get["status"] : null;
        $get["paytype"] != "" ? $where["paytype"] = $get["paytype"] : null;

        //获取摇摇杯的的订单
        if($get['gid']) {
            $where["gid"] = $get["gid"];
        }
        #
        $list = poPage($orders, $where, "id desc");
        $acc_addr =M('acc_addr');
        foreach ($list['list'] as $k =>$v){
            $addr_info =$acc_addr->where(['id'=>$v['addr']])->find();
            $list['list'][$k]['add'] =$addr_info['street'];
            $list['list'][$k]['add_name'] =$addr_info['name'];
            $list['list'][$k]['add_phone'] =$addr_info['phone'];
        }
        $this->assign("list", $list);
        $this->assign("type", $type);
        $this->assign("page", I("get.p"));
        $this->assign("name", I("get.name"));
        $this->assign("phone", I("get.phone"));
        $this->assign("start", I("get.start"));
        $this->assign("end", I("get.ends"));
        $this->assign("tjid",$tjid);
        $this->assign("username",$username);
        $this->assign("get",$get);
        $this->display();
    }

    /**
     * 订单操作
     */
    public function orderConanel() {
        set_time_limit(0);
        #
        $orders = M("orders");
        $divide = D("Backet/Divide", "Logic");
        #
        $where["sn"] = I("post.sn");
        $info = $orders->where($where)->find();
        if ($info["status"] != 0) {
            return get_op_put(0, "无法操作");
        }
        #
        $save["snrec"] = getOrderRec($this->order["openid"]);
        $save["paytime"] = time();
        $save["status"] = 1;
        if (!$orders->where($where)->save($save)) {
            return get_op_put(0, "付款失败");
        }
        #
        if (!$divide->runon($info)) {
            return get_op_put(0, "操作失败");
        }
        return get_op_put(1, "设置成功", 1);
    }

    /**
     * 订单发货
     */
    public function orderTrans() {
        $orders = M("orders");
        $order_trans = M("order_trans");
        $account = M("account");
        #$wctemp = D("Home/Wctemp", "Logic");
        #
        $post = I("post.");
        $get_type =$post['type'];
        //提货类型为自提
        if($get_type==1){
            $where["sn"] = $post["sn"];
            $info = $orders->where($where)->find();
            if ($info == null) {
                return get_op_put(0, "订单不存在");
            }
            $save["trantime"] = time();
            $save["status"] = 5;
            if (!$orders->where($where)->save($save)) {
                return get_op_put(0, "提货失败");
            }
            if (!$order_trans->add($post)) {
                return get_op_put(0, "提货失败...");
            }
            #
            if ($info['gid'] == 8) {
                $update["stock"] = array("exp", "stock-" . $info["gnums"]);
                if (!$account->where("id='" . $info["uid"] . "'")->save($update)) {
                    return get_op_put(0, "发货失败[ACCUP]");
                }
                //扣除库存
                ###添加记录---- 2018-9-6 13:55:50add
                $user_info = $account->where(['id' => $info['uid']])->field('stock')->find();
                $add_nums = [
                    'uid' => $info['uid'],
                    'aboutid' => 0,
                    'sn' => $info['sn'],
                    'nums' => $info["gnums"],
                    'after' => 0,
                    'uafter' => $user_info["stock"],
                    'time' => time(),
                    'type' => 23, //提货记录
                ];
                addAcc_nums($add_nums);
            }
        }else {
            $where["sn"] = $post["sn"];
            $info = $orders->where($where)->find();
            if ($info == null) {
                return get_op_put(0, "订单不存在");
            }
            if ($post["transid"] == 0) {
                return get_op_put(0, "请选择物流公司");
            }
            if ($post["vosn"] == "") {
                return get_op_put(0, "请填写物流单号");
            }
            $save["trantime"] = time();
            $save["status"] = 2;
            if (!$orders->where($where)->save($save)) {
                return get_op_put(0, "发货失败");
            }
            if (!$order_trans->add($post)) {
                return get_op_put(0, "发货失败...");
            }
            #
            if ($info['gid'] == 8) {
                $update["stock"] = array("exp", "stock-" . $info["gnums"]);
                if (!$account->where("id='" . $info["uid"] . "'")->save($update)) {
                    return get_op_put(0, "发货失败[ACCUP]");
                }
                //扣除库存
                ###添加记录---- 2018-9-6 13:55:50add
                $user_info = $account->where(['id' => $info['uid']])->field('stock')->find();
                $add_nums = [
                    'uid' => $info['uid'],
                    'aboutid' => 0,
                    'sn' => $info['sn'],
                    'nums' => $info["gnums"],
                    'after' => 0,
                    'uafter' => $user_info["stock"],
                    'time' => time(),
                    'type' => 23, //提货记录
                ];
                addAcc_nums($add_nums);
            }
        }
        return get_op_put(1, "发货成功", U('Goods/orderinfo', array("id" => $info["id"])));
    }

    /**
     * 订单提货
     * @param type $type
     */
    public function order_sr($type = 10) {
        $order_drs = M("order_drs");
        $systrans = M("systrans");
        $get = I("get.");
        #
        $username =$get['username'];
        if($username){
            $where_acc['nickname']=array("like", "%" .$username . "%");
            $acc_info=M('account')->where($where_acc)->select();
            foreach ($acc_info as $v){
                $uid[]=$v['id'];
            }
            $where['uid']=array('in',implode(',',$uid));
        }
        #
        $start = strtotime($get["start"]);
        $ends = strtotime($get["ends"]);
        if ($start && $ends && $ends > $start) {
            $where["times"] = array(array("gt", $start), array("lt", $ends));
        }
        $type != 10 ? $where["status"] = $type : null;
        $get["name"] != "" ? $where["name"] = array("like", "%" . $get["name"] . "%") : null;
        $get["phone"] != "" ? $where["phone"] = array("like", "%" . $get["phone"] . "%") : null;
        $get["status"] != "" ? $where["status"] = $get["status"] : null;
        //是否支付
//        $where['have_pay'] =1;
        #
        $list = poPage($order_drs, $where, "id desc");
        $this->assign("list", $list);
        $this->assign("type", $type);
        $this->assign("page", I("get.p"));
        $this->assign("name", I("get.name"));
        $this->assign("phone", I("get.phone"));
        $this->assign("username",$username);
        $this->assign("trans", $systrans->where("status=1")->select());
        $this->display();
    }

    /**
     * 处理订单提货
     */
    public function orderSrOption() {
        $order_drs = M("order_drs");
        $account = M("account");
        $post = I("post.");
        #
        $where["id"] = $post["id"];
        $info = $order_drs->where($where)->find();
        $save["status"] = $post["status"];
        if ($post["status"] == 2) {
            if (!$order_drs->where($where)->save($save)) {
                return get_op_put(0, "处理失败");
            }
            return get_op_put(1, null, U("Goods/order_sr"));
        }
        if ($post["status"] ==4) {
            if (!$order_drs->where($where)->save($save)) {
                return get_op_put(0, "处理失败");
            }
            return get_op_put(1, null, U("Goods/order_sr"));
        }
        #
        $update["stock"] = array("exp", "stock-" . $info["nums"]);
        if (!$account->where("id='" . $info["uid"] . "'")->save($update)) {
            return get_op_put(0, "处理失败");
        }
        if (!$order_drs->where($where)->save($save)) {
            return get_op_put(0, "处理失败");
        }
        ###添加记录----2018-9-6 17:31:14 add
        $user_info =$account->where(['id'=>$info['uid']])->field('stock')->find();
        $add_nums = [
            'uid' => $info['uid'],
            'aboutid' => 0,
            'sn' => $info['sn'],
            'nums' => $info["nums"],
            'after' => 0,
            'uafter' => $user_info["stock"],
            'time' => time(),
            'type' => 23, //提货记录
        ];
        addAcc_nums($add_nums);

        return get_op_put(1, null, U("Goods/order_sr"));
    }

    /**
     * 提货发货
     */
    public function orderSrTrans() {
        $order_drs = M("order_drs");
        $post = I("post.");
        #
        $where["id"] = $post["ids"];
        if($post['url']){
            $url= '';
        }else{
            $url = U('Goods/order_sr');
        }
        $info = $order_drs->where($where)->find();
        if ($info == null) {
            return get_op_put(0, "订单不存在");
        }
        if ($post["trans"] == 0) {
            return get_op_put(0, "请选择物流公司");
        }
        if ($post["trsn"] == "") {
            return get_op_put(0, "请填写物流单号");
        }
        $tran_info =M('systrans')->where(['id'=>$post["trans"]])->find();
        $post["status"] = 3;
        if (!$order_drs->where($where)->save($post)) {
            return get_op_put(0, "发货失败");
        }
        //发送短信
        $verify = D("Backet/Verif", "Logic");
        $res =$verify->TransendSms($info['phone'],$info['name'],$tran_info['name'],$post["trsn"]);
        //扣除库存
        ###添加记录---- 2018-9-6 17:30:59 add
        $user_info =M('account')->where(['id'=>$info['uid']])->field('stock')->find();
        $add_nums = [
            'uid' => $info['uid'],
            'aboutid' => 0,
            'sn' => $info['sn'],
            'nums' => $info["nums"],
            'after' => 0,
            'uafter' => $user_info["stock"],
            'time' => time(),
            'type' => 23, //提货记录
        ];
        addAcc_nums($add_nums);
        return get_op_put(1, "发货成功",$url);
    }

    /**
     * 导出数据
     * @param type $model
     * @param type $type
     */
    public function orderexport($model, $type = 10) {
        $excel = D("Backet/Excel", "Logic");
        $excel->runOn($model, $type);
    }

    /**
     *获取物流发货信息
     */
    public function transinfo(){
        if(session('admin_info')['groups'] =='15' or session('admin_info')['groups'] =='0') {
            $order_drs = M('order_drs');
            $where['nums'] = array('gt', 19);
            $where['have_pay'] =1;
            $list = poPage($order_drs, $where, "id desc");
            $this->assign("list", $list);
            $this->display();
        }else{
            $this->error('访问出错');
        }
    }


    /**
     *物流费
     */
    public function order_srpay(){
        $get = I('get.');
        $order_drs = M('order_drs');
        #时间
        if($get['start']||$get["ends"]){
            $start = strtotime($get["start"]);
            $ends = strtotime($get["ends"]);
            if(!$ends){
                $where["times"] = array("gt", $start);
            }if(!$start){
                $where["times"] = array("lt", $ends);
            }
            if ($ends > $start) {
                $where["times"] = array(array("gt", $start), array("lt", $ends));
            }
            if ($ends == $start){
                $where["times"] = array(array("gt", $start), array("lt", $ends+86400));
            }
        }
        $where['have_pay'] =1;
        $data =poPage($order_drs,$where);
        $this->assign('list',$data);
        $this->assign('start',$get['start']);
        $this->assign('ends',$get['ends']);
        $this->display();
    }

}
