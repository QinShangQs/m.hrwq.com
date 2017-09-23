@extends('layout.default')
@section('content')
<div id="subject">
    <div id="main">
        @if($share_user)
            <div class="popupWindow" style="cursor:pointer;vertical-align:middle">
                <div class="popupWindow_hp"></div>
                <div class="popupWindow_frame" style="background:url() left top repeat;top:30%">
                    <div class="glr_div1">
                        <div class="glr_close"><img src="/images/look/glr_close.png" alt=""/></div>
                        <div class="glr_button glr_button1">立即领取</div>
                    </div>
                    <div class="glr_div2" style="display:none;">
                        <div class="glr_close"><img src="/images/look/glr_close.png" alt=""/></div>
                        <div class="glr_prize">红包</div>
                        <a href="{{route('user.coupon')}}" class="glr_button glr_button2">我的优惠券</a>
                    </div>
                </div>
            </div>
        @endif
        <input type="hidden" name="id" id="id" value="{{$course->id}}">
        <input type="hidden" name="requestUri" id="requestUri" value="{{$requestUri}}">

        <div class="look_charge_details">
            <div class="lcd_banner">
                <div class="lcd_banner_img"><img src="{{$course->picture}}" alt=""/></div>
                <div class="lcd_banner_div">
                    <div class="lcd_banner_title">{{$course->title}}</div>
                    <div class="lcd_banner_span">
                        @if($course->type == 1)
                            <span class="lcd_banner_span_1">免费</span>
                        @else
                            <span class="lcd_banner_span_1">¥{{$course->price}}</span> 
                            <span class="lcd_banner_span_2">¥{{$course->original_price}}</span> 
                        @endif
                    </div>
                </div>
            </div>
            <ul class="lcd_tab">
                <li id="lcd_tab_1" class="selected">课程详情</li>
                <li id="lcd_tab_2">评价</li>
                <li id="lcd_tab_3">推荐课程</li>
            </ul>
            <div class="lcd_div">
                <div class="lcd_div_1">
                    <dl>
                        @if($course->course_date) 
                        <dt>课程时间</dt>
                        <dd><p>
                            {{$course->course_date}}</p></dd>
                         @endif
                        <dt>具体地址</dt>
                        <dd><p>@if($course->course_addr){{$course->course_addr}}@else 未填写 @endif</p></dd>
                        <dt>适合对象</dt>
                        <dd><p>@if($course->suitable){!! nl2br($course->suitable) !!}@else 未填写 @endif</p></dd>
                        <dt>老师介绍</dt>
                        <dd>
                            @if($course->teacher_intr)
                            <div>{!! nl2br($course->teacher_intr) !!}</div>
                            @else
                            <p>未填写</p>
                            @endif
                        </dd>
                        <dt>课程安排</dt>
                        <dd>
                            @if($course->course_arrange)
                            <article>{!! nl2br($course->course_arrange) !!}</article>
                            @else
                            <p>未填写</p>
                            @endif
                        </dd>
                    </dl>
                </div>
                <div class="lcd_div_2" style="display:none;">
                    <ul class="lcd_div_2_list">
                        @foreach($course_comments as $course_comment)
                        <li>
                            <div class="lcd_div_2_list_img"><img src="{{url($course_comment->user->profileIcon)}}" alt=""/></div>
                            <div class="lcd_div_2_list_title">{{$course_comment->user->nickname}}</div><!--需要链接直接加a标签就行-->
                            <div class="lcd_div_2_list_time">{{$course_comment->created_at}}</div><!--需要链接直接加a标签就行-->
                            <div class="lcd_div_2_list_p">{{$course_comment->content}}</div><!--需要链接直接加a标签就行-->
                            <div class="opo_div_list_zambia"
                                 data-id="{{$course_comment->id}}">{{$course_comment->likes?$course_comment->likes:''}} @if(count($course_comment->like_records)>0)
                                    <img src="/images/public/zambia_on.png" alt=""/>@else<img
                                            src="/images/public/zambia.png" alt=""/>@endif</div>
                        </li>
                        @endforeach
                    </ul>
                </div>
                <div class="lcd_div_3" style="display:none;">
                    <ul>
                        @foreach($recommend_courses as $recommend_course)
                        <li>
                            <dl>
                                <dt><a href="{{ route('course.detail',['id'=>$recommend_course->id]) }}"><img src="@if($recommend_course->picture){{$recommend_course->picture}}@endif" alt=""/></a></dt>
                                <dd><a href="{{ route('course.detail',['id'=>$recommend_course->id]) }}">{{$recommend_course->title}}</a></dd>
                            </dl>
                        </li>
                        @endforeach
                    </ul>
                    <div class="clearboth"></div>
                </div>
            </div>
            <!--lcd_collection_no是未收藏，lcd_collection_yes是已收藏-->
            <div id="is_favor" @if($userfavor) class="lcd_collection lcd_collection_yes" @else class="lcd_collection lcd_collection_no" @endif></div>
            
            <!-- 评论 参加过该课程，方可进行评论-->
            @if($orderPaid)

            @endif
            <a class="lcd_evaluate lcd_evaluate_fa" href="{{ route('course.comment',['id'=>$course->id]) }}">评论</a>
            <!--免费课程没有一键咨询-->
            @if($course->type == 1)
                @if(!$order && !$orderPaid)
                <div class="lcd_button">参加该课程</div>
                @endif
            @else
                @if($order&&$order->order_type=='1'&&$order->pay_method=='1')
                <div class="lcd_button1" onclick="location.href = '{{route('wechat.course_pay')}}?id={{$order->id}}';">去付款</div>
                @elseif($order&&$order->order_type=='1'&&$order->pay_method=='2')
                <div class="lcd_button1" onclick="window.location.href = '{{route('course.line_pay_static')}}';">待线下付款</div>
                @else
                <div class="lcd_button1" id="course_add">参加该课程</div><!--当未参加时显示此项-->
                @endif
                <div class="lcd_button_consult">一键咨询</div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js" type="text/javascript" charset="utf-8"></script>
