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
    <a href="<?php echo U('Order/index');?>" class="order-menu_item <?php if(($st) == "10"): ?>order-menu_item_on<?php endif; ?>">全部</a>
    <!--<a href="<?php echo U('Order/index','st=3');?>" class="order-menu_item <?php if(($st) == "4"): ?>order-menu_item_on<?php endif; ?>">交易成功</a>-->
    <a href="<?php echo U('Order/index','st=1');?>" class="order-menu_item <?php if(($st) == "1"): ?>order-menu_item_on<?php endif; ?>">已入库</a>
    <a href="<?php echo U('Order/index','st=0');?>" class="order-menu_item <?php if(($st) == "0"): ?>order-menu_item_on<?php endif; ?>">待支付</a>
    <a href="<?php echo U('Order/index','st=4');?>" class="order-menu_item <?php if(($st) == "2"): ?>order-menu_item_on<?php endif; ?>">已取消</a>
    <input type="hidden" id="st" value="<?php echo ($st); ?>">
</div>

<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><div class="weui-panel order-list">
        <div class="weui-panel__hd">
            <div>
                <i class="iconfont">&#xe63e;</i><?php echo (date('Y年m月d日 H:i:s',$v["times"])); ?>
            </div>

            <p class="weui-media-box__price">
                <?php if(($v["status"]) == "0"): ?><span class="waits">待支付</span><?php endif; ?>
                <?php if(($v["status"]) == "1"): ?><span class="saves"><?php echo ($v['status']==1?($v['gid']==8?'已入库':'待发货'):''); ?></span><?php endif; ?>
                <!--<?php if(($v["status"]) == "2"): ?><span class="saves">待收货</span><?php endif; ?>-->
                <?php if(($v["status"]) == "3"): ?><span>已完成</span><?php endif; ?>
                <?php if(($v["status"]) == "4"): ?><span class="cancel">已取消</span><?php endif; ?>
                <?php if(($v["status"]) == "5"): ?><span class="cancel">自提</span><?php endif; ?>
            </p>
        </div>
        <div class="weui-panel__bd">
            <a href="<?php echo U('Order/infos','id='.$v['id']);?>" class="weui-media-box weui-media-box_appmsg">
                <div class="weui-media-box__hd">
                    <img class="weui-media-box__thumb" src="/Public/uploads/goods/<?php echo ($v["gimgs"]); ?>"/>
                </div>
                <div class="order-list-right" style="display: flex;justify-content: space-between;width: 100%;text-align: left;">
                    <div>
                        <div class="weui-media-box__bd">
                            <h4 class="weui-media-box__title"><!--<i class="order-list-name-icon green">0元计划</i>--><i class="order-list-name-icon gold">云库存</i><?php echo ($v["gname"]); ?>
                            </h4>
                            <p class="weui-media-box__desc"><?php echo ($v["introduct"]); ?></p>
                            <!--<p class="weui-media-box__desc">订单备注：<?php echo ($v['remakes']==null?'无':$v['remakes']); ?></p>-->
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <p>¥<?php echo ($v["gprice"]); ?></p>
                        <p>x<?php echo ($v["gnums"]); ?></p>
                    </div>
                </div>
            </a>
        </div>
        <div class="weui-panel__hd order-price">
            <div class="order-price-name">合计金额</div>
            <div class="order-price-number">¥<?php echo ($v["money"]); ?></div>
        </div>
        <div class="weui-panel__hd order-price">
            <div class="order-price-name">出货人</div>
            <div class="order-list-dealer"><a href="tel:<?php echo ($v["phone"]); ?>" style="color:#dcb29f"><?php echo ($v["up_user"]); ?><i class="iconfont">&#xe640;</i></a></div>
        </div>
        <div class="weui-panel__ft">
            <div class="weui-cell weui-cell_link order-btn-list">
                <div class="weui-cell__bd">
                    <?php if($v['status'] == 0): ?><a href="<?php echo U('Malls/payer','sn='.$v['sn']);?>" class="waits">去支付</a>
                        <a href="javascript:doOption('<?php echo ($v["id"]); ?>');" class="cancel">取消</a><?php endif; ?>
                    <?php if($v['status'] == 1): ?><a href="<?php echo U('Order/infos','id='.$v['id']);?>" class="cancel">查看订单</a><?php endif; ?>
                    <?php if($v['status'] == 2): ?><a href="<?php echo U('Order/trans','sn='.$v['sn']);?>" class="cancel">查看物流</a>
                        <a href="<?php echo U('Malls/buyer','sn='.$v['gsn'].'&nums='.$v['gnums']);?>" class="again">再次购买</a><?php endif; ?>
                    <?php if($v['status'] == 3): ?><a href="<?php echo U('Malls/buyer','sn='.$v['gsn'].'&nums='.$v['gnums']);?>" class="again">再次购买</a><?php endif; ?>
                    <?php if($v['status'] == 4): ?><a href="<?php echo U('Malls/buyer','sn='.$v['gsn'].'&nums='.$v['gnums']);?>" class="again">再次购买</a><?php endif; ?>
                </div>
            </div>    
        </div>
    </div><?php endforeach; endif; else: echo "" ;endif; ?>

