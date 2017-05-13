@extends('layout.default')

@section('content')
    <div id="subject">
        <div id="main">
            <div class="good_asking">
                <div class="ga_tutor_details">
                    <div class="gatd_div">
                        <div class="gatd_img" style="background:url({{asset($data->tutor_cover)}}) center no-repeat; background-size:100%;"></div>
                        <div class="gatd_name">{{$data->realname or $data->nickname}} <span>{{$data->tutor_honor}}</span></div>
                        <ul class="gatd_information">
                            <li class="gatd_information_1"><img src="/images/ask/ga_div_1_list_information_1.png" alt=""/> {{$data->question->count()}}人提问</li>
                            <li class="gatd_information_2"><img src="/images/ask/ga_div_1_list_information_2.png" alt=""/> <span id="user_favor_num">{{$data->user_favor->count()}}人收听</span></li>
                        </ul>
                        <div class="clearboth"></div>
                        @if($data->id != user_info('id'))
                            <div class="lcd_tune @if($is_favor) lcd_tune_yes  @else lcd_tune_no @endif"></div><!--lcd_tune_no是未收听，lcd_tune_yes是已收听，需要链接就把div改成a标签-->
                        @endif
                    </div>
                    <div class="gatd_div">
                        <div class="gatd_price"><div><span>￥{{$data->tutor_price or '0.00'}}</span>/次</div>在线问题解答</div>
                    </div>
                    <div class="gatd_div">
                        <div class="gatd_title"><div>指导师简介</div></div>
                        <div class="gatd_p">
                            <p>{{$data->tutor_introduction}}</p>
                        </div>
                    </div>
                    <div class="gatd_div">
                        <div class="gatd_title"><div>历史解答</div></div>
                        @foreach($data->question as $question)
                        <dl class="gatd_list">
                            <dt>
                                <div class="gatd_list_img"><img src="{{asset($question->ask_user->profileIcon)}}" alt=""/></div>
                                <div class="gatd_list_title">问题：{{$question->content}}</div>
                                <div class="gatd_list_p">￥{{$question->price or '0.00'}}</div>
                            </dt>
                            <dd>
                                <div class="gatd_list_img"><img src="{{asset($question->answer_user->profileIcon)}}" alt=""/></div>

                                @if($question->audio_type!=1)
                                    <div class="ga_div_2_list_answer_voice audio_can_play" style="cursor:pointer;" aid="{{$question->audio_type}}" qid="{{$question->id}}">{{ $question->audio_msg }}
                                        <audio data-src="{{config('qiniu.DOMAIN').$question->answer_url}}"></audio>
                                    </div>
                                @else
                                    <div class="ga_div_2_list_answer_voice audio_cant_play" style="cursor:pointer;" qid="{{$question->id}}">{{ $question->audio_msg }}
                                    </div>
                                @endif


                                <div class="ga_div_2_list_answer_time">{{$question->voice_long}}"</div>
                                <div class="ga_div_2_list_answer_people" style="left: 250px;top:11px;">{{$question->listener_nums}}人旁听</div>
                            </dd>
                        </dl>
                        @endforeach
                    </div>

                    <!--指导师不能对自己提问题，不能对自己关注收听-->
                    @if($data->id != user_info('id'))
                        <div class="gatd_ask">向Ta提问</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" name="requestUri" id="requestUri" value="{{$requestUri}}">
@endsection

@section('script')
    <script type="text/javascript" src="/js/audio_play.js"></script>
    <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js" type="text/javascript" charset="utf-8"></script>
    <script>
        $(document).ready(function(){
            wx.config(<?php echo $wx_js->config(array("onMenuShareAppMessage", "onMenuShareTimeline"),false) ?>);
            wx.ready(function(){
                wx.onMenuShareAppMessage({
                    title: '中国好家长{{$data->realname or $data->nickname}}，等你来问', // 分享标题
                    desc: '好家长是怎么教育孩子的？快来听一听', // 分享描述
                    link: '{{route('question.teacher',['id'=>$data->id])}}?from=singlemessage', // 分享链接
                    imgUrl: '{{url($data->profileIcon)}}', // 分享图标
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
                    title: '中国好家长{{$data->realname or $data->nickname}}，等你来问', // 分享标题
                    link: '{{route('question.teacher',['id'=>$data->id])}}?from=singlemessage', // 分享链接
                    imgUrl: '{{url($data->profileIcon)}}', // 分享图标
                    success: function () {
                        // 用户确认分享后执行的回调函数
                    },
                    cancel: function () {
                        // 用户取消分享后执行的回调函数
                    }
                });
            });
            /*banner 图高度适应*/
            $('.gatd_img').height($('.gatd_img').width()*34/75);
            @if(request('come_from')=='tutor_center' && session('user_info')['id']==$data->id)
            Popup.init({
                popHtml: '分享给微信好友或朋友圈，邀请朋友来提问',
                popOkButton:{
                    buttonDisplay:true,
                    buttonName:"我知道了",
                    buttonfunction:function(){
                    }
                }
            });
            @endif

            /*----------音频播放---------*/
            audioPlay.init({
                url:'{{route('question.question_listen')}}'
            });

            /*-----------需支付跳转到详情页操作------*/
            $(document).on('click','.audio_cant_play',function(){
                @if(!session('wechat_user'))
                    window.location.href = '{{route('wechat.qrcode')}}';return;
                @endif
                var qid = $(this).attr('qid');
                location.href  = '{{route('wechat.question')}}?id='+qid;
            });

            //点击收听/取消收听
            $(".lcd_tune").click(function(){
                @if(!session('wechat_user'))
                    window.location.href = '{{route('wechat.qrcode')}}';return;
                @endif
                var that = this;
                var tid = '{{$data->id}}';
                $.ajax({
                    type: 'post',
                    url: '{{route('question.teacher_favor')}}',
                    data: {tid:tid},
                    dataType: 'json',
                    success:function(res){
                        var code = res.code;
                        var user_favor_num = $('#user_favor_num').html()*1;

                        if(code == 0)
                        {
                            $('#user_favor_num').html(user_favor_num-1)
                        }else if(code == 2){
                            $('#user_favor_num').html(user_favor_num+1)
                        }

                        $(that).toggleClass("lcd_tune_yes").toggleClass("lcd_tune_no");
                        Popup.init({
                            popHtml:res.message,
                            popFlash:{
                                flashSwitch:true,
                                flashTime:2000
                            }
                        });
                    }
                });
            });

            //向ta提问
            $(".gatd_ask").click(function(){
                @if(!session('wechat_user'))
                    window.location.href = '{{route('wechat.qrcode')}}';return;
                @endif
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
                    location.href="{{route('question.ask_question',['id'=>$data->id])}}";
                }
            });
        });
    </script>
@endsection
