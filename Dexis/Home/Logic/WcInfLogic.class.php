<?php

/**
 * 微信授权获取用户信息
 */

namespace Home\Logic;

class WcInfLogic {

    const authCode = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=";
    const webAuthTk = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=";
    const userInfo = "https://api.weixin.qq.com/sns/userinfo?access_token=";
    //
    const unInfo = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=";

    //
    private $appid;
    private $appsecret;
    private $redirectUri;
    //
    private $token;

    /**
     * 初始化参数
     */
    public function __construct() {
        $sysconfig = C("WEC_CONFIG");
        $this->appid = $sysconfig["APPID"];
        $this->appsecret = $sysconfig["APPSECRET"];
        $this->redirectUri = $sysconfig["redirectUri"];
    }

    /**
     * 开放入口
     */
    public function entrance() {
        $code = I("get.code");
        if (!empty($code)) {
            return $this->getAccessToken($code);
        }
        $url = self::authCode . $this->appid . "&redirect_uri=" . $this->redirectUri;
        $url .= "&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect";
        echo "<script>window.location.href='" . $url . "';</script>";
        exit;
    }

    /**
     * 取网页授权
     * @param type $code
     */
    private function getAccessToken($code) {
        $url = self::webAuthTk . $this->appid . "&secret=" . $this->appsecret . "&";
        $url .= "code=" . $code . "&grant_type=authorization_code";
        $tmp = poCurl($url, null);
        $res = json_decode($tmp, true);
        if ($res["access_token"] == null) {
            return false;
        }
        $this->token = $res;
        return $this->getUsrinfo();
        #return $this->getUseUninfo();
    }

    /**
     * 取用户基本信息
     */
    private function getUsrinfo() {
        $url = self::userInfo . $this->token["access_token"];
        $url .= "&openid=" . $this->token["openid"] . "&lang=zh_CN";
        $tmp = poCurl($url, null);
        $res = json_decode($tmp, true);
        if ($res["openid"] == null) {
            return false;
        }
        return $res;
    }

    /**
     * 用户UNION信息
     */
    private function getUseUninfo() {
        $token = D("Home/Token", "Logic");
        #
        $url = self::unInfo . $token->entrance();
        $url .= "&openid=" . $this->token["openid"] . "&lang=zh_CN";
        $tmp = poCurl($url, null);
        dump($tmp);
        exit;
        $res = json_decode($tmp, true);
        if ($res["openid"] == null) {
            return false;
        }
        return $res;
    }

}
