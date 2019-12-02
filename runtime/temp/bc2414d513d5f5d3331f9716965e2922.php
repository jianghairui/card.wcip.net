<?php /*a:4:{s:63:"/var/www/caves.wcip.net/application/admin/view/index/index.html";i:1571129594;s:65:"/var/www/caves.wcip.net/application/admin/view/public/header.html";i:1566808376;s:64:"/var/www/caves.wcip.net/application/admin/view/public/aside.html";i:1574402862;s:65:"/var/www/caves.wcip.net/application/admin/view/public/footer.html";i:1566381559;}*/ ?>

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
    <script src="/lib/echarts/echarts.min.js"></script>
    <title>山海文化管理系统</title>
</head>
<body>
<header class="navbar-wrapper">
    <div class="navbar navbar-fixed-top">
        <div class="container-fluid cl"> <a class="logo navbar-logo f-l mr-10 hidden-xs" href="<?php echo url('Index/index'); ?>">山洞小程序后台</a>
            <span class="logo navbar-slogan f-l mr-10 hidden-xs">v1.1</span>
            <a aria-hidden="false" class="nav-toggle Hui-iconfont visible-xs" href="javascript:;">&#xe667;</a>
            <nav id="Hui-userbar" class="nav navbar-nav navbar-userbar hidden-xs">
                <ul class="cl">
                    <li>管理员&nbsp;&nbsp;</li>
                    <li class="dropDown dropDown_hover">
                        <a href="#" class="dropDown_A"><?php echo htmlentities(app('session')->get('realname')); ?> <i class="Hui-iconfont">&#xe6d5;</i></a>
                        <ul class="dropDown-menu menu radius box-shadow">
                            <li><a href="javascript:;" onClick="member_add('个人信息','<?php echo url("Login/personal"); ?>','800','500')">个人信息</a></li>
                            <li><a href="<?php echo url('Login/logout'); ?>">退出</a></li>
                        </ul>
                    </li>
                    <!--<li id="Hui-msg"> <a href="#" title="消息"><span class="badge badge-danger">1</span><i class="Hui-iconfont" style="font-size:18px">&#xe68a;</i></a> </li>-->
                    <li id="Hui-skin" class="dropDown right dropDown_hover"> <a href="javascript:;" class="dropDown_A" title="换肤"><i class="Hui-iconfont" style="font-size:18px">&#xe62a;</i></a>
                        <ul class="dropDown-menu menu radius box-shadow">
                            <li><a href="javascript:;" data-val="default" title="默认（黑色）">默认（黑色）</a></li>
                            <li><a href="javascript:;" data-val="blue" title="蓝色">蓝色</a></li>
                            <li><a href="javascript:;" data-val="green" title="绿色">绿色</a></li>
                            <li><a href="javascript:;" data-val="red" title="红色">红色</a></li>
                            <li><a href="javascript:;" data-val="yellow" title="黄色">黄色</a></li>
                            <li><a href="javascript:;" data-val="orange" title="橙色">橙色</a></li>
                        </ul>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</header>
