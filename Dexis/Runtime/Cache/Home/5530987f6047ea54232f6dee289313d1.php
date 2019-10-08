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
    <input type="hidden" name="sn" value="<?php echo ($get["nsn"]); ?>"/>
    <input type="hidden" name="gsn" value="<?php echo ($get["sn"]); ?>"/>
     <?php if($get["sn"] == 815526127943355165): ?><input type="hidden" name="gnums" value="<?php echo ($get['nums']+$get['nums2']+$get['nums3']); ?>"/>
        <input type="hidden" name="money"  value="<?php echo ($info['price']+$get['freight']); ?>"/>
        <?php else: ?>
        <input type="hidden" name="gnums" value="<?php echo ($get['nums']); ?>"/>
        <input type="hidden" name="money"  value="<?php echo ($info['price']*$get['nums']+$get['freight']); ?>"/><?php endif; ?>
    <input type="hidden" name="fees" value="<?php echo ($get["freight"]); ?>"/>
    <input type="hidden" name="numser" value="<?php echo ($get["numser"]); ?>"/>
    <!-- <input type="hidden" name="money"  value="<?php echo ($info['price']*$get['nums']+$get['freight']); ?>"/> -->
    <!-- <input type="hidden" name="gnums" value="<?php echo ($get["nums"]); ?>"/> -->
    <input type="hidden" name="addr" value="<?php echo ($deft["id"]); ?>"/>
    <input type="hidden" name="yunfei" value="<?php echo ($price); ?>"/>
    <input type="hidden" name="add_stock" id="is_add" value="<?php echo ($status); ?>"/>
<!--     <input type="hidden" name="money"  value="<?php echo ($info['price']*$get['nums']); ?>"/>
 -->    <?php if($status != 9): ?><div class="weui-panel order-info order-addr">
            <div class="weui-panel__bd">
                <div class="weui-media-box weui-media-box_small-appmsg">
                    <div class="weui-cells">

                            <a href="<?php echo U('Users/addr','gsn='.$get['sn'].'&nums='.$get['nums']);?>" class="weui-cell weui-cell_access">
                                <div class="weui-cell__bd weui-cell_primary">
                                    <p>添加收货地址</p>
                                </div>
                            </a>
                        <?php if($deft != null): ?><a class="weui-cell  " id="showPicker">
                                <div class="weui-cell__hd"><i class="icon-locs"></i></div>
                                <div class="weui-cell__bd weui-cell_primary">
                                    <p class="trans"><?php echo ($deft["name"]); ?>  <?php echo ($deft["phone"]); ?></p>
                                    <p class="times">地址：<?php echo ($deft["street"]); ?></p>
                                </div>
                            </a><?php endif; ?>
                        <?php if($deft == null and $count > 0): ?><a class="weui-cell weui-cell_access" id="showPicker">
                                <div class="weui-cell__hd"><i class="icon-locs"></i></div>
                                <div class="weui-cell__bd weui-cell_primary">
                                    <p class="trans">请选择</p>
                                    <p class="times">收货地址</p>
                                </div>
                            </a><?php endif; ?>
                    </div>
                </div>
            </div>
        </div><?php endif; ?>


    <div class="weui-panel order-list order-info_goods">
        <div class="weui-panel__bd">
            <a href="javascript:;" class="weui-media-box weui-media-box_appmsg">
                <div class="weui-media-box__hd">
                    <img class="weui-media-box__thumb" src="/Public/uploads/goods/<?php echo (getGoodsImg($info['gsn'],$img)); ?>"/>
                </div>
                <div class="weui-media-box__bd">
                    <h4 class="weui-media-box__title"><?php echo ($info["name"]); ?></h4>
                    <p class="weui-media-box__desc"><?php echo (date('Y-m-d H:i:s',$get["times"])); ?> 数量:<?php if($get["sn"] == 815526127943355165 ): echo ($get["numser"]); else: echo ($get["nums"]); endif; ?></p>
                    <p class="weui-media-box__price">¥<?php if($get["sn"] == 815526127943355165 ): echo ($info['price']); else: echo ($info['price']*$get['nums']); endif; ?><font>元</font></p>
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

</form>

