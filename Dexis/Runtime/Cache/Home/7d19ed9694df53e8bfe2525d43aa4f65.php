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
                <div class="user-header">
    <div class="index-rider">
        <div class="head" id="showIOSActionSheet">
            <img src="<?php echo ($user["headimgurl"]); ?>">
            <p class="name">
                <?php if(($user["name"]) != ""): echo ($user["name"]); endif; ?>
            </p>
        </div>
        <div style="width: 100%">
            <p class="level" style="text-align: left">
                <!--<button><?php echo (getUserLevel($user['level'],$level)); ?></button>-->
                <!--联创  &#xe64d;
                游客  &#xe648;
                周体验 &#xe649;
                月体验 &#xe64a;
                黄金  &#xe64b;
                钻石  &#xe64c;-->
                <button class="<?php echo ($class); ?>"><i class="iconfont"><?php echo ($icon); ?></i><?php echo (getUserLevel($user['level'],$level)); ?></button>
            </p>
            <div class="user-grow" style="color: #fff;">
                <p class="user-grow-name">会员成长值</p>
                <div class="user-grow-point" style="display: flex;justify-content: space-between;width: 100%">
                    <p class="grow-points"><?php echo ($info["totalpoints"]); ?><span>分</span></p>
                    <?php if($reclevel['cat'] != 0): ?><p class="points-target">还差<?php echo ($reclevel["cat"]); ?>分升级<?php echo ($reclevel["name"]); ?></p><?php endif; ?>
                   <!-- <p class="points-target">还差<?php echo ($reclevel["cat"]); ?>分分升级<?php echo ($reclevel["name"]); ?></p>-->

                </div>
            </div>
            <div class="progress">
                <div class="loader-container" style="width: <?php echo ($reclevel["loader"]); ?>%"></div>
            </div>
          <!--  <p style="text-align: center;color: #fff;font-size: 14px;margin-top: 5px">
                系统账号：<span id="sysid"><?php echo ($user["sysid"]); ?></span>
                &lt;!&ndash; <textarea style="position: absolute;top: 0;left: 0;opacity: 0;z-index: -10;" id="input"></textarea> &ndash;&gt;
                <button class="weui-btn weui-btn_mini weui-btn_default" onclick="copy('<?php echo ($user["sysid"]); ?>')" style="line-height: 1.8;color: #2b3c64;padding-left: 8px;padding-right: 8px">点击复制</button>
            </p>-->
            <div class="user-grow money-grow" style="color: #fff;">
                <p class="user-grow-name">收益中心</p>
            </div>
        </div>
    </div>

    <div class="weui-flex user-data">
        <div class="weui-flex__item">
            <a href="<?php echo U('users/number_list',array('id'=>$user['id'],'type'=>1));?>">
                <p class="weui-grid__label user-data-des">销售量(盒)</p>
                <p class="weui-grid__label user-data-number"><?php echo ($saleinfo["out_nums"]); ?></p>

            </a>
        </div>
        <div class="weui-flex__item">

            <a href="<?php echo U('users/number_list',array('id'=>$user['id'],'type'=>1));?>">
                <p class="weui-grid__label user-data-des">销售额</p>
                <p class="weui-grid__label user-data-number"><?php echo ($saleinfo["salemoney"]); ?></p>
            </a>
        </div>

        <div class="weui-flex__item">
            <a href="<?php echo U('index/rider');?>">
                <p class="weui-grid__label user-data-des">团队人数</p>
                <p class="weui-grid__label user-data-number" id="count"><?php echo ($user["team"]); ?></p>
            </a>
        </div>

    </div>
