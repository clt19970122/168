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
                <!--
<div class='index-rider'>
    <p class="head"><img src="<?php echo ($user["headimgurl"]); ?>"/></p>
    <p class="name"><?php echo ($user["nickname"]); ?></p>
    <p class="level">
        <button style="color: #C3A66B"><?php echo (getUserLevel($user['level'],$level)); ?></button>
    </p>
    <p class="peop" >已推荐人数: <span id="count"></span></p>
</div>-->
<style>

</style>
<div class="user-header member-header" style="overflow: hidden;height: auto;padding-bottom: 5.6rem;">
    <div class="index-rider">
        <div class="head" id="showIOSActionSheet">
            <img src="<?php echo ($user["headimgurl"]); ?>">
            <p class="name">
                <?php if(($user["name"]) != ""): echo ($user["name"]); endif; ?>
            </p>
        </div>
        <div style="width: 100%">
            <p class="level" style="text-align: left">
                <button class="<?php echo ($class); ?>"><i class="iconfont"><?php echo ($icon); ?></i><?php echo (getUserLevel($user['level'],$level)); ?></button>
            </p>
        </div>
    </div>

</div>

<div class="user-menu up-user-item">
    <a href="" class="user-menu_item">
        <p class="icon"><img src="<?php echo ($top_u["headimgurl"]); ?>"></p>
        <p class="fonts up-user-title">总代理</p>
        <p class="fonts"><?php echo ($top_u["name"]); ?></p>
    </a>
    <a href="" class="user-menu_item">
        <p class="icon"><img src="<?php echo ($out_u["headimgurl"]); ?>"></p>
        <p class="fonts up-user-title">出货人</p>
        <p class="fonts"><?php echo ($out_u["name"]); ?></p>
    </a>
    <a href="" class="user-menu_item">
        <p class="icon"><img src="<?php echo ($push_u["headimgurl"]); ?>"></p>
        <p class="fonts up-user-title">推荐人</p>
        <p class="fonts"><?php echo ($push_u["name"]); ?></p>
    </a>
</div>
<?php if(count($new_data) != 0): ?><div class="weui-cells user-item recent-member" style="margin-top: 1rem">
        <diV class="weui-cell weui-cell_access">
            <div class="weui-cell__bd">
                <?php if(is_array($new_data)): $i = 0; $__LIST__ = $new_data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$news): $mod = ($i % 2 );++$i;?><img class="recent-member-headerimg" src="<?php echo ($news["headimgurl"]); ?>"><?php endforeach; endif; else: echo "" ;endif; ?>
            </div>
            <div class="weui-cell__ft"><span class="sysid" style="color: #dcb29f">新增成员动态</span>
                <!--<i class="icon-36"></i>-->
            </div>
        </div>
    </div><?php endif; ?>
