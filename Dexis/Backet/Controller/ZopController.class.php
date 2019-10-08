<?php

namespace Backet\Controller;

#use Think\Controller;
include ("Zopsdk/ZopClient.php");
include ("Zopsdk/ZopProperties.php");
include ("Zopsdk/ZopRequest.php");
use zop\ZopClient;
use zop\ZopProperties;
use zop\ZopRequest;

class ZopController extends CommController {


    /**
     *无用
     */
    public function getTrans()
    {
// ZopProperties类的构造方法接收两个参数，分别是companyid和key，都需要注册中通开放平台后到个人中心查看
        $properties = new ZopProperties("kfpttestCode", "kfpttestkey==");
        $client = new ZopClient($properties);

        $request = new ZopRequest();
        $request->setUrl("http://58.40.16.120:9001/submitOrderCode");


        $request ->addParam("data",
            '{"content":
                {"branchId":"",
                    "buyer":"",
                    "collectMoneytype":"CNY",
                    "collectSum":"12.00",
                    "freight":"10.00",
                    "id":"xfs2018031500002222333",
                    "orderSum":"0.00",
                    "orderType":"1",
                    "otherCharges":"0.00",
                    "packCharges":"1.00",
                    "premium":"0.50",
                    "price":"126.50",
                    "quantity":"2",
                    "receiver":
                    {"address":"育德路XXX号",
                        "area":"501022",
                        "city":"四川省,XXX,XXXX",
                        "company":"XXXX有限公司",
                        "email":"yyj@abc.com",
                        "id":"130520142097",
                        "im":"yangyijia-abc",
                        "mobile":"136*****321",
                        "name":"XXX",
                        "phone":"010-222***89",
                        "zipCode":"610012"
                    },
                    "remark":"请勿摔货",
                    "seller":"",
                    "sender":
                    {"address":"华新镇华志路XXX号",
                        "area":"310118",
                        "city":"上海,上海市,青浦区"
                        ,"company":"XXXXX有限公司",
                        "email":"ll@abc.com",
                        "endTime":1369033200000,
                        "id":"131*****010",
                        "im":"1924656234",
                        "mobile":"1391***5678",
                        "name":"XXX",
                        "phone":"021-87***321",
                        "startTime":1369022400000,
                        "zipCode":"610012"
                    },
                    "size":"12,23,11",
                    "tradeId":"2701843",
                    "type":"1",
                    "typeId":"",
                    "weight":"0.753"
                },
                "datetime":"2018-10-30 12:00:00",
                "partner":"test",
                "verify":"ZTO123"
            }');

        echo $client->execute($request);
    }

    /**
     *无用
     */
    public function gettrans2(){
        $properties = new ZopProperties("ea8c719489de4ad0bf475477bad43dc6", "submitordertest==");
        $client = new ZopClient($properties);

        $request = new ZopRequest();
        $request->setUrl("http://58.40.16.120:9001/partnerInsertSubmitagent");
        //'receiver'==>收件人信息
//      $request ->addParam("data",'{"partner":"test","id":"xfs101100111011","tradeid":"1","sender":{"name":"xxx","mobile":"13666200691","company":"XXXXX有限公司","city":"上海,上海市,青浦区","address":"华新镇华志路XXX号"},"receiver":{"name":"xxx","mobile":"13666200691","city":"四川省,成都市,武侯区","address":"育德路497号"}}');
        $request ->addParam('data','{"partner":"test",
        "id":"xfs101100111011",
        "tradeid":"1",
        "sender":{"id":"131*****010"
        ,"name":"XXX",
        "company":"XXXXX有限公司",
        "mobile":"1391***5678",
        "phone":"021-87***321",
        "area":"310118",
        "city":"上海,上海市,青浦区",
        "address":"华新镇华志路XXX号",
        "zipcode":"610012",
        "email":"ll@abc.com",
        "im":"1924656234",
        "starttime":"2013-05-20 12:00:00",
        "endtime":"2013-05-20 15:00:00"},
        "receiver":{"id":"130520142097",
        "name":"XXX",
        "company":"XXXX有限公司",
        "mobile":"136*****321",
        "phone":"010-222***89",
        "area":"501022",
        "city":"四川省,XXX,XXXX",
        "address":"育德路XXX号",
        "zipcode":"610012",
        "email":"yyj@abc.com",
        "im":"yangyijia-abc"},
        "weight":"0.753",
        "size":"12,23,11",
        "quantity":"2",
        "price":"126.50",
        "freight":"10.00",
        "premium":"0.50",
        "pack_charges":"1.00",
        "other_charges":"0.00",
        "order_sum":"0.00",
        "collect_moneytype":"CNY",
        "collect_sum":"12.00",
        "remark":"请勿摔货",
        "order_type":"1"}');
        echo $client->execute($request);
    }

