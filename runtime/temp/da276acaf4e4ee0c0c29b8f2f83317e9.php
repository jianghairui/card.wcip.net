<?php /*a:3:{s:72:"/var/www/caves.wcip.net/application/admin/view/funding/funding_list.html";i:1567663849;s:58:"/var/www/caves.wcip.net/application/admin/view/layout.html";i:1566381559;s:65:"/var/www/caves.wcip.net/application/admin/view/public/footer.html";i:1566381559;}*/ ?>
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
    .order-font-b{color:#363636;font-size: 13px}
    .funding-label{display: inline-flex;font-size: 12px;width: 60px;font-weight: bold}
</style>
<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 众筹管理 <span class="c-gray en">&gt;</span> 众筹列表 <a class="btn btn-success radius r" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a></nav>
<div class="page-container">
    <section id="formAjax">
        <div class="text-c">
            <button onclick="removeIframe()" class="btn btn-primary radius">关闭选项卡</button>
            <input type="text" name="search" value="<?php echo htmlentities(app('request')->get('search')); ?>" id="search" placeholder=" 众筹" style="width:250px" class="input-text">
            <button name="" id="search-btn" class="btn btn-success" type="button"><i class="Hui-iconfont">&#xe665;</i> 查找众筹</button>
        </div>
    </section>
    <div class="cl pd-5 bg-1 bk-gray mt-20">
        <span class="l">
            <a class="btn btn-primary radius" data-title="发起众筹" data-href="" onclick="pagefull('发起众筹','<?php echo url("Funding/fundingAdd"); ?>')" href="javascript:;"><i class="Hui-iconfont">&#xe600;</i> 发起众筹
            </a>
        </span>
        <span class="r">共有数据：<strong><?php echo htmlentities($page['count']); ?></strong> 条</span> </div>
    <table class="table table-border table-bordered table-bg">
        <thead>
        <tr class="text-c">
            <th width="25">#</th>
            <th width="120">众筹封面</th>
            <th>众筹标题</th>
            <th width="80">目标金额</th>
            <th width="80">已筹金额</th>
            <th width="80">无偿金额</th>
            <th width="80">有偿金额</th>
            <th width="60">状态</th>
            <th width="100">操作</th>
        </tr>
        </thead>
        <tbody>
        <form id="multi-check">
            <?php foreach($list as $li): ?>
            <tr class="text-c userinfo">
                <td rowspan="2"><?php echo htmlentities($li['id']); ?></td>
                <td rowspan="2">
                    <div style="width: 120px;height: 120px;background-image: url('<?php echo config('qiniu_weburl');?><?php echo htmlentities($li['cover']); ?>');background-position: center;background-repeat: no-repeat;background-size: 100%"></div>
                </td>
                <td rowspan="2" class="text-l">
                    <div style="margin: 0 10px;">
                        <div><label class="funding-label">众筹标题:</label><span class="order-font-b"><?php echo htmlentities($li['title']); ?></span></div>
                        <div><label class="funding-label">活动来源:</label><span class="order-font-b"><?php echo htmlentities($li['req_title']); ?></span></div>
                        <div><label class="funding-label">引用创意:</label><span class="order-font-b"><?php if($li['idea_title']): ?><?php echo htmlentities($li['idea_title']); else: ?>未引用<?php endif; ?></span></div>
                        <div><label class="funding-label">创作作品:</label><span class="order-font-b"><?php echo htmlentities($li['work_title']); ?></span></div>
                        <div><label class="funding-label">接单工厂:</label><span class="order-font-b"><?php echo htmlentities($li['factory_name']); ?></span></div>
                    </div>
                </td>
                <td><?php echo htmlentities($li['need_money']); ?></td>
                <td><?php echo htmlentities($li['curr_money']); ?></td>
                <td><?php echo htmlentities($li['free_money']); ?></td>
                <td><?php echo htmlentities($li['paid_money']); ?></td>
                <td class="td-status">
                    <?php switch($li['status']): case "0": ?>
                    <span class="label label-danger radius">已终止</span>
                    <?php break; case "1": ?>
                    <span class="label label-success radius">众筹中</span>
                    <?php break; case "2": ?>
                    <span class="label label-primary radius">已结束</span>
                    <?php break; case "3": ?>
                    <span class="label label-defaunt radius">众筹失败</span>
                    <?php break; default: endswitch; ?>
                </td>
                <td class="td-manage" rowspan="2">
                    <span>
                        <a style="text-decoration:none" onclick="pagefull('众筹详情','<?php echo url("Funding/fundingDetail",array("id"=>$li["id"])); ?>')" href="javascript:;" title="众筹详情">
                            <i class="Hui-iconfont">&#xe6df;</i>
                        </a>
                    </span>
                    <span>
                        <a style="text-decoration:none" onClick="funding_del(this,<?php echo htmlentities($li['id']); ?>)" href="javascript:;" title="删除">
                        <i class="Hui-iconfont">&#xe6e2;</i>
                        </a>
                    </span>
                </td>
            </tr>
            <tr>
                <td colspan="5">
                    <div style="margin: 0 10px;">
                        <div><label class="funding-label">开始时间:</label><span class="order-font-b"><?php echo htmlentities(date("Y年m月d日",!is_numeric($li['start_time'])? strtotime($li['start_time']) : $li['start_time'])); ?></span></div>
                        <div><label class="funding-label">截止日期:</label><span class="order-font-b"><?php echo htmlentities(date("Y年m月d日",!is_numeric($li['end_time'])? strtotime($li['end_time']) : $li['end_time'])); ?></span></div>
                        <div><label class="funding-label">支持人数:</label><span class="order-font-b"><?php echo htmlentities($li['order_num']); ?></span></div>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </form>
        <tr>
            <td colspan="12" id="page" class="text-r"></td>
        </tr>
        </tbody>
    </table>
</div>
<!--请在下方写此页面业务相关的脚本-->
<script type="text/javascript" src="/lib/My97DatePicker/4.8/WdatePicker.js"></script>
<script type="text/javascript" src="/lib/datatables/1.10.0/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="/lib/laypage/1.2/laypage.js"></script>
<script type="text/javascript">

    var url = '<?php echo url("Funding/fundingList"); ?>' + '?<?php echo $page["query"];?>';
    var curr = '<?php echo htmlentities($page['curr']); ?>',totalPage = '<?php echo htmlentities($page['totalPage']); ?>';
    var click_lock = true;
    if(totalPage > 1) {
        laypage({
            cont: 'page', //容器。值支持id名、原生dom对象，jquery对象。
            pages: totalPage, //通过后台拿到的总页数
            skip: true, //是否开启跳页
            skin: '#5a98de',
            curr: curr || 1, //当前页
            jump: function(obj, first){ //触发分页后的回调
                // console.log(obj)
                if(!first) {
                    window.location.href = url+'&page=' + obj.curr;
                }
            }
        });
    }

    $("#search-btn").click(function () {
        var search = $("#formAjax").find("#search").val();
        var id = $("#formAjax").find("#id").val();
        var str = 'id=' + id;
        if(search.length != '') {
            str += '&search=' + search;
        }
        window.location.href = '<?php echo url("Funding/fundingList"); ?>' + '?' + str;
    });

    /*发布众筹*/
    function pagefull(title,url){
        var index = layer.open({
            type: 2,
            title: title,
            content: url,
            area: ['100%','100%']
        });
    }

    /*删除*/
    function funding_del(obj,id){
        layer.confirm('警告!此操作会将相关商品一并删除,确定删除吗？',function(index){
            $.ajax({
                url: "<?php echo url('Funding/fundingDel'); ?>",
                type: 'POST',
                dataType: 'json',
                data:{id:id},
                success: function(data){
                    console.log(data);
                    if(data.code == 1) {
                        $(obj).parents("tr").next().remove();
                        $(obj).parents("tr").remove();
                        layer.msg('已删除!',{icon:1,time:1000});
                    }else {
                        layer.msg('删除失败!',{icon:2,time:1000});
                    }
                },
                error:function(data) {
                    console.log(data.msg);
                },
            });
        });
    }
    /*众筹审核*/
    function funding_shenhe(obj,id){
        layer.confirm('审核众筹？', {
                btn: ['通过','不通过','取消'],
                shade: false,
                closeBtn: 0
            },
            function(){
                $.ajax({
                    url:"<?php echo url('Funding/fundingPass'); ?>",
                    type:'post',
                    dataType:'json',
                    data:{id:id},
                    success:function(data) {
                        console.log(data)
                        if(data.code == 1) {
                            $(obj).parents("tr").find(".td-status").html('<span class="label label-success radius">已通过</span>');
                            $(obj).parent().html('<span class="btn btn-link radius"></span>');
                            layer.msg('已审核', {icon:1,time:1000});
                        }else {
                            layer.msg(data.data,{icon:2,time:1000})
                        }
                    },
                    error:function(data) {
                        layer.msg('请求失败!',{icon:5,time:1000})
                    },
                })

            },
            function(){
                layer.prompt({
                    formType: 2,
                    value: '内容违规',
                    title: '请输入理由(最多50个字)',
                    maxlength:50,
                    area: ['400px', '300px'] //自定义文本域宽高
                }, function(value, index, elem){
                    reason = value;
                    layer.close(index);
                    $.ajax({
                        url:"<?php echo url('Funding/fundingReject'); ?>",
                        type:'post',
                        dataType:'json',
                        data:{id:id,reason:reason},
                        success:function(data) {
                            console.log(data)
                            if(data.code == 1) {
                                $(obj).parents("tr").find(".td-status").html('<span class="label label-danger radius">未通过</span>');
                                $(obj).parent().html('<span class="btn btn-link radius"></span>');
                                layer.msg('已拒绝', {icon:1,time:1000});
                            }else {
                                layer.msg(data.data,{icon:2,time:1000})
                            }
                        },
                        error:function(data) {
                            layer.msg('请求失败!',{icon:5,time:1000})
                        }
                    })
                })


            });
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
