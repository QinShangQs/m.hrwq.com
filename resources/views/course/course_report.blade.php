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
                        <div class="glps_top">报道成功</div>
                    </div>
                    <div class="glps_div">
                        <ul class="glps_list1">
                            <li><label for="glps_radio_1"><span>{{$order->order_name}}</span>课程名</label></li>
                        </ul>
                    </div>
                    <div class="glps_div">
                        <ul class="glps_list1">
                            <li><label for="glps_radio_1"><span>{{$order->user->realname or $order->user->nickname}}</span>用户</label></li>
                        </ul>
                    </div>
                    <div class="glps_div">
                        <ul class="glps_list1">
                            <li><label for="glps_radio_1"><span>{{$order->order_course->report_time or date('Y-m-d H:i:s')}}</span>报到时间</label></li>
                        </ul>
                    </div>
                    <div class="glps_div">
                        <ul class="glps_list1">
                            <li><label for="glps_radio_1"><span>{{$order->quantity}}人</span>包含人数</label></li>
                        </ul>
                    </div>
                    <div class="glps_div">
                        <ul class="glps_list1">
                            <li><label for="glps_radio_1"><span>@if($orderCourse->package_flg ==1)单人@else套餐@endif</span>单人/套餐</label></li>
                        </ul>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script type="text/javascript">
$(document).ready(function(){
});
</script>
@endsection
