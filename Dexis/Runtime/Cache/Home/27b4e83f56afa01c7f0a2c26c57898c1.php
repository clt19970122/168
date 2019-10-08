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
                <link rel="stylesheet" href="/Public/libs/swiper/css/swiper.min.css">
<script src="/Public/libs/swiper/js/swiper.min.js"></script>
<script src="/Public/layer/layer.js"></script>

<style>
    .contentDiv{
        margin-top: 1rem;
    }
    .contentDiv .numContent .on {
        color: #fff!important;
        background: #1aad19!important;
    }
    .contentDiv .numContent{
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: .8rem;
    }
    .contentDiv .numContent li {
        list-style: none;
        width: 46%;
        height: 3rem;
        line-height: 3rem;
        color: #D7D9D8;
        border: solid 1px #D7D9D8;
        border-radius: 0.6rem;
        margin: 0.2rem 0!important;
        text-align: center;
        font-size: 1.8rem;
    }
    .numTitle{
        font-size: 1.9rem;
        color: #005DAC;
    }
    /*计数器样式*/
    .weui-count {
        display: inline-block;
        height: 25px;
        line-height: 25px;
    }
    .weui-count .weui-count__btn {
        height: 21px;
        width: 21px;
        line-height: 21px;
        display: inline-block;
        position: relative;
        border: 1px solid #04BE02;
        border-radius: 50%;
        vertical-align: -6px;
    }
    .weui-count .weui-count__btn:after,
    .weui-count .weui-count__btn:before {
        content: " ";
        position: absolute;
        height: 1px;
        width: 11px;
        background-color: #04BE02;
        left: 50%;
        top: 50%;
        margin-left: -5.5px;
    }
    .weui-count .weui-count__btn:after {
        height: 11px;
        width: 1px;
        margin-top: -5.5px;
        margin-left: -1px;
    }
    .weui-count .weui-count__decrease:after {
        display: none;
    }
    .weui-count .weui-count__increase {
        background-color: #04BE02;
    }
    .weui-count .weui-count__increase:after,
    .weui-count .weui-count__increase:before {
        background-color: white;
    }
    .weui-count .weui-count__number {
        background-color: transparent;
        font-size: 14px;
        border: 0;
        height: 21px;
        width: 2.6rem;
        text-align: center;
        color: #5f646e;
    }
    .seed-num-list{
        padding: 10px 0;
    }
    .seed-num-list p{
        font-size: 16px;
    }
</style>

<div class="swiper-container goods-scroll">
    <div class="swiper-wrapper">
        <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><div class="swiper-slide"><img src="/Public/uploads/goods/<?php echo ($v["imgs"]); ?>"/></div><?php endforeach; endif; else: echo "" ;endif; ?>
    </div>
    <div class="swiper-pagination"></div>
