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
                <!--
<div class="regs">
    <div class="regs-form">
        <p class="label"><input type="text" name="phone" placeholder="请输入手机号"/></p>
        <p class="label">
            <input type="text" name="code" placeholder="请输入验证码"/>
            <a href="javascript:getSysCode();" id="verifycode">获取验证码</a>
        </p>
        <p class="label">
            <input type="text" name="user" placeholder="请输入密码"/>
            <img src="/Public/home/imgs/regs-eye.png" class="eye"/>
        </p>
        <p class="label">
            <input type="text" name="user2" placeholder="再次确认密码"/>
            <img src="/Public/home/imgs/regs-eopen.png" class="eopen"/>
        </p>
        <a class="regs" href="javascript:doOption();">确认</a>
    </div>
</div>

<script>

    function getSysCode() {
        var phone = $("input[name='phone']").val();
        if (!checkPhone(phone)) {
            return alert_wec("请输入正确的手机号码");
        }
        showPreLoading();
        $.ajax({
            url: "<?php echo U('Login/regs_sms');?>", type: 'post',
            data: {phone: phone}, dataType: "json",
            success: function (res) {
                hidePreLoading();
                if (res.status !== 1) {
                    return alert_wec(res.msg);
                }
                setTimtout(120);
            }
        });
    }

    function setTimtout(t) {
        var codeRd = $("#verifycode");
        codeRd.attr("href", "javascript:void(0);");
        codeRd.addClass("gray");
        var timers = setInterval(function () {
            t&#45;&#45;;
            codeRd.html(t);
            if (t <= 0) {
                clearInterval(timers);
                codeRd.removeClass("gray");
                codeRd.attr("href", "javascript:getSysCode();");
                codeRd.html("获取验证码");
            }
        }, 1000);
    }

    function doOption() {
        var temp = $("form").serializeArray();
        var data = objToArray(temp);
        ////
        var phone = $("input[name='phone']").val();
        var code = $("input[name='code']").val();
        var user = $("input[name='user']").val();
        var user2 = $("input[name='user2']").val();
        if (!checkPhone(phone)) {
            return alert_wec("请填写正确的手机号码");
        }
        if (code === null || data.code === "") {
            return alert_wec("请填写验证码");
        }
        if ($.trim(user) === null || $.trim(user) === "") {
            return alert_wec("密码不能为空");
        }
        if (user !== data.vipass) {
            return alert_wec("两次密码不一致");
        }
        ////
        showPreLoading();
        $.ajax({
            url: "<?php echo U('Login/forget_op');?>", type: 'post',
            data: data, dataType: "json",
            success: function (res) {
                hidePreLoading();
                if (res.status !== 1) {
                    return alert_wec(res.msg);
                }
                window.location.href = "<?php echo U('User/index');?>";
            }
        });
    }

</script>-->
<div class="weui-msg">
    <div class="weui-msg__text-area">
        <h2 class="weui-msg__title">绑定上级信息</h2>
        <p class="weui-msg__desc">您未绑定上级,请输入上级系统账号！</p >
    </div>
</div>

<form>
    <div class="regs">
        <div class="regs-form">
            <p class="label">
                <input type="text" name="recid" id="idcode" placeholder="请输入上级系统账号"/>
            </p>
            <p id="getuser" style="color: red;text-align: center;font-size: 20px"></p>
            <a class="regs" href="javascript:doOption();">确认</a>
        </div>
    </div>
</form>

<script>

    function doOption() {
        var temp = $("form").serializeArray();
        var data = objToArray(temp);
        if (data.name === null || data.name === "") {
            return alert_wec("请输入系统账号");
        }
        ////
        showPreLoading();
        $.ajax({
            url: "<?php echo U('Login/doSetUp');?>", type: 'post',
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

<script>
    $("#idcode").blur(function(){
        $.ajax({
            type: "GET",
            url: "<?php echo U('getuserbysys');?>",
            data: {srsid:$("#idcode").val()},
            dataType: "json",
            success: function(data){
                if(data==null){
                    $('#getuser').html('error:请检查系统账号是否正确');
                }else {
                    $('#getuser').html('用户名:'+data.nickname);
                }
            }
        })

    });
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