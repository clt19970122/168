<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
        <link rel="shortcut icon" href="/Public/backet//Public/backet/images/favicon.png" type="image/png">
        <title>环球商城-创想互助生活!</title>
        <link href="/Public/backet/css/style.default.css" rel="stylesheet">
        <link href="/Public/backet/css/jquery.datatables.css" rel="stylesheet">
        <!--[if lt IE 9]>
        <script src="/Public/backet/js/html5shiv.js"></script>
        <script src="/Public/backet/js/respond.min.js"></script>
        <![endif]-->
        <script src="/Public/backet/js/jquery-1.11.1.min.js"></script>
        <script src="/Public/backet/js/jquery-migrate-1.2.1.min.js"></script>
        <script src="/Public/backet/js/bootstrap.min.js"></script>
        <script src="/Public/backet/js/modernizr.min.js"></script>
        <script src="/Public/backet/js/jquery.sparkline.min.js"></script>
        <script src="/Public/backet/js/toggles.min.js"></script>
        <script src="/Public/backet/js/retina.min.js"></script>
        <script src="/Public/backet/js/jquery.cookies.js"></script>
        <script src="/Public/backet/js/custom.js"></script>
        <script src="/Public/backet/js/jquery.datatables.min.js"></script>
        <script src="/Public/backet/js/morris.min.js"></script>
        <script src="/Public/backet/js/raphael-2.1.0.min.js"></script>
        <script src="/Public/libs/laydate/laydate.js"></script>
        <script src="/Public/libs/common.js"></script>
        <script src="/Public/libs/ajaxfileupload.js"></script>
        <!--弹窗封装-->
        <script src="/Public/libs/dialog/layer.js"></script>
        <script src="/Public/libs/dialog.js"></script>
    </head>

    <body style="height: 100%">

        <!-- Preloader -->
        <div id="preloader">
            <div id="status"><i class="fa fa-spinner fa-spin"></i></div>
        </div>

        <section>
            <div class="leftpanel">
                <div class="logopanel">
                    <h1><span>[</span> World Mall <span>]</span></h1>
                </div><!-- logopanel -->

                <div class="leftpanelinner">

                    <!-- This is only visible to small devices -->
                    <div class="visible-xs hidden-sm hidden-md hidden-lg">   
                        <div class="media userlogged">
                            <img alt="" src="/Public/backet/images/photos/loggeduser.png" class="media-object">
                            <div class="media-body">
                                <h4>管理员</h4>
                            </div>
                        </div>

                        <h5 class="sidebartitle actitle">账户中心</h5>
                        <ul class="nav nav-pills nav-stacked nav-bracket mb30">
                            <li><a href=""><i class="fa fa-cog"></i> <span>账户设置</span></a></li>
                            <li><a href="<?php echo U('Index/out');?>"><i class="fa fa-sign-out"></i> <span>退出登录</span></a></li>
                        </ul>
                    </div>

                    <h5 class="sidebartitle">导航</h5>
                    <ul class="nav nav-pills nav-stacked nav-bracket">
                        <!--都看-->
                        <?php if(session('admin_info')['groups'] != '15' and session('admin_info')['groups'] != '4' and session('admin_info')['groups'] != '3' and session('admin_info')['groups'] != '6'): ?><li><a href="<?php echo U('Dash/index');?>"><i class="fa fa-home"></i> <span>概览</span></a></li><?php endif; ?>
                        <!--商学院-->
                        <?php if(session('admin_info')['groups'] == '6' ): ?><li>
                                <a href="<?php echo U('dash/tableimg');?>">
                                    <i class="fa fa-user"></i>
                                    <span>图表分析图</span>
                                </a>
                            </li><?php endif; ?>
                        <!--销售 ==5-->
                        <?php if(session('admin_info')['groups'] == '5' or session('admin_info')['groups'] == '0'): ?><li>
                                <a href="<?php echo U('Users/userLlCgList');?>">
                                    <i class="fa fa-user"></i>
                                    <span>用户升级统计</span>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo U('dash/tableimg');?>">
                                    <i class="fa fa-user"></i>
                                    <span>图表分析图</span>
                                </a>
                            </li><?php endif; ?>
                            <?php if(session('admin_info')['groups'] == '1' or session('admin_info')['groups'] == '0'): ?><li>
                                <a href="<?php echo U('Users/getUserInAndOut');?>">
                                    <i class="fa fa-user"></i>
                                    <span>用户购买统计</span>
                                </a>
                            </li><?php endif; ?>
                             <?php if(session('admin_info')['groups'] == '5' or session('admin_info')['groups'] == '0'): ?><li>
                                <a href="<?php echo U('Users/outGoods');?>">
                                    <i class="fa fa-user"></i>
                                    <span>进货统计</span>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo U('Users/index');?>">
                                    <i class="fa fa-user"></i>
                                    <span>账户管理</span>
                                </a>
                            </li><?php endif; ?>
                        <?php if(session('admin_info')['groups'] == '0' ): ?><li>
                                <a href="<?php echo U('Stages/index');?>">
                                    <i class="fa fa-user"></i>
                                    <span>分期管理</span>
                                </a>
                            </li>
                        <li class="nav-parent">
                            <a href="javascript:void(0);">
                                <i class="glyphicon glyphicon-indent-left"></i>
                                <span>订单管理</span>
                            </a>
                            <ul class="children">
                                <li><a href="<?php echo U('Goods/order','type=10');?>"><i class="fa fa-caret-right"></i>全部订单</a></li>
                                <li><a href="<?php echo U('Goods/order','type=1');?>"><i class="fa fa-caret-right"></i>待发货订单</a></li>
                                <li><a href="<?php echo U('Goods/order','type=2');?>"><i class="fa fa-caret-right"></i>已发货订单</a></li>
                                <li><a href="<?php echo U('Goods/order','type=3');?>"><i class="fa fa-caret-right"></i>已完成订单</a></li>
                                <!--<li><a href="<?php echo U('Goods/order_sr');?>"><i class="fa fa-caret-right"></i>提货管理</a></li>-->
                            </ul>
                        </li><?php endif; ?>

                        <?php if(session('admin_info')['groups'] == '0'or session('admin_info')['groups'] == '1'or session('admin_info')['groups'] == '4'): ?><!--   <li>
                                <a href="<?php echo U('Plans/index');?>">
                                    <i class="fa fa-play"></i>
                                    <span>0元计划</span>
                                </a>
                            </li>-->
                            <li class="nav-parent">
                                <a href="<?php echo U('Plansb/index');?>">
                                    <i class="fa fa-play"></i>
                                    <span>零元计划</span>
                                </a>
                                <ul class="children">
                                    <li><a href="<?php echo U('Plansb/index','type=10');?>"><i class="fa fa-caret-right">已还款</i></a></li>
                                    <li><a href="<?php echo U('Plans/index','status=7');?>"><i class="fa fa-caret-right">已逾期</i></a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="<?php echo U('Users/index');?>">
                                    <i class="fa fa-user"></i>
                                    <span>账户管理</span>
                                </a>
                            </li><?php endif; ?>

                        <!--  -->
                        <?php if(session('admin_info')['groups'] == '0'): ?><!--  -->
                            <li>
                                <a href="<?php echo U('Apply/index');?>">
                                    <i class="fa fa-play"></i>
                                    <span>赚呗申请列表</span>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo U('Goods/index');?>">
                                    <i class="fa fa-shopping-cart"></i>
                                    <span>商品管理</span>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo U('Goods/catlist');?>">
                                    <i class="fa fa-shopping-cart"></i>
                                    <span>分类管理</span>
                                </a>
                            </li>
                            <li class="nav-parent">
                                <a href="javascript:void(0);">
                                    <i class="fa fa-rmb"></i>
                                    <span>费用设置</span>
                                </a>
                                <ul class="children">
                                    <li><a href="<?php echo U('Membs/level');?>"><i class="fa fa-caret-right"></i>升级返利设置</a></li>
                                    <li><a href="<?php echo U('Membs/draws');?>"><i class="fa fa-caret-right"></i>提现设置</a></li>
                                </ul>
                            </li><?php endif; ?>

                        <?php if(session('admin_info')['groups'] == '4' or session('admin_info')['groups'] == '0'): ?><li class="nav-parent">
                                <a href="javascript:void(0);">
                                    <i class="fa fa-file-word-o"></i>
                                    <span>内容管理</span>
                                </a>
                                <ul class="children">
                                    <li><a href="<?php echo U('Conte/artlist');?>"><i class="fa fa-caret-right"></i>素材中心</a></li>
                                    <li><a href="<?php echo U('Conte/question');?>"><i class="fa fa-caret-right"></i>系统回答</a></li>
                                </ul>
                            </li><?php endif; ?>

                        <?php if(session('admin_info')['groups'] == '1' or session('admin_info')['groups'] == '0'): ?><li class="nav-parent">
                                <a href="javascript:void(0);">
                                    <i class="fa fa-bank"></i>
                                    <span>财务管理</span>
                                </a>
                                <ul class="children">
                                    <li><a href="<?php echo U('Goods/order','type=10');?>"><i class="fa fa-caret-right"></i>全部订单</a></li>
                                    <li><a href="<?php echo U('Fina/index','type=11');?>"><i class="fa fa-caret-right"></i>提款订单</a></li>
                                    <li><a href="<?php echo U('Fina/index','type=0');?>"><i class="fa fa-caret-right"></i>待处理</a></li>
                                    <li><a href="<?php echo U('Fina/index','type=1');?>"><i class="fa fa-caret-right"></i>已处理</a></li>
                                    <li><a href="<?php echo U('Goods/order_srpay','type=1');?>"><i class="fa fa-caret-right"></i>已收物流费明细</a></li>
                                    <li><a href="<?php echo U('Sale/getRebackMoneyList','type=1');?>"><i class="fa fa-caret-right"></i>应返利明细</a></li>
                                    <li><a href="<?php echo U('fina/getDrawCode');?>"><i class="fa fa-caret-right"></i>打款记录</a></li>
                                    <li><a href="<?php echo U('fina/moneyIn');?>"><i class="fa fa-caret-right"></i>入款记录</a></li>
                                    <li><a href="<?php echo U('goods/cart');?>"><i class="fa fa-caret-right"></i>转货记录</a></li>
                                    <li><a href="<?php echo U('users/doturn_list');?>"><i class="fa fa-caret-right"></i>转货申请</a></li>

                                </ul>
                            </li><?php endif; ?>

                        <?php if(session('admin_info')['groups'] == '0'): ?><li class="nav-parent">
                                <a href="javascript:void(0);">
                                    <i class="fa fa-gears"></i>
                                    <span>系统设置</span>
                                </a>
                                <ul class="children">
                                    <li><a href="javascript:sendSms();"><i class="fa fa-caret-right"></i>群发短信</a></li>
                                    <li><a href="javascript:sendtemp();"><i class="fa fa-caret-right"></i>群发微信模板</a></li>
                                    <li><a href="<?php echo U('Dash/basic');?>"><i class="fa fa-caret-right"></i>基本设置</a></li>
                                    <!--<li><a href="<?php echo U('Dash/basic');?>"><i class="fa fa-caret-right"></i>数据库备份</a></li>-->
                                    <li><a href="<?php echo U('Dash/ads');?>"><i class="fa fa-caret-right"></i>广告位</a></li>
                                    <li><a href="<?php echo U('Dash/trans');?>"><i class="fa fa-caret-right"></i>物流管理</a></li>
                                    <li><a href="<?php echo U('Dash/transPay');?>"><i class="fa fa-caret-right"></i>运费管理</a></li>
                                    <li><a href="<?php echo U('users/exchange');?>"><i class="fa fa-caret-right"></i>库存转让</a></li>
                                    <li><a href="<?php echo U('plans/payplan');?>"><i class="fa fa-caret-right"></i>还款处理</a></li>
                                    <li><a href="<?php echo U('team/show_list');?>"><i class="fa fa-caret-right"></i>团队管理奖</a></li>
                                    
                                    <li><a href="<?php echo U('users/parentLevel');?>"><i class="fa fa-caret-right"></i>团队转移</a></li>

                                </ul>
                            </li><?php endif; ?>

                        <?php if(session('admin_info')['groups'] == '3' or session('admin_info')['groups'] == '0' or session('admin_info')['groups'] == '1'): ?><li class="nav-parent">
                                <a href="javascript:void(0);">
                                    <i class="fa fa-gears"></i>
                                    <span>物流信息</span>
                                </a>
                                <ul class="children">
                                    <li><a href="<?php echo U('Goods/order_sr');?>"><i class="fa fa-caret-right"></i>发货/提货管理</a></li>
                                    <li><a href="<?php echo U('Goods/order_sr',array('dotype'=>1));?>"><i class="fa fa-caret-right"></i>快递发货列表（20盒↓）</a></li>
                                    <li><a href="<?php echo U('Goods/order_sr',array('dotype'=>2));?>"><i class="fa fa-caret-right"></i>快运发货列表（20盒↑）</a></li>
                                    <li><a href="<?php echo U('Goods/order_sr',array('dotype'=>3));?>"><i class="fa fa-caret-right"></i>自提列表</a></li>
                                    <li><a href="<?php echo U('Goods/order_cup',['gid'=>14]);?>"><i class="fa fa-caret-right"></i>摇摇杯发货管理</a></li>
                                    <li><a href="<?php echo U('Goods/order_cup',['gid'=>15]);?>"><i class="fa fa-caret-right"></i>手提袋发货管理</a></li>
                                    <li><a href="<?php echo U('Goods/order_cup',['gid'=>16]);?>"><i class="fa fa-caret-right"></i>太空育种发货管理</a></li>
                                    <li><a href="<?php echo U('Plans/getHavedPickPlanUser');?>"><i class="fa fa-caret-right"></i>0元计划提货用户</a></li>


                                </ul>
                            </li><?php endif; ?>
                        <!--<?php if(session('admin_info')['groups'] == '3' or session('admin_info')['groups'] == '0'): ?>&lt;!&ndash;<li><a href="<?php echo U('Goods/order_sr');?>"><i class="fa fa-caret-right"></i>发货/提货管理</a></li>&ndash;&gt;
                            <li><a href="<?php echo U('Goods/order_cup',['gid'=>14]);?>"><i class="fa fa-caret-right"></i>摇摇杯发货管理</a></li>
                            <li><a href="<?php echo U('Goods/order_cup',['gid'=>15]);?>"><i class="fa fa-caret-right"></i>手提袋发货管理</a></li><?php endif; ?>-->
                        
                        <?php if(session('admin_info')['groups'] == '15' or session('admin_info')['groups'] == '0'): ?><li><a href="<?php echo U('Goods/transinfo','type='.session('admin_info')['groups']);?>"><i class="fa fa-home"></i> <span>物流</span></a></li><?php endif; ?>

                        <?php if(session('admin_info')['groups'] != '5' and session('admin_info')['groups'] != '4'and session('admin_info')['groups'] != '6'): ?><!--<li class="nav-parent">
                                <a href="javascript:void(0);">
                                    <i class="fa fa-gears"></i>
                                    <span>商品进销存</span>
                                </a>
                                <ul class="children">
                                    <li><a href="<?php echo U('sale/salelist',array('type'=>2));?>"><i class="fa fa-caret-right"></i>采购模块</a></li>
                                    <li><a href="<?php echo U('sale/salelist',array('type'=>1));?>"><i class="fa fa-caret-right"></i>销售管理</a></li>
                                </ul>
                            </li>-->
                        <li><a href="<?php echo U('sale/salelist');?>"><i class="fa fa-caret-right"></i>商品进销存</a></li><?php endif; ?>
                        <li>
                            <a href="<?php echo U('Users/weight');?>">
                                <i class="fa fa-user"></i>
                                <span>减肥营问卷管理</span>
                            </a>
                        </li>
                    </ul>


                </div>
                <!-- leftpanelinner -->
            </div>
            <!-- leftpanel -->

            <div class="mainpanel">

                <div class="headerbar">
                    <a class="menutoggle"><i class="fa fa-bars"></i></a>
                    <div class="header-right">
                        <ul class="headermenu">
                            <li>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                        <!-- <img src="/Public/backet/images/photos/loggeduser.png" alt="" /> -->
                                        <?php echo session("admin_info")['ch_name'];?>
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-usermenu pull-right">
                                        <li>
                                            <a href="<?php echo U('Dash/sett');?>">
                                                <i class="glyphicon glyphicon-cog"></i> 账户设置
                                            </a>
                                        </li>
                                        <li><a href="<?php echo U('Index/out');?>"><i class="glyphicon glyphicon-log-out"></i> 退出登录</a></li>
                                    </ul>
                                </div>
                            </li>
                            <li>
                                <button id="chatview" class="btn btn-default tp-icon chat-icon">
                                    <i class="glyphicon glyphicon-comment"></i>
                                </button>
                            </li>
                        </ul>
                    </div><!-- header-right -->
                </div><!-- headerbar -->

                <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