</div>
<div class="weui-cells user-item" style="margin-top: 0">
    <diV class="weui-cell weui-cell_access">
        <div class="weui-cell__bd">
            <p>我的推荐人</p>
        </div>
        <div class="weui-cell__ft"><span class="sysid" style="color: #868e94"><?php echo ($parent["name"]); ?></span> <a class="weui-btn weui-btn_mini weui-btn_default" href="tel:<?php echo ($parent["phone"]); ?>">拨打电话</a></div>
    </div>
    <div class="weui-cell weui-cell_access" style="padding-bottom: 0;padding-top: 0">
        <div class="weui-cell__bd">
            <p>我的二维码</p>
        </div>
        <a href="<?php echo U('Wcix/share',array('sysid'=>$user['sysid']));?>"><div class="weui-cell__ft"><i class="iconfont">&#xe641;</i></div></a>
    </div>
    <diV class="weui-cell weui-cell_access">
        <div class="weui-cell__bd">
            <p>系统账号</p>
        </div>
        <div class="weui-cell__ft"><span class="sysid" id="sysid"><?php echo ($user["sysid"]); ?></span> <button class="weui-btn weui-btn_mini weui-btn_default" onclick="copy('<?php echo ($user["sysid"]); ?>')">复制</button></div>
    </div>

    <!--<diV class="weui-cell weui-cell_access">
        <div class="weui-cell__bd">
            <p>现出货人</p>
        </div>
        <div class="weui-cell__ft"><span id="sysid"><?php echo ($tuser["name"]); ?></span> <a class="weui-btn weui-btn_mini weui-btn_default" href="tel:<?php echo ($tuser["phone"]); ?>">拨打电话</a></div>
    </div>-->
</div>

<div class="weui-panel weui-panel_access user-menu-main-title">
    <div class="weui-panel__ft">
        <div class="weui-cell weui-cell_access weui-cell_link">
            <div class="weui-cell__bd user-menu-title">货物管理</div>
            <span class="user-menu-title-left">库存剩余:<b><?php echo ($user["stock"]); ?>盒</b></span>
        </div>
    </div>
</div>
<div class="user-menu">
    <a href="<?php echo U('malls/goods','sn=415244730815416283');?>" class="user-menu_item">
        <p class="icon"><img src="/Public/static/imgs/s8.png"/></p>
        <p class="fonts">补充库存</p>
    </a>
    <a href="<?php echo U('users/getNumCg');?>" class="user-menu_item">
        <p class="icon"><img src="/Public/static/imgs/kcjl.png"/></p>
        <p class="fonts">库存记录</p>
    </a>
    <a  href="<?php echo U('Users/stock');?>" class="user-menu_item">
    <!--<a  href="javascript:getSrOut()" class="user-menu_item">-->
        <p class="icon"><img src="/Public/static/imgs/s9.png"/></p>
        <p class="fonts">提货发货</p>
    </a>
    <a href="<?php echo U('Users/getStockList');?>" class="user-menu_item">
        <p class="icon"><img src="/Public/static/imgs/s10.png"/></p>
        <p class="fonts">提货记录</p>
    </a>
</div>
<div class="user-menu">
    <a href="<?php echo U('Users/turnstock');?>" class="user-menu_item" >
        <p class="icon"><img src="/Public/static/imgs/s11.png"/></p>
        <p class="fonts">库存转让</p>
    </a>
    <a href="<?php echo U('Users/charts');?>" class="user-menu_item">
        <p class="icon"><img src="/Public/static/imgs/s6.png"/></p>
        <p class="fonts">图表分析</p>
    </a>
    <a href="javacript:void(0)" class="user-menu_item">

    </a>
    <a href="javacript:void(0)" class="user-menu_item">

    </a>
</div>
<!--直播设置-->
<div class="weui-panel weui-panel_access user-menu-main-title">
    <div class="weui-panel__ft">
        <div class="weui-cell weui-cell_access weui-cell_link">
            <div class="weui-cell__bd user-menu-title">直播</div>
            <span class="user-menu-title-left">产品和模式的全方位多角度剖析</span>
        </div>
    </div>
