@extends('layout.default')
@section('content')
<div id="subject">
    <div id="main">
        <div class="invite_register">
            <div class="ir_banner"><img src="/images/other/ir_banner.jpg" alt=""/></div>
            <dl class="ir_dl">
                <dt>活动规则</dt>
                <dd>1.发送推荐注册链接，好友点击链接成功注册以后，即可在我的优惠券查看该红包。</dd>
                <dd>2.好友首次购买爱中管教根基课，您可获得100元现金红包，可在我的收益中查看。</dd>
                <dd>3.如果发现任何作弊行为，本站将取消用户获得奖励的资格。</dd>
                <dd>4.活动解释权归本站所有，有疑问请联系和润万青客服400-6363-555</dd>
            </dl>
        </div>
    </div>
</div>
@endsection
@section('script')
    <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js" type="text/javascript" charset="utf-8"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $("body").css("background","#fff");
            wx.config(<?php echo $wx_js->config(array("onMenuShareAppMessage"),false) ?>);
            wx.ready(function(){
                    wx.onMenuShareAppMessage({
                    title: '送你100元家庭教育爱心基金！科学教子，幸福每一家', // 分享标题
                    desc: '和润万青，让教育孩子变得简单', // 分享描述
                    link: '{{route("my.invited_user")}}?invite_user={{$user_id}}&from=singlemessage', // 分享链接
                    imgUrl: '{{url("/images/look/red_div_bg1.png")}}', // 分享图标
                    type: '', // 分享类型,music、video或link，不填默认为link
                    dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                    success: function () { 
                        // 用户确认分享后执行的回调函数
                    },
                    cancel: function () { 
                        // 用户取消分享后执行的回调函数
                    }
                });
            });
        });
    </script>
@endsection