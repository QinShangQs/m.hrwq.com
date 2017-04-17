@extends('layout.default')
@section('content')
    <div id="subject">
        <div id="main">
            <div class="my">
                <div class="my_release">
                    <div class="mr_title">
                        <div class="mr_title_1 select">我发布的</div>
                        <div class="mr_title_2">我参与的</div>
                    </div>
                    <div class="mr_div">
                        <div class="mr_div_1">
                            <ul class="mr_div_list">
                                @foreach($talk as $item)
                                <li>
                                    <div class="mr_div_list_div">
                                        <div class="mr_div_list_img"><img src="{{url($item->ask_user->profileIcon)}}" alt=""/></div>
                                        <div class="mr_div_list_name">{{$item->ask_user->realname or $item->ask_user->nickname}}</div>
                                        <div class="mr_div_list_source">来自  {{@$item->ask_user->c_city->area_name}}  {{config('constants.user_label')[$item->ask_user->label]}}</div>
                                    </div>
                                    <div class="mr_div_list_people">{{$item->view or 0}}人已看</div>
                                    <div class="mr_div_list_problem"><a href="{{route('question.talk',['id'=>$item->id])}}">{{$item->title}}</a></div>
                                    <div class="mr_div_list_p"><a href="{{route('question.talk',['id'=>$item->id])}}">{!! replace_em($item->content) !!}</a></div>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="mr_div_2" style="display:none;">
                            <ul class="mr_div_list">
                                @foreach($comment_talk as $item)
                                    <li>
                                        <div class="mr_div_list_div">
                                            <div class="mr_div_list_img"><img src="{{url($item->ask_user->profileIcon)}}" alt=""/></div>
                                            <div class="mr_div_list_name">{{$item->ask_user->realname or $item->ask_user->nickname}}</div>
                                            <div class="mr_div_list_source">来自  {{@$item->ask_user->c_city->area_name}}  {{config('constants.user_label')[$item->ask_user->label]}}</div>
                                        </div>
                                        <div class="mr_div_list_people">{{$item->view or 0}}人已看</div>
                                        <div class="mr_div_list_problem"><a href="{{route('question.talk',['id'=>$item->id])}}">{{$item->title}}</a></div>
                                        <div class="mr_div_list_p"><a href="{{route('question.talk',['id'=>$item->id])}}">{!! replace_em($item->content) !!}</a></div>

                                        @foreach($item->comments as $child_item)
                                            <div class="mr_div_list_comment">我的评论:   {{$child_item->comment_c}} </div>
                                        @endforeach
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        $(document).ready(function(){
            $(".mr_title>div").click(function(){
                if($(this).attr("class")!="mr_title_1 select"&&$(this).attr("class")!="mr_title_2 select"){
                    $(".mr_title_1").attr("class","mr_title_1");
                    $(".mr_title_2").attr("class","mr_title_2");
                    $(this).addClass("select");
                    if($(this).attr("class")=="mr_title_1 select"){
                        $(".mr_div_2").hide();
                        $(".mr_div_1").show();
                    }else{
                        $(".mr_div_1").hide();
                        $(".mr_div_2").show();
                    }
                }
            });
        });
    </script>
@endsection