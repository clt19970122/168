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
                <form>
    <input type="hidden" name="uid" value="<?php echo ($uid); ?>"/>
    <input type="hidden" name="name" id="input1" value="<?php echo ($deft["name"]); ?>"/>
    <input type="hidden" name="phone"id="input2" value="<?php echo ($deft["phone"]); ?>"/>
    <input type="hidden" name="address" id="input3" value="<?php echo ($deft["street"]); ?>"/>
    <input type="hidden" name="addr" id="input4" value="<?php echo ($deft["id"]); ?>"/>
    <div class="weui-cells weui-cells_form user-addr_add">

       <!-- <div class="weui-cell">
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
            <div class="weui-cell__hd"><label class="weui-label">详细地址</label></div>
            <div class="weui-cell__bd">
                <input class="weui-input" type="text" name="address" placeholder="请输入收货人地址"/>
            </div>
        </div>-->

        <div class="weui-panel order-info order-addr">
            <div class="weui-panel__bd">
                <div class="weui-media-box weui-media-box_small-appmsg">
                    <div class="weui-cells">

                        <?php if($count <= 0): ?><a href="<?php echo U('Users/addr',array('view'=>'stock'));?>" class="weui-cell weui-cell_access">
                                <div class="weui-cell__bd weui-cell_primary">
                                    <p>添加收货地址</p>
                                </div>
                            </a><?php endif; ?>

                        <?php if($deft != null): ?><a class="weui-cell weui-cell_access" id="showPicker">
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
        </div>
       <!-- <div class="weui-cell">
            <div class="weui-cell__hd"><label class="weui-label">自提</label></div>
            <div class="weui-cell__bd">
                <input class="weui-input" type="number"  placeholder="请输入提货盒数"/>
            </div>
        </div>-->
        <div class="weui-cell">
            <div class="weui-cell__hd"><label class="weui-label">提货盒数</label>(剩余库存<?php echo ($userdata["stock"]); ?>盒)</div>
            <!--<div class="weui-cell__bd">
                <input class="weui-input" type="number" name="nums" placeholder="请输入提货盒数"/>
            </div>-->
        </div>

        <!--<div class="weui-grids">
		        <div class="weui-grid" style="text-align: center;">
		            <input type="radio" onclick="getprice()" class="buyt" checked=checked name="nums" value="1">1盒
		        </div>
		        <div class="weui-grid" style="text-align: center;">
		            <input type="radio" onclick="getprice()" class="buyt" name="nums" value="4">4盒
		        </div>
		        <div class="weui-grid" style="text-align: center;">
		            <input type="radio" onclick="getprice()" class="buyt" name="nums" value="12">12盒
		        </div>
		        <div class="weui-grid" style="text-align: center;">
		            <input type="radio" onclick="getprice()" class="buyt" name="nums" value="20">20盒
		        </div>
		        <div class="weui-grid" style="text-align: center;">
		            <input type="radio" onclick="getprice()" class="buyt" name="nums" value="40">40盒
                </div>
            <div class="weui-grid" style="text-align: center;">
                <input type="radio" onclick="selfTo()"  id="getnums" name="nums" >自提
            </div>
    	</div>-->
        <div class="weui-cell" >
            <div class="weui-cell__bd">
                <input class="weui-input" type="number" name="nums" oninput="getprice()"  id="selfnum"  placeholder="请输入提货盒数"/>
            </div>
        </div>
        <div class="weui-cell">
            <div class="weui-cell__hd"><label class="weui-label">选择发货方式</label></div>
            <!--<div class="weui-cell__bd">
                <input class="weui-input" type="number" name="nums" placeholder="请输入提货盒数"/>
            </div>-->
        </div>
        <div class="weui-cells weui-cells_checkbox" style="margin-top: 0">
           
            <label class="weui-cell weui-check__label" for="s11">
                <div class="weui-cell__hd buyt">
                    <input type="radio" class="weui-check" name="checkbox1" id="s11" checked="checked">
                    <i class="weui-icon-checked"></i>
                </div>
                <div class="weui-cell__bd">
                    <p style="font-size: 1.6rem;color: #666">物流发货</p >
                </div>
            </label>
            <div id="showIOSDialog1">
                <label class="weui-cell weui-check__label" for="getnums" >
                    <div class="weui-cell__hd ziti">
                        <input type="radio" name="checkbox1" id="getnums" class="weui-check"  >
                        <i class="weui-icon-checked"></i>
                    </div>
                    <div class="weui-cell__bd" >
                        <p style="font-size: 1.6rem;color: #666">公司自提</p >
                    </div>
                </label>
            </div>
        </div>
    	<div class="weui-cell" id="paytran">
            <div class="weui-cell__hd" id="getpay"><label class="weui-label">支付物流费用</label></div>
            <div class="weui-cell__bd">
                <div id="prices"></div>

            </div>
        </div>

        <div class="weui-cell" id ='slefdo' style="display: none">
            <div class="weui-cell__bd">
                <input type="number" class="weui-input"   placeholder="请输入提货盒数"/>
            </div>
        </div>
        <div class="weui-cell">
            <div class="weui-cell__bd">
                <textarea name="remakes" id="remakes" class="weui-textarea" placeholder="可输入订单备注信息" rows="5" style="font-size: 2rem"></textarea>
                <!--<div class="weui-textarea-counter"><span>0</span>/200</div>-->
            </div>
        </div>
    </div>
