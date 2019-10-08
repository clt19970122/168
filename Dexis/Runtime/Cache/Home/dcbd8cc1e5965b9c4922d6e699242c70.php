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
                <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
</head>
<body>
<div class="weui-panel weui-panel_access credit-card-container" style="background: none;">
    <div class="weui-panel__bd user-bank">
        <a href="javascript:creditCard();">
            <div class="weui-media-box weui-media-box_appmsg user-bank-item credit-card-item orange">
                <div class="weui-cell__bd">
                    <h1>申请信用卡</h1>
                    <p class="weui-media-box__desc">
                        轻松消费，创业无忧
                    </p>
                </div>
            </div>
        </a>
       <!--  <a href="javascript:stageCard();">
            <div class="weui-media-box weui-media-box_appmsg user-bank-item credit-card-item gold">
                <div class="weui-cell__bd">
                    <h1>消费分期</h1>
                    <p class="weui-media-box__desc">
                        轻松分期消费
                    </p>
                </div>
            </div>
        </a> -->
        <a href="javascript:;" id="showIOSActionSheet">
            <div class="weui-media-box weui-media-box_appmsg user-bank-item credit-card-item blue">
                <div class="weui-cell__bd">
                    <h1>信用卡提现</h1>
                    <p class="weui-media-box__desc">
                        信用卡提现，费率低
                    </p>
                </div>
            </div>
        </a>
        <a href="javascript:repayment();" >
            <div class="weui-media-box weui-media-box_appmsg user-bank-item credit-card-item green">
                <div class="weui-cell__bd">
                    <h1>信用卡还款</h1>
                    <p class="weui-media-box__desc">
                        轻松分期还款
                    </p>
                </div>
            </div>
        </a>
    </div>
</div>
<!--分期弹窗-->
<div>
    <div class="weui-mask" id="iosMask" style="display: none"></div>
    <div class="weui-actionsheet" id="iosActionsheet">
        <div class="weui-actionsheet__menu">
            <div class="weui-actionsheet__cell"><a href="javascript:regularMembers();">普通会员入口</a></div>
            <div class="weui-actionsheet__cell"><a href="javascript:vipMembers();">VIP会员入口</a></div>
        </div>
        <div class="weui-actionsheet__action">
            <div class="weui-actionsheet__cell" id="iosActionsheetCancel">取消</div>
        </div>
    </div>
</div>
<!--END 分期弹窗-->
<script>
    function creditCard() {
        showPreLoading();
        $.ajax({
            url: "<?php echo U('Index/credit_card');?>",
            type: 'post',
            dataType: "json",
            success: function (res) {
                hidePreLoading();
                if (res.status !== 1) {
                    alert(res.msg);
                    window.location.href = res.data;
                }
                window.location.href = res.data;
            }
        });
    }
    //普通会员入口
    function regularMembers() {
        var memberid="<?php echo ($data["memberId"]); ?>";
        console.log(memberid);
        showPreLoading();
        $.ajax({
            url: "<?php echo U('Index/certification');?>",
            type: 'post',
            data: {memberid:memberid},
            dataType: "json",
            success: function (res) {
                hidePreLoading();
                console.log(res.status,res.data);
               if (res.status === 0) {
                  alert(res.msg);
                  window.location.href = res.data;
                }else if(res.status === 2){
                  alert(res.msg);
                }else {
                  window.location.href = 'http://akk.028wkf.cn/pay/speed2/quick-pay?token=<?php echo ($data["token"]); ?>&type=QUICK';
                }
            }
        });
    }
    //vip会员入口
    function vipMembers() {
        var memberid="<?php echo ($data["memberId"]); ?>";
        showPreLoading();
        $.ajax({
            url: "<?php echo U('Index/certification');?>",
            type: 'post',
            data: {memberid:memberid},
            dataType: "json",
            success: function (res) {
                hidePreLoading();
                console.log(res.status,res.data);
                 if (res.status === 0) {
                  alert(res.msg);
                  window.location.href = res.data;
                }else if(res.status === 2){
                  alert(res.msg);
                }else {
                  window.location.href = 'http://akk.028wkf.cn/pay/speed2/quick-pay?token=<?php echo ($data["token"]); ?>&type=QUICK';
                }
            }
        });
    }
    //还款
    function repayment() {
        var memberid="<?php echo ($data["memberId"]); ?>";
        showPreLoading();
        $.ajax({
            url: "<?php echo U('Index/repayment');?>",
            type: 'post',
            dataType: "json",
            success: function (res) {
                hidePreLoading();
                console.log(res.status,res.data);
                if (res.status !== 1) {
                    window.location.href = res.data;
                }else {
                    window.location.href = 'http://akk.028wkf.cn/pay/hlb-repay/homepage?memberId=<?php echo ($data["memberId"]); ?>&providerID=<?php echo ($data["providerId"]); ?>';
                }
            }
        });
    }
    //

    function stageCard() {
        showPreLoading();
        $.ajax({
            url: "<?php echo U('Index/stageCard_card');?>",
            type: 'post',
            dataType: "json",
            success: function (res) {
                hidePreLoading();
                if (res.status !== 1) {
                    alert(res.msg);
                    window.location.href = res.data;
                }
                window.location.href = res.data;
            }
        });
    }
</script>
<script type="text/javascript">
    // ios
    $(function(){
        var $iosActionsheet = $('#iosActionsheet');
        var $iosMask = $('#iosMask');

        function hideActionSheet() {
            $iosActionsheet.removeClass('weui-actionsheet_toggle');
            $iosMask.fadeOut(200);
        }

        $iosMask.on('click', hideActionSheet);
        $('#iosActionsheetCancel').on('click', hideActionSheet);
        $("#showIOSActionSheet").on("click", function(){
            $iosActionsheet.addClass('weui-actionsheet_toggle');
            $iosMask.fadeIn(200);
        });
    });


</script>
</body>
</html>
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