<?php

namespace Home\Controller;

#use Think\Controller;

class LoginController extends CommController {

    public function _initialize() {
        parent::_initialize();
        layout("laynot");
    }

    public function index($recid = null) {
        
        if(strpos($recid,".")==true){
            $recid =substr($recid,0,strpos($recid,"."));
        }
        session("bang_recid", $recid);
        $account=M('account');
        //查询用户存在和是否验证用户信息
        $where["openid"] = $this->wecs["openid"];
        $info = $account->where($where)->find();
        //获取用户微信号是否登陆
        if($info){
            $this->display();
        }else{
            $this->display('setname');
        }
       /* $ssid = session("ssid_dexis");
        if ($ssid != null) {
            $this->redirect("Index/index");
        }*/

    }

    /**
     * 登录
     */
    public function logon() {
        $account = M("account");
        #
        $post = I("post.");
        $post["passwd"] = md5($post["passwd"]);
        $info = $account->where($post)->find();
        if ($info == null) {
            return get_op_put(0, "用户名或密码错误");
        }
        #
        $count = countOrder($info["id"]);
        if ($info["recid"] == null || $info["recid"] == "0") {
            $count <= 0 ? $save["recid"] = session("bang_recid") : null;
        }
        $save["nickname"] = $this->wecs["nickname"];
        $save["headimgurl"] = $this->wecs["headimgurl"];
        #$save["openid"] = $this->wecs["openid"];
        $account->where($post)->save($save);
        #
        session("ssid_dexis", $info["id"]);
        return get_op_put(1, null, U("Index/index"));
    }

    /**
     * 微信登录
     */
    public function logWechat() {
        $account = M("account");
        #
        $where["openid"] = $this->wecs["openid"];
        $info = $account->where($where)->find();
        if($info['status']==0){
            return get_op_put(0, '账户异常，已被冻结');
        }
        if ($info != null) {
            //绑定姓名
            session("set_id", $info["id"]);
            //判断上级
            if ($info["recid"] != null && $info["recid"] != "0") {
//                session("set_id", $info["id"]);
                //是否设置名字
                $haveSetName=$this->havesetName($info["id"]);
                if($haveSetName){
                    session("ssid_dexis", $info["id"]);
                    $url ="Index/index";
                }else{
                    $url ='login/setname';
                }
                return get_op_put(1, null, U($url));
            }
            //统计订单
            /*$count = countOrder($info["id"]);
            if ($count > 0) {
                session("ssid_dexis", $info["id"]);
                return get_op_put(1, null, U("Index/index"));
            }*/

            $recid = session("bang_recid");
            if($recid){
                $save['recid'] =$recid;
                $account->where($where)->save($save);
            }else {
                //如果不存在上级的话跳转到上级绑定
                if ($info["recid"] == null || $info["recid"] == "0") {
                    $url = 'login/setup';
                    return get_op_put(1, null, U($url));
                }
            }

            //
//            session("ssid_dexis", $info["id"]);
            $haveSetName=$this->havesetName($info["id"]);
            if($haveSetName){
                session("ssid_dexis", $info["id"]);
                $url ="Index/index";
            }else{
                $url ='login/setname';
            }

            return get_op_put(1, null, U($url));
        }
        #
        $ssid = session("bang_recid");
        $where["sysid"] = uniqid(true);
        $ssid != null ? $where["recid"] = session("bang_recid") : null;
        $where["nickname"] = $this->wecs["nickname"];
        $where["headimgurl"] = $this->wecs["headimgurl"];
        $where["level"] = 0;
		$where["status"] = 1;
        $where["times"] = time();
        #
        $id = $account->add($where);
        if (!$id) {
            return get_op_put(0, "网络异常，请稍后再试");
        }
//        session("ssid_dexis", $id);
//是否认真姓名
        session("set_id",$id);
        //是否有上级
        if($ssid ==null){
            $url ='login/setup';
            return get_op_put(1, null, U($url));
        }

        $haveSetName=$this->havesetName($id);
        if($haveSetName){
            session("ssid_dexis", $id);
            $url ="Index/index";
        }else{
            $url ='login/setname';
        }

        return get_op_put(1, null, U($url));
//        return get_op_put(1, null, U("login/viphone"));
    }
    
    ////////////////////////////////////////////////////////////////////////////

    /**
     * 用户注册
     */
    public function regs() {
        $ssid = session("ssid_dexis");
        if ($ssid != null) {
            $this->redirect("Index/index");
        }
        //$recid = session("bang_recid");
        //session("bang_recid", $recid);
        //$this->assign("rec", $recid);
        $this->display();
    }

    /**
     * 注册短信
     */
    public function regs_sms() {
        $verify = D("Verif", "Logic");
        #
        $phone = I("post.phone");
        if (!preg_match("/1[1,2,3,4,5,6,7,8,9][0-9]\d{8}$/", $phone)) {
            return get_op_put(0, "手机号码格式错误");
        }
        $res = $verify->getCode($phone);
        if ($res != 200) {
            return get_op_put(0, "验证码获取失败");
        }
        return get_op_put(1, "验证码获取成功");
    }

