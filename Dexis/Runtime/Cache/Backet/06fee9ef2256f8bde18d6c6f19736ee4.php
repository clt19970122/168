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
        转货管理
        <!--span>Subtitle goes here...</span-->
    </h2>
    <div class="breadcrumb-wrapper">
        <span class="label">当前位置:</span>
        <ol class="breadcrumb">
            <li class="active">转货列表</li>
        </ol>
    </div>
</div>

<div class="contentpanel">

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">
                转货列表
                <!--a href="<?php echo U('Goods/orderexport','model=orders&type='.$type);?>" class="btn btn-primary btn-xs pull-right" target="_blank">导出</a-->
            </h3>
        </div>
        <div class="panel-body">

            <form action="<?php echo U('users/doturn_list','&p='.$page);?>" class="form-inline mb15" role="form">
                <div class="form-group">
                    <input type="text" name='sn' class="form-control input-sm" id="sn" value="<?php echo ($sn); ?>" placeholder="请输入订单编号"/>
                </div>
                <div class="form-group">
                    <input type="text" name='pname' class="form-control input-sm" id="pname" value="<?php echo ($pname); ?>" placeholder="请输入转货人姓名"/>
                </div>

                <div class="form-group">
                    <select name="status" id="sssss" class="form-control input-sm">
                        <option value="">全部</option>
                        <option value="2">待审核</option>
                        <option value="3">未通过</option>
                        <option value="1">通过</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-default btn-sm">搜索</button>

                <!--<div class="btn btn-default btn-sm" id="doExcel" style="padding-left: 20px;background-color: #2f27b2;color: white;">导出数据</div>-->
            </form>

            <div class="table-responsive">
                <table class="table table-bordered" id="datatables">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th width="10%">订单编号</th>
                            <th width="10%">转出用户</th>
                            <th width="10%">转出用户逾期数量</th>
                            <th width="10%">下单用户</th>
                            <th width="10%">转入数量</th>
                            <th width="10%">状态</th>
                            <th width="10%">时间</th>
                            <th width="">操作</th>
                        </tr>
                    </thead>
                    <tbody>

                    <?php if(is_array($list["list"])): $i = 0; $__LIST__ = $list["list"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><tr>
                            <td><?php echo ($v["id"]); ?></td>
                            <td><?php echo ($v["sn"]); ?></td>
                            <td><?php echo (getNameById($v['uid'])); ?></td>
                            <td><?php echo ($v["out_nums"]); ?></td>
                            <td><?php echo (getNameById($v["aboutid"])); ?></td>
                            <td><?php echo ($v["nums"]); ?></td>
                            <td> <?php echo ($v['status']==1?'已转入':''); ?>
                                 <?php echo ($v['status']==3?'转货驳回':''); ?>
                                 <?php echo ($v['status']==2?'待审核':''); ?>
                            </td>
                            <td><?php echo (date('Y/m/d H:i:s',$v["time"])); ?></td>
                            <td>
                                <?php if($v["status"] == 2): ?><a href="javascript:dels(<?php echo ($v["id"]); ?>);" class='btn btn-xs btn-success'>审核通过</a><?php endif; ?>
                                <?php if($v["status"] == 2): ?><a href="javascript:chargeback(<?php echo ($v["id"]); ?>);" class='btn btn-xs  btn-danger'>转货驳回</a><?php endif; ?>
                            </td>
                        </tr><?php endforeach; endif; else: echo "" ;endif; ?>

                    </tbody>
                </table>

                <nav class="pagec pull-left">
                    <?php echo ($list["show"]); ?>
                </nav>
                <div class="pull-right">
                    <div class="col-sm-1 input-group input-group-sm">
                        <input type="text" id="pages" class="form-control">
                        <span class="input-group-addon" onclick="pagereload();">跳转</span>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

<script>

    laydate.render({elem: '#single_start'});
    laydate.render({elem: '#single_end'});

    function pagereload() {
        var page = $("#pages").val();
        if ($.trim(page) === null || page <= 0) {
            alert("请输入正确的页面编号");
            return false;
        }
        var url = "<?php echo U('Goods/order','type='.$type.'&name='.$name.'&phone='.$phone);?>?p=" + page;
        window.location.href = url;
    }

    function dels(id) {
        layer.prompt({title: '确认请输入密码', formType: 1}, function(pass, index){
            var data = {pwd: pass};
            $.ajax({
                url: "<?php echo U('Users/exportPermission');?>", type: 'post',
                data: data, dataType: "json",
                success: function (res) {
                    if (res.status !== 1) {
                        return layer.msg(res.msg);
                    }else {
                        var configURL = "<?php echo U('users/doturn');?>";
                        var type=1;
                        var data = {id: id,type:type};
                        ajaxRt(configURL, data);
                    }
                }
            });
            layer.close(index);
        });
    }

    function chargeback(id){
        layer.prompt({title: '确认请输入密码', formType: 1}, function(pass, index){
            var data = {pwd: pass};
            $.ajax({
                url: "<?php echo U('Users/exportPermission');?>", type: 'post',
                data: data, dataType: "json",
                success: function (res) {
                    if (res.status !== 1) {
                        return layer.msg(res.msg);
                    }else {
                        var configURL = "<?php echo U('users/doturn');?>";
                        var type=2;
                        var data = {id: id,type:type};
                        ajaxRt(configURL, data);
                    }
                }
            });
            layer.close(index);
        });
    }
</script>
<script>
    function checkPassword(obj,id){
        layer.prompt({title: '查看信息请输入密码', formType: 1}, function(pass, index){
            var data = {pwd: pass,uid:id};
            $.ajax({
                url: "<?php echo U('Users/examine');?>", type: 'post',
                data: data, dataType: "json",
                success: function (res) {
                    if (res.status !== 1) {
                        return layer.msg(res.msg);
                    }else {
                        $(obj).html(res.msg);
                    }
                }
            });
            layer.close(index);
        });
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