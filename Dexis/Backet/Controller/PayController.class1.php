<?php

namespace Backet\Controller;

#use Think\Controller;

class PayController extends CommController {

    public $appid;
    public $mchid;
    public $paykey;

    public function __construct()
    {
        $this->appid=C("WEC_CONFIG.APPID");
        $this->mchid=C("WEC_CONFIG.MCHID");
        $this->paykey=C("WEC_CONFIG.PAYKEY");
    }

    //处理支付的逻辑
    public function  doPayLogic(){
        $get =I('post.');
        $pay_pwd =$get['pwd'];
        $sn ='P'.getOrdSn();
        //验证支付密码
        if($pay_pwd==='168tkss'){
            //查询提款相关信息
            $money_draw = M('money_draw');
            $draw_code = M('money_code');
            $where['id'] = $get['id'];
            $where['status'] =0;
            $remark = '公司付款到个人零钱';
            $draw_info = $money_draw->where($where)->find(); 
            //订单存在且为未支付
            if($draw_info){
                //付款到微信 1 是付款到微信  2是到银行卡
                if($get['type']==1){
                    $pay_res = $this->pay($draw_info['wechat'], $sn ,$draw_info['money'], $remark);
                }else{//付款到银行卡
                    $pay_res = $this->pay_bank($sn, $draw_info['bankcode'], $draw_info['money'], $draw_info,$draw_info['money']);
                }
                //支付成功返回逻辑
                if ($pay_res['return_code'] == 'SUCCESS' && $pay_res['result_code'] == 'SUCCESS') {
                    $save['status'] = 1;
                    $res_data = $money_draw->where($where)->save($save);
                    if ($res_data) {
                        $add = [
                            'uid' => $draw_info['usid'],
                            'sn' => $sn,
                            'drop_id' => $draw_info['sn'],
                            'type' => $get['type'],
                            'money' => $draw_info['money'],
                            'wc_sn' => $pay_res['payment_no'],
                            'time' => time(),
                            'status' => 1,
                        ];
                        $draw_code->add($add);

                        //发送模板消息
                        $user_info =M('account')->where(['id'=>$draw_info['usid']])->find();

                        $wctemp = D("Home/Wctemp", "Logic");
                        $user = ["openid" => $user_info["openid"], "nickname" => $user_info["nickname"]];
                        $data = ["type" => "PayForUser", "money" => $draw_info["money"],"time"=>date('Y-m-d H:i:s'),'intype'=>'零钱','info'=>'系统提现到账','sn'=>$draw_info['sn']];
                        $wctemp->entrance($user, $data);

                        return get_op_put(1, '打款成功');
                    }
                }else {
                    $add=[
                        'uid'=>$draw_info['usid'],
                        'sn'=>$sn,
                        'drop_id'=>$draw_info['sn'],
                        'type'=>$get['type'],
                        'money'=>$draw_info['money'],
                        'msg'=>$pay_res['return_msg'],
                        'time'=>time(),
                    ];
                    $draw_code->add($add);
                    $msg ='打款失败' . $pay_res['return_msg'];
                    return get_op_put(0, $msg);
                }
            } else{
                return get_op_put(0, '订单查询错误【errorOrder】', U('Fina/index'));
            }
        }else{
            return get_op_put(0, '支付密码错误', U('Fina/index'));
        }
    }
    /**
     * 支付方法
     * @param $openid
     * @param $trade_no
     * @param $money
     * @param $desc
     * @return array
     */
    public function pay($openid, $trade_no, $money, $desc){
//    public function pay(){
//        $params["mch_appid"]= C("WEC_CONFIG.APPID");
//        $params["mchid"] =C("WEC_CONFIG.MCHID");
        $params["mch_appid"]=$this->appid;
        $params["mchid"] =$this->mchid;
        $params["nonce_str"]= 'suiji'.mt_rand(100,999);
        $params["partner_trade_no"] = $trade_no; //商户订单号
        $params["amount"]= $money*100;//金额 单位是分
        $params["desc"]=$desc;//企业付款备注
        $params["openid"]= $openid;
        $params["check_name"]= 'NO_CHECK'; //校验用户姓名
        $params['spbill_create_ip'] = $_SERVER['SERVER_ADDR'];

        //支付秘钥
//        $paykey =C("WEC_CONFIG.PAYKEY");
        $paykey =$this->paykey;
        //生成签名
        $str = 'amount='.$params["amount"].'&check_name='.$params["check_name"].'&desc='.$params["desc"].'&mch_appid='.$params["mch_appid"].'&mchid='.$params["mchid"].'&nonce_str='.$params["nonce_str"].'&openid='.$params["openid"].'&partner_trade_no='.$params["partner_trade_no"].'&spbill_create_ip='.$params['spbill_create_ip'].'&key='.$paykey;

        //md5加密 转换成大写
        $sign = strtoupper(md5($str));
        //生成签名
        $params['sign'] = $sign;
        //构造XML数据
        $xmldata = $this->array_to_xml($params); //数组转XML
        $url='https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';
        //发送post请求
        $res = wcPoCurl($url, $xmldata); //curl请求
        if(!$res){
            return array('status'=>1,
                'msg'=>"服务器连接失败" );
        }
        //付款结果分析
        $content = $this->xml_to_array($res); //xml转数组
        return $content;
    }