    /**
     * 注册逻辑
     */
    public function regs_op() {
        $account = D("account");
        $verify = D("Verif", "Logic");
        #
        $post = I("post.");
        $res = $verify->checkCode($post["phone"], $post["code"]);
        if ($res != 200) {
            return get_op_put(0, "验证码错误");
        }
        if (!$account->create()) {
            return get_op_put(0, $account->getError());
        }
        $ssid = session("bang_recid");
        $ssid != null ? $account->recid = $ssid : null;
        $id = $account->add();
        if (!$id) {
            return get_op_put(0, "注册失败");
        }
        session("ssid_dexis", $id);
        return get_op_put(1, null, U("Login/index"));
    }

    ////////////////////////////////////////////////////////////////////////////

    /**
     * 忘记密码
     */
    public function forget() {
        $this->display();
    }

    /**
     * 忘记密码操作
     */
    public function forget_op() {
        $account = M("account");
        $verify = D("Verif", "Logic");
        #
        $post = I("post.");
        $res = $verify->checkCode($post["phone"], $post["code"]);
        if ($res != 200) {
            return get_op_put(0, "验证码错误");
        }
        $where["phone"] = $post["phone"];
        $count = $account->where($where)->count();
        if ($count <= 0) {
            return get_op_put(0, "账户不存在，请检查后再次尝试");
        }
        #
        $save["passwd"] = md5($post["pass"]);
        if (!$account->where($where)->save($save)) {
            return get_op_put(0, "系统错误，请稍后再试[0UP01]");
        }
        return get_op_put(1);
    }

    /**
     *系统实名认证
     */
    public function havesetName($id){
        $account =M('account');
        $acc_idauth =M('acc_idauth');
//        $ssid = session("ssid_dexis");
        $userinfo =$account->where(['id'=>$id])->find();
        if($userinfo['phone']==null ||$userinfo['phone']==' '){
           /* $idauth =$acc_idauth->where(['uid'=>$ssid])->find();
            if($idauth){
                $save['name']=$idauth['name'];
                $save['phone']=$idauth['phone'];
                $save['passwd']=md5($save['phone']);
                $res =$account->where(['id'=>$ssid])->save($save);
                if($res) {
                    return true;
                }else{
                    return false;
                }
            }else{
                return false;
            }*/
           return false;
        }
        return true;
    }

    /**
     *设置上级用户
     */
    public function doSetUp(){
        $data =I('post.');
        $id =session("set_id");
        $account =M('account');
        $res_info =$account->where(['id'=>$id])->find();
        if(!$res_info){
            return get_op_put(0,'网络错误，请稍后重试【error】');
        }

        if($res_info['recid'] !=null &&$res_info['recid'] !=0){
            return get_op_put(0,'上级信息无需重复绑定');
        }
        $data_info =$account->where(['sysid'=>$data['recid']])->find();
        if($data_info) {
            $res = $account->where(['id'=>$id])->save(['recid'=>$data['recid']]);
            if($res){//成功
                $have_set =$this->havesetName($id);
                if($have_set){//存在名字
                    session("ssid_dexis", $id);
                    return get_op_put(1,null,U('index/index'));
                }else{
                    return get_op_put(1,null,U('login/setname'));
                }
            }else{
                return get_op_put(0,'信息错误，请稍后重试【error】');
            }
        }else{
            return get_op_put(0,'网络信息错误，请稍后重试【error】');
        }

    }

    public function SetName(){
        $this->display();
    }
    /**
     *操作实名认证
     */
    public function doSetName(){
        $account = M("account");
        $verify = D("Verif", "Logic");
        #
        $post = I("post.");
        $name=trim($post['name']);
        //验证手机号
        $res_info =$account ->where(['phone'=>$post['phone']])->find();
        if($res_info){
            return get_op_put(0, "手机号已注册");
        }
        if($name== null){
            return get_op_put(0, "请输入真实姓名");
        }
        //验证码
        $res = $verify->checkCode($post["phone"], $post["code"]);
        if ($res != 200) {
            return get_op_put(0, "验证码错误");
        }
        $id =session('set_id');//用户是否登陆
//       $id ='';
        if($id) {
            $save['name'] = $post['name'];
            $save['phone'] = $post['phone'];
//        $save['passwd']=md5($post['phone']);
            $res = $account->where(['id' => $id])->save($save);
        }else{
            //录入信息
            $where["openid"] = $this->wecs["openid"];
            $where['name'] = $post['name'];
            $where['phone'] = $post['phone'];
            $ssid = session("bang_recid");
            $where["sysid"] = uniqid(true);
            $ssid != null ? $where["recid"] = session("bang_recid") : null;
            $where["nickname"] = $this->wecs["nickname"];
            $where["headimgurl"] = $this->wecs["headimgurl"];
            $where["level"] = 0;
            $where["status"] = 1;
            $where["times"] = time();
            #
            $id = $account->add($where);
        }
        if($res){
            session('ssid_dexis',$id);
            return get_op_put(1, null, U('index/index'));
        }
        return get_op_put(0,'网络错误');
    }

    /////////
    /**
     *根据系统id获取用户信息  08-21 add
     */
    public function  getuserbysys(){
        $id =I('srsid');
        $account =M('account');
        $user_info=$account->where(['sysid'=>$id])->find();
        $this->ajaxReturn($user_info);
    }

}
