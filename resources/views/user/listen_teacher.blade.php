@extends('layout.default')
@section('content')
    <div id="subject">
        <div id="main">
            <div class="my">
                <div class="my_question">
                    <div class="mq_title">
                        <div class="mq_title_1 select">收听记录</div>
                        <div class="mq_title_2">关注指导师</div>
                    </div>
                    <div class="mq_div">
                        <div class="mq_div_1">
                            <ul class="mq_div_1_list">
                                @foreach($questions as $item)
                                    <li>
                                        <div class="mq_div_1_list_problem"><a href="{{route('wechat.question')}}.'?id='.{{$item->question->id}}">问题：{{$item->question->content}}</a></div>
                                        <div class="mq_div_1_list_name">{{$item->question->answer_user->realname or $item->question->answer_user->nickname}} | {{$item->question->answer_user->tutor_honor}}</div>
                                        <div class="mq_div_1_list_answer">
                                            <div class="mq_div_1_list_answer_img"><img src="{{asset($item->question->answer_user->profileIcon)}}" alt=""/></div>

                                            <div class="mq_div_1_list_answer_voice audio_can_play" style="cursor:pointer;" aid="{{$item->question->audio_type}}" qid="{{$item->question->id}}">{{ $item->question->audio_msg }}
                                                <audio data-src="{{config('qiniu.DOMAIN').$item->question->answer_url}}"></audio>
                                            </div>

                                            <div class="mq_div_1_list_answer_time">{{$item->question->voice_long}}"</div>
                                        </div>
                                        <div class="mq_div_1_list_date">{{$item->question->created_at}}</div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <div class="mq_div_2" style="display:none;">
                            <ul class="mq_div_2_list">
                                @if(count($teachers)>0)
                                @foreach($teachers as $item)
                                    <li>
                                        <a href="{{route('question.teacher',['id'=>$item->favor_teacher->id])}}">
                                            <div class="ga_div_1_list_img"><img src="{{url($item->favor_teacher->profileIcon)}}" alt=""/></div>
                                            <div style="padding-left: 65px;">
                                                <div class="ga_div_1_list_title">{{$item->favor_teacher->realname or $item->favor_teacher->nickname}}</div>
                                                <div class="ga_div_1_list_post">{{$item->favor_teacher->tutor_honor}}</div>
                                                <ul class="ga_div_1_list_information">
                                                    <li class="ga_div_1_list_information_1"><img src="/images/ask/ga_div_1_list_information_1.png" alt=""/> {{$item->favor_teacher->question->count()}}</li>
                                                    <li class="ga_div_1_list_information_2"><img src="/images/ask/ga_div_1_list_information_2.png" alt=""/> {{$item->favor_teacher->user_favor->count()}}</li>
                                                    <li class="ga_div_1_list_information_3"><span style="font-weight:bold">￥{{$item->favor_teacher->tutor_price or '0.00'}}</span><span style="color:#6b6e75">/次</span></li>
                                                </ul>
                                            </div>

                                            <div class="clearboth"></div>
                                        </a>
                                    </li>
                                @endforeach
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script type="text/javascript" src="/js/audio_play.js"></script>
<script>
    $(document).ready(function(){
        audioPlay.init({});
    })

    $(document).on('click','.audio_cant_play',function(){
        var qid = $(this).attr('qid');
        location.href  = '{{route('wechat.question')}}?id='+qid;
    });

    $(".mq_title>div").click(function(){
        if($(this).attr("class")!="mq_title_1 select"&&$(this).attr("class")!="mq_title_2 select"){
            $(".mq_title_1").attr("class","mq_title_1");
            $(".mq_title_2").attr("class","mq_title_2");
            $(this).addClass("select");
            if($(this).attr("class")=="mq_title_1 select"){
                $(".mq_div_2").hide();
                $(".mq_div_1").show();
            }else{
                $(".mq_div_1").hide();
                $(".mq_div_2").show();
            }
        }
    });
</script>
@endsection