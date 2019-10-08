<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
    <head>
        <title><?php echo ($conf_title); ?></title>
        <meta charset="UTF-8">

        <meta HTTP-EQUIV="pragma" CONTENT="no-cache">
        <meta HTTP-EQUIV="Cache-Control" CONTENT="no-store, must-revalidate">
        <meta HTTP-EQUIV="expires" CONTENT="Wed, 26 Feb 1997 08:21:57 GMT">
        <meta HTTP-EQUIV="expires" CONTENT="0">

        <meta name="viewport" content="width=device-width, initial-scale=1.0,maximum-scale=1.0,user-scalable=0">
        <meta name="format-detection" content="telephone=no" />
        <link href="/Public/libs/weui/style/weui.min.css?v=1550124091" rel="stylesheet">
        <link href="/Public/libs/weui/style/weui-picker.css" rel="stylesheet">
        <link href="/Public/home/css/app.css?v=1550124091" rel="stylesheet">
        <link href="/Public/home/css/icon.css?v=1550124091" rel="stylesheet">
        <script src="/Public/libs/jquery.min.js" charset="utf-8"></script>
        <script src="/Public/libs/weui/js/weui.min.js" charset="utf-8"></script>
        <script src="/Public/libs/common.js" charset="utf-8"></script>
        <!-- <script src="/Public/libs/city.js" charset="utf-8"></script> -->
        <script src='http://res.wx.qq.com/open/js/jweixin-1.0.0.js'></script>

        <link rel="stylesheet" type="text/css" href="/Public/home/areachoose/jsdaima.com.css">
        <link rel="stylesheet" href="/Public/home/areachoose/larea.css">
        <script src="/Public/home/areachoose/lareadata1.js"></script>
        <script src="/Public/home/areachoose/lareadata2.js"></script>
        <script src="/Public/home/areachoose/larea.js"></script>


  
        <script>
            wx.config({
                debug: false,
                appId: '<?php echo ($ticket["appid"]); ?>',
                timestamp: '<?php echo ($ticket["timestamp"]); ?>',
                nonceStr: '<?php echo ($ticket["noncestr"]); ?>',
                signature: '<?php echo ($ticket["signature"]); ?>',
                jsApiList: ['onMenuShareTimeline', 'onMenuShareAppMessage']
            });

        </script>
<!--         <link rel="stylesheet" href="../../../../../xamppfiles/htdocs/yagebu/Public/home/css/app.css">
 -->    
<!--<link rel="stylesheet" href="../../../../../xamppfiles/htdocs/yagebu/Public/home/css/app.css">-->
        <link rel="stylesheet" href="/Public/home/css/app.css">
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
                
<div class="order-menu">
    <a href="<?php echo U('users/getStockList');?>" class="order-menu_item <?php if(($st) == "10"): ?>order-menu_item_on<?php endif; ?>">全部</a>
    <a href="<?php echo U('users/getStockList','st=1');?>" class="order-menu_item <?php if(($st) == "1"): ?>order-menu_item_on<?php endif; ?>">待发货</a>
    <a href="<?php echo U('users/getStockList','st=3');?>" class="order-menu_item <?php if(($st) == "3"): ?>order-menu_item_on<?php endif; ?>">待收货</a>
