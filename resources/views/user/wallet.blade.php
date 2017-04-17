@extends('layout.default')
@section('content')
    <div id="subject">
        <div class="my">
            <div class="my_member_ordinary">
                <ul class="mmo_list">
                    <li><a href="{{route('user.balance')}}"><span><img src="/images/public/select_right.jpg" alt=""/></span>我的收益 @if($balanceCount>0)
                                <div>{{$balanceCount}}</div>@endif</a></li>
                    <li><a href="{{route('user.score')}}"><span><img src="/images/public/select_right.jpg" alt=""/></span>我的和贝 @if($pointCount>0)
                                <div>{{$pointCount}}</div>@endif</a></li>
                    <li><a href="{{route('user.coupon')}}"><span><img src="/images/public/select_right.jpg" alt=""/></span>我的优惠券 @if($couponCount>0)
                                <div>{{$couponCount}}</div>@endif</a></li>
                </ul>
            </div>
        </div>
    </div>
@endsection
