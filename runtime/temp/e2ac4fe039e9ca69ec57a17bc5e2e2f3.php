<?php /*a:3:{s:63:"/var/www/caves.wcip.net/application/admin/view/qiniu/index.html";i:1566816054;s:58:"/var/www/caves.wcip.net/application/admin/view/layout.html";i:1566381559;s:65:"/var/www/caves.wcip.net/application/admin/view/public/footer.html";i:1566381559;}*/ ?>
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
    .thumbnail{ width:200px;height: 200px;background-size: cover;background-position: center;position: relative}
</style>
<article class="page-container">
    <form class="form form-horizontal" id="form-article-add">
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2">
                <span id="qiniu-upload" class="btn btn-uploadstar radius ml-10">上传附件</span>
            </label>
            <div class="formControls col-xs-4 col-sm-4">
                <label for="qiniu-file" class="btn btn-toolbar" style="display: inline-block">选择文件</label>
                <input id="filename" type="text" class="input-text" value=""  style="display: inline-block;width: 200px;" disabled>
            </div>
            <div class="formControls col-xs-4 col-sm-4">
                <div style="width: 100%;height: 20px;background: #e3e3e3;border-radius:2px;display: none" id="progress-bar">
                    <div style="width: 0px;height: 20px;background: #177fcb;border-radius:2px" id="progress"><b style="margin-left: 15px;width: 80px;display: block" id="progress-text">0%</b></div>
                </div>
            </div>
            <input type="hidden" name="file_path" id="file_path" value="">
        </div>
    </form>
</article>
<input type="file" name="qiniu" id="qiniu-file" style="display: none">

<script type="text/javascript" src="/lib/jquery.validation/1.14.0/jquery.validate.js"></script>
<script type="text/javascript" src="/lib/jquery.validation/1.14.0/validate-methods.js"></script>
<script type="text/javascript" src="/lib/jquery.validation/1.14.0/messages_zh.js"></script>
<script type="text/javascript" src="/lib/checkfile.js"></script>
<script type="text/javascript" src="/lib/qiniu.min.js"></script>
<script type="text/javascript">
    $(function(){

        $("#qiniu-upload").on("click", function () {
            var obj = $("#qiniu-file");
            var fileName = obj.val();            //上传的本地文件绝对路径
            if(fileName === '') {
                layer.alert('请选择要上传的附件');
                return;
            }
            var suffix = fileName.substring(fileName.lastIndexOf("."),fileName.length);//后缀名
            var file = obj.get(0).files[0];	                                           //上传的文件
            if(file.size > 2*1024*1024*1024) {
                layer.alert('上传文件大小不超过2G');
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
                                // console.log(Math.floor(res.total.percent));
                                $("#progress-bar").show();
                                $("#progress").css('width',Math.floor(res.total.percent)+'%');
                                $("#progress-text").text(Math.floor(res.total.percent)+'%')
                            },
                            error(err){                          //失败后
                                alert(err.message);
                            },
                            complete(res){                       //成功后
                                // console.log(res,'---upload success');
                                layer.alert('上传完成',{icon:6});
                                // console.log("http://"+result.data.domain+"/"+result.data.filename)
                                $("#file_path").val("http://"+result.data.domain+"/"+result.data.filename)
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

        $(document).on("change","#qiniu-file",function(){
            var obj = $("#qiniu-file");
            var fileName = obj.val();            //上传的本地文件绝对路径
            console.log(fileName);
            $("#filename").val(fileName);
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
