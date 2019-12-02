<?php /*a:3:{s:70:"/var/www/caves.wcip.net/application/admin/view/xuqiu/xuqiu_detail.html";i:1574393457;s:58:"/var/www/caves.wcip.net/application/admin/view/layout.html";i:1566381559;s:65:"/var/www/caves.wcip.net/application/admin/view/public/footer.html";i:1566381559;}*/ ?>
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
    .thumbnail{ width:256px;height: 256px;background-size: cover;background-position: center;position: relative}
    .comment-box{margin-top: 20px}
    .comment-box .form-label{
        text-align: right;
    }
</style>
<link href="/lib/lightbox2/2.8.1/css/lightbox.css" rel="stylesheet" type="text/css" >
<article class="page-container">
    <form class="form form-horizontal" id="form-article-add">

        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2">
                <span id="btn-star1" class="btn btn-primary btn-uploadstar radius ml-10">需求图片</span>
            </label>
            <div class="formControls col-xs-8 col-sm-9">
                <ul class="cl portfolio-area" id="fileList">
                    <?php $images = unserialize($info['pics']);if($images): foreach($images as $v): ?>
                    <li class="item">
                        <div class="portfoliobox">
                            <!--<span class="image-del" title="删除"><i class="Hui-iconfont">&#xe6e2;</i></span>-->
                            <div class="picbox">
                                <a href="<?php echo htmlentities($qiniu_weburl); ?><?php echo htmlentities($v); ?>" data-lightbox="gallery" data-title=""><img src="<?php echo htmlentities($qiniu_weburl); ?><?php echo htmlentities($v); ?>"></a>
                            </div>
                        </div>
                    </li>
                    <?php endforeach; endif; ?>
                </ul>
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>需求标题：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" class="input-text" value="<?php echo htmlentities($info['title']); ?>" name="title" placeholder="" >
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2">需求内容：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <textarea name="content" class="textarea" maxlength="1000" placeholder="" oninput="this.value.length<=1000?$('#textlen1').text(this.value.length):$('#textlen1').text(1000)" ><?php echo htmlentities($info['content']); ?></textarea>
                <p class="textarea-numberbar"><em class="textarea-length" id="textlen1">0</em>/1000</p>
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>发布人(只读)：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" class="input-text" value="<?php echo htmlentities($info['nickname']); ?>" name="nickname" placeholder=""  readonly>
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2">发布时间(只读)：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" class="input-text" value="<?php echo htmlentities($info['create_time']); ?>" placeholder=""  readonly>
            </div>
        </div>
        <div class="row cl">
            <div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-2">
                <button class="btn btn-primary radius" type="submit"><i class="Hui-iconfont">&#xe632;</i> 保存</button>
                <button onClick="layer_close();" class="btn btn-default radius" type="button">&nbsp;&nbsp;取消&nbsp;&nbsp;</button>
            </div>
        </div>
        <input type="hidden" name="id" value="<?php echo htmlentities($info['id']); ?>">
    </form>
    <div class="row cl comment-box">
        <label class="form-label col-xs-4 col-sm-2">评论内容：</label>
        <div class="formControls col-xs-8 col-sm-9">
            <div class="comment">评论内容</div>
        </div>
    </div>
</article>


<script type="text/javascript" src="/lib/lightbox2/2.8.1/js/lightbox.min.js"></script>
<script type="text/javascript" src="/lib/jquery.validation/1.14.0/jquery.validate.js"></script>
<script type="text/javascript" src="/lib/jquery.validation/1.14.0/validate-methods.js"></script>
<script type="text/javascript">


    $(function () {

        var isclick = true;
        //表单验证
        $("#form-article-add").validate({
            rules:{},
            focusCleanup:false,
            success:"valid",
            submitHandler:function(form){
                if(isclick === true) {
                    isclick = false;
                    $(form).ajaxSubmit({
                        type: 'post',
                        url: "<?php echo url('Xuqiu/xuqiuModPost'); ?>" ,
                        success: function(data){
                            console.log(data);
                            if(data.code == 1) {
                                layer.msg('保存成功!',{icon:1,time:1000},function () {
                                    window.parent.location.reload()
                                });
                            }else {
                                layer.msg(data.data,{icon:2,time:1000});
                                isclick = true
                            }

                        },
                        error: function(XmlHttpRequest, textStatus, errorThrown){
                            layer.msg('接口请求失败!',{icon:5,time:1000});
                            isclick = true
                        }
                    });
                }

            }
        });


        var textlen1 = $("textarea[name='content']").val().length;
        $("#textlen1").text(textlen1);

    })


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
