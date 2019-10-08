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
            <div class="item text-left"><b class="text-left">日期时间</b></div>
            <div class="item"><b>金额类型</b></div>
            <!--<div class="item"><?php echo (date('H:i:s',$v["times"])); ?></div>-->
            <div class="item"><b>提现金额(￥)</b></div>
            <div class="item"><b class="text-right">到账时间</b></div>
        </div>
    <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><div class="index-money_item-box">
            <div class="item text-left">
                <div class="item-time">
                    <p><?php echo (date('Y-m-d',$v["times"])); ?></p><p><?php echo (date('H:i:s',$v["times"])); ?></p></div>
            </div>
            
            <div class="item text-center"><?php echo ($v["d_type"]); ?></div>
           
            <div class="item"><span><?php echo ($v['type']>0?'+':'-'); echo ($v["money"]); ?></span></div>
            <div class="item text-right">
                <?php echo ($v['status']==0?'<span>审核中</span>':''); ?>
                <?php if($v["status"]==1): ?>
                <span><?php echo (date('Y-m-d',$v["endtime"])); ?></span>
                <?php endif;?>
                <?php echo ($v['status']==2?'<span>被驳回</span>':''); ?>
            </div>
        </div><?php endforeach; endif; else: echo "" ;endif; ?>
        <div class="showtime"></div>

        <!--<div class="index-money_item-box">
            <div class="item text-left">
                <div class="item-time">
                    <p>2019-04-08</p><p>14:36</p>
                </div>
            </div>
            <div class="item text-center">银行卡</div>
            <div class="item"><span>200</span></div>
            <div class="item text-right">
                <div class="item-time">
                    <p>2019-04-08</p><p>14:36</p>
                </div>
            </div>
        </div>-->

        <div class="load-more" id="ajax-get-self" style="display: <?php echo ($arrs["style"]); ?>;"><i class="iconfont"></i>点击加载更多</div>
    </div>
</div>


<script>
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

    var p= <?php echo I('p',1);?>;
    $(function(){
        $('#ajax-get-self').click(function(){
            $.get("<?php echo U('Index/draw_success');?>",{'p':p+1},function(data){
                if(data.error==1){
                    $("#ajax-get-self").css('display',data.style);
                    var list = data.data;
                    var html = '';
                    console.log(list);
                    $(list).each(function(i,e){
                        if(e.d_type==''){
                            html += '<div class="index-money_item-box">\
                                    <div class="item text-left">\
                                    <div class="item-time">\
                                    <p>'+ e.a_time+'</p><p>'+ e.b_time+'</p></div>\
                                    </div>\
                                    <div class="item text-center">'+ e.d_type+'</div> \
                                    <div class="item"><span>'+ e.jj+''+ e.money+'</span></div>\
                                        <div class="item text-right">\
                                             <span>'+ e.dete+'</span>\
                                    </div>\
                              </div>';
                        }
                        if(e.d_type!=''){
                            html += '<div class="index-money_item-box">\
                                    <div class="item text-left">\
                                    <div class="item-time">\
                                    <p>'+ e.a_time+'</p><p>'+ e.b_time+'</p></div>\
                                    </div>\
                                    <div class="item text-center">'+ e.d_type+'</div> \
                                    <div class="item"><span>'+ e.jj+''+ e.money+'</span></div>\
                                    <div class="item text-right">\
                                        <div class="item-time">\
                                            <p>'+ e.c_time+'</p><p>'+ e.d_time+'</p>\
                                        </div>\
                                    </div>\
                              </div>';
                        }

                        });


                }
                p += 1;
                $(".showtime").append(html);
            })
         });
    })

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