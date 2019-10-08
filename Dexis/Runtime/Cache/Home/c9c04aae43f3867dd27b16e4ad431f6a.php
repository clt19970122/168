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
                <script src="/Public/libs/qrcode.js"></script>
<div class="container" id="app"></div>


<div class="share">
    <!-- <img style="width: 100vw;height: 100vh" src="http://www.jacobhooychina.cn/Home/Index/images.html"/> -->
   <img style="width: 100vw;height: 100vh" src="<?php echo ($data); ?>"/>
    <!-- <div style="color: #fff;border-bottom: 1vh" class="share_cont">来自<?php echo ($name); ?>的分享</div> -->
</div>

<script>

    wx.ready(function () {

        wx.onMenuShareTimeline({
            title: '快来加入吧',
            link: "<?php echo ($weburl); ?>Home/Wcix/share/sysid/<?php echo ($id); ?>.html",
            imgUrl: '<?php echo ($weburl); ?>Public/home/imgs/share_code.png',
            success: function () {
                // 用户确认分享后执行的回调函数
            },
            cancel: function () {
                // 用户取消分享后执行的回调函数
            }
        });

        wx.onMenuShareAppMessage({
            title: '168太空素食快来加入吧', // 分享标题
            desc: '来自<?php echo ($name); ?>的分享', // 分享描述
            link: '<?php echo ($weburl); ?>Home/Wcix/share/sysid/<?php echo ($id); ?>.html',
            imgUrl: '<?php echo ($weburl); ?>Public/home/imgs/share_code.png',
            type: 'link', // 分享类型,music、video或link，不填默认为link
            dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
            success: function () {
                // 用户确认分享后执行的回调函数
            },
            cancel: function () {
                // 用户取消分享后执行的回调函数
            }
        });
    });


</script>


<script>
    QRCode.create( {
        'thumb' : '<?php echo ($weburl); ?>Public/home/imgs/share.jpg',
        'qrcode' : location.href,
        'title' : '夏季男士长裤子修身小脚裤运动裤韩版卫裤束脚休闲裤薄款哈伦裤潮',
        'price' : '6.90',
        'coupon' : '3'
    }, 
    function( data ){
        //生成图片
        var img = document.createElement('img');
            img.src = data;

        document.querySelector('#app').appendChild( img );

    } );
    function downloadImage(imgurl) {
        //imgurl 图片地址
        var a = $("<a></a>").attr("href", imgurl).attr("download", "img.png").appendTo("body");
        a[0].click();
        a.remove();
    }
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