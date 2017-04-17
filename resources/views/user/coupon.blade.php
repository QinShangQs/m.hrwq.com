@extends('layout.default')
@section('content')
    <div id="subject">
        <div id="main">
            <div class="my">
                <div class="my_coupon_situation">
                    <ul class="mcs_tab">
                        <li id="mcs_tab_1" class="selected">未使用</li>
                        <li id="mcs_tab_2">已使用</li>
                        <li id="mcs_tab_3">已过期</li>
                    </ul>
                    <ul class="mcs_list mcs_list1"><!--未使用list-->
                        @foreach($data[2] as $item)
                            <li>
                                <div class="mcs_list_left">
                                    @if($item->c_coupon->type == 1)
                                        <div class="mcs_list_left_1">{{$item->c_coupon->cut_money}}元</div>
                                        <div class="mcs_list_left_2">抵用券</div>
                                    @else
                                        <div class="mcs_list_left_1">{{(int)$item->c_coupon->discount}}折</div>
                                        <div class="mcs_list_left_2">折扣券</div>
                                    @endif
                                </div>
                                <div class="mcs_list_right">
                                    <p> {{$item->use_condition}}</p>
                                    <p>{{$item->use_scope}}使用</p>
                                    <p>使用期限{{date('Y.m.d',strtotime($item->use_start))}}-{{date('Y.m.d',strtotime($item->use_end))}}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>

                    <ul class="mcs_list mcs_list2" style="display:none;"><!--已使用list-->
                        @foreach($data[1] as $item)
                            <li>
                                <div class="mcs_list_left">
                                    @if($item->c_coupon->type == 1)
                                        <div class="mcs_list_left_1">{{$item->c_coupon->cut_money}}元</div>
                                        <div class="mcs_list_left_2">抵用券</div>
                                    @else
                                        <div class="mcs_list_left_1">{{(int)$item->c_coupon->discount}}折</div>
                                        <div class="mcs_list_left_2">折扣券</div>
                                    @endif
                                </div>
                                <div class="mcs_list_right">
                                    <p> {{$item->use_condition}}</p>
                                    <p>{{$item->use_scope}}使用</p>
                                    <p>使用期限{{date('Y.m.d',strtotime($item->use_start))}}-{{date('Y.m.d',strtotime($item->use_end))}}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                    <ul class="mcs_list mcs_list3" style="display:none;"><!--已过期list-->
                        @foreach($data[3] as $item)
                            <li>
                                <div class="mcs_list_left">
                                    @if($item->c_coupon->type == 1)
                                        <div class="mcs_list_left_1">{{$item->c_coupon->cut_money}}元</div>
                                        <div class="mcs_list_left_2">抵用券</div>
                                    @else
                                        <div class="mcs_list_left_1">{{(int)$item->c_coupon->discount}}折</div>
                                        <div class="mcs_list_left_2">折扣券</div>
                                    @endif
                                </div>
                                <div class="mcs_list_right">
                                    <p> {{$item->use_condition}}</p>
                                    <p>{{$item->use_scope}}使用</p>
                                    <p>使用期限{{date('Y.m.d',strtotime($item->use_start))}}-{{date('Y.m.d',strtotime($item->use_end))}}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@section('script')
    <script>
        $(document).ready(function(){
            $(".mcs_list li").height($(".mcs_list li").width()/690*160);
            $(".mcs_list_left").css("top",($(".mcs_list li").height()-52)/2+"px");
            $(".mcs_list_right").css("top",($(".mcs_list li").height()-60)/2+"px");
            $(".mcs_tab li").click(function(){//tab切换
                if($(this).attr("class")!="selected"){
                    $(".mcs_tab li").attr("class","");
                    $(this).attr("class","selected");
                    $(".mcs_list").hide();
                    switch($(this).attr("id")){
                        case "mcs_tab_1":
                            $(".mcs_list1").show();
                            $(".mcs_list li").height($(".mcs_list1 li").width()/690*160);
                            $(".mcs_list_left").css("top",($(".mcs_list1 li").height()-52)/2+"px");
                            $(".mcs_list_right").css("top",($(".mcs_list1 li").height()-60)/2+"px");
                            break;
                        case "mcs_tab_2":
                            $(".mcs_list2").show();
                            $(".mcs_list li").height($(".mcs_list2 li").width()/690*160);
                            $(".mcs_list_left").css("top",($(".mcs_list2 li").height()-52)/2+"px");
                            $(".mcs_list_right").css("top",($(".mcs_list2 li").height()-60)/2+"px");
                            break;
                        case "mcs_tab_3":
                            $(".mcs_list3").show();
                            $(".mcs_list li").height($(".mcs_list3 li").width()/690*160);
                            $(".mcs_list_left").css("top",($(".mcs_list3 li").height()-52)/2+"px");
                            $(".mcs_list_right").css("top",($(".mcs_list3 li").height()-60)/2+"px");
                            break;
                        default:
                            break;
                    }
                }
            });
        });
    </script>
@endsection
