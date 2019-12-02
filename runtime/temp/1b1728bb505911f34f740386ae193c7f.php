<?php /*a:3:{s:65:"/var/www/caves.wcip.net/application/admin/view/req/work_list.html";i:1568129623;s:58:"/var/www/caves.wcip.net/application/admin/view/layout.html";i:1566381559;s:65:"/var/www/caves.wcip.net/application/admin/view/public/footer.html";i:1566381559;}*/ ?>
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
<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 作品管理 <span class="c-gray en">&gt;</span> 作品列表 <a class="btn btn-success radius r" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a></nav>
<div class="page-container">
    <section id="formAjax">
        <div class="text-c">
            <button onclick="removeIframe()" class="btn btn-primary radius">关闭选项卡</button>
            <span class="select-box inline">
				<select class="select" name="req_id" id="req_id" style="width: 200px;">
                    <option value="">全部(活动)</option>
                    <?php foreach($reqlist as $li): ?>
					<option value="<?php echo htmlentities($li['id']); ?>" <?php if($param['req_id']==$li['id']): ?>selected<?php endif; ?>><?php echo htmlentities($li['title']); ?>(id:<?php echo htmlentities($li['id']); ?>)</option>
                    <?php endforeach; ?>
				</select>
            </span>
            <input type="text" name="search" value="<?php echo htmlentities(app('request')->get('search')); ?>" id="search" placeholder=" 作品" style="width:250px" class="input-text">
            <button name="" id="search-btn" class="btn btn-success" type="button"><i class="Hui-iconfont">&#xe665;</i> 查找作品</button>
            <span class="select-box inline">
				<select class="select" name="sort" id="sort" style="width: 150px;">
                    <option value="0">排序(默认)</option>
					<option value="1" <?php if($param['sort']==1): ?>selected<?php endif; ?>>票数从高到低</option>
                    <option value="2" <?php if($param['sort']==2): ?>selected<?php endif; ?>>票数从低到高</option>
                    <option value="3" <?php if($param['sort']==3): ?>selected<?php endif; ?>>竞标数从高到低</option>
                    <option value="4" <?php if($param['sort']==4): ?>selected<?php endif; ?>>竞标数从低到高</option>
				</select>
            </span>
        </div>
    </section>
    <div class="cl pd-5 bg-1 bk-gray mt-20">
        <span class="l">

        </span>
        <span class="r">共有数据：<strong><?php echo htmlentities($page['count']); ?></strong> 条</span> </div>
    <table class="table table-border table-bordered table-bg">
        <thead>
        <tr class="text-c">
            <th width="25">#</th>
            <th width="100">作品封面</th>
            <th>作品标题</th>
            <th width="200">所属活动</th>
            <th width="120">举办方单位</th>
            <th width="80">投票数</th>
            <th width="80">竞标数</th>
            <th width="120">投稿时间</th>
            <th width="80">审核状态</th>
            <th width="80">审核</th>
            <th width="100">操作</th>
        </tr>
        </thead>
        <tbody>
        <form id="multi-check">
            <?php foreach($list as $li): ?>
            <tr class="text-c userinfo">
                <td><?php echo htmlentities($li['id']); ?></td>
                <td>
                    <?php $image = unserialize($li['pics'])[0];?>
                    <div style="width: 100px;height: 100px;background-image: url('<?php echo config('qiniu_weburl');?><?php echo htmlentities($image); ?>');background-position: center;background-repeat: no-repeat;background-size: 100%"></div>
                </td>
                <td><?php echo htmlentities($li['title']); ?></td>
                <td><?php echo htmlentities($li['req_title']); ?></td>
                <td><?php echo htmlentities($li['org']); ?></td>
                <td><?php echo htmlentities($li['vote']); ?></td>
                <td><?php echo htmlentities($li['bid_num']); ?></td>
                <td><?php echo htmlentities(date("Y-m-d H:i:s",!is_numeric($li['create_time'])? strtotime($li['create_time']) : $li['create_time'])); ?></td>
                <td class="td-status">
                    <?php switch($li['status']): case "0": ?>
                    <span class="label label-warning radius">审核中</span>
                    <?php break; case "1": ?>
                    <span class="label label-success radius">已通过</span>
                    <?php break; case "2": ?>
                    <span class="label label-danger radius">未通过</span>
                    <?php break; default: endswitch; ?>
                </td>
                <td>
                    <?php if($li['status'] == 0): ?>
                    <a class="btn btn-link radius" style="text-decoration:none;" onClick="work_shenhe(this,<?php echo htmlentities($li['id']); ?>)" href="javascript:;" title="审核">审核</a>
                    <?php else: ?>
                    <span class="btn btn-link radius"></span>
                    <?php endif; ?>
                </td>
                <td class="td-manage">
                    <span>
                        <a style="text-decoration:none" onclick="pagefull('作品详情','<?php echo url("Req/workDetail",array("id"=>$li["id"])); ?>')" href="javascript:;" title="作品详情">
                            <i class="Hui-iconfont">&#xe6df;</i>
                        </a>
                    </span>
                    <span>
                        <a style="text-decoration:none" onClick="work_del(this,<?php echo htmlentities($li['id']); ?>)" href="javascript:;" title="删除">
                        <i class="Hui-iconfont">&#xe6e2;</i>
                        </a>
                    </span>
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

    var url = '<?php echo url("Req/workList"); ?>' + '?<?php echo $page["query"];?>';
    var curr = '<?php echo htmlentities($page['curr']); ?>',totalPage = '<?php echo htmlentities($page['totalPage']); ?>';
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
        var req_id = $("#formAjax").find("#req_id").val();
        var search = $("#formAjax").find("#search").val();
        var sort = $("#formAjax").find("#sort").val();
        var str = '';
        if(req_id.length != '') {
            str += '&req_id=' + req_id;
        }
        if(search.length != '') {
            str += '&search=' + search;
        }
        if(sort.length != '') {
            str += '&sort=' + sort;
        }
        window.location.href = '<?php echo url("Req/workList"); ?>' + '?' + str;
    });

    /*发布作品*/
    function pagefull(title,url){
        var index = layer.open({
            type: 2,
            title: title,
            content: url,
            area: ['100%','100%']
        });
    }

    /*删除*/
    function work_del(obj,id){
        layer.confirm('确认要删除吗？',function(index){
            $.ajax({
                url: "<?php echo url('Req/workDel'); ?>",
                type: 'POST',
                dataType: 'json',
                data:{id:id},
                success: function(data){
                    console.log(data);
                    if(data.code == 1) {
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

    /*作品审核*/
    function work_shenhe(obj,id){
        layer.confirm('审核作品？', {
                btn: ['通过','不通过','取消'],
                shade: false,
                closeBtn: 0
            },
            function(){
                $.ajax({
                    url:"<?php echo url('Req/workPass'); ?>",
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
                        url:"<?php echo url('Req/workReject'); ?>",
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
