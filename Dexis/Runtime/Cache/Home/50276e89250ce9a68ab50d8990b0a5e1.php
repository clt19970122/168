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
                <form>
    <input type="hidden" name="uid" value="<?php echo ($uid); ?>"/>
    <div class="weui-cells user-idcard">

        <div class="weui-cell weui-cell_access">
            <div class="weui-cell__hd">姓名</div>
            <div class="weui-cell__bd">
                <input class="weui-input" type="text" name="name" value="<?php echo ($info["name"]); ?>" placeholder="请输入姓名">
                <input type="hidden" name="type" value="<?php echo ($info["type"]); ?>" >
            </div>
            <div class="weui-cell__ft"></div>
        </div>
        <div class="weui-cell weui-cell_access">
            <div class="weui-cell__hd">手机号码</div>
            <div class="weui-cell__bd">
                <input class="weui-input" type="number" name="phone" value="<?php echo ($info["phone"]); ?>" pattern="[0-9]*" placeholder="请输入号码">
            </div>
            <div class="weui-cell__ft"></div>
        </div>
        <div class="weui-cell weui-cell_select weui-cell_select-after">
            <div class="weui-cell__hd">
                <label class="weui-label">证件类型</label>
            </div>
            <div class="weui-cell__bd">
                <select class="weui-select" name="idtype" dir="rtl">
                    <option value="1">身份证</option>
                </select>
            </div>
        </div>
        <div class="weui-cell weui-cell_access">
            <div class="weui-cell__hd">证件号码</div>
            <div class="weui-cell__bd">
                <input class="weui-input" type="text" name="idcard" value="<?php echo ($info["idcard"]); ?>" placeholder="请输入号码">
            </div>
            <div class="weui-cell__ft"></div>
        </div>
        <div class="weui-cell weui-cell_select weui-cell_select-after">
            <div class="weui-cell__hd">
                <label class="weui-label">性别</label>
            </div>
            <div class="weui-cell__bd">
                <select class="weui-select" name="sex" dir="rtl">
                    <option value="1" <?php if(($info["sex"]) == "1"): ?>selected<?php endif; ?>>男</option>
                    <option value="2" <?php if(($info["sex"]) == "2"): ?>selected<?php endif; ?>>女</option>
                </select>
            </div>
        </div>
        <!--div class="weui-cell weui-cell_access">
            <div class="weui-cell__hd">出生年月</div>
            <div class="weui-cell__bd">
                <input class="weui-input" type="number" name="myear" pattern="[0-9]*"  value="<?php echo ($info["myear"]); ?>" placeholder="请输入">
            </div>
            <div class="weui-cell__ft"></div>
    </div-->

    </div>
</form>

<div class="weui-btn-area">
    <a class="weui-btn weui-btn_primary" href="javascript:doOption();">保存</a>
</div>

<script>

    function doOption() {
        var temp = $("form").serializeArray();
        var data = objToArray(temp);
        showPreLoading();
        $.ajax({
            url: "<?php echo U('Users/idauth_op');?>", type: 'post',
            data: data, dataType: "json",
            success: function (res) {
                hidePreLoading();
                if (res.status !== 1) {
                    return alert_wec(res.msg);
                }
                window.location.href = res.data;
            }
        });
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