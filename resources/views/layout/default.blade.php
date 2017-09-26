<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>@yield('title','和润万青父母学院')</title>
    <meta name="viewport" content="width=device-width, user-scalable=no">
    <meta name="format-detection" content="telephone=no" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="/css/style.css?2017092622">
    <script type="text/javascript" src="/js/jquery.min.js"></script>
    <script type="text/javascript" src="/js/public.js?2017092622"></script>
    @yield("style")
</head>
<body>
@yield("content")
<!--新增主页按钮开始-->
@if(request('from'))
<script>
if ($('#nav').length==0) {
    $("body").append('<a href="/vcourse" class="return_index" style="bottom:160px;right:23px;"></a>');
}
</script>
@endif
<!--新增主页按钮结束-->

<script>
    (function ($) {
        $.extend({
            Request: function (m) {
                var sValue = location.search.match(new RegExp("[\?\&]" + m + "=([^\&]*)(\&?)", "i"));
                return sValue ? sValue[1] : sValue;
            },
            UrlUpdateParams: function (url, name, value) {
                var r = url;
                if (r != null && r != 'undefined' && r != "") {
                    value = encodeURIComponent(value);
                    var reg = new RegExp("(^|)" + name + "=([^&]*)(|$)");
                    var tmp = name + "=" + value;
                    if (url.match(reg) != null) {
                        r = url.replace(eval(reg), tmp);
                    }
                    else {
                        if (url.match("[\?]")) {
                            r = url + "&" + tmp;
                        } else {
                            r = url + "?" + tmp;
                        }
                    }
                }
                return r;
            }
        });
    })(jQuery);
</script>
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js" type="text/javascript" charset="utf-8"></script>
<script>
    $(document).ready(function () {
        wx.config(<?php echo $wx_js->config(array("onMenuShareAppMessage", "onMenuShareTimeline"), false) ?>);
        wx.ready(function () {
            wx.onMenuShareAppMessage({
                title: '和润万青-让教育孩子变得简单', // 分享标题
                desc: '学习家庭教育，做中国好父母', // 分享描述
                //link: '{{route('course')}}?from=singlemessage', // 分享链接
                link: $.UrlUpdateParams(window.location.href, "from", 'singlemessage'), // 分享链接
                imgUrl: '{{url('/images/my/my_about_us_img.png')}}', // 分享图标
                type: '', // 分享类型,music、video或link，不填默认为link
                dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                success: function () {
                    // 用户确认分享后执行的回调函数
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });
            wx.onMenuShareTimeline({
                title: '和润万青-让教育孩子变得简单', // 分享标题
                link: $.UrlUpdateParams(window.location.href, "from", 'singlemessage'), // 分享链接
                imgUrl: '{{url('/images/my/my_about_us_img.png')}}', // 分享图标
                success: function () {
                    // 用户确认分享后执行的回调函数
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });
        });
    });
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>
@yield("script")
</body>
</html>
