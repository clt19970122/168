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
                <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>168减肥营</title>
    <script src="/Public/home/js/index.js"></script>
    <link rel="stylesheet" href="/Public/home/css/answer.css">

    <!-- Link Swiper's CSS -->
    <!--<link rel="stylesheet" href="static/css/index.css">-->

    <!-- Demo styles -->
</head>
<body>

<section id="start">
    <div class="btn start zt1">开始测试</div>
</section>

<section id="dati" hidden>
    <div class="answer-bg"></div>
    <!--<div class="dati_top"></div>-->
    <div class="timu_box text1">
    </div>
    <div class="q">Q</div>
    <form>
        <div id="xuanxiang">
        </div>
    </form>
    <div class="btn zt1 next" id="next" hidden>下一题</div>
    <div class="btn zt1 next" id="prev" hidden>上一题</div>
</section>

<section class="baoming formUpdate" style="display: none">
    <div class="baoming-wrap">

    </div>
    <div class="bigbox">
        <form action="">
            <div class="sbox">
                <div class="title">请耐心等待营养师为您定制减脂营养餐方案</div>
                <span>请输入您的姓名</span>
                <input name="name" type="text">
                <span>请输入您的手机号</span>
                <input name="phone" type="number">
                <span>方便联系您的时间</span>
                <select name="contact" style="overflow: hidden">
                    <option value="上午" style="width: 100%">上午</option>
                    <option value="中午" style="width: 100%">中午</option>
                    <option value="下午" style="width: 100%">下午</option>
                    <option value="晚上" style="width: 100%">晚上</option>
                </select>
                <!--<input name="phone" type="number">-->
                <div  class="btn zt1 tijiao" id="tijiao">提交</div>
            </div>
        </form>
    </div>
</section>

<!--成功提示-->
<section id="tips" class="baoming" style="display: none">
    <div class="baoming-wrap">

    </div>
    <div class="bigbox">
        <form action="">
            <div class="sbox">
                <div class="title">请耐心等待营养师为您定制减脂营养餐方案</div>
                <div  class="btn zt1 tijiao know">知道了</div>
            </div>
        </form>
    </div>
</section>


<script src="static/js/html2canvas.min.js"></script>


