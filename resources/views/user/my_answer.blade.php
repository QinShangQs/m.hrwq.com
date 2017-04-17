@extends('layout.default')
@section('content')
    <div id="subject">
        <div id="main">
            待回答
            <ul class="ga_div_2_list">
                @forelse($no_answer_data as $item)
                    <li> <div class="ga_div_2_list_answer">
                        <div class="ga_div_2_list_answer_img"><img src="{{asset($item->ask_user->profileIcon)}}" alt=""/></div></div>
                        <div class="ga_div_2_list_name">{{$item->ask_user->realname}} | {{$item->ask_user->tutor_honor}}         <span>价格 ：{{$item->price}}</span></div>

                        <div class="ga_div_2_list_problem"><a href="{{route('wechat.question').'?id='.$item->id}}">问题：{{$item->content}}</a></div>

                        <div class="ga_div_2_list_answer">
                            <div>{{$item->created_at}}</div>
                        </div>
                        <div><a href="{{route('user.answer_voice',['id'=>$item->id])}}"><button>回答问题</button></a></div>
                    </li>
                @empty
                   ------ 暂无问题   ------
                @endforelse
            </ul>
        </div>

        <div id="main">
            已回答
            <ul class="ga_div_2_list">
            @foreach($answer_data as $item)
                <li>
                    <div class="ga_div_2_list_answer">
                       <div class="ga_div_2_list_answer_img"><img src="{{asset($item->ask_user->profileIcon)}}" /></div>
                    </div>
                    <div class="ga_div_2_list_name">{{$item->ask_user->realname}}     <span>价格 ：{{$item->price}}</span></div>

                    <div class="ga_div_2_list_problem"><a href="{{route('wechat.question').'?id='.$item->id}}">问题：{{$item->content}}</a></div>

                    <div class="ga_div_2_list_name">{{$item->answer_user->realname}} | {{$item->answer_user->tutor_honor}}</div>
                    <div class="ga_div_2_list_answer">
                        <div class="ga_div_2_list_answer_img"><img src="{{asset($item->answer_user->profileIcon)}}" alt=""/></div>

                        <div class="ga_div_2_list_answer_voice audio_can_play" style="cursor:pointer;" aid="{{$item->audio_type}}" qid="{{$item->id}}">{{ $item->audio_msg }}
                            <audio data-src="{{config('qiniu.DOMAIN').$item->answer_url}}"></audio>
                        </div>

                        <div class="ga_div_2_list_answer_time">{{$item->voice_long}}"</div>
                        <div >{{$item->created_at}}</div>
                    </div>
                </li>
            @endforeach
            </ul>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript" src="/js/audio_play.js"></script>
    <script>
        $(document).ready(function(){
            /*----------音频播放---------*/
            audioPlay.init({});
        })
    </script>

@endsection