</head>
<body>
<div class="contentpanel">
    <?php if(session('admin_info')['groups'] != '1'): ?><div class="row">
            <div class="col-sm-6 col-md-6">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <h5 class="subtitle mb5">各个等级的会员数量</h5>
                                <div id="member-number"  style="width: 100%; height: 350px"></div>
                            </div><!-- col-sm-8 -->

                        </div><!-- row -->
                    </div><!-- panel-body -->
                </div><!-- panel -->
            </div><!-- col-sm-9 -->

            <div class="col-sm-6 col-md-6">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <h5 class="subtitle mb5">各个等级7天变动</h5>
                        <!--<p class="mb15"><br/></p>-->
                        <div id="line-member-change" style="width: 100%;height: 350px"></div>
                    </div><!-- panel-body -->
                </div><!-- panel -->
            </div><!-- col-sm-3 -->

            <div class="col-sm-6 col-md-6">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <h5 class="subtitle mb5">每个级别今日的出货数据</h5>
                        <!--<p class="mb15"><br/></p>-->
                        <div id="goods-number" style="width: 100%;height: 350px"></div>
                    </div><!-- panel-body -->
                </div><!-- panel -->
            </div><!-- col-sm-3 -->
            <div class="col-sm-6 col-md-6">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <h5 class="subtitle mb5">每个级别今日的进货数据</h5>
                        <!--<p class="mb15"><br/></p>-->
                        <div id="goods-number-in" style="width: 100%;height: 350px"></div>
                    </div><!-- panel-body -->
                </div><!-- panel -->
            </div><!-- col-sm-3 -->

            <div class="col-sm-12 col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <h5 class="subtitle mb5">按月会员等级变动</h5>
                        <!--<p class="mb15"><br/></p>-->
                        <div id="memberNumberWeek" style="width: 100%;height: 350px"></div>
                    </div><!-- panel-body -->
                </div><!-- panel -->
            </div><!-- col-sm-3 -->

            <div class="col-sm-12 col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <h5 class="subtitle mb5">本月每天会员等级变动</h5>
                        <!--<p class="mb15"><br/></p>-->
                        <div id="memberNumberDay" style="width: 100%;height: 350px"></div>
                    </div><!-- panel-body -->
                </div><!-- panel -->
            </div><!-- col-sm-3 -->
        </div><?php endif; ?>
