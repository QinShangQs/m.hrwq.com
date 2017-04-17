@extends('layout.default')
@section('content')
<div id="subject">
    <div id="main">
        <div class="good_looking">
            <div class="gl_payment_select">
                <form class="glps_form">
                    {!! csrf_field() !!}
                    <input type="hidden" name="order_id" id="order_id" value="{{$order->id}}">
                    <div class="glps_div">
                        <div class="glps_top">选择支付方式</div>
                    </div>
                    <div class="glps_div">
                        <ul class="glps_list1">
                            <li><label for="glps_radio_1"><span><input name="glps_radio" value="1" type="radio" id="glps_radio_1" checked></span>微信支付</label></li>
                            <li><label for="glps_radio_2"><span><input type="radio" value="2" name="glps_radio" id="glps_radio_2"></span>线下支付</label></li>
                        </ul>
                    </div>
                    <div class="glps_div">
                        <div class="glps_div_money"><span>￥{{$order->price}}</span>实付金额</div>
                    </div>
                    <div class="glps_div_button">立即支付</div>
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
    $(".glps_div_button").click(function(){//点击提交
        //判断radio，决定跳转链接，然后跳转
        var way = $('input[name="glps_radio"]:checked').val();
        // 微信支付
        if (way == 1) {
            wx.chooseWXPay({
                timestamp: <?= $config['timestamp'] ?>,
                nonceStr: '<?= $config['nonceStr'] ?>',
                package: '<?= $config['package'] ?>',
                signType: '<?= $config['signType'] ?>',
                paySign: '<?= $config['paySign'] ?>', // 支付签名
                success: function (res) {
                   setInterval("checkStatus()", 1000);
                }
            });
        }
        // 线下支付
        if (way == 2) {
            window.location.href = "{{ route('course.line_pay',['id'=>$order->id]) }}";
        }

    });
});

function checkStatus() {
    $.ajax({
        url: "{{route('wechat.status')}}",
        type: "post",
        dataType: "json",
        data: {"order_id": "{{$order->id}}", "_token": "{{csrf_token()}}"},
        success: function (res) {
            if (res.code == 0 && res.data == "2") {
                location.href = '{{route('course.detail',['id'=>$order->pay_id])}}'+'?share=1';
            }
        },
        error: function () {
        }
    });
}

</script>

@endsection