<div class="weui-form-preview order-info_item">
    <div class="weui-form-preview__bd">
        <div class="weui-form-preview__item">
            <label class="weui-form-preview__label">下单时间</label>
            <span class="weui-form-preview__value"><?php echo (date('Y-m-d H:i:s',$get["times"])); ?></span>
        </div>
        <div class="weui-form-preview__item">
            <label class="weui-form-preview__label">订单号</label>
            <span class="weui-form-preview__value"><?php echo ($get["nsn"]); ?></span>
        </div>
        <div class="weui-form-preview__item">
            <label class="weui-form-preview__label">商品总额</label>
            <span class="weui-form-preview__value">¥<?php if($get["sn"] == 815526127943355165 ): echo ($info['price']); else: echo ($info['price']*$get['nums']); endif; ?></span>
        </div>
        <?php if($get["type"] == 2): ?><div class="weui-form-preview__item">
                <label class="weui-form-preview__label">+运费</label>
                <!--<span class="weui-form-preview__value">￥<?php echo ($price); ?></span>-->
                <!--<span class="weui-form-preview__value"  style="color:red;">运费自理，货到付款</span>-->
                <?php if($get["type"] == 2): ?><span class="weui-form-preview__value"  style="color:red;">包邮</span>
                    <?php else: ?>
                    <span class="weui-form-preview__value"  style="color:red;">补充库存后自提付款</span><?php endif; ?>
            </div><?php endif; ?>
         <?php if($get["sn"] == 815526127943355165 or $get["sn"] == 615583172840856677 ): ?><div class="weui-form-preview__item">
                <label class="weui-form-preview__label">+运费</label>
                <!--<span class="weui-form-preview__value">￥<?php echo ($price); ?></span>-->
                <!--<span class="weui-form-preview__value"  style="color:red;">运费自理，货到付款</span>-->
                <?php if($info["price"] > 100): ?><span class="weui-form-preview__value"  style="color:red;">包邮</span>
                    <?php else: ?>
                    <span class="weui-form-preview__value"  style="color:red;"><?php echo ($get["freight"]); ?></span><?php endif; ?>
            </div><?php endif; ?>
    </div>
    <div class="weui-form-preview__ft">
        <a class="weui-form-preview__btn" href="javascript:">总费用</a>
        <a class="weui-form-preview__btn">¥<?php if($get["sn"] == 815526127943355165 or $get["sn"] == 615583172840856677 ): echo ($info['price']+$get['freight']); else: echo ($info['price']*$get['nums']); endif; ?><font>元</font></a>
    </div>
    <!--文本域备注-->
    <?php if($get["type"] == 2): ?><div class="weui-cell">
            <div class="weui-cell__bd">
                <textarea name="remakes" id="remakes" class="weui-textarea" placeholder="可输入订单备注信息" rows="5" style="font-size: 2rem"></textarea>
                <!--<div class="weui-textarea-counter"><span>0</span>/200</div>-->
            </div>
        </div><?php endif; ?>
</div>

<div class="order-btns">
    <a href="javascript:doOption();" class="full">去支付</a>
</div>

<script>
    var addr = <?php echo ($addr); ?>;
    $('#showPicker').on('click', function () {
        weui.picker(<?php echo ($addrshow); ?>, {
            onConfirm: function (result) {
                var res = addr[result];
                var names = res["name"] + "  " + res["phone"];
                var addrs = "地址：" + res["street"];
                $("input[name='addr']").val(res["id"]);
                $("#showPicker").find("p[class='trans']").html(names);
                $("#showPicker").find("p[class='times']").html(addrs);
            }
        });
    });

    function doOption() {
        var temp = $("form").serializeArray();
        var data = objToArray(temp);
        var statuss =$('#is_add').val();
        //产品分类doOption
        data['type'] ="<?php echo ($get["type"]); ?>";
        data['remakes'] =$('#remakes').val();
        if(!statuss){
            if ($.trim(data.addr) === null || $.trim(data.addr) === "") {
                return alert_wec("请选择地址");
            }
        }
        ////
        showPreLoading();
        $.ajax({
            url: "<?php echo U('Malls/doBuyerOpt');?>", type: 'post',
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