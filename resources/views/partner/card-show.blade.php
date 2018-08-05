@extends('layout.default')
@section('content')
<link href="/qiniu/js/videojs/video-js.min.css" rel="stylesheet">
<link rel="stylesheet" href="/css/partner-card.css"/>
<style>
.popupWindow_frame{
    top:35%;
    left:40%;
    width:90%;
}
</style>

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
                    <div class='name'><a href="tel:{{$card_info->tel}}">{{$card_info->tel}}</a></div>
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
                    <div class='name'><a href="mailto:{{$card_info->email}}">{{$card_info->email}}</a></div>
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
    
    <div class='mid-content' >
        <div class='show'>
            <div>点击查看更多信息</div>
            <img src='/images/partner/more.png' />
        </div>
    </div>
    
    <div class='last-content' style="display:none">
        <div class='item'>
            <div class='title'>
                <img src='/images/partner/left-line.png'/>
                <span>简介</span>
            </div>
            <div class='tcont'>
                <p>
                    和润万青（北京）教育科技有限公司，专注华人家庭教育和青少年成长教育15年，是由华人家庭教育领域唯一的父子专家——全国十佳教育公益人物贾容韬老师、北京师范大学心理学硕士贾语凡老师共同创立。
                <br/>全国免费咨询电话：400-6363-555
                </p>
            </div>
        </div>
        <div class='item fixed'
             @if(empty($card_info->video_url) && empty($card_info->images))
                style="display:none"
              @endif
             >
            @if (count($card_info->images) > 0)
                <div class='title'>
                    <img src='/images/partner/left-line.png'/>
                    <span>照片</span>
                </div>
                <div class='tcont'>
                    <div class='pics'>
                        @foreach($card_info->images as $image)
                        <div class='lmg' >
                            <img src='{{$image->url}}?imageslim' onclick="showPhoto('{{$image->url}}');"/>
                        </div>
                        @endforeach
                    </div>
                </div>
            @endif
            
            <div style="height:0.8rem"></div>
            @if(!empty($card_info->video_url))
            <div class="tcont"><!--height="550"-->
                <video id="bus_video_vj" name="bus_video_vj" class="video-js vjs-default-skin  vjs-big-play-centered" 
			width="100%" 
                        controls
                        poster="{{ $card_info->video_url }}?vframe/jpg/offset/1" 
                        data-setup="{}" 
                        preload="auto">
			<source src="{{ $card_info->video_url }}" type='video/mp4' />
		</video> 
            </div>
            @endif
        </div>
        
        <img id="tolearn" class='save' src='/images/partner/to-learn.png'/>
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

@endsection
@section('script')
    <script>
        (function(){
            $('.lcd_evaluate').hide();
            $('.return_index').hide();
            $('.mid-content .show').click(function(){
                $('.mid-content').hide();
                $('.last-content').show();
            });
        })();
    </script>
    
    <script>
        (function(){
            $('#tolearn').click(function(){
                location.href = '/';
            });
        })();
    </script>

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
    
    <script src="/qiniu/js/videojs/video.min.js"></script>
    <script >
        (function(){
            var width = $('.banner').width();
            if($("#bus_video_vj").length > 0){
                $("#bus_video_vj").width(width).height((width/4)*3);
                $(".video-js").removeClass("vjs-controls-disabled");
            }
        })();
        
        function showPhoto(src){
            Popup.init({
                popHtml: '<img src="'+src+'" style="width:100%"/>'
            });
        }
    </script>
@endsection