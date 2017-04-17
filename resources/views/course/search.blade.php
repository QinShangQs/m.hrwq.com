@extends('layout.default')
@section('content')
<div id="subject" style="cursor:pointer;">
    <div id="main">
        <div class="good_looking_list">
           <div class="gl_search"><div><img src="/images/public/search_bg.png" alt=""/> <span id="search_tip">@if(request('search_key')) {{request('search_key')}} @else  搜索课程 @endif</span></div></div>
                <div class="public_search"  style="display:none;" >
                    <form class="public_search_form">
                        <div class="public_search_form_div"><input class="public_search_form_input" type="text" name="search_key" value="{{request('search_key')}}" placeholder="搜索课程" ><div class="public_search_form_input_delete"></div></div>
                        <div class="public_search_form_cancel">取消</div>
                    </form>
                    <div class="public_search_hot" style="display: none">
                        <div>搜索"<span>东风</span>"</div>
                    </div>
                    <dl class="public_search_quick">
                        <dt>热门搜索</dt>
                        <dd>
                            <ul>
                                @foreach($hot_search as $item)
                                     <li data-value="{{$item}}">{{$item}}</li><!--data-value为要检索的内容-->
                                @endforeach
                            </ul>
                            <div class="clearboth"></div>
                        </dd>
                        <dt class="public_search_delete_con">最近搜索</dt><!--若没有最近搜索信息，则此dt和下面的dd不显示-->
                        <dd class="public_search_delete_con">
                            <ul class="h-search-item">

                            </ul>
                            <div class="clearboth"></div>
                        </dd>
                    </dl>
                    <div class="public_search_delete">清除搜索记录</div><!--若没有最近搜索信息,则不显示清除搜索记录-->
                </div>
            <div class="search_condition">
                <div class="search_condition_1">@if(request('agency_id')>0){{$agencyArr[request('agency_id')]}}@else课程类别@endif <img src="/images/public/select_button.jpg" alt=""/></div>
                <div class="search_condition_2">@if(request('type')>0){{$typeArr[request('type')]}}@else收费类别@endif <img src="/images/public/select_button.jpg" alt=""/></div>
                <div class="search_condition_3">@if(request('city')>0){{$cityArr[request('city')]}}@else城市@endif <img src="/images/public/select_button.jpg" alt=""/></div>
                <ul class="search_condition_1_list">
                    <li data-value="" data-name="agency_id">不限</li>
                    @foreach ($agencyArr as $key=>$item)
                        <li data-value="{{$key}}" data-name="agency_id">{{$item}}</li>
                    @endforeach
                </ul>
                <ul class="search_condition_2_list">
                    <li data-value="" data-name="type">不限</li>
                    @foreach ($typeArr as $key=>$item)
                        <li data-value="{{$key}}" data-name="type">{{$item}}</li>
                    @endforeach
                </ul>
                <ul class="search_condition_3_list">
                    <li data-value="" data-name="city">不限</li>
                    @foreach ($cityArr as $key=>$item)
                        <li data-value="{{$key}}" data-name="city">{{$item}}</li>
                    @endforeach
                </ul>
            </div>
            @if(@count($courses)>0)
            <ul class="dll_list">
                 @foreach ($courses as $item)
                <li>
                    <a href="{{route('course.detail',['id'=>$item->id])}}">
                        <div class="gl_list2_xz">{{mb_substr($item->agency->agency_name,0,4)}}</div>
                        <div class="dll_list_img">
                        @if($item->picture)
                            <img src="{{ $item->picture }}" alt="" onerror="javascript:this.src='/images/error.jpg'"/>
                        @endif
                        </div>
                        <div class="dll_list_title">{{ @str_limit($item->title,20) }}</div>
                        @if($item->type=='2')
                            <div class="dll_list_money">￥{{ $item->price }}</div>
                        @else
                            <div class="dll_list_money">免费</div>
                        @endif
                        <div class="dll_list_people">{{{ $item->participate_num or 0 }}}人参加</div>
                    </a>
                </li>
                @endforeach
            </ul>
            @else
            <div class="search_none">没有搜索到相关信息</div><!--当没有搜索到相关信息时显示-->
            @endif
        </div>
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript" src="/js/history_search.js"></script>
<script type="text/javascript">
$(document).ready(function(){
        historySearch.init({
             localStorageKey : 'c_kwd_list'
         });
         //搜索关键词 下拉框
         var $inputs = $(".public_search_form_input");
         $inputs.on('input paste',function() {
             if($(this).val()=='')
             {
                 $('.public_search_hot').hide();
             }else{
                 $('.public_search_hot').show();
                 $('.public_search_hot span').html($(this).val());
             }
         });

         //点击搜索
         $('.public_search_hot').click(function(){
             //搜索内容
             var search_key = $('>div >span',this).html();
             historySearch.store(search_key);

             location.href = '{{route('course.search')}}'+'?search_key='+search_key;
         })

         $(".gl_search").click(function(){//弹出搜索框
                $(this).hide();
                $(".public_search").show();

                var tmp_val =  $(".public_search_form_input").val();
                if(tmp_val =='')
                {
                   $('.public_search_hot').hide();
                }else{
                   $('.public_search_hot').show();
                   $('.public_search_hot span').html(tmp_val);
                }

                $(".public_search_form_input").focus();
            });
            $(".public_search_form_input_delete").click(function(){//清空搜索input中的内容
                $(".public_search_form_input").val("");
            });
            $(".public_search_form_cancel").click(function(){//取消搜索
                $(".public_search_form_input").val("");
                $('#search_tip').html('搜索课程');
                $(".gl_search").show();
                $(".public_search").hide();
            });

            $(".public_search_quick li,.h-search-item li").click(function(){//快捷搜索
                var value=$(this).attr("data-value");
                historySearch.store(value);
                location.href = '{{route('course.search')}}'+'?search_key='+value;
                /*----------ajax开始----------*/
                //传值为value，value是要搜索的内容

                /*----------ajax结束----------*/
            });
            $(".public_search_delete").click(function(){//删除最近搜索记录
                historySearch.empty();  //删除c_kwd_list这个键值的里面所有的值
                $(".public_search_delete").remove();
                $(".public_search_delete_con").remove();
            });


    $(".search_condition div").click(function(){//点击筛选分类
        if($(this).attr("class")=="search_condition_1"){//如果是课程类别
            $(".search_condition ul").hide();
            $(".search_condition_1_list").show();
        }else if($(this).attr("class")=="search_condition_2"){//如果是免费和精品
            $(".search_condition ul").hide();
            $(".search_condition_2_list").show();
        }else if($(this).attr("class")=="search_condition_3"){//如果是城市
            $(".search_condition ul").hide();
            $(".search_condition_3_list").show();
            //调用微信的城市信息
        }
    });
    $(".search_condition li").click(function(){//点击筛选分类
        $(".search_condition ul").hide();
        var value=$(this).data("value");
        var cname=$(this).data("name");
        var searchkey = $('input[name="search_key"]').val();
        var url = '{{route('course.search')}}';

        url = url+'?search_key='+searchkey;

        if (cname=='agency_id') {
            url = url+'&agency_id='+value+'&type={{request('type')}}'+'&city={{request('city')}}';
        } else if(cname=='type') {
            url = url+'&type='+value+'&agency_id={{request('agency_id')}}'+'&city={{request('city')}}';
        } else if(cname=='city') {
            url = url+'&city='+value+'&type={{request('type')}}'+'&agency_id={{request('agency_id')}}';
        } else {
            url = url+'&type={{request('type')}}&agency_id={{request('agency_id')}}&city={{request('city')}}';
        }
        location.href = url;
    });
    $(document).click(function (e) {//点击其他部分隐藏筛选分类
        var drag = $(".search_condition"),
            dragel = $(".search_condition")[0],
            target = e.target;
        if (dragel !== target && !$.contains(dragel, target)) {
            $(".search_condition ul").hide();
        }
    });
});
</script>
@endsection