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
                <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <style>
        .search-container{
            display: flex;
            padding: .4rem;
            height: 4rem;
            align-items: center;
            font-size: 1.4rem;
            border-bottom: 1px solid #f0f0f0;
        }
        .select-level{
            font-size: 1.4rem;
            position: relative;
            color: #323436;
            flex: 1;
        }
        #idNumber1 {
            overflow: hidden;
            text-align: left;
            height: 2.6rem;
            border: none;
            font-size: 1.4rem;
            width: 100%;
            flex: 3;
        }
        #idNumber1::-webkit-input-placeholder{
            color: #898e92;
        }
        #idNumber1::-moz-placeholder{  //不知道为何火狐的placeholder的颜色是粉红色，怎么改都不行，希望有大牛路过帮忙指点
            color: #898e92;
        }
        #idNumber1:-ms-input-placeholder{  //由于我的IE刚好是IE9，支持不了placeholder，所以也测试不了(⊙﹏⊙)，有IE10以上的娃可以帮我试试
            color: #898e92;
        }
        .delete{
            margin: 0 15px;
        }
        .delete>div {
            height: 3rem;
            line-height: 3rem;
            padding: 0 1rem;
            text-align: center;
            font-size: 1.2rem;
            box-sizing:border-box;
            color: #898e92;
            margin-right: 1rem;
            background: #f3f6f8;
            border-radius: 2.5rem;
            margin-bottom: 1rem;
        }
        .history{
            display: flex;
            flex-wrap: wrap;
        }

        #search {
            height:3rem;
            font-size: 14px;
            line-height: 14px;
            color: #005cac;
            padding: 0 .8rem;
            font-weight: bold;
            border: none;
            background: none;
        }

        #his-dele {

        }

        #idNumber1 input[type="image"]{
            width: 1.16rem;height: 1.16rem;
            float: left;
            margin-right: .42rem;
        }
        #idNumber1 input[type="text"]{
            height: 1.16rem;
            line-height: 1.16rem;
            float: left;
            color: #878787;
            text-align: left;
            font-size: .62rem;
            background: none;
        }
        .search-history-titile{
            display: flex;
            font-size: 1.2rem;
            height: 4rem;
            line-height: 4rem;
            color: #898e92;
            justify-content: space-between;
            align-items: center;
        }

    </style>
</head>
<body style="background: #fff;height: 100vh">


