<?php

/**
 * 微信模板
 */

namespace Home\Logic;

class WctempLogic {

    const tempUrl = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=";

    #

    private $user;
    private $token;
    private $data;

    #
    private $push;

    /**
     * 系统入口
     * @param type $user
     * @param type $token
     * @param type $data
     */
    public function entrance($user, $data) {
        $this->user = $user;
        $this->data = $data;
        return $this->combDatas();
    }

    /**
     * 组合数据
     */
    private function combDatas() {
        $token = D("Home/Token", "Logic");
        $res = $token->entrance();
        if ($res == 403 || $res == 423) {
            return false;
        }
        $this->token = $res;
        #
        $this->push["touser"] = $this->user["openid"];
        $this->push["url"] = "";
        return $this->judgeTemps();
    }

    /**
     * 判断发送模板
     */
   /* private function judgeTemps() {
        //根据分类判断发送模板类型
        if ($this->data["type"] == "tips") {
            // $this->push["template_id"] = "h7fZFOxa4II1lXLJ6nGSlSx-jlqIZc4wd1m7CnOT45E";
            $this->push["template_id"] = "xkDkXqAISOhptRJAGYBc0q7QX-X6Lrg7R1oI6Xh8jeo";
            return $this->accTips();
        }
        return true;
    }*/

    private function judgeTemps() {
        //根据分类判断发送模板类型
        $type =$this->data["type"] ;
        switch ($type){
            case 'tips':
                $this->push["template_id"] = "xkDkXqAISOhptRJAGYBc0q7QX-X6Lrg7R1oI6Xh8jeo";
                return $this->accTips();
                break;
            case 'proin':
                $this->push["template_id"] = "PS9WYjtIAw5nV2-SvXZ8KDxklcrhwhUbDm_YtWdwWps";
                return $this->proIn();
                break;
            case 'band':
                $this->push["template_id"] = "yJI3kmYowqrP0gF08mFDJEol81MQOivWsuklXBqoM0w";
                return $this->haveBand();
                break;
            case 'haveUplevel':
                $this->push["template_id"] = "QKnPq0b4FZDlja6rCQ1Ab9br_YHTij8zsZH0l0qId_M";
                return $this->haveUplevel();
                break;
            case 'failed':
                $this->push["template_id"] = "auZJczhxEQ7AyASUWxH_1O8f29NoUHEyf1ZqWjmWKhM";
                return $this->failed();
                break;
            case 'reback':
                $this->push["template_id"] = "VZ77AO9KI838EhgfmBVJ5lkX7oAqZq9jIMU1pPGzS6Q";
                return $this->reback();
                break;
        }
        return true;
    }

    #############消息处理############

    /**
     * 账户激活--库存不足提醒
     * @return type
     */
    private function accTips() {
        $this->push["data"]["first"] = array("value" => "您当前账号货物库存不足，请在1小时内尽快进货", "color" => "#2da94c");
        $this->push["data"]["keyword1"] = array("value" => $this->user["nickname"], "color" => "#2da94c");
        $this->push["data"]["keyword2"] = array("value" => $this->data["count"] . "套", "color" => "#2da94c");
        $this->push["data"]["remark"] = array("value" => "感谢您的使用，请及时登录系统查看", "color" => "#2da94c");
        return $this->sentTempMsg();
    }
   /**
     * 账户激活--产品入库通知
     * @return type
     */
    private function proIn() {
        $this->push["data"]["first"] = array("value" => "你购买的".$this->data["product"]."产品已经入库", "color" => "#2da94c");
        $this->push["data"]["keyword1"] = array("value" => $this->data["nums"] , "color" => "#2da94c");
        $this->push["data"]["keyword2"] = array("value" => $this->data["stock"] , "color" => "#2da94c");
        $this->push["data"]["remark"] = array("value" => "感谢您的使用，请登录系统查看", "color" => "#2da94c");
        return $this->sentTempMsg();
    }
    /**
     * 账户激活--绑定成功通知
     * @return type
     */
    private function haveBand() {
        $this->push["data"]["first"] = array("value" => "您好，".$this->data["user"]."成功绑定为您的一级代理。", "color" => "#2da94c");
        $this->push["data"]["keyword1"] = array("value" => $this->data["phone"] , "color" => "#2da94c");
        $this->push["data"]["keyword2"] = array("value" => $this->data["time"] , "color" => "#2da94c");
        $this->push["data"]["remark"] = array("value" => "请登录系统查看我的团队", "color" => "#2da94c");
        return $this->sentTempMsg();
    }
    /**
     * 账户激活--升级通知
     * @return type
     */
    private function haveUplevel() {
        $this->push["data"]["first"] = array("value" => "您已累计成功升级。", "color" => "#2da94c");
        $this->push["data"]["keyword1"] = array("value" => $this->user["nickname"], "color" => "#2da94c");
        $this->push["data"]["keyword2"] = array("value" => $this->data["old_level"] , "color" => "#2da94c");
        $this->push["data"]["keyword3"] = array("value" => $this->data["now_level"] , "color" => "#2da94c");
        $this->push["data"]["keyword4"] = array("value" => $this->data["time"] , "color" => "#2da94c");
        $this->push["data"]["remark"] = array("value" => "如有问题，请联系回复！", "color" => "#2da94c");
        return $this->sentTempMsg();
    }
    /**
     * 账户激活--申请失败
     * @return type
     */
    private function failed() {
        $this->push["data"]["first"] = array("value" => "抱歉,您的申请失败，审核不通过", "color" => "#2da94c");
        $this->push["data"]["keyword1"] = array("value" => $this->user["nickname"], "color" => "#2da94c");
        $this->push["data"]["keyword2"] = array("value" => $this->data["info"] , "color" => "#2da94c");
        $this->push["data"]["keyword3"] = array("value" => $this->data["time"] , "color" => "#2da94c");
        $this->push["data"]["remark"] = array("value" => "您的帐号审核未通过，请您重新提交。", "color" => "#2da94c");
        return $this->sentTempMsg();
    }
    /**
     * 账户激活--退款处理
     * @return type
     */
    private function reback() {
        $this->push["data"]["first"] = array("value" => $this->data["first"] , "color" => "#2da94c");
        $this->push["data"]["keyword1"] = array("value" => $this->user["money"], "color" => "#2da94c");
        $this->push["data"]["keyword2"] = array("value" => $this->data["info"] , "color" => "#2da94c");
        $this->push["data"]["keyword3"] = array("value" => $this->data["sn"] , "color" => "#2da94c");
        $this->push["data"]["remark"] = array("value" => $this->data["remake"], "color" => "#2da94c");
        return $this->sentTempMsg();
    }

    ////////////////////////////////////////////////////////////////////////////

    /**
     * 发送模板消息
     */
    private function sentTempMsg() {
        $url = self::tempUrl . $this->token;
        $params = json_encode($this->push);
        $tmp = poCurl($url, $params);
        $res = json_decode($tmp, true);
        #
        if ($res["errcode"] == 0) {
            return true;
        }
        #
        $syslog = M("syslog");
        $syslog->name = "WX_TEMP_MODEL";
        $syslog->send = $params;
        $syslog->back = $tmp;
        $syslog->times = date("Y-m-d H:i:s");
        $syslog->add();
        #
        return false;
    }

}