<aside class="Hui-aside">
    <div class="menu_dropdown bk_2">
    <?php if(session('username')===config('superman')): ?>
        <dl id="menu-banner">
            <dt><i class="Hui-iconfont">&#xe625;</i> 首页设置<i class="Hui-iconfont menu_dropdown-arrow">&#xe6d5;</i></dt>
            <dd>
                <ul>
                    <li><a data-href="<?php echo url('Banner/slideList'); ?>" data-title="轮播图" href="javascript:;">轮播图设置</a></li>
                    <li><a data-href="<?php echo url('Banner/videoList'); ?>" data-title="宣传视频" href="javascript:;">宣传视频</a></li>
                </ul>
            </dd>
        </dl>
        <dl id="menu-activity">
            <dt><i class="Hui-iconfont">&#xe70a;</i> 活动管理<i class="Hui-iconfont menu_dropdown-arrow">&#xe6d5;</i></dt>
            <dd>
                <ul>
                    <li><a data-href="<?php echo url('Req/reqList'); ?>" data-title="活动列表" href="javascript:void(0)">活动列表</a></li>
                    <li><a data-href="<?php echo url('Req/workList'); ?>" data-title="设计作品" href="javascript:void(0)">设计作品</a></li>
                    <li><a data-href="<?php echo url('Req/ideaList'); ?>" data-title="用户创意" href="javascript:void(0)">用户创意</a></li>
                    <li><a data-href="<?php echo url('Req/showWorkList'); ?>" data-title="展示作品" href="javascript:void(0)">展示作品</a></li>
                </ul>
            </dd>
        </dl>
        <dl id="menu-funding">
            <dt><i class="Hui-iconfont">&#xe63a;</i> 众筹管理<i class="Hui-iconfont menu_dropdown-arrow">&#xe6d5;</i></dt>
            <dd>
                <ul>
                    <li><a data-href="<?php echo url('Funding/fundingList'); ?>" data-title="众筹管理" href="javascript:void(0)">众筹管理</a></li>
                    <li><a data-href="<?php echo url('Funding/goodsList'); ?>" data-title="回报商品" href="javascript:void(0)">回报商品</a></li>
                    <li><a data-href="<?php echo url('Funding/orderList'); ?>" data-title="众筹订单" href="javascript:void(0)">众筹订单</a></li>
                </ul>
            </dd>
        </dl>
        <dl id="menu-note">
            <dt><i class="Hui-iconfont">&#xe70c;</i> 笔记管理<i class="Hui-iconfont menu_dropdown-arrow">&#xe6d5;</i></dt>
            <dd>
                <ul>
                    <li><a data-href="<?php echo url('Note/noteList'); ?>" data-title="笔记列表" href="javascript:void(0)">笔记列表</a></li>
                </ul>
            </dd>
        </dl>
        <dl id="menu-xuqiu">
            <dt><i class="Hui-iconfont">&#xe639;</i> 即时需求<i class="Hui-iconfont menu_dropdown-arrow">&#xe6d5;</i></dt>
            <dd>
                <ul>
                    <li><a data-href="<?php echo url('Xuqiu/xuqiuList'); ?>" data-title="即时需求" href="javascript:void(0)">即时需求</a></li>
                </ul>
            </dd>
        </dl>
        <dl id="menu-member">
            <dt><i class="Hui-iconfont">&#xe60d;</i> 会员管理<i class="Hui-iconfont menu_dropdown-arrow">&#xe6d5;</i></dt>
            <dd>
                <ul>
                    <li><a data-href="<?php echo url('User/userList'); ?>" data-title="会员列表" href="javascript:void(0);">会员列表</a></li>
                    <li><a data-href="<?php echo url('User/rechargeList'); ?>" data-title="充值记录" href="javascript:void(0);">充值记录</a></li>
                    <li><a data-href="<?php echo url('User/role3OrderList'); ?>" data-title="工厂套餐" href="javascript:void(0);">工厂套餐</a></li>
                    <li><a data-href="<?php echo url('User/vipList'); ?>" data-title="充值设置" href="javascript:void(0);">充值设置</a></li>
                    <li><a data-href="<?php echo url('User/signList'); ?>" data-title="签到记录" href="javascript:void(0);">签到记录</a></li>
                </ul>
            </dd>
        </dl>
        <dl id="menu-shop">
            <dt><i class="Hui-iconfont">&#xe66a;</i> 商城管理<i class="Hui-iconfont menu_dropdown-arrow">&#xe6d5;</i></dt>
            <dd>
                <ul>
                    <li><a data-href="<?php echo url('Shop/goodsList'); ?>" data-title="商品列表" href="javascript:void(0);">商品列表</a></li>
                    <li><a data-href="<?php echo url('Shop/cateList'); ?>" data-title="商品分类" href="javascript:void(0);">商品分类</a></li>
                    <li><a data-href="<?php echo url('Shop/orderList'); ?>" data-title="订单管理" href="javascript:void(0);">订单管理</a></li>
                </ul>
            </dd>
        </dl>
        <dl id="menu-admin">
            <dt><i class="Hui-iconfont">&#xe62d;</i> 管理员管理<i class="Hui-iconfont menu_dropdown-arrow">&#xe6d5;</i></dt>
            <dd>
                <ul>
                    <li><a data-href="<?php echo url('Admin/adminlist'); ?>" data-title="管理员列表" href="javascript:void(0)">管理员列表</a></li>
                    <li><a data-href="<?php echo url('Admin/grouplist'); ?>" data-title="角色管理" href="javascript:void(0)">角色管理</a></li>
                    <li><a data-href="<?php echo url('Admin/rulelist'); ?>" data-title="权限管理" href="javascript:void(0)">权限管理</a></li>
                </ul>
            </dd>
        </dl>
        <dl id="menu-system">
            <dt><i class="Hui-iconfont">&#xe62e;</i> 系统管理<i class="Hui-iconfont menu_dropdown-arrow">&#xe6d5;</i></dt>
            <dd>
                <ul>
                    <!--<li><a data-href="<?php echo url('System/setting'); ?>" data-title="系统设置" href="javascript:void(0)">系统设置</a></li>-->
                    <li><a data-href="<?php echo url('System/syslog'); ?>" data-title="系统日志" href="javascript:void(0)">系统日志</a></li>
                </ul>
            </dd>
        </dl>
        <dl id="menu-fake">
            <dt><i class="Hui-iconfont">&#xe6f9;</i> 假数据<i class="Hui-iconfont menu_dropdown-arrow">&#xe6d5;</i></dt>
            <dd>
                <ul>
                    <li><a data-href="<?php echo url('Fake/roleList'); ?>" data-title="假数据" href="javascript:void(0)">假数据</a></li>
                    <li><a data-href="<?php echo url('Fake/worksList'); ?>" data-title="参赛作品" href="javascript:void(0)">参赛作品</a></li>
                </ul>
            </dd>
        </dl>
        <dl id="menu-tmpac">
            <dt><i class="Hui-iconfont">&#xe6b5;</i> 临时活动<i class="Hui-iconfont menu_dropdown-arrow">&#xe6d5;</i></dt>
            <dd>
                <ul>
                    <li><a data-href="<?php echo url('Tmp/acDetail'); ?>" data-title="临时活动" href="javascript:void(0)">临时活动</a></li>
                    <li><a data-href="<?php echo url('Tmp/joinList'); ?>" data-title="参加记录" href="javascript:void(0)">参加记录</a></li>
                </ul>
            </dd>
        </dl>

    <?php else: ?>

        <dl id="member">
            <dt><i class="Hui-iconfont">&#xe60d;</i> 会员管理<i class="Hui-iconfont menu_dropdown-arrow">&#xe6d5;</i></dt>
            <dd>
                <ul>
                    <li><a data-href="<?php echo url('User/userList'); ?>" data-title="会员列表" href="javascript:void(0);">会员列表</a></li>
                </ul>
            </dd>
        </dl>

    <?php endif; ?>

    </div>
