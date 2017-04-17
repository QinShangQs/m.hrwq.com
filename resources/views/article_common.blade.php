@extends('layout.default')
@section('style')
<style type="text/css">
    .mbtd_button input { width: 48%; }
</style>
@endsection
@section('content')
    <div id="subject">
        <div id="main">
            <div class="my">
                <article class="my_integral_introduce">
                    {!! $article->content !!}
                </article>
            </div>
            @if($article->type==6)
            <input type="hidden" name="requestUri" id="requestUri" value="{{$requestUri}}">
                <div class="mbtd_button">
                    <a href="#" id="vip_open"><input type="button" class="mbtd_button" value="开通会员"></a>
                    <a href="{{route('vip.active')}}"><input type="button" class="mbtd_button" value="激活会员卡"></a>
                </div>
            @endif
        </div>
    </div>
@endsection
@section('script')
<script>
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
</script>
@endsection
