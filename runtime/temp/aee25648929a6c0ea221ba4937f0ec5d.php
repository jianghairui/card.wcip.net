<?php /*a:1:{s:60:"/var/www/caves.wcip.net/application/api/view/sign/index.html";i:1570796471;}*/ ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>信息登记</title>
  <script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.4.0.js"></script>
  <link rel="stylesheet" href="/static/src/swiper/swiper.min.css">
  <style>
    * {
      margin: 0;
      padding: 0;
    }

    html, body {
      position: relative;
      height: 100%;
    }

    body {
      font-family: Helvetica Neue, Helvetica, Arial, sans-serif;
      font-size: 14px;
      color: #000;
      margin: 0;
      padding: 0;
    }

    img {
      width: 100%;
      display: block;
    }

    .ping-jin img {
      width: 100%;
      display: block;
    }

    .ping-jin .img-box {
      width: 100%;
    }

    .ping-jin .swiper-box {
      margin: 10px;
    }

    .ping-jin .swiper-container {
      width: 100%;
      height: 300px;
      margin-left: auto;
      margin-right: auto;
    }

    .ping-jin .swiper-slide {
      background-size: 100%;
      background-repeat: no-repeat;
      background-position: center;
    }

    .ping-jin .gallery-top {
      height: 150px;
      width: 100%;
      border-radius: 5px;
    }

    .ping-jin .gallery-thumbs {
      height: 65px;
      box-sizing: border-box;
      padding: 10px 0;
    }

    .ping-jin .gallery-thumbs .swiper-slide {
      width: 25%;
      height: 100%;
      opacity: 0.4;
      border-radius: 5px;
    }

    .ping-jin .gallery-thumbs .swiper-slide-thumb-active {
      opacity: 1;
    }

    .submit-box {
      background: linear-gradient(to bottom right, #1435a6, #e08071);
      overflow: hidden;
    }

    .header {
      height: 30px;
      line-height: 30px;
      margin-top: 20px;
      text-align: center;
      width: 100%;
      font-size: 24px;
      color: #ffffff;
    }

    .content .cont {
      overflow: hidden;
      /*border-top: 1px solid #ff7e00;*/
      margin: 12px 9px;
      /*background-color: #ffffff;*/
      /*box-shadow: 0 1px 35px 0 rgba(213, 213, 213, 0.75);*/
    }

    .ipt-box {
      margin: 0 15px 30px;
    }

    .ipt-box label {
      font-weight: bold;
      font-size: 16px;
      line-height: 21px;
      margin: 15px 0;
      color: #fff;
    }

    .ipt-box .input {
      margin-top: 10px;
      height: 30px;
      background-color: #efefef;
      border-radius: 2px;
      border: solid 1px #aaa;
      overflow: hidden;
    }

    .ipt-box .input .smoll-box {
      height: 100%;
      margin: 0 10px;
    }

    .ipt-box .input .smoll-box input {
      display: block;
      font-size: 16px;
      width: 100%;
      height: 100%;
      border: none;
      outline: none;
      background: unset;
    }

    .ipt-box .recommend-people {
      height: auto;
      overflow: unset;
      border: none;
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
      align-items: center;
    }

    .ipt-box .recommend-people .people {
      width: 31%;
      height: 100%;
      margin-top: 15px;
      text-align: center;
      line-height: 40px;
      background-color: #cccccc;
      border-radius: 2px;
      font-size: 16px;
      color: #ffffff;
    }

    .ipt-box .recommend-people .on {
      background-color: #ff7e00;
    }

    .separator {
      width: 100%;
    }

    .separator img {
      width: 100%;
      display: block;
    }

    .bottom-cont .address {
      border: none;
      height: 30px;
      line-height: 30px;
      border-bottom: 1px solid #ccc;
      font-size: 14px;
      color: #999999;
    }

    .bottom-cont .company-brief {
      text-indent: 2em;
      text-align: justify;
      overflow: unset;
      height: auto;
      padding-bottom: 10px;
    }

    .btn-box {
      background-color: #1b284a;
      border-radius: 40px;
      margin: 45px 15px 30px;
      height: 40px;
      line-height: 40px;
      text-align: center;
      color: #ffffff;
    }
  </style>
</head>
<body>
<script type="text/javascript">
  wx.config({
    debug: false,
    appId: '<?php echo htmlentities($data["appId"]); ?>',
    timestamp: '<?php echo htmlentities($data["timestamp"]); ?>',
    nonceStr: '<?php echo htmlentities($data["nonceStr"]); ?>',
    signature: '<?php echo htmlentities($data["signature"]); ?>',
    jsApiList: [
      'checkJsApi',
      'onMenuShareTimeline',
      'onMenuShareAppMessage'
    ]
  });
  wx.ready(function () {
    wx.onMenuShareTimeline({
      title: '<?php echo htmlentities($share_data['title']); ?>', // 分享标题
      desc: '<?php echo htmlentities($share_data['desc']); ?>',
      link: '<?php echo htmlentities($share_data['link']); ?>', // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
      imgUrl: '<?php echo htmlentities($share_data['imgUrl']); ?>', // 分享图标
      success: function () {
        // 用户点击了分享后执行的回调函数
        //alert('分享成功')
      }
    });
    wx.onMenuShareAppMessage({
      title: '<?php echo htmlentities($share_data['title']); ?>', // 分享标题
      desc: '<?php echo htmlentities($share_data['desc']); ?>', // 分享描述
      link: '<?php echo htmlentities($share_data['link']); ?>', // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
      imgUrl: '<?php echo htmlentities($share_data['imgUrl']); ?>', // 分享图标
      type: '', // 分享类型,music、video或link，不填默认为link
      dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
      success: function () {
        //alert('分享成功')
      }
    });

  });

</script>
<div class="ping-jin">
  <div class="img-box">
    <img src="/static/src/image/pingjin/1.png"/>
  </div>
  <div class="swiper-box">
    <div class="swiper-container gallery-top">
      <div class="swiper-wrapper">
        <div class="swiper-slide" style="background-image:url('/static/src/image/pingjin/img1.png')"></div>
        <div class="swiper-slide" style="background-image:url('/static/src/image/pingjin/img2.png')"></div>
        <div class="swiper-slide" style="background-image:url('/static/src/image/pingjin/img3.png')"></div>
      </div>
    </div>
    <div class="swiper-container gallery-thumbs">
      <div class="swiper-wrapper">
        <div class="swiper-slide" style="background-image:url('/static/src/image/pingjin/img1.png')"></div>
        <div class="swiper-slide" style="background-image:url('/static/src/image/pingjin/img2.png')"></div>
        <div class="swiper-slide" style="background-image:url('/static/src/image/pingjin/img3.png')"></div>
      </div>
    </div>
  </div>
  <div class="img-box">
    <img src="/static/src/image/pingjin/2.png"/>
  </div>
</div>
<div class="submit-box">
  <div class="header">信息登记</div>
  <div class="content">

    <div class="cont">
      <form id="formAjax">
        <div class="ipt-box">
          <label for="company">参会单位名称</label>
          <div class="input">
            <div class="smoll-box">
              <input id="company" type="text" name="company" value="" maxlength="20" >
            </div>
          </div>
        </div>
        <div class="ipt-box">
          <label for="busine">经营范围</label>
          <div class="input">
            <div class="smoll-box">
              <input id="busine" type="text" name="busine" value="" maxlength="255" >
            </div>
          </div>
        </div>
        <div class="ipt-box">
          <label for="name">参会代表</label>
          <div class="input">
            <div class="smoll-box">
              <input id="name" type="text" name="name" value="" maxlength="20"  οninput="this.value.length>5?this.value=this.value.slice(0,5)" />
            </div>
          </div>
        </div>
        <div class="ipt-box">
          <label for="tel">参会人数</label>
          <div class="input">
            <div class="smoll-box">
              <input id="num" type="number" name="num" oninput="this.value.length>3?this.value=this.value.slice(0,3):this.value" />
            </div>
          </div>
        </div>
        <div class="ipt-box">
          <label for="tel">联系方式</label>
          <div class="input">
            <div class="smoll-box">
              <input id="tel" type="text" name="tel" value="" maxlength="15" >
            </div>
          </div>
        </div>
        <button type="submit" name="提交信息" id="submit" style="display: none"></button>
        <div class="btn-box" onclick="$('#submit').click()">提交信息</div>
      </form>
    </div>
  </div>
</div>
<!--<div class="img-box">-->
  <!--<img src="/static/src/image/pingjin/shwh.png" alt="山海文化有限公司">-->
<!--</div>-->

<!--_footer 作为公共模版分离出去-->
<script type="text/javascript" src="/lib/jquery/1.9.1/jquery.min.js"></script>
<script type="text/javascript" src="/lib/layer/2.4/layer.js"></script>
<script type="text/javascript" src="/static/h-ui/js/H-ui.min.js"></script>
<script type="text/javascript" src="/static/h-ui.admin/js/H-ui.admin.js"></script>

<script type="text/javascript" src="/lib/jquery.validation/1.14.0/jquery.validate.js"></script>
<script type="text/javascript" src="/lib/jquery.validation/1.14.0/validate-methods.js"></script>
<script type="text/javascript" src="/lib/jquery.validation/1.14.0/messages_zh.js"></script>
<script type="text/javascript" src="/static/src/swiper/swiper.min.js"></script>

<!--/_footer 作为公共模版分离出去-->
<!--请在下方写此页面业务相关的脚本-->
<script type="text/javascript">

  $(function () {
    var galleryThumbs = new Swiper('.gallery-thumbs', {
      spaceBetween: 10,
      slidesPerView: 3,
      freeMode: true,
      watchSlidesVisibility: true,
      watchSlidesProgress: true,
    });
    var galleryTop = new Swiper('.gallery-top', {
      spaceBetween: 10,
      autoplay: {
        delay: 3000,
        stopOnLastSlide: false,
        disableOnInteraction: true,
      },
      navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
      },
      thumbs: {
        swiper: galleryThumbs
      }
    });


    var isclick = true;
    var rule_tel = /^1[34578]\d{9}$/;

    $("#formAjax").validate({
      rules: {
        company: {
          required: true,
          maxlength: 20
        },
        busine: {
          required: true,
          maxlength: 255
        },
        name: {
          required: true,
          maxlength: 20
        },
        tel: {
          required: true,
          maxlength: 15,
        },
        num: {
          required: true,
          maxlength: 3
        }
      },
      onkeyup: false,
      focusCleanup: true,
      success: "valid",
      submitHandler: function (form) {
        if (!rule_tel.test($("#tel").val())) {
          layer.msg('无效的手机号', { time: 1000 }, function () {
            isclick = true;
          });
          return;
        }
        if (isclick === true) {
          isclick = false;
          $(form).ajaxSubmit({
            type: 'post',
            url: "<?php echo url('Sign/index'); ?>",
            success: function (data) {
              // console.log(data);isclick = true;return;
              // alert(JSON.stringify(data));
              if (data.code == 1) {
                layer.msg('提交成功!', { icon: 1, time: 1000 }, function () {
                  window.location.href = window.location.href + "?" + 10000 * Math.random();
                  isclick = true;
                });
              } else {
                layer.msg(data.data, { icon: 2, time: 1000 });
                isclick = true
              }
            },
            error: function (XmlHttpRequest, textStatus, errorThrown) {
              layer.msg('接口请求错误!', { icon: 5, time: 1000 });
              isclick = true
            }
          });
        }
      }
    });



  })

</script>


</body>
</html>