</form>
<!--<div id="prices"></div>-->
<div class="weui-btn-area">
    <a class="weui-btn weui-btn_primary" id="suree" href="javascript:doOption();">确认提货</a>
    <!-- <a class="weui-btn weui-btn_primary" id="suree" href="javascript:viod;">确认提货</a> -->
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
                <div class="weui-dialog__hd"><strong class="weui-dialog__title">确认自提</strong></div>
                <div class="weui-dialog__bd">确认后不会发货，仅支持到公司（成都环球中心s2-1605）自提 </div>
                <div class="weui-dialog__ft">
                    <!-- <a href="javascript:;" class="weui-dialog__btn weui-dialog__btn_default">取消</a> -->
                    <!-- <a href="javascript:walletPays();" class="weui-dialog__btn weui-dialog__btn_primary">确认</a> -->
                    <a href="javascript:walletPays();" class="weui-dialog__btn weui-dialog__btn_primary">确认</a>
                </div>
            </div>
        </div>
        <!--END dialog4-->
    </div>
</div>
<div class="page">
    <div id="dialogs">
        <!--BEGIN dialog1-->
        <div class="js_dialog" id="iosDialog2" style="display: none;">
            <div class="weui-mask"></div>
            <div class="weui-dialog">
                <div class="weui-dialog__hd"><strong class="weui-dialog__title">物流停止发货通知</strong></div>
                <div class="weui-dialog__bd">春节将至，物流于1月26日下午两点停止发货，恢复时间为2月14日。暂时支持到公司自提</div>
                <div class="weui-dialog__ft">
                    <!--<a href="javascript:;" class="weui-dialog__btn weui-dialog__btn_default">取消</a>-->
                    <a href="javascript:sureNoSend();" class="weui-dialog__btn weui-dialog__btn_primary">确认</a>
                </div>
            </div>
        </div>
        <!--END dialog4-->
    </div>
</div>
<!--<script>
    $(document).ready(function (){
        getprice();
    });
</script>-->

<script type="text/javascript">
    $(function(){
        var $iosDialog1 = $('#iosDialog1');

        $('#dialogs').on('click', '.weui-dialog__btn', function(){
            $(this).parents('.js_dialog').fadeOut(200);
        });

        $('#showIOSDialog1').on('click', function(){
            $iosDialog1.fadeIn(200);
        });
    });
