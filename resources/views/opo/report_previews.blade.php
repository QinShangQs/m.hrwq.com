@extends('layout.default')
@section('content')
    <div id="subject">
        <div id="main">
            <div class="my">
                <div class="my_integral_introduce">
                    <p style="text-align: center;">我的家庭服务日志</p>
                    <p>{{@$order->order_opo->service_comment}}</p>
                </div>
            </div>
        </div>
    </div>
@endsection