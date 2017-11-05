@extends('layout.default')

@section('content')
    <div id="subject">
        <div id="main">
            <div class="good_asking">
                <div class="ga_cooperation_details">
                    <div class="gacd_div">
                        <div class="gacd_div_1">
                            <div class="gacd_div_1_img"><img src="{{asset($talk->ask_user->profileIcon)}}" alt=""/></div>
                            <div class="gacd_div_1_title">{{$talk->ask_user->realname or $talk->ask_user->nickname}}</div>
                            <div class="gacd_div_1_address">来自  {{@$talk->ask_user->c_city->area_name}}  {{config('constants.user_label')[$talk->ask_user->label]}}</div>
                        </div>
                    </div>
                    <div class="gacd_div">
                        <div class="gacd_div_2">
                            <div class="gacd_div_2_title">
                                @foreach($talk->tags as $tag)
                                   <span>#{{$tag->title}}#</span>
                                @endforeach
                                {{$talk->title}}</div>
                            <div class="gacd_div_2_time"><span>{{$talk->view}}人已看</span>{{$talk->created_at}}</div>
                            <div class="gacd_div_2_p"><pre style="white-space:pre-wrap;white-space:-moz-pre-wrap;white-space:-pre-wrap;white-space:-o-pre-wrap;word-wrap:break-word;">{!! replace_em($talk->content) !!}</pre></div>
                        </div>
                    </div>
                    <div class="gacd_div">
                        <div class="gacd_title"><div>{{count($talk->comments)>0 ? '全部回复' : '暂无回复'}}</div></div>
                        <ul class="gacd_div_list">
                            @foreach($talk->comments as $comment)
                                <li>
                                    <div class="gacd_div_list_img"><img src="{{url($comment->answer_user->profileIcon)}}" alt=""/></div>
                                    <div class="gacd_div_list_title">{{$comment->answer_user->realname or $comment->answer_user->nickname}}</div>
                                    <div class="gacd_div_list_time">{{date('Y-m-d',strtotime($comment->created_at))}}</div>
                                    <div class="gacd_div_list_p">{{$comment->comment_c}}</div>
                                    <div class="gacd_div_list_zambia" data-id="{{$comment->id}}"><span id='like_{{$comment->id}}'>{{$comment->likes?$comment->likes:''}}</span> @if(count($comment->like_record)>0)<img src="/images/public/zambia_on.png" alt=""/>@else<img src="/images/public/zambia.png" alt=""/>@endif</div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @if(!session('wechat_user'))
            <a class="lcd_evaluate lcd_evaluate_fa" href="{{ route('wechat.qrcode')}}">评论</a>
            @else
            <a class="lcd_evaluate lcd_evaluate_fa" href="{{ route('question.talk_comment',['id'=>$talk->id])}}">评论</a>
            @endif
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function(){
            var lock=false;
            $(".gacd_div_list_zambia").click(function(){//点赞
                var value=$(this).attr("data-id");
                var likes=$(this).attr("likes");
                if (lock) {return;}
                lock = true;
                $.get("{{route('question.talk_comment_like')}}",{ id:value },function(res){
                    Popup.init({
                        popHtml:res.message,
                        popFlash:{
                            flashSwitch:true,
                            flashTime:2000
                        }
                    });

                    if (res.code == 0) {
                        //ajax成功返回事件开始
                        var a = $("#like_"+value).text();
                        if (a=='') {a=0;}
                        $("#like_"+value).text(parseInt(a)+1);
                        $("#like_"+value).next('img').attr('src','/images/public/zambia_on.png');
                    }
                    lock = false;
                },'json')
            });

            @if ($is_guest)
                $('.lcd_evaluate').click(function() {
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



            wx.config(<?php echo $wx_js->config(array("onMenuShareAppMessage", "onMenuShareTimeline"), false) ?>);
            wx.ready(function () {
                wx.onMenuShareAppMessage({
                    title: '{{$talk->title}}', // 分享标题
                    desc: '【中国好家长交流圈】{{$talk->title}}', // 分享描述
                    //link: '{{route('course')}}?from=singlemessage', // 分享链接
                    link: $.UrlUpdateParams(window.location.href, "from", 'singlemessage'), // 分享链接
                    imgUrl: '{{url('/images/my/my_about_us_img.png')}}', // 分享图标
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
                    title: '{{$talk->title}}', // 分享标题
                    link: $.UrlUpdateParams(window.location.href, "from", 'singlemessage'), // 分享链接
                    imgUrl: '{{url('/images/my/my_about_us_img.png')}}', // 分享图标
                    success: function () {
                        // 用户确认分享后执行的回调函数
                    },
                    cancel: function () {
                        // 用户取消分享后执行的回调函数
                    }
                });
            });
        });
    </script>
@endsection