</div>
<div class="user-menu">

    <a href="<?php echo U('Users/liveList');?>" class="user-menu_item">
        <p class="icon"><img src="/Public/static/imgs/live.png"/></p>
        <p class="fonts">直播间列表</p>
    </a>
    <?php if($user["level"] == 6): ?><a href="<?php echo U('Users/bindLive');?>" class="user-menu_item">
            <p class="icon"><img src="/Public/static/imgs/live2.png"/></p>
            <p class="fonts">绑定直播号</p>
        </a>
        <?php else: ?>
        <a href="javacript:void(0)" class="user-menu_item">

        </a><?php endif; ?>

    <a href="javacript:void(0)" class="user-menu_item">

    </a>
    <a href="javacript:void(0)" class="user-menu_item">

    </a>
</div>

<!--0元计划-->
<div class="weui-panel weui-panel_access user-menu-main-title">
    <div class="weui-panel__ft">
        <div class="weui-cell weui-cell_access weui-cell_link">
            <div class="weui-cell__bd user-menu-title">0元计划</div>
            <span class="user-menu-title-left">申请0元计划请先实名认证</span>
        </div>
    </div>
</div>
<div class="user-menu">
    <a href="<?php echo U('Users/plans');?>" class="user-menu_item">
        <p class="icon"><img src="/Public/static/imgs/s13.png"/></p>
        <p class="fonts">0元计划记录</p>
    </a>
    <a href="<?php echo U('Users/idauth');?>" class="user-menu_item">
        <p class="icon"><img src="/Public/static/imgs/s14.png"/></p>
        <p class="fonts">实名认证</p>
    </a>
    <a href="javacript:void(0)" class="user-menu_item">

    </a>
    <a href="javacript:void(0)" class="user-menu_item">

    </a>
</div>

<div class="weui-panel weui-panel_access user-menu-main-title">
    <div class="weui-panel__ft">
        <div class="weui-cell weui-cell_access weui-cell_link">
            <div class="weui-cell__bd user-menu-title">个人信息</div>
        </div>
    </div>
</div>
<div class="user-menu">
    <a href="<?php echo U('Users/bingphone');?>" class="user-menu_item">
        <p class="icon"><img src="/Public/static/imgs/s16.png"/></p>
        <p class="fonts">手机绑定</p>
    </a>
    <a href="<?php echo U('Users/addr');?>" class="user-menu_item">
        <p class="icon"><img src="/Public/static/imgs/s17.png"/></p>
        <p class="fonts">收货地址</p>
    </a>
    <a href="<?php echo U('Users/bank');?>" class="user-menu_item">
        <p class="icon"><img src="/Public/static/imgs/s18.png"/></p>
        <p class="fonts">绑定银行卡</p>
    </a>
    <a href="<?php echo U('Users/uppass');?>" class="user-menu_item">
        <p class="icon"><img src="/Public/static/imgs/s19.png"/></p>
        <p class="fonts">修改密码</p>
    </a>
</div>
<!--<div class="weui-cells user-item">
    <a class="weui-cell weui-cell_access" href="<?php echo U('Users/codes');?>">
        <div class="weui-cell__bd">
            <p>我的二维码</p>
        </div>
        <div class="weui-cell__ft"></div>
    </a>
</div>-->
<!--BEGIN toast-->
<div id="toast" style="display: none;">
    <div class="weui-mask_transparent"></div>
    <div class="weui-toast">
        <i class="weui-icon-success-no-circle weui-icon_toast"></i>
        <p class="weui-toast__content">系统账号已复制</p>
    </div>
</div>
<!--end toast-->
<!--<a href="javascript:;" class="weui-btn weui-btn_default" id="showToast">成功提示</a>-->
<!--BEGIN actionSheet-->
<div>
    <div class="weui-mask" id="iosMask" style="display: none"></div>
    <div class="weui-actionsheet" id="iosActionsheet">
        <div class="weui-actionsheet__menu">
            <a href="<?php echo U('Users/photochange');?>"><div class="weui-actionsheet__cell">修改头像</div></a>
        </div>
        <div class="weui-actionsheet__action">
            <div class="weui-actionsheet__cell" id="iosActionsheetCancel">取消</div>
        </div>
    </div>
