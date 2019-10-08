<?php

/**
 * 短信验证码系统数据库
 * id
 * phone    手机号码
 * code     验证码
 * seconds  有效秒数
 * expired  过期时间UNIX
 * status   状态
 * times    获取时间UNIX
 *
 * 102 请等待对应时间后再次获取
 * 502 验证码服务器发送失败
 * 503 保存验证码失败
 * 200 验证码处理成功
 */

namespace Home\Logic;

class VerifLogic {

    const smsUrl = "http://106.ihuyi.cn/webservice/sms.php?method=Submit";
    const timeout = 120;

    //系统参数
    private $account;
    private $password;
    //变量参数
    private $mobile;

    /**
     * 初始化参数
     */
    public function __construct() {
        $sysconfig = C("SMS_CONF");
        $this->account = $sysconfig["ACCOUNT"];
        $this->password = $sysconfig["PASSWD"];
    }

    /**
     * 获取验证码
     */
    public function getCode($mobile) {
        $this->mobile = $mobile;
        return $this->checkCodeEsit();
    }

    /**
     * 检测验证码是否存在
     */
    private function checkCodeEsit() {
        $code = $this->getVerifiCode();
        if ($code != null) {
            return 102;
        }
        return $this->getNewCode();
    }

    /**
     * 获取新验证码
     */
    private function getNewCode() {
        $code = mt_rand(0, 9) . mt_rand(0, 9) . mt_rand(0, 9) . mt_rand(0, 9);
        $code .= mt_rand(0, 9) . mt_rand(0, 9);
        if (!$this->sentSms($code)) {
            return 502;
        }
        $data["phone"] = $this->mobile;
        $data["code"] = $code;
        $data["seconds"] = self::timeout;
        $data["times"] = time();
        $data["expired"] = time() + self::timeout;
        $data["status"] = 0;
        if (!M("accode")->add($data)) {
            return 503;
        }
        return 200;
    }

    /**
     * 校验验证码
     * @param type $mobile
     * @param type $code
     */
    public function checkCode($mobile, $code) {
        $this->mobile = $mobile;
        $info = $this->getVerifiCode($code);
        if ($info == null) {
            return 404;
        }
        return $this->updateCodeSt($code);
    }

    /**
     * 更新验证码状态
     * @param type $code
     */
    private function updateCodeSt($code) {
        $accode = M("accode");

        $where["phone"] = $this->mobile;
        $where["code"] = $code;
        $where["expired"] = array("gt", time());
        $where["status"] = 0;
        $save["status"] = 1;
        if (!$accode->where($where)->save($save)) {
            return 503;
        }
        return 200;
    }

    #############公共方法############

    /**
     * 获取已有验证码
     * @param type $code
     */
    private function getVerifiCode($code = null) {
        $accode = M("accode");

        $where["phone"] = $this->mobile;
        $code != null ? $where["code"] = $code : null;
        $where["expired"] = array("gt", time());
        $where["status"] = 0;
        $info = $accode->where($where)->order("id desc")->find();
        return $info;
    }

    /**
     * 发送验证码
     * @param type $code
     * @return boolean
     */
    private function sentSms($code) {
        $put = "account=" . $this->account . "&password=" . $this->password . "&mobile=" . $this->mobile;
        $put .= "&content=" . rawurlencode("您的验证码是：" . $code . "。请不要把验证码泄露给其他人。");
        $tmp = poCurl(self::smsUrl, $put);
        $res = xml_to_array($tmp);
        if ($res['SubmitResult']['code'] == 2) {
            return true;
        }
        #
        $syslog = M("syslog");
        $syslog->name = "SMSRETURN";
        $syslog->send = $put;
        $syslog->back = $tmp;
        $syslog->times = date("Y-m-d H:i:s");
        $syslog->add();
        #
        return false;
    }

    /**发货发送短信
     * @param $phone
     * @param $user
     * @param $tran_com
     * @param $tran_id
     * @return bool
     */
    public function TransendSms($phone, $user, $tran_com, $tran_id){
        $put = "account=" . $this->account . "&password=" . $this->password . "&mobile=" .$phone;
        $put .= "&content=" . rawurlencode("亲爱的".$user."，您好！您的货物已通过".$tran_com."发货，快递单号是：".$tran_id."。请近几天保持电话畅通，以备快递人员及时联系。");
        $tmp = poCurl(self::smsUrl, $put);
        $res = xml_to_array($tmp);
        if ($res['SubmitResult']['code'] == 2) {
            return true;
        }
        $syslog = M("syslog");
        $syslog->name = "SMSRETURN";
        $syslog->send = $put;
        $syslog->back = $tmp;
        $syslog->times = date("Y-m-d H:i:s");
        $syslog->add();
        #
        return false;
    }

    /**0元计划
     * @param $phone
     * @param $user
     * @param $tran_com
     * @param $tran_id
     * @return bool
     */
    public function PlanSms($phone, $plan, $moneypay, $time,$refund){
        $put = "account=" . $this->account . "&password=" . $this->password . "&mobile=" .$phone;
        $put .= "&content=" . rawurlencode("您好，您申请的".$plan."，产品总价".$moneypay."于".$time."申请成功，请于".$refund."前登陆系统进行结算，感谢您的支持。");
        $tmp = poCurl(self::smsUrl, $put);
        $res = xml_to_array($tmp);
        if ($res['SubmitResult']['code'] == 2) {
            return true;
        }
        $syslog = M("syslog");
        $syslog->name = "PSMSRETURN";
        $syslog->send = $put;
        $syslog->back = $tmp;
        $syslog->times = date("Y-m-d H:i:s");
        $syslog->add();
        #
        return false;
    }
}
