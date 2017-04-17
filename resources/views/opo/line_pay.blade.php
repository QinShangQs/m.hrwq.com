@extends('layout.default')
@section('content')
<div id="subject">
    <div id="main">
        <div class="good_looking">
            <div class="gl_payment_confirm">
                <form class="glpc_form">
                <input type='hidden' name='_token' value="{{csrf_token()}}">
                <input type="hidden" name="id" value="{{$order->id}}"/>
                    <div class="glpc_div">
                        <div class="glpc_div_p">若无法微信支付，您可通过支付宝或银行等其他支付平台，将课程费用转账至下方银行账户，然后向客服确认。</div>
                    </div>
                    <div class="glpc_div">
                        <div class="glpc_div_list"><span>{{config('constants.card_no')}}</span>银行卡号</div>
                    </div>
                    <div class="glpc_div">
                        <div class="glpc_div_list"><span>{{config('constants.opening_bank')}}</span>开户行</div>
                    </div>
                    <div class="glpc_div">
                        <div class="glpc_div_list"><span>{{config('constants.card_holder')}}</span>姓名</div>
                    </div>
                    <a href="#" class="glpc_div_button">确认</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript">
$(document).ready(function(){
    var lock = false;
    $(".glpc_div_button").click(function(e){
        e.preventDefault();
        if (lock) {return;}
        lock = true;
        /*-----ajax事件开始-----*/
        $.ajax({
            type: 'post',
            url: '{{route('opo.confirm.offline.pay', ['id'=>$order->id])}}',
            data: $('form').serialize(),
            dataType: 'json',
            success: function (res) {
                if(res.code==0) {
                    location.href = '{{route('opo')}}';
                } else {
                    Popup.init({
                        popHtml:'<p>'+res.message+'</p>',
                        popFlash:{
                            flashSwitch:true,
                            flashTime:2000
                        }
                    });
                    lock = false;
                }
            }
        });
        return false;
    });
});
</script>
@endsection