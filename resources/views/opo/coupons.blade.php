@extends('layout.default')
@section('content')

    <div id="subject">
        <div id="main">
            <div class="my">
                <div class="my_coupon_choice">
                    <div class="mcc_top">选择优惠券</div>
                    <ul class="mcc_list mcc_list1"><!--可用优惠券-->
                        @foreach ($fitCoupons as $item)
                            <li data-id="{{$item->id}}"><!--data-id为优惠券id-->
                                <!-- 抵用券 -->
                                @if($item->c_coupon->type == 1)
                                    <div class="mcc_list_left ">
                                        <div class="mcc_list_left_1">{{$item->c_coupon->cut_money}}元</div>
                                        <div class="mcc_list_left_2">抵用券</div>
                                    </div>
                                    <div class="mcc_list_right">
                                        <p>满{{$item->c_coupon->full_money}}可使用</p>
                                        <p>限使用</p>
                                        @if($item->c_coupon->available_period_type == 1)
                                            <p>期限{{ date('Y.m.d',strtotime($item->created_at)) }}
                                                -{{ date('Y.m.d',strtotime($item->expire_at)) }}</p>
                                        @else
                                            <p>期限{{ date('Y.m.d',strtotime($item->c_coupon->available_start_time)) }}
                                                -{{ date('Y.m.d',strtotime($item->c_coupon->available_end_time)) }}</p>
                                        @endif
                                    </div>
                                @elseif($item->c_coupon->type == 2)
                                    <div class="mcc_list_left ">
                                        <div class="mcc_list_left_1">{{$item->c_coupon->name}}</div>
                                        <div class="mcc_list_left_2">折扣券</div>
                                    </div>
                                    <div class="mcc_list_right">
                                        <p>{{$item->c_coupon->name}}</p>
                                        <p>限使用</p>
                                        @if($item->c_coupon->available_period_type == 1)
                                            <p>期限{{ date('Y.m.d',strtotime($item->created_at)) }}
                                                -{{ date('Y.m.d',strtotime($item->expire_at)) }}</p>
                                        @else
                                            <p>期限{{ date('Y.m.d',strtotime($item->c_coupon->available_start_time)) }}
                                                -{{ date('Y.m.d',strtotime($item->c_coupon->available_end_time)) }}</p>
                                        @endif
                                    </div>
                                @endif

                            </li>
                        @endforeach
                    </ul>

                    <ul class="mcc_list mcc_list2"><!--不可用优惠券-->
                        @foreach ($unfitCoupons as $item)
                            <li>
                                <!-- 抵用券 -->
                                @if($item->c_coupon->type == 1)
                                    <div class="mcc_list_left ">
                                        <div class="mcc_list_left_1">{{$item->c_coupon->cut_money}}元</div>
                                        <div class="mcc_list_left_2">抵用券</div>
                                    </div>
                                    <div class="mcc_list_right">
                                        <p>满{{$item->c_coupon->full_money}}可使用</p>
                                        <p>限使用</p>
                                        @if($item->c_coupon->available_period_type == 1)
                                            <p>期限{{ date('Y.m.d',strtotime($item->created_at)) }}
                                                -{{ date('Y.m.d',strtotime($item->expire_at)) }}</p>
                                        @else
                                            <p>期限{{ date('Y.m.d',strtotime($item->c_coupon->available_start_time)) }}
                                                -{{ date('Y.m.d',strtotime($item->c_coupon->available_end_time)) }}</p>
                                        @endif
                                    </div>
                                @elseif($item->c_coupon->type == 2)
                                    <div class="mcc_list_left ">
                                        <div class="mcc_list_left_1">{{$item->c_coupon->name}}</div>
                                        <div class="mcc_list_left_2">折扣券</div>
                                    </div>
                                    <div class="mcc_list_right">
                                        <p>{{$item->c_coupon->name}}</p>
                                        <p>限使用</p>
                                        @if($item->c_coupon->available_period_type == 1)
                                            <p>期限{{ date('Y.m.d',strtotime($item->created_at)) }}
                                                -{{ date('Y.m.d',strtotime($item->expire_at)) }}</p>
                                        @else
                                            <p>期限{{ date('Y.m.d',strtotime($item->c_coupon->available_start_time)) }}
                                                -{{ date('Y.m.d',strtotime($item->c_coupon->available_end_time)) }}</p>
                                        @endif
                                    </div>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('script')
    <script type="text/javascript">
        $(document).ready(function () {
            $(".mcc_list li").height($(".mcc_list li").width() / 690 * 160);
            $(".mcc_list_left").css("top", ($(".mcc_list li").height() - 52) / 2 + "px");
            $(".mcc_list_right").css("top", ($(".mcc_list li").height() - 60) / 2 + "px");
            $(".mcc_list1 li").click(function () {//点击可使用优惠券
                var coupon_user_id = $(this).data("id");
                window.location.href = '{{route('opo.buy', ['id'=>$opo->id])}}' +'?coupon_user_id='+coupon_user_id+'&use_point={{request('use_point')}}&use_balance={{request('use_balance')}}';
            });
        });
    </script>

@endsection

