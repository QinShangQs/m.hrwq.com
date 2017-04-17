@extends('layout.default')
@section('content')
<div id="subject">
    <div id="main">
        <div class="my">
            <div class="my_order">
                <div class="mo_top">我的订单</div>
                <ul class="mo_list">
                    @foreach($orders as $value)
                    <li>
                        <div class="mo_list_top">
                            <div class="mo_list_number">订单编号：{{$value->order_code}}
                            @if($value->pay_type=='6')
                            <span class="mo_list_price">(和会员)</span>
                            @endif
                            </div>
                            <div @if($value->order_type=='1') class="mo_list_state mo_list_state_orange" @else class="mo_list_state mo_list_state_green" @endif>{{$order_type[$value->order_type]}}</div><!--mo_list_state_green为绿色，mo_list_state_orange为橙色-->
                        </div>
                        @if($value->pay_type=='1')
                        <!--好课-->
                        <div class="mo_list_text">
                            <div class="mo_list_img"><img src="{{config('constants.admin_url').@$value->course->picture}}" alt=""/></div>
                            <div class="mo_list_title">{{ @str_limit(@$value->course->title,20) }}</div>
                            <div class="mo_list_price">@if($value->free_flg=='2')￥{{$value->each_price}}@else免费@endif</div>
                            <div class="mo_list_people">{{ $value->course->participate_num or 0}}人已参加</div>
                        </div>
                        @elseif($value->pay_type=='2')
                        <!--好看-->
                        <div class="mo_list_text">
                            <div class="mo_list_img">
                            @if(@$value->vcourse->cover)
                                <img src="{{ config('constants.admin_url').@$value->vcourse->cover}}" alt="" onerror="javascript:this.src='/images/error.jpg'"/>
                            @else
                                <img src="{{ config('qiniu.DOMAIN').@$value->vcourse->video_tran}}?vframe/jpg/offset/{{ config('qiniu.COVER_TIME')}}" alt="" onerror="javascript:this.src='/images/error.jpg'"/>
                            @endif
                            </div>
                            <div class="mo_list_title">{{ @str_limit(@$value->vcourse->title,20) }}</div>
                            <div class="mo_list_price">@if($value->free_flg=='2')￥{{$value->price}}@else免费@endif</div>
                            <div class="mo_list_people">{{ $value->vcourse->view_cnt or 0 }}人已观看</div>
                        </div>
                        @elseif($value->pay_type=='3')
                        <!--壹家壹-->
                        <div class="mo_list_text">
                            <div class="mo_list_img">
                            <img src="{{config('constants.admin_url').@$value->opo->picture}}" alt=""/>
                            </div>
                            <div class="mo_list_title">{{ @str_limit(@$value->opo->title,20) }}</div>
                            <div class="mo_list_price">@if($value->free_flg=='2')￥{{$value->price}}@else免费@endif</div>
                            <div class="mo_list_people">{{ $value->opo->purchase_num or 0 }}人已购买</div>
                        </div>
                        @elseif($value->pay_type=='6')
                        <!--和会员-->
                        @endif
                        <div class="mo_list_none">
                            <ul class="mo_list_none_list">
                                <li><span>{{$value->created_at}}</span>下单时间</li>
                                <li><span>x{{$value->quantity}}</span>商品数量</li>
                                <li><span>￥{{number_format($value->total_price,2)}}</span>商品总价</li>
                                <li><span>￥{{number_format(($value->point_price+$value->coupon_price),2)}}</span>优惠金额</li>
                                <li><span>￥{{number_format($value->balance_price,2)}}</span>余额减免</li>
                                <li><span class="orange">￥{{number_format($value->price,2)}}</span>实际支付金额</li>
                            </ul>
                        </div>
                        <div class="mo_list_button">
                            @if($value->pay_type=='1')
                                <div class="mo_list_button_detail" id="course_detail"  course_id="{{ $value->pay_id }}" >课程详情</div>
                            <div class="mo_list_button_service" data-tel="{{$value->course->tel}}" data-area="{{@$value->course->area->area_name}}">联系客服</div>
                            @elseif($value->pay_type=='3')
                                <div class="mo_list_button_detail" id="course_detail"  course_id="{{ $value->pay_id }}" >课程详情</div>
                            <div class="mo_list_button_service" data-tel="{{config('constants.opo_tel')}}"  data-area="壹家壹">联系客服</div>
                            @elseif($value->pay_type=='6')
                                @if(isset($value->order_vip)&&($value->order_vip->delivery_flg=='2')&&!empty($value->order_vip->delivery_com)&&!empty($value->order_vip->delivery_nu))
                                    <div class="mo_list_button_payment" onclick="location.href = 'http://m.kuaidi100.com/index_all.html?type={{$value->order_vip->delivery_com}}&postid={{$value->order_vip->delivery_nu}}&callbackurl={{route('my.orders')}}';">查看物流</div>
                                @endif
                            @endif
                            @if($value->order_type=='1')
                            <div class="mo_list_button_cancel" data-id="{{$value->id}}">取消订单</div><!--data-id为订单id-->
                                @if($value->pay_method=='1')
                                <div class="mo_list_button_payment" id="payment" data-id="{{$value->id}}" data-type="{{$value->pay_type}}">前往支付</div>
                                @else
                                <div class="mo_list_button_payment" id="payment_static">线下支付</div>
                                @endif
                            @endif

                        </div>
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
    $(".mo_list_text").click(function(){//展开订单详情
        $(".mo_list_none").hide();
        $(this).siblings(".mo_list_none").show();
    });


    var lockf = false;
    $(".mo_list_button_cancel").click(function(){//取消订单
        var id=$(this).data("id");
        Popup.init({
            popHtml:'<p>确认要删除该订单吗?</p>',
            popOkButton:{
                buttonDisplay:true,
                buttonName:"删除",
                buttonfunction:function(){
                        if (lockf) {return;}
                        lockf = true;
                        $.get("{{route('my.orders.cancel')}}",{ id:id },function(res){
                               if (res.code == 0) {
                                   xalert(res.message, '', function() { location.reload() });
                                   lockf = false;
                                } else {
                                   xalert(res.message);
                                   lockf = false;
                                }
                        },'json')
                }
            },
            popCancelButton:{
                buttonDisplay:true,
                buttonName:"取消",
                buttonfunction:function(){}
            },
            popFlash:{
                flashSwitch:false
            }
        });
    });
    //点击一键咨询
    $(".mo_list_button_service").click(function(){
        var tel = $(this).data('tel');
        Popup.init({
            popTitle:$(this).data('area')+'服务中心',//此处标题随情况改变，需php调用
            popHtml:'<p><span style="color:#ff9900;">'+$(this).data('tel')+'</span>是否立即拨打电话？</p>',//此处信息会涉及到变动，需php调用
            popOkButton:{
                buttonDisplay:true,
                buttonName:"是",
                buttonfunction:function(){
                    //此处填写拨打电话的脚本
                    window.location.href = 'tel://' + tel;
                }
            },
            popCancelButton:{
                buttonDisplay:true,
                buttonName:"否",
                buttonfunction:function(){}
            },
            popFlash:{
                flashSwitch:false
            }
        });
    });
    //点击前往支付
    $("#payment").click(function(){
        var pay_type = $(this).data('type');
        var id = $(this).data('id');
        if (pay_type=='1') {
            window.location.href = '{{route('wechat.course_pay')}}?id='+id;
        } else if(pay_type=='2'){
            window.location.href = '{{route('wechat.vcourse_pay')}}?id='+id;
        } else if(pay_type=='3'){
            window.location.href = '{{route('wechat.opo.pay')}}?id='+id;
        } else if(pay_type=='6'){
            window.location.href = '{{route('wechat.vip_pay')}}?id='+id;
        };
    });
    //线下支付
    $("#payment_static").click(function(){
        window.location.href = '{{route('course.line_pay_static')}}';
    });
    //线下支付
    $(".mo_list_button_detail").click(function(){
        var id = $(this).attr("course_id")
        window.location.href = '{{route('course.detail')}}/'+id;
    });
});
</script>
@endsection