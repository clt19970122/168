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
                <?php if($info['gid'] != 8): ?><div class="weui-panel order-info">
    <div class="weui-panel__bd">
        <div class="weui-media-box weui-media-box_small-appmsg">
            <div class="weui-cells">

                <a class="weui-cell weui-cell_access" href="<?php echo U('Order/trans','sn='.$info['sn']);?>">
                    <div class="weui-cell__hd"><i class="icon-cars"></i></div>
                    <div class="weui-cell__bd weui-cell_primary">
                        <p class="trans"><?php echo ($info["tag"]); ?></p>
                        <!--p class="times">2017年10月1日 19:50:06</p-->
                    </div>
                    <span class="weui-cell__ft"></span>
                </a>

            </div>
        </div>
    </div>
</div><?php endif; ?>

<div class="weui-panel order-info order-addr">
    <div class="weui-panel__bd">
        <div class="weui-media-box weui-media-box_small-appmsg">
            <div class="weui-cells">

                <a class="weui-cell weui-cell_access" href="javascript:;">
                    <div class="weui-cell__hd"><i class="icon-locs"></i></div>
                    <div class="weui-cell__bd weui-cell_primary">
                        <p class="trans"><?php echo (getUserAddr($info['addr'],'name',$name)); ?>  <?php echo (getUserAddr($info['addr'],'phone',$name)); ?></p>
                        <p class="times">地址：<?php echo (getUserAddr($info['addr'],'street',$name)); ?></p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="weui-panel order-list order-info_goods">
    <div class="weui-panel__bd">
        <a href="javascript:;" class="weui-media-box weui-media-box_appmsg">
            <div class="weui-media-box__hd">
                <img class="weui-media-box__thumb" src="/Public/uploads/goods/<?php echo ($info["gimgs"]); ?>"/>
            </div>
            <div class="weui-media-box__bd">
                <h4 class="weui-media-box__title"><?php echo ($info["gname"]); ?></h4>
                <p class="weui-media-box__desc"><?php echo (date('Y年m月d日 H:i:s',$info["times"])); ?> 数量:<?php echo ($info["gnums"]); ?></p>
                <p class="weui-media-box__price">¥<?php echo ($info["money"]); ?><font>元</font></p>
            </div>
        </a>
    </div>
    <div class="weui-panel__ft">
        <div class="weui-cell weui-cell_link">
            <a class="weui-cell__bd">
                联系客服
            </a>
        </div>    
    </div>
</div>

<div class="weui-form-preview order-info_item">
    <div class="weui-form-preview__bd">
        <div class="weui-form-preview__item">
            <label class="weui-form-preview__label">订单状态</label>
            <span class="weui-form-preview__value">
                <?php echo ($info['status']==0?'待支付':''); ?>
                <?php echo ($info['status']==1?($info['gid']==8?'已入库':'待发货'):''); ?>
                <?php echo ($info['status']==2?'待收货':''); ?>
                <?php echo ($info['status']==3?'已完成':''); ?>
                <?php echo ($info['status']==4?'已取消':''); ?>
                <?php echo ($info['status']==5?'自提':''); ?>
            </span>
        </div>
        <div class="weui-form-preview__item">
            <label class="weui-form-preview__label">下单时间</label>
            <span class="weui-form-preview__value"><?php echo (date('Y年m月d日 H:i:s',$info["times"])); ?></span>
        </div>
        <div class="weui-form-preview__item">
            <label class="weui-form-preview__label">支付方式</label>
            <span class="weui-form-preview__value"><?php echo ($info['paytype']==4?'余额':($info['paytype']==1?'微信':'无')); ?></span>
        </div>
        <div class="weui-form-preview__item">
            <label class="weui-form-preview__label">订单号</label>
            <span class="weui-form-preview__value"><?php echo ($info["sn"]); ?></span>
        </div>
        <div class="weui-form-preview__item">
            <label class="weui-form-preview__label">商品总额</label>
            <span class="weui-form-preview__value">¥<?php echo ($info["gprice"]); ?>*<?php echo ($info["gnums"]); ?></span>
        </div>
        <div class="weui-form-preview__item">
            <label class="weui-form-preview__label">+运费</label>
            <!--<span class="weui-form-preview__value">￥<?php echo ($info["trans_pay"]); ?></span>-->
            <?php if($info['gid'] != 8): ?><span class="weui-form-preview__value"  style="color:red;">包邮</span>
                <?php else: ?>
                <span class="weui-form-preview__value"  style="color:red;">补充库存后自提付款</span><?php endif; ?>
            <!--<span class="weui-form-preview__value" style="color:red;">运费自理，货到付款</span>-->
        </div>
        <div class="weui-form-preview__item">
        <label class="weui-form-preview__label">订单备注</label>
        <span class="weui-form-preview__value" style="text-align: left;color: #005dac"><?php echo ($info["remakes"]); ?></span>
    </div>
    </div>
    <div class="weui-form-preview__ft">
        <a class="weui-form-preview__btn" href="javascript:;">总费用</a>
        <a class="weui-form-preview__btn">¥<?php echo ($info["money"]); ?><font>元</font></a>
    </div>
</div>

<?php if($info['status'] == 0): ?><div class="order-btns">
        <a href="javascript:doOption();">取消订单</a>
        <a href="<?php echo U('Malls/payer');?>?sn=<?php echo ($info["sn"]); ?>" class="full">去支付</a>
    </div><?php endif; ?>

<?php if($info['status'] > 1): ?><div class="order-btns">
        <a href="<?php echo U('Malls/buyer','sn='.$info['gsn'].'&nums='.$info['gnums']);?>" class="full">再次购买</a>
    </div><?php endif; ?>


<script>

    function doOption() {
        var data = {ids: "<?php echo ($info["id"]); ?>", status: 4};
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