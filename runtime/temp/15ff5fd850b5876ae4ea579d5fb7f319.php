<?php /*a:3:{s:72:"/var/www/caves.wcip.net/application/admin/view/req/show_work_detail.html";i:1571799929;s:58:"/var/www/caves.wcip.net/application/admin/view/layout.html";i:1566381559;s:65:"/var/www/caves.wcip.net/application/admin/view/public/footer.html";i:1566381559;}*/ ?>
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
<link href="/lib/lightbox2/2.8.1/css/lightbox.css" rel="stylesheet" type="text/css" >
<style>
    .image-del {
        position: absolute;z-index: +9;font-size: 20px;cursor: pointer;
    }
    .thumbnail{ width:132px;height:132px;border-radius: 50%;}
</style>
<article class="page-container">
    <form class="form form-horizontal" id="form-article-add">
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3"></label>
            <div class="formControls col-xs-8 col-sm-9">
                <img class="thumbnail" src="<?php if($info['avatar']): if(substr($info['avatar'],0,4) == 'http'): ?><?php echo htmlentities($info['avatar']); else: ?><?php echo htmlentities($qiniu_weburl); ?><?php echo htmlentities($info['avatar']); endif; else: ?>/static/src/image/default.jpg<?php endif; ?>">
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3">设计师昵称：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <span class=""><?php echo htmlentities($info['nickname']); ?></span>
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>作品标题：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" class="input-text" value="<?php echo htmlentities($info['title']); ?>" placeholder="输入活动标题" id="title" name="title" maxlength="30" readonly >
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3">作品展示: </label>
            <div class="formControls col-xs-8 col-sm-9">
                <ul class="cl portfolio-area" id="fileList">
                    <?php $images = unserialize($info['pics']);if($images): foreach($images as $v): ?>
                    <li class="item">
                        <div class="portfoliobox">
                            <div class="picbox">
                                <a href="<?php echo htmlentities($qiniu_weburl); ?><?php echo htmlentities($v); ?>" data-lightbox="gallery" data-title=""><img src="<?php echo htmlentities($qiniu_weburl); ?><?php echo htmlentities($v); ?>"></a>
                            </div>
                            <input type="hidden" value="<?php echo htmlentities($v); ?>" name="pic_url[]"/>
                        </div>
                    </li>
                    <?php endforeach; endif; ?>
                </ul>
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3">作品内容：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <textarea name="desc" cols="" rows="" class="textarea" maxlength="500" placeholder="" oninput="this.value.length<=500?$('#textlen1').text(this.value.length):$('#textlen1').text(500)" readonly><?php echo htmlentities($info['desc']); ?></textarea>
                <p class="textarea-numberbar"><em class="textarea-length" id="textlen1">0</em>/500</p>
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3">作品时间：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <span class=""><?php echo htmlentities(date("Y-m-d H:i:s",!is_numeric($info['create_time'])? strtotime($info['create_time']) : $info['create_time'])); ?></span>
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3">审核状态<?php echo htmlentities($info['status']); ?>：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <?php switch($info['status']): case "0": ?>
                <span class="label label-warning radius">审核中</span><?php break; case "1": ?>
                <span class="label label-success radius">已通过</span><?php break; case "2": ?>
                <span class="label label-danger radius">未通过</span>

                <span style="color: red;margin-left: 15px">(*<?php echo htmlentities($info['reason']); ?>)</span><?php break; default: endswitch; ?>
            </div>
        </div>
        <div class="row cl">
            <div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-3">
                <button onClick="layer_close();" class="btn btn-default radius" type="button">&nbsp;&nbsp;关闭页面&nbsp;&nbsp;</button>
            </div>
        </div>
        <input type="hidden" name="id" value="<?php echo htmlentities($info['id']); ?>">
    </form>
    <input type="file" name="file" id="qiniu-file" style="display: none;">
</article>

<script type="text/javascript" src="/lib/jquery.validation/1.14.0/jquery.validate.js"></script>
<script type="text/javascript" src="/lib/jquery.validation/1.14.0/validate-methods.js"></script>
<script type="text/javascript" src="/lib/jquery.validation/1.14.0/messages_zh.js"></script>
<script type="text/javascript" src="/lib/lightbox2/2.8.1/js/lightbox.min.js"></script>

<script type="text/javascript">

    $(function(){


    });

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
