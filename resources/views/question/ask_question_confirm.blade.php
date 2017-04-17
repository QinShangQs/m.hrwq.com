@extends('layout.default')
@section('content')
<div id="subject">
    <div id="main">
        <div class="good_looking">
            <div class="gl_payment_confirm">
                <form class="glpc_form">
                    <div class="glpc_div">
                        <div class="glps_top">确认支付</div>
                        <div class="glps_title">{{ $data->content }}</div>
                        <div class="glps_price">￥{{ $data->price }}</div>
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
    var order_id;
    $(document).ready(function(){
        wx.config(<?php echo Wechat::js()->config(array('chooseWXPay'), false) ?>);
        $(".glpc_div_button").click(function(){
            $.ajax({
                url:'{{route('wechat.question_ask_pay')}}',
                type:'post',
                dataType: "json",
                data: {qid:'{{$data->id}}'},
                success: function (res) {
                    if (res.code == 0) {
                        var config = res.data.config;
                        var order  = res.data.order;
                        order_id = order.id;
                        wx.chooseWXPay({
                            timestamp: config.timestamp,
                            nonceStr: config.nonceStr,
                            package: config.package,
                            signType: config.signType,
                            paySign: config.paySign, // 支付签名
                            success: function () {
                                setInterval(checkStatus, 1000);
                            }
                        });
                    }else{
                        Popup.init({
                            popHtml:res.message,
                            popFlash:{
                                flashSwitch:true,
                                flashTime:2000
                            }
                        });
                    }
                }
            })

        });
    });


    function checkStatus() {
        $.ajax({
            url: "{{route('wechat.status')}}",
            type: "post",
            dataType: "json",
            data: {order_id: order_id},
            success: function (res) {
                if (res.code == 0 && res.data == "2") {
                    location.href = '{{route('question.teacher',['id'=>$data->tutor_id])}}';
                }
            }
        });
    }
</script>
@endsection