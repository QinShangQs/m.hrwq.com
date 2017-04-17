@extends('layout.default')
@section('content')
    <div id="subject">
        <div id="main">
            <div class="my">
                <div class="my_collection">
                    <div class="mc_title">
                        <div class="mc_title_1 select">我的收藏</div>
                        <div class="mc_title_2">我的参与</div>
                    </div>
                    <div class="mc_div">
                        <div class="mc_div_1">
                            <ul class="mc_div_list">
                                @if(count($favors))
                                    @foreach($favors as $favor)
	                                    @if($favor->favor_type == 1)
	                                            <li>
	                                                <div class="mc_div_list_img"><a
	                                                            href="{{route('course.detail',['id'=>$favor->course->id])}}">
	                                                        <div class="gl_list2_xz">好课</div><img style="width:100%; height:100%;" src="{{admin_url($favor->course->picture)}}" alt=""/></a>
	                                                </div>
	                                                <div class="mc_div_list_title"><a
	                                                            href="{{route('course.detail',['id'=>$favor->course->id])}}">{{@str_limit($favor->course->title,20)}}</a>
	                                                </div>
	                                                @if($favor->course->type == 1)
	                                                    <div class="mc_div_list_price">免费</div>
	                                                @else
	                                                    <div class="mc_div_list_price">¥{{$favor->course->price}}
	                                                        <span>¥{{$favor->course->original_price}}</span></div>
	                                                @endif
	                                                <div class="mc_div_list_people">{{$favor->course->participate_num}}
	                                                    人已报名
	                                                </div>
	                                            </li>
                                        @endif
                                        
                                        @if($favor->favor_type == 2)
	                                            <li>
	                                                <div class="mc_div_list_img"><a
	                                                            href="{{route('vcourse.detail',['id'=>$favor->vcourse->id])}}">
	                                                        <div class="gl_list2_xz">好看</div><img style="width:100%; height:100%;" src="{{admin_url($favor->vcourse->cover)}}" alt=""/></a>
	                                                </div>
	                                                <div class="mc_div_list_title"><a
	                                                            href="{{route('vcourse.detail',['id'=>$favor->vcourse->id])}}">{{@str_limit($favor->vcourse->title,20)}}</a>
	                                                </div>
	                                                @if($favor->vcourse->type == 1)
	                                                    <div class="mc_div_list_price">免费</div>
	                                                @else
	                                                    <div class="mc_div_list_price">¥{{$favor->vcourse->price}}
	                                                        </div>
	                                                @endif
	                                                <div class="mc_div_list_people">{{$favor->vcourse->view_cnt}}
	                                                    人已观看
	                                                </div>
	                                            </li>
                                        @endif
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                        <div class="mc_div_2" style="display:none;">
                            <ul class="mc_div_list">
                                @if(count($participates))
                                    @foreach($participates as $participate)
                                        @if($participate->pay_type == 1)
	                                            <li>
	                                                <div class="mc_div_list_img">
	                                                    <a href="{{route('course.detail',['id'=>$participate->course->id])}}">
	                                                        <div class="gl_list2_xz">好课</div><img style="width:100%; height:100%;" src="{{admin_url($participate->course->picture)}}" alt=""/>
	                                                    </a>
	                                                </div>
	                                                <div class="mc_div_list_title"><a
	                                                            href="{{route('course.detail',['id'=>$participate->course->id])}}">{{@str_limit($participate->course->title,20)}}</a>
	                                                </div>
	                                                @if($participate->course->type == 1)
	                                                    <div class="mc_div_list_price">免费</div>
	                                                @else
	                                                    <div class="mc_div_list_price">¥{{$participate->course->price}}
	                                                        <span>¥{{$participate->course->original_price}}</span></div>
	                                                @endif
	                                                <div class="mc_div_list_people">{{$participate->course->participate_num}}
	                                                    人已参与
	                                                </div>
	                                                <a href="{{route('course.detail',['id'=>$participate->course->id])}}">
	                                                <div class="mc_div_list_button" data-id="0">开始学习</div></a>
	                                            </li>
                                        @endif
                                        @if($participate->pay_type == 2)
	                                            <li>
	                                                <div class="mc_div_list_img">
	                                                    <a href="{{route('vcourse.detail',['id'=>$participate->vcourse->id])}}">
	                                                        <div class="gl_list2_xz">好看</div><img style="width:100%; height:100%;" src="{{admin_url($participate->vcourse->cover)}}" alt=""/>
	                                                    </a>
	                                                </div>
	                                                <div class="mc_div_list_title"><a
	                                                            href="{{route('vcourse.detail',['id'=>$participate->vcourse->id])}}">{{@str_limit($participate->vcourse->title,20)}}</a>
	                                                </div>
	                                                @if($participate->vcourse->type == 1)
	                                                    <div class="mc_div_list_price">免费</div>
	                                                @else
	                                                    <div class="mc_div_list_price">¥{{$participate->vcourse->price}}
	                                                        </div>
	                                                @endif
	                                                <div class="mc_div_list_people">{{$participate->vcourse->view_cnt}}
	                                                    人已观看
	                                                </div>
	                                                <a href="{{route('vcourse.detail',['id'=>$participate->vcourse->id])}}">
	                                                <div class="mc_div_list_button" data-id="0">开始观看</div></a>
	                                            </li>
                                        @endif
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
    <script type="text/javascript">
        $(document).ready(function () {
            $(".mc_div_list_button").click(function () {//点击开始学习
                var value = $(this).attr("data-id");
                /*----------ajax开始----------*/
                //传值为value
                /*----------ajax结束----------*/
            });
            $(".mc_title>div").click(function () {
                if ($(this).attr("class") != "mc_title_1 select" && $(this).attr("class") != "mc_title_2 select") {
                    $(".mc_title_1").attr("class", "mc_title_1");
                    $(".mc_title_2").attr("class", "mc_title_2");
                    $(this).addClass("select");
                    if ($(this).attr("class") == "mc_title_1 select") {
                        $(".mc_div_2").hide();
                        $(".mc_div_1").show();
                    } else {
                        $(".mc_div_1").hide();
                        $(".mc_div_2").show();
                    }
                }
            });
        });
    </script>
@endsection