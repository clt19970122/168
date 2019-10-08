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
                <div class="index-money_head_container" style="">
    <div class="index-money_head">
        <p class="money big-money"><?php echo ($user["money"]); ?></p>
        <p class="title">当前账户余额(元)</p>
        <!-- <p class="button">
             <a href="javascript:onDraw();">立即提现</a>
         </p>-->
    </div>
    <div style="display: flex;justify-content: center;align-items: center">
        <div class="index-money_head" style="width: 50%">
            <p class="money"><?php echo ($user["money"]); ?></p>
            <p class="title">可提现余额(元)</p>
            <!-- <p class="button">
                 <a href="javascript:onDraw();">立即提现</a>
             </p>-->
        </div>
        <div style="border-right:1px solid #d9d9d9;width: 2px;height:3rem;color: red;margin-top: 2rem"></div>
        <div class="index-money_head" style="width: 50%">
            <p class="money"><?php echo ($frozen); ?></p>
            <p class="title">待入帐(元)
                <a href="javascript:;" class="ques" style="margin-top: 2rem">
                    <img id="showIOSDialog2" src="/Public/home/imgs/index_ques.png"/>
                </a>
            </p>
        </div>
    </div>
</div>
<div class="user-menu">
    <a href="<?php echo U('Index/draw_number');?>" class="user-menu_item">
        <p class="icon"><img src="/Public/static/imgs/s15.png"/></p>
        <p class="fonts">我要提现</p>
    </a>
    <a href="<?php echo U('Index/draw_success');?>" class="user-menu_item">
        <p class="icon"><img src="/Public/static/imgs/s13.png"/></p>
        <p class="fonts">查看提现历史</p>
    </a>
    <a href="<?php echo U('Users/bank');?>" class="user-menu_item">
        <p class="icon"><img src="/Public/static/imgs/s5.png"/></p>
        <p class="fonts">银行卡管理</p>
    </a>
</div>
<div class="index-common-title p32">
    <p>收支明细</p>
    <div class="index-money_list">
        <div class="index-money_list_item on" title="a" id="1" onclick='test(this)'><span>只看收益</span></div>
        <div class="index-money_list_item" title="b" id="2" onclick='test(this)'><span>只看支出</span></div>
        <div class="index-money_list_item" title="c" id="3" onclick='test(this)'><span>只看佣金</span></div>
        <!--<div class="index-money_list_item" title="d" onclick="list()"><span>库存记录</span></div>-->
    </div>
    <div class="total-money">

        <div>
            <!--<div class="date-seleted date-begin" href="javascript:;" id="beginDate">开始时间</div>
            <div class="date-seleted" style="background: none">-</div>
            <div class="date-seleted date-over" href="javascript:;" id="overDate">结束时间</div>-->
        </div>
        <div class="total-money-number" >合计金额<br><span id="total_money">￥<?php echo ($money); ?></span></div>
    </div>
</div>
<script>
    $('#beginDate').on('click', function () {
        weui.datePicker({
            start: 2015,
            end: new Date().getFullYear(),
            defaultValue: [new Date().getFullYear(), new Date().getMonth()+1, new Date().getDate()],
            onChange: function (result) {
                console.log(result);
            },
            onConfirm: function (begin) {
                $('#beginDate').html(begin[0]+'-'+begin[1]+'-'+begin[2])

            }
        });
    });

    $('#overDate').on('click', function () {
        weui.datePicker({
            start: 2015,
            end: new Date().getFullYear(),
            defaultValue: [new Date().getFullYear(), new Date().getMonth()+1, new Date().getDate()],
            onChange: function (result) {
                console.log(result);
            },
            onConfirm: function (over) {
                $('#overDate').html(over[0]+'-'+over[1]+'-'+over[2])
            }
        });

    });
