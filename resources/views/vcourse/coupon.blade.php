@extends('layout.default')
@section('content')

<div id="subject">
    <div id="main">
        <div class="my">
            <div class="my_coupon_choice">
                <div class="mcc_top">选择优惠券</div>
                <ul class="mcc_list mcc_list1"><!--可用优惠券-->
                    @foreach ($couponusers_usable as $item)
                        <li data-couponuserid="{{$item->id}}" data-couponid="{{$item->coupon_id}}" data-type="{{$item->type}}" data-cutmoney="{{$item->cut_money}}" data-discount="{{$item->discount}}" ><!--data-id为优惠券id-->
                            <!-- 抵用券 -->
                            @if($item->type == 1)
                            <div class="mcc_list_left ">
                                <div class="mcc_list_left_1">{{$item->cut_money}}元</div>
                                <div class="mcc_list_left_2">抵用券</div>
                            </div>
                            <div class="mcc_list_right">
                                <p>满{{$item->full_money}}可使用</p>
                                <p>限{{$coupon_use_scope[$item->use_scope]}}使用</p>
                                @if($item->available_period_type==1)
                                <p>期限{{ date('Y.m.d',strtotime($item->created_at)) }}-{{ date('Y.m.d',strtotime($item->expire_at)) }}</p>
                                @else
                                <p>期限{{ date('Y.m.d',strtotime($item->available_start_time)) }}-{{ date('Y.m.d',strtotime($item->available_end_time)) }}</p>
                                @endif
                            </div>
                            @endif
                            <!-- 折扣券 -->
                            @if($item->type == 2)
                            <div class="mcc_list_left ">
                                <div class="mcc_list_left_1">{{$item->name}}</div>
                                <div class="mcc_list_left_2">折扣券</div>
                            </div>
                            <div class="mcc_list_right">
                                <p>{{$item->name}}</p>
                                <p>限{{$coupon_use_scope[$item->use_scope]}}使用</p>
                                @if($item->available_period_type==1)
                                <p>期限{{ date('Y.m.d',strtotime($item->created_at)) }}-{{ date('Y.m.d',strtotime($item->expire_at)) }}</p>
                                @else
                                <p>期限{{ date('Y.m.d',strtotime($item->available_start_time)) }}-{{ date('Y.m.d',strtotime($item->available_end_time)) }}</p>
                                @endif
                                
                            </div>
                            @endif

                        </li>
                    @endforeach

                </ul>

                <ul class="mcc_list mcc_list2"><!--不可用优惠券-->
                    @foreach ($couponusers_unusable as $item)
                        <li><!--data-id为优惠券id-->
                            <!-- 抵用券 -->
                            @if($item->type == 1)
                            <div class="mcc_list_left ">
                                <div class="mcc_list_left_1">{{$item->cut_money}}元</div>
                                <div class="mcc_list_left_2">抵用券</div>
                            </div>
                            <div class="mcc_list_right">
                                <p>满{{$item->full_money}}可使用</p>
                                <p>限{{$coupon_use_scope[$item->use_scope]}}使用</p>
                                @if($item->available_period_type==1)
                                <p>期限{{ date('Y.m.d',strtotime($item->created_at)) }}-{{ date('Y.m.d',strtotime($item->expire_at)) }}</p>
                                @else
                                <p>期限{{ date('Y.m.d',strtotime($item->available_start_time)) }}-{{ date('Y.m.d',strtotime($item->available_end_time)) }}</p>
                                @endif
                            </div>
                            @endif
                            <!-- 折扣券 -->
                            @if($item->type == 2)
                            <div class="mcc_list_left ">
                                <div class="mcc_list_left_1">{{$item->name}}</div>
                                <div class="mcc_list_left_2">折扣券</div>
                            </div>
                            <div class="mcc_list_right">
                                <p>{{$item->name}}</p>
                                <p>限{{$coupon_use_scope[$item->use_scope]}}使用</p>
                                @if($item->available_period_type==1)
                                <p>期限{{ date('Y.m.d',strtotime($item->created_at)) }}-{{ date('Y.m.d',strtotime($item->expire_at)) }}</p>
                                @else
                                <p>期限{{ date('Y.m.d',strtotime($item->available_start_time)) }}-{{ date('Y.m.d',strtotime($item->available_end_time)) }}</p>
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
$(document).ready(function(){

    $(".mcc_list li").height($(".mcc_list li").width()/690*160);
    $(".mcc_list_left").css("top",($(".mcc_list li").height()-52)/2+"px");
    $(".mcc_list_right").css("top",($(".mcc_list li").height()-60)/2+"px");

    $(".mcc_list1 li").click(function(){//点击可使用优惠券
        var id=$(this).attr("data-id");

        var url = "{{ route('vcourse.order',['id'=>$vcourse->id]) }}";
        var coupon_user_id = $(this).data('couponuserid');
        var coupon_id = $(this).data('couponid');
        var coupon_type = $(this).data('type');
        var coupon_cutmoney = $(this).data('cutmoney');
        var coupon_discount = $(this).data('discount');

        var is_point = '{{$is_point}}';// 积分开关
        var usable_point = '{{$usable_point}}';// 可用积分
        var usable_money = '{{$usable_money}}';// 积分可抵用现金
        var is_balance = '{{$is_balance}}';// 可用余额开关
        var usable_balance = '{{$usable_balance}}';// 可用余额
        var total_price = '{{$total_price}}';// 总计

        window.location.href = url + "?temp=2" + "&coupon_user_id=" + coupon_user_id + "&coupon_id=" + coupon_id + "&coupon_type=" + coupon_type + "&coupon_cutmoney=" + coupon_cutmoney  
            + "&coupon_discount=" + coupon_discount + "&is_point=" + is_point + 
            "&usable_point=" + usable_point + "&usable_money=" + usable_money + "&is_balance=" + is_balance + "&usable_balance=" + usable_balance + "&total_price=" + total_price;

        /*--------ajax开始--------*/
            //id为优惠券id
        /*--------ajax结束--------*/
    });
});
</script>

@endsection

