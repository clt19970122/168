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
    html,body{
        background: #323436;
    }
</style>

<a href="<?php echo U('Users/bank_add');?>" class="user-addr_btn">+&nbsp;添加银行卡</a>
<div class="weui-panel weui-panel_access m32" style="background: none">
    <div class="weui-panel__bd user-bank">

        <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><div class="weui-media-box weui-media-box_appmsg user-bank-item blue">
                <!-- <div class="weui-media-box__hd">
                    <i class="weui-media-box__thumb iconfont ny-bank">&#xe601;</i>
                </div> -->
                <a href="<?php echo U('Users/bank_edit','id='.$v['id']);?>" class="weui-cell__bd" style='color:black;'>
                    <h4 class="weui-media-box__title"><?php echo (getBankName($v['bank'],$ban)); ?></h4>
                    <p class="weui-media-box__desc">
                        <?php echo ($v["name"]); ?>
                    </p>
                    <p class="weui-media-box__desc bank-number">
                        <?php echo ($v["card"]); ?>
                    </p>
                </a>
                <a href="javascript:;" class="weui-cell__ft"><i class="icon-dels showIOSDialog2"></i></a>
            </div><?php endforeach; endif; else: echo "" ;endif; ?>

    </div>
</div>

<div class="js_dialog iosDialog2" style="display: none;">
    <div class="weui-mask"></div>
    <div class="weui-dialog">
        <div class="weui-dialog__bd">确认删除银行卡？</div>
        <div class="weui-dialog__ft">
            <a href="javascript:;" class="weui-dialog__btn weui-dialog__btn_default">取消</a>
            <a href="javascript:dels('<?php echo ($v["id"]); ?>');" class="weui-dialog__btn weui-dialog__btn_primary">确定</a>
        </div>
    </div>
</div>


<script>

    var $iosDialog2 = $('.iosDialog2');
    $('.showIOSDialog2').on('click', function(){
        $iosDialog2.fadeIn(200);
    });
    function dels(ids) {
        var data = {id: ids};
        showPreLoading();
        $.ajax({
            url: "<?php echo U('Core/dels','model=acc_bank');?>", type: 'post',
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