</div>
<div class="goods-descs">
    <div class="goods-descs_title overflow"><?php echo ($info["name"]); ?></div>
    <div class="goods-descs_descs"><?php echo ($info["subname"]); ?></div>

    <div class="contentDiv" id="buytip" style="display: none">
        <div class="numTitle">选择订购数量(个)</div>
        <div class="numContent">
            <li class="on" data-id="1">20</li>
            <li data-id="2">50</li>
        </div>
    </div>

    <div class="goods-descs_price" >
        <span id="goods-pice"><font>参考价:</font>￥<?php echo ($info["price"]); ?><font>元</font></span>

        <input type="hidden" name="buy" value="<?php echo ($buynums); ?>" id='last_buy'/>
        <input type="hidden" name="buylevels" value="<?php echo ($buylevel); ?>" id='buy_level'/>

    </div>

    <?php if($info["gsn"] == 815526127943355165): ?><!--    <p class="goods-descs_descs" style="color: #c21a14;margin-top: 10px">请选择购买数量</p>
            <div style="display: flex;flex-direction: column;">
                <div style="display: flex;align-items: center;align-self: flex-end">
                    <p style="margin-right: 2rem;font-size: 1.4rem">黄瓜育种</p>
                    <div class="goods-descs_price_input" id="goods1">
                        <a href="javascript:changeItemNums1(0);"><img src="/Public/home/imgs/goods_ceil.png"/></a>
                        <input class="buy-number" type="text" name="nums" value="0" id='goods_num1'/>
                        <a href="javascript:changeItemNums1(1);"><img src="/Public/home/imgs/goods_adds.png"/></a>
                    </div>
                </div>
                <div style="display: flex;align-items: center;align-self: flex-end;margin-top: 1rem">
                    <p style="margin-right: 2rem;font-size: 1.4rem">西红柿育种</p>
                    <div class="goods-descs_price_input" id="goods2">
                        <a href="javascript:changeItemNums2(0);"><img src="/Public/home/imgs/goods_ceil.png"/></a>
                        <input class="buy-number" type="text" name="nums2" value="0" id='goods_num2'/>
                        <a href="javascript:changeItemNums2(1);"><img src="/Public/home/imgs/goods_adds.png"/></a>
                    </div>
                </div>

                <div style="display: flex;align-items: center;align-self: flex-end;margin-top: 1rem">
                    <p style="margin-right: 2rem;font-size: 1.4rem">辣椒育种</p>
                    <div class="goods-descs_price_input" id="goods3">
                        <a href="javascript:changeItemNums3(0);"><img src="/Public/home/imgs/goods_ceil.png"/></a>
                        <input class="buy-number" type="text" name="nums3" value="0" id='goods_num3'/>
                        <a href="javascript:changeItemNums3(1);"><img src="/Public/home/imgs/goods_adds.png"/></a>
                    </div>
                </div>
            </div>
    -->
        <!--计数器-->
        <div class="page__bd">
            <!--<div class="weui-cells__title seed-num-list" style="color: #c21a14;">请选择购买数量</div>-->
            <div class="weui-cells">
                <div class="weui-cell seed-num-list">
                    <div class="weui-cell__bd">
                        <p>黄瓜育种</p>
                    </div>
                    <div class="weui-cell__ft">
                        <div class="weui-count">
                            <a class="weui-count__btn weui-count__decrease"></a>
                            <input class="weui-count__number" name="nums" type="number" id="goods_num1" value="0">
                            <a class="weui-count__btn weui-count__increase"></a>
                        </div>
                    </div>
                </div>
                <div class="weui-cell seed-num-list">
                    <div class="weui-cell__bd">
                        <p>西红柿育种</p>
                    </div>
                    <div class="weui-cell__ft">
                        <div class="weui-count">
                            <a class="weui-count__btn weui-count__decrease"></a>
                            <input class="weui-count__number" name="nums" type="number" id="goods_num2" value="0">
                            <a class="weui-count__btn weui-count__increase"></a>
                        </div>
                    </div>
                </div>
                <div class="weui-cell seed-num-list">
                    <div class="weui-cell__bd">
                        <p>辣椒育种</p>
                    </div>
                    <div class="weui-cell__ft">
                        <div class="weui-count">
                            <a class="weui-count__btn weui-count__decrease"></a>
                            <input class="weui-count__number" name="nums" type="number" id="goods_num3" value="0">
                            <a class="weui-count__btn weui-count__increase"></a>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <script>
            var MAX = 999, MIN = 0;
            // 获取焦点事件
            $('.weui-count__number').bind('input propertychange', function(){
                if (isNaN(parseFloat($(this).val())) || parseFloat($(this).val()) <= 0) $(this).val(1);
                setTotal();
            });

            $('.weui-count__decrease').click(function (e) {
                var $input = $(e.currentTarget).parent().find('.weui-count__number');
                var number = parseInt($input.val() || "0") - 1;
                if (number < MIN) number = MIN;
                $input.val(number);
                setTotal();
            });
            $('.weui-count__increase').click(function (e) {
                var $input = $(e.currentTarget).parent().find('.weui-count__number');
                var number = parseInt($input.val() || "0") + 1;
                if (number > MAX) number = MAX;
                $input.val(number);
                setTotal();
            });
            function setTotal() {
                var totalNum = 0;
                //黄瓜
                var totalNum1 = $("#goods_num1").val();
                //西红柿
                var totalNum2 = $("#goods_num2").val();
                //辣椒
                var totalNum3 = $("#goods_num3").val();
                // console.log("黄瓜："+totalNum1+"西红柿："+totalNum2+"辣椒："+totalNum3);
                var aLi = $(".weui-count__number");
                for(var i = 0; i < aLi.length; i++){
                    totalNum += parseInt(aLi[i].value);

                }
                var money=toDecimal2(<?php echo ($info["price"]); ?>*totalNum);
                var str = "<font>参考价:</font>￥" + money + "<font>元</font>";
                $("#goods-pice").html(str);
                //购买总数
                console.log(totalNum);
            }
            function toDecimal2(x) {
                var f = parseFloat(x);
                if (isNaN(f)) {
                    return false;
                }
                var f = Math.round(x*100)/100;
                var s = f.toString();
                var rs = s.indexOf('.');
                if (rs < 0) {
                    rs = s.length;
                    s += '.';
                }
                while (s.length <= rs + 2) {
                    s += '0';
                }
                return s;
            }
        </script>
        <!--计数器end-->
        <?php else: ?>
        <!--168数量变动-->
        <div class="goods-descs_price_input" id="goods">
            <a href="javascript:changeItemNums(0);"><img src="/Public/home/imgs/goods_ceil.png"/></a>
            <input class="buy-number" type="text" name="nums" value="<?php echo ($buynums); ?>" id='goods_num'/>
            <a href="javascript:changeItemNums(1);"><img src="/Public/home/imgs/goods_adds.png"/></a>
        </div>
        <p class="goods-descs_descs" style="color: #c21a14;margin-top: 10px">提示:当前等级为<?php echo ($buylevel); ?>,补货数量不能低于<?php echo ($buynums); ?>盒</p><?php endif; ?>
