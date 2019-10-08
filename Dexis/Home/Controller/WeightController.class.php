<?php
namespace Home\Controller;

use Think\Controller;

class WeightController extends Controller{
    public function index(){
        if(IS_AJAX){
            $data=I('post.');
            $json= json_encode($data);
           /* $json= json_decode($json);
            var_dump($json);exit;*/
            $put["count"] = $json;
            $put["time"] = time();
            $add=M('weight')->add($put);
            if($add){
                return get_op_put(1, "添加成功");
            }else{
                return get_op_put(0, "添加失败");
            }

            //var_dump($json,$i->age);exit;2019国际超模「线上减肥营」立下你的减肥军令状 让你健康瘦，轻松美 誓与肉肉抗战到底！
            //
            //让你健康瘦，轻松美
            //
        }else{
            $tocket = D("Home/Ticket", "Logic");
            $data["jsapi_ticket"] = $tocket->entrance();
            $data["noncestr"] = md5(mt_rand(0, 999));
            $data["timestamp"] = time();
            $data["url"] = get_url();
            ksort($data);
            $string = "";
            foreach ($data as $k => $v) {
                $string .= $k . "=" . $v . "&";
            }
            $string = trim($string, "&");
            $data["signature"] = sha1($string);
            $data["appid"] = C("WEC_CONFIG")["APPID"];
            $data["img"] = '/Public/home/imgs/wenda/title.jpg';
            layout("laynot");
            $this->assign("weburl", C("WEBURL"));
            $this->assign("ticket", $data);
            $this->display('Weight/weight');
        }

    }
    //{"age":"18\u4ee5\u4e0b","sex":"\u7537","height":"161~170cm","weight":"121\u65a4~130\u65a4","job":"\u957f\u65f6\u95f4\u7ad9\u7acb","breakfast":"8\u70b9\u4ee5\u540e","stomach":["\u4fbf\u79d8","\u60c5\u7eea\u4f4e\u843d",""],"problem":"","habit":"\u7ecf\u5e38\u5728\u5916\u5c31\u9910","taste":"\u65e0\u8fa3\u4e0d\u6b22","diet":"\u4e00\u5929\u4e00\u987f\u8089\u83dc","time":"19:31~20:30","sport":"\u4e00\u54683~4\u6b21","way":["\u8282\u98df","\u4ee3\u9910"],"effectLose":"","loseNum":"11\u65a4~20\u65a4","howlong":"7\u5929","name":"\u738b","phone":"18595518631","contact":"\u4e0a\u5348"}
}