</div>
<script src="/Public/backet/js/echarts.common.min.js"></script>

<script type="text/javascript">
    window.addEventListener("resize", function () {
        option.chart.resize();

    });
    var memberNumber = echarts.init(document.getElementById('member-number'));
    var numberChange = echarts.init(document.getElementById('line-member-change'));
    var goodsDayOut = echarts.init(document.getElementById('goods-number'));
    var goodsDayIn = echarts.init(document.getElementById('goods-number-in'));

    var memberNumberWeek = echarts.init(document.getElementById('memberNumberWeek'));
    var memberNumberDay = echarts.init(document.getElementById('memberNumberDay'));

    memberNumber.setOption({
        tooltip: {
            trigger: 'axis',
            axisPointer : {            // 坐标轴指示器，坐标轴触发有效
                type : 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
            }
        },
        legend: {
            show: true,
            data: ['数量']
        },
        xAxis: {
            data: [],
            axisLabel:{
                interval:0,
                rotate:45,
                marginTop:20,
                textStyle:{
                    color:"#222"
                }},
        },
        yAxis: {
            name: '数量'
        },
        series: [{
            name: '会员数量',
            type: 'bar',
            data: [],
            itemStyle: {
                normal: {
                    // 随机显示
                    //color:function(d){return "#"+Math.floor(Math.random()*(256*256*256-1)).toString(16);}
                    label:{
                        show: true,
                        position:'top',
                        textStyle: {
                            color: '#000'
                        }

                    },
                    // 定制显示（按顺序）
                    color: function(params) {
                        var colorList = ['#C33531','#EFE42A','#64BD3D','#EE9201','#29AAE3', '#B74AE5','#0AAF9F' ];
                        return colorList[params.dataIndex]
                    }
                }
            }
        }]
    });
    numberChange.setOption({
        tooltip: {
            trigger: 'axis'
        },
        legend: {
            data:["游客","普通","白银","黄金","铂金","钻石","联创"]
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            containLabel: true
        },
        /*    toolbox: {
                feature: {
                    saveAsImage: {}
                }
            },*/
        xAxis: {
            type: 'category',
            boundaryGap: false,
            data: []
        },
        yAxis: {
            type: 'value'
        },
        series: [
            {
                name:'游客',
                type:'line',
                stack: '总量',
                data:[],
                color: "blue"  //折线颜色
            },
            {
                name:'普通',
                type:'line',
                stack: '总量',
                data:[]
            },
            {
                name:'白银',
                type:'line',
                stack: '总量',
                data:[]
            },
            {
                name:'黄金',
                type:'line',
                stack: '总量',
                data:[]
            },
            {
                name:'铂金',
                type:'line',
                stack: '总量',
                data:[]
            },
            {
                name:'钻石',
                type:'line',
                stack: '总量',
                data:[]
            },
            {
                name:'联创',
                type:'line',
                stack: '总量',
                data:[]
            }
        ]
    });
    goodsDayOut.setOption({
        tooltip: {
            trigger: 'axis',
            axisPointer : {            // 坐标轴指示器，坐标轴触发有效
                type : 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
            }
        },
        legend: {
            show: true,
            data:["游客","普通","白银","黄金","铂金","钻石","联创"]
        },
        xAxis: {
            data: ["游客","普通","白银","黄金","铂金","钻石","联创"]
        },
        yAxis: {
            name: '数量'
        },
        series: [{
            name: '出货数量',
            type: 'bar',
            data: []
        }]
    });
    goodsDayIn.setOption({
        color: ['#3398DB'],
        tooltip: {
            trigger: 'axis',
            axisPointer : {            // 坐标轴指示器，坐标轴触发有效
                type : 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
            }
        },
        legend: {
            show: true,
            data:["游客","普通","白银","黄金","铂金","钻石","联创"]
        },
        xAxis: {
            data: ["游客","普通","白银","黄金","铂金","钻石","联创"]
        },
        yAxis: {
            name: '数量'
        },
        series: [{
            name: '出货数量',
            type: 'bar',
            data: []
        }]
    });
    // 周
    memberNumberWeek.setOption({
        tooltip: {
            trigger: 'axis'
        },
        legend: {
            data:["游客","普通","白银","黄金","铂金","钻石","联创"]
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            containLabel: true
        },
        /*    toolbox: {
                feature: {
                    saveAsImage: {}
                }
            },*/
        xAxis: {
            type: 'category',
            boundaryGap: false,
            data: []
        },
        yAxis: {
            type: 'value'
        },
        series: [
            {
                name:'游客',
                type:'line',
                stack: '总量',
                data:[],
                color: "blue"  //折线颜色
            },
            {
                name:'普通',
                type:'line',
                stack: '总量',
                data:[]
            },
            {
                name:'白银',
                type:'line',
                stack: '总量',
                data:[]
            },
            {
                name:'黄金',
                type:'line',
                stack: '总量',
                data:[]
            },
            {
                name:'铂金',
                type:'line',
                stack: '总量',
                data:[]
            },
            {
                name:'钻石',
                type:'line',
                stack: '总量',
                data:[]
            },
            {
                name:'联创',
                type:'line',
                stack: '总量',
                data:[]
            }
        ]
    });
    // 天
    memberNumberDay.setOption({
        tooltip: {
            trigger: 'axis'
        },
        legend: {
            data:["游客","普通","白银","黄金","铂金","钻石","联创"]
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            containLabel: true
        },
        /*    toolbox: {
                feature: {
                    saveAsImage: {}
                }
            },*/
        xAxis: {
            type: 'category',
            boundaryGap: false,
            data: []
        },
        yAxis: {
            type: 'value'
        },
        series: [
            {
                name:'游客',
                type:'line',
                stack: '总量',
                data:[],
                color: "blue"  //折线颜色
            },
            {
                name:'普通',
                type:'line',
                stack: '总量',
                data:[]
            },
            {
                name:'白银',
                type:'line',
                stack: '总量',
                data:[]
            },
            {
                name:'黄金',
                type:'line',
                stack: '总量',
                data:[]
            },
            {
                name:'铂金',
                type:'line',
                stack: '总量',
                data:[]
            },
            {
                name:'钻石',
                type:'line',
                stack: '总量',
                data:[]
            },
            {
                name:'联创',
                type:'line',
                stack: '总量',
                data:[]
            }
        ]
    });
    numberChange.showLoading();
    memberNumber.showLoading();
    goodsDayOut.showLoading();
    goodsDayIn.showLoading();
    memberNumberWeek.showLoading();
    memberNumberDay.showLoading();
    var names=[];    //类别数组（实际用来盛放X轴坐标值）
    var nums=[];    //销量数组（实际用来盛放Y坐标值）
    var time=[];   //获取时间
    var numbChange=[];
    var goodsnumber=[];
    var goodsnumberIn=[];

    var week=[];
    var weekNumbers=[];
    var day=[];
    var monthDay=[];
    $.ajax({
        type: "POST", //提交方式
        url: "<?php echo U('dash/getusercount');?>",//路径
        data: {},//数据，这里使用的是Json格式进行传输
        dataType: "json",        //返回数据形式为json
        success: function (result) {
            var nameArray = result.level;
            var numsArray = result.level_count;
            var numberChangeDate = result.time;
            var numberChangeArray = result.time_data;
            var goodsNumberArray = result.daliy_out;
            var goodsNumberInArray = result.daliy_in;

            /*    console.log(weekArray);
                console.log(weekNumbersArray);*/
            for (var i = 0; i < nameArray.length; i++) {
                names.push(nameArray[i]);    //挨个取出销量并填入销量数组
            }

            $.each(numsArray, function (name, value) {
                nums.push(value.counts);    //挨个取出销量并填入销量数组
            });

            $.each(numberChangeDate, function (name, value) {
                time.push(value);    //时间
            });

            $.each(numberChangeArray, function (name, value) {
                numbChange.push(value);
            });
            $.each(goodsNumberArray, function (name, value) {
                goodsnumber.push(value);    //每日出货数据
            });
            $.each(goodsNumberInArray, function (name, value) {
                goodsnumberIn.push(value);    //每日进货数据并填入销量数组
            });


            numberChange.hideLoading();
            memberNumber.hideLoading();
            goodsDayOut.hideLoading();
            goodsDayIn.hideLoading();

            memberNumber.setOption({        //加载数据图表
                xAxis: {
                    data: names
                },
                series: [{
                    data: nums
                }]
            });

            // 各个等级7天变动
            numberChange.setOption({
                xAxis: {
                    type: 'category',
                    boundaryGap: false,
                    data: time
                },
                series: [
                    {
                        name: '游客',
                        data: numbChange[0],
                        color: "blue"  //折线颜色
                    },
                    {
                        name: '普通',
                        data: numbChange[1]
                    },
                    {
                        name: '白银',
                        data: numbChange[2]
                    },
                    {
                        name: '黄金',
                        data: numbChange[3]
                    },
                    {
                        name: '铂金',
                        data: numbChange[4]
                    },
                    {
                        name: '钻石',
                        data: numbChange[5]
                    },
                    {
                        name: '联创',
                        data: numbChange[6]
                    }
                ]
            });
            // 每个级别每日的出货数据
            goodsDayOut.setOption({        //加载数据图表
                series: [{
                    name: '出货数量',
                    type: 'bar',
                    data: goodsnumber,
                }]
            });
            // 每个级别每日的出货数据
            goodsDayIn.setOption({        //加载数据图表
                series: [{
                    name: '进货数量',
                    type: 'bar',
                    data: goodsnumberIn,
                }]
            });

        }
    });


    $.ajax({
        type: "POST", //提交方式
        url: "<?php echo U('dash/getLevelChange');?>",//路径
        data: {},//数据，这里使用的是Json格式进行传输
        dataType: "json",        //返回数据形式为json
        success: function (result) {
            var weekArray =result.weeks;
            var weekNumbersArray=result.week_data;

            $.each(weekArray,function(name,value) {
                week.push(value);    //每日进货数据并填入销量数组
            });
            $.each(weekNumbersArray,function(name,value) {
                weekNumbers.push(value);    //每日进货数据并填入销量数组
            });
            memberNumberWeek.hideLoading();

            // 各个等级周变动
            memberNumberWeek.setOption({
                xAxis: {
                    type: 'category',
                    boundaryGap: false,
                    data: week
                },
                series: [
                    {
                        name:'游客',
                        data:weekNumbers[0],
                        color: "blue"  //折线颜色
                    },
                    {
                        name:'普通',
                        data:weekNumbers[1]
                    },
                    {
                        name:'白银',
                        data:weekNumbers[2]
                    },
                    {
                        name:'黄金',
                        data:weekNumbers[3]
                    },
                    {
                        name:'铂金',
                        data:weekNumbers[4]
                    },
                    {
                        name:'钻石',
                        data:weekNumbers[5]
                    },
                    {
                        name:'联创',
                        data:weekNumbers[6]
                    }
                ]
            });

        }

    });


    $.ajax({
        type: "POST", //提交方式
        url: "<?php echo U('dash/getThisMonDay');?>",//路径
        data: {},//数据，这里使用的是Json格式进行传输
        dataType: "json",        //返回数据形式为json
        success: function (result) {

            var dayArray=result.month_day;
            var monthDayArray = result.mon_data;

            memberNumberDay.hideLoading();
            // 天
            $.each(dayArray,function(name,value) {
                day.push(value);    //每日进货数据并填入销量数组
            });
            $.each(monthDayArray,function(name,value) {
                monthDay.push(value);    //每日进货数据并填入销量数组
            });

            // 各个等级天变动
            memberNumberDay.setOption({
                xAxis: {
                    axisLabel:{
                        interval:0,
                        rotate:45,
                        marginTop:20,
                        textStyle:{
                            color:"#222"
                        }},
                    type: 'category',
                    boundaryGap: false,
                    data: day
                },
                series: [
                    {
                        name:'游客',
                        data:monthDay[0],
                        color: "blue"  //折线颜色
                    },
                    {
                        name:'普通',
                        data:monthDay[1]
                    },
                    {
                        name:'白银',
                        data:monthDay[2]
                    },
                    {
                        name:'黄金',
                        data:monthDay[3]
                    },
                    {
                        name:'铂金',
                        data:monthDay[4]
                    },
                    {
                        name:'钻石',
                        data:monthDay[5]
                    },
                    {
                        name:'联创',
                        data:monthDay[6]
                    }
                ]
            });

        }

    });

