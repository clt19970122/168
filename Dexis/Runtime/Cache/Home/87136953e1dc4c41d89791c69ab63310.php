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
                <link rel="stylesheet" href="/Public/libs/swiper/css/swiper.min.css">
<style>
    /*2.0轮播*/
    .swiper-container{
        background: #fff;
        overflow: hidden;
    }
    .swiper-slide{
        width: 92%!important;
        margin: 0 4%;
        position: relative;
    }
    .swiper-slide img{
        border-radius: 10px;
        height: 100%;
    }
    .swiper-slide-prev{
        right:-6.5%;
    }
    .swiper-slide-next {
        left: -6.5%;
    }
    .index-goods-banner .swiper-slide:after {
        content: '';
        position: absolute;
        top: 50%;
        bottom: 0;
        left: 20px;
        right: 20px;
        -webkit-box-shadow: 0 0 20px rgba(0,92,172,0.2);
        -moz-box-shadow: 0 0 20px rgba(0,92,172,0.2);
        -o-box-shadow: 0 0 20px rgba(0,92,172,0.2);
        box-shadow: 0 0 20px rgba(0,92,172,0.6);
        border-radius: 100px/10px; /*水平半径/垂直半径*/
        z-index: -1;
    }

    .swiper-pagination-bullet{
        width: 4px;
        height: 4px;
        background: #fff!important;
        opacity: .5;
    }
    .swiper-container-horizontal>.swiper-pagination-bullets .swiper-pagination-bullet{
        margin: 0 2px;
    }
    .swiper-pagination-bullet-active{
        width: 14px;
        opacity: 1;
        border-radius: 5px;
        background: #fff;
    }
