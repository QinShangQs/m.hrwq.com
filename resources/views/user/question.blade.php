@extends('layout.default')
@section('content')
    <div id="subject">
        <div id="main">
            <div class="good_asking">

                <ul class="ga_tab">
                    <li id="ga_tab_1"  ga_div="ga_div_1" class="selected" >已提问</li>
                    <li id="ga_tab_2"  ga_div="ga_div_2" >已旁听</li>
                    <li id="ga_tab_3"  ga_div="ga_div_3"  >已关注</li>
                </ul>
                <div class="ga_div">

                    <div class="ga_div_1 children_div"  >
                        <ul class="mq_div_1_list" >
                          		@foreach($no_answer_data as $item)
                                    <li>
                                        <div class="mq_div_2_list_problem"><a href="{{route('wechat.question').'?id='.$item->id}}">问题：{{$item->content}}</a></div>
                                        <div class="mq_div_2_list_price">￥{{$item->price}}</div>
                                        <div class="mq_div_2_list_date">{{$item->created_at}}</div>
                                        <div class="mq_div_2_list_div">
                                            <div class="mq_div_2_list_div_img"><img src="{{asset($item->answer_user->profileIcon)}}" alt=""/></div>
                                            <div class="mq_div_2_list_div_name">{{$item->answer_user->realname or $item->answer_user->nickname}}</div>
                                            <div class="mq_div_2_list_div_post">{{$item->answer_user->tutor_honor}}</div><span style="float: right;color: #ed6d11;font-size: 14px;">待回答</span>
                                        </div>
                                    </li>
                                 @endforeach
                                 @foreach($answer_data as $item)
                                    <li>
                                        <div class="mq_div_1_list_problem"><a href="{{route('wechat.question').'?id='.$item->id}}">问题：{{$item->content}}</a></div>
                                        <div class="mq_div_1_list_name">{{$item->answer_user->realname or $item->answer_user->nickname}} | {{$item->answer_user->tutor_honor}}</div>
                                        <div class="mq_div_1_list_answer">
                                            <div class="mq_div_1_list_answer_img"><img src="{{asset($item->answer_user->profileIcon)}}" alt=""/></div>

                                            <div class="mq_div_1_list_answer_voice audio_can_play" style="cursor:pointer;" aid="{{$item->audio_type}}" qid="{{$item->id}}">{{ $item->audio_msg }}
                                                <audio data-src="{{config('qiniu.DOMAIN').$item->answer_url}}"></audio>
                                            </div>

                                            <div class="mq_div_1_list_answer_time">{{$item->voice_long}}"</div>
                                            <span style="float: right;color: #ed6d11;font-size: 14px;">已回答</span>
                                        </div>
                                        <div class="mq_div_1_list_date">{{$item->created_at}}</div>
                                    </li>
                                @endforeach

                        </ul>
                    </div>
                    <div class="ga_div_2 children_div"  style="display:none;">
                        <ul class="mq_div_1_list" >
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
                    <div class="ga_div_3 children_div" style="display:none;" >
                        <ul class="mq_div_2_list" >
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
@endsection

@section('script')
<script type="text/javascript" src="/js/audio_play.js"></script>
<script>
    $(function(){
        audioPlay.init({});
        
        $(document).on('click','.audio_cant_play',function(){
            var qid = $(this).attr('qid');
            location.href  = '{{route('wechat.question')}}?id='+qid;
        });

        $(".ga_tab>li").click(function(){
            var now_ga_li = $(this);
            var now_ga_div = $(this).attr("ga_div");
            $(".ga_tab").find("li").each(function(){
                if($(this).hasClass("selected")){
                	$(this).removeClass("selected");
                }
            });
            now_ga_li.addClass("selected");
            $(".children_div").each(function(){
                $(this).hide();
            });
            $("."+now_ga_div).show();
            
        });
    })
</script>
@endsection