</div>

<div class="goods-menu">
    <div class="goods-menu_item goods-menu_item_on" id="desc">商品介绍</div>
    <div class="goods-menu_item" id="para">商品参数</div>
    <div class="goods-menu_item" id="adse">广告大片</div>
</div>

<div class="goods-infos">
    <div class="goods-infos_desc" id="desc_on">
        <?php echo (htmlspecialchars_decode($info["context"])); ?>
    </div>
    <div class="goods-infos_para none" id="para_on">

        <div class="goods-infos_para_item">
            <div class="goods-infos_para_item_label">产品类型：</div>
            <div class="goods-infos_para_item_conts"><?php echo ($info["locs"]); ?></div>
        </div>
        <div class="goods-infos_para_item">
            <div class="goods-infos_para_item_label">产品规格：</div>
            <div class="goods-infos_para_item_conts"><?php echo ($info["net"]); ?></div>
        </div>
        <div class="goods-infos_para_item">
            <div class="goods-infos_para_item_label">保质期：</div>
            <div class="goods-infos_para_item_conts"><?php echo ($info["daty"]); ?></div>
        </div>
        <div class="goods-infos_para_item">
            <div class="goods-infos_para_item_label">产品标准号：</div>
            <div class="goods-infos_para_item_conts"><?php echo ($info["batch"]); ?></div>
        </div>
        <div class="goods-infos_para_item">
            <div class="goods-infos_para_item_label">生产许可证：</div>
            <div class="goods-infos_para_item_conts"><?php echo ($info["suit"]); ?></div>
        </div>
        <div class="goods-infos_para_item">
            <div class="goods-infos_para_item_label">包装方式：</div>
            <div class="goods-infos_para_item_conts"><?php echo ($info["sorts"]); ?></div>
        </div>
        <div class="goods-infos_para_item">
            <div class="goods-infos_para_item_label">贮藏方法：</div>
            <div class="goods-infos_para_item_conts"><?php echo ($info["product"]); ?></div>
        </div>
        <div class="goods-infos_para_item">
            <div class="goods-infos_para_item_label">配方专利号：</div>
            <div class="goods-infos_para_item_conts"><?php echo ($info["method"]); ?></div>
        </div>
        <div class="goods-infos_para_item">
            <div class="goods-infos_para_item_label">配方专利名称：</div>
            <div class="goods-infos_para_item_conts"><?php echo ($info["advice"]); ?></div>
        </div>
        <div class="goods-infos_para_item">
            <div class="goods-infos_para_item_label">注意事项：</div>
            <div class="goods-infos_para_item_conts"><?php echo ($info["tips"]); ?></div>
        </div>

    </div>
    <div class="goods-infos_adse none" id="adse_on">
        <iframe src='<?php echo ($info["video"]); ?>' frameborder='0'></iframe>
    </div>
</div>
<?php if(empty($status)): ?><div class="buy-btn-container">
      <!--   <a href="javascript:" id="showIOSActionSheet" class="stage-buy-btn">分期付款</a> -->
        <a href="javascript:doBuyer();"  id='buything'  class="goods-buys">立即购买</a>
    </div>
    <?php else: ?>
    <a href="javascript:doBuyer();" class="goods-buys">补货</a><?php endif; ?>
