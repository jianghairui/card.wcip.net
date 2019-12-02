<?php /*a:3:{s:67:"/var/www/caves.wcip.net/application/admin/view/shop/goods_list.html";i:1571620150;s:58:"/var/www/caves.wcip.net/application/admin/view/layout.html";i:1566381559;s:65:"/var/www/caves.wcip.net/application/admin/view/public/footer.html";i:1566381559;}*/ ?>
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
<link href="/lib/select2/select2.min.css" rel="stylesheet">
<script src="/lib/select2/select2.min.js"></script>
<style>
    .userinfo>td>img {width:80px;height:80px}
</style>
<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 商城管理 <span class="c-gray en">&gt;</span> 商品列表 <a class="btn btn-success radius r" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a></nav>
<div class="page-container">
    <section id="formAjax">
        <div class="text-c">
            <span class="inline">
				<select class="select" name="shop_id" id="shop_id" style="width: 400px;">
                    <option value="">全部商户</option>
                    <option value="0">自主平台</option>
                    <?php foreach($shoplist as $li): ?>
                    <option value="<?php echo htmlentities($li['id']); ?>" <?php if($param['shop_id']==$li['id']): ?>selected<?php endif; ?>>
                            (id:<?php echo htmlentities($li['id']); ?> |
                            <?php switch($li['role']): case "1": ?> 博物馆 <?php break; case "2": ?> 设计师 <?php break; case "3": ?> 工厂 <?php break; default: endswitch; ?> ) -
                            <?php if($li['role'] == 2): ?>
                                <?php echo htmlentities($li['nickname']); else: ?>
                                <?php echo htmlentities($li['org']); endif; ?>
                    </option>
                    <?php endforeach; ?>
				</select>
            </span>
            <input type="text" name="search" value="<?php echo htmlentities(app('request')->get('search')); ?>" id="search" placeholder=" 商品名" style="width:250px;height: 28px;line-height: 28px;border-radius: 4px" class="input-text">
            <button name="" id="search-btn" class="btn btn-success" style="height: 28px;" type="button"><i class="Hui-iconfont">&#xe665;</i> 查找商品</button>
        </div>
    </section>
    <div class="cl pd-5 bg-1 bk-gray mt-20">
        <span class="l">
            <a class="btn btn-primary radius" data-title="添加商品" data-href="article-add.html" onclick="pagefull('添加商品','<?php echo url("Shop/goodsAdd"); ?>')" href="javascript:;"><i class="Hui-iconfont">&#xe600;</i> 添加商品
            </a>
        </span>
        <span class="r">共有数据：<strong><?php echo htmlentities($page['count']); ?></strong> 条</span> </div>
    <table class="table table-border table-bordered table-bg">
        <thead>
        <tr class="text-c">
            <th width="25">#</th>
            <th width="50">排序</th>
            <th width="80">商品图片</th>
            <th width="80">商品价格</th>
            <th width="80">库存</th>
            <th width="80">销量</th>
            <th>商户名</th>
            <th width="120">上市日期</th>
            <th width="60">审核状态</th>
            <th width="60">审核</th>
            <th width="80">操作</th>
        </tr>
        </thead>
        <tbody>
        <form id="multi-check">
            <?php foreach($list as $li): ?>
            <tr class="text-c userinfo">
                <td rowspan="2"><?php echo htmlentities($li['id']); ?></td>
                <td><?php echo htmlentities($li['sort']); ?></td>
                <td>
                    <?php $image = unserialize($li['pics'])[0];?>
                    <div style="width: 80px;height: 80px;background-image: url('<?php echo config('qiniu_weburl');?><?php echo htmlentities($image); ?>');background-position: center;background-repeat: no-repeat;background-size: 100%"></div>
                </td>
                <td><?php echo htmlentities($li['price']); ?></td>
                <td><?php echo htmlentities($li['stock']); ?></td>
                <td><?php echo htmlentities($li['sales']); ?></td>
                <td>
                    <?php if($li['shop_id']): if($li['role'] == 2): ?>
                    <?php echo htmlentities($li['role_name']); else: ?>
                    <?php echo htmlentities($li['org']); endif; ?>
                    (
                    <?php switch($li['role']): case "1": ?> 博物馆 <?php break; case "2": ?> 设计师 <?php break; case "3": ?> 工厂 <?php break; default: endswitch; ?>
                    )
                    <?php else: ?>
                    自主平台
                    <?php endif; ?>
                </td>
                <td><?php echo htmlentities(date('Y年m月d日',!is_numeric($li['create_time'])? strtotime($li['create_time']) : $li['create_time'])); ?></td>
                <th class="td-check">
                    <?php switch($li['check']): case "0": ?><span class="label label-warning radius">待审核</span><?php break; case "1": ?> <span class="label label-success radius">已通过</span> <?php break; case "2": ?> <span class="label label-danger radius">未通过</span> <?php break; default: endswitch; ?>
                </th>
                <td>
                    <?php if($li['check'] == '0'): ?>
                    <a class="btn btn-link radius" style="text-decoration:none;" onClick="goods_shenhe(this,<?php echo htmlentities($li['id']); ?>)" href="javascript:;" title="审核">审核</a>
                    <?php else: ?>
                    <span class="btn btn-link radius"></span>
                    <?php endif; ?>
                </td>
                <td class="td-manage" rowspan="2">
                    <?php if($li['status'] == '1'): ?>
                    <a style="text-decoration:none" onClick="goods_hide(this,<?php echo htmlentities($li['id']); ?>)" href="javascript:;" title="下架">
                        <i class="Hui-iconfont">&#xe6de;</i>
                    </a>
                    <?php else: ?>
                    <a style="text-decoration:none" onClick="goods_show(this,<?php echo htmlentities($li['id']); ?>)" href="javascript:;" title="上架">
                        <i class="Hui-iconfont">&#xe603;</i>
                    </a>
                    <?php endif; ?>
                    <a style="text-decoration:none" onclick="pagefull('商品信息','<?php echo url("Shop/goodsDetail",array("id"=>$li["id"])); ?>')" class="ml-5" href="javascript:;" title="查看商品">
                    <i class="Hui-iconfont">&#xe6df;</i>
                    </a>
                </td>
            </tr>
            <tr class="text-c">
                <td colspan="9" class="text-l"><b>商品名称</b> : <?php echo htmlentities($li['name']); ?>
                    (
                    <span class="td-status">
                    <?php if($li['status'] == '1'): ?>
                    <span class="label label-success radius">已上架</span>
                    <?php endif; if($li['status'] == '0'): ?>
                    <span class="label label-defaunt radius">已下架</span>
                    <?php endif; ?>
                    </span>
                    )
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

    var url = '<?php echo url("Shop/goodsList"); ?>' + '?<?php echo $page["query"];?>';
    var curr = '<?php echo htmlentities($page['curr']); ?>',totalPage='<?php echo htmlentities($page['totalPage']); ?>';
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
        var shop_id = $("#formAjax").find("#shop_id").val();
        var search = $("#formAjax").find("#search").val();
        var id = $("#formAjax").find("#id").val();
        var str = 'id=' + id;
        if(shop_id.length != '') {
            str += '&shop_id=' + shop_id;
        }
        if(search.length != '') {
            str += '&search=' + search;
        }
        window.location.href = '<?php echo url("Shop/goodsList"); ?>' + '?' + str;
    });

    /*添加商品*/
    function pagefull(title,url) {
        var index = layer.open({
            type: 2,
            title: title,
            content: url,
            area:['100%','100%']
        });
        // layer.full(index);
    }

    /*下架*/
    function goods_hide(obj,id){
        layer.confirm('确认要下架吗？',function(index){
            $.ajax({
                url:"<?php echo url('Shop/goodsHide'); ?>",
                type:'post',
                dataType:'json',
                data:{id:id},
                success:function(data) {
                    console.log(data);
                    if(data.code == 1) {
                        $(obj).parents("tr").next().find(".td-status").html('<span class="label label-defaunt radius">已下架</span>');
                        $(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" onClick="goods_show(this,'+id+')" href="javascript:;" title="上架"><i class="Hui-iconfont">&#xe603;</i></a>');
                        $(obj).remove();
                        layer.msg('已下架!',{icon: 1,time:1000});
                    }else {
                        layer.msg(data.data,{icon:2,time:1000})
                    }
                },
                error:function(data) {
                    layer.msg('请求失败!',{icon:5,time:1000})
                },
            })

        });
    }
    /*上架*/
    function goods_show(obj,id){
        layer.confirm('确认要上架吗？',function(index){
            $.ajax({
                url:"<?php echo url('Shop/goodsShow'); ?>",
                type:'post',
                dataType:'json',
                data:{id:id},
                success:function(data) {
                    console.log(data);
                    if(data.code == 1) {
                        $(obj).parents("tr").next().find(".td-status").html('<span class="label label-success radius">已上架</span>');
                        $(obj).parents("tr").find(".td-manage").prepend('<a style="text-decoration:none" onClick="goods_hide(this,'+id+')" href="javascript:;" title="下架"><i class="Hui-iconfont">&#xe6de;</i></a>');
                        $(obj).remove();
                        layer.msg('已上架!',{icon: 1,time:1000});
                    }else {
                        layer.msg(data.data,{icon:2,time:1000})
                    }
                },
                error:function(data) {
                    layer.msg('请求失败!',{icon:5,time:1000})
                },
            })

        });
    }

    /*作品审核*/
    function goods_shenhe(obj,id){
        layer.confirm('审核商品？', {
                btn: ['通过','不通过','取消'],
                shade: false,
                closeBtn: 0
            },
            function(){
                $.ajax({
                    url:"<?php echo url('Shop/goodsPass'); ?>",
                    type:'post',
                    dataType:'json',
                    data:{id:id},
                    success:function(data) {
                        console.log(data)
                        if(data.code == 1) {
                            $(obj).parents("tr").find(".td-check").html('<span class="label label-success radius">已通过</span>');
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
                        url:"<?php echo url('Shop/goodsReject'); ?>",
                        type:'post',
                        dataType:'json',
                        data:{id:id,reason:reason},
                        success:function(data) {
                            console.log(data)
                            if(data.code == 1) {
                                $(obj).parents("tr").find(".td-check").html('<span class="label label-danger radius">未通过</span>');
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


    $(document).ready(function () {
        $('#shop_id').select2();
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
