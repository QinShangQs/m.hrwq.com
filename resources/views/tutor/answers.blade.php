@extends('layout.default')
@section('content')
    <div id="subject">
        <div id="main">
            <div class="my">
                <div class="my_instructor_my_answer">
                    <div class="mima_title">
                        <div class="mima_title_1 select">待回答</div>
                        <div class="mima_title_2">已回答</div>
                    </div>
                    <div class="mima_div">
                        <div class="mima_div_1">
                            <ul class="mima_div_1_list">
                                @forelse($no_answer_data as $item)
                                    <li>
                                        <div class="mima_div_1_list_img"><img
                                                    src="{{asset($item->ask_user->profileIcon)}}" alt=""/></div>
                                        <div class="mima_div_1_list_name">{{$item->ask_user->nickname}}</div>
                                        <div class="mima_div_1_list_problem">问题：{{$item->content}}</div>
                                        <div class="mima_div_1_list_date">{{$item->created_at}}</div>
                                        <div class="mima_div_1_list_price">￥{{$item->price}}</div>
                                        <div class="mima_div_1_list_button">
                                            <a href="{{route('user.answer_voice',['id'=>$item->id])}}">
                                                <div class="mima_div_1_list_button1">回答问题</div>
                                            </a>
                                            {{--<div class="mima_div_1_list_button2" data-id="0">删除问题</div>--}}
                                        </div>
                                    </li>
                                @empty
                                @endforelse
                            </ul>
                        </div>
                        <div class="mima_div_2" style="display:none;">
                            <ul class="mima_div_2_list">
                                @forelse($answer_data as $item)
                                <li>
                                    <dl>
                                        <dt>
                                        <div class="mima_div_2_list_img"><img src="{{asset($item->ask_user->profileIcon)}}" alt=""/></div>
                                        <div class="mima_div_2_list_name">{{$item->ask_user->nickname}}</div>
                                        <div class="mima_div_2_list_problem">问题：{{$item->content}}</div>
                                        <div class="mima_div_2_list_date">{{$item->created_at}}</div>
                                        <div class="mima_div_2_list_price">￥{{$item->price}}</div>
                                        </dt>
                                        <dd>
                                            <div class="mima_div_1_list_answer">
                                                <div class="mima_div_1_list_answer_img"><img src="{{asset($item->answer_user->profileIcon)}}"
                                                                                             alt=""/></div>
                                                <div class="mima_div_1_list_answer_voice audio_can_play" aid="{{$item->audio_type}}" qid="{{$item->id}}">
                                                    {{ $item->audio_msg }}
                                                    <audio data-src="{{config('qiniu.DOMAIN').$item->answer_url}}"></audio>
                                                </div>
                                                <!--data-value为信息id-->
                                                <div class="mima_div_1_list_answer_time">{{$item->voice_long}}"</div>
                                                <div class="mima_div_1_list_answer_date">{{$item->created_at}}</div>
                                            </div>

                                        </dd>
                                    </dl>
                                </li>
                                @empty
                                @endforelse
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
        $(document).ready(function () {
            /*----------音频播放---------*/
            audioPlay.init({});

            $(".mima_title>div").click(function () {
                if ($(this).attr("class") != "mima_title_1 select" && $(this).attr("class") != "mima_title_2 select") {
                    $(".mima_title_1").attr("class", "mima_title_1");
                    $(".mima_title_2").attr("class", "mima_title_2");
                    $(this).addClass("select");
                    if ($(this).attr("class") == "mima_title_1 select") {
                        $(".mima_div_2").hide();
                        $(".mima_div_1").show();
                    } else {
                        $(".mima_div_1").hide();
                        $(".mima_div_2").show();
                    }
                }
            });
        })
    </script>

@endsection