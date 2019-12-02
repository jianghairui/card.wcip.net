<?php /*a:3:{s:68:"/var/www/caves.wcip.net/application/user/view/banner/video_list.html";i:1568093976;s:57:"/var/www/caves.wcip.net/application/user/view/layout.html";i:1566381559;s:64:"/var/www/caves.wcip.net/application/user/view/public/footer.html";i:1566381559;}*/ ?>
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
<style>
    .thumbnail{ width:192px;height: 108px;background-size: cover;background-position: center;position: relative}
</style>
<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 轮播图 <span class="c-gray en">&gt;</span> 图片列表 <a class="btn btn-success radius r" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a></nav>
<div class="page-container">
    <div class="mt-20">
        <table class="table table-border table-bordered table-bg table-sort">
            <thead>
            <tr class="text-c">
                <th width="70">ID</th>
                <th width="200">图片</th>
                <th width="200">具体描述</th>
                <th>链接地址</th>
                <th width="100">操作</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($list as $li): ?>
            <tr class="text-c">
                <td><?php echo htmlentities($li['id']); ?></td>
                <td>
                    <div id="cover" class="thumbnail" style="background-image: url('<?php echo htmlentities($qiniu_weburl); ?><?php echo htmlentities($li['poster']); ?>')"></div>
                </td>
                <td class="text-c"><?php echo htmlentities($li['title']); ?></td>
                <td class="text-l"><?php echo htmlentities($li['video_url']); ?></td>
                <td class="f-14 product-brand-manage">
                    <a style="text-decoration:none" class="ml-5" onClick="pagefull('视频编辑','<?php echo url("Banner/videoDetail",array("id"=>$li["id"])); ?>')" href="javascript:;" title="编辑">
                    <i class="Hui-iconfont">&#xe6df;</i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!--请在下方写此页面业务相关的脚本-->
<script type="text/javascript" src="/lib/datatables/1.10.0/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="/lib/jquery.validation/1.14.0/jquery.validate.js"></script>
<script type="text/javascript" src="/lib/jquery.validation/1.14.0/validate-methods.js"></script>
<script type="text/javascript" src="/lib/laypage/1.2/laypage.js"></script>
<script type="text/javascript">

    /*打开新窗口*/
    function layeropen(title,url,w,h){
        layer_show(title,url,w,h);
    }

    /*打开新窗口铺满*/
    function pagefull(title,url){
        var index = layer.open({
            type: 2,
            title: title,
            content: url,
            area: ['100%','100%']
        });
        // layer.full(index);
    }


</script>
<!--请在下方写此页面业务相关的脚本-->
<script type="text/javascript" src="/lib/jquery.contextmenu/jquery.contextmenu.r2.js"></script>
<script type="text/javascript">
    /*个人信息*/
    function myselfinfo(){
        layer.open({
            type: 1,
            area: ['300px','200px'],
            fix: false, //不固定
            maxmin: true,
            shade:0.4,
            title: '查看信息',
            content: '<div>管理员信息</div>'
        });
    }


</script>
</body>
</html>