</script>
<script>

    var addr = <?php echo ($addr); ?>;


    $('#showPicker').on('click', function () {
        weui.picker(<?php echo ($addrshow); ?>, {
            onConfirm: function (result) {
                var res = addr[result];
                var names = res["name"] + "  " + res["phone"];
                var addrs = "地址：" + res["street"];
                $("#showPicker").find("p[class='trans']").html(names);
                $("#showPicker").find("p[class='times']").html(addrs);
                $("#showPicker").find("p[class='times']").html(addrs);
                $("input[name='addr']").val(res["id"]);
                $("input[name='name']").val(res["name"]);
                $("input[name='phone']").val(res["phone"]);
                $("input[name='address']").val(res["street"]);
                getprice();
            }
        });
    });

    function doOption() {
        showPreLoading();
        var temp = $("form").serializeArray();
        var data = objToArray(temp);
        var nums =$('#selfnum').val();
        console.log(nums);
        if($('#selfnum').val()==0){
            hidePreLoading();
            return alert_wec('提货数量要大于0哦');
        }
        if($('#getnums').is(':checked')){
            data['self']=1;
        }else {
            getprice();
        }

        $.ajax({
            url: "<?php echo U('Core/addon','model=order_drs');?>", type: 'post',
            data: data, dataType: "json",
            success: function (res) {
                hidePreLoading();
                if (res.status !== 1) {
                    return alert_wec(res.msg);
                }

                if(res.data){
                    toDopay(res.data);
                }else {
                    window.location.href ="<?php echo U('users/getStocklist');?>";
                }
                // window.location.href = "<?php echo U('Users/toPayTrans');?>?sn="+res.data;
                //执行支付

            }
        });
    }
    //获取价格 old
    // function getprice() {
    //     var temp = $("form").serializeArray();
    //     var data = objToArray(temp);
    //     showPreLoading();
    //     $.ajax({
    //         url: "<?php echo U('users/getTranPay');?>", type: 'get',
    //         data: data, dataType: "json",
    //         success: function (res) {
    //             hidePreLoading();
    //             $('#prices').html('<p style="color: red;text-align: right;font-size: 1.6rem">'+'<span>￥<span>'+res.data+'</p>');
    //         }
    //     });
    // }
    //

    //获取价格 2018年9月11日10:42:07
    function getprice() {
        var temp = $("form").serializeArray();
        var data = objToArray(temp);
        showPreLoading();
        $.ajax({
            url: "<?php echo U('users/gettranprice');?>", type: 'get',
            data: data, dataType: "json",
            success: function (res) {
                hidePreLoading();
                $('#prices').html('<p style="color: red;text-align: right;font-size: 1.6rem">'+'<span>￥<span>'+res.data+'</p>');
            }
        });
    }

    /**
     * 微信支付
     * @param {type} data
     * @returns {undefined}
     */

    function toDopay(sn) {
        $.ajax({
            url: "<?php echo U('users/toPayTrans');?>",
            type: 'post',
            data: {'sn':sn},
            dataType: "json",
            success: function (res) {
                hidePreLoading();
                if(res.status ===2){
                    window.location.href = "<?php echo U('users/getStocklist');?>";
                    return false;
                }
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
</script>

<script>
    function selfTo() {
        $('#prices').html('<p style="color: red;text-align: right;font-size: 1.6rem">'+'<span>￥<span>'+0+'</p>');
        $('#slefdo').css('display','block');
    }
    //
    $('.buyt').on('click',function () {
       $('#paytran').css('display','block');
    });

    $('.ziti').on('click',function () {
       $('#paytran').css('display','none');
    });

    $('#selfnum').blur(function () {
        var nums =$('#selfnum').val();
        $('#getnums').val(nums);
    })
</script>

<script>
    //停止操作
   /* function sureNoSend() {
        $('#suree').css('display','none');
    }*/
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