<div class="user-list-contain">
    <div class="index_w_o p32">
        <div class="z"><a href="#"><span>团队成员</span></a></div>
        <a href="<?php echo U('Index/search_member');?>"><div class="member-search"><i class="iconfont">&#xe65c;</i>搜索</div></a>
        <!--<div class="member-search"><i class="iconfont">&#xe65c;</i>搜索</div>-->
    </div>
    <?php if(is_array($list2)): $i = 0; $__LIST__ = $list2;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="page-bd" id="sysid">
            <div class='index-rider_box collapse'>
                <li>
                    <div class="weui-flex js-category" >
                        <div class="weui-flex__item"><i class="iconfont"><?php echo ($vo["icon"]); ?></i><?php echo ($vo["name"]); ?></div>
                        <!--<i class="icon icon-74"></i>-->
                        <span class="member-number" style="margin-right: 10px;color: #999"><?php echo ($vo["count"]); ?></span>
                        <i class="icon-74">
                        </i>
                    </div>
                    <!--<p style="font-size:1.4rem;text-align:center;margin-bottom:.8rem;color:#333"><?php echo ($vo["name"]); ?></p>-->
                    <?php $_result=SidType($vo['id'],$user['sysid'],$selfId);if(is_array($_result)): $i = 0; $__LIST__ = $_result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="page-category js-categoryInner page-category-content">
                            <div class="weui-cell weui-cell_access" style="align-items: flex-start">
                                <div class="weui-cell__hd" style="position: relative;margin-right: 10px;">
                                    <img src="<?php echo ($vo["headimgurl"]); ?>" style="width: 50px;display: block;border-radius: 5px">
                                </div>
                                <div class="weui-cell__bd">

                                        <div style="display: flex;justify-content: space-between;align-items: center;text-align: left;">
                                            <div>
                                                <div style="display: inline-flex;justify-content: center;align-items: center">
                                                    <p style="font-size: 17px"><?php if($vo['name'] == null or $vo['name'] == ' '): ?><span style="color: red">未实名注册会员</span><?php else: echo ($vo["name"]); endif; ?></p>
                                                    <!--<span style="background: #153afa;border-radius: 5px;font-size: 11px;padding: 2px 6px;color: #fff;margin-left: 5px;height: 15px"><?php echo ($vo["level"]); ?></span>-->
                                                </div>
                                                <p style="font-size: 13px;color: #888888;">系统账号：<?php echo ($vo["sysid"]); ?><button class="weui-btn weui-btn_mini orange" style="padding: 0 .5rem;line-height: 1.6rem;font-size: 1.2rem" onclick="copy('<?php echo ($vo["sysid"]); ?>')">复制</button></p>
                                                <p style="font-size: 13px;color: #888888;">注册时间：<?php echo (date('Y-m-d H:i:s', $vo["times"])); ?></p>
                                            </div>

                                            <!--<div class="rider-number" style="flex-grow: 1">
                                                <p>团队人数</p>
                                                <p style="font-size: 17px;color: #005DAC;margin-top: .3rem;font-weight: 500"><?php echo ($vo["count_child"]); ?>人</p>
                                            </div>-->
                                            <a href="<?php echo U('index/rider',array('sysid'=>$vo['sysid']));?>">
                                            <div>
                                                <div style="content: '';display: inline-block;height: 6px;width: 6px;border-width: 2px 2px 0 0;border-color: #c8c8cd;border-style: solid;-webkit-transform: matrix(.71,.71,-.71,.71,0,0);transform: matrix(.71,.71,-.71,.71,0,0);margin-top: 8px;right: 15px">
                                                </div>
                                            </div>
                                            </a>
                                        </div>

                                    <div class="weui-flex" style="flex-wrap:wrap">
                                        <div class="weui-flex__item">
                                            <div class="user-info-about">
                                                <p class="user-info-number"><?php echo ($vo["outnums"]); ?></p>
                                                <p class="user-info-desc">销售量</p>
                                            </div>
                                        </div>
                                        <div class="weui-flex__item">
                                            <div class="user-info-about">
                                                <p class="user-info-number">￥<?php echo ($vo["salemoney"]); ?></p>
                                                <p class="user-info-desc">销售额</p>
                                            </div>
                                        </div>
                                        <div class="weui-flex__item">
                                            <div class="user-info-about">
                                                <p class="user-info-number">￥<?php echo ($vo["innums"]); ?></p>
                                                <p class="user-info-desc">进货量</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!--<div class="index-rider_box-item">
                            <a href="<?php echo U('index/rider',array('sysid'=>$vo['sysid']));?>">
                                <p class="head"><img src="<?php echo ($vo["headimgurl"]); ?>"/></p>
                                <p class="name"><?php echo ($vo["nickname"]); ?></p>
                                <p class="level">
                                    <button><?php echo (getUserLevel($vo['level'],$level)); ?></button>
                                <p>进货量:<?php echo ($vo["innums"]); ?>出货量:<?php echo ($vo["outnums"]); ?></p>

                                </p>
                            </a>
                        </div>--><?php endforeach; endif; else: echo "" ;endif; ?>
                </li>
            </div>
        </div><?php endforeach; endif; else: echo "" ;endif; ?>
</div>
<!--BEGIN toast-->
<div id="toast" style="display: none;">
    <div class="weui-mask_transparent"></div>
    <div class="weui-toast">
        <i class="weui-icon-success-no-circle weui-icon_toast"></i>
        <p class="weui-toast__content">系统账号已复制</p>
    </div>
</div>
<!--end toast-->


<script>
    $(document).ready(function() {
        var sysid="<?php echo ($user["sysid"]); ?>";
        console.log(sysid);
        $.ajax({
            cache: false,
            async: true,
            dataType: 'json',
            type: 'post',
            url: "<?php echo U('index/getTeamNums');?>",
            data:{sysid:sysid},
            success: function (data){
                $('#count').html(data);
            }
        });
    })
</script>
<script>
    $(function(){
        $('.collapse .js-category').click(function(){
            $parent = $(this).parent('li');
            if($parent.hasClass('js-show')){
                $parent.removeClass('js-show');
                $(this).children('i').removeClass('icon-35').addClass('icon-74');
            }else{
                $parent.siblings().removeClass('js-show');
                $parent.addClass('js-show');
                $(this).children('i').removeClass('icon-74').addClass('icon-35');
                $parent.siblings().find('i').removeClass('icon-35').addClass('icon-74');
            }
        });

    });

</script>

<script language="javascript">
    function copy(message) {
        var input = document.createElement("input");
        input.value = message;
        document.getElementById('sysid').appendChild(input);
        input.select();
        input.setSelectionRange(0, input.value.length), document.execCommand('Copy');
        document.getElementById('sysid').removeChild(input);

        var $toast = $('#toast');
        if ($toast.css('display') != 'none') return;
        $toast.fadeIn(100);
        setTimeout(function () {
            $toast.fadeOut(100);
        }, 2000);
    }
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