    /**调用中通接口
     * @param $url
     * @param $send
     * @param $msg_type
     * @return bool|string
     */
    public function code($url, $send, $msg_type){
//        $url = 'http://58.40.16.120:9001/partnerInsertSubmitagent';
//        $company_id = 'ea8c719489de4ad0bf475477bad43dc6';
//        $key = 'submitordertest==';
//        $url = 'http://japi.zto.cn/partnerInsertSubmitagent';
        $company_id = '878e5be655eb41a6bcfaf780fe97b0c9';
        $key = '366382f371f3';
        $data = array(
//            'data' => '{"partner":"878e5be655eb41a6bcfaf780fe97b0c9","id":"1234556","tradeid":"1","sender":{"name":"成都德熙爱汝实业集团股份有限公司","mobile":"13666200691","company":"XXXXX有限公司","city":"上海,上海市,青浦区","address":"华新镇华志路XXX号"},"receiver":{"name":"张","mobile":"13666200691","city":"四川省,成都市,武侯区","address":"育德路497号"}}',
            'data' =>$send,
            'company_id' => $company_id,
            'msg_type'=>$msg_type);
        $str_to_digest = "";
        foreach ($data as $k=>$v) {
            $str_to_digest =$str_to_digest.$k."=".$v."&";
        }
        $str_to_digest = substr($str_to_digest, 0, -1).$key;
        $data_digest = base64_encode(md5($str_to_digest,TRUE));
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded; charset=utf-8\r\n".
                    "x-companyid: ".$company_id."\r\n".
                    "x-datadigest: ".$data_digest."\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data)
            )
        );
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        return $result;
    }

    /**
     *获取订单详情
     */
    public function getinfo(){
        $orders=M('orders');
        $orders_trans=M('order_trans');
        $order_drs =M('order_drs');
        $id =I('id');
        $order_info =$order_drs->where(['id'=>$id])->find();

        $address =explode(',',$order_info['address']);
//        $getaddress =strstr(end($address),'县')==false?strstr(end($address),'区'):strstr(end($address),'县');
        $res_add =strstr($order_info['address'],end($address),true);

        $data =[
            'partner'=>'878e5be655eb41a6bcfaf780fe97b0c9',
            'id'=>$order_info['sn'],
            "tradeid"=>"1",
            "sender"=>[
                "name"=>"熊先生",
                "mobile"=>"15102806891",
                "city"=>"四川省,成都市,双流区",
                "address"=>"双华路三段458号",
                ],
            "receiver"=>[
                "name"=>$order_info['name'],
                "mobile"=>$order_info['phone'],
                "city"=>$res_add,
                "address"=>end($address),
                ],
            ];
        $send=json_encode($data);
        //获取订单号
        $url = 'http://japi.zto.cn/partnerInsertSubmitagent';
        $getcode =$this->code($url,$send,'submitAgent');//获取订单号
        $getdata =json_decode($getcode);
        if($getdata->result ==true){
            $code =$getdata->data->billCode;
            //获取成功-- 拿到订单号
            $url = 'http://japi.zto.cn/bagAddrMarkGetmark';
            if(count($address)<4){
                $city =$address[0];
                $district =$address[1];
                $addr =$address[2];
            }else{
                $city=$address[1];
                $district=$address[2];
                $addr=$address[3];
            }
//            $district=strstr(end($address),'县')==false?strstr(end($address),'区',true).'区':strstr(end($address),'县',true).'县';

            $data_head =[
                'unionCode'=>$code,
                'send_province'=>'四川省',
                'send_city'=>'成都市',
                'send_district'=>'双流区',
                'send_address'=>'双华路三段458号',
                'receive_province'=>$address[0],
                'receive_city'=>$city,
                'receive_district'=>$district,
                'receive_address'=>$addr,
            ];
            $data_head=json_encode($data_head);
            $header =$this->code($url,$data_head,'GETMARK');
            $gethead =json_decode($header);
//            var_dump($gethead);die;

//          var_dump($code);
            $save['trans'] =5;
            $save['trsn'] =$code;
            $save['sure'] =2;

            $drs_res =$order_drs->where(['id'=>$id])->save($save);
            if(!$drs_res){
                echo '失败';
                return false;
            }
            $start =strtotime('-7 hours',strtotime(date('Y-m-d')));
            $end =strtotime('+17 hours',strtotime(date('Y-m-d')));
            $wheres['uid'] =$order_info['uid'];
            $wheres['addr'] =$order_info['addr'];
            $wheres['status'] =1;
            $wheres['have_pay'] =1;
            $wheres['times'] =array('between',$start,$end);
            #
            //相同的提货订单
            $sameorder =$order_drs->where($wheres)->select();
//            var_dump($sameorder);
            foreach($sameorder as $k=>$v){
                $order_drs->where(['id'=>$v['id']])->save($save);
            }
            #
            //获取相同的其他的订单
            $sameplace =getsameAddress($order_info['uid'],$order_info['addr']);
            if ($sameplace){
                foreach ($sameplace as $key =>$val) {
                    $id[]=$val['id'];
                    $add['sn'] =$v['sn'];
                    $add['transid'] =$save['trans'];
                    $add['vosn'] =$code;
                    $sameadd =$orders_trans->add($add);
                }
            }
//            echo '成功';
//            return true;
            return get_op_put(1, null, U("Goods/order_sr"));
        }else{
//            echo '获取单号失败';
            return get_op_put(0, '网络错误');
        }
       /*$start =strtotime('-7 hours',strtotime(date('Y-m-d')));
        $end =strtotime('+17 hours',strtotime(date('Y-m-d')));
        $wheres['uid'] =$order_info['uid'];
        $wheres['addr'] =$order_info['addr'];
        $wheres['status'] =1;
        $wheres['gid'] =array('neq',8);
        $wheres['times'] =array('between',$start,$end);
        $orders->where($wheres)->select();*/

    }

    /**
     *生成条形码
     */
    public function barcode(){
        $this->display();
    }
}