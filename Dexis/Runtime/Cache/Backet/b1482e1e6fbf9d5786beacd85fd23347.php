<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
        <link rel="shortcut icon" href="/Public/backet/images/favicon.png" type="image/png">
        <title>环球商城-创想互助生活!</title>
        <link href="/Public/backet/css/style.default.css" rel="stylesheet">
        <!--[if lt IE 9]>
        <script src="/Public/backet/js/html5shiv.js"></script>
        <script src="/Public/backet/js/respond.min.js"></script>
        <![endif]-->
        <script src="/Public/backet/js/jquery-1.11.1.min.js"></script>
        <script src="/Public/libs/common.js"></script>
    </head>

    <body class="signin">

        <section>

            <div class="signinpanel">

                <div class="row">

                    <div class="col-md-7">

                        <div class="signin-info">
                            <div class="logopanel">
                                <h1><span>[</span> 环球商城 <span>]</span></h1>
                            </div><!-- logopanel -->

                            <div class="mb20"></div>

                            <h5><strong>环球商城-欢迎回来环球商城管理中心</strong></h5>
                            <!--ul>
                                <li><i class="fa fa-arrow-circle-o-right mr5"></i> Fully Responsive Layout</li>
                                <li><i class="fa fa-arrow-circle-o-right mr5"></i> HTML5/CSS3 Valid</li>
                                <li><i class="fa fa-arrow-circle-o-right mr5"></i> Retina Ready</li>
                                <li><i class="fa fa-arrow-circle-o-right mr5"></i> WYSIWYG CKEditor</li>
                                <li><i class="fa fa-arrow-circle-o-right mr5"></i> and much more...</li>
                            </ul-->
                            <div class="mb20"></div>
                            <!--strong>Not a member? <a href="signup.html">Sign Up</a></strong-->
                        </div>
                        <!-- signin0-info -->

                    </div><!-- col-sm-7 -->

                    <div class="col-md-5">

                        <form name="login">
                            <h4 class="nomargin">登录</h4>
                            <p class="mt5 mb20">请登录您的账户.</p>

                            <input type="text" name="name" class="form-control uname" placeholder="请输入用户名" />
                            <input type="password" name="passwd" class="form-control pword" placeholder="请输入密码" />
                            <!--a href=""><small>Forgot Your Password?</small></a-->
                            <button type='button' class="btn btn-success btn-block" onclick="doLogon();">登录</button>
                        </form>
                    </div><!-- col-sm-5 -->

                </div><!-- row -->

                <div class="signup-footer">
                    <div class="pull-left">
                        &copy; 2016. All Rights Reserved. <!--www.ulittle.com,成都优诺万象技术支持。-->
                    </div>
                </div>

            </div><!-- signin -->

        </section>


        <script>
            function doLogon() {
                var configURL = "<?php echo U('Index/logon');?>";
                var formelemt = $('form[name=login]').serializeArray();
                var data = objToArray(formelemt);
                ajaxRt(configURL, data);
            }
        </script>


    </body>
</html>