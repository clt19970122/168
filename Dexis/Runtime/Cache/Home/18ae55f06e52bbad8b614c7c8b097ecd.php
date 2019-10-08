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
<div class="order-menu">
    <a href="<?php echo U('users/getStockList');?>" class="order-menu_item <?php if(($st) == "10"): ?>order-menu_item_on<?php endif; ?>">全部</a>
    <a href="<?php echo U('users/getStockList','st=1');?>" class="order-menu_item <?php if(($st) == "1"): ?>order-menu_item_on<?php endif; ?>">官方直播间</a>
    <a href="<?php echo U('users/getStockList','st=3');?>" class="order-menu_item <?php if(($st) == "3"): ?>order-menu_item_on<?php endif; ?>">用户直播间</a>
</div>-->
<?php if(is_array($d)): $i = 0; $__LIST__ = $d;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><ul class="weui-payrec">
        <a href="<?php echo ($vo["liveurl"]); ?>" title="进入<?php echo ($vo["user_name"]); ?>的直播间">
            <div class="weui-pay-m">
                <li class="weui-pay-order">
                    <dl class="weui-pay-line">
                        <dd class="weui-pay-name"><?php echo ($vo["user_name"]); ?></dd>
                        <dt class="weui-pay-label">等 级 ：</dt><dd class="weui-pay-e"><?php echo ($vo['user_level']); ?></dd>
                        <img src="<?php echo ($vo["user_img"]); ?>" alt=""/>
                    </dl>

                </li>
            </div>
        </a>
    </ul><?php endforeach; endif; else: echo "" ;endif; ?>
<!--<?php if(is_array($company)): $i = 0; $__LIST__ = $company;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><ul class="weui-payrec">
        <a href="<?php echo ($v["liveurl"]); ?>" title="进入<?php echo ($v["user_name"]); ?>的直播间">
            <div class="weui-pay-m">
                <li class="weui-pay-order">
                    <dl class="weui-pay-line">
                        <dd class="weui-pay-name">168太空素食官方直播间</dd>
                        <img src="<?php echo ($v["user_img"]); ?>" alt=""/>
                    </dl>

                </li>
            </div>
        </a>
    </ul><?php endforeach; endif; else: echo "" ;endif; ?>
<?php if(is_array($top)): $i = 0; $__LIST__ = $top;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><ul class="weui-payrec">
    <a href="<?php echo ($vo["liveurl"]); ?>" title="进入<?php echo ($vo["user_name"]); ?>的直播间">
        <div class="weui-pay-m">
            <li class="weui-pay-order">
                <dl class="weui-pay-line">
                    <dd class="weui-pay-name">联创:<?php echo ($vo["user_name"]); ?>的直播间</dd>
                    <img src="<?php echo ($vo["user_img"]); ?>" alt=""/>
                </dl>

            </li>
        </div>
    </a>
