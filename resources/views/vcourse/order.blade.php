@extends('layout.default')
@section('content')
<div id="subject">
    <div id="main">
        <div class="good_looking">
            <div class="gl_order_details">
                <form class="glod_form" action="{{route('vcourse.order_save',['id'=>$vcourseDetail->id])}}" method="post">
                <input type='hidden' name='_token' value="{{csrf_token()}}">
                    <div class="glod_div">
                        <div class="glod_top">确认订单</div>
                    </div>
                    <div class="glod_div">
                        <div class="glod_details">
                            <div class="glod_details_img"><div class="gl_list2_xz">{{mb_substr($vcourseDetail->agency->agency_name,0,4)}}</div>
                             @if($vcourseDetail->cover)
                                <img src="{{ config('constants.admin_url').$vcourseDetail->cover}}" alt="" onerror="javascript:this.src='/images/error.jpg'"/>
                            @else
                                <img src="{{ config('qiniu.DOMAIN').$vcourseDetail->video_tran}}?vframe/jpg/offset/{{ config('qiniu.COVER_TIME')}}" alt="" onerror="javascript:this.src='/images/error.jpg'"/>
                            @endif
                            </div>
                            <div class="glod_details_title">{{ @str_limit($vcourseDetail->title,20) }}</div>
                            <div class="glod_details_price">￥{{ $vcourseDetail->price }}</div>
                            <div class="glod_details_people">{{{ $vcourseDetail->view_cnt or 0 }}}人观看</div>
                        </div>
                        <ul class="glod_list">
                            @if(count($couponusers_usable)>0)
                            <li>
                                <div class="glod_list_div" id="coupon_name">
                                @if($coupon_name)
                                {{$coupon_name}}
                                @else
                                使用优惠券
                                @endif</div>
                                <div class="glod_list_more">
                                    <a href="javascript:void(0);" class="div_coupon">
                                        <img src="/images/public/select_right.jpg" alt=""/>
                                    </a>
                                </div>
                            </li>
                            @endif
                             <input type="hidden" id="coupon_id" name="coupon_id" value="{{$coupon_id}}">
                            <input type="hidden" id="coupon_user_id" name="coupon_user_id" value="{{$coupon_user_id}}">
                            <input type="hidden" id="coupon_type" name="coupon_type" value="{{$coupon_type}}">
                            <input type="hidden" id="coupon_cutmoney" name="coupon_cutmoney" value="{{$coupon_cutmoney}}">
                            <input type="hidden" id="coupon_discount" name="coupon_discount" value="{{$coupon_discount}}">
                            <li>
                                <div class="glod_list_div">可用
                                    <span id="usable_point">{{$usable_point}}</span><input type="hidden" name="usable_point">和贝抵￥
                                    <span id="usable_money">{{$usable_money}}</span><input type="hidden" name="usable_money">
                                </div>
                                <div class="glod_list_switch">
                                    <div class="switch point" data-value=@if($is_point==1)"open"@else"shut"@endif></div><!--open为开，shut为关-->
                                    <input id="is_point" name="is_point" type="hidden" value="{{$is_point or 0}}">
                                </div>
                            </li>
                            <li>
                                <div class="glod_list_div">可用余额：￥<span id="usable_balance">{{$usable_balance}}</span>
                                    <input type="hidden" name="usable_balance" value="{{$usable_balance}}"></div>
                                <div class="glod_list_switch">
                                    <div class="switch balance" data-value=@if($is_balance==1)"open"@else"shut"@endif></div><!--open为开，shut为关-->
                                    <input id="is_balance" name="is_balance" type="hidden" value="{{$is_balance or 0}}">
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="glod_bottom_div">
                        <div class="glod_bottom_div_price">合计 <span>￥</span><span id="total_price">{{ $total_price or $vcourseDetail->price }}</span></div>
                        <input type="hidden" name="price" value="{{$vcourseDetail->price}}"/>
                        <input type="hidden" name="id" value="{{$vcourseDetail->id}}"/>
                        <div class="glod_bottom_div_button"><div class="glod_button">提交订单</div></div>
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
     // 默认单价
    var default_price = {{$vcourseDetail->price}};
    var usable_point = '{{$usable_point}}';
    var usable_money = '{{$usable_money}}';

    //初始化页面
    // 可用积分/抵用现金(积分抵用不得超过总金额的一半)
    var score = {{$user->score}};
    if (usable_point&&usable_money) {
        $("#usable_point").html({{$usable_point}});
        $('input[name="usable_point"]').val({{$usable_point}});
        $("#usable_money").html({{$usable_money}});
        $('input[name="usable_money"]').val({{$usable_money}});
    }else{
        if (score/100 >= default_price/2) {
            $('#usable_point').html(default_price/2*100);
            $('input[name="usable_point"]').val(default_price/2*100);
            $('#usable_money').html((default_price/2).toFixed(2));
            $('input[name="usable_money"]').val((default_price/2).toFixed(2));
        }else {
            $('#usable_point').html(score);
            $('input[name="usable_point"]').val(score);
            $('#usable_money').html((score/100).toFixed(2));
            $('input[name="usable_money"]').val((score/100).toFixed(2));
        }
    };
    
    
    // 可用余额
    var usable_balance = '{{$usable_balance}}';
    if(usable_balance){
        $("#usable_balance").html({{$usable_balance}});
        $('input[name="usable_balance"]').val({{$usable_balance}});
    }else{
        $("#usable_balance").html(default_price.toFixed(2));
        $('input[name="usable_balance"]').val(default_price);
        if (default_price >= {{$user->current_balance}}) {
            $("#usable_balance").html({{$user->current_balance}});
            $('input[name="usable_balance"]').val({{$user->current_balance}});
        }
    }

    // 单击更换优惠券
    $("#coupon_name").click(function(){
        var url = "{{route('vcourse.coupon',['id'=>$vcourseDetail->id])}}";

        var is_point = $('#is_point').val();// 积分开关
        var usable_point = $('#usable_point').html();// 可用积分
        var usable_money = $('#usable_money').html();// 积分可抵用现金
        var is_balance = $('#is_balance').val();// 可用余额开关
        var usable_balance = $("#usable_balance").html();// 可用余额

        window.location.href = url + "?temp=2&is_point=" + is_point + "&usable_point=" + usable_point + "&usable_money=" + usable_money + "&is_balance=" + is_balance + "&usable_balance=" + usable_balance;

    });

    // *****点击开关
    // 积分抵用
    $(".point").click(function(){
        if($(this).attr("data-value")=="open"){
            // 按钮样式变化
            $(this).attr("data-value","shut");
            $('#is_point').val(0);

            // 总价变化
            if ($('#is_balance').val()=='1') {
                total_price_before = parseFloat($('#total_price').html()) + parseFloat($('#usable_money').html())+ parseFloat($('input[name="usable_balance"]').val());
            }else{
                total_price_before = parseFloat($('#total_price').html()) + parseFloat($('#usable_money').html());
            }
            $('#total_price').html(total_price_before.toFixed(2));// 总价

            // 将下面的设置成默认
            $(".balance").attr("data-value","shut");// 余额开关不开启
            $('#is_balance').val(0);
            // 可用余额变化
            $("#usable_balance").html(total_price_before.toFixed(2));
            $('input[name="usable_balance"]').val(total_price_before.toFixed(2));
            if (total_price_before >= {{$user->current_balance}}) {
                $("#usable_balance").html({{$user->current_balance}});
                $('input[name="usable_balance"]').val({{$user->current_balance}});
            }
        }else{
            if (score < 1) {// 积分为0，不可抵用
                alert('您的积分为0，不可抵用！');
            }else{
                // 按钮样式变化
                $(this).attr("data-value","open");
                $('#is_point').val(1);

                // 总价变化
                var point_score = $('#usable_money').html();
                var total_price = $('#total_price').html();
                var usable_balance = $('#usable_balance').html();

                if ($('#is_balance').val()=='1') {
                    var total_price_after =  parseFloat($('#total_price').html()) + parseFloat($('#usable_balance').html()) - parseFloat($('#usable_money').html());
                }else{
                    var total_price_after =  parseFloat($('#total_price').html()) - parseFloat($('#usable_money').html());
                }

                $('#total_price').html(total_price_after.toFixed(2));// 总价

                // 将下面的设置成默认
                $(".balance").attr("data-value","shut");// 余额开关不开启
                $('#is_balance').val(0);

                // 可用余额变化
                $("#usable_balance").html(total_price_after.toFixed(2));
                $('input[name="usable_balance"]').val(total_price_after.toFixed(2));
                if (total_price_after >= {{$user->current_balance}}) {
                    $("#usable_balance").html({{$user->current_balance}});
                    $('input[name="usable_balance"]').val({{$user->current_balance}});
                }
            }
        }
    });

    // 可用余额
    $(".balance").click(function(){
        if($(this).attr("data-value")=="open"){
            // 按钮样式变化
            $(this).attr("data-value","shut");
            $('#is_balance').val(0);
            // 总价变化
            total_price_before = parseFloat($('#total_price').html()) + parseFloat($('#usable_balance').html());
            $('#total_price').html(total_price_before.toFixed(2));// 总价
        }else{
            // 按钮样式变化
            $(this).attr("data-value","open");
            $('#is_balance').val(1);
            // 总价变化
            var balance_score = $("#usable_balance").html();
            var total_price = $('#total_price').html();
            var total_price_after = (total_price - balance_score).toFixed(2);
            // 总价变化
            $('#total_price').html(total_price_after);// 总价
        }
    });

    var lock = false;
    $(".glod_button").click(function(e){
        e.preventDefault();
        if (lock) {return;}
        var form_data = $('form').serialize();
        lock = true;
        /*-----ajax事件开始-----*/
        $.post("{{route('vcourse.order_save')}}", form_data,function(data){
            if(data.status){
               if (data.vcourse_id) {
                 location.href = '{{route('vcourse.detail')}}/'+data.vcourse_id;
               }else{
                 location.href = '{{route('wechat.vcourse_pay')}}?id='+data.order_id;
               }
            }else{
               Popup.init({
                    popHtml:'<p>'+data.msg+'</p>',
                    popFlash:{
                        flashSwitch:true,
                        flashTime:2000,
                    }
                });
                lock = false;
            }
        },'json')
        /*-----ajax事件结束-----*/
        return false;
    });
});
</script>
@endsection