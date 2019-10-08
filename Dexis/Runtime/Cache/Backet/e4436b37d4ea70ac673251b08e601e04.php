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
                        <?php if(session('admin_info')['groups'] != '15' and session('admin_info')['groups'] != '4' and session('admin_info')['groups'] != '3' and session('admin_info')['groups'] != '6' and session('admin_info')['groups'] != '7'): ?><li><a href="<?php echo U('Dash/index');?>"><i class="fa fa-home"></i> <span>概览</span></a></li><?php endif; ?>
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

                        <?php if(session('admin_info')['groups'] == '0'or session('admin_info')['groups'] == '1'): ?><li>
                                <a href="<?php echo U('Plans/index');?>">
                                    <i class="fa fa-play"></i>
                                    <span>0元计划</span>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo U('Users/index');?>">
                                    <i class="fa fa-user"></i>
                                    <span>账户管理</span>
                                </a>
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
                                    <!-- <li><a href="<?php echo U('Users/index');?>"><i class="fa fa-caret-right">用户管理</i></a></li> -->
                                    <li><a href="<?php echo U('Plans/index','status=7');?>"><i class="fa fa-caret-right">已逾期</i></a></li>
                                    <li><a href="<?php echo U('Plansb/index');?>"><i class="fa fa-caret-right">已还款</i></a></li>
                                    <!-- <li><a href="<?php echo U('Plansb/index');?>"><i class="fa fa-caret-right">已认证</i></a></li> -->
                                </ul>
                            </li>
            <!--                 <li>
                <a href="<?php echo U('Users/index');?>">
                    <i class="fa fa-user"></i>
                    <span>账户管理</span>
                </a>
            </li> --><?php endif; ?>

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

                        <?php if(session('admin_info')['groups'] == '1' or session('admin_info')['groups'] == '0' or session('admin_info')['groups'] == '7'): ?><li class="nav-parent">
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
                                    <li><a href="<?php echo U('Goods/order_cup',['gid'=>17]);?>"><i class="fa fa-caret-right"></i>宣传手册发货管理</a></li>
                                    <li><a href="<?php echo U('Goods/order_cup',['gid'=>14]);?>"><i class="fa fa-caret-right"></i>摇摇杯发货管理</a></li>
                                    <li><a href="<?php echo U('Goods/order_cup',['gid'=>15]);?>"><i class="fa fa-caret-right"></i>手提袋发货管理</a></li>
                                    <li><a href="<?php echo U('Goods/order_cup',['gid'=>18]);?>"><i class="fa fa-caret-right"></i>手提袋发货（红色）管理</a></li>
                                    <li><a href="<?php echo U('Goods/order_cup',['gid'=>16]);?>"><i class="fa fa-caret-right"></i>太空育种发货管理</a></li>
                                    <li><a href="<?php echo U('Plans/getHavedPickPlanUser');?>"><i class="fa fa-caret-right"></i>0元计划提货用户</a></li>


                                </ul>
                            </li><?php endif; ?>
                        <!--<?php if(session('admin_info')['groups'] == '3' or session('admin_info')['groups'] == '0'): ?>&lt;!&ndash;<li><a href="<?php echo U('Goods/order_sr');?>"><i class="fa fa-caret-right"></i>发货/提货管理</a></li>&ndash;&gt;
                            <li><a href="<?php echo U('Goods/order_cup',['gid'=>14]);?>"><i class="fa fa-caret-right"></i>摇摇杯发货管理</a></li>
                            <li><a href="<?php echo U('Goods/order_cup',['gid'=>15]);?>"><i class="fa fa-caret-right"></i>手提袋发货管理</a></li><?php endif; ?>-->
                        
                        <?php if(session('admin_info')['groups'] == '15' or session('admin_info')['groups'] == '0'): ?><li><a href="<?php echo U('Goods/transinfo','type='.session('admin_info')['groups']);?>"><i class="fa fa-home"></i> <span>物流</span></a></li><?php endif; ?>

                        <?php if(session('admin_info')['groups'] != '5' and session('admin_info')['groups'] != '4'and session('admin_info')['groups'] != '6' and session('admin_info')['groups'] != '7'): ?><!--<li class="nav-parent">
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

                <div class="pageheader">
    <h2>
        <i class="fa fa-home"></i>
        账户管理
    </h2>
    <div class="breadcrumb-wrapper">
        <span class="label">当前位置:</span>
        <ol class="breadcrumb">
            <li class="active">出货统计</li>
        </ol>
    </div>