<input type="hidden" id="isaddstock" value="<?php echo ($status); ?>">
<!--<input type="hidden" id="goodid" value="<?php echo ($info["gsn"]); ?>">-->


<!--<input type="hidden" id="goodid" value="<?php echo ($info["gsn"]); ?>">-->
<!--分期弹窗-->
<div>
    <div class="weui-mask" id="iosMask" style="display: none"></div>
    <div class="weui-actionsheet" id="iosActionsheet">
        <div class="weui-actionsheet__menu">
            <div class="weui-actionsheet__cell"><a href="<?php echo U('malls/agreement_top');?>">分期总代理(联合创始人)</a></div>
            <div class="weui-actionsheet__cell"><a href="<?php echo U('malls/agreement_diamond');?>">分期经销商(钻石)</a></div>
        </div>
        <div class="weui-actionsheet__action">
            <div class="weui-actionsheet__cell" id="iosActionsheetCancel">取消</div>
        </div>
    </div>
</div>
<!--END 分期弹窗-->
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

    // android
    $(function(){
        var $androidActionSheet = $('#androidActionsheet');
        var $androidMask = $androidActionSheet.find('.weui-mask');

        $("#showAndroidActionSheet").on('click', function(){
            $androidActionSheet.fadeIn(200);
            $androidMask.on('click',function () {
                $androidActionSheet.fadeOut(200);
            });
        });
    });
