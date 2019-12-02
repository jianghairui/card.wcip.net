<?php /*a:3:{s:68:"/var/www/caves.wcip.net/application/admin/view/shop/cate_detail.html";i:1567060855;s:58:"/var/www/caves.wcip.net/application/admin/view/layout.html";i:1566381559;s:65:"/var/www/caves.wcip.net/application/admin/view/public/footer.html";i:1566381559;}*/ ?>
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
    .thumbnail{ width:300px;}
</style>
<article class="page-container">
    <form class="form form-horizontal" id="form-article-add">
        <?php if($info['pid'] != 0): ?>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2">父级分类：</label>
            <div class="formControls col-xs-8 col-sm-9"> <span class="select-box">
				<select class="select" name="pid" id="pid">
					<option value="0">无</option>
                    <?php foreach($list as $li): ?>
					<option value="<?php echo htmlentities($li['id']); ?>" <?php if($info['pid'] == $li['id']): ?>selected<?php endif; ?>><?php echo htmlentities($li['cate_name']); ?></option>
                    <?php endforeach; ?>
				</select>
				</span>
            </div>
        </div>
        <?php else: ?>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2">
            </label>
            <div class="formControls col-xs-8 col-sm-9">
                顶级分类
            </div>
        </div>
        <?php endif; ?>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2">
                <span id="btn-star" class="btn btn-primary uploadfile radius ml-10">分类图标</span>
            </label>
            <div class="formControls col-xs-8 col-sm-9">
                <div class="uploader-thum-container">
                    <div id="fileList" class="uploader-list">
                        <img class="thumbnail" src="<?php echo htmlentities($qiniu_weburl); ?><?php echo htmlentities($info['icon']); ?>" alt="">
                    </div>
                </div>
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>分类名称：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" class="input-text" value="<?php echo htmlentities($info['cate_name']); ?>" placeholder="" id="cate_name" name="cate_name">
            </div>
        </div>

        <div class="row cl">
            <div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-2">
                <button class="btn btn-primary radius" type="submit"><i class="Hui-iconfont">&#xe632;</i> 保存</button>
                <button onClick="layer_close();" class="btn btn-default radius" type="button">&nbsp;&nbsp;取消&nbsp;&nbsp;</button>
            </div>
        </div>
        <input type="hidden" name="icon" id="icon" value="<?php echo htmlentities($info['icon']); ?>">
        <input type="hidden" name="id" value="<?php echo htmlentities($info['id']); ?>">
    </form>
    <input type="file" name="qiniu-file" id="qiniu-file" style="display: none;">

</article>


<script type="text/javascript" src="/lib/My97DatePicker/4.8/WdatePicker.js"></script>
<script type="text/javascript" src="/lib/jquery.validation/1.14.0/jquery.validate.js"></script>
<script type="text/javascript" src="/lib/jquery.validation/1.14.0/validate-methods.js"></script>
<script type="text/javascript" src="/lib/jquery.validation/1.14.0/messages_zh.js"></script>
<script type="text/javascript" src="/lib/qiniu.min.js"></script>

<script type="text/javascript">
    $(function(){

        $('.skin-minimal input').iCheck({
            checkboxClass: 'icheckbox-blue',
            radioClass: 'iradio-blue',
            increaseArea: '20%'
        });

        var isclick = true;
        //表单验证
        $("#form-article-add").validate({
            rules:{
                cate_name:{
                    required:true,
                    maxlength:50
                }
            },
            focusCleanup:false,
            success:"valid",
            submitHandler:function(form){
                if($("#pid").val() == 0) {
                    layer.alert('请选择一个父级分类');
                    return false;
                }
                if(isclick === true) {
                    isclick = false;
                    $(form).ajaxSubmit({
                        type: 'post',
                        url: "<?php echo url('Shop/cateModPost'); ?>" ,
                        success: function(data){
                            // console.log(data);return;
                            if(data.code == 1) {
                                layer.msg('保存成功!',{icon:1,time:1000});
                                setTimeout("window.parent.location.reload()", 1000)
                            }else {
                                layer.msg(data.data,{icon:2,time:1000});
                                isclick = true
                            }

                        },
                        error: function(XmlHttpRequest, textStatus, errorThrown){
                            layer.msg('error!',{icon:5,time:1000});
                            isclick = true
                        }
                    });
                }

            }
        });


        $(".uploadfile").click(function () {
            $("#qiniu-file").click();
        });

        $(document).on("change","#qiniu-file",function(){
            var obj = $("#qiniu-file");
            var fileName = obj.val();//上传的本地文件绝对路径
            $("#uploadfile").val(fileName);
            if(fileName === '') {
                return;
            }else {
                console.log(fileName,' file.value');
            }

            var suffix = fileName.substring(fileName.lastIndexOf("."),fileName.length);//后缀名
            var suffix_str = suffix.toUpperCase();
            if(suffix_str!=".BMP"&&suffix_str!=".JPG"&&suffix_str!=".JPEG"&&suffix_str!=".PNG"&&suffix_str!=".GIF"){
                alert("请上传图片（格式BMP、JPG、JPEG、PNG、GIF等）!");
                return;
            }
            var file = obj.get(0).files[0];	                                           //上传的文件
            if(file.size > 128*1024) {
                layer.alert('上传文件大小不超过128Kb');
                return;
            }
            //七牛云上传
            $.ajax({
                type:'post',
                url: "<?php echo url('Qiniu/getUpToken'); ?>",
                data:{"suffix":suffix},
                dataType:'json',
                success: function(result){
                    if(result.code == 1){
                        var observer = {                         //设置上传过程的监听函数
                            next(res){                        //上传中(result参数带有total字段的 object，包含loaded、total、percent三个属性)
                                Math.floor(res.total.percent);//查看进度[loaded:已上传大小(字节);total:本次上传总大小;percent:当前上传进度(0-100)]
                                console.log(Math.floor(res.total.percent));
                            },
                            error(err){                          //失败后
                                alert(err.message);
                            },
                            complete(res){                       //成功后
                                // console.log(res,'---upload success');
                                layer.alert('上传成功',{icon:6});
                                $("#icon").val(result.data.filename);
                                $("#fileList").html('<img class="thumbnail" src="' + result.data.weburl + result.data.filename + '" />')
                            }
                        };
                        var putExtra = {
                            fname: "",                          //原文件名
                            params: {},                         //用来放置自定义变量
                            mimeType: null                      //限制上传文件类型
                        };
                        var config = {
                            region:qiniu.region.z1,             //存储区域(z0:代表华东;z2:代表华南,不写默认自动识别)
                            concurrentRequestLimit:3            //分片上传的并发请求量
                        };
                        var observable = qiniu.upload(file,result.data.filename,result.data.token,putExtra,config);
                        var subscription = observable.subscribe(observer);          // 上传开始
                        // 取消上传
                        // subscription.unsubscribe();
                    }else{
                        alert(result.data);                  //获取凭证失败
                    }
                },error:function(){                             //服务器响应失败处理函数
                    layer.alert("服务器繁忙");
                }
            });
        });



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
