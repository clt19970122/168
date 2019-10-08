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
                <div class="weui-cells mall-payer_info">
    <div class="weui-cell">
        <div class="weui-cell__bd">
            <p>订单号码<span><?php echo ($info["sn"]); ?></span></p>
        </div>
    </div>
    <div class="weui-cell">
        <div class="weui-cell__bd">
            <p>充值金额<span ><?php echo ($info["money"]); ?></span></p>
        </div>
    </div>
</div>

<div class="weui-panel weui-panel_access mall-payer_type">

    <div class="weui-panel__bd">

        <a href="javascript:wechatPays();" class="weui-media-box weui-media-box_appmsg weui-cell_access">
            <div class="weui-media-box__hd">
                <img class="weui-media-box__thumb" src="/Public/home/imgs/pay_wechat.png"/>
            </div>
            <div class="weui-media-box__bd">
                <h4 class="weui-media-box__title">微信支付</h4>
                <p class="weui-media-box__desc">使用微信安全支付</p>
            </div>
            <span class="weui-cell__ft"></span>
        </a>
        <a href="javascript:void(0);" class="weui-media-box weui-media-box_appmsg weui-cell_access" id="showIOSDialog1">
            <div class="weui-media-box__hd">
                <img class="weui-media-box__thumb" src="/Public/home/imgs/yue.png"/>
            </div>
            <div class="weui-media-box__bd">
                <h4 class="weui-media-box__title">余额支付</h4>
                <p class="weui-media-box__desc">使用余额快捷支付</p>
            </div>
            <span class="weui-cell__ft"></span>
        </a>
        <?php if($info['gid'] == '8' ): ?><a href="javascript:creditCard();" class="weui-media-box weui-media-box_appmsg weui-cell_access" >
                <div class="weui-media-box__hd">
                    <img class="weui-media-box__thumb" src="/Public/home/imgs/fqi.png"/>
                </div>
                <div class="weui-media-box__bd">
                    <h4 class="weui-media-box__title">分期支付</h4>
                    <p class="weui-media-box__desc">使用信用卡分期支付</p>
                </div>
                <span class="weui-cell__ft"></span>
            </a><?php endif; ?>
    </div>

</div>

<script type="text/javascript" class="dialog js_show">
    $(function(){
        var $iosDialog1 = $('#iosDialog1'),
            $iosDialog2 = $('#iosDialog2'),
            $androidDialog1 = $('#androidDialog1'),
            $androidDialog2 = $('#androidDialog2');

        $('#dialogs').on('click', '.weui-dialog__btn', function(){
            $(this).parents('.js_dialog').fadeOut(200);
        });

        $('#showIOSDialog1').on('click', function(){
            $iosDialog1.fadeIn(200);
        });
        $('#showIOSDialog2').on('click', function(){
            $iosDialog2.fadeIn(200);
        });
        $('#showAndroidDialog1').on('click', function(){
            $androidDialog1.fadeIn(200);
        });
        $('#showAndroidDialog2').on('click', function(){
            $androidDialog2.fadeIn(200);
        });
    });</script>

    <div class="page">
        <div id="dialogs">
            <!--BEGIN dialog1-->
            <div class="js_dialog" id="iosDialog1" style="display: none;">
                <div class="weui-mask"></div>
                <div class="weui-dialog">
                    <div class="weui-dialog__hd"><strong class="weui-dialog__title">确认余额支付</strong></div>
                    <div class="weui-dialog__bd">确认后将使用余额进行支付，会扣除余额金额</div>
                    <div class="weui-dialog__ft">
                        <a href="javascript:;" class="weui-dialog__btn weui-dialog__btn_default">取消</a>
                        <a href="javascript:walletPays();" class="weui-dialog__btn weui-dialog__btn_primary">确认</a>
                    </div>
                </div>
            </div>
            <!--END dialog4-->
        </div>
    </div>
    <script type="text/javascript">
        $(function(){
            var $iosDialog1 = $('#iosDialog1'),;

            $('#dialogs').on('click', '.weui-dialog__btn', function(){
                $(this).parents('.js_dialog').fadeOut(200);
            });

            $('#showIOSDialog1').on('click', function(){
                $iosDialog1.fadeIn(200);
            });
        });
</script>
<script>

    function wechatPays() {
        var data = {sn: "<?php echo ($info["sn"]); ?>", paytype: 1};
        showPreLoading();
        $.ajax({
            url: "<?php echo U('Malls/creatPay');?>", type: 'post', data: data, dataType: "json",
            success: function (res) {
                hidePreLoading();
                if (res.status === 0) {
                    return alert_wec(res.msg);
                }
                if (res.status ===3) {
                    onBridgeother(res.data);
                }else {
                    onBridgeReady(res.data);
                }
            },
            error: function () {
                hidePreLoading();
                return alert_wec("网络错误");
            }
        });
    }


    /**
     * 微信支付
     * @param {type} data
     * @returns {undefined}
     */
    function onBridgeReady(data) {
        var pay = {
            appId: data.appId, timeStamp: data.timeStamp, nonceStr: data.nonceStr, package: data.package,
            signType: data.signType, paySign: data.paySign
        };
        //
        WeixinJSBridge.invoke(
                'getBrandWCPayRequest', pay,
                function (res) {
                    if (res.err_msg === "get_brand_wcpay_request:ok") {
                        // window.location.href = "<?php echo U('Order/index');?>";
                        window.location.href = "<?php echo U('malls/pay_success');?>";
                        return;
                    }
                    return alert_wec("支付取消");
                }
        );
    }
    /**
     * 微信支付其他商品的逻辑
     * @param {type} data
     * @returns {undefined}
     */
    function onBridgeother(data) {
        var pay = {
            appId: data.appId, timeStamp: data.timeStamp, nonceStr: data.nonceStr, package: data.package,
            signType: data.signType, paySign: data.paySign
        };
        //
        WeixinJSBridge.invoke(
                'getBrandWCPayRequest', pay,
                function (res) {
                    if (res.err_msg === "get_brand_wcpay_request:ok") {
                        // window.location.href = "<?php echo U('Order/index');?>";
                        window.location.href = "<?php echo U('Order/index');?>";
                        return;
                    }
                    return alert_wec("支付取消");
                }
        );
    }

    ////////////////////////////////////////////////////////////////////////////

    function walletPays() {

        var data = {sn: "<?php echo ($info["sn"]); ?>", paytype: 4};
        showPreLoading();
        $.ajax({
            url: "<?php echo U('Malls/creatPay');?>", type: 'post', data: data, dataType: "json",
            success: function (res) {
                hidePreLoading();
                console.log(res);
                if (res.status === 0) {
                    return alert_wec(res.msg);
                }else if(res.status ===2){
                    window.location.href ="<?php echo U('Users/getbuhuolist');?>";
                }else if(res.status ===3){
                    window.location.href = "<?php echo U('order/index');?>";
                }else {
                    window.location.href = "<?php echo U('malls/pay_success');?>";
                }
            },
            error: function () {
                hidePreLoading();
                return alert_wec("网络错误");
            }
        });
    }
//分期
    function creditCard() {
        var money =  "<?php echo ($info["money"]); ?>";
        var url =  "<?php echo ($data); ?>";
       // console.log(url);
       // var url =  $('#url').val();
        if(money < 600) {
            alert_wec("分期支付金额不得低于600");
            return false;
        }
        window.location.href = 'http://akk.028wkf.cn/pay/stages#/'+url;
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