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
    <div class="weui-cells weui-cells_form user-addr_add">

        <div class="weui-cell">
            <div class="weui-cell__hd"><label class="weui-label">收货人</label></div>
            <div class="weui-cell__bd">
                <input class="weui-input" type="text" name="name" placeholder="请输入收货人姓名"/>
            </div>
        </div>
        <div class="weui-cell">
            <div class="weui-cell__hd"><label class="weui-label">联系电话</label></div>
            <div class="weui-cell__bd">
                <input class="weui-input" type="text" name="phone" placeholder="请输入联系人电话"/>
            </div>
        </div>
        <div class="weui-cell">
            <div class="weui-cell__hd"><label class="weui-label">选择省市</label></div>
            <div class="weui-cell__bd">
                <!-- <div class="content-block">
                     <input id="cascadePickerBtn" type="text" class="weui-input" name="province" readonly="" placeholder="点击选择省市区" value=""/>
                     <a href="javascript:" style="display:block;width: 100%" id="cascadePickerBtn" class="weui-input">点击选择省市区</a>
                    <input id="cascadePicker" type="hidden" class="weui-input" name="province"  value=""/>
                </div> -->
                <div class="weui-cell__bd">
                <input id="cascadePickerBtn" class="weui-input" type="text" name="province" readonly="" placeholder="点击选择省市区" value="" />
            </div>
            </div>
        </div>
        <div class="weui-cell">
            <div class="weui-cell__hd"><label class="weui-label idcard">详细地址</label></div>
            <div class="weui-cell__bd">
                <textarea id="street" class="weui-textarea" name="street" placeholder="详细地址" rows="2"></textarea>
            </div>
        </div>
       <!-- <div class="weui-cell">
            <div class="weui-cell__hd"><label class="weui-label">详细地址</label></div>
            <div class="weui-cell__bd">
                <input class="weui-input" type="text" name="street" placeholder="请输入收货人地址"/>
            </div>
        </div>-->
        <div class="weui-cell">
            <div class="weui-cell__hd"><label class="weui-label idcard">备注</label></div>
            <div class="weui-cell__bd">
                <textarea class="weui-textarea" name="idcard" placeholder="如有特殊要求请备注" rows="2"></textarea>
            </div>
        </div>
        <div class="weui-cell weui-cell_switch">
            <div class="weui-cell__bd">设置默认地址<br/><p>注：每次下单时会使用该地址</p></div>
            <div class="weui-cell__ft">
                <input class="weui-switch" type="checkbox" checked="checked" name="def" value="1"/>
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
            if($("#cascadePickerBtn").val() == ""){
            alert_wec("请选择省市区");
            return false;
        }else if($("#street").val() == ""){
            alert_wec("请填写详细地址");
            return false;
        };
        showPreLoading();
        $.ajax({
            url: "<?php echo U('Core/addon','model=acc_addr');?>", type: 'post',
            data: data, dataType: "json",
            success: function (res) {
                hidePreLoading();
                if (res.status !== 1) {
                    return alert_wec(res.msg);
                }
                //
                var sn = "<?php echo ($get["sn"]); ?>";
                var nums = "<?php echo ($get["nums"]); ?>";
                var type = "<?php echo ($get["type"]); ?>";
                //
                if (type !== "" ) {
                    window.location.href = "<?php echo U('Index/financial_centre');?>";
                    return;
                }
                //
                if (sn !== "" && nums !== "") {
                    window.location.href = "<?php echo U('Malls/buyer','sn='.$get['sn'].'&nums='.$get['nums']);?>";
                    return;
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

   <script src="/Public/home/areachoose/weui.min.js"></script>
   <script src="https://cdn.bootcss.com/jquery/3.3.1/jquery.js"></script>
        <script src="/Public/home/areachoose/addrData.js"></script>
        <script src="/Public/home/areachoose/addrDataOrigin.js"></script>

<script>
    $("#cascadePickerBtn").click(function(){
    
        var addrData = iosProvinces;
        weui.picker(addrData,{
            depth: 3,
            defaultValue: [22, 0, 3],
          /*  onChange: function onChange(result) {
                // console.log(result);
            },*/
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