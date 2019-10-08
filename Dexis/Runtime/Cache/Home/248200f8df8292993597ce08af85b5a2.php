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
                <style>
    .weui-media-box__desc{
        color: #000;
    }
    .weui-panel__footer:before{
        content: " ";
        position: absolute;
        left: 0;
        right: 0;
        height: 1px;
        color: red;
    }
</style>
<div class="container" id="container">
    <div class="page navbar js_show">
    <div class="page__bd" style="height: 100%;">
        <div class="weui-tab">
            <div class="order-menu">
                <a href="<?php echo U('users/number_list',array('id'=>$user['id'],'type'=>1));?>" class="order-menu_item <?php if(($type) == "1"): ?>order-menu_item_on<?php endif; ?>">出售记录</a>
                <a href="<?php echo U('users/number_list',array('id'=>$user['id'],'type'=>2));?>" class="order-menu_item <?php if(($type) == "2"): ?>order-menu_item_on<?php endif; ?>">转让记录</a>
                <a href="<?php echo U('users/rebate_list',array('id'=>$user['id'],'type'=>3));?>" class="order-menu_item <?php if(($type) == "3"): ?>order-menu_item_on<?php endif; ?>">佣金记录</a>
            </div>

            <div class="weui-tab__panel">
                <!--卖-->
                <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="weui-panel">
                        <div class="weui-panel__bd">
                            <div class="weui-media-box weui-media-box_text">
                                <!--<h4 class="weui-media-box__title">-￥<em class="num">56.5</em></h4>-->
                                <p class="weui-media-box__desc" style="font-size: 1.6rem">订单号：<?php echo ($vo["sn"]); ?></p>
                                <div class="weui-flex">
                                    <?php if(($type) == "1"): ?><div class="weui-flex__item"><p class="weui-media-box__desc" style="margin-top: 8px">下单用户：<?php echo ($vo["buyer"]); ?></p></div><?php endif; ?>
                                    <?php if(($type) == "2"): ?><div class="weui-flex__item"><p class="weui-media-box__desc" style="margin-top: 8px">进货人：<?php echo ($vo["buyer"]); ?></p></div><?php endif; ?>
                                    <div class="weui-flex__item"><p class="weui-media-box__desc" style="margin-top: 8px">数量：<?php echo ($vo["nums"]); ?></p></div>
                                </div>

                                <div class="weui-flex" style="justify-content: space-between">
                                    <ul class="weui-media-box__info">
                                        <li class="weui-media-box__info__meta">交易时间：<?php echo (date('Y-m-d H:i:s' ,$vo["time"])); ?></li>
                                    </ul>
                                </div>
                                <div class="weui-panel__footer">
                                    <p style="float: right;font-size: 13px;padding: 10px 0">支付金额：<span style="color: red;font-size: 1.8rem">￥<?php echo ($vo["pay"]); ?></span></p>
                                </div>
                            </div>
                        </div>
                    </div><?php endforeach; endif; else: echo "" ;endif; ?>
                <!--转--><!--
                <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="weui-panel">
                        <div class="weui-panel__bd">
                            <div class="weui-media-box weui-media-box_text">
                                &lt;!&ndash;<h4 class="weui-media-box__title">-￥<em class="num">56.5</em></h4>&ndash;&gt;
                                <p class="weui-media-box__desc" style="font-size: 1.6rem">订单号：<?php echo ($v["sn"]); ?></p>
                                <div class="weui-flex">
                                    <div class="weui-flex__item"><p class="weui-media-box__desc" style="margin-top: 8px">下单用户：<?php echo ($v["buyer"]); ?></p></div>
                                    <div class="weui-flex__item"><p class="weui-media-box__desc" style="margin-top: 8px">数量：<?php echo ($v["nums"]); ?></p></div>
                                </div>

                                <div class="weui-flex" style="justify-content: space-between">
                                    <ul class="weui-media-box__info">
                                        <li class="weui-media-box__info__meta">交易时间：<?php echo (date('Y-m-d H:i:s' ,$v["times"])); ?></li>
                                    </ul>
                                </div>
                                <div class="weui-panel__footer">
                                    <p style="float: right;font-size: 13px;padding: 10px 0">支付金额：<span style="color: red;font-size: 1.8rem">￥0</span></p>
                                </div>
                            </div>
                        </div>
                    </div><?php endforeach; endif; else: echo "" ;endif; ?>-->
                </div>
            </div>
        </div>
    </div>
</div>

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