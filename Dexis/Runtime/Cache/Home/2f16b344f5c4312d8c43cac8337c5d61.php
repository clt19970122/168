<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
    <head>
        <title><?php echo ($conf_title); ?></title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0,maximum-scale=1.0,user-scalable=0">
        <meta name="format-detection" content="telephone=no" />
        <meta HTTP-EQUIV="pragma" CONTENT="no-cache">
<meta HTTP-EQUIV="Cache-Control" CONTENT="no-store, must-revalidate">
<meta HTTP-EQUIV="expires" CONTENT="Wed, 26 Feb 1997 08:21:57 GMT">
<meta HTTP-EQUIV="expires" CONTENT="0">
        <link href="/Public/libs/weui/style/weui.min.css" rel="stylesheet">
        <link href="/Public/libs/weui/style/weui-picker.css" rel="stylesheet">
        <link href="/Public/home/css/app.css?v=<?php echo ($version); ?>" rel="stylesheet">
        <link href="/Public/home/css/icon.css?v=<?php echo ($version); ?>" rel="stylesheet">
        <script src="/Public/libs/jquery.min.js" charset="utf-8"></script>
        <script src="/Public/libs/weui/js/weui.min.js" charset="utf-8"></script>
        <script src="/Public/libs/common.js" charset="utf-8"></script>
        <script src='http://res.wx.qq.com/open/js/jweixin-1.0.0.js'></script>


<!--        <link rel="stylesheet" type="text/css" href="/Public/home/areachoose/jsdaima.com.css">
        <link rel="stylesheet" href="/Public/home/areachoose/LArea.css">
        <script src="/Public/home/areachoose/LAreaData1.js"></script>
        <script src="/Public/home/areachoose/LAreaData2.js"></script>
        <script src="/Public/home/areachoose/LArea.js"></script>-->
        <script src="/Public/home/areachoose/weui.min.js"></script>
        <script src="/Public/home/areachoose/addrData.js"></script>
        <script src="/Public/home/areachoose/addrDataOrigin.js"></script>
        <script>

            wx.config({
                debug: false,
                appId: '<?php echo ($ticket["appid"]); ?>',
                timestamp: '<?php echo ($ticket["timestamp"]); ?>',
                nonceStr: '<?php echo ($ticket["noncestr"]); ?>',
                signature: '<?php echo ($ticket["signature"]); ?>',
                jsApiList: ['onMenuShareTimeline','onMenuShareAppMessage']
            });

        </script>
    </head>
    <body>
        <div class="page">

            <div id="loadingToast" class="none">
                <div class="weui-mask_transparent"></div>
                <div class="weui-toast">
                    <i class="weui-loading weui-icon_toast"></i>
                    <p class="weui-toast__content">处理中</p>
                </div>
            </div>

            <div class="js_dialog none" id="iosDialog">
                <div class="weui-mask"></div>
                <div class="weui-dialog">
                    <div class="weui-dialog__bd" id="iosDialogTitle">&nbsp;</div>
                    <div class="weui-dialog__ft">
                        <a href="javascript:;" class="weui-dialog__btn">知道了</a>
                    </div>
                </div>
            </div>

            <div class="content">
                <style>
   .photoBg{
        height: 100vh;
       display: flex;
       justify-content: center;
       align-items: center;
       background-color: rgba(0,0,0,.5);
    }
   /*  #clipArea {
        height: 200px;
        position: relative;
    }*/
.photoBtn{
    display: flex;
    width: 100%;
    position: absolute;
    z-index: 9999;
    justify-content: space-between;
    bottom: 30px;
}

    #clipBtn {
        right: 10px;
        width: 40%;
        height: 40px;
        line-height: 40px;
        text-align: center;
        background: #153afa;
        border: none;
        font-size: 16px;
        border-radius: 5px;
        margin-right: 10px;
        color: #fff;
    }

/*    #view {
        margin: 0 auto;
        width: 200px;
        height: 200px;
        background-color: #666;
    }*/
    .photoselect{
        width: 40%;
        height: 40px;
        line-height: 40px;
        text-align: center;
        background: #153afa;
        display: block;
        font-size: 16px;
        border-radius: 5px;
        margin-left: 10px;
        color: #fff;
        position: relative;
    }
   #file{
       width: 100%;
       height: 40px;
       opacity: 0;
       position: absolute;
       left: 0;
       top: 0;
   }
#clipArea {
    width: 100%;
    height: 300px;
}
.backBtn a{
    position: absolute;
    top: 20px;
    left: 10px;
    width: 30%;
    height: 40px;
    line-height: 40px;
    text-align: center;
    background: #153afa;
    display: block;
    font-size: 16px;
    border-radius: 5px;
    color: #fff;
}


</style>
<div class="photoBg">
    <div class="backBtn">
        <a href="<?php echo U('users/index');?>">返回</a>
    </div>
    <div id="clipArea">
    </div>
    <div class="photoBtn">
        <div class="photoselect">选择照片<input type="file" id="file"></div>
        <button id="clipBtn">完成</button>
    </div>
</div>





<!--头像剪裁-->
<script src="/Public/home/photoclip/js/hammer.min.js"></script>
<script src="/Public/home/photoclip/js/iscroll-zoom-min.js"></script>
<script src="/Public/home/photoclip/js/lrz.all.bundle.js"></script>

<script src="/Public/home/photoclip/js/PhotoClip.js"></script>

<script>
    var pc = new PhotoClip('#clipArea', {
        size: 260,
        outputSize: 640,
        //adaptive: ['60%', '80%'],
        file: '#file',
        //view: '#view',
        ok: '#clipBtn',
        img: '<?php echo ($user_info["headimgurl"]); ?>',
        loadStart: function() {
            console.log('开始读取照片');
        },
        loadComplete: function() {
            console.log('照片读取完成');
        },
        done: function(dataURL) {
            showPreLoading();
            //上传图片
            $.ajax({
                url: "<?php echo U('Users/editSelfinfo');?>",//请求的url地址
                dataType: "json",//返回的格式为json
                async: true,//请求是否异步，默认true异步，这是ajax的特性
                data: {"img": dataURL},//参数值
                type: "POST",//请求的方式
                beforeSend: function () {
                },//请求前的处理
                success: function (res) {
                    hidePreLoading();
                    if (res.status !== 1) {
                        return alert_wec(res.msg);
                    }
                    alert_wec(res.msg);
                    window.location.href ="<?php echo U('users/index');?>";

                },//请求成功的处理
                complete: function () {
                },//请求完成的处理
                error: function () {
                }//请求出错的处理
            })
        },
        fail: function(msg) {
            alert(msg);
        }
    });

    // 加载的图片必须要与本程序同源，否则无法截图
    pc.load('img/mm.jpg');

</script>

            </div>

        </div>
        <script>
            window.onload = function () {
                document.addEventListener('touchstart', function (event) {
                    if (event.touches.length > 1) {
                        event.preventDefault();
                    }
                });
                var lastTouchEnd = 0;
                document.addEventListener('touchend', function (event) {
                    var now = (new Date()).getTime();
                    if (now - lastTouchEnd <= 300) {
                        event.preventDefault();
                    }
                    lastTouchEnd = now;
                }, false);
            }
        </script>
    </body>
</html>