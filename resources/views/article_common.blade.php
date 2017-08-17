@extends('layout.default')
@section('style')
<style type="text/css">
    .mbtd_button input { width: 47%; }
</style>
@endsection
@section('content')
    <div id="subject">
        <div id="main">
            <div class="my">
            	@if($article->type==6)
            		<div class="my-intro-user" >
            			<div >和会员有效期</div>
                        <div class="period" >
                            <span class="day">{{ computer_vip_left_day($user['vip_left_day']) }}</span><span>天</span>
                            @if($pointVipCount > 0)
	                            <div class="hot">
	                            	  <a href="{{route('vip.records')}}">
	                            	  	<span class="link">会员动态
	                            	  		<li></li>
	                            	  	</span>
	                            	  </a>
	                            </div>
	                        @endif
                        </div>
            		</div>
            	@endif
            
                <article class="my_integral_introduce" style="padding-left:0;padding-right:0">
                    {!! $article->content !!}
                </article>
            </div>
            @if($article->type==6)
            <input type="hidden" name="requestUri" id="requestUri" value="{{$requestUri}}">
                <div class="mbtd_button" style="position:static;">
                    <a href="#" id="vip_open"><input type="button" class="mbtd_button" value="{{$user['vip_flg'] == 1 ? '开通会员':'续费会员'}}" style="width: 95%;background-color: #ed6d11"></a>
                    <div style="height:1rem"></div>
                    <a href="{{route('vip.active')}}" 
                    style="font-size: 1rem;color: #666666;text-decoration: underline; font-style: italic;">已有会员卡用户请点击此激活</a>  
                 </div>
            @endif
        </div>
    </div>
@endsection
@section('script')

@if($article->type==6)
<script>
			$(document).ready(function () {
				wx.config(<?php echo $wx_js->config(array("onMenuShareAppMessage", "onMenuShareTimeline"), false) ?>);
					wx.ready(function () {
				            wx.onMenuShareAppMessage({
				                title: '365天，和全国精英家长一起，成为更懂教育的父母', // 分享标题
				                desc: '我们穷尽一生的时间爱孩子，却很少关注自身的提升', // 分享描述
				                //link: '{{route('course')}}?from=singlemessage', // 分享链接
				                link: $.UrlUpdateParams(window.location.href, "from", 'singlemessage'), // 分享链接
				                imgUrl: '{{url('/images/my/dis_in_love.jpg')}}', // 分享图标
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
				                title: '365天，和全国精英家长一起，成为更懂教育的父母', // 分享标题
				                link: $.UrlUpdateParams(window.location.href, "from", 'singlemessage'), // 分享链接
				                imgUrl: '{{url('/images/my/dis_in_love.jpg')}}', // 分享图标
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
@endif
<script>
//向ta提问
$("#vip_open").click(function(e){
    e.preventDefault();
    @if($order)
        location.href="{{route('wechat.vip_pay')}}";
    @else
        location.href="{{route('vip.buy')}}";
    @endif
});
</script>
@endsection
