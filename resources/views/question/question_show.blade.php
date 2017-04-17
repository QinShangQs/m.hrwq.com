@extends('layout.default')

@section('content')
    <div id="subject">
        <div id="main">
            <div class="good_asking">
                <div class="ga_free_details">
                    <div class="gafd_div">
                        <dl class="gafd_problem">
                            <dt>
                            <div class="gafd_problem_img"><img src="{{asset($question->ask_user->profileIcon)}}"
                                                               alt=""/></div>
                            <div class="gafd_problem_price">￥{{$question->price or '0.00'}}</div>
                            <div class="gafd_problem_name">{{$question->ask_user->realname or $question->ask_user->nickname}}</div>
                            <div class="gafd_problem_problem">问题：{{$question->content}}</div>
                            <div class="gafd_problem_time">{{$question->created_at}}</div>
                            </dt>
                            @if($question->answer_flg == 2)
                                <dd>
                                <a href="{{route('question.teacher',['id'=>$question->answer_user->id])}}">
                                    <div class="gafd_problem_img"><img
                                                src="{{asset($question->answer_user->profileIcon)}}" alt=""/></div></a>
                                    @if($question->audio_type!=1)
                                        <div class="ga_div_2_list_answer_voice audio_can_play" style="cursor:pointer;"
                                             aid="{{$question->audio_type}}"
                                             qid="{{$question->id}}">{{ $question->audio_msg }}
                                            <audio data-src="{{config('qiniu.DOMAIN').$question->answer_url}}"></audio>
                                        </div>
                                    @else
                                        <div class="ga_div_2_list_answer_voice audio_cant_play" style="cursor:pointer;"
                                             qid="{{$question->id}}">{{ $question->audio_msg }}
                                        </div>
                                    @endif

                                    <div class="ga_div_2_list_answer_time">{{$question->voice_long}}"</div>
                                    <div class="ga_div_2_list_answer_time2">{{format_date(strtotime($question->answer_date))}}</div>
                                </dd>
                            @endif
                        </dl>
                    </div>
                    <div class="gafd_div">
                        <div class="gafd_character">
                         <a href="{{route('question.teacher',['id'=>$question->answer_user->id])}}">
                            <div class="gafd_character_img"><img src="{{url($question->answer_user->profileIcon)}}"
                                                                 alt=""/></div></a>
                            <div class="gafd_character_name">{{$question->answer_user->realname or $question->answer_user->nickname}}</div>
                            <div class="gafd_character_post">{{$question->answer_user->tutor_honor}}</div>
                            <ul class="gafd_character_information">
                                <li class="gafd_character_information_1"><img
                                            src="/images/ask/ga_div_1_list_information_1.png"
                                            alt=""> {{$question->answer_user->question->count()}}</li>
                                <li class="gafd_character_information_2"><img
                                            src="/images/ask/ga_div_1_list_information_2.png"
                                            alt=""> {{$question->answer_user->user_favor->count()}}</li>
                            </ul>
                            <div class="clearboth"></div>
                            <a href="{{route('question.teacher', ['id'=>$question->answer_user->id])}}" class="gafd_character_more"><img src="/images/public/select_right.jpg" alt=""/></a>
                        </div>
                    </div>
                    @if($hot_question)
                    <div class="gafd_div">
                        <div class="gafd_title">
                            <div>为您推荐</div>
                        </div>
                        <div class="gafd_3_title"><a href="{{route('wechat.question')}}?id={{$hot_question->id}}"><img
                                        src="/images/public/select_right.jpg" alt=""/>问题：{{$hot_question->content}}</a>
                        </div>
                        <div class="gafd_3_p">
                            <p>{{$hot_question->ask_user->realname or $hot_question->ask_user->nickname}}
                                &nbsp; {{$hot_question->answer_user->tutor_honor}}</p>
                            <p>{{format_date(strtotime($hot_question->answer_date))}}回答 {{$hot_question->listener_nums}}
                                人已听</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js" type="text/javascript" charset="utf-8"></script>
    <script type="text/javascript" src="/js/audio_play.js"></script>
    <script>
        var order_id, order_question_id;

        $(document).ready(function () {
            wx.config(<?php echo Wechat::js()->config(array('chooseWXPay','onMenuShareAppMessage', 'onMenuShareTimeline'), false) ?>);
            wx.ready(function(){
                wx.onMenuShareAppMessage({
                    title: '我听到一个教育孩子的绝妙问答，来旁听下吧', // 分享标题
                    desc: '{{$question->content}}', // 分享描述
                    link: '{{route('wechat.question')}}?id={{$question->id}}?from=singlemessage', // 分享链接
                    @if($question->answer_flg == 2)
                    imgUrl: '{{asset($question->answer_user->profileIcon)}}', // 分享图标
                    @endif
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
                    title: '我听到一个教育孩子的绝妙问答，来旁听下吧', // 分享标题
                    link: '{{route('wechat.question')}}?id={{$question->id}}?from=singlemessage', // 分享链接
                    @if($question->answer_flg == 2)
                    imgUrl: '{{asset($question->answer_user->profileIcon)}}', // 分享图标
                    @endif
                    success: function () {
                        // 用户确认分享后执行的回调函数
                    },
                    cancel: function () {
                        // 用户取消分享后执行的回调函数
                    }
                });

            });

            /*----------音频播放---------*/
            audioPlay.init({
                url: '{{route('question.question_listen')}}'
            });

            /*-----------需支付跳转到详情页操作------*/
            $(document).on('click', '.audio_cant_play', function () {
                var qid = $(this).attr('qid');

                $.ajax({
                    url: '{{route('wechat.question_listen_pay')}}',
                    type: 'post',
                    dataType: "json",
                    data: {qid: qid},
                    success: function (res) {
                        if (res.code == 0) {
                            var config = res.data.config;
                            var order = res.data.order;
                            order_id = order.id;
                            order_question_id = order.pay_id;
                            wx.chooseWXPay({
                                timestamp: config.timestamp,
                                nonceStr: config.nonceStr,
                                package: config.package,
                                signType: config.signType,
                                paySign: config.paySign, // 支付签名
                                success: function () {
                                    setInterval(checkStatus, 1000);
                                }
                            });
                        } else {
                            Popup.init({
                                popHtml: res.message,
                                popFlash: {
                                    flashSwitch: true,
                                    flashTime: 2000
                                }
                            });
                        }
                    }
                })
            });
        });

        function checkStatus() {
            $.ajax({
                url: "{{route('wechat.status')}}",
                type: "post",
                dataType: "json",
                data: {order_id: order_id},
                success: function (res) {
                    if (res.code == 0 && res.data == "2") {
                        location.href = '{{route('wechat.question')}}?id=' + order_question_id;
                    }
                }
            });
        }
    </script>
@endsection