@include('element.share')
<script type="text/javascript" src="{{ url('/js/ueditor.parse.min.js') }}?r=1"></script>
<script type="text/javascript">
$(document).ready(function(){
	if($("iframe").length > 0){
		$("iframe").width('100%')
	}
});
                
    var subscribe = '{{$subscribe}}';
    $(document).ready(function(){
	uParse('article');
        //分享课程
        @if($share_flg=='1')
            wx.config(<?php echo $wx_js->config(array("onMenuShareAppMessage", "onMenuShareTimeline"),false) ?>);
            wx.ready(function(){
                wx.onMenuShareAppMessage({
                    title: '分享课程有奖', // 分享标题
                    desc: '分享课程获取爱心红包', // 分享描述
                    link: '{{route('course.detail',['id'=>$course->id])}}'+'?share_user={{$user_id}}&from=singlemessage', // 分享链接
                    imgUrl: '{{url($course->picture)}}', // 分享图标
                    type: '', // 分享类型,music、video或link，不填默认为link
                    dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                    success: function () { 
                        // 用户确认分享后执行的回调函数
                        shared();
                    },
                    cancel: function () { 
                        // 用户取消分享后执行的回调函数
                    }
                });
                wx.onMenuShareTimeline({
                    title: '分享课程有奖', // 分享标题
                    link: '{{route('course.detail',['id'=>$course->id])}}'+'?share_user={{$user_id}}&from=singlemessage', // 分享链接
                    imgUrl: '{{url($course->picture)}}', // 分享图标
                    success: function () {
                        // 用户确认分享后执行的回调函数
                        shared();
                    },
                    cancel: function () {
                        // 用户取消分享后执行的回调函数
                    }
                });

            });
            Popup.init({
                popTitle:'分享课程',//此处标题随情况改变，需php调用
                popHtml:'<p>点击课程右上角将课程发送给朋友，双方将得到爱心红包</p>',//此处信息会涉及到变动，需php调用
                popOkButton:{
                    buttonDisplay:true,
                    buttonName:"确认",
                    buttonfunction:function(){
                    }
                },
                popFlash:{
                    flashSwitch:false
                }
            });
        @else
            wx.config(<?php echo $wx_js->config(array("onMenuShareAppMessage", "onMenuShareTimeline"),false) ?>);
            wx.ready(function(){
                wx.onMenuShareAppMessage({
                    title: '{!! strip_tags($course->title)!!}', // 分享标题
                    desc: '汇聚顶尖教子智慧,和润万青助您成就卓越孩子!', // 分享描述
                    link: '{{route('course.detail',['id'=>$course->id])}}?from=singlemessage', // 分享链接
                    imgUrl: '{{url($course->picture)}}', // 分享图标
                    type: '', // 分享类型,music、video或link，不填默认为link
                    dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                    success: function () { 
                        // 用户确认分享后执行的回调函数
                    },
                    cancel: function () { 
                        // 用户取消分享后执行的回调函数
                    }
                });
                wx.onMenuShareTimeline({
                    title: '{!! strip_tags($course->title)!!}', // 分享标题
                    link: '{{route('course.detail',['id'=>$course->id])}}?from=singlemessage', // 分享链接
                    imgUrl: '{{url($course->picture)}}', // 分享图标
                    success: function () {
                        // 用户确认分享后执行的回调函数
                    },
                    cancel: function () {
                        // 用户取消分享后执行的回调函数
                    }
                });

            });
        @endif

        $(".good_looking_red").height($(document).height());
        $(".good_looking_red").width($(document).width());
        $(".glr_close").click(function(){//关闭弹窗
            $(".good_looking_red").hide();
            Popup.Close();
        });
        var locks = false;
        $(".glr_button1").click(function(){//点击立即领取
            /*-----ajax开始-----*/
             if(locks) {return false;}
             locks = true;
            //给glr_prize重新赋值
            $.ajax({
                type: 'post',
                url: '{{route('course.get_coupon')}}',
                data: {id: '{{$course->id}}',user_id: '{{$share_user}}'},
                success: function (res) {
                    if (res.code == 0) {
                        $(".glr_div1").hide();
                        $(".glr_div2").show();
                        locks = false;
                    }else {
                        Popup.init({
                            popTitle:'失败',
                            popHtml:'<p>'+res.message+'</p>',
                            popFlash:{
                                flashSwitch:true,
                                flashTime:3000,
                            }
                        });
                        locks = false;
                    }
                }
            });
            //返回事件结束
            /*-----ajax结束-----*/
        });
        $(".glr_button2").click(function(){//点击立即领取
            location.href='{{route('user.coupon')}}';
        });
        //点击一键咨询
        $(".lcd_button_consult").click(function(){
            @if(@$course->tel)
            Popup.init({
                popTitle:'服务中心',//此处标题随情况改变，需php调用
                popHtml:'<p><span style="color:#ff9900;">{{$course->getTel()}}</span>是否立即拨打电话？</p>',//此处信息会涉及到变动，需php调用
                popOkButton:{
                    buttonDisplay:true,
                    buttonName:"是",
                    buttonfunction:function(){
                        //此处填写拨打电话的脚本
                        window.location.href = 'tel://' + '{{$course->getTel()}}';
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
            @else
            Popup.init({
                popTitle:'全国服务中心',//此处标题随情况改变，需php调用
                popHtml:'<p><span style="color:#ff9900;">{{config('constants.opo_tel')}}</span>是否立即拨打电话？</p>',//此处信息会涉及到变动，需php调用
                popOkButton:{
                    buttonDisplay:true,
                    buttonName:"是",
                    buttonfunction:function(){
                        //此处填写拨打电话的脚本
                        window.location.href = 'tel://' + '{{config('constants.opo_tel')}}';
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
            @endif
        });

        //点击参加该课程（免费）
        $(".lcd_button").click(function(){
            if ( subscribe == '0') {
            window.location.href = '{{route('wechat.qrcode')}}';
            } else {
		var $this = $(this);
                var session_mobile = '{{$session_mobile}}';
                if (session_mobile == '') {
                    //ajax成功返回事件开始
                    //如果信息不完善执行开始（2选1）
                    Popup.init({
                        popHtml:'<p>您尚未注册，请先完成注册。</p>',
                        popOkButton:{
                            buttonDisplay:true,
                            buttonName:"去注册",
                            buttonfunction:function(){
                                //此处填写信息不完善的时候的跳转信息
                                var requestUri = $('#requestUri').val();
                                window.location.href='/user/login?url='+requestUri;
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
                    return false;
                    //如果信息不完善执行结束
                }else{
		    if ($this.hasClass('disabled')) return false;
		    $this.addClass('disabled');
                    /*----------ajax开始----------*/
                    var id = $('#id').val();
                    $.ajax({
                        type: 'post',
                        url: '{{route('course.join_free')}}',
                        data: {id: id},
                        success: function (res) {
			    $this.removeClass('disabled');
                            if (res.code == 0) {
                                //ajax成功返回事件开始
                                // Popup.init({
                                //     popTitle:'报名成功',
                                //     popHtml:'<p>已向您的微信号发送二维码，此验证码作为参加活动的凭证！</p>',
                                //     popFlash:{
                                //         flashSwitch:true,
                                //         flashTime:3000,
                                //     }
                                // });
                                // window.location.reload();

                                Popup.init({
                                    popTitle:'报名成功',
                                    popHtml:'<p>已向您的微信号发送二维码，此验证码作为参加活动的凭证！</p>',
                                    popOkButton:{
                                        buttonDisplay:true,
                                        buttonName:"确定",
                                        buttonfunction:function(){
                                            window.location.reload();
                                        }
                                    },
                                    popFlash:{
                                        flashSwitch:false
                                    }
                                });

                                
                                //返回成功后应跳转页面
                                //ajax成功返回事件结束
                            }else {
                                Popup.init({
                                    popTitle:'失败',
                                    popHtml:'<p>'+res.message+'</p>',
                                    popFlash:{
                                        flashSwitch:true,
                                        flashTime:3000,
                                    }
                                });
                            }
                        }
                    });
                        
                    /*----------ajax结束----------*/
                }
            }
        });

        //点击参加该课程（收费）
        $("#course_add").click(function(){
            if ( subscribe == '0') {
                window.location.href = '{{route('wechat.qrcode')}}';
            } else {
                var session_mobile = '{{$session_mobile}}';
                if (session_mobile == '') {
                    //ajax成功返回事件开始
                    //如果信息不完善执行开始（2选1）
                    Popup.init({
                        popHtml:'<p>您尚未注册，请先完成注册。</p>',
                        popOkButton:{
                            buttonDisplay:true,
                            buttonName:"去注册",
                            buttonfunction:function(){
                                //此处填写信息完善的跳转信息
                                var requestUri = $('#requestUri').val();
                                window.location.href='/user/login?url='+requestUri;
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
                    return false;
                    //如果信息不完善执行结束
                }else{
                    //返回成功后应跳转页面
                    window.location.href='{{ route('course.join_charge',['id'=>$course->id]) }}';
                }
            } 
        });

        //tab切换
        $(".lcd_tab li").click(function(){
            if($(this).attr("class")!="selected"){
                $(".lcd_tab li").attr("class","");
                $(this).attr("class","selected");
                $(".lcd_div>div").hide();
                switch($(this).attr("id")){
                    case "lcd_tab_1":
                        $(".lcd_div_1").show();
                        break;
                    case "lcd_tab_2":
                        $(".lcd_div_2").show();
                        break;
                    case "lcd_tab_3":
                        $(".lcd_div_3").show();
                        $('.lcd_div_3 img').height($('.lcd_div_3 img').width()*91/172)
                        break;
                    default:
                        break;
                }
            }
        });
        
        //点击收藏
        var lock = false;
        $("#is_favor").click(function(){
            @if(!session('wechat_user'))
                window.location.href = '{{route('wechat.qrcode')}}';
            @endif
            if(lock) {return false;}
            /*----------ajax开始----------*/
            var id = $('#id').val();
            lock = true;
            $.ajax({
                type: 'post',
                url: '{{route('course.collection')}}',
                data: {id: id},
                success: function (res) {
                    if (res.code == 0) {
                        //ajax成功返回事件开始
                        $("#is_favor").attr("class","lcd_collection lcd_collection_no");
                        lock = false;
                        //ajax成功返回事件结束
                    }else if(res.code == 2){
			Popup.init({
                            popHtml: '您已成功收藏该课程，可在 <b>我的</b>-<b>我的课程</b> 中查看',
                            popFlash:{
                                flashSwitch:true,
                                flashTime:2000
                            }
                        });
                        $("#is_favor").attr("class","lcd_collection lcd_collection_yes");
                        lock = false;
                    }else {
                        Popup.init({
                            popTitle:'失败',
                            popHtml:'<p>'+res.message+'</p>',
                            popFlash:{
                                flashSwitch:true,
                                flashTime:3000,
                            }
                        });
                        lock = false;
                    }
                }
            });
                
            /*----------ajax结束----------*/
        });

        //点赞
        var like_lock = false;
        $(".opo_div_list_zambia").click(function () {//点赞
            var comment_id = $(this).data("id");
            var _self = $(this);
            if (like_lock) return;
            like_lock = true;
            $.ajax({
                type: 'post',
                url: '{{route('course.comment.like', ['id'=>$course->id])}}',
                data: {"comment_id": comment_id},
                success: function (res) {
                    Popup.init({
                        popHtml: res.message,
                        popFlash: {
                            flashSwitch: true,
                            flashTime: 2000
                        }
                    });
                    if (res.code == 0) {
                        _self.html(res.data + ' <img src="/images/public/zambia_on.png" alt=""/>');
                    }
                    like_lock = false;
                }
            });
        });

    });
</script>
@endsection
