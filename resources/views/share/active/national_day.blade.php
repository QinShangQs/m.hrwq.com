@extends('layout.default')
@section('content')

<style>

</style>

<div style="position: relative;font-size: 0">
    <img src="/images/share/national-day.jpeg" style="width: 100%" />
    <div id="share" style="background-color: red;width:100%;height: 8rem;position: absolute;z-index: 10;top:35rem"></div>
    <div id="buy" style="background-color: blue;width:100%;height: 8rem;position: absolute;z-index: 10;top:50rem"></div>
</div>

<div class="win-share" style="display: none;background:url(/images/vcourse/share-shadow.jpg);top:0px;opacity: 0.9;z-index:100;width:100%;height:100%;
    position: fixed;background-size: 100%;background-repeat: round;"></div>

@endsection
@section('script')
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js" type="text/javascript" charset="utf-8"></script>

<script type="text/javascript">
    $(document).ready(function () {
        $('#share').click(function () {
            $('.win-share').show();
        });
        $(".win-share").click(function () {
            $(".win-share").hide();
        });
        $('#buy').click(function () {
            location.href = '/course/detail/24';
        });
        
        wx.config(<?php echo $wx_js->config(array("onMenuShareAppMessage", "onMenuShareTimeline"),false) ?>);
        wx.ready(function(){
            wx.onMenuShareAppMessage({
                title: '盛世华诞 举国同庆', 
                desc: '和润万青国庆70周年特惠活动，转发即享家庭教育专题课', 
                link:"{{ route('share.hot',['id'=> $user_info['id'] ] ) }}"+"?back="+location.pathname,
                imgUrl: 'http://photos.partner.hrwq.com/WechatIMG5165.jpeg', // 分享图标
                success: function () { 
                    $.post('{{ route("share.active.national.receive_vipday")}}',{},function(json){
                        console.info(json.message)
                    });
                },
                cancel: function () { 
                }
            });
            wx.onMenuShareTimeline({
                title: '盛世华诞 举国同庆', // 分享标题
                link:"{{ route('share.hot',['id'=> $user_info['id'] ] ) }}"+"?back="+location.pathname,
                imgUrl: 'http://photos.partner.hrwq.com/WechatIMG5165.jpeg', // 分享图标
                success: function () {
                    $.post('{{ route("share.active.national.receive_vipday")}}',{},function(json){
                        console.info(json.message)
                    });
                },
                cancel: function () {
                }
            });

        });

    });


</script>

@endsection