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
    <h2><i class="fa fa-home"></i> 系统概览</h2>
</div>

<div class="contentpanel">
    <?php if(session('admin_info')['groups'] != '5'): ?><div class="row">
            <div class="col-sm-6 col-md-3">
                <div class="panel panel-dark panel-stat">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="btn-group pull-right">
                                    <button type="button" class="btn btn-info btn-sm" onclick="getmoney(1);">今日</button>
                                    <button type="button" class="btn btn-info btn-sm" onclick="getmoney(2)">昨日</button>
                                    <button type="button" class="btn btn-info btn-sm" onclick="getmoney(3)">本周</button>
                                    <button type="button" class="btn btn-info btn-sm" onclick="getmoney(4)">上周</button>
                                    <button type="button" class="btn btn-info btn-sm" onclick="getmoney(5)">本月</button>
                                    <button type="button" class="btn btn-info btn-sm" onclick="getmoney(6)">上月</button>
                                </div>
                            </div>
                        </div>
                        <div class="mb15"></div>
                        <div class="container-fluid" style="color: #FFFFFF">
                            <div class="row">
                                <div class="col-xs-4">
                                    <img src="/Public/backet/images/is-money.png" alt="">
                                </div>
                                <div class="col-xs-8">
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <small class="stat-label">现金流总计</small>
                                            <h1 id="all-money"><?php echo $money['online_money']+$money['offine_money']+$money['jr_money'];?></h1>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- row -->
                            <div class="mb15"></div>
                            <div id="hiddenval"></div>
                            <div class="row">
                                    <div class="col-xs-4 check-excel" attr-id="1" attr-message="线上商城">
                                        <small class="stat-label">线上商城</small>
                                        <h4 id="online_money">¥<?php echo $money['online_money'];?></h4>
                                    </div>
                                    <div class="col-xs-4 check-excel" attr-id="2" attr-message="线下现金">
                                        <small class="stat-label">线下现金</small>
                                        <h4 id="offine_money">¥<?php echo $money['offine_money'];?></h4>
                                    </div>
                                    <div class="col-xs-4 check-excel" attr-id="3" attr-message="金融收款">
                                        <small class="stat-label">金融收款</small>
                                        <h4 id="jr_money">¥<?php echo $money['jr_money'];?></h4>
                                    </div>
                            </div>
                        </div><!-- stat -->
                    </div><!-- panel-heading -->
                </div><!-- panel -->
            </div><!-- col-sm-6 -->
          <div class="col-sm-6 col-md-3">
              <div class="panel panel-dark panel-stat">
                  <div class="panel-heading">
                      <div class="row">
                          <div class="col-xs-12">
                              <div class="btn-group pull-right">
                                  <button type="button" class="btn btn-info btn-sm" onclick="getsale(1);">今日</button>
                                  <button type="button" class="btn btn-info btn-sm" onclick="getsale(2)">昨日</button>
                                  <button type="button" class="btn btn-info btn-sm" onclick="getsale(3)">本周</button>
                                  <button type="button" class="btn btn-info btn-sm" onclick="getsale(4)">上周</button>
                                  <button type="button" class="btn btn-info btn-sm" onclick="getsale(5)">本月</button>
                                  <button type="button" class="btn btn-info btn-sm" onclick="getsale(6)">上月</button>
                              </div>
                          </div>
                      </div>
                      <div class="mb15"></div>
                      <div class="container-fluid" style="color: #FFFFFF">
                          <div class="row">
                              <!--  <div class="col-xs-4">
                                    <img src="/Public/backet/images/is-money.png" alt="">
                                </div>-->
                              <div class="col-xs-12">
                                  <div class="row">
                                      <div class="col-xs-6">
                                          <small class="stat-label">联创销售量</small>
                                          <h1 id="sale"><?php echo $num['outnum'];?></h1>
                                      </div>
                                      <div class="col-xs-6">
                                          <small class="stat-label">联创销售额(55元结算)</small>
                                          <h1 id="salemoney">¥<?php echo $num['paymoney'];?></h1>
                                      </div>
                                  </div>
                              </div>
                          </div><!-- row -->
                          <div class="mb15"></div>

                      </div><!-- stat -->
                  </div><!-- panel-heading -->
              </div>
          </div>
          <!--黄金雨-->
           <div class="col-sm-6 col-md-3">
              <div class="panel panel-dark panel-stat">
                  <div class="panel-heading">

                      <div class="mb15"></div>
                      <div class="container-fluid" style="color: #FFFFFF">
                          <div class="row">

                              <div class="col-xs-12">
                                  <div class="row">
                                      <div class="col-xs-6">
                                          <small class="stat-label">分期钻石申请数</small>
                                          <a href="<?php echo U('Stages/index');?>">
                                            <h1 id=""><?php  echo $stages['demon'];?></h1>
                                          </a>
                                      </div>
                                      <div class="col-xs-6">
                                          <small class="stat-label">分期联创申请数</small>
                                          <a href="<?php echo U('Stages/index');?>">
                                          <h1 id=""><?php echo $stages['founder'];?></h1>
                                          </a>
                                      </div>
                                  </div>
                              </div>
                          </div>
                          <div class="mb15"></div>
                      </div>
                  </div>
              </div>
          </div>
          <!--钻石雨-->
           <div class="col-sm-6 col-md-3">
              <div class="panel panel-dark panel-stat">
                  <div class="panel-heading">

                      <div class="mb15"></div>
                      <div class="container-fluid" style="color: #FFFFFF">
                          <div class="row">
                              <a href="<?php echo U('goods/order_sr');?>">
                              <div class="col-xs-12">
                                  <div class="row">
                                      <div class="col-xs-6">
                                          <small class="stat-label">待发货量</small>
                                          <h1 id=""><?php echo $pick_nums['need_out'];?></h1>
                                      </div>
                                      <div class="col-xs-6">
                                          <small class="stat-label">今日发货量</small>
                                          <h1 id=""><?php echo $pick_nums['this_out'];?></h1>
                                      </div>
                                  </div>
                              </div>
                              </a>
                          </div>
                          <div class="mb15"></div>

                      </div>
                  </div>
              </div>
          </div>
      </div>
        <!--box2--><?php endif; ?>
    <div class="row">
        <?php if(session('admin_info')['groups'] != '1'): ?><div class="col-sm-6 col-md-3">
            <div class="panel panel-success panel-stat">
                <div class="panel-heading">
                    <div class="stat">
                        <div class="row">
                            <div class="col-xs-4">
                                <img src="/Public/backet/images/is-user.png" alt="">
                            </div>
                            <div class="col-xs-8">
                                <small class="stat-label">用户总数</small>
                                <h1><?php echo ($user); ?></h1>
                            </div>
                        </div><!-- row -->
                    </div><!-- stat -->

                </div><!-- panel-heading -->
            </div><!-- panel -->
        </div><!-- col-sm-6 -->

        <div class="col-sm-6 col-md-3">
            <div class="panel panel-danger panel-stat">
                <div class="panel-heading">

                    <div class="stat">
                        <div class="row">
                            <div class="col-xs-4">
                                <img src="/Public/backet/images/is-document.png" alt="">
                            </div>
                            <div class="col-xs-8">
                                <small class="stat-label">订单总数</small>
                                <h1><?php echo ($ords); ?></h1>
                            </div>
                        </div><!-- row -->
                    </div><!-- stat -->
                </div><!-- panel-heading -->
            </div><!-- panel -->
        </div><!-- col-sm-6 --><?php endif; ?>
        <?php if(session('admin_info')['groups'] != '5'): ?><div class="col-sm-6 col-md-3">
            <div class="panel panel-dark panel-stat">
                <div class="panel-heading">

                    <div class="stat">
                        <div class="row">
                            <div class="col-xs-4">
                                <img src="/Public/backet/images/is-money.png" alt="">
                            </div>
                            <div class="col-xs-8">
                                <small class="stat-label">今日联创佣金</small>
                                <h1 >¥<?php echo $money['top_reback_money'];?></h1>
                            </div>
                        </div><!-- row -->
                    </div><!-- stat -->

                </div><!-- panel-heading -->
            </div><!-- panel -->
        </div><!-- col-sm-6 -->
        <div class="col-sm-6 col-md-3">
            <div class="panel panel-dark panel-stat">
                <div class="panel-heading">

                    <div class="stat">
                        <div class="row">
                            <div class="col-xs-4">
                                <img src="/Public/backet/images/is-money.png" alt="">
                            </div>
                            <div class="col-xs-8 do_excel" attr-id="1" attr-message="今日非联佣金">
                                <small class="stat-label">今日非联创佣金</small>
                                <h1 >¥<?php echo $money['other_money'];?></h1>
                            </div>
                        </div><!-- row -->
                    </div><!-- stat -->

                </div><!-- panel-heading -->
            </div><!-- panel -->
        </div><!-- col-sm-6 -->
        <div class="col-sm-6 col-md-3">
                <div class="panel panel-dark panel-stat">
                    <div class="panel-heading">
                        <div class="stat">
                            <div class="row">
                                <div class="col-xs-4">
                                    <img src="/Public/backet/images/is-money.png" alt="">
                                </div>
                                <div class="col-xs-8 " attr-id="1" attr-message="今日联创返利金额">
                                    <small class="stat-label">联创用户总余额</small>
                                    <h1 >¥<?php echo $all_top_money ;?></h1>
                                </div>
                            </div><!-- row -->
                        </div><!-- stat -->

                    </div><!-- panel-heading -->
                </div><!-- panel -->
            </div><!-- col-sm-6 --><div class="col-sm-6 col-md-3">
                <div class="panel panel-dark panel-stat">
                    <div class="panel-heading">
                        <div class="stat">
                            <div class="row">
                                <div class="col-xs-4">
                                    <img src="/Public/backet/images/is-money.png" alt="">
                                </div>
                                <div class="col-xs-8 " attr-id="1" attr-message="今日联创返利金额">
                                    <small class="stat-label">非联创用户总余额</small>
                                    <h1 >¥<?php echo $rest_money;?></h1>
                                </div>
                            </div><!-- row -->
                        </div><!-- stat -->

                    </div><!-- panel-heading -->
                </div><!-- panel -->
            </div><!-- col-sm-6 --><?php endif; ?>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">权限列表</h3>
        </div>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="datatables">
                    <thead>
                    <tr>
                        <th width="10%">姓名</th>
                        <th width="10%">账号</th>
                        <th width="80%">权限</th>
                    </tr>
                    </thead>
                    <tbody>

                    <?php if(is_array($sysmang)): $i = 0; $__LIST__ = $sysmang;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><tr>
                            <td><?php echo ($v["ch_name"]); ?></td>
                            <td><?php echo ($v["name"]); ?></td>
                            <td><?php echo ($v["description"]); ?></td>
                        </tr><?php endforeach; endif; else: echo "" ;endif; ?>

                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
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

