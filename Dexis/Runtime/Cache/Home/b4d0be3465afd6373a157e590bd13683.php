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
    <input type="hidden" name="ids" value="<?php echo ($info["id"]); ?>"/>
    <div class="weui-cells weui-cells_form user-addr_add">

        <div class="weui-cell">
            <div class="weui-cell__hd"><label class="weui-label">收货人</label></div>
            <div class="weui-cell__bd">
                <input class="weui-input" type="text" name="name" value="<?php echo ($info["name"]); ?>"/>
            </div>
        </div>
        <div class="weui-cell">
            <div class="weui-cell__hd"><label class="weui-label">联系电话</label></div>
            <div class="weui-cell__bd">
                <input class="weui-input" type="text" name="phone" value="<?php echo ($info["phone"]); ?>"/>
            </div>
        </div>
        <div class="weui-cell">
            <div class="weui-cell__hd"><label class="weui-label">详细地址</label></div>
            <div class="weui-cell__bd">
                <input id="cascadePickerBtn" class="weui-input" type="text" name="province" readonly="" value="<?php echo ($info["pro"]); ?>" />
            </div>
        </div>
        <div class="weui-cell">
            <div class="weui-cell__hd"><label class="weui-label idcard">详细地址</label></div>
            <div class="weui-cell__bd">
                <textarea class="weui-textarea" name="street" placeholder="详细地址" rows="2"></textarea>
            </div>
        </div>
        <div class="weui-cell">
            <div class="weui-cell__hd"><label class="weui-label idcard">备注</label></div>
            <div class="weui-cell__bd">
                <textarea class="weui-textarea" name="idcard" rows="2"><?php echo ($info["idcard"]); ?></textarea>
            </div>
        </div>
        <div class="weui-cell weui-cell_switch">
            <div class="weui-cell__bd">设置默认地址<br/><p>注：每次下单时会使用该地址</p></div>
            <div class="weui-cell__ft">
                <input class="weui-switch" type="checkbox" name="def" value="1" <?php if(($info["def"]) == "1"): ?>checked<?php endif; ?>/>
            </div>
        </div>

    </div>
</form>

<div class="weui-btn-area">
    <a class="weui-btn weui-btn_primary" href="javascript:doOption();">保存</a>
</div>

<input type="hidden" id="view" value="<?php echo ($view); ?>">

<script>

    function doOption() {
        var temp = $("form").serializeArray();
        var data = objToArray(temp);
        var view =$('#view').val();
        showPreLoading();
        $.ajax({
            url: "<?php echo U('Core/edits','model=acc_addr');?>", type: 'post',
            data: data, dataType: "json",
            success: function (res) {
                hidePreLoading();
                if (res.status !== 1) {
                    return alert_wec(res.msg);
                }
                if(view){
                    window.location.href = "<?php echo U('Users/"+view+"');?>";
                }else {
                    window.location.href = "<?php echo U('Users/addr');?>";
                }
            }
        });
    }

</script>

<script src="/Public/home/areachoose/addrData.js"></script>
<script src="/Public/home/areachoose/addrDataOrigin.js"></script>

<script>
    // 级联选择器
    document.querySelector('#cascadePickerBtn').addEventListener('click', function () {
        let addrData = iosProvinces;
        weui.picker(addrData,{
            depth: 3,
            defaultValue: [22, 0, 3],
            onChange: function onChange(result) {
                // console.log(result);
            },
            onConfirm: function onConfirm(result) {
                var data = result[0].label+','+result[1].label+','+result[2].label;
                $('#cascadePickerBtn').val(data);
                // console.log(data);
            },
            id: 'cascadePicker'
        });
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