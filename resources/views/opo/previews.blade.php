@extends('layout.default')
@section('content')
    @foreach($previews as $image)
    <img src="{{qiniu_url($image)}}">
        <br>
    @endforeach
@endsection
@section('script')
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">
        $(document).ready(function () {
              wx.config(<?php echo $wx_js->config(array("onMenuShareAppMessage", "onMenuShareTimeline"), false) ?>);
              wx.ready(function () {
                wx.onMenuShareAppMessage({
                    title: '我的家庭服务日志', // 分享标题
                    desc: '{{@$order->order_opo->service_comment}}', // 分享描述
                    link: '{{route('opo.report.shares',['id'=>$order->id])}}?from=singlemessage', // 分享链接
                    imgUrl: '{{admin_url(@$order->opo->picture)}}', // 分享图标
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
                    title: '我的家庭服务日志', // 分享标题
                    link: '{{route('opo.report.shares',['id'=>$order->id])}}?from=singlemessage', // 分享链接
                    imgUrl: '{{admin_url(@$order->opo->picture)}}', // 分享图标
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