</div>
        <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><ul class="weui-payrec">
                <div class="weui-pay-m">
                    <li class="weui-pay-order">
                        <dl class="weui-pay-line">
                            <dd class="weui-pay-name"><?php echo ($vo['name']); ?></dd>
                            <dt class="weui-pay-label">订 单 号：</dt><dd class="weui-pay-e"><?php echo ($vo['sn']); ?></dd>
                        </dl>
                        <dl class="weui-pay-line"><dt class="weui-pay-label">发货地址：</dt><dd class="weui-pay-e"><?php echo ($vo['address']); ?></dd></dl>
                        <dl class="weui-pay-line"><dt class="weui-pay-label">申请时间：</dt><dd class="weui-pay-e"><?php echo (date('Y年m月d日 H:i:s',$vo['times'])); ?></dd></dl>
                        <dl class="weui-pay-line"><dt class="weui-pay-label">发货数量：</dt><dd class="weui-pay-e"><?php echo ($vo['nums']); ?></dd></dl>
                        <dl class="weui-pay-line"><dt class="weui-pay-label">支付运费：</dt><dd class="weui-pay-e">￥<?php echo ($vo['trans_pay']); ?></dd></dl>
                        <dl class="weui-pay-line"><dt class="weui-pay-label">备注信息：</dt><dd class="weui-pay-e"><?php echo ($vo['remakes']); ?></dd></dl>
                        <div class="weui-pay-area">
                            <?php if($vo["have_pay"] == 0): ?><a href="javascript:doOption('<?php echo ($vo["id"]); ?>');" class="weui-pay-c">取消订单</a>
                                <a href="javascript:toDopay('<?php echo ($vo["sn"]); ?>');" class="weui-pay-v">去支付</a>
                                <?php else: ?>
                                <?php if($vo["status"] == 3): ?><a href="<?php echo U('users/getdrs_trans',array('sn'=>$vo['sn']));?>" class="weui-pay-v"><i class="icon-cars"></i>&nbsp;查看物流信息</a>
                                    <hr>
                                    <a href="javascript:sureGet(<?php echo ($vo["id"]); ?>);" class="weui-pay-v"><font color="#4169e1">确认收货</font></a>
                                    <?php elseif($vo["status"] == 1): ?>
                                    <a href="javascript:;" class="weui-pay-v">待发货</a>
                                    <?php elseif($vo["status"] == 2): ?>
                                    <a href="javascript:;" class="weui-pay-v"><font color="red">未通过</font></a>
                                    <?php elseif($vo["status"] == 4): ?>
                                    <a href="javascript:;" class="weui-pay-v">系统自提</a>
                                    <?php elseif($vo["status"] == 5): ?>
                                    <a href="javascript:;" class="weui-pay-v"><font color="red">已取消</font></a>
                                    <?php elseif($vo["status"] == 6): ?>
                                    <a href="javascript:;" class="weui-pay-v"><font color="#6495ed">已确认收货</font></a><?php endif; endif; ?>
                        </div>
                    </li>
                </div>
            </ul><?php endforeach; endif; else: echo "" ;endif; ?>

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
            <div style="height:6rem">&nbsp;</div>
            <div class="weui-tabbar">

                <?php if($tabbar == 'Index'): ?><a href="<?php echo U('Index/index');?>" class="weui-tabbar__item weui-bar__item_on">
                        <!--<img src="/Public/home/imgs/tabbar_home_on.png" alt="" class="weui-tabbar__icon">-->
                        <span class="iconfont nav-icon-bottom">&#xe638;</span>
                        <p class="weui-tabbar__label">首页</p>
                    </a><?php endif; ?>
                <?php if($tabbar != 'Index'): ?><a href="<?php echo U('Index/index');?>" class="weui-tabbar__item">
                        <!--<img src="/Public/home/imgs/tabbar_home.png" alt="" class="weui-tabbar__icon">-->
                        <span class="iconfont nav-icon-bottom">&#xe638;</span>
                        <p class="weui-tabbar__label">首页</p>
                    </a><?php endif; ?>


                <?php if($tabbar == 'Malls'): ?><a href="<?php echo U('Malls/index');?>" class="weui-tabbar__item weui-bar__item_on">
                        <span class="iconfont nav-icon-bottom">&#xe63a;</span>
                        <p class="weui-tabbar__label">商城</p>
                    </a><?php endif; ?>
                <?php if($tabbar != 'Malls'): ?><a href="<?php echo U('Malls/index');?>" class="weui-tabbar__item">
                        <span class="iconfont nav-icon-bottom">&#xe63a;</span>
                        <p class="weui-tabbar__label">商城</p>
                    </a><?php endif; ?>

                <?php if($tabbar == 'Wcix'): ?><a href="<?php echo U('Wcix/share',array('sysid'=>$sysid));?>" class="weui-tabbar__item weui-bar__item_on">
                        <span class="iconfont nav-icon-bottom">&#xe654;</span>
                        <p class="weui-tabbar__label">分享</p>
                    </a><?php endif; ?>
                <?php if($tabbar != 'Wcix'): ?><a href="<?php echo U('Wcix/share',array('sysid'=>$sysid));?>" class="weui-tabbar__item">
                        <span class="iconfont nav-icon-bottom">&#xe654;</span>
                        <p class="weui-tabbar__label">分享</p>
                    </a><?php endif; ?>

                <?php if($tabbar == 'Order'): ?><a href="<?php echo U('Order/index');?>" class="weui-tabbar__item weui-bar__item_on">
                        <!--<img src="/Public/home/imgs/tabbar_order_on.png" alt="" class="weui-tabbar__icon">-->
                        <span class="iconfont nav-icon-bottom">&#xe63c;</span>
                        <p class="weui-tabbar__label">订单</p>
                    </a><?php endif; ?>
                <?php if($tabbar != 'Order'): ?><a href="<?php echo U('Order/index');?>" class="weui-tabbar__item">
                        <span class="iconfont nav-icon-bottom">&#xe63c;</span>
                        <p class="weui-tabbar__label">订单</p>
                    </a><?php endif; ?>

                <?php if($tabbar == 'Users'): ?><a href="<?php echo U('Users/index');?>" class="weui-tabbar__item weui-bar__item_on">
                        <span class="iconfont nav-icon-bottom">&#xe63d;</span>
                        <p class="weui-tabbar__label">我的</p>
                    </a><?php endif; ?>
                <?php if($tabbar != 'Users'): ?><a href="<?php echo U('Users/index');?>" class="weui-tabbar__item">
                        <span class="iconfont nav-icon-bottom">&#xe63d;</span>
                        <p class="weui-tabbar__label">我的</p>
                    </a><?php endif; ?>

            </div>
        </div>
        <script>

            function doAlert() {
                showPreLoading();
                $.ajax({
                    url: "<?php echo U('Index/checks','&redi=1');?>", type: 'post', dataType: "json",
                    success: function (res) {
                        hidePreLoading();
                        if (res.status !== 1) {
                            return alert_wec(res.msg);
                        }
                        window.location.href = "<?php echo U('Wcix/share','sysid='.$conf_user['sysid']);?>";
                    }
                });
            }

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
        <!--百度流量统计-->
        <script>
            var _hmt = _hmt || [];
            (function() {
                var hm = document.createElement("script");
                hm.src = "https://hm.baidu.com/hm.js?3347a54c783fd56da7bf36a4555184a4";
                var s = document.getElementsByTagName("script")[0];
                s.parentNode.insertBefore(hm, s);
            })();
        </script>
    </body>
</html>