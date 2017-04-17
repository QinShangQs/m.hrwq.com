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
            <div class="one_plus_one">
                <div class="opo_banner">
                    <div class="opo_banner_img"><img src="{{admin_url($opo->picture)}}" alt=""/></div>
                    <div class="opo_banner_div">
                        <div class="opo_banner_title">{{$opo->title}}</div>
                        <div class="opo_banner_p">{{$opo->project_title}}</div>
                        <div class="opo_banner_people">{{$opo->purchase_num}}人已购买</div>
                    </div>
                </div>
                <div class="opo_div1">
                    <div class="opo_div1_title">{{$opo->title}}</div>
                    <div class="opo_div1_price">￥{{$opo->price}}</div>
                    @if($order==null)
                        @if(!session('wechat_user'))
                        <a href="{{route('wechat.qrcode')}}" class="opo_div1_button">立即购买</a>
                        @else
                        <a href="{{route('opo.buy', ['id'=>$opo->id])}}" class="opo_div1_button">立即购买</a>
                        @endif
                    @elseif($order->pay_method==2)
                        <a href="{{route('course.line_pay_static')}}" class="opo_div1_button">待线下付款</a>
                    @else
                        <a href="{{route('wechat.opo.pay', ['id'=>$order->id])}}" class="opo_div1_button">继续付款</a>
                    @endif
                </div>
                <div class="opo_div">
                    <div class="opo_div_title">项目简介</div>
                    <div class="lcd_div_1" style="padding: 0 0">
                        <dl>
                            <dd>
                                @if($opo->project_intr)
                                    <div>{!! $opo->project_intr !!}</div>
                                @else
                                    <p>未填写</p>
                                @endif
                            </dd>
                        </dl>
                    </div>
                </div>
                <div class="opo_div">
                    <div class="opo_div_title">服务流程</div>
                    <div class="opo_div_img"><img src="/images/look/opo_div_img.png" alt=""/></div>
                </div>
                <div class="opo_div">
                    <div class="opo_div_title">热门评论</div>
                    <div class="opo_div_list">
                        <ul>
                            @if(count($comments))
                                @foreach($comments as $comment)
                                    <li>
                                        <div class="opo_div_list_img"><img src="{{url($comment->user->profileIcon)}}"
                                                                           alt=""/></div>
                                        <div class="opo_div_list_title">{{$comment->user->nickname}}</div>
                                        <!--需要链接直接加a标签就行-->
                                        <div class="opo_div_list_time">{{$comment->created_at->format('Y-m-d')}}</div>
                                        <!--需要链接直接加a标签就行-->
                                        <div class="opo_div_list_p">{{$comment->content}}</div><!--需要链接直接加a标签就行-->
                                        <div class="opo_div_list_zambia"
                                             data-id="{{$comment->id}}">{{$comment->likes?$comment->likes:''}} @if(count($comment->like_records)>0)
                                                <img src="/images/public/zambia_on.png" alt=""/>@else<img
                                                        src="/images/public/zambia.png" alt=""/>@endif</div>
                                    </li>
                                @endforeach
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
            @include('element.nav', ['selected_item' => 'nav3'])
        </div>
    </div>
@endsection

@section('script')
    <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js" type="text/javascript" charset="utf-8"></script>
    @include('element.share')
    <script type="text/javascript">
        $(document).ready(function () {
            //分享壹家壹
            wx.config(<?php echo $wx_js->config(array("onMenuShareAppMessage", "onMenuShareTimeline"), false) ?>);
            @if($share_flg=='1'&&$order)
            wx.ready(function () {
                wx.onMenuShareAppMessage({
                    title: '分享壹家壹有奖', // 分享标题
                    desc: '分享壹家壹获取爱心红包', // 分享描述
                    link: '{{route('opo')}}' + '?share_user={{$order->user_id}}', // 分享链接
                    imgUrl: '{{admin_url($opo->picture)}}', // 分享图标
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
                    title: '分享壹家壹有奖', // 分享标题
                    link: '{{route('opo')}}' + '?share_user={{$order->user_id}}', // 分享链接
                    imgUrl: '{{admin_url($opo->picture)}}', // 分享图标
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
                popTitle: '分享壹家壹',//此处标题随情况改变，需php调用
                popHtml: '<p>点击课程右上角将壹家壹发送给朋友，双方将得到爱心红包</p>',//此处信息会涉及到变动，需php调用
                popOkButton: {
                    buttonDisplay: true,
                    buttonName: "确认",
                    buttonfunction: function () {
                    }
                },
                popFlash: {
                    flashSwitch: false
                }
            });
            @else
            wx.ready(function () {
                wx.onMenuShareAppMessage({
                    title: '专家一对一咨询，定制家庭教育方案', // 分享标题
                    desc: '和润万青，让教育孩子变得简单', // 分享描述
                    link: '{{route('opo')}}', // 分享链接
                    imgUrl: '{{admin_url($opo->picture)}}', // 分享图标
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
                    title: '专家一对一咨询，定制家庭教育方案', // 分享标题
                    link: '{{route('opo')}}', // 分享链接
                    imgUrl: '{{admin_url($opo->picture)}}', // 分享图标
                    success: function () {
                        // 用户确认分享后执行的回调函数
                    },
                    cancel: function () {
                        // 用户取消分享后执行的回调函数
                    }
                });
            });
            @endif

            @if ($is_guest)
                $('.opo_div1_button').click(function() {
                    var url = $(this).attr('href');
                    Popup.init({
                        popHtml:'<p>您尚未注册，请先完成注册。</p>',
                        popOkButton:{
                            buttonDisplay:true,
                            buttonName:"去注册",
                            buttonfunction:function(){
                                window.location.href='/user/login?url='+ encodeURIComponent(url);
                                return false;
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
                });
            @endif

            var locks = false;
            $(".glr_button1").click(function () {//点击立即领取
                /*-----ajax开始-----*/
                if (locks) {
                    return false;
                }
                locks = true;
                //给glr_prize重新赋值
                $.ajax({
                    type: 'post',
                    url: '{{route('opo.get_coupon')}}',
                    data: {id: '{{$opo->id}}', user_id: '{{$share_user}}'},
                    success: function (res) {
                        if (res.code == 0) {
                            $(".glr_div1").hide();
                            $(".glr_div2").show();
                            locks = false;
                        } else {
                            Popup.init({
                                popTitle: '失败',
                                popHtml: '<p>' + res.message + '</p>',
                                popFlash: {
                                    flashSwitch: true,
                                    flashTime: 3000,
                                }
                            });
                            locks = false;
                        }
                    }
                });
                //返回事件结束
                /*-----ajax结束-----*/
            });
            $(".glr_button2").click(function () {//点击立即领取
                location.href = '{{route('user.coupon')}}';
            });

            var lock = false;
            $(".opo_div_list_zambia").click(function () {//点赞
                var comment_id = $(this).attr("data-id");
                var _self = $(this);
                if (lock) return;
                lock = true;
                $.ajax({
                    type: 'post',
                    url: '{{route('opo.comment.like', ['id'=>$opo->id])}}',
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
                        lock = false;
                    }
                });
            });
        });
    </script>
@endsection