</script>
<div class="index-money_item index-money_serv" id="a">

    <!--<p class="head"><img src="<?php echo ($user["headimgurl"]); ?>"/></p>
    <p class="name"><?php echo ($user["nickname"]); ?></p>
    <p class="level">
        <button><?php echo (getUserLevel($user['level'],$level)); ?></button>
    </p>
    <div class="index-money_serv_data">
        <div class="index-money_serv_data_i"><span>利润:<br/><font><?php echo ($user["person"]); ?></font></span></div>
        <div class="index-money_serv_data_i"><span>总进货量:<br/><font><?php echo ($user["totalpoints"]); ?></font></span></div>
        <div class="index-money_serv_title">
            <img src="/Public/home/imgs/index_calen.png"/>&nbsp;本月业绩
        </div>
    </div>-->

    <div class="index-money_item p32">
        <div class="index-money_item-box">
            <div class="item" style="text-align: left"><b>日期时间</b></div>
            <!--<div class="item"><?php echo (date('H:i:s',$v["times"])); ?></div>-->
            <div class="item"><b>金额(￥)</b></div>
            <div class="item" style="text-align: right"><b>类型</b></div>
        </div>
        <input type="hidden" id="style" value="">
        <div class="money_list">
            <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><div class="index-money_item-box ">
                    <div class="item text-left"><?php echo ($v["times"]); ?></div>
                    <div class="item"><span><?php echo ($v["money"]); ?></span></div>
                    <div class="item" style="text-align: right">
                        <span><?php echo ($v["type"]); ?></span>
                    </div>
                </div><?php endforeach; endif; else: echo "" ;endif; ?>
        </div>
        <div class="load-more" id="load_more" style="display: <?php echo ($xyy); ?>;"><i class="iconfont"></i>点击加载更多</div>
    </div>
</div>



<div class="js_dialog" id="iosDialog2" style="display: none;">
    <div class="weui-mask"></div>
    <div class="weui-dialog">
        <div class="weui-dialog__bd">“0元计划”用户当收货人确认收货后，待入帐金额将会转入余额，用户可进行提现</div>
        <div class="weui-dialog__ft">
            <a href="javascript:;" class="weui-dialog__btn weui-dialog__btn_primary">知道了</a>
        </div>
    </div>
</div>

<script>

    function onDraw() {
        var bank = <?php echo ($bank); ?>;
        if (bank.length <= 0) {
            return alert_wec("请绑定银行卡后在进行提现");
        }
        weui.picker(<?php echo ($bank); ?>, {
            onConfirm: function (result) {
                var data = {bank: result[0]};
                showPreLoading();
                $.ajax({
                    url: "<?php echo U('Index/money_draw');?>", type: 'post',
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
        });
    }
    var p= <?php echo I('p',1);?>;
    function test(obj){
        var id=obj.id;
        var html = '';
        showPreLoading();
        $.ajax({
            url: "<?php echo U('Index/arr_money');?>", type: 'post',
            data: {status:id}, dataType: "json",
            success: function (res) {
                hidePreLoading();
                $(res.data).each(function(i,e){
                    html += '<div class="index-money_item-box ">\
                                  <div class="item text-left">'+ e.time+'</div>\
                                  <div class="item"><span>'+ e.money+'</span></div>\
                                  <div class="item" style="text-align: right">\
                                  <span>'+ e.type+'</span>\
                                  </div>\
                              </div>';
                });
                $("#load_more").css('display',res.xyy);
                $(".money_list").append(html);
                $("#style").val(res.status);
                $('#total_money').html('￥'+res.money);
                p=1;
            }
        });
    }

    $(function(){
        $('#load_more').click(function(){
            var id=$("#style").val();
            console.log(id);
            var html = '';
            if(id!='') {
                $.get("<?php echo U('Index/arr_money');?>", {'p': p + 1, status: id}, function (data) {
                    var html = '';
                    $(data.data).each(function (i, e) {
                        html += '<div class="index-money_item-box ">\
                                    <div class="item text-left">' + e.time + '</div>\
                                    <div class="item"><span>' + e.money + '</span></div>\
                                    <div class="item" style="text-align: right">\
                                    <span>' + e.type + '</span>\
                                    </div>\
                                    </div>';
                    });
                    $("#load_more").css('display',data.xyy);
                    p += 1;
                    $(".money_list").append(html);
                })
            }else {
                $.get("<?php echo U('Index/money');?>", {'page':p+1}, function (data) {
                    console.log(data);
                    var html = '';
                    $(data.data).each(function (i, e) {
                        html += '<div class="index-money_item-box ">\
                                    <div class="item text-left">' + e.times + '</div>\
                                    <div class="item"><span>' + e.money + '</span></div>\
                                    <div class="item" style="text-align: right">\
                                    <span>' + e.type + '</span>\
                                    </div>\
                                    </div>';
                    });
                    $("#load_more").css('display',data.xyy);
                    p += 1;
                    $(".money_list").append(html);
                })
            }
        });
    });

    $(document).on("click touchstart", ".index-money_list_item", function () {
        var id = $(this).attr("title");
        $(".index-money_list_item").removeClass("on");
        // $(".index-money_item").hide();
        $(this).addClass("on");
        $(".money_list").empty();
        // $("#" + id).show();

    });


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
    });

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