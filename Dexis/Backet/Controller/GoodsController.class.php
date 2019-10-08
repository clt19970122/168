<?php

namespace Backet\Controller;
use Backet\Model\GoodsModel;
#use Think\Controller;

use Think\Db;

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
        $type != 10 ? $where["status"] = $type : null;//状态
        $get["sn"] != "" ? $where["sn"] = array("like", "%" . $get["sn"] . "%") : null; //订单号
        /*$get["goods"] != "" ? $where["gid"] =$get["goods"] : null;*/
        //$get["name"] != "" ? $where["name"] = array("like", "%" . $get["name"] . "%") : null;
        $get["phone"] != "" ? $where["phone"] = array("like", "%" . $get["phone"] . "%") : null;// 电话
        $get["status"] != "" ? $where["status"] = $get["status"] : null;// 状态
        $get["paytype"] != "" ? $where["paytype"] = $get["paytype"] : null; //支付类型
        $get["buy_level"] != "" ? $where["buy_level"] = $get["buy_level"] : null; //等级
        //根据不同的id获取商品信息
        if($get['gid']) {
            $where["gid"] = $get["gid"];
        }
        //获取货物
        $wheres["ind"] = 1;
        $wheres["status"] = 1;
        $goods = M('goods')->where($wheres)->select();

        # 获取等级
        $level =M('acc_level')->field('id,name')->select();
        #
        $list = poPage($orders, $where, "id desc");
        $this->assign("list", $list);
        $this->assign("level", $level);
        $this->assign("goods", $goods);
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
     *订单退款
     */
    public function DoReturnByOrder(){
        $post=I('sn');
        $orders = M("orders");
        $acc_record =M('acc_record');
        $acc_level =M('acc_level');
        $acc_money = M("acc_money");
        $acc_nums = M("acc_nums");
        $acc_points = M("acc_points");
        $account = M("account");
        $post=$orders->where(['id'=>$post])->find();
        $post=$post['sn'];
        //$post = '415541701877094284';
        $where["sn"] = $post;
        $order_info = $orders->where($where)->find();//订单信息
        $user_info = $account->where(['id'=>$order_info['uid']])->find();//用户信息
        $number = $acc_nums->where($where)->find();//库存加减信息
        //判断用户云库存是否满足退单
        if($user_info['stock']<$order_info['gnums']){
            return get_op_put(0, "退单失败用户云库存不足");
        }
        $where1["sn"] = $post;
        $where1["models"] = 'ORDER';
        $where2["models"] = 'REBACK';
        $user_money = $acc_money->where($where1)->find();//用户支付

        $reback_money = $acc_money->where($where)->where($where2)->select();//用户返利
        //判断返利人和出货人金额够不够退
        if($reback_money!=null){
            foreach ($reback_money as $k=>$v){
                $reback_user_money = $account->where(['id'=>$v['uid']])->find();//返利用户
                if($reback_user_money['money']<$v['money']){
                    return get_op_put(0, "退单失败".$reback_user_money['name']."余额不足");
                }
            }
        }
        //下面是退单逻辑
        $reback['uid']=$user_money['uid'];
        $reback['money']=$user_money['money'];
        $reback['sn']=$user_money['sn'];
        $reback['befores']=getUserInf($user_money['uid'],'money');
        $reback['afters']=$reback['befores']+$user_money['money'];
        $reback['type']=1;
        $reback['models']='REBACK';
        $reback['times']=time();
        $order_list=$acc_money->add($reback);//返下单用户钱
        if($order_list){
            //积分信息录入
            $points['sn']=$user_money['sn'];
            $points['uid']=$user_money['uid'];
            $points['befores']=getUserInf($user_money['uid'],'points');
            $points['afters']=getUserInf($user_money['uid'],'points')-$number['nums'];
            $points['point']=$number['nums'];
            $points['times']=time();
            $acc_points->add($points);

            $datas["money"]=$reback['afters'];
            $datas["stock"]=$user_info['stock']-$number['nums'];//减库存
            $datas["points"]=$user_info['points']-$number['nums']; //减积分
            //用户等级
            $map['months'] = array('elt',$datas["points"]);
            $level =$acc_level->where($map)->order('months desc')->find();
            if($level['id']!=$user_info['level']){
                $datas["level"]=$level['id'];
                $list['uid']=$user_money['uid'];
                $list['dotype']='用户降级';
                $list['before']=$user_info['level'];
                $list['after']=$level['id'];
                $list['level_num']=$level['months'];
                $list['time']=time();
                $acc_record->add($list);
            }
            $account->where(['id'=>$user_money['uid']])->save($datas);
        }
        $data2["stock"]= array("exp", "stock+" . $number['nums']);
        $account->where(['id'=>$number['aboutid']])->save($data2);
        //返利人和出货人扣钱
        if($reback_money!=null){
            foreach ($reback_money as $k=>$v){
                $update['uid']=$v['uid'];
                $update['money']=$v['money'];
                $update['models']='RETURN';
                $update['sn']=$v['sn'];
                $update['type']=0;
                $update['befores']=getUserInf($v['uid'],'money');
                $update['models']='RETURN';
                $update['afters']=$update['befores']-$v['money'];
                $update['times']=time();
                $acc_money->add($update);
                $data["money"]= array("exp", "money-" . $v['money']);
                $reback_user_money = $account->where(['id'=>$v['uid']])->save($data);//用户退钱
            }
        }
        //数量变化数据添加
        $nums['aboutid']=$number['uid'];
        $nums['uid']=$number['aboutid'];
        $nums['sn']=$number['sn'];
        $nums['uafter']=getUserInf($number['uid'],'stock');
        $nums['after']=getUserInf($number['aboutid'],'stock');
        $nums['nums']=$number['nums'];
        $nums['type']=12;
        $nums['time']=time();
        $res=$acc_nums->add($nums);
        //更变状态
        $orders->where(['sn'=>$post])->save(['status'=>6]);
        return get_op_put(1, "退单成功");
        //已通过的
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
            $where_acc['name']=array("like", "%" .$username . "%");
            $acc_info=M('account')->where($where_acc)->select();
            if($acc_info!=null){
                foreach ($acc_info as $v){
                    $uid[]=$v['id'];
                }
                $where['uid']=array('in',implode(',',$uid));
            }
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
                //自提的话 公司出货
                AddStockAndSale($info['uid'],$info['gid'],0,$info["gnums"]);
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

        //物流发货 仓库
        AddStockAndSale($info['uid'],$info['gid'],1,$info["gnums"]);
        return get_op_put(1, "发货成功", U('Goods/orderinfo', array("id" => $info["id"])));
    }

    /**
     * 订单提货
     * @param type $type
     */
    public function order_sr($type = 10) {
//        addField('order_drs','sure','int(2) DEFAULT 0');
        $order_drs = M("order_drs");
//        $orders = M("orders");
        $account = M("account");
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
        if($get['dotype'] ==1){
            $where['nums'] =array('lt',20);
            $where['have_pay'] =array('neq',3);
        }elseif($get['dotype'] ==2){
            $where['nums'] =array('egt',20);
            $where['have_pay'] =array('neq',3);
        }elseif($get['dotype'] ==3){
            $where['have_pay'] =array('eq',3);
        }
        //是否支付
//        $where['have_pay'] =1;
        #
        $list = poPage($order_drs, $where, "id desc");
        foreach ($list['list'] as $k =>$v){
//            $start =strtotime('-7 hours',strtotime(date('Y-m-d')));
//            $end =strtotime('+17 hours',strtotime(date('Y-m-d')));
//            $where['times'] =array('gt','1537833600');
            /*$where['uid'] =$v['uid'];
            $where['addr'] =$v['addr'];
            $where['status'] =1;
            $where['times'] =array('between',$start,$end);
            //查询相同地区的单号
            $sameplace=$orders->where($where)->select();*/
//            $sameplace=getsameAddress($v['uid'],$v['addr']);
//            if ($sameplace){
//                $arr_sn=array();
//                foreach ($sameplace as $key =>$val){
//                    $arr_sn[] =$val['sn'];
//                }
//                $list['list'][$k]['same'] ='周边订单-'.implode('<br/>',$arr_sn);
//            }
           /* $samesend =$order_drs->where($where)->select();
            if ($samesend){
                $arr_send=array();
                $arr_num=array();
                foreach ($samesend as $key =>$val){
                    $arr_send[] =$val['sn'];
                    $arr_num[] =$val['nums'];
                }
                $list['list'][$k]['same_pro'] ='168提货订单-'.implode('<br/>',$arr_send) .',总需加'.array_sum($arr_num).'盒';
            }*/
            //
            /*判断自提时间>申请时间的2天
             * 自提进入判断
             * 2018-11-1 16:29:27 add
             * */
           if($v['have_pay']==3&&$v['sure']==0){
               $time  =strtotime('-2days');
               if($v['times']<$time){
                    $save =[
                        'remakes'=>'温馨提示：该自提请求已超过提货时间两天，已被取消',
                        'status'=>5,//自提取消
                        'sure'=>3,
                    ];
                   $res =$order_drs->where(['id'=>$v['id']])->save($save);
                   //如果处理成功  用户添加库存
                   if($res){
                        $account->where(['id' => $v['uid']])->setInc('stock',$v['nums']);
                       $user_info =$account->where(['id' => $v['uid']])->find();
                       $add_nums = [
                           'uid' => 0,
                           'aboutid' => $v['uid'],
                           'sn' => $v['sn'],
                           'nums' => $v["nums"],
                           'after' => $user_info["stock"],
                           'uafter' => 0,
                           'time' => time(),
                           'type' => 21, //提货记录
                       ];
                       addAcc_nums($add_nums);
                   }
               }
           }
        }

        //统计发货出货
        $where["status"] =3;
        $where["have_pay"] =1;
        $buy_outNums =$order_drs->where($where)->sum('nums');
        $where["status"] =4;
        $where["have_pay"] =3;
        $where["sure"] =1;
        $user_self =$order_drs->where($where)->sum('nums');
        $this->assign("list", $list);
        $this->assign("self_out", $user_self);
        $this->assign("buy_out", $buy_outNums);
        $this->assign("type", $type);
        $this->assign("dotype", $get['dotype']);
        $this->assign("page", I("get.p"));
        $this->assign("name", I("get.name"));
        $this->assign("phone", I("get.phone"));
        $this->assign("start", I("get.start"));
        $this->assign("ends", I("get.ends"));
        $this->assign("username",$username);
        $this->assign("trans", $systrans->where("status=1")->select());
        $this->display();
    }

    /**
     *自提确认发货 2018-9-29 17:02:07
     */
    public function suresend(){
        $id =I("id");
        $sure =I("sure");

        $account = M("account");
        $order_drs = M("order_drs");
        //订单信息
        $data_info =$order_drs->where(['id'=>$id])->find();
        $res =$order_drs->where(['id'=>$id])->save(['sure'=>$sure]);
        if($res){
            //取消发货
            if($sure ==3){
                $order_drs->where(['id'=>$id])->save(['status'=>5]);
                if($res){
                    $account->where(['id' => $data_info['uid']])->setInc('stock',$data_info['nums']);//返回盒数
                    $user_info =$account->where(['id' => $data_info['uid']])->find();
                    $add_nums = [
                        'uid' => 0,
                        'aboutid' =>$data_info['uid'],
                        'sn' => $data_info['sn'],
                        'nums' => $data_info["nums"],
                        'after' => $user_info["stock"],
                        'uafter' => 0,
                        'time' => time(),
                        'type' => 21, //提货记录
                    ];
                    addAcc_nums($add_nums);
                }
            }else {
                //确认提货后扣除库存
                $res = AddStockAndSale($data_info['uid'], 8, 0, $data_info['nums']);
            }
            return get_op_put(1, null, U("Goods/order_sr"));
        }else{
            return get_op_put(0, '网络错误');

        }
    }
    /**
     * 处理订单提货
     */
    public function orderSrOption(){
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
     *取消订单
     */
    public function calloffOrder(){
        $order_drs = M("order_drs");
        $account = M("account");
        $draw_code = M('money_code');

        $post = I("post.");
        $where["id"] = $post["id"];
        $info = $order_drs->where($where)->find();

        //已通过的
        if($info['status']==1){
            $status =$post['status'];
            //取消订单
            $res =$order_drs->where($where)->save(['status'=>$status]);
            if($res){
                $user_info =$account->where(['id'=>$info['uid']])->find();
                if($user_info) {

                    $reback_num = $account->where(['id' => $info['uid']])->setInc('stock', $info['nums']);
                    if($info['trans_pay']>0) {
                        $pay = new PayController();
                        $pay_res = $pay->pay($user_info['openid'], $info['sn'], $info['trans_pay'], '取消提货支付退款到零钱');
                        if ($pay_res['return_code'] == 'SUCCESS' && $pay_res['result_code'] == 'SUCCESS') {
                            $add = [
                                'uid' => $info['uid'],
                                'sn' => $info['sn'],
                                'drop_id' => $info['sn'],
                                'type' => 1,
                                'money' => $info['trans_pay'],
                                'wc_sn' => $pay_res['payment_no'],
                                'time' => time(),
                                'status' => 1,
                            ];
                            $draw_code->add($add);
                        }
                    }
                    return get_op_put(1, '取消成功', U("Goods/order_sr"));
                }
            }
        }
        return get_op_put(0, '处理失败');
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
//        $verify = D("Backet/Verif", "Logic");
//        $res =$verify->TransendSms($info['phone'],$info['name'],$tran_info['name'],$post["trsn"]);



        //扣除库存###添加记录---- 2018-9-6 17:30:59 add
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
        //扣除公司的库存
        $res =AddStockAndSale($info['uid'],8,1,$info["nums"]);
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
        $goods =M('goods')->where(['gsn'=>'415244730815416283'])->find();
        $this->assign('list',$data);
        $this->assign('goods',$goods);
        $this->assign('start',$get['start']);
        $this->assign('ends',$get['ends']);
        $this->display();
    }

    /**
     *获取订单的详情
     */
    public function getOrdersInfo(){
        $sn =I('sn');
        $id =I('id');

        $orders=M('orders');
        $order_drs=M('order_drs');
        $acc_money=M('acc_money');
        $acc_num=M('acc_nums');
        $where['sn']=$sn;

        $nums_info =$acc_num->where($where)->find();
        $res_data['nickname'] =getUserInf($nums_info['uid'],'nickname');
        $res_data['upname'] =getUserInf($nums_info['aboutid'],'nickname');
        if(substr($sn,0,5)=='2018A'){
            $res_data['order_type'] ='后台调整';
//            $res_data['username'] =getUserInf($order_info['uid'],'nickname');
            $res_data['nums'] =$nums_info['nums'];
            $this->ajaxReturn( array('status'=>true,'msg'=>'','data'=>$res_data));
        }
        //订单
        $order_info=$orders->where($where)->find();
        if($order_info){
            $where['uid']=$id;
            $money_info =$acc_money->where($where)->find();
            if($money_info &&$money_info['models']=='ORDER'){
                $res_data['money'] ='-'.$money_info['money'];
            }else{
                $res_data['money'] ='+'.$money_info['money'];
            }

            $res_data['order_type'] ='系统订单';
//            $res_data['username'] =getUserInf($order_info['uid'],'nickname');
            $res_data['nums'] =$order_info['gnums'];
            $this->ajaxReturn( array('status'=>true,'msg'=>'','data'=>$res_data));
        }
        //提货
        $drs_info=$order_drs->where($where)->find();
        if($drs_info){
            $res_data['order_type'] ='提货订单';
//            $res_data['username'] =getUserInf($drs_info['uid'],'nickname');
            $res_data['money'] =0;
            $res_data['nums'] =$drs_info['nums'];
            $this->ajaxReturn( array('status'=>true,'msg'=>'','data'=>$res_data));
        }
        //金融
        $sr_info =M('order_sr')->where($where)->find();
        if($sr_info){
            $res_data['order_type'] ='金融订单';
//            $res_data['username'] =getUserInf($sr_info['uid'],'nickname');
            $res_data['money'] =$sr_info['money'];
            $res_data['nums'] =$sr_info['money']==3000?40:($sr_info['money']==1200?12:($sr_info['money']==480?4:1));
            $this->ajaxReturn( array('status'=>true,'msg'=>'','data'=>$res_data));
        }
    }

    /**
     *微信订单的退单处理
     */
    /*public function  doReturnOrder(){
        $order_id =I('id');
        $order=M('orders');
        $acc_money =M('acc_money');
        $acc_num =M('acc_nums');
        $account =M('account');
        $info =$order->where(['id'=>$order_id,'status'=>1])->find();
        if($info){
            $sn =$info['sn'];
            //查询金额记录
            $money_info =$acc_money->where(['sn'=>$sn])->select();
            //查询数量记录
            $nums_data =$acc_num->where(['sn'=>$sn])->find();
            foreach($money_info as $k=>$v){
                $user_info =$account->where(['id'=>$v['uid']])->find();
                //获取到下单人
                if($v['models'] == 'ORDER'){

                }else{
                    //获取用户信息
                    if($user_info){
                        //出货人
                        if($nums_data['aboutid'] == $v['uid']){
                            $save['stock'] =array('exp','');
                        }
                        $save['money']=$v['money'];
                        $account->where(['id'=>$v['id']])->save($save);
                    }
                }
            }
            $about_uid =array_unique(array_column($money_info,'uid'));
        }
    }*/
  //转货数据导出和查询
    public function cart(){
        if(IS_AJAX){
            $account = M("account");
            $id=I('status');
            $uid =  $account->where(['sysid'=>$id])->find();
            $categoryModel=new GoodsModel();
            $rows = $categoryModel->getList('0',$uid['id']);
            $datas=[];
            foreach ($rows as $k=>$v){
                $datas[$k]['name'] =getUserInf($v['uid'],'name');
                $datas[$k]['sn'] =$v['sn'];
                $datas[$k]['aboutname'] =getUserInf($v['aboutid'],'name');
                $datas[$k]['uname'] =getUserInf($v['aboutid'],'nickname');
                $datas[$k]['nums'] =$v['nums'];
                $datas[$k]['time'] =date('Y-m-d H:i:s',$v['time']);
            }
            $this->ajaxReturn(['data'=>$datas]);exit;
        }
        $this->display();
    }
}