<div class="search-container">
    <select name="level" id="level1" class="weui-select select-level">
        <option value="">全部等级</option>
        <?php if(is_array($list2)): $i = 0; $__LIST__ = $list2;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><option value="<?php echo ($v["id"]); ?>"><?php echo ($v["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
    </select>
    <i class="iconfont">&#xe65b;</i>
    <div class="board" style="border-right:1px solid #d9d9d9;width: 2px;color: red;height: 1.8rem;margin: 0 1rem"></div>
    <input type="text" class="input-search" name="name" id="idNumber1" placeholder="请输入用户真实姓名" style="text-overflow:ellipsis;">
    <!--<input type="button" id="search" value="搜索">-->
    <button id="search">搜索</button>
</div>

<div class="history-container" style="display: none">
    <div class="search-history-titile m32">
        <div>搜索历史</div>
        <div class="history" id="his-dele" style="display: none"><i class="iconfont">&#xe6be;</i>清除搜索记录</div>
    </div>

    <div class="delete history">
    </div>

</div>
<div id="model">

</div>
<!--清空历史记录-->

<script type="text/javascript">
    $(document).ready(function(){
        $(".input-search").focus(function(){
            // $(".history-container").css("display","block")
            $(".history-container").fadeIn();
        });
        $(".input-search").blur(function(){
            $(".history-container").fadeOut();
        });
    });
    $(document).delegate(".delete>div", "click", function() {
        $("#idNumber1").val($(this).text());
    });

    /*搜索记录相关*/
    //从localStorage获取搜索时间的数组
    var hisTime;
    //从localStorage获取搜索内容的数组
    var hisItem;
    //从localStorage获取最早的1个搜索时间
    var firstKey;

    function init() {
        //每次执行都把2个数组置空
        hisTime = [];
        hisItem = [];
        //模拟localStorage本来有的记录
        //localStorage.setItem("a",12333);
        //本函数内的两个for循环用到的变量
        var i = 0
        for(; i < localStorage.length; i++) {
            if(!isNaN(localStorage.key(i))) {
                hisItem.push(localStorage.getItem(localStorage.key(i)));
                hisTime.push(localStorage.key(i));
            }
        }
        i = 0;
        //执行init(),每次清空之前添加的节点
        $(".delete").html("");
        for(; i < hisItem.length; i++) {
            //alert(hisItem);
            $(".delete").prepend('<div class="word-break">' + hisItem[i] + '</div>')
        }
        if(($(".word-break").length)>6){
            $("#his-dele").css("display","block")
        }
    }
    init();

    $("#search").click(function() {
        var value = $("#idNumber1").val();
        var time = (new Date()).getTime();
        /*if(!value) {
            alert("请输入搜索内容");
            return false;
        }*/

        //输入的内容localStorage有记录
        if($.inArray(value, hisItem) >= 0) {
            for(var j = 0; j < localStorage.length; j++) {
                if(value == localStorage.getItem(localStorage.key(j))) {
                    localStorage.removeItem(localStorage.key(j));
                }
            }
            localStorage.setItem(time, value);
        }
        //输入的内容localStorage没有记录
        else {
            //由于限制了只能有6条记录，所以这里进行判断
            if(hisItem.length > 10) {
                firstKey = hisTime[0]
                localStorage.removeItem(firstKey);
                localStorage.setItem(time, value);
            } else {
                localStorage.setItem(time, value)
            }
        }
        init();
        //正式的时候要提交的！！！
        //$("#form1").submit()

    });

    //清除记录功能
    $("#his-dele").click(function() {
        var f = 0;
        for(; f < hisTime.length; f++) {
            localStorage.removeItem(hisTime[f]);
        }
        init();
    });
    //苹果手机不兼容出现input无法取值以下是解决方法
    $(function() {
        $('.word-break').click(function() {
            var div = $(this).text();
            $('#idNumber1').val(div);
        })
        //取到值以后button存储无法取值，这里强迫浏览器强行刷新可解决
        $('#search').click(function() {
            //window.location.reload();
        })
    })

    function copy(message) {
        var input = document.createElement("input");
        input.value = message;
        document.body.appendChild(input);
        input.select();
        input.setSelectionRange(0, input.value.length), document.execCommand('Copy');
        document.body.removeChild(input);

        var $toast = $('#toast');
        if ($toast.css('display') != 'none') return;
        $toast.fadeIn(100);
        setTimeout(function () {
            $toast.fadeOut(100);
        }, 2000);
    }
    $('#search').click(function(){
        $("#model").empty();
        var level =$('#level1').val();
        var name =$('#idNumber1').val();
        if(!name) {
            alert("请输入搜索内容");
            return false;
        }
        showPreLoading();
        $.get("<?php echo U('Index/search_member');?>",{'name':name,'level': level},function(data){
            hidePreLoading();
            if(data.status==1){
                var list = data.data;
                var html = '';
                if(list.length==0){
                    html = '<p style="text-align:center;font-size: 2rem;padding-top: 50%;">此用户不在你团队或者用户不存在</p>';
                }else{
                    $(list).each(function(i,e){
                        html += '<div class="page-category js-categoryInner page-category-content">\
                                    <div class="weui-cell weui-cell_access" style="align-items: flex-start">\
                                        <div class="weui-cell__hd" style="position: relative;margin-right: 10px;">\
                                            <img src="'+ e.headimgurl+'" style="width: 50px;display: block;border-radius: 5px">\
                                        </div>\
                             <div class="weui-cell__bd">\
                                            <div style="display: flex;justify-content: space-between;align-items: center;text-align: left;">\
                                                <div>\
                                                    <div style="display: inline-flex;justify-content: center;align-items: center">\
                                                        <p style="font-size: 17px">';
                        if(e.name==null || e.name==" "){
                            html+='<span style="color: red">未实名注册会员</span>';
                        }else{
                            html+=''+ e.name+'</p>';
                        }
                        html+='<span style="background: #153afa;border-radius: 5px;font-size: 11px;padding: 2px 6px;color: #fff;margin-left: 5px;height: 15px">'+ e.level+'</span>\
                        </div>\
                        <p style="font-size: 13px;color: #888888;">系统账号：'+ e.sysid+' <button class="weui-btn weui-btn_mini orange" style="padding: 0 .5rem;line-height: 1.6rem;font-size: 1.2rem" onclick="copy("'+ e.sysid+'")">复制</button></p>\
                    <p style="font-size: 13px;color: #888888;">注册时间：'+ e.times+'</p>\
                    </div>\
                    <a href="<?php echo U('index/rider');?>?sysid='+e.sysid +'">\
                        <div>\
                        <div style="content:'+' '+';display: inline-block;height: 6px;width: 6px;border-width: 2px 2px 0 0;border-color: #c8c8cd;border-style: solid;-webkit-transform: matrix(.71,.71,-.71,.71,0,0);transform: matrix(.71,.71,-.71,.71,0,0);margin-top: 8px;right: 15px">\
                        </div>\
                        </div>\
                    </a>\
                        </div>\
                        <div class="weui-flex" style="flex-wrap:wrap">\
                        <div class="weui-flex__item">\
                        <div class="user-info-about">\
                        <p class="user-info-number">'+ e.outnums+'</p>\
                    <p class="user-info-desc">销售量</p>\
                        </div>\
                        </div>\
                        <div class="weui-flex__item">\
                        <div class="user-info-about">\
                        <p class="user-info-number">￥'+ e.salemoney+'</p>\
                    <p class="user-info-desc">销售额</p>\
                        </div>\
                        </div>\
                        <div class="weui-flex__item">\
                        <div class="user-info-about">\
                        <p class="user-info-number">￥'+ e.innums+'</p>\
                    <p class="user-info-desc">进货量</p>\
                        </div>\
                        </div>\
                        </div>\
                        </div>\
                        </div>\
                        </div>';

                    });
                }
            }
            $("#model").append(html);
        })
    });

</script>

</body>
</html>
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