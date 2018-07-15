@extends('layout.default')
@section('content')
<link rel="stylesheet" href="/css/partner-card.css"/>

<div class="card-body">
    <div class="banner">
        <img src="{{$card_info->cover_url or "/images/partner/banner.png"}}" />
<!--        <div class="change"></div>-->
    </div>
    
    <div class="info-content">
        <div class="name-info">
            <div class="author">
                <img src="{{$user_info['profileIcon']}}"/>
                <div class="desc">
                    <div class="name">{{$user_info['realname']}}</div>
                    <div class="cityname">{{$user_info['city']['area_name']}}合伙人</div>
                </div>
            </div>
            
            <div class="qrcode">
                <img src="/images/partner/qrcode-logo.png"/>
                <div class="desc">二维码</div>
            </div> 
        </div>
        
        <div class='items'>
            <div class='item'>
                <img src='/images/partner/phone.png' />
                <div class='remark' >
                    <div class='title'>电话</div>
                    <div class='name'>{{$card_info->tel}}</div>
                </div>
            </div>
            <div class='item'>
                <img src='/images/partner/wechat.png' />
                <div class='remark' >
                    <div class='title'>微信</div>
                    <div class='name'>{{$card_info->wechat}}</div>
                </div>
            </div>
            <div class='item'>
                <img src='/images/partner/msg.png' />
                <div class='remark' >
                    <div class='title'>邮箱</div>
                    <div class='name'>{{$card_info->email}}</div>
                </div>
            </div>
            <div class='item'>
                <img src='/images/partner/pointer.png' />
                <div class='remark' >
                    <div class='title'>地址</div>
                    <div class='name'>{{$card_info->address}}</div>
                </div>
            </div>
            <div class='item'>
                <img src='/images/partner/ie.png' />
                <div class='remark' >
                    <div class='title'>网址</div>
                    <div class='name'>{{$card_info->website}}</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class='mid-content'>
        <a href='{{route('partner.cardEdit')}}'><img src='/images/partner/edit.png' /></a>
        <img src='/images/partner/send.png'  onclick="$('.win-share').show()"/>
    </div>
    
    <div class='last-content'>
        <div class='item'>
            <div class='title'>
                <img src='/images/partner/left-line.png'/>
                <span>简介</span>
            </div>
            <div class='tcont'>
                <p>
                    {!! nl2br($card_info->remark) !!}
                </p>
            </div>
        </div>
        <div class='item'>
            <div class='title'>
                <img src='/images/partner/left-line.png'/>
                <span>照片</span>
            </div>
            <div class='tcont'>
                <div class='pics'>
                    @if ($card_info->images)
                        @foreach($card_info->images as $image)
                        <div class='lmg' >
                            <img src='{{$image->url}}'/>
                        </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
        <div class='item'>
            <div class='title'>
                <img src='/images/partner/left-line.png'/>
                <span>视频</span>
            </div>
            <div class='tcont'>
                <img src='/images/partner/video.png' />
                <div class='desc'>
                    点击上传视频
                </div>
            </div>
        </div>
        
<!--        <img class='save' src='/images/partner/save.png'/>-->
    </div>
    
</div>

<div class="qrcode-bg" style="display:none">
    <div class="qrcode-body">
        <div class="title">
            <img src="{{$user_info['profileIcon']}}"/>
            <div class="name">{{$user_info['realname']}}</div>
            <img class="close" src="/images/look/glr_close.png" />
        </div>
        <div class="cav">
            <div id="qrcode"></div>
        </div>
    </div>
</div>

<div class="win-share" 
     style="background: url(&quot;/images/vcourse/share-shadow.jpg&quot;) 0% 0% / contain; 
     top: 0px; opacity: 0.9; z-index: 100; width: 100%; 
     height: 100%; position: fixed; display: none;" onclick="$(this).hide()">
</div>

@endsection
@section('script')
    <script type="text/javascript" src="/js/qrcode.min.js"></script>
    <script>
        $(document).ready(function(){
            var qrcodeWidth = window.screen.width*.8*.8;
            qrcodeWidth > 500 ? qrcodeWidth = 250 : null;

            var qrcode = new QRCode(document.getElementById("qrcode"), {
		text: "{{ route('partner.card.show',[ 'uid'=> $base64_id ] ) }}",
		width : qrcodeWidth,
		height : qrcodeWidth
            });
            $('#qrcode').width(qrcodeWidth).css('margin','auto');
            
            $('.qrcode-body .title .close').click(function(){
                $('.qrcode-bg').hide();
            });
            
            $('.name-info .qrcode').click(function(){
                $('.qrcode-bg').show();
            });
        });
    </script>
    
    <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js" type="text/javascript" charset="utf-8"></script>
    <script type="text/javascript">
	$(document).ready(function(){
                var _title = "我是和润万清合伙人{{$user_info['realname']}}";
                var _link = "{{ route('partner.card.show',[ 'uid'=> $base64_id ] ) }}?from=singlemessage";
                var _imgUrl = "{{$user_info['profileIcon']}}";

		wx.config(<?php echo $wx_js->config(array("onMenuShareAppMessage", "onMenuShareTimeline"), false) ?>);
                wx.ready(function () {
	        wx.onMenuShareAppMessage({
	            title: _title, // 分享标题
	            desc: '我们穷尽一生的时间爱孩子，却很少关注自身的提升', // 分享描述
	            link: _link, // 分享链接
	            imgUrl: '{{$user_info['profileIcon']}}', // 分享图标
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
	            title: _title, // 分享标题
	            link: _link, // 分享链接
	            imgUrl: _imgUrl, // 分享图标
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