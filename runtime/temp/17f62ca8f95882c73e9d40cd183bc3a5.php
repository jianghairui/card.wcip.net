<?php /*a:1:{s:60:"/mnt/www.caves.vip/application/admin/view/public/noAuth.html";i:1540107967;}*/ ?>

<!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <meta name="renderer" content="webkit|ie-comp|ie-stand">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <link rel="Bookmark" href="/favicon.ico" >
    <link rel="Shortcut Icon" href="/favicon.ico" />
    <!--[if lt IE 9]>
    <script type="text/javascript" src="/lib/html5shiv.js"></script>
    <script type="text/javascript" src="/lib/respond.min.js"></script>
    <![endif]-->
    <link rel="stylesheet" type="text/css" href="/static/h-ui/css/H-ui.min.css" />
    <link rel="stylesheet" type="text/css" href="/static/h-ui.admin/css/H-ui.admin.css" />
    <link rel="stylesheet" type="text/css" href="/lib/Hui-iconfont/1.0.8/iconfont.css" />
    <link rel="stylesheet" type="text/css" href="/static/h-ui.admin/skin/default/skin.css" id="skin" />
    <link rel="stylesheet" type="text/css" href="/static/h-ui.admin/css/style.css" />
    <!--[if IE 6]>
    <script type="text/javascript" src="/lib/DD_belatedPNG_0.0.8a-min.js" ></script>
    <script>DD_belatedPNG.fix('*');</script>
    <![endif]-->

    <!--_footer 作为公共模版分离出去-->
    <script type="text/javascript" src="/lib/jquery/1.9.1/jquery.min.js"></script>
    <script type="text/javascript" src="/lib/layer/2.4/layer.js"></script>
    <script type="text/javascript" src="/static/h-ui/js/H-ui.min.js"></script>
    <script type="text/javascript" src="/static/h-ui.admin/js/H-ui.admin.js"></script>
    <!--/_footer 作为公共模版分离出去-->
    <title></title>
</head>
<body>
<!--请在下方写此页面业务相关的脚本-->
<section class="container-fluid page-404 minWP text-c">
    <p class="error-title">
        <i class="Hui-iconfont va-m" style="font-size:80px;color:#555555">&#xe688;</i>
    </p>
    <p class="error-description" style="color:#555555">没有权限</p>

    <p class="detail"></p>
    <p class="jump" style="color:#555555">
        &nbsp;&nbsp;&nbsp;&nbsp; <b id="wait">3</b> 秒后页面自动关闭
    </p>
</section>
<script type="text/javascript" src="/lib/laypage/1.2/laypage.js"></script>
<script type="text/javascript">
    (function(){
        var wait = document.getElementById('wait')
        var interval = setInterval(function(){
            var time = --wait.innerHTML;
            if(time <= 0) {
                layer_close()
                removeIframe()
                clearInterval(interval);
            };
        }, 1000);
    })();
</script>
</body>
</html>