</aside>

<div class="dislpayArrow hidden-xs"><a class="pngfix" href="javascript:void(0);" onClick="displaynavbar(this)"></a></div>
<section class="Hui-article-box">
    <div id="Hui-tabNav" class="Hui-tabNav hidden-xs">
        <div class="Hui-tabNav-wp">
            <ul id="min_title_list" class="acrossTab cl">
                <li class="active">
                    <span title="我的桌面" data-href="welcome.html">我的桌面</span>
                    <em></em></li>
            </ul>
        </div>
        <div class="Hui-tabNav-more btn-group"><a id="js-tabNav-prev" class="btn radius btn-default size-S" href="javascript:;"><i class="Hui-iconfont">&#xe6d4;</i></a><a id="js-tabNav-next" class="btn radius btn-default size-S" href="javascript:;"><i class="Hui-iconfont">&#xe6d7;</i></a></div>
    </div>
    <div id="iframe_box" class="Hui-article">
        <div class="show_iframe">
            <div class="page-container">
                <div class="row">
                    <div id="echart-1" class="col-sm-6" style="height: 400px"></div>
                    <div id="echart-2" class="col-sm-6" style="height: 400px"></div>
                    <div id="echart-3" class="col-sm-6" style="height: 400px"></div>
                </div>
                <!-- 为ECharts准备一个具备大小（宽高）的Dom -->

                <script type="text/javascript">
                    // 基于准备好的dom，初始化echarts实例
                    var echart_1 = echarts.init(document.getElementById('echart-1'));
                    var echart_2 = echarts.init(document.getElementById('echart-2'));
                    var echart_3 = echarts.init(document.getElementById('echart-3'));

                    // 指定图表的配置项和数据
                    var option1 = {
                        title : {
                            text: '商城订单统计',
                            subtext: '年度统计'
                        },
                        tooltip : {
                            trigger: 'axis'
                        },
                        legend: {
                            data:['自主品台','商户订单']
                        },
                        toolbox: {
                            show : true,
                            feature : {
                                dataView : {show: true, readOnly: false},
                                magicType : {show: true, type: ['line', 'bar']},
                                restore : {show: true},
                                saveAsImage : {show: true}
                            }
                        },
                        calculable : true,
                        xAxis : [
                            {
                                type : 'category',
                                data : ['1月','2月','3月','4月','5月','6月','7月','8月','9月','10月','11月','12月']
                            }
                        ],
                        yAxis : [
                            {
                                type : 'value'
                            }
                        ],
                        series : [
                            {
                                name:'自主品台',
                                type:'bar',
                                data:[20, 49, 70, 232, 256, 767, 1356, 1622, 326, 200, 64, 33]
                            },
                            {
                                name:'商户订单',
                                type:'bar',
                                data:[26, 59, 90, 264, 287, 707, 1756, 1820, 487, 188, 60, 23]
                            }
                        ]
                    };


                    var option2 = {
                        title : {
                            text: '用户数量统计',
                            subtext: 'Total : 1500',
                            x:'center'
                        },
                        tooltip : {
                            trigger: 'item',
                            formatter: "{a} <br/>{b} : {c} ({d}%)"
                        },
                        legend: {
                            orient: 'vertical',
                            left: 'left',
                            data: ['博物馆','文创机构','工厂','普通用户']
                        },
                        series : [
                            {
                                name: '访问来源',
                                type: 'pie',
                                radius : '55%',
                                center: ['50%', '60%'],
                                data:[
                                    {value:15, name:'博物馆'},
                                    {value:149, name:'文创机构'},
                                    {value:78, name:'工厂'},
                                    {value:987, name:'普通用户'}
                                ],
                                itemStyle: {
                                    emphasis: {
                                        shadowBlur: 10,
                                        shadowOffsetX: 0,
                                        shadowColor: 'rgba(0, 0, 0, 0.5)'
                                    }
                                }
                            }
                        ]
                    };


                    var option3 = {
                        title : {
                            text: '众筹订单统计',
                            subtext: '年度统计'
                        },
                        tooltip : {
                            trigger: 'axis'
                        },
                        legend: {
                            data:['有偿众筹','无偿众筹']
                        },
                        toolbox: {
                            show : true,
                            feature : {
                                dataView : {show: true, readOnly: false},
                                magicType : {show: true, type: ['line', 'bar']},
                                restore : {show: true},
                                saveAsImage : {show: true}
                            }
                        },
                        calculable : true,
                        xAxis : [
                            {
                                type : 'category',
                                data : ['1月','2月','3月','4月','5月','6月','7月','8月','9月','10月','11月','12月']
                            }
                        ],
                        yAxis : [
                            {
                                type : 'value'
                            }
                        ],
                        series : [
                            {
                                name:'有偿众筹',
                                type:'line',
                                data:[420, 490, 538, 556, 612, 767, 899, 1080, 1446, 1550, 1920, 2138]
                            },
                            {
                                name:'无偿众筹',
                                type:'line',
                                data:[26, 59, 90, 264, 287, 308, 156, 120, 256, 188, 60, 23]
                            }
                        ]
                    };

                    // 使用刚指定的配置项和数据显示图表。
                    echart_1.setOption(option1);
                    echart_2.setOption(option2);
                    echart_3.setOption(option3);
                </script>
            </div>
        </div>
    </div>
</section>
<div class="contextMenu" id="Huiadminmenu">
    <ul>
        <li id="closethis">关闭当前 </li>
        <li id="closeall">关闭全部 </li>
    </ul>
</div>
<!--_footer 作为公共模版分离出去-->
<script type="text/javascript" src="/lib/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript" src="/lib/layer/2.4/layer.js"></script>
<script type="text/javascript" src="/static/h-ui/js/H-ui.min.js"></script>
<script type="text/javascript" src="/static/h-ui.admin/js/H-ui.admin.js"></script>
<!--/_footer 作为公共模版分离出去-->

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

    /*资讯-添加*/
    function article_add(title,url){
        var index = layer.open({
            type: 2,
            title: title,
            content: url
        });
        layer.full(index);
    }
    /*用户-添加*/
    function member_add(title,url,w,h){
        layer_show(title,url,w,h);
    }


</script>
</body>
</html>