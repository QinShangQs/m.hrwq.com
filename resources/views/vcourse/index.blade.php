@extends('layout.default')
@section('content')
<div id="subject">
    <div id="main">
        <div class="good_looking">
            <div class="gl_search"><div><img src="images/public/search_bg.png" alt=""/> <span id="search_tip">@if(request('search_key')) {{request('search_key')}} @else  搜索讲师/课程 @endif</span></div></div>
            <div class="public_search"  style="display:none;" >
                <form class="public_search_form">
                    <div class="public_search_form_div"><input class="public_search_form_input" type="text" name="search_key" value="{{request('search_key')}}" placeholder="搜索讲师/课程" ><div class="public_search_form_input_delete"></div></div>
                    <div class="public_search_form_cancel">取消</div>
                </form>
                <div class="public_search_hot" style="display: none">
                    <div>搜索"<span>东风</span>"</div>
                </div>
                <dl class="public_search_quick">
                    <dt>热门搜索</dt>
                    <dd>
                        @if(isset($hot_search)&&count($hot_search)>0)
                        <ul>
                            @foreach($hot_search as $item)
                                 <li data-value="{{$item}}">{{$item}}</li><!--data-value为要检索的内容-->
                            @endforeach
                        </ul>
                        @endif
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
            @if(count($carouselList)>0)
            <div class="public_slide">
                <div class="main_visual">
                    <div class="flicking_con">
                        @foreach ($carouselList as $item)
                        <a href="#">{{ $item->id }}</a>
                        @endforeach
                    </div>
                    <div class="main_image">
                        <ul>
                           @foreach($carouselList as $item)
                                @if($item->redirect_type == 1)
                                <li> <span class="img_{{$item->id}}" style="background:url({{ config('constants.admin_url').$item->image_url}}) left top no-repeat; background-size:100%;">
                                    </span></li>
                                @elseif($item->redirect_type == 2)
                                <li>
                                    <a href="{{$item->redirect_url}}">
                                        <span class="img_{{$item->id}}" style="background:url({{ config('constants.admin_url').$item->image_url}}) left top no-repeat; background-size:100%;">
                                    </span>
                                    </a>
                                @else
                                 <li><a href="{{route('course.staticlink',['id'=>$item->id])}}">
                                        <span class="img_{{$item->id}}" style="background:url({{ config('constants.admin_url').$item->image_url}}) left top no-repeat; background-size:100%;">
                                    </span>
                                    </a>
                                @endif
                            @endforeach
                        </ul>
                        <a href="javascript:;" id="btn_prev"></a>
                        <a href="javascript:;" id="btn_next"></a>
                    </div>
                </div>
            </div>
            @endif
            @if(count($freeVcourseList)>0)
            <div class="gl_div gl_div1">
                <div class="gl_title">免费课程</div>
                <a class="gl_more" href="{{route('vcourse.more',['type'=>'1'])}}">更多></a>
                <ul class="gl_list">
                    @foreach ($freeVcourseList as $item)
                    <li>
                        <div class="gl_list_img"><div class="gl_list2_xz">{{mb_substr($item->agency->agency_name,0,4)}}</div><a href="{{route('vcourse.detail',['id'=>$item->id])}}">
                        @if($item->cover)
                            <img src="{{ config('constants.admin_url').$item->cover}}" alt="" onerror="javascript:this.src='/images/error.jpg'"/>
                        @else
                            <img src="{{ config('qiniu.DOMAIN').$item->video_tran}}?vframe/jpg/offset/{{ config('qiniu.COVER_TIME')}}" alt="" onerror="javascript:this.src='/images/error.jpg'"/>
                        @endif
                        </a></div>
                        <div class="gl_list_title"><a href="{{route('vcourse.detail',['id'=>$item->id])}}">{{ @str_limit($item->title,30) }}</a></div>
                    </li>
                    @endforeach
                </ul>
                <div class="clearboth"></div>
            </div>
            @endif
            @if(count($chargeVcourseList)>0)
            <div class="gl_div gl_div2">
                <div class="gl_title">畅销课程</div>
                <a class="gl_more" href="{{route('vcourse.more',['type'=>'2'])}}">更多></a>
                <ul class="gl_list">
                    @foreach ($chargeVcourseList as $item)
                    <li>
                        <div class="gl_list_img">
                            <div class="gl_list2_xz">{{mb_substr($item->agency->agency_name,0,4)}}</div>
                            <a href="{{route('vcourse.detail',['id'=>$item->id])}}">
                        @if($item->cover)
                            <img src="{{ config('constants.admin_url').$item->cover}}" alt="" onerror="javascript:this.src='/images/error.jpg'"/>
                        @else
                            <img src="{{ config('qiniu.DOMAIN').$item->video_tran}}?vframe/jpg/offset/{{ config('qiniu.COVER_TIME')}}" alt="" onerror="javascript:this.src='/images/error.jpg'"/>
                        @endif
                        </a></div>
                        <div class="gl_list_title">
                            <a href="{{route('vcourse.detail',['id'=>$item->id])}}">{{ @str_limit($item->title,30) }}</a>
                        </div>
                        <div class="gl_list_money">￥{{ $item->price }}</div>
                        <div class="gl_list_people">{{ $item->view_cnt or 0 }}人观看</div>
                    </li>
                    @endforeach
                </ul>
                <div class="clearboth"></div>
            </div>
            @endif
            @if(count($recommendVcourseList)>0)
            <div class="gl_div gl_div3">
                <div class="gl_title">推荐课程</div>
                <ul class="gl_list2">
                    @foreach ($recommendVcourseList as $item)
                    <li>
                        <a href="{{route('vcourse.detail',['id'=>$item->id])}}">
                            <div class="gl_list2_xz">{{mb_substr($item->agency->agency_name,0,4)}}</div>
                            <div class="gl_list2_img">
                            @if($item->cover)
                                <img src="{{ config('constants.admin_url').$item->cover}}" alt="" onerror="javascript:this.src='/images/error.jpg'"/>
                            @else
                                <img src="{{ config('qiniu.DOMAIN').$item->video_tran}}?vframe/jpg/offset/{{ config('qiniu.COVER_TIME')}}" alt="" onerror="javascript:this.src='/images/error.jpg'"/>
                            @endif
                            </div>
                            <div class="gl_list2_div">
                                <div class="gl_list2_title">{{ $item->title }}</div>
                                <div class="gl_list2_span">
                                    @if($item->type=='1')
                                    <span class="lcd_banner_span_1">免费</span> 
                                    @else
                                    <span class="lcd_banner_span_1">￥{{ $item->price }}</span> 
                                    @endif
                                    @if($item->current_class)
                                    <span class="lcd_banner_span_3">课时：<span>{{ $item->current_class }}/{{ $item->total_class }}</span>
                                    </span>
                                    @endif()
                                </div>
                            </div>
                        </a>
                    </li>
                    @endforeach
                </ul>
                @if($recommendVcourseList->hasMorePages())
                <div class="gl_list2_more">点击加载更多...</div>
                @endif
            </div>
            @endif
        </div>
        @include('element.nav', ['selected_item' => 'nav2'])
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript" src="js/jquery.event.drag-1.5.min.js"></script><!--幻灯效果-->
<script type="text/javascript" src="js/jquery.touchSlider.js"></script><!--幻灯效果-->
<script type="text/javascript" src="/js/history_search.js"></script>
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js" type="text/javascript" charset="utf-8"></script>
<script>
$(document).ready(function(){//搜索
            wx.config(<?php echo $wx_js->config(array("onMenuShareAppMessage", "onMenuShareTimeline"),false) ?>);
            wx.ready(function(){
                wx.onMenuShareAppMessage({
                    title: '和润万青-让教育孩子变得简单', // 分享标题
                    desc: '爱中管教，互联网时代中国父母的必修课', // 分享描述
                    link: '{{route('vcourse')}}', // 分享链接
                    @if(@$carouselList[0]['image_url'])
                    imgUrl: '{{config('constants.admin_url').$carouselList[0]['image_url']}}', // 分享图标
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
                    title: '和润万青-让教育孩子变得简单', // 分享标题
                    link: '{{route('vcourse')}}', // 分享链接
                    @if(@$carouselList[0]['image_url'])
                    imgUrl: '{{config('constants.admin_url').$carouselList[0]['image_url']}}', // 分享图标
                    @endif
                    success: function () {
                        // 用户确认分享后执行的回调函数
                    },
                    cancel: function () {
                        // 用户取消分享后执行的回调函数
                    }
                });

            });

         $('.gl_div1 img').height($('.gl_div1 img').width()*9/17);
         $('.gl_div2 img').height($('.gl_div2 img').width()*9/17)
         historySearch.init({
             localStorageKey : 'vc_kwd_list'
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

             location.href = '{{route('vcourse.search')}}'+'?search_key='+search_key;
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
                $('#search_tip').html('搜索讲师/课程');
                $(".gl_search").show();
                $(".public_search").hide();
            });

            $(".public_search_quick li,.h-search-item li").click(function(){//快捷搜索
                var value=$(this).attr("data-value");
                historySearch.store(value);
                location.href = '{{route('vcourse.search')}}'+'?search_key='+value;
                /*----------ajax开始----------*/
                //传值为value，value是要搜索的内容

                /*----------ajax结束----------*/
            });
            $(".public_search_delete").click(function(){//删除最近搜索记录
                historySearch.empty(); //删除vc_kwd_list这个键值的里面所有的值
                $(".public_search_delete").remove();
                $(".public_search_delete_con").remove();
            });

    /*----------推荐课程加载更多----------*/
     var current_page = 1, last_page = '{{$recommendVcourseList->lastPage()}}';
     $(".gl_list2_more").click(function(){//点击加载更多按钮
         if(last_page > current_page)
         {
             var sheight = $('.gl_list2_more').offset().top;
             current_page ++;
             $.ajax({
                 type: 'post',
                 url: '{{route('vcourse.recommend_list')}}',
                 data: {page:current_page},
                 dataType: 'json',
                 success: function (res) {
                    if(res)
                    {
                        var recommend_data = res.data;
                        var recommend_ul_li ='';
                        $.each(recommend_data,function(k,v){
                            recommend_ul_li += '<li>';
                            recommend_ul_li += '<a href="{{route('vcourse.detail')}}/'+ v.id+'">';
                            recommend_ul_li += '<div class="gl_list2_xz">'+v.agency.agency_name.substring(0,20)+'</div>';
                            if(v.cover==''||v.cover==null){
                                recommend_ul_li += '<div class="gl_list2_img"><img src="{{ config("qiniu.DOMAIN")}}'+ v.video_tran+'?vframe/jpg/offset/{{ config("qiniu.COVER_TIME")}}" alt="" onerror="javascript:this.src=\'/images/error.jpg\'"/></div>';
                            }else{
                                recommend_ul_li += '<div class="gl_list2_img"><img src="{{config('constants.admin_url')}}'+ v.cover+'" alt=""/></div>';
                            }
                            recommend_ul_li += '<div class="gl_list2_div">';
                            recommend_ul_li += '<div class="gl_list2_title">'+ v.title.substring(0,20)+'</div>';
                            recommend_ul_li += '<div class="gl_list2_span">';
                            if(v.type=='1'){ 
                                recommend_ul_li += '<span class="lcd_banner_span_1">免费</span>';
                            }else{
                                recommend_ul_li += '<span class="lcd_banner_span_1">￥'+v.price+'</span>';
                            }
                            if(v.current_class>0){
                                recommend_ul_li +='<span class="lcd_banner_span_3">课时：<span>'+v.current_class+'/'+v.total_class+'</span></span>';
                            }
                            recommend_ul_li += '</div></div></a></li>';
                        })
                        if(recommend_ul_li!='')
                        {
                            $('.gl_list2').append(recommend_ul_li);
                            $("html,body").animate({scrollTop:sheight}, 1000);
                        }
                    }
                 }
             });
             if(current_page >= last_page){
                 $(".gl_list2_more").hide();
             }
         }
     });
});
</script>
<script type="text/javascript">
$(document).ready(function(){//幻灯效果
    $(".main_visual").hover(function(){
        $("#btn_prev,#btn_next").fadeIn()
    },function(){
        $("#btn_prev,#btn_next").fadeOut()
    });
    
    $dragBln = false;
    
    $(".main_image").touchSlider({
        flexible : true,
        speed : 200,
        btn_prev : $("#btn_prev"),
        btn_next : $("#btn_next"),
        paging : $(".flicking_con a"),
        counter : function (e){
            $(".flicking_con a").removeClass("on").eq(e.current-1).addClass("on");
        }
    });
    
    $(".main_image").bind("mousedown", function() {
        $dragBln = false;
    });
    
    $(".main_image").bind("dragstart", function() {
        $dragBln = true;
    });
    
    $(".main_image a").click(function(){
        if($dragBln) {
            return false;
        }
    });
    
    timer = setInterval(function(){
        $("#btn_next").click();
    }, 5000);
    
    $(".main_visual").hover(function(){
        clearInterval(timer);
    },function(){
        timer = setInterval(function(){
            $("#btn_next").click();
        },5000);
    });
    
    $(".main_image").bind("touchstart",function(){
        clearInterval(timer);
    }).bind("touchend", function(){
        timer = setInterval(function(){
            $("#btn_next").click();
        }, 5000);
    });
    $(".main_visual").height($(".main_visual").width()/750*300);
});
</script>
<script type="text/javascript">
$(document).ready(function(){
    $(".gl_list2_more").click(function(){//点击加载更多按钮
        /*----------ajax开始----------*/
            //ajax获取更多的信息
            //ajax成功返回事件开始
                //将获取的信息追加到gl_list2中，若是没有更多的信息则隐藏gl_list2_more即隐藏点击加载更多按钮
            //ajax成功返回事件结束
        /*----------ajax结束----------*/
    });
});
</script>
@endsection