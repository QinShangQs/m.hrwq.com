@extends('layout.default')
@section('content')
    <div id="subject">
        <div id="main">
            <div class="good_looking">
                <div class="gl_search"><div><img src="images/public/search_bg.png" alt=""/> <span id="search_tip">@if(request('search_key')) {{request('search_key')}} @else  搜索讲师/课程 @endif</span></div></div>
                <div class="public_search" style="display:none;">
                    <form class="public_search_form">
                        <div class="public_search_form_div"><input class="public_search_form_input" type="text" value="{{request('search_key')}}" placeholder="搜索讲师/课程"><div class="public_search_form_input_delete"></div></div>
                        <div class="public_search_form_cancel">取消</div>
                    </form>
                    <div class="public_search_hot" style="display: none">
                        <div>搜索"<span>东风</span>"</div>
                    </div>
                    <dl class="public_search_quick">
                        <dt>热门搜索</dt>
                        <dd>
                            <ul>
                                @foreach($hotsearch as $item)
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
                <div class="public_slide">
                    <div class="main_visual">
                        <div class="flicking_con">
                            @foreach($carousels as $key => $carousel)
                                <a href="#">{{$key+1}}</a>
                            @endforeach
                        </div>
                        <div class="main_image">
                            <ul>
                                @foreach($carousels as $carousel)
                                @if($carousel->redirect_type == 1)
                                <li><span class="img_1" style="background:url({{$carousel->image_url}}) left top no-repeat; background-size:100%;"></span></li>
                                @elseif($carousel->redirect_type == 2)
                                <li><a href="{{$carousel->redirect_url}}"><span class="img_1" style="background:url({{$carousel->image_url}}) left top no-repeat; background-size:100%;"></span></a></li>
                                @else
                                 <li><a href="{{route('course.staticlink',['id'=>$carousel->id])}}"><span class="img_1" style="background:url({{$carousel->image_url}}) left top no-repeat; background-size:100%;"></span></a></li>
                                @endif
                                @endforeach
                                
                            </ul>
                            <a href="javascript:;" id="btn_prev"></a>
                            <a href="javascript:;" id="btn_next"></a>
                        </div>
                    </div>
                </div>
                <div class="gl_div gl_div3" style="padding-top:0;">
                    <ul class="gl_list2">
                        @if(count($courses)>0)
                            @foreach($courses as $item)
                                <li>
                                    <a href="{{ route('course.detail',['id'=>$item->id]) }}">
                                        <div class="gl_list2_xz">{{mb_substr($item->agency->agency_name,0,4)}}</div>
                                        <div class="gl_list2_img"><img src="{{$item->picture}}" alt=""/></div>
                                        <div class="gl_list2_div">
                                            <div class="gl_list2_title">{{$item->title}}</div>
                                            <div class="gl_list2_span">
                                                @if($item->type == 1)
                                                    <span class="lcd_banner_span_1">免费</span>
                                                @else
                                                    <span class="lcd_banner_span_1">¥{{$item->price}}</span> 
                                                    <span class="lcd_banner_span_2">¥{{$item->original_price}}</span> 
                                                @endif
                                            </div>
                                        </div>
                                        <div class="gl_list2_people"><span>{{$item->participate_num}}</span>/{{$item->allow_num}}人</div>
                                        <div class="gl_list2_address"><img src="images/look/address_bg.png" alt=""/> {{$item->course_addr}}</div>
                                    </a>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                    @if($courses->hasMorePages())
                    <div class="gl_list2_more">点击加载更多...</div>
                    @endif
                </div>
            </div>
            @include('element.nav', ['selected_item' => 'nav1'])
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript" src="/js/jquery.event.drag-1.5.min.js"></script><!--幻灯效果-->
    <script type="text/javascript" src="/js/jquery.touchSlider.js"></script><!--幻灯效果-->
    <script type="text/javascript" src="/js/history_search.js"></script>
    <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js" type="text/javascript" charset="utf-8"></script>
    <script>
        $(document).ready(function(){//搜索
            wx.config(<?php echo $wx_js->config(array("onMenuShareAppMessage", "onMenuShareTimeline"),false) ?>);
            wx.ready(function(){
                wx.onMenuShareAppMessage({
                    title: '和润万青-让教育孩子变得简单', // 分享标题
                    desc: '爱中管教，互联网时代中国父母的必修课', // 分享描述
                    link: '{{route('course')}}', // 分享链接
                    @if(@$carousels[0]['image_url'])
                    imgUrl: '{{$carousels[0]['image_url']}}', // 分享图标
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
                    link: '{{route('course')}}', // 分享链接
                    @if(@$carousels[0]['image_url'])
                    imgUrl: '{{$carousels[0]['image_url']}}', // 分享图标
                    @endif
                    success: function () {
                        // 用户确认分享后执行的回调函数
                    },
                    cancel: function () {
                        // 用户取消分享后执行的回调函数
                    }
                });

            });

            //被禁用用户
            @if(@$block)
            Popup.init({
                popTitle:'服务中心',//此处标题随情况改变，需php调用
                popHtml:'<p>账号被禁用，请联系客服处理！</p>',//此处信息会涉及到变动，需php调用
                popOkButton:{
                    buttonDisplay:true,
                    buttonName:"联系客服",
                    buttonfunction:function(){
                        //此处填写拨打电话的脚本
                        window.location.href = 'tel://4006363555';
                    }
                }
            });
            @endif
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
                $('#search_tip').html('搜索讲师/课程');
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
                historySearch.empty(); //删除h_kwd_list这个键值的里面所有的值
                $(".public_search_delete").remove();
                $(".public_search_delete_con").remove();
            });

            var course_current_page = 1, course_last_page = '{{$courses->lastPage()}}';
            $(".gl_list2_more").click(function(){//点击加载更多按钮
                if(course_last_page > course_current_page)
                {
                    course_current_page ++;
                    $.ajax({
                        type: 'post',
                        url: '{{route('course.course_list')}}',
                        data: {page:course_current_page},
                        dataType: 'json',
                        success: function (res) {
                            if(res)
                            {
                                var course_data = res.data;
                                var course_ul_li ='';
                                $.each(course_data,function(k,v){
                                    
                                    course_ul_li += '<li>';
                                    course_ul_li += '<a href="{{route('course.detail')}}/'+ v.id+'">';
                                    course_ul_li += '<div class="gl_list2_xz">'+ v.agency.agency_name.substr(0,4) +'</div>'; 
                                    course_ul_li += '<div class="gl_list2_img"><img src="'+ v.picture +'" alt=""/></div>'; 
                                    course_ul_li += '<div class="gl_list2_div">';
                                    course_ul_li += '<div class="gl_list2_title">'+ v.title +'</div>';
                                    course_ul_li += '<div class="gl_list2_span">';
                                    
                                    if(v.type == 1){
                                        course_ul_li += '<span class="lcd_banner_span_1">免费</span>';
                                    }else{
                                        course_ul_li += '<span class="lcd_banner_span_1">¥'+ v.price +'</span>';
                                        course_ul_li += '<span class="lcd_banner_span_2">¥'+ v.original_price +'</span>';
                                    }
                                    course_ul_li += '</div>';
                                    course_ul_li += '</div>';
                                    course_ul_li += '<div class="gl_list2_people"><span>'+ v.participate_num +'</span>/'+ v.allow_num +'人</div>';
                                    course_ul_li += '<div class="gl_list2_address"><img src="images/look/address_bg.png" alt=""/> '+ v.course_addr +'</div>';
                                    course_ul_li += '</a>';
                                    course_ul_li += '</li>';

                                })
                                if(course_ul_li!='')
                                {
                                    $('.gl_list2').append(course_ul_li);
                                }
                            }
                        }
                    });
                    if(course_current_page >= course_last_page){
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

@endsection
