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
        .akk-idauth-title{
            text-align: center;
            font-size: 1.8rem;
            padding-top: 2rem;
            background: #fff;
        }
        .idcard-container{
            display: flex;
            padding-top: 2rem;
            background: #fff;
        }
        .idcard-container .idcard-item{
            width: 50%;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .idcard-container .idcard-item img{
            display: block;
            width: 12rem;
            height: 7rem;
            justify-self: center;
        }
        .idcard-container .idcard-item .img-input{
            margin-top: 1rem;
            width: 50%;
            height: 1.8rem;
            display: inline-block;
            background: #D0EEFF;
            border: 1px solid #99D3F5;
            border-radius: 4px;
            padding: .2rem 1rem;
            color: #1E88C7;
            line-height: 1.8rem;
            text-align: center;
        }
        .idcard-tips{
            font-size: 1.2rem;
            text-align: center;
            background: #fff;
            padding: .5rem 1rem;
        }
    </style>

</head>
<body>
<form id="myForm" enctype="multipart/form-data">
    <div class="idauth-step1" style="display: block">
        <p class="akk-idauth-title">请拍照上传您的身份证信息</p>
        <div class="idcard-container">
            <div class="idcard-item">
                <img id="idcard-img-1" src="/Public/static/imgs/idcard-1.png" alt="">
                <label for="img-input" class="img-input">
                    拍摄身份证正面
                    <input type="file" style="display: none" name="idCardFace"  id="img-input">
                </label>
            </div>

            <!--<input type="button" onclick="uploadImg" style="width: 100%;height: 70px;background: blue" value="拍摄正面">-->

            <div class="idcard-item">
                <img id="idcard-img-2" src="/Public/static/imgs/idcard-2.png" alt="">
                <label for="img-input2" class="img-input">
                    拍摄手持身份证
                    <input style="display: none" type="file" name="holdIdCardFace" id="img-input2"  value="拍摄正面">
                </label>
            </div>
            <!--<input type="button" onclick="uploadImg" style="width: 100%;height: 70px;background: blue" value="拍摄正面">-->
        </div>
        <div class="idcard-tips">请左手手持银行卡，右手手持身份证，保证证件信息完整</div>
        <div class="weui-cells user-idcard" style="margin-top: 0">
            <div class="weui-cell weui-cell_access">
                <div class="weui-cell__hd">姓名</div>
                <div class="weui-cell__bd">
                    <input class="weui-input" type="text" name="name" value="<?php echo ($info["name"]); ?>" placeholder="请输入姓名">
                </div>
                <div class="weui-cell__ft"></div>
            </div>
            <div class="weui-cell weui-cell_access">
                <div class="weui-cell__hd">手机号码</div>
                <div class="weui-cell__bd">
                    <input class="weui-input" type="number" name="phone" value="<?php echo ($info["phone"]); ?>" pattern="[0-9]*" placeholder="请输入号码">
                </div>
                <div class="weui-cell__ft"></div>
            </div>
            <div class="weui-cell weui-cell_select weui-cell_select-after">
                <div class="weui-cell__hd">
                    <label class="weui-label">证件类型</label>
                </div>
                <div class="weui-cell__bd">
                    <select class="weui-select" name="idtype" dir="rtl">
                        <option value="1">身份证</option>
                    </select>
                </div>
            </div>
            <div class="weui-cell weui-cell_access">
                <div class="weui-cell__hd">证件号码</div>
                <div class="weui-cell__bd">
                    <input class="weui-input" type="text" name="idcard" value="<?php echo ($info["idcard"]); ?>" placeholder="请输入号码">
                </div>
                <div class="weui-cell__ft"></div>
            </div>
            <div class="weui-cell weui-cell_select weui-cell_select-after">
                <div class="weui-cell__hd">
                    <label class="weui-label">性别</label>
                </div>
                <div class="weui-cell__bd">
                    <select class="weui-select" name="sex" dir="rtl">
                        <option value="1" <?php if(($info["sex"]) == "1"): ?>selected<?php endif; ?>>男</option>
                        <option value="2" <?php if(($info["sex"]) == "2"): ?>selected<?php endif; ?>>女</option>
                    </select>
                </div>
            </div>
            <div class="weui-cell weui-cell_access">
                <div class="weui-cell__hd">身份证地址</div>
                <div class="weui-cell__bd">
                    <input class="weui-input" type="text" name="address" value="<?php echo ($info["address"]); ?>" placeholder="请输入地址">
                </div>
                <div class="weui-cell__ft"></div>
            </div>
            <div class="weui-btn-area">
                <div class="weui-btn weui-btn_primary step1">下一步</div>
            </div>
        </div>
    </div>
    <div class="idauth-step2" style="display: none">
        <p class="akk-idauth-title">请拍照上传您的银行卡信息</p>
        <div class="idcard-container">
            <div class="idcard-item">
                <img id="idcard-img-3" src="/Public/static/imgs/idcard-3.png" alt="">
                <label for="img-input3" class="img-input">
                    拍摄银行卡正面
                    <input type="file" style="display: none"   id="img-input3">
                </label>
            </div>
        </div>
        <div class="idcard-container">
            <div class="idcard-item">
                <img id="idcard-img-4" src="/Public/static/imgs/idcard-4.png" alt="">
                <label for="img-input4" class="img-input">
                    拍摄手持身份证和银行卡
                    <input style="display: none" type="file" id="img-input4"  value="拍摄正面">
                </label>
            </div>
        </div>
        <div class="idcard-tips">请左手手持银行卡，右手手持身份证，保证证件信息完整</div>
        <div class="weui-cells user-idcard" style="margin-top: 0">
            <div class="weui-cell weui-cell_select weui-cell_select-after">
                <div class="weui-cell__hd">
                    <label class="weui-label">银行卡类别</label>
                </div>
                <div class="weui-cell__bd">
                    <select class="weui-select" name="back" dir="rtl">
                        <option value="1" <?php if(($info["back"]) == "1"): ?>selected<?php endif; ?>>储蓄卡</option>
                        <option value="2" <?php if(($info["back"]) == "2"): ?>selected<?php endif; ?>>信用卡</option>
                    </select>
                </div>
            </div>
            <div class="weui-cell weui-cell_access">
                <div class="weui-cell__hd">银行卡号</div>
                <div class="weui-cell__bd">
                    <input class="weui-input" type="text" name="backid" value="<?php echo ($info["backid"]); ?>" placeholder="请输银行卡号">
                </div>
                <div class="weui-cell__ft"></div>
            </div>
            <div class="weui-cell weui-cell_access">
                <div class="weui-cell__hd">银行卡名称</div>
                <div class="weui-cell__bd">
                    <input class="weui-input" type="text" name="backname" value="<?php echo ($info["backname"]); ?>" placeholder="请输银行卡名称">
                </div>
                <div class="weui-cell__ft"></div>
            </div>

            <input type="hidden" name="memberId" value="<?php echo ($memberId["memberId"]); ?>"/>
            <input type="hidden" name="idCardFace" id="idCardFace" value=""/>
            <input type="hidden" name="holdIdCardFace" id="holdIdCardFace" value=""/>
            <input type="hidden" name="bankCardFace" id="bankCardFace" value=""/>
            <input type="hidden" name="holdBankAndIdFace" id="holdBankAndIdFace" value=""/>
            <div class="weui-btn-area">
                <div class="weui-btn weui-btn_primary step2" id="sumbit">提交</div>
                <div class="weui-btn weui-btn_primary step-back">返回上一步</div>
            </div>
        </div>
    </div>
</form>




<script>
    $('.step1').click(function () {
        $(".idauth-step1").css('display','none');
        $(".idauth-step2").css('display','block');
    });
    $('.step-back').click(function () {
        $(".idauth-step1").css('display','block');
        $(".idauth-step2").css('display','none');
    });
    $('#sumbit').click(function () {
        var temp = $("form").serializeArray();
        var data = objToArray(temp);
        if(data.name===''){
            alert('请输入姓名');
            return false;
        }
        if(!data.name.match( /^[\u4E00-\u9FA5]{1,}$/)){
            alert('请输入正确姓名');
            return false;
        }
        var pattern1 = /^1[3456789]\d{9}$/;
        if (!pattern1.test(data.phone)){
            alert('请输入正确手机号');
            return false;
        }
        var pattern2 = /(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/;
        if (!pattern2.test(data.idcard)){
            alert('请输入正确身份证号');
            return false;
        }
        var back=  CheckBankNo(data.backid);
        if(back==false){
            return false;
        }
        //showPreLoading();
        $.ajax({
            url: "<?php echo U('Index/idcard');?>", type: 'post',
            data: data, dataType: "json",
            success: function (res) {
                if(res.success===false){
                    alert(res.msg);
                } else {
                    alert('资料上传成功，审核为时间2-3天，请耐心等待')
                    window.location.href=res.url;
                }

            },
            error: function (res) {
                if(res.success===false){
                    alert(res.msg);
                }
            }
        });
    });
    function CheckBankNo(t_bankno) {
        //var bankno = $.trim(t_bankno.val());
        if(t_bankno == "") {
            alert("请填写银行卡号");
            return false;
        }
        if(t_bankno.length < 16 || t_bankno.length > 19) {
            alert("银行卡号错误,请检查输入卡号是否正确");
            return false;
        }
        var num = /^\d*$/; //全数字
        if(!num.exec(t_bankno)) {
            alert("银行卡号错误,请检查输入卡号是否正确");
            return false;
        }
        //开头6位
        var strBin = "10,18,30,35,37,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,58,60,62,65,68,69,84,87,88,94,95,98,99";
        if(strBin.indexOf(t_bankno.substring(0, 2)) == -1) {
            alert("银行卡号错误,请检查输入卡号是否正确");
            return false;
        }
    }

    //身份证正面

    $('#img-input').change(function () {
        var reader = new FileReader();
        var file = this.files[0];
        var fileSize =(file.size / 1024).toFixed(2);
        var maxImg = 10;  //设置最大图片大小
        if(Number(fileSize) > (maxImg * 1024)){
            alert("图片不能大于10M");
            return;
        };
        reader.readAsDataURL(file);
        reader.onload = function () {
            var re = this.result;
            $('#idcard-img-1').attr("src",re);
        };
        var imgsrc='#idCardFace';
        uploadImg(file,imgsrc);
    });

    //手持身份证
    $('#img-input2').change(function () {
        var reader = new FileReader();
        var file = this.files[0];
        var fileSize =(file.size / 1024).toFixed(2);
        var maxImg = 10;  //设置最大图片大小
        if(Number(fileSize) > (maxImg * 1024)){
            alert("图片不能大于10M");
            return;
        };
        reader.readAsDataURL(file);
        reader.onload = function () {
            var re = this.result;
            $('#idcard-img-2').attr("src",re);
        };
        var imgsrc='#holdIdCardFace';
        uploadImg(file,imgsrc);
    });
    //拍摄银行卡正面
    $('#img-input3').change(function () {
        var reader = new FileReader();
        var file = this.files[0];
        var fileSize =(file.size / 1024).toFixed(2);
        var maxImg = 10;  //设置最大图片大小
        if(Number(fileSize) > (maxImg * 1024)){
            alert("图片不能大于10M");
            return;
        };
        reader.readAsDataURL(file);
        reader.onload = function () {
            var re = this.result;
            $('#idcard-img-3').attr("src",re);
        };
        var imgsrc='#bankCardFace';
        uploadImg(file,imgsrc);
    });
    //拍摄手持身份证银行卡
    $('#img-input4').change(function () {

        var reader = new FileReader();
        var file = this.files[0];
        var fileSize =(file.size / 1024).toFixed(2);
        var maxImg = 10;  //设置最大图片大小
        if(Number(fileSize) > (maxImg * 1024)){
            alert("图片不能大于10M");
            return;
        };
        reader.readAsDataURL(file);
        reader.onload = function () {
            var re = this.result;
            $('#idcard-img-4').attr("src",re);
        };
        var imgsrc='#holdBankAndIdFace';
        uploadImg(file,imgsrc);
    });


    function uploadImg(file,imgsrc){
        //console.log("ajax上传图片");
        var formData = new FormData();
        formData.append("picFile",file);
        showPreLoading();
        $.ajax({
            url: 'http://akk.028wkf.cn/gateway/app-api/upload-image?picType=NORMAL&catalog=auth',
            // url: "<?php echo U('Index/upload_image');?>",
            type: 'POST',
            data: formData,
            async: false,
            cache: false,
            contentType: false,
            processData: false,
            success: function (returndata) {
                $(imgsrc).val(returndata.data.imgPath);
                hidePreLoading();
                console.log(returndata.data.imgPath);
                // $('#idcard-img-1').attr("src",returndata.data.imgPath);
            },
            error: function (returndata) {
                alert('上传失败');
            }
        });
    }
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