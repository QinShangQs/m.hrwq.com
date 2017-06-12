@extends('layout.default')
@section('content')
<input type="hidden" name="requestUri" id="requestUri" value="{{$requestUri}}">
    <div id="subject">
        <div id="main">
            <div class="my">
                <div class="my_data_personal">
                    <div class="mdp_top">
                        <div class="mdp_top_img"><img src="{{asset($data['profileIcon'])}}" alt=""/></div>
                        <div class="mdp_top_name">{{$data['realname'] or $data['nickname']}}</div>
                    </div>
                    <ul class="mmo_list">
                        <li><a href="{{route('article',['id'=>6])}}"><span><img src="/images/public/select_right.jpg" alt=""/></span>和会员介绍</a></li>
                        <li><a href="#" id="vip_open"><span><img src="/images/public/select_right.jpg" alt=""/></span>开通会员</a></li>
                        <li><a id="vip_card" href="{{route('vip.active')}}"><span><img src="/images/public/select_right.jpg" alt=""/></span>激活会员卡</a></li>
                        <li><a href="{{route('vip.records')}}" ><span><img src="/images/public/select_right.jpg" alt=""/></span>会员状态</a></li>
                    </ul>
                </div>
            </div>
            @include('element.nav', ['selected_item' => 'nav5'])
        </div>
    </div>
@endsection
@section('script')
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js" type="text/javascript" charset="utf-8"></script>
<script>
$(document).ready(function () {
    wx.config(<?php echo $wx_js->config(array("onMenuShareAppMessage", "onMenuShareTimeline"), false) ?>);
    wx.ready(function () {
        wx.onMenuShareAppMessage({
            title: '哇！父母原来应该这样做', // 分享标题
            desc: '和润万青，让教育孩子变得简单', // 分享描述
            link: '{{route('vip')}}?from=singlemessage', // 分享链接
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
            title: '哇！父母原来应该这样做', // 分享标题
            link: '{{route('vip')}}?from=singlemessage', // 分享链接
            imgUrl: '{{url('/images/my/my_about_us_img.png')}}', // 分享图标
            success: function () {
                // 用户确认分享后执行的回调函数
            },
            cancel: function () {
                // 用户取消分享后执行的回调函数
            }
        });
    });
    //向ta提问
    $("#vip_open").click(function(e){
        e.preventDefault();
        var session_mobile = '{{$session_mobile}}';
        if (session_mobile == '') {
            //ajax成功返回事件开始
            //如果信息不完善执行开始（2选1）
            Popup.init({
                popHtml:'<p>您尚未注册，请先完成注册。</p>',
                popOkButton:{
                    buttonDisplay:true,
                    buttonName:"去注册",
                    buttonfunction:function(){
                        //此处填写信息不完善的时候的跳转信息
                        var requestUri = $('#requestUri').val();
                        window.location.href='/user/login?url='+requestUri;
                    }
                },
                popCancelButton:{
                    buttonDisplay:true,
                    buttonName:"否",
                    buttonfunction:function(){}
                },
                popFlash:{
                    flashSwitch:false
                }
            });
            return false;
            //如果信息不完善执行结束
        }else{
            @if($order)
                location.href="{{route('wechat.vip_pay')}}";
            @else
                location.href="{{route('vip.buy')}}";
            @endif
        }
    });

    @if ($is_guest)
        $('#vip_card').click(function() {
            var url = $(this).attr('href');
            Popup.init({
                popHtml:'<p>您尚未注册，请先完成注册。</p>',
                popOkButton:{
                    buttonDisplay:true,
                    buttonName:"去注册",
                    buttonfunction:function(){
                        window.location.href='/user/login?url='+ encodeURIComponent(url);
                        return false;
                    }
                },
                popCancelButton:{
                    buttonDisplay:true,
                    buttonName:"否",
                    buttonfunction:function(){}
                },
                popFlash:{
                    flashSwitch:false
                }
            });
            return false;
        });
    @endif
});
</script>
@endsection