</script>

<script>
    function getmoney(aaa) {
        // alert(aaa);
        $.ajax({
            type : "POST", //提交方式
            url : "<?php echo U('dash/getAboutMoney');?>",//路径
            data : {
                "type" :aaa
            },//数据，这里使用的是Json格式进行传输
            success : function(result) {
                var html ='<input type="hidden" id="gettype" value="'+aaa+'">'
                $('#hiddenval') .html(html);
                $('#online_money').text(result.online_money);
                $('#all-money').text(parseFloat(result.online_money)+parseFloat(result.offine_money)+parseFloat(result.jr_money));
                $('#offine_money').text(result.offine_money);
                $('#jr_money').text(result.jr_money);
            }
        });
    }
    function getsale(aaa) {
        // alert(aaa);
        $.ajax({
            type : "POST", //提交方式
            url : "<?php echo U('dash/getOutGood');?>",//路径
            data : {
                "type" :aaa
            },//数据，这里使用的是Json格式进行传输
            success : function(result) {
                $('#sale').text(result.outnum);
                $('#salemoney').text(result.paymoney);

            }
        });
    }
</script>

</body>
</html>

            </div><!-- mainpanel -->
            <script>
                function sendSms() {
                    $.ajax({
                        type: "post", //提交方式
                        url: "<?php echo U('Dash/sendSms');?>",//路径
                        data: {
                            "id": "${org.id}"
                        },
                        success: function (result) {
                                console.log(result.id)
                            if(result.status==1){
                                sendSms();
                            }else {
                                alert('发送完成');
                            }
                        }
                    });
                }
            </script>
            <script>
                function sendtemp(length) {
                    $.ajax({
                        type: "post", //提交方式
                        url: "<?php echo U('Dash/sendTempToAll');?>",//路径
                        data: {
                            "length":length
                        },
                        success: function (result) {
                                console.log(result.id)
                            if(result.status===1){
                                sendtemp(result.length);
                            }else {
                                alert('发送完成');
                            }
                        }
                    });
                }
            </script>
        </section>
    </body>
</html>