</div>

<div class="contentpanel">

        <div class="row">
            <!--黄金雨-->
          <div class="col-sm-6 col-md-3">
              <div class="panel panel-dark panel-stat">
                  <div class="panel-heading">

                      <div class="mb15"></div>
                      <div class="container-fluid" style="color: #FFFFFF">
                          <div class="row">
                              <!--  <div class="col-xs-4">
                                    <img src="/Public/backet/images/is-money.png" alt="">
                                </div>-->
                              <div class="col-xs-12">
                                  <div class="row">
                                      <div class="col-xs-4">
                                          <img src="/Public/backet/images/is-user.png" alt="">
                                      </div>
                                      <div class="col-xs-6">
                                          <small class="stat-label">黄金雨总数</small>
                                          <h1 id=""><?php echo $gold_data['all_gold'];?></h1>
                                      </div>
                                      <!-- <div class="col-xs-6">
                                          <small class="stat-label">今日黄金雨总数</small>
                                          <h1 id=""><?php echo $gold_data['this_gold'];?></h1>
                                      </div> -->
                                  </div>
                              </div>
                          </div><!-- row -->
                          <div class="mb15"></div>

                      </div><!-- stat -->
                  </div><!-- panel-heading -->
              </div>
          </div>
          <!--钻石雨-->
          <div class="col-sm-6 col-md-3">
              <div class="panel panel-dark panel-stat">
                  <div class="panel-heading">

                      <div class="mb15"></div>
                      <div class="container-fluid" style="color: #FFFFFF">
                          <div class="row">
                        
                              <div class="col-xs-12">
                                  <div class="row">
                                                                        <div class="col-xs-4">
                                    <img src="/Public/backet/images/is-user.png" alt="">
                              </div>
                                      <div class="col-xs-6">
                                          <small class="stat-label">钻石雨总数</small>
                                          <h1 id=""><?php echo $gold_data['all_ston'];?></h1>
                                      </div>
                                      <!-- <div class="col-xs-6">
                                          <small class="stat-label">今日钻石雨总数</small>
                                          <h1 id=""><?php echo $gold_data['this_ston'];?></h1>
                                      </div> -->
                                  </div>
                              </div>
                          </div><!-- row -->
                          <div class="mb15"></div>

                      </div><!-- stat -->
                  </div><!-- panel-heading -->
              </div>
          </div>
            <div class="col-sm-6">
                <div class="panel panel-primary" style="color: #fff">
                    <div class="panel-heading">
                        <div class="stat">
                            <div class="row">
                                <div class="col-xs-8">
                                    <small class="stat-label">出货量</small>
                                    <h2>总 计：<br><?php echo ($outnums); ?>盒</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="tinystat pull-right">

                            <!-- <div class="datainfo">
                                <span class="text-muted">2018年出货量</span>

                                <h4>系统：<?php echo ($lastyear); ?>盒</h4>
                                <h4>加上：线下记录 11889 盒</h4>
                                <h4>减去：noe的妈妈 200盒</h4>
                                <h4>合计：<?php echo ($lastyear +11889 -200); ?>盒</h4>
                                <h4>128134盒</h4>
                            </div> -->
                        </div>
                        <div class="tinystat pull-left doexcel">
                            <div class="datainfo">
                                <span class="text-muted">2019年出货量</span>
                                <h4><?php echo ($thisyear); ?>盒</h4>
                            </div>
                        </div><!-- tinystat -->
                    </div>
                </div><!-- panel -->
            </div>

            <div class="col-sm-6">
                <div class="panel panel-dark" style="color: #fff">
                    <div class="panel-heading">
                        <div class="stat">
                            <div class="row">
                                <div class="col-xs-8">
                                    <small class="stat-label">销售额</small>
                                    <h2>总 计：<?php echo ($salemoney); ?>元</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="tinystat pull-left">

                            <div class="datainfo">
                                <span class="text-muted">2019年销售额</span>
                                <h4><?php echo ($thismoney); ?>元</h4>
                            </div>
                        </div><!-- tinystat -->
                        <!-- <div class="tinystat pull-right ">
                            <div class="datainfo">
                                <span class="text-muted">2018年销售额</span>
                                <h4><?php echo ($lastmoney); ?>元</h4>
                            </div>
                        </div> -->
                    </div>
                </div><!-- panel -->

            </div>
            <div class="col-sm-6 col-md-3">
                <div class="panel panel-success panel-stat">
                    <div class="panel-heading">

                        <div class="stat">
                            <div class="row">
                                <div class="col-xs-12">
                                    <small class="stat-label">所有用户总库存量(云库存)</small>
                                    <h1><?php echo ($stocknums); ?>盒</h1>
                                </div>
                            </div><!-- row -->
                        </div><!-- stat -->
                    </div><!-- panel-heading -->
                </div><!-- panel -->
            </div><!-- col-sm-6 -->

           <!-- <div class="col-sm-6 col-md-3">
                <div class="panel panel-primary panel-stat">
                    <div class="panel-heading">

                        <div class="stat">
                            <div class="row">
                                <div class="col-xs-4">
                                    <img src="/Public/backet/images/is-document.png" alt="">
                                </div>
                                <div class="col-xs-8">
                                    <small class="stat-label">用户总提款额</small>
                                    <h1><?php echo ($drowmoney); ?></h1>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>-->
            <!--<div class="col-sm-6 col-md-3">
                    <div class="panel panel-success panel-stat">
                        <div class="panel-heading">

                            <div class="stat">
                                <div class="row">
                                    <div class="col-xs-4">
                                        <img src="/Public/backet/images/is-document.png" alt="">
                                    </div>
                                    <div class="col-xs-8">
                                        <small class="stat-label">返利额</small>
                                        <h1><?php echo ($summary["reback_money"]); ?></h1>
                                    </div>
                                </div>&lt;!&ndash; row &ndash;&gt;
                            </div>&lt;!&ndash; stat &ndash;&gt;
                        </div>&lt;!&ndash; panel-heading &ndash;&gt;
                    </div>&lt;!&ndash; panel &ndash;&gt;
                </div>&lt;!&ndash; col-sm-6 &ndash;&gt;-->

        </div>
        <div class="row">
            <div class="col-sm-6 col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <h5 class="subtitle mb5">各个等级云库存数量</h5>
                                    <div id="userOutStock"  style="width: 100%; height: 350px;"></div>
                                </div><!-- col-sm-8 -->

                            </div><!-- row -->
                        </div><!-- panel-body -->
                    </div><!-- panel -->
                </div><!-- col-sm-9 -->
        </div>
        <div class="row">
            <div class="col-sm-6 col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <h5 class="subtitle mb5">公司各个等级云库存数量</h5>
                                    <div id="companyOutStock"  style="width: 100%; height: 350px;"></div>
                                </div><!-- col-sm-8 -->

                            </div><!-- row -->
                        </div><!-- panel-body -->
                    </div><!-- panel -->
                </div><!-- col-sm-9 -->
        </div>
    <div class="panel panel-default">

        <div class="panel-heading">
            <div class="panel-btns">
                <!--a href="" class="panel-close">&times;</a-->
                <!--a href="" class="minimize">&minus;</a-->
            </div><!-- panel-btns -->
            <!--h3 class="panel-title">
                会员列表
                <a href="<?php echo U('Goods/orderexport','model=account&type='.$type);?>" class="btn btn-primary btn-xs pull-right" target="_blank">导出</a>
            </h3-->
        </div>
        <div class="panel-body">
            <form action="<?php echo U('Users/outGoods');?>" class="form-inline mb15" role="form">
                <!--<div class="form-group">
                    <select name="level"  id="level" class="form-control input-sm">
                        <option value="">会员等级</option>
                        <?php if(is_array($level)): $i = 0; $__LIST__ = $level;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><option value="<?php echo ($v["id"]); ?>" <?php if($v['id']==$setlevel) echo "selected='selected'";?>><?php echo ($v["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                </div>-->
                <!--<div class="form-group">
                    <select name="time"  id="time" class="form-control input-sm">
                        <option value="">选择时间</option>
                        <option value="1day" <?php if($settime=='1day') echo "selected='selected'";?>>一天内</option>
                        <option value="1week" <?php if($settime=='1week') echo "selected='selected'";?>>这周内</option>
                        <option value="1month" <?php if($settime=='1month') echo "selected='selected'";?>>这月内</option>
                        <option value="2month" <?php if($settime=='2month') echo "selected='selected'";?>>上月</option>
                    </select>
                </div>-->

                <div class="form-group">
                    <input type="text" name='start' class="form-control input-sm" id="single_start" value='<?php echo ($start); ?>' placeholder="请选择上架开始时间"/>
                </div>
                <div class="form-group">
                    <input type="text" name='ends' class="form-control input-sm" id="single_end" value='<?php echo ($ends); ?>' placeholder="请选择上架结束时间"/>
                </div>
                <input type="hidden" name="id" id="ids" value="<?php echo ($id); ?>">
                <button type="submit" class="btn btn-default btn-sm">搜索</button>
                <div class="btn btn-default btn-sm" id="doExcel" style="padding-left: 20px;background-color: #2f27b2;color: white;">导出数据</div>
            </form>
            <div class="table-responsive">
                <table class="table table-striped" id="datatables">
                    <thead>
                    <tr>
                        <th width="15%">订单</th>
                        <th width="10%">出货人</th>
                        <th width="10%">进货人</th>
                        <th width="10%">等级</th>
                        <th width="5%">数量</th>
                        <th width="5%">操作</th>
                        <th width="10%">时间</th>
                    </tr>
                    </thead>
                    <tbody>

                    <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><tr>
                            <td><a href="javascript:getinfo('<?php echo ($v["sn"]); ?>');"><?php echo ($v["sn"]); ?></a></td>
                            <td><?php echo ($v["out_name"]); ?></td>
                            <td><?php echo ($v["name"]); ?></td>
                            <td><?php echo (getUserLevel($v["level"])); ?></td>
                            <td ><?php echo ($v["nums"]); ?></td>
                            <td ><?php echo ($v["type"]); ?></td>
                            <td><?php echo ($v["time"]); ?></td>
                        </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                    </tbody>
                </table>
            </div>
            <?php echo ($list["show"]); ?>
        </div>
    </div>
</div>


<script>

    laydate.render({elem: '#single_start'});
    laydate.render({elem: '#single_end'});

    function dels(id) {
        if (!confirm("确定要删除吗？")) {
            return;
        }
        var configURL = "<?php echo U('Core/dels', 'model=account');?>";
        var data = {id: id};
        ajaxRt(configURL, data);
    }

    function onChange(id, st) {
        var configURL = "<?php echo U('Users/accStatus');?>";
        var data = {ids: id, status: st};
        ajaxRt(configURL, data);
    }

    function showList(id){
        var url = "<?php echo U('users/getList');?>?id="+id;
        window.location.href=url;
    }

    function getinfo(sn) {
        var id= $("#ids").val();
        $.ajax({
            url: "<?php echo U('goods/getOrdersInfo');?>",
            type: 'get',
            data: {sn:sn,id:id},
            dataType: "json",
            success: function (res) {

                // hidePreLoading();
                if(res.status) {
                    var html='';
                    /*html += "<div class='modal-dialog modal-lg' role='document'>"+
                        "<div class='modal-content tx-size-sm'>"+
                        "<div class='modal-header pd-x-20' id='popup_header'>"+
                        "<h6 class='tx-14 mg-b-0 tx-uppercase tx-inverse tx-bold'>订单详情</h6>"+
                        "<button type='button' class='close' data-dismiss='modal' aria-label='Close'>"+
                        "<span aria-hidden='true'>&times;</span>"+
                    "</button>"+
                    "</div>"+
                    "<div class='modal-body pd-20' id='popup_body'>"+
                        "<div style='border-bottom:#cb938a 1px solid ;margin-top: 10px'>订单类型：</div>" +
                        "<div style='border-bottom:#cb938a 1px solid ;margin-top: 10px'>11：</div>" +
                        "<div style='border-bottom:#cb938a 1px solid ;margin-top: 10px'>进货人：</div>" +
                        "<div style='border-bottom:#cb938a 1px solid ;margin-top: 10px'>数量：</div>" +
                        "<div style='border-bottom:#cb938a 1px solid ;margin-top: 10px'>金额：</div>"+
                        "</div>"+
                    "<div class='modal-footer'>"+
                    "<button type='button' class='btn btn-primary tx-size-xs' data-dismiss='modal'>确认</button>"+
                        "</div>"+
                        "</div>"+
                        "</div>";*/
                    html +=
                        "<div style='border-bottom:#cb938a 1px solid ;margin-top: 10px'>订单类型：</div>" +
                        "<div style='border-bottom:#cb938a 1px solid ;margin-top: 10px'>11：</div>" +
                        "<div style='border-bottom:#cb938a 1px solid ;margin-top: 10px'>进货人：</div>" +
                        "<div style='border-bottom:#cb938a 1px solid ;margin-top: 10px'>数量：</div>" +
                        "<div style='border-bottom:#cb938a 1px solid ;margin-top: 10px'>金额：</div>";

                    $("#modaldemo3").html(html)
                }
            }
        });
    }
</script>
<!--图表-->
<script src="/Public/backet/js/echarts.common.min.js"></script>
<script>
    window.addEventListener("resize", function () {
        option.chart.resize();

    });
    var userOutStock = echarts.init(document.getElementById('userOutStock'));
    userOutStock.setOption({
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
        grid: {
        left: '1%',
        right: '4%',
        bottom: '3%',
        containLabel: true
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
            name: '数量',
        },
        series: [{
            name: '库存数量',
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
    userOutStock.showLoading();
    var level=[];
    var numStock=[];
    $.ajax({
        type: "POST", //提交方式
        url: "<?php echo U('dash/getUserStock');?>",//路径
        data: {},//数据，这里使用的是Json格式进行传输
        dataType: "json",        //返回数据形式为json
        success: function (result) {
            console.log(result);
            var levelArray =result.level;
            var outNumbersArray=result.data;

            $.each(levelArray,function(name,value) {
                level.push(value.name);    //每日进货数据并填入销量数组
            });
            $.each(outNumbersArray,function(name,value) {
                numStock.push(value.count);    //每日进货数据并填入销量数组
            });
            userOutStock.hideLoading();

            // 各个等级周变动
            userOutStock.setOption({        //加载数据图表
                xAxis: {
                    data: level
                },
                series: [{

                    data: numStock
                }]
            });

        }

    });
</script>
<script>
    var comOutStock = echarts.init(document.getElementById('companyOutStock'));
    comOutStock.setOption({
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
        grid: {
        left: '1%',
        right: '4%',
        bottom: '3%',
        containLabel: true
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
            name: '数量',
        },
        series: [{
            name: '库存数量',
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
    comOutStock.showLoading();
    var level1=[];
    var numStock1=[];
    $.ajax({
        type: "POST", //提交方式
        url: "<?php echo U('dash/getUserStock');?>",//路径
        data: {},//数据，这里使用的是Json格式进行传输
        dataType: "json",        //返回数据形式为json
        success: function (result) {
            console.log(result);
            var levelArray1 =result.level;
            var comNumbersArray=result.company;

            $.each(levelArray1,function(name,value) {
                level1.push(value.name);    //每日进货数据并填入销量数组
            });
            $.each(comNumbersArray,function(name,value) {
                numStock1.push(value.count);    //每日进货数据并填入销量数组
            });
            comOutStock.hideLoading();

            // 各个等级周变动
            comOutStock.setOption({        //加载数据图表
                xAxis: {
                    data: level1
                },
                series: [{

                    data: numStock1
                }]
            });

        }

    });
</script>

<script>
    $('.doexcel').on('click',function(){
        var id = $(this).attr('attr-id');
        var message = $(this).attr("attr-message");
        //var url = SCOPE.set_status_url;
        console.log(message);
        /*  data = {};
          data['id'] = id;
          data['status'] = -1;*/

        layer.open({
            type : 0,
            title : '提示',
            btn: ['导出', '暂不'],
            icon : 3,
            // closeBtn : 2,
            content: "老大同意才可导出表格，是否确定",
            scrollbar: true,
            yes: function(){
                // 执行相关跳转
                doexcel();
            },
        });

    });
    function doexcel(){
        window.location.href="<?php echo U('Backet/Doexc/doexcel_nums');?>";
        layer.msg('导出表格成功');
    }
</script>

<script>
    $("#doExcel").on('click',function(){
        var nickname =$('#nickname').val();
        var up_nickname =$('#up_nickname').val();
        var phone =$('#phone').val();
        var start =$('#single_start').val();
        var ends =$('#single_end').val();
        var level =6;
        window.location.href="<?php echo U('Backet/Doexc/Doexcel_outgoods');?>?nickname="+nickname+"&phone="+phone+"&level="+level+"&up_nickname="+up_nickname+"&start="+start+"&ends="+ends+"&status="+status;
    });
</script>

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