</div>
<script type="text/javascript">
    // ios
    $(function(){
        var $iosActionsheet = $('#iosActionsheet');
        var $iosMask = $('#iosMask');
        function hideActionSheet() {
            $iosActionsheet.removeClass('weui-actionsheet_toggle');
            $iosMask.fadeOut(200);
        }
        $iosMask.on('click', hideActionSheet);
        $('#iosActionsheetCancel').on('click', hideActionSheet);
        $("#showIOSActionSheet").on("click", function(){
            $iosActionsheet.addClass('weui-actionsheet_toggle');
            $iosMask.fadeIn(200);
        });
    });
</script>
<a href="<?php echo U('Users/out');?>" class="weui-btn user-btns">退出帐号</a>
<style>
    #clipArea {
        height: 300px;
    }
    #file,
    #clipBtn {
        margin: 20px;
    }
    #view {
        margin: 0 auto;
        width: 200px;
        height: 200px;
        background-color: #666;
    }
</style>
<!--<div id="clipArea"></div>-->
<!--<input type="file" id="file">-->
<!--<button id="clipBtn">截取</button>-->
<!--<div id="view"></div>-->
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

    function gengxing() {
        alert('提货系统正在升级中');
    }
</script>
<!--头像剪裁-->
<script src="/Public/home/photoclip/js/hammer.min.js"></script>
<script src="/Public/home/photoclip/js/iscroll-zoom-min.js"></script>
<script src="/Public/home/photoclip/js/lrz.all.bundle.js"></script>

<script src="/Public/home/photoclip/js/PhotoClip.js"></script>

<script>
    var pc = new PhotoClip('#clipArea', {
        size: 260,
        outputSize: 640,
        //adaptive: ['60%', '80%'],
        file: '#file',
        view: '#view',
        ok: '#clipBtn',
        //img: 'img/mm.jpg',
        loadStart: function() {
            console.log('开始读取照片');
        },
        loadComplete: function() {
            console.log('照片读取完成');
        },
        done: function(dataURL) {
            $.ajax({
                url: "<?php echo U('Users/editSelfinfo');?>",//请求的url地址
                dataType: "json",//返回的格式为json
                async: true,//请求是否异步，默认true异步，这是ajax的特性
                data: {"img": dataURL},//参数值
                type: "POST",//请求的方式
                beforeSend: function () {
                },//请求前的处理
                success: function (req) {
                },//请求成功的处理
                complete: function () {
                },//请求完成的处理
                error: function () {
                }//请求出错的处理
            })
        },
        fail: function(msg) {
            alert(msg);
        }
    });

    // 加载的图片必须要与本程序同源，否则无法截图
    pc.load('img/mm.jpg');

</script>
<!--
<script type="text/javascript">
    function copyText() {
        var text = document.getElementById("sysid").innerText;
        var input = document.getElementById("input");
        input.value = text; // 修改文本框的内容
        input.select(); // 选中文本
        document.execCommand("copy"); // 执行浏览器复制命令
        alert("复制成功");
    }
</script>-->


<script>
    $(document).ready(function() {
        var sysid="<?php echo ($user["id"]); ?>";
        console.log(sysid);
        $.ajax({
            cache: false,
            async: false,
            dataType: 'json',
            type: 'post',
            url: "<?php echo U('index/getTeamNums');?>",
            data:{sysid:sysid},
            success: function (data){
                $('#count').html(data);
            }
        });
    })
//    获取逾期的出货人
    function getSrOut(){
        $.ajax({
            cache: false,
            async: false,
            dataType: 'json',
            type: 'post',
            url: "<?php echo U('users/havesrout');?>",
            data:'',
            success: function (data){
                if (data.status !== 1) {
                    return alert_wec(data.msg);
                }
                //
               window.location.href = data.data;
            }
        });
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