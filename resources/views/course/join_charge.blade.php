@extends('layout.default')
@section('content')
<div id="subject">
    <div id="main">
        <div class="good_looking">
            <div class="gl_order_details">
                <form class="glod_form" action="{{route('course.do_join_charge')}}" method="post">
                    {!! csrf_field() !!}
                    <input type="hidden" id="url" name="url">
                    <input type="hidden" name="id" id="id" value="{{$course->id}}">
                    <input type="hidden" name="team_id" id="team_id" value="{{$team_id}}">
                    <div class="glod_div">
                        <div class="glod_top">确认订单</div>
                    </div>
                    <div class="glod_div">
                        <div class="glod_details">
                            <div class="glod_details_img"><div class="gl_list2_xz">{{$course->agency->agency_name}}</div><img src="{{$course->picture}}" alt=""/></div>
                            <div class="glod_details_title">{{ @str_limit($course->title,20) }}</div>
                            <div class="glod_details_people" id="package_name" name="package_name">@if($package_flg ==1)单人@elseif($package_flg ==2)家庭套餐@else单人@endif</div>
                            <input type="hidden" id="package_flg" name="package_flg" value="{{$package_flg or 1}}">
                            <input type="hidden" id="independent" name="independent" value="{{$independent}}">
                            <div class="glod_details_price" >
                                @if($independent == 1)
                                    <!--单独购买-->
                                    ￥<span id="package_prices">{{$course->price}}</span> 
                                     <input type="hidden" name="package_prices" value="{{$course->price}}">
                                @elseif($course->type == 3) 
                                    团购价:
                                    ￥<span id="package_prices">{{$course->tuangou_price}}</span> 
                                     <input type="hidden" name="package_prices" value="{{$course->tuangou_price}}">
                                @else
                                    ￥<span id="package_prices">{{$package_prices or $course->price}}</span> 
                                    <input type="hidden" name="package_prices" value="{{$package_prices or $course->price}}">
                                @endif 
                            </div>
                        </div>
                        <ul class="glod_list">
                            <li>
                                <div class="glod_list_div">产品套餐</div>
                                <ul class="glod_list_package">
                                    @if($course->type == 3) 
                                        <li class="select" data-value="0"  id="single">组团</li>
                                        <input type="hidden" name="price" id="price" value="{{$course->tuangou_price}}">
                                    @else
                                        <li @if($package_flg !=2) class="select" @endif data-value="0"  id="single">单人</li>
                                        <input type="hidden" name="price" id="price" value="{{$course->price}}">
                                        @if($course->package_price > 0)<li  @if($package_flg ==2) class="select" @endif data-value="1" id="home">家庭套餐</li>@endif
                                        <input type="hidden" id="package_price" value="{{$course->package_price}}">
                                    @endif                                    
                                </ul>
                                <div class="clearboth"></div>
                            </li>
                            <li>
                                <div class="glod_list_div">购买数量</div>
                                <div class="glod_list_number">
                                    <div class="number_div">
                                        <div class="number_div_reduce"><img src="/images/look/reduce.png" alt=""/></div>
                                        <div class="number_div_input"><input type="text" name="number" value="{{$number or 1}}" readonly></div>
                                        <div class="number_div_plus"><img src="/images/look/plus.png" alt=""/></div>
                                    </div>
                                </div>
                            </li>
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
                            <li style="display: none">
                                <div class="glod_list_div">可用
                                    <span id="usable_point">{{$usable_point}}</span><input type="hidden" name="usable_point">和贝抵￥
                                    <span id="usable_money">{{$usable_money}}</span><input type="hidden" name="usable_money">
                                </div>
                                <div class="glod_list_switch">
                                    <div class="switch point" data-value="{{$is_point==1?"open":"shut"}}"></div><!--open为开，shut为关-->
                                    <input id="is_point" name="is_point" type="hidden" value="{{$is_point or 0}}">
                                </div>
                            </li>
                            <li>
                                <div class="glod_list_div">可用余额：￥<span id="usable_balance">{{$usable_balance}}</span>
                                    <input type="hidden" name="usable_balance" value="{{$usable_balance}}"></div>
                                <div class="glod_list_switch">
                                    <div class="switch balance" data-value="{{$is_balance==1?"open":"shut"}}"></div><!--open为开，shut为关-->
                                    <input id="is_balance" name="is_balance" type="hidden" value="{{$is_balance or 0}}">
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="glod_bottom_div">
                        <div class="glod_bottom_div_price">合计 <span>￥</span>
                                <span id="total_price">
                                    @if($independent == 1)
                                        {{$total_price or $course->price}}
                                    @elseif($course->type == 3) 
                                        {{$course->tuangou_price}}
                                    @else
                                        {{$total_price or $course->price}}
                                    @endif
                                    
                                </span></div>
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
    @if($course->type == 3)
        //团购不显示套餐类型和数量
        $('.glod_list>li').eq(0).hide()
        $('.glod_list>li').eq(1).hide()
    @endif
        
        
    $(document).ready(function(){
        @if (count($errors) > 0)
            var str = '';
            @foreach ($errors->all() as $error)
                str+='{{ $error }}<br>';
            @endforeach
            Popup.init({
                popHtml:'<p>'+str+'</p>',
                popFlash:{
                    flashSwitch:true,
                    flashTime:3000,
                }
            });
        @endif
        // 默认单价
        var default_price = {{$course->price}};
        var usable_point = '{{$usable_point}}';
        var usable_money = '{{$usable_money}}';
        var package_flg = '{{$package_flg}}';
        var number = '{{$number}}';

        //初始化页面
        if (!package_flg) {
            $('#package_flg').val(1);
            $('input[name="package_prices"]').val(default_price);
        };
        if (!number) {
            $('input[name="number"]').val(1);
        };
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
            var url = "{{route('course.coupon',['id'=>$course->id])}}";
            var package_flg = $('#package_flg').val();// 套餐类别
            var package_prices = $('#package_prices').html();// 单人/套餐价格
            var number = $('input[name="number"]').val();// 购买数量
            var is_point = $('#is_point').val();// 积分开关
            var usable_point = $('#usable_point').html();// 可用积分
            var usable_money = $('#usable_money').html();// 积分可抵用现金
            var is_balance = $('#is_balance').val();// 可用余额开关
            var usable_balance = $("#usable_balance").html();// 可用余额

            window.location.href = url + "?package_flg="+ package_flg + "&package_prices="+ package_prices
                                    + "&number=" + number + "&is_point=" + is_point + "&usable_point=" + usable_point 
                                    + "&usable_money=" + usable_money + "&is_balance=" + is_balance + "&usable_balance=" + usable_balance;

        });

        // **********套餐选择
        //单人
        $("#single").click(function(){
            var price = $('#price').val();
            $('#single').attr('class','select');
            $('#home').attr('class','');
            $('#package_name').html('单人');// 套餐名称
            $('#package_flg').val('1');// 单人或者家庭套餐
            $('#package_prices').html(price);// 套餐价格
            $('input[name="package_prices"]').val(price);// 套餐价格(隐藏)
            $('#total_price').html(price);// 总价

            // 将下面的设置成默认
            $(".number_div_reduce").siblings(".number_div_input").children("input").val(1);// 购买数量默认为1
            $("#coupon_name").html('使用优惠券');// 优惠券默认为不使用优惠券
            $("#coupon_id").val('')
            $('#is_point').val(0);
            $(".point").attr("data-value","shut");// 积分抵用开关不开启
            $('#is_point').val(0);
            $(".balance").attr("data-value","shut");// 余额开关不开启
            $('#is_balance').val(0);

            // 可用积分/抵用现金
            if (score/100 >= price/2) {
                $('#usable_point').html(price/2*100);
                $('input[name="usable_point"]').val(price/2*100);
                $('#usable_money').html((price/2).toFixed(2));
                $('input[name="usable_money"]').val((price/2).toFixed(2));
            }else {
                $('#usable_point').html(score);
                $('input[name="usable_point"]').val(score);
                $('#usable_money').html((score/100).toFixed(2));
                $('input[name="usable_money"]').val((score/100).toFixed(2));
            }

            // 可用余额变化
            $("#usable_balance").html(price);
            $('input[name="usable_balance"]').val(price);
            if (price >= {{$user->current_balance}}) {
                $("#usable_balance").html({{$user->current_balance}});
                $('input[name="usable_balance"]').val({{$user->current_balance}});
            }

        });
        // 家庭套餐
        $("#home").click(function(){
            var package_price = $('#package_price').val();
            $('#single').attr('class','');
            $('#home').attr('class','select');
            $('#package_name').html('家庭套餐');
            $('#package_flg').val('2');
            $('#package_prices').html(package_price);
            $('input[name="package_prices"]').val(package_price);// 套餐价格(隐藏)
            $('#total_price').html(package_price);

            // 将下面的设置成默认
            $(".number_div_reduce").siblings(".number_div_input").children("input").val(1);
            $("#coupon_name").html('使用优惠券');// 优惠券默认为不使用优惠券
            $("#coupon_id").val('')
            $('#is_point').val(0);
            $(".point").attr("data-value","shut");// 积分抵用开关不开启
            $('#is_point').val(0);
            $(".balance").attr("data-value","shut");// 余额开关不开启
            $('#is_balance').val(0);

            // 可用积分/抵用现金
            if (score/100 >= package_price/2) {
                $('#usable_point').html(package_price/2*100);
                $('input[name="usable_point"]').val(package_price/2*100);
                $('#usable_money').html((package_price/2).toFixed(2));
                $('input[name="usable_money"]').val((package_price/2).toFixed(2));
            }else {
                $('#usable_point').html(score);
                $('input[name="usable_point"]').val(score);
                $('#usable_money').html((score/100).toFixed(2));
                $('input[name="usable_money"]').val((score/100).toFixed(2));
            }

            // 可用余额变化
            $("#usable_balance").html(package_price);
            $('input[name="usable_balance"]').val(package_price);
            if (package_price >= {{$user->current_balance}}) {
                $("#usable_balance").html({{$user->current_balance}});
                $('input[name="usable_balance"]').val({{$user->current_balance}});
            }

        });

        // **********购买数量
        // 点击number减号
        $(".number_div_reduce").click(function(){
            if($(this).siblings(".number_div_input").children("input").val()>1){
                // 数量变化
                $(this).siblings(".number_div_input").children("input").val($(this).siblings(".number_div_input").children("input").val()-1);
                // 总价变化
                var package_prices = $('#package_prices').html();
                var total_price = package_prices * $(this).siblings(".number_div_input").children("input").val();
                $('#total_price').html(total_price.toFixed(2));// 总价

                // 将下面的设置成默认
                $("#coupon_name").html('使用优惠券');// 优惠券默认为不使用优惠券
                $("#coupon_id").val('')
                $('#is_point').val(0);
                $(".point").attr("data-value","shut");// 积分抵用开关不开启
                $('#is_point').val(0);
                $(".balance").attr("data-value","shut");// 余额开关不开启
                $('#is_balance').val(0);

                // 可用积分/抵用现金
                if (score/100 >= total_price/2) {
                    $('#usable_point').html(total_price/2*100);
                    $('input[name="usable_point"]').val(total_price/2*100);
                    $('#usable_money').html((total_price/2).toFixed(2));
                    $('input[name="usable_money"]').val((total_price/2).toFixed(2));
                }else {
                    $('#usable_point').html(score);
                    $('input[name="usable_point"]').val(score);
                    $('#usable_money').html((score/100).toFixed(2));
                    $('input[name="usable_money"]').val((score/100).toFixed(2));
                }

                // 可用余额变化
                $("#usable_balance").html(total_price.toFixed(2));
                $('input[name="usable_balance"]').val(total_price);
                if (total_price >= {{$user->current_balance}}) {
                    $("#usable_balance").html({{$user->current_balance}});
                    $('input[name="usable_balance"]').val({{$user->current_balance}});
                }

            }
        });
        // 点击number加号
        $(".number_div_plus").click(function(){
            // 数量变化
            $(this).siblings(".number_div_input").children("input").val($(this).siblings(".number_div_input").children("input").val()-1+2);
            // 总价变化
            var package_prices = $('#package_prices').html();
            var total_price = package_prices * $(this).siblings(".number_div_input").children("input").val();
            $('#total_price').html(total_price.toFixed(2));// 总价
            

            // 将下面的设置成默认
            $("#coupon_name").html('使用优惠券');// 优惠券默认为不使用优惠券
            $("#coupon_id").val('')
            $('#is_point').val(0);
            $(".point").attr("data-value","shut");// 积分抵用开关不开启
            $('#is_point').val(0);
            $(".balance").attr("data-value","shut");// 余额开关不开启
            $('#is_balance').val(0);

            // 可用积分/抵用现金
            if (score/100 >= total_price/2) {
                $('#usable_point').html(total_price/2*100);
                $('input[name="usable_point"]').val(total_price/2*100);
                $('#usable_money').html((total_price/2).toFixed(2));
                $('input[name="usable_money"]').val((total_price/2).toFixed(2));
            }else {
                $('#usable_point').html(score);
                $('input[name="usable_point"]').val(score);
                $('#usable_money').html((score/100).toFixed(2));
                $('input[name="usable_money"]').val((score/100).toFixed(2));
            }

            // 可用余额变化
            $("#usable_balance").html(total_price.toFixed(2));
            $('input[name="usable_balance"]').val(total_price);
            if (total_price >= {{$user->current_balance}}) {
                $("#usable_balance").html({{$user->current_balance}});
                $('input[name="usable_balance"]').val({{$user->current_balance}});
            }
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

        $(".glod_button").click(function(){//点击提交
            $('form').submit();
        });
    });
    </script>

@endsection