<script src="/Public/home/js/html2canvas.min.js"></script>
<script>
    var tdata=[
        {t:"您的年龄 （单选）:",type:"one",d:['18以下','18~25岁','26~35岁','36~45岁','46~55岁','55岁以上'],name:"age"},
        {t:"您的性别（单选）:",type:"one",d:['男','女'],name:"sex"},
        {t:"您的身高（单选）:",type:"one",d:['160cm以下','161~170cm','170cm以上'],name:'height'},
        {t:"您的体重（单选）:",type:"one",d:['100斤以下','100~110斤','111~120斤','121斤~130斤','130斤以上'],name:'weight'},
        {t:"您的职业（单选）:",type:"one",d:['长时间看电脑','长时间站立','经常加班者','体力劳动者','自由时间者','其他'],name:'job'},
        {t:"一般吃早餐时间？（单选）:",type:"one",d:['7点以前','7点~8点','8点以后','平时7点过但周末8点以后','不吃早饭'],name:'breakfast'},
        {t:'胃肠道健康异常表现?（多选）',type:'more',d:['腹泻、腹胀','食欲不振','口气不清晰','便秘','情绪低落','面色暗黄、痘痘等皮肤','易生病'],name:'stomach'},
        {t:'现在身体问题？（多选）',type:'more',d:['睡眠','便秘','嘴里有味道','皮肤暗黄泛油光','超重'],name:'problem'},
        {t:'您平时的饮食习惯',type:'two',d:['只吃自己喜欢的食物','饥一顿饱一顿','经常在外就餐','按时吃饭'],name:'habit'},
        {t:'您的口味偏好？（多选）',type:'two',d:['清淡','喜爱甜食','无辣不欢','重油重盐'],name:'taste'},
        {t:'您的饮食偏好？（单选）',type:'one',d:['顿顿有肉','偏爱素菜','一天一顿肉菜','每顿必吃米饭或面食'],name:'diet'},
        {t:'晚餐时间？（单选）',type:'one',d:['18点以前','18:00~19:30','19:31~20:30','20:30以后'],name:'time'},
        {t:'有运动习惯吗？（单选）',type:'one',d:['无','偶尔','一周3~4次','每天'],name:'sport'},
        {t:'此前用过什么方式减脂？ （多选）',type:'two',d:['减肥药','节食','代餐','运动'],name:'way'},
        {t:'有没有相关疾病？',type:'more',d:['无','心脏病','三高','糖尿病','低血糖'],name:'effectLose'},
        {t:'想要减到多少斤？（单选）',type:'one',d:['5斤以内','5~10斤','11斤~20斤','20斤以上'],name:'loseNum'},
        {t:'期望多长时间实现目标？ （单选）',type:'one',d:['3天','7天','21天','30天以上'],name:'howlong'}
    ];
    var i= -1;
    function init(obj) {
        console.log(obj);
        if(obj==1){
            i++;
        }else {
            i--;
            var inputNodes = $(".itemContainer")[i].getElementsByTagName('input[type=checkbox]');
            for (j = 0; j < inputNodes.length; j++) {
                inputNodes[j].checked=false;
            }
        }

        var lastNum = tdata.length;
        console.log("i:"+i);
        console.log("lst:"+lastNum);
        if(i==lastNum){
            baoming();
            return false;
        }else {
            $('.next').hide();
            $('.next').off('click');
            $('#next').click(function(){init(1)});
            $('#prev').click(function(){init(-1)});

        }
        $('.timu_box').html(tdata[i].t);
        var k=0;
        var option_html = [];
        var dLength= tdata[i].d.length;
        var int = tdata[i].type;

        if(int=="one"){
            for(k=0;k<dLength;k++){
                option_html += '<div class="xx">'+'<label><input type="radio" name="'+tdata[i].name+'" value="'+tdata[i].d[k]+'">'+tdata[i].d[k]+'</label>'+'</div>';
            }
            var one = '<div class="itemContainer">'+option_html+'</div>';
            putItem(one);
        }
        if(int=="two"){
            for(k=0;k<dLength;k++){
                option_html += '<div class="xx">'+'<label><input type="checkbox" name="'+tdata[i].name+'" value="'+tdata[i].d[k]+'">'+tdata[i].d[k]+'</label>'+'</div>';
            }
            var two = '<div class="itemContainer">'+option_html+'</div>';
            putItem(two);
        }
        if(int=="more"){
            for(k=0;k<dLength;k++){
                option_html += '<div class="xx">'+'<label><input type="checkbox" name="'+tdata[i].name+'" value="'+tdata[i].d[k]+'">'+tdata[i].d[k]+'</label>'+'</div>';
            }
            var more = '<div class="itemContainer">'+option_html+'<div class="xx">'+'其他'+'<input checked name="'+tdata[i].name+'" class="other" value="">'+'</div></div>';
            putItem(more);
        }
        if(int == "write"){
            var write = '<div class="itemContainer">'+option_html+'<div class="xx"><input checked name="'+tdata[i].name+'" class="other" value="">'+'</div></div>';
            putItem(write);
        }

        function putItem(po) {
            $('#xuanxiang').append(po);
        }

        $(".itemContainer").hide().eq($(this).index()).show();
        $(':input').change(function () {
            if(i==0){
                $("#prev").hide();
                $("#next").show();
            }else {
                $('.next').show();
            }

        });

    }
    $('.start').click(function(){
        $("#start").hide();
        $("#dati").show();
        init(1);
    });

    function baoming() {
        $('.formUpdate').show();
        $('#tijiao').click(function(){
            var name=$('input[name="name"]').val();
            var phone=$('input[name="phone"]').val();
            if(name==''){
                alert("请输入您的姓名");
                return false;
            }
            if(!(/^1(3|4|5|6|7|8|9)\d{9}$/.test(phone))){
                alert("手机号码有误，请重填");
                return false;
            }
            formdata();
        })

    }
    function formdata() {
        var data ={};
        $.fn.serializeObject = function () {
            var o = {};
            var a = this.serializeArray();
            $.each(a, function () {
                if (o[this.name] !== undefined) {
                    if (!o[this.name].push) {
                        o[this.name] = [o[this.name]];
                    }
                    o[this.name].push(this.value || '');
                } else {
                    o[this.name] = this.value || '';
                }
            });
            var $radio = $('input[type=radio],input[type=checkbox]',this);
            $.each($radio,function(){
                if(!o.hasOwnProperty(this.name)){
                    o[this.name] = '';
                }
            });
            return o;
        };

        var t = $('form').serializeObject();
         console.log(t);
        $.each(t, function () {
            data [this.name] = this.value;
        });
        $('.baoming').hide();
        $.ajax({
            url: "<?php echo U('Weight/index');?>", type: 'post',
            data: t,   dataType: "json",
            success: function (res) {
                if (res.status !== 1) {
                    return alert_wec(res.msg);
                }else {
                    $("#tips").show();
                    $('.know').click(function () {
                        window.location.href = "http://www.jacobhooychina.cn";
                    });

                }
            }
        });
    };

    /* function formdata() {

         var data = {};
         var t = $('form').serializeArray();
         $.each(t, function () {
             data [this.name] = this.value;
         });
         ////
         var count= JSON.stringify(data);
        console.log(count,data);
     //    window.location.href = "/Public/home/imgs/wenda/win.png";
         /!*$.ajax({
             url: "<?php echo U('Weight/index');?>", type: 'post',
             data: data,   dataType: "json",
             success: function (res) {
                 if (res.status !== 1) {
                     return alert_wec(res.msg);
                 }else {

                 }
             }
         });*!/
     };*/
    /* function init(i) {

         console.log("i:"+i);
         var lastNum = tdata.length - 1;
         if(i>lastNum){
             baoming();
             return false;
         }
         console.log("lst:"+lastNum);

             $('.timu_box').html(tdata[i].t);
             var k=0;
             var option_html = [];
             var dLength= tdata[i].d.length;
             var int = tdata[i].type;

             if(int=="one"){
                 for(k=0;k<dLength;k++){
                     option_html += '<div class="xx">'+'<label><input type="radio" name="'+tdata[i].name+'" value="'+tdata[i].d[k]+'">'+tdata[i].d[k]+'</label>'+'</div>';
                 }
                 var one = '<div class="itemContainer">'+option_html+'</div>';
                 putItem(one);
             }
             if(int=="two"){
                 for(k=0;k<dLength;k++){
                     option_html += '<div class="xx">'+'<label><input type="checkbox" name="'+tdata[i].name+'" value="'+tdata[i].d[k]+'">'+tdata[i].d[k]+'</label>'+'</div>';
                 }
                 var two = '<div class="itemContainer">'+option_html+'</div>';
                 putItem(two);
             }
             if(int=="more"){
                 for(k=0;k<dLength;k++){
                     option_html += '<div class="xx">'+'<label><input type="checkbox" name="'+tdata[i].name+'" value="'+tdata[i].d[k]+'">'+tdata[i].d[k]+'</label>'+'</div>';
                 }
                 var more = '<div class="itemContainer">'+option_html+'<div class="xx">'+'其他'+'<input checked name="'+tdata[i].name+'" class="other" value="">'+'</div></div>';
                 putItem(more);
             }
             if(int == "write"){
                 var write = '<div class="itemContainer">'+option_html+'<div class="xx"><input checked name="'+tdata[i].name+'" class="other" value="">'+'</div></div>';
                 putItem(write);
             }
             function putItem(po) {
                 $('#xuanxiang').append(po);
             }
             $(".itemContainer").hide().eq($(this).index()).show();
             $('.nextBtn').click(function(){
                 validate();
             });

     }


     $('.startBtn').click(function(){
         $("#start").hide();
         $("#dati").show();
         init(0);
     });
     function validate() {
         var inputNodes = $(".itemContainer")[i].getElementsByTagName("input");

         for (j = 0; j < inputNodes.length; j++) {
                 if (inputNodes[j].checked && inputNodes[j].value !== "") {
                     $("#tips").hide();
                     i++;
                     init(i);
                 } else {
                     $("#tips").show();
                 }
             }
     }
       function baoming() {
           $('#baoming').show();
           $('#tijiao').click(function(){
               var name=$('input[name="name"]').val();
               var phone=$('input[name="phone"]').val();
               if(name==''){
                   alert("请输入您的姓名");
                   return false;
               }
               if(!(/^1(3|4|5|6|7|8|9)\d{9}$/.test(phone))){
                   alert("手机号码有误，请重填");
                   return false;
               }
               formdata();
           })
       }
     function formdata() {

         var data = {};
         var t = $('form').serializeArray();
         $.each(t, function () {
             data [this.name] = this.value;
         });
         console.log(JSON.stringify(data));
     };*/
</script>
<script>
    wx.ready(function () {
        wx.onMenuShareTimeline({
            title: '2019国际超模「线上减肥营」调查问卷!',
            link: "<?php echo ($ticket["url"]); ?>",
            imgUrl: '<?php echo ($weburl); echo ($ticket["img"]); ?>',
            success: function () {
                // 用户确认分享后执行的回调函数
            },
            cancel: function () {
                // 用户取消分享后执行的回调函数
            }
        });

        wx.onMenuShareAppMessage({
            title: '2019国际超模「线上减肥营」调查问卷!', // 分享标题
            desc: '立下你的减肥军令状 让你健康瘦，轻松美 誓与肉肉抗战到底！', // 分享描述
            link: "<?php echo ($ticket["url"]); ?>",
            imgUrl: '<?php echo ($weburl); echo ($ticket["img"]); ?>',
            type: 'link', // 分享类型,music、video或link，不填默认为link
            dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
            success: function () {
                // 用户确认分享后执行的回调函数
            },
            cancel: function () {
                // 用户取消分享后执行的回调函数
            }
        });
    });
</script>
</body>
</html>
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