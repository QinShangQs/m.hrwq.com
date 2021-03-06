@extends('layout.default')
@section('content')
<div id="subject">
    <div id="main">
        <div class="good_looking">
            <div class="gl_payment_confirm">
                <form class="glpc_form">
                    <div class="glpc_div">
                        <div class="glps_top">确认支付</div>
                        <div class="glps_title">{{ $order->order_name }}</div>
                        <div class="glps_price">￥{{ $order->price }}</div>
                    </div>
                    <div class="glpc_div">
                        <div class="glpc_div_list"><span>和润万青</span>收款方</div>
                    </div>
                    <a href="#" class="glpc_div_button">立即支付</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">
$(document).ready(function(){
    wx.config(<?php echo $wechatJs->config(array('chooseWXPay'), false) ?>);
    $(".glpc_div_button").click(function(){
        wx.chooseWXPay({
                timestamp: '{{$config['timestamp']}}',
                nonceStr:  '{{$config['nonceStr']}}',
                package:  '{{$config['package']}}',
                signType:  '{{$config['signType']}}',
                paySign:   '{{$config['paySign']}}', // 支付签名
                success: function () {
                   setInterval(checkStatus, 1000);
                }
          });
    });
});
function checkStatus() {
    $.ajax({
        url: "{{route('wechat.status')}}",
        type: "post",
        dataType: "json",
        data: {order_id: "{{$order->id}}", user_id:"{{$order->user_id}}"},
        success: function (res) {
            if (res.code == 0 && res.data == "2") {
                if(res.user.mobile){
                	//location.href = '{{route('user')}}';
                        location.href = '{{route('my.orders',[])}}';
                }else{
                	location.href = '{{route('user.login')}}';
                }
                
            }
        },
        error: function () {
        }
    });
}
</script>
@endsection