</ul><?php endforeach; endif; else: echo "" ;endif; ?>-->
<div class="page-category js-categoryInner page-category-content" style="width: 96%;margin: 0 auto">

    <?php if(is_array($company)): $i = 0; $__LIST__ = $company;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><a href="<?php echo ($v["liveurl"]); ?>">
            <div class="weui-cell weui-cell_access" style="align-items: flex-start;background: #fff;border-radius: 8px;box-shadow: 0 4px 6px #e9e8e8;margin-top: 1rem">
                <div class="weui-cell__hd" style="position: relative;margin-right: 10px;">
                    <img src="/Public/static/imgs/comp.jpg" style="width: 50px;display: block;border-radius: 5px">
                </div>
                <div class="weui-cell__bd">
                    <div style="display: flex;justify-content: space-between;align-items: center;text-align: left;">
                        <div>
                            <div style="display: inline-flex;justify-content: center;align-items: center">
                                <p style="font-size: 17px;display: flex;align-items: center">168太空素食官方直播间<button class="weui-btn weui-btn_mini orange" style="margin-left:.5rem;padding: 0 .5rem;line-height: 1.4rem;font-size: 1rem">官方</button></p>
                            </div>
                            <p style="font-size: 13px;color: #888888;">映客ID号：<?php echo ($v["live_id"]); ?></p>
                        </div>

                            <div>
                                <div style="content: '';display: inline-block;height: 6px;width: 6px;border-width: 2px 2px 0 0;border-color: #c8c8cd;border-style: solid;-webkit-transform: matrix(.71,.71,-.71,.71,0,0);transform: matrix(.71,.71,-.71,.71,0,0);margin-top: 8px;right: 15px">
                                </div>
                            </div>
                    </div>
                </div>
            </div>
        </a><?php endforeach; endif; else: echo "" ;endif; ?>

    <!--第二个-->
    <?php if(is_array($top)): $i = 0; $__LIST__ = $top;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><a href="<?php echo ($vo["liveurl"]); ?>">
            <div class="weui-cell weui-cell_access" style="align-items: flex-start;background: #fff;border-radius: 8px;box-shadow: 0 4px 6px #e9e8e8;margin-top: 1rem">
                <div class="weui-cell__hd" style="position: relative;margin-right: 10px;">
                    <img src="<?php echo ($vo["user_img"]); ?>" style="width: 50px;display: block;border-radius: 5px">
                </div>
                <div class="weui-cell__bd">
                    <div style="display: flex;justify-content: space-between;align-items: center;text-align: left;">
                        <div>
                            <div style="display: inline-flex;justify-content: center;align-items: center">
                                <p style="font-size: 17px;display: flex;align-items: center"><?php echo ($vo["user_name"]); ?>的直播间<button class="weui-btn weui-btn_mini blue" style="margin-left:.5rem;padding: 0 .5rem;line-height: 1.4rem;font-size: 1rem">联创</button></p>
                            </div>
                            <p style="font-size: 13px;color: #888888;">映客ID号：<?php echo ($vo["live_id"]); ?></p>
                        </div>
                            <div>
                                <div style="content: '';display: inline-block;height: 6px;width: 6px;border-width: 2px 2px 0 0;border-color: #c8c8cd;border-style: solid;-webkit-transform: matrix(.71,.71,-.71,.71,0,0);transform: matrix(.71,.71,-.71,.71,0,0);margin-top: 8px;right: 15px">
                                </div>
                            </div>
                    </div>
                </div>
            </div>
        </a><?php endforeach; endif; else: echo "" ;endif; ?>
</div>
<!--
<script>

    function doOption(id) {
        var data = {ids: id, status: 4};
        showPreLoading();
        $.ajax({
            url: "<?php echo U('Core/edits','model=orders');?>", type: 'post',
            data: data, dataType: "json",
            success: function (res) {
                hidePreLoading();
                if (res.status !== 1) {
                    return alert_wec(res.msg);
                }
                window.location.reload();
            }
        });
    }

</script>-->

<script>
    /**
     * 微信支付
     * @param {type} data
     * @returns {undefined}
     */

    function toDopay(sn) {
        $.ajax({
            url: "<?php echo U('Users/toPayTrans');?>",
            type: 'post',
            data: {'sn':sn},
            dataType: "json",
            success: function (res) {
                hidePreLoading();
                if (res.status !== 1) {
                    return alert_wec(res.msg);
                }
                onBridgeReady(res.data);
            },
            error: function () {
                hidePreLoading();
                return alert_wec("网络错误");
            }
        });
    }
    function onBridgeReady(data) {
        var pay = {
            appId: data.appId, timeStamp: data.timeStamp, nonceStr: data.nonceStr, package: data.package,
            signType: data.signType, paySign: data.paySign
        };
        //
        WeixinJSBridge.invoke(
                'getBrandWCPayRequest', pay,
                function (res) {
                    // alert(JSON.stringify(res));
                    if (res.err_msg === "get_brand_wcpay_request:ok") {
                        window.location.href = "<?php echo U('users/getStocklist');?>";
                        return;
                    }
                    // alert_wec("支付取消");
                    alert_wec("支付取消");
                    window.location.href = "<?php echo U('users/getStocklist');?>";
                    return;
                }
        );
    }

    function doOption(id){
        $.ajax({
            url: "<?php echo U('Users/cancel');?>",
            type: 'post',
            data: {'id':id},
            dataType: "json",
            success: function (res) {
                hidePreLoading();
                if (res.status ) {
                    alert_wec(res.msg);
                    window.location.href = "<?php echo U('users/getStocklist');?>";
                }
                return alert_wec(res.msg);

            },
            error: function () {
                hidePreLoading();
                return alert_wec("网络错误");
            }
        });
    }

    function sureGet(id) {
        $.ajax({
            url: "<?php echo U('users/sureGetGood');?>",
            type: 'post',
            data: {'id': id},
            dataType: "json",
            success: function (res) {
                hidePreLoading();
                if (res.status) {
                    alert_wec(res.msg);
                    window.location.href = "<?php echo U('users/getStocklist');?>";
                }
                return alert_wec(res.msg);
            },
        })
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