<style>
    .load-more{
        height: 4rem;
        line-height: 4rem;
        font-size: 1.4rem;
        border-top: 1px solid #f2f2f2;
        margin: 0;
        padding: 0;
        text-align: center;
        font-family: -apple-system-font,Helvetica Neue,sans-serif;
        background-color: #FFFFFF;
    }
</style>
<div id="model"></div>
<div class="load-more"  id="ajax-get-self" style="display: <?php echo ($page); ?>;"><i class="iconfont"></i>点击加载更多</div>
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
		var p= <?php echo I('p',1);?>;
        $('#ajax-get-self').click(function(){
            var st=$("#st").val();
            //console.log(1);
            showPreLoading();
            $.get("<?php echo U('Order/index');?>",{'p':p+1,st: st},function(data){
            	hidePreLoading();
                console.log(data.page);
                if(data.page==null){
                    $("#ajax-get-self").css('display','none');
                }
                if(data.error==1){
                   // $("#ajax-get-self").css('display',data.style);
                    var list = data.data;
                    var html = '';
                    console.log(list);
                    $(list).each(function(i,e){
                            html += '<div class="weui-panel order-list">\
                                        <div class="weui-panel__hd">\
                                            <div>\
                                                <i class="iconfont">&#xe63e;</i>'+ e.ajax_times+'\
                                            </div>\
                                            <p class="weui-media-box__price">\
                                              <span class="'+ e.status_class+'">'+ e.ajax_status+'</span>\
                                            </p>\
                                      </div>\
                                        <div class="weui-panel__bd">\
                                            <a href="" class="weui-media-box weui-media-box_appmsg">\
                                                <div class="weui-media-box__hd">\
                                                    <img class="weui-media-box__thumb" src="/Public/uploads/goods/<?php echo ($v["gimgs"]); ?>"/>\
                                                </div>\
                                                <div class="order-list-right" style="display: flex;justify-content: space-between;width: 100%;text-align: left;">\
                                                    <div>\
                                                        <div class="weui-media-box__bd">\
                                                            <h4 class="weui-media-box__title"><!--<i class="order-list-name-icon green">0元计划</i>--><i class="order-list-name-icon gold">云库存</i>'+ e.gname+'\
                                                            </h4>\
                                                            <p class="weui-media-box__desc">'+ e.introduct+'</p>\
                                                            </div>\
                                                    </div>\
                                                    <div style="text-align: right;">\
                                                        <p>¥'+ e.gprice+'</p>\
                                                        <p>x'+ e.gnums+'</p>\
                                                   </div>\
                                                </div>\
                                            </a>\
                                        </div>\
                                        <div class="weui-panel__hd order-price">\
                                            <div class="order-price-name">合计金额</div>\
                                            <div class="order-price-number">¥'+ e.money+'</div>\
                                        </div>\
                                        <div class="weui-panel__hd order-price">\
                                            <div class="order-price-name">出货人</div>\
                                            <div class="order-list-dealer">\
                                            <a href="tel:<?php echo ($v["phone"]); ?>" style="color:#dcb29f">'+ e.up_user+'\
                                            <i class="iconfont">&#xe640;</i>\
                                            </a>\
                                            </div>\
                                        </div>\
                                        <div class="weui-panel__ft">\
                                            <div class="weui-cell weui-cell_link order-btn-list">\
                                                <div class="weui-cell__bd">';
                                                if(e.status==4){
                                                        html+='<a href="<?php echo U('Malls/buyer');?>?sn='+e.gsn +'&nums='+e.gnums +'" class="again">再次购买</a>';
                                                    }
                                                if(e.status==0){
                                                    html+='<a href="<?php echo U('Malls/payer');?>?sn='+e.sn +'" class="waits">去支付</a>\
                                                         <a href="javascript:doOption('+e.id+');" class="cancel">取消</a>';
                                                }
                                                if(e.status==1){
                                                    html+='<a href="<?php echo U('Order/infos');?>?id='+e.id +'" class="cancel">查看订单</a>';
                                                }
                                                if(e.status==2){
                                                    html+=' <a href="<?php echo U('Order/trans');?>?sn='+e.sn +'" class="cancel">查看物流</a>\
                                                            <a href="<?php echo U('Malls/buyer');?>?sn='+e.gsn +'&nums='+e.gnums +'" class="again">再次购买</a>';
                                                }
                                                if(e.status==3){
                                                    html+='<a href="<?php echo U('Malls/buyer');?>?sn='+e.gsn +'&nums='+e.gnums +'" class="again">再次购买</a>';
                                                }
                                                html+='</div>\
                                                            </div> \
                                                               </div>\
                                                    </div>';
                    });
                }

                p += 1;
                $("#model").append(html);
            })
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