    /** 付款到银行卡
     * @param $trade_no @订单号
     * @param $bank_no @卡号
     * @param $uname @用户名
     * @param $bank_code @银行序号
     * @param $money @金额
     * @return array|mixed
     */
    public function pay_bank($trade_no, $bank_no, $uname, $bank_code, $money){
        $params["mch_id"]= $this->mchid;
        $params["partner_trade_no"] =$trade_no;//商户订单号，需保持唯一
        $params["nonce_str"]= 'suiji'.mt_rand(100,999);
        $params["enc_bank_no"] = $bank_no; //卡号
        $params["enc_true_name"]=$uname;//收款方用户名
        $params["bank_code"]=$bank_code;//银行的编号
        $params["amount"]= $money*100;//金额 单位是分
        $params["desc"]= '企业付款到银行卡'; //付款备注
        //支付秘钥
        $paykey =$this->paykey;
        //生成签名
        $str = 'amount='.$params["amount"].
            '&bank_code='.$params["bank_code"].
            '&desc='.$params["desc"].
            '&enc_bank_no='.$params["enc_bank_no"].
            '&enc_true_name='.$params["enc_true_name"].
            '&mch_id='.$params["mch_id"].
            '&nonce_str='.$params["nonce_str"].
            '&partner_trade_no='.$params["partner_trade_no"].
            '&key='.$paykey;

        //md5加密 转换成大写
        $sign = strtoupper(md5($str));
        //生成签名
        $params['sign'] = $sign;
        //构造XML数据
        $xmldata = $this->array_to_xml($params); //数组转XML
        $url ='https://api.mch.weixin.qq.com/mmpaysptrans/pay_bank';
        //发送post请求
        $res = wcPoCurl($url, $xmldata); //curl请求
        var_dump($res);
        if(!$res){
            return array('status'=>1,
                'msg'=>"服务器连接失败" );
        }
        //付款结果分析
        $content = $this->xml_to_array($res); //xml转数组
        var_dump($res);
        return $content;
    }

    public function getRsaData(){

//        $params["mch_appid"]=$this->appid;
//        $params["mchid"] =$this->mchid;
        $nonce_str= 'suiji'.mt_rand(100,999);
        //生成签名
        $str = "mch_id=".$this->mchid ."&nonce_str=".$nonce_str."&sign_type=MD5&key=".$this->paykey;
        //md5加密 转换成大写
        $sign = strtoupper(md5($str));
        //发送post请求
        $params=[
            'mch_id'=>$this->mchid,
            'nonce_str'=>$nonce_str,
            'sign'=>$sign,
            'sign_type'=>'MD5',
        ];
        $xmldata = $this->array_to_xml($params); //数组转XML
        $url='https://fraud.mch.weixin.qq.com/risk/getpublickey';
        $res = wcPoCurl($url, $xmldata); //curl请求
        $content = $this->xml_to_array($res); //xml转数组
        var_dump($content);
    }

    ////////////////////////////////////////////////////////////////////////
    /**
     * array 转 xml
     * 用于生成签名
     */
    public function array_to_xml($arr){
        $xml = "<xml>";
        foreach ($arr as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" .$key.">".$val."</".$key.">";
            } else
                $xml .= "<".$key."><![CDATA[".$val."]]></".$key.">";
        }
        $xml .= "</xml>";
        return $xml;
    }

    /**
     * xml 转化为array
     */
    public function xml_to_array($xml){
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $values;
    }
}