<script>
$('.check-excel').on('click',function(){
        var id = $(this).attr('attr-id');
       var message = $(this).attr("attr-message");
       //var url = SCOPE.set_status_url;
        console.log(message);
     /*  data = {};
       data['id'] = id;
       data['status'] = -1;*/

    /*layer.open({
        type : 0,
        title : '提示',
        btn: ['导出', '暂不'],
        icon : 3,
        // closeBtn : 2,
        content: "老大同意才可导出"+message+"表格，是否确定",
        scrollbar: true,
        yes: function(){
            // 执行相关跳转
            doExcel(id);
        },
    });*/
    layer.prompt({title: '导出信息请输入密码', formType: 1}, function(pass, index){
        var data = {pwd: pass};
        $.ajax({
            url: "<?php echo U('Users/doExcel');?>", type: 'post',
            data: data, dataType: "json",
            success: function (res) {
                if (res.status !== 1) {
                    return layer.msg(res.msg);
                }else {
                    doExcel(id);
                }
            }
        });
       // var data = {pwd: pass,id:id};
        layer.close(index);
    });

});

    function doExcel(getin){
        var type =$('#gettype').val();
        if(getin){
            window.location.href="<?php echo U('Backet/Doexc/doExcel_money');?>?in="+getin+"&type="+type;
            layer.msg('导出表格成功');
        }else {
            layer.msg('参数不存在');
        }
    };


    $('.do_excel').on('click',function(){
    var id = $(this).attr('attr-id');
    var message = $(this).attr("attr-message");
    //var url = SCOPE.set_status_url;
    console.log(message);
    /*  data = {};
      data['id'] = id;
      data['status'] = -1;*/

    /*layer.open({
        type : 0,
        title : '提示',
        btn: ['导出', '暂不'],
        icon : 3,
        // closeBtn : 2,
        content: "老大同意才可导出"+message+"表格，是否确定",
        scrollbar: true,
        yes: function(){
            // 执行相关跳转
            doexcel2(id);
        },
    });*/
    layer.prompt({title: '导出信息请输入密码', formType: 1}, function(pass, index){
            var data = {pwd: pass};
            $.ajax({
                url: "<?php echo U('Users/doExcel');?>", type: 'post',
                data: data, dataType: "json",
                success: function (res) {
                    if (res.status !== 1) {
                        return layer.msg(res.msg);
                    }else {
                        doexcel2(id);
                    }
                }
            });
            layer.close(index);
        });
});
function doexcel2(t){
    window.location.href="<?php echo U('Backet/Doexc/Doexcel_money_two');?>?type="+t;
    layer.msg('导出表格成功');
}
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