</style>
<script src="/Public/libs/swiper/js/swiper.min.js"></script>
<div class='index'>
    <div class="swiper-container index-goods-banner" id="top_banner" style="padding-bottom: 18px;padding-top: 1rem">
        <div class="swiper-wrapper">
            <?php if(is_array($put["index_banner"])): $i = 0; $__LIST__ = $put["index_banner"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><div class="swiper-slide">
                    <a href="<?php echo ($v["links"]); ?>">
                        <img src="/Public/uploads/ads/<?php echo ($v["imgs"]); ?>"/>
                    </a>
                </div><?php endforeach; endif; else: echo "" ;endif; ?>
        </div>
		<div class="swiper-pagination" style="bottom: 20px;"></div>
    </div>
</div>

<div class='index-menu'>

    <a href="<?php echo U('Index/sorts');?>" class="index-menu_item">
        <p class="icon"><img src="/Public/static/imgs/e01.png"/></p>
        <p class="name">我的等级</p>
    </a>
    <a href="<?php echo U('Index/learn');?>" class="index-menu_item">
        <p class="icon"><img src="/Public/static/imgs/e06.png"/></p>
        <p class="name">获取帮助</p>
    </a>
    
    <a href="<?php echo U('Index/financial_centre');?>" class="index-menu_item">
        <!-- <a href="<?php echo U('Index/plans');?>" class="index-menu_item"> -->
        <p class="icon"><img src="/Public/static/imgs/e03.png"/></p>
        <p class="name">0元创业计划</p>
    </a>
    <a href="<?php echo U('Wcix/share',array('sysid'=>$sysid));?>" class="index-menu_item">
	<!-- <a href="javascript:;" onclick="alert('正在建设中')" class="index-menu_item"> -->
        <p class="icon"><img src="/Public/static/imgs/e04.png"/></p>
        <p class="name">分享好友</p>
    </a>
    <!--<a href="<?php echo U('Index/tious');?>" class="index-menu_item">
        <p class="icon"><img src="/Public/static/imgs/e5.png"/></p>
        <p class="name">团队业绩</p>
    </a>-->
    <a href="<?php echo U('Index/schol');?>" class="index-menu_item">
        <p class="icon"><img src="/Public/static/imgs/e05.png"/></p>
        <p class="name">素材中心</p>
    </a>
    <a href="<?php echo U('Index/money');?>" class="index-menu_item">
        <p class="icon"><img src="/Public/static/imgs/e02.png"/></p>
        <p class="name">我的钱包</p>
    </a>
    <a href="<?php echo U('Index/rider');?>" class="index-menu_item">
        <p class="icon"><img src="/Public/static/imgs/e07.png"/></p>
        <p class="name">我的团队</p>
    </a>
    <a href="<?php echo U('Users/stock');?>" class="index-menu_item">
        <p class="icon"><img src="/Public/static/imgs/e08.png"/></p>
        <p class="name">我要提货</p>
    </a>

</div>

<!--0元计划抢购-->
<div class='index index_banner'>
    <div class="swiper-container" id="banner">
        <div class="swiper-wrapper">
            <?php if(is_array($put["index_middle"])): $i = 0; $__LIST__ = $put["index_middle"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><div class="swiper-slide">
                    <a href="<?php echo U('Index/financial_card');?>">
                        <img style="border-radius: 9px" src="/Public/uploads/ads/<?php echo ($v["imgs"]); ?>"/>
                    </a>
                </div><?php endforeach; endif; else: echo "" ;endif; ?>

        </div>
    </div>
</div>
<!--滚动信息-->
<div class="index-notic">
    <div class="index-notic-title">

    </div>
    <div class="newsScroll" id="s1">
        <ul style="overflow: hidden">
            <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>
                    <a href="<?php echo U('Index/schol_infos','id='.$vo['id']);?>"><?php echo (substr($vo["title"],0,51)); ?></a>
                </li><?php endforeach; endif; else: echo "" ;endif; ?>
        </ul>
    </div>
    <div class="board" style="border-right:1px solid #d9d9d9;width: 2px;color: red;margin: 0 6px;height: 16px"></div>
    <a href="<?php echo U('Index/schol');?>" class="more-notice">更多</a>
</div>

<!--黄金雨-->
<!-- <div class="home-item" style="margin-top: 1rem">
    <a href="<?php echo U('Malls/goods',['sn'=>'415244730815416283','gold'=>1]);?>" class="home-item_thumb home-item_thumb_shadow">
        <img src="/Public/uploads/goods/<?php echo (getGoodsImg('415244730815416283',$img)); ?>"/>
    </a>
    <div class="home-item_cont">
        <div class="home-item_cont_title overflow" style="color:red ;font-size: 20px"><i class="order-list-name-icon orange">HOT</i>168太空素食黄金雨活动</div>
        <div class="home-item_cont_descs">168太空素食黄金雨通道入口</div>
        <div class="home-item_cont_price">
            <span><font>￥</font>150</span>
            <a class="blue" href="<?php echo U('Malls/goods',['sn'=>'415244730815416283','gold'=>1]);?>">立即购买</a>
        </div>
    </div>
</div> -->
<!--产品列表-->
<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><div class="home-item" style="margin-top: 1rem">
        <a href="<?php echo U('Malls/goods','sn='.$v['gsn']);?>" class="home-item_thumb home-item_thumb_shadow">
            <img src="/Public/uploads/goods/<?php echo (getGoodsImg($v['gsn'],$img)); ?>"/>
        </a>
        <div class="home-item_cont">
            <div class="home-item_cont_title overflow"><i class="order-list-name-icon orange">HOT</i><?php echo ($v["name"]); ?></div>
            <div class="home-item_cont_descs"><?php echo ($v["subname"]); ?></div>
            <div class="home-item_cont_price">
                <span><font>￥</font><?php echo ($v["price"]); ?></span>
                <a class="blue" href="<?php echo U('Malls/goods','sn='.$v['gsn']);?>">立即购买</a>
            </div>
        </div>
    </div><?php endforeach; endif; else: echo "" ;endif; ?>

<!-- 0元计划列表 -->
<div class="index_w index_b p32">
    <div class="index_w_o" style="margin-top: 0">
        <div class="z"><a href="<?php echo U('Index/financial_card');?>"><span>0元计划</span></a></div>
        <div class="y"><a href="<?php echo U('Index/financial_card');?>">先拿货 后付款</a></div>
    </div>
    <div class="index_w_t" style="clear: both">

        <!-- <a href="<?php echo U('Index/plans_info','sn=815264414134609747');?>" class="index-plan-bg a1"> -->
        <a href="<?php echo U('Index/financial_card');?>" class="index-plan-bg a1">
            <p class="p1">0元创业</p>
            <p class="p2">40盒 3800元</p>
        </a>

       <!--  <a href="<?php echo U('Index/plans_info','sn=815270623423588968');?>" class="index-plan-bg a2"> -->
        <a href="<?php echo U('Index/financial_card');?>" class="index-plan-bg a2">
            <p class="p1">月体验</p>
            <p class="p2">12盒 1560元</p>
        </a>
        <!-- <a href="<?php echo U('Index/plans_info','sn=715270469154287144');?>" class="index-plan-bg a3"> -->
        <a href="<?php echo U('Index/financial_card');?>" class="index-plan-bg a3">
            <p class="p1">周体验</p>
            <p class="p2">4盒 600元</p>
        </a>
        <!-- <a href="<?php echo U('Index/plans_info','sn=315264382127443659');?>" class="index-plan-bg a4"> -->
        <a href="<?php echo U('Index/financial_card');?>" class="index-plan-bg a4">
            <p class="p1">单盒体验</p>
            <p class="p2">1盒 168元</p>
        </a>
    </div>

</div>
<!--产品列表-->
<!--<div class='index-product'>
    <?php if(is_array($list)): $k = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($k % 2 );++$k;?><a href="<?php echo U('Malls/goods','sn='.$v['gsn']);?>" class="index-product_item">
            <img src="/Public/uploads/goods/<?php echo (getGoodsImg($v['gsn'],$img)); ?>"/>
            <p class="title overflow"><?php echo ($v["name"]); ?></p>
            <p class="descs"><?php echo ($v["subname"]); ?></p>
            <p class="price">
                ¥<?php echo ($v["price"]); ?><span>立即购买</span>
            </p>
        </a><?php endforeach; endif; else: echo "" ;endif; ?>
</div>-->
<div style="background: #fff">
	<div class="index_w_o p32">
        <div class="z"><a href="<?php echo U('Index/schol');?>"><span>相关资讯</span></a></div>
        <div style="content: '';display: inline-block;height: 8px;width: 8px;border-width: 2px 2px 0 0;border-color: #c8c8cd;border-style: solid;-webkit-transform: matrix(.71,.71,-.71,.71,0,0);transform: matrix(.71,.71,-.71,.71,0,0);">
        </div>
    </div>
    <div class="weui-panel__bd index-news_item" id="a1">
        <?php if(is_array($list1)): $i = 0; $__LIST__ = $list1;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><a href="<?php echo U('Index/schol_infos','id='.$vo['id']);?>" class="weui-media-box index-news-msg p32">
                <div class="weui-media-box__bd">
                    <h4 class="weui-media-box__title"><?php echo ($vo["title"]); ?></h4>
                    <!-- <p class="weui-media-box__desc"><?php echo ($vo["content"]); ?></p> -->
                    <p class="index-msg-time"><?php echo ($vo["times"]); ?></p>
                </div>
                <div class="index-news-img">
                    <img src="/Public/uploads/school/<?php echo ($vo["imgs"]); ?>"/>
                </div>
            </a><?php endforeach; endif; else: echo "" ;endif; ?>
    </div>
</div>
<script>
    $(document).on("click touchstart", ".index-news_list_item", function () {
        var id = $(this).attr("title");
        $(".index-news_list_item").removeClass("on");
        $(".index-news_item").fadeOut();
        $(this).addClass("on");
        $("#" + id).fadeIn();
//        $("#" + id).addClass("trans");
        return false;
    });

        function creditCard() {
  //  var data = {ids: "<?php echo ($list["id"]); ?>"};
        showPreLoading();
        $.ajax({
            url: "<?php echo U('Index/credit_card');?>",
            type: 'post',
           // data: data,
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

<script>
    new Swiper('#top_banner',{
        pagination: '.swiper-pagination',
        paginationClickable: true,
        initialSlide:1,
        centeredSlides:!0,
        autoplay: 4000,
        //speed:500,
        loop:true,
        slidesPerView:"auto",
        onSlideChangeEnd:function(e){
            var i=$(e.slides[e.activeIndex]).attr("data-index");
            $(".js-teacher-course").eq(i).siblings().hide(),$(".js-teacher-course").eq(i).show()
        }
    });
    function AutoScroll(obj) {
        $(obj).find("ul:first").animate({
                marginTop: "-24px"
            },
            500,
            function() {
                $(this).css({
                    marginTop: "0px"
                }).find("li:first").appendTo(this);
            });
    }
    setInterval('AutoScroll("#s1")', 3000);

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