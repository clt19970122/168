<?php

namespace Home\Controller;

use Think\Controller;

class CommController extends Controller {

    public $ssid;
    public $wecs;
    public $user;

    public function _initialize() {
        $baseMemory = memory_get_usage();
        session("ssid_history", get_url());
        $ssid = session("ssid_dexis");
//        session("ssid_dexis_wechat",null);
   //     session("ssid_dexis_wechat", array("openid" => "oJQO41ZYyfZhzVehfqeH-j9gYuxk"));//李昌红
        session("ssid_dexis_wechat", array("openid" => "oJQO41S-C74UDpcm05kLsSqVBd58"));//华侨
     //  session("ssid_dexis_wechat", array("openid" => "orMnrwBDN8x6N_fQYvaBPUU4TTWE"));
//       session("ssid_dexis_wechat", array("openid" => "qq"));
//       session("ssid_dexis_wechat", array("openid" => "oJQO41cqMGgYfXB5GHu40tioIK4c"));//瑞银
//        session("ssid_dexis_wechat", array("openid" => "oJQO41Rj5Lz7BQi4QCcaP20fnybE"));//养生开
        $wechat = session("ssid_dexis_wechat");
        if ($wechat == null) {
            $this->redirect("Wcix/getInfos");
        }

        set_time_limit(100);
        #
        $this->wecs = $wechat;
        $this->ssid = $ssid;
        $sysid =M('account')->where(['id'=>$ssid])->find();
        $this->getUser();
        $this->assign("version", time());
        $this->assign("sysid",$sysid['sysid']);
        $this->assign("tabbar", CONTROLLER_NAME);
        #
        $title = C(CONTROLLER_NAME . "/" . ACTION_NAME);
        $this->assign("conf_title", $title == null ? "我的商城" : $title);
    }

    /**
     * 获取用户信息
     */
    public function getUser() {
        $account = M("account");
        #
        $where["id"] = $this->ssid;
        $info = $account->where($where)->find();
        #
        if ($info["headimgurl"] == null) {
            $info["headimgurl"] = __ROOT__ . "/Public/home/imgs/head.png";
        } else {
            $loc = strpos($info["headimgurl"], "ttp:");
            if (!$loc) {
                $info["headimgurl"] = __ROOT__ . "/Public/uploads/head/" . $info["headimgurl"];
            }
        }
        $this->user = $info;
        $this->assign("conf_user", $info);
    }

    public function accessToken(){
        $AppID='wx35e214c1721ff689';
        $AppSecret='c179b118a5033b01a0bca7724b15c497';
        $Token=getAccessToken($AppID,$AppSecret);
        var_dump($Token);exit;
    }
    /**
     * @param $src 背景图片
     * @param $url 二维码网址
     * @param $text 内容字
     */
    public function test($src,$url,$text){
        // // 2.获取图片信息
        $info = getimagesize($src);
        // // 3.通过图像的编号来获取图像的类型
        $type = image_type_to_extension($info[2],false);
        // // 4.在内存中创建和图片类型一样的图像
        $fun = "imagecreatefrom{$type}";
        // // 5.把图片复制到内存中
        $image = $fun($src);
        //获取二维码
        $image2 = $this->scerweima2($url);

        $img = $src;//图片跟路径
        $size = 30;//字体大小
        $font ='./simheittf.ttf';//加载字体ttf
        $img = imagecreatefromjpeg($img);// 加载已有图像
        $black = imagecolorallocate($img, 255, 255, 255);//设置颜色为蓝色
        $x =200;//首个字的横坐标
        imagettftext($img, $size, 0, $x, 1300, $black, $font, $text);//添加文字
        // // 6.合并图片                       水印图上下移动
        $msg  = imagecopymerge($img,$image2,490,880,0,0,180,180,100);// 底图--水印图--底图X轴坐标--底图Y轴坐标--水印图X--水印图Y--拷贝的宽--拷贝的高--透明度;
        // // 7.销毁水印图片
        imagepng($img,'./Dexis/Runtime/img/'.$this->user['sysid'].'.png');
        // // 7.销毁水印图片
       // imagedestroy($image2);
        return '/Dexis/Runtime/img/'.$this->user['sysid'].'.png';
        imagedestroy($image2);
        ob_end_clean();
       // $func = "image{$type}";//拼接函数
        //var_dump($func($image));exit;
       // $func($img);//直接显示图片
    }
    /*
   * 根据传入的url生成二维码
   */
    public function scerweima2($url){
        vendor('phpqrcode');
        $str = base64_encode($url);
        //获取随机字符串
        $n_str = $this->getStr(4);
        $value = $url;         //二维码内容
        $errorCorrectionLevel = 'L';  //容错级别
        $matrixPointSize =5.2;      //生成图片大小
        //生成二维码图片
        $arr = \QRcode::png($value,false,$errorCorrectionLevel, $matrixPointSize, 1,true);
        return $arr;
    }


    //转base64方法 传入图片路径
    function imgToBase64($img_file) {

        $img_base64 = '';
        if (file_exists($img_file)) {
            $app_img_file = $img_file; // 图片路径
            $img_info = getimagesize($app_img_file); // 取得图片的大小，类型等

            //echo '<pre>' . print_r($img_info, true) . '</pre><br>';
            $fp = fopen($app_img_file, "r"); // 图片是否可读权限

            if ($fp) {
                $filesize = filesize($app_img_file);
                $content = fread($fp, $filesize);
                $file_content = chunk_split(base64_encode($content)); // base64编码
                switch ($img_info[2]) {           //判读图片类型
                    case 1: $img_type = "gif";
                        break;
                    case 2: $img_type = "jpg";
                        break;
                    case 3: $img_type = "png";
                        break;
                }
                $img_base64 = 'data:image/' . $img_type . ';base64,' . $file_content;//合成图片的base64编码
            }
            fclose($fp);
        }

        return $img_base64; //返回图片的base64
    }
     /*
  * 生成随机字符串
  */
    public function getStr($length){
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str ="";
        for ( $i = 0; $i < $length; $i++ ) {
            $str.= substr($chars, mt_rand(0, strlen($chars)-1), 1);
        }
        return $str;
    }
}