</script>
<script>

    var swiper = new Swiper('.swiper-container', {
        pagination: '.swiper-pagination',
        paginationClickable: true,
        loop: true,
    });
    //页面加载分辨产品
    $(window).load(function () {
        var goodsId = <?php echo ($info['gsn']); ?>;
        if(goodsId=='515382251920431381'){
            $('#goods').remove();
            $('#buytip').css('display','block');

            var str = "<font>参考价:</font>￥58<font>元</font>" +
                "<input type='hidden' name='nums' value='20' id='goods_num'/>" +
                "<input type='hidden' name='price' value='58' id='goods_price'/>";
            $("#goods-pice").html(str);
        }
        //判断产品信息
        if(goodsId!='415244730815416283'){
            $('#showIOSActionSheet').css('display','none');
            $('#buything').attr('class','goods-buys');
        }
    });
    // 获取168输入数量焦点事件
	$('#goods_num').bind('input propertychange', function(){
            if (isNaN(parseFloat($(this).val())) || parseFloat($(this).val()) <= 0) $(this).val(1);
            var result = $("#goods_num").val();
        	// var newcs = type > 0 ? parseInt(value) + 1 : parseInt(value) - 1;
       		// var result = newcs > 0 ? newcs : 1;
            $("#goods_num").val(result);
        	var goodsId = <?php echo ($info['gsn']); ?>;
        	// var goodsId = $('#goodid').val();
        	if(goodsId == '415244730815416283') {
            	changeGoodsPrice(result,goodsId);
        	}
    });
    function changeItemNums(type) {
        // console.log($(opts).text());
        console.log(type);
        $(this).siblings().addClass('user-menu-nav');
        var value = $("#goods_num").val();
        var newcs = type > 0 ? parseInt(value) + 1 : parseInt(value) - 1;
        var result = newcs > 0 ? newcs : 1;
        $("#goods_num").val(result);
        var goodsId = <?php echo ($info['gsn']); ?>;
        // var goodsId = $('#goodid').val();
        if(goodsId == '415244730815416283') {
            changeGoodsPrice(result,goodsId);
        }
    }

    function changeItemNums1(type) {
        $(this).siblings().addClass('user-menu-nav');
        var value = $("#goods_num1").val();1
        var newcs = type > 0 ? parseInt(value) + 1 : parseInt(value) - 1;
        var result = newcs > 0 ? newcs : 0;
        $("#goods_num1").val(result);
        var goodsId = <?php echo ($info['gsn']); ?>;
        // var goodsId = $('#goodid').val();
        if(goodsId == '415244730815416283') {
            changeGoodsPrice(result,goodsId);
        }
    }
    function changeItemNums2(type) {
        $(this).siblings().addClass('user-menu-nav');
        var value = $("#goods_num2").val();
        var newcs = type > 0 ? parseInt(value) + 1 : parseInt(value) - 1;
        var result = newcs > 0 ? newcs : 0;
        $("#goods_num2").val(result);
        var goodsId = <?php echo ($info['gsn']); ?>;
        // var goodsId = $('#goodid').val();
        if(goodsId == '415244730815416283') {
            changeGoodsPrice(result,goodsId);
        }
    }
    function changeItemNums3(type) {
        $(this).siblings().addClass('user-menu-nav');
        var value = $("#goods_num3").val();
        var newcs = type > 0 ? parseInt(value) + 1 : parseInt(value) - 1;
        var result = newcs > 0 ? newcs : 0;
        $("#goods_num3").val(result);
        var goodsId = <?php echo ($info['gsn']); ?>;
        // var goodsId = $('#goodid').val();
        if(goodsId == '415244730815416283') {
            changeGoodsPrice(result,goodsId);
        }
    }

    function changeGoodsPrice(nums,goodsId) {
        var price = <?php echo ($info["price"]); ?>;
    /*var  goodsId = <?php echo ($info['gsn']); ?>;
   if(goodsId == '415244730815416283') {
       alert(goodsId);
   }*/
        // alert(goodsId);
        /*var nowpoint = <?php echo ($nowpoint); ?>;
        nums =nums +nowpoint;*/
        var discount = <?php echo ($discount); ?>;
        if(discount==0){
            price = '168.00';
        }else {

            $.ajax({
                type: "get",
                data:{'nums':nums,'gsn':'415244730815416283','sta':2},
                url: "<?php echo U('malls/getBuyprice');?>",
                async: true,
                success:function(data){
                    //请求成功函数内容
                    console.log(nums);
                    var str = "<font>参考价:</font>￥" + data*nums + "<font>元</font>";
                    $("#goods-pice").html(str);
                },
            })
        }

    }


    $("#desc_on img").css("width", "100%");


    $(document).on("click touchstart", ".goods-menu_item", function () {
        var type = $(this).attr("id");
        $(".goods-menu div").removeClass("goods-menu_item_on");
        $(".goods-infos_desc").addClass("none");
        $(".goods-infos_para").addClass("none");
        $(".goods-infos_adse").addClass("none");
        //
        $(this).addClass("goods-menu_item_on");
        $("#" + type + "_on").removeClass("none");
    });

    function doBuyer() {
        var value =parseInt( $("#goods_num").val());
        var value1 =parseInt( $("#goods_num1").val());
        var value2 =parseInt( $("#goods_num2").val());
        var value3 =parseInt( $("#goods_num3").val());
        var last_buy = parseInt($("#last_buy").val());
        var num=value1+value2+value3;
        // console.log(num);
        if(num<=0){
            return alert_wec("请选择数量");
        }
        var buy_level = $("#buy_level").val();
        if(value<last_buy){
//            layer.alert(''+buy_level+'补货数量不能低于'+last_buy+'盒');
            alert_wec('购买数量低于'+last_buy+'盒就不能参加黄金雨了哟^-^');
            return false;
        }
        var isstock =$('#isaddstock').val();
        var goodsId = <?php echo ($info['gsn']); ?>;
        var prcie =$('#goods_price').val();

        var links = "<?php echo U('Malls/buyer','sn='.$info['gsn']);?>?nums=" + value ;
        //摇摇杯
        if(goodsId=='915360537990536142'){
            links =links +'&type=2';
        }
        if(isstock){
            var links =links + '&status='+isstock;
        }
        //手提袋
        if(goodsId=='515382251920431381'){
            var links =links + '&price='+prcie;
        }
        //太空育种
        if(goodsId=='815526127943355165'){
            var links ="<?php echo U('Malls/buyer','sn='.$info['gsn']);?>?nums=" + value1+ '&nums2='+value2+ '&nums3='+value3;
        }
        window.location.href = links;
    }

    /** 选择套餐 **/
    $(".numContent li").click(function(){
//        alert(bagPrice);
        var bagPrice ='';
        $(this).addClass('on').siblings().removeClass('on');
        var id=$(this).attr('data-id');
        switch (id){
            case '1':
                bagPrice=58;
                bagnum=20;
                break;
            case '2':
                bagPrice=136;
                bagnum=50;
                break;

        }
        var str = "<font>参考价:</font>￥" + bagPrice + "<font>元</font>" +
            "<input type='hidden' name='nums' value='"+bagnum+"' id='goods_num'/>" +
            "<input type='hidden' name='price' value='"+bagPrice+"' id='goods_price'/>";
        $("#goods-pice").html(str);
    });
</script>

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