<?php /*a:3:{s:65:"/var/www/caves.wcip.net/application/user/view/shop/goods_add.html";i:1571109882;s:57:"/var/www/caves.wcip.net/application/user/view/layout.html";i:1566381559;s:64:"/var/www/caves.wcip.net/application/user/view/public/footer.html";i:1566381559;}*/ ?>
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
    .image-del {cursor: pointer;position: absolute;height: 30px;z-index: +9;font-size: 18px;width: 30px;line-height: 30px;background-color: rgba(0,0,0,0.3);color: #efefef;text-align: center;border-bottom-right-radius: 4px;}
    .my-style li .picbox{width: 100%;height: 100%;display: block;}
    .my-style li .picbox a{display: block;width: 100%;height: 100%;background-size: cover;background-repeat: no-repeat;background-position: center;}
</style>
<article class="page-container">
    <form class="form form-horizontal" id="form-article-add">
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2">商品分类：</label>
            <div class="formControls col-xs-8 col-sm-4"> <span class="select-box">
				<select class="select" name="pcate_id" id="pcate_id">
					<option value="">无</option>
                    <?php foreach($list as $li): ?>
					<option value="<?php echo htmlentities($li['id']); ?>"><?php echo htmlentities($li['cate_name']); ?></option>
                    <?php endforeach; ?>
				</select>
				</span>
            </div>
            <div class="formControls col-xs-8 col-sm-4"> <span class="select-box">
				<select class="select" name="cate_id" id="cate_id">

				</select>
				</span>
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>商品名：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" class="input-text" value="" placeholder="" id="name" name="name" maxlength="50">
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span></label>
            <div class="formControls col-xs-8 col-sm-9">
                <span style="font-size: 16px">
                    (建议尺寸: 800*800)
                </span>
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2">
                <span id="btn-star1" class="btn btn-primary btn-uploadstar radius ml-10"  onclick="document.getElementById('qiniu-file').click()">上传图片</span>
            </label>
            <div class="formControls col-xs-8 col-sm-9">
                <ul class="cl portfolio-area my-style" id="fileList">

                </ul>
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2">商品原价(只显示用)：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" class="input-text" value="" placeholder="金额格式 0.00" id="origin_price" name="origin_price" onkeyup="onlyNumber(this)">
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2">商品价格：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" class="input-text" value="" placeholder="金额格式 0.00" id="price" name="price" onkeyup="onlyNumber(this)">
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2">使用规格：</label>
            <div class="formControls col-xs-8 col-sm-10">
                <input type="checkbox" id="use_attr" name="use_attr" value="1" style="width: 20px;height: 20px">
            </div>
        </div>
        <div class="row cl" id="attr_detail" style="display: none">
            <div class="row cl">
                <label class="form-label col-sm-2">规格组：</label>
                <div class="formControls col-sm-4">
                    <input type="text" class="input-text" value="" placeholder=“(如颜色、尺寸)" id="attr" name="attr">
                </div>
                <label class="col-sm-6" id="attr_add"><span class="btn btn-primary">添加+</span></label>
            </div>
            <div class="row cl">
                <label class="form-label col-sm-2"></label>
                <label class="form-label col-sm-2" style="text-align: left" id="attr-value">属性值</label>
                <label class="form-label col-sm-2" style="text-align: left">价格</label>
                <label class="form-label col-sm-2" style="text-align: left">库存</label>
            </div>
        </div>

        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2">运费：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" class="input-text" value="" placeholder="金额格式 0.00" id="carriage" name="carriage" onkeyup="onlyNumber(this)">
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2">商家服务：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" class="input-text" value="" placeholder="7天无理由退换" id="service" name="service" maxlength="30">
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2">单位：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" class="input-text" value="" placeholder="单位:件" id="unit" name="unit" maxlength="10">
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2">库存：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="number" class="input-text" value="0" placeholder="输入数字" id="stock" name="stock" maxlength="8">
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2">排序：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="number" class="input-text" value="0" placeholder="输入数字" id="sort" name="sort" maxlength="8">
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2"><span class="c-red">*</span>是否热销：</label>
            <div class="formControls col-xs-8 col-sm-9 skin-minimal">
                <div class="radio-box">
                    <input name="hot" type="radio" id="hot" value="1" >
                    <label for="hot">是</label>
                </div>
                <div class="radio-box">
                    <input name="hot" type="radio" id="cool" value="0" checked>
                    <label for="cool">否</label>
                </div>
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-2">商品详情：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <textarea id="editor" type="text/plain" name="detail" style="width:100%;height:400px;"></textarea>
            </div>
        </div>
        <div class="row cl">
            <div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-2">
                <button class="btn btn-primary radius" type="submit"><i class="Hui-iconfont">&#xe632;</i> 添加</button>
                <button onClick="layer_close();" class="btn btn-default radius" type="button">&nbsp;&nbsp;取消&nbsp;&nbsp;</button>
            </div>
        </div>
    </form>
    <input type="file"  id="qiniu-file" name="qiniu-file" style="display:none;">

</article>

<script type="text/javascript" src="/lib/lightbox2/2.8.1/js/lightbox.min.js"></script>
<script type="text/javascript" src="/lib/My97DatePicker/4.8/WdatePicker.js"></script>
<script type="text/javascript" src="/lib/jquery.validation/1.14.0/jquery.validate.js"></script>
<script type="text/javascript" src="/lib/jquery.validation/1.14.0/validate-methods.js"></script>
<script type="text/javascript" src="/lib/jquery.validation/1.14.0/messages_zh.js"></script>

<script type="text/javascript" src="/lib/ueditor/1.4.3/ueditor.config.js"></script>
<script type="text/javascript" src="/lib/ueditor/1.4.3/ueditor.all.min.js"> </script>
<script type="text/javascript" src="/lib/ueditor/1.4.3/lang/zh-cn/zh-cn.js"></script>
<script type="text/javascript" src="/lib/checkfile.js"></script>
<script type="text/javascript" src="/lib/qiniu.min.js"></script>
<script type="text/javascript">
    $(function(){
        var ue = UE.getEditor('editor'),isclick=true;

        $('.skin-minimal input').iCheck({
            checkboxClass: 'icheckbox-blue',
            radioClass: 'iradio-blue',
            increaseArea: '80%'
        });

        //表单验证
        $("#form-article-add").validate({
            rules:{
                name:{
                    required:true,
                    maxlength:50
                },
                price:{
                    required:true
                },
                origin_price:{
                    required:true
                },
                carriage:{
                    required:true
                },
                service:{
                    required:true
                },
                unit:{
                    required:true
                },
                stock:{
                    required:true
                },
                sort:{
                    required:true
                }
            },
            focusCleanup:true,
            success:"valid",
            submitHandler:function(form){
                if($("#pcate_id").val() == null || $("#pcate_id").val() == '') {
                    layer.msg('请选择一个一级分类',{icon:5,time:1000});
                    return false;
                }
                if($("#cate_id").val() == null || $("#pcate_id").val() == '') {
                    layer.msg('请选择一个二级分类',{icon:5,time:1000});
                    return false;
                }
                // console.log(d);return;
                if(isclick === true) {
                    isclick = false;
                    $(form).ajaxSubmit({
                        type: 'post',
                        url: "<?php echo url('Shop/goodsAddPost'); ?>" ,
                        success: function(data){
                            // console.log(data);isclick=true;return;
                            if(data.code == 1) {
                                layer.msg('添加成功!',{icon:1,time:1000},function () {
                                    window.parent.location.reload();
                                });
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

        $("#pcate_id").change(function () {
            var pid = $(this).val();
            $.ajax({
                url: "<?php echo url('Shop/getCateList'); ?>" ,
                type: 'post',
                data:{pid:pid},
                success: function(data){
                    // console.log(data.data)
                    if(data.code == 1) {
                        var html = '',list = data.data;
                        if(list.length > 0) {
                            for(var i=0; i <list.length;i++) {
                                html += '<option value="'+list[i].id+'">'+list[i].cate_name+'</option>';
                            }
                        }
                        $("#cate_id").html(html);

                    }else {
                        layer.msg('接口异常',{icon:5,time:1000});
                    }
                },
                error: function(res){
                    layer.msg('接口请求失败!',{icon:5,time:1000});
                    isclick = true
                }
            })
        });

        $(document).on("change","#use_attr",function(){
            if($('#use_attr').is(':checked')) {
                $("#attr_detail").show()
            }else {
                $("#attr_detail").hide()
            }
        });

        $(document).on("click",'.image-del',function(){
            var picBox = $(this).parent().parent();
            layer.confirm("是否要删除该图片？",{
                btn:['是','否']
            },function(){
                picBox.remove();
                layer.closeAll();
            });
        });

        $(document).on("change","#qiniu-file",function(){

            var load = null;
            var limit = 9;
            if($("input[name='pic_url[]']").length > (limit-1)) {
                layer.alert('最多上传'+limit+'张图');
                return false;
            }

            var obj = $("#qiniu-file");
            var fileName = obj.val();//上传的本地文件绝对路径

            if(fileName === '') {
                return;
            }else {
                console.log(fileName,' file.value');
            }

            var suffix = fileName.substring(fileName.lastIndexOf("."),fileName.length);//后缀名
            var suffix_str = suffix.toUpperCase();
            if(suffix_str!=".BMP"&&suffix_str!=".JPG"&&suffix_str!=".JPEG"&&suffix_str!=".PNG"&&suffix_str!=".GIF"){
                layer.alert("请上传图片（格式BMP、JPG、JPEG、PNG、GIF等）!",{title:'提示'});
                return;
            }
            var file = obj.get(0).files[0];	                                           //上传的文件
            var limitsize = 128;
            if(file.size > limitsize*1024) {
                layer.alert('上传文件大小不超过'+limitsize+'Kb',{title:'提示'});
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
                        load = layer.load(2);
                        var observer = {                         //设置上传过程的监听函数
                            next(res){
                                //上传中(result参数带有total字段的 object，包含loaded、total、percent三个属性)
                                Math.floor(res.total.percent);//查看进度[loaded:已上传大小(字节);total:本次上传总大小;percent:当前上传进度(0-100)]
                                console.log(Math.floor(res.total.percent));
                            },
                            error(err){                          //失败后
                                alert(err.message);
                                layer.close(load);
                            },
                            complete(res){                       //成功后
                                // console.log(res,'---upload success');
                                var src = result.data.weburl + result.data.filename;
                                var html = '<li class="item"><div class="portfoliobox"><span class="image-del" title="删除"><i class="Hui-iconfont">&#xe6e2;</i></span><div class="picbox"><a href="'+src+'" data-lightbox="gallery" data-title="" style="background-image: url('+src+')"></a></div><input type="hidden" value="'+result.data.filename+'" name="pic_url[]"/></div></li>';
                                $("#fileList").append(html);
                                // layer.alert('上传成功',{icon:6});
                                layer.close(load);
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

        //属性描述
        $("#attr").on("input",function() {
            $("#attr-value").text("属性值(" + $(this).val() +")")
        });
        //添加属性值
        $("#attr_add").click(function() {
            var new_value = $("#attr").val();
            if(new_value ==  '') {
                alert('请先设置一个规格');
                return false;
            }
            $("#attr_detail").append(
                '<div class="row cl"><label class="form-label col-sm-2"><a style="text-decoration:none" class="ml-5" title="删除" onclick="javascript:$(this).parent().parent().remove();"><i class="Hui-iconfont">&#xe6e2;</i></a></label><div class="formControls col-sm-2"><input type="text" class="input-text" value="" name="attr1[]" maxlength="20"></div><div class="formControls col-sm-2"><input type="text" class="input-text" value="" name="attr2[]" onkeyup="onlyNumber(this)"></div><div class="formControls col-sm-2"><input type="number" class="input-text" value="" name="attr3[]" maxlength="8"></div></div>'
            );
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
