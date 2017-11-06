@extends('layout.default')
@section('content')
    <div id="subject">
        <div id="main">
            <div class="good_asking">

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
                                        <span class="img_{{$item->id}}" style="background:url({{ config('constants.admin_url').$item->image_url}}) left top no-repeat; background-size:100%">
                                    </span>
                                    </a>
                                @else
                                 <li><a href="{{route('course.staticlink',['id'=>$item->id])}}">
                                        <span class="img_{{$item->id}}" style="background:url({{ config('constants.admin_url').$item->image_url}}) left top no-repeat; background-size:100%">
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

                    <div class="ga_div_3"  >
                        <div class="clearboth"></div>
                        @if(count($talks))
                            <ul class="ga_div_3_list">
                                @foreach($talks as $item)
                                <li>
                                    <a href="{{route('question.talk',['id'=>$item->id])}}">
                                        <div class="ga_div_3_list_div">
                                            <div class="ga_div_3_list_img"><img src="{{url($item->ask_user->profileIcon)}}" alt=""/></div>
                                            <div class="ga_div_3_list_name">{{$item->ask_user->realname or $item->ask_user->nickname}}</div>
                                            <div class="ga_div_3_list_source">来自  {{@$item->ask_user->c_city->area_name}} {{config('constants.user_label')[$item->ask_user->label]}}</div>
                                        </div>
                                        <div class="ga_div_3_list_people">{{$item->view or 0}}人已看</div>
                                        <div class="ga_div_3_list_problem">
                                            @foreach($item->tags as $tag)
                                                  <span>#{{$tag->title}}#</span>
                                            @endforeach
                                            {{$item->title}}
                                        </div>
                                        <div class="ga_div_2_list_p two_line"><pre>{!! replace_em($item->content) !!}</pre></div>
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="search_none">没有搜索到相关信息</div>
                        @endif

                        @if($talks->hasMorePages())
                            <div class="gl_list2_more talk_more">点击加载更多...</div>
                        @endif
                        @if(!session('wechat_user'))
                        <a class="lcd_evaluate lcd_evaluate_fa" href="{{ route('wechat.qrcode')}}">发帖</a>
                        @else
                        <a class="lcd_evaluate lcd_evaluate_fa" href="{{ route('question.ask_talk')}}">发帖</a>
                        @endif
                    </div>
                </div>
            </div>
            @include('element.nav', ['selected_item' => 'nav4'])
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript" src="/js/audio_play.js"></script>
    <script type="text/javascript" src="/js/history_search.js"></script>
    <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js" type="text/javascript" charset="utf-8"></script>
	<style type="text/css">
	.two_line { height: 29px; overflow: hidden; }
	</style>
    <script>
     $(document).ready(function(){
         

     @if ($is_guest)
         $('.lcd_evaluate, .ga_div_2_list_problem a').click(function() {
             var url = $(this).attr('href');
             Popup.init({
                 popHtml:'<p>您尚未注册，请先完成注册。</p>',
                 popOkButton:{
                     buttonDisplay:true,
                     buttonName:"去注册",
                     buttonfunction:function(){
                         window.location.href='/user/login?url='+ encodeURIComponent(url);
                         return false;
                     }
                 },
                 popCancelButton:{
                     buttonDisplay:true,
                     buttonName:"否",
                     buttonfunction:function(){}
                 },
                 popFlash:{
                     flashSwitch:false
                 }
             });
             return false;
         });
     @endif


         /*----------互助榜加载更多----------*/
         var talk_current_page = 1, talk_last_page = '{{$talks->lastPage()}}';
         $(".talk_more").click(function(){//点击加载更多按钮
             if(talk_last_page > talk_current_page)
             {
                 var theight = $('.talk_more').offset().top;
                 talk_current_page ++;
                 $.ajax({
                     type: 'post',
                     url: '{{route('question.talk_list')}}',
                     data: {page:talk_current_page,search_key:'{{request('search_key')}}',search_tag:'{{request('search_tag')}}',selected_tab:'{{request('selected_tab')}}'},
                     dataType: 'json',
                     success: function (res) {
                         if(res) {
                             var talk_data = res.data;
                             var talk_ul_li ='';
                             $.each(talk_data,function(k,v){
                                 var name = v.ask_user.nickname;
                                 if(v.ask_user.realname){
                                     name = v.ask_user.realname;
                                 }
                                 var label = '暂无';
                                 if(v.ask_user.label == 2){
                                     label = '爸爸';
                                 }
                                 else if(v.ask_user.label == 3)
                                 {
                                     label = '妈妈';
                                 }

                                 talk_ul_li+= '<li>';
                                 talk_ul_li+= '<a href="{{route('question.talk')}}/'+ v.id+'">';
                                 talk_ul_li+= '<div class="ga_div_3_list_div">';
                                 talk_ul_li+= '<div class="ga_div_3_list_img"><img src="'+v.ask_user.profileIcon+'" alt=""/></div>';
                                 talk_ul_li+= '<div class="ga_div_3_list_name">'+name+'</div>';
                                 talk_ul_li+= '<div class="ga_div_3_list_source">来自  '+v.ask_user.c_city.area_name+' '+label+'</div>';
                                 talk_ul_li+= '</div>';
                                 talk_ul_li+= '<div class="ga_div_3_list_people">'+ v.view+'人已看</div>';
                                 talk_ul_li+= '<div class="ga_div_3_list_problem">';

                                 $.each(v.tags,function(kk,vv){
                                     talk_ul_li+= '<span>#'+vv.title+'#</span>';
                                 })

                                 talk_ul_li+= v.title+'</div>';
                                 talk_ul_li+= '<div class="ga_div_2_list_p two_line">'+ replace_em(v.content)+'</div>';
                                 talk_ul_li+= '</a>';
                                 talk_ul_li+= '</li>';
                             })
                             if(talk_ul_li!='')
                             {
                                 $('.ga_div_3_list').append(talk_ul_li);
                                 $("html,body").animate({scrollTop:theight}, 1000);
                             }
                         }
                     }
                 });
                 if(talk_current_page >= talk_last_page){
                     $(".talk_more").hide();
                 }
             }
         });
        });
    </script>
    <script type="text/javascript">
        //qq表情替换
        function replace_em(str){
            str = str.replace(/\</g,'&lt;');
            str = str.replace(/\>/g,'&gt;');
            str = str.replace(/\n/g,'<br/>');
            str = str.replace(/\[em_([0-9]*)\]/g,'<img src="../images/face/$1.gif" border="0" />');
            return str;
        }

        $(document).ready(function(){

            //标签搜索   问题榜/互助榜
            $(".ga_div_2 .ga_div_ul li,.ga_div_3 .ga_div_ul li").click(function(){//点击ga_div_ul中的li事件
                var value=$(this).attr("data-value");
                location.href = '{{route('question')}}'+'?search_tag='+value+'&selected_tab='+$(".ga_tab .selected").attr('index_v');
            });

            /*----------音频播放---------*/
            audioPlay.init({
                url:'{{route('question.question_listen')}}'
            });

            /*-----------需支付跳转到详情页操作------*/
            $(document).on('click','.audio_cant_play',function(){
                @if(!session('wechat_user'))
                    window.location.href = '{{route('wechat.qrcode')}}';return;
                @endif
                var qid = $(this).attr('qid');
                location.href  = '{{route('wechat.question')}}?id='+qid;
            });

            //默认加载tab
            $(".ga_tab li").each(function(){
                if($(this).attr("class")=="selected"){
                    $(".ga_div>div").hide();
                    switch($(this).attr("id")){
                        case "ga_tab_1":
                            $(".ga_div_1").show();
                            break;
                        case "ga_tab_2":
                            $(".ga_div_2").show();
                            break;
                        case "ga_tab_3":
                            $(".ga_div_3").show();
                            break;
                        default:
                            break;
                    }
                }
            });

            $(".ga_tab li").click(function(){//tab切换
                location.href = '{{route('question')}}'+'?selected_tab='+($(this).index()+1);
            });
            /*$(".ga_tab li").click(function(){//tab切换
                if($(this).attr("class")!="selected"){
                    $(".ga_tab li").attr("class","");
                    $(this).attr("class","selected");
                    $(".ga_div>div").hide();
                    var placeholder = $(".public_search_form_input").attr('placeholder');
                    switch($(this).attr("id")){
                        case "ga_tab_1":
                            location.href = '{{route('question')}}'+'?selected_tab=1';
                            placeholder = '智慧榜家长/问题/城市';
                            $(".ga_div_1").show();
                            break;
                        case "ga_tab_2":
                            location.href = '{{route('question')}}'+'?selected_tab=2';
                            placeholder = '用户名/问题/城市';
                            $(".ga_div_2").show();
                            break;
                        case "ga_tab_3":
                            location.href = '{{route('question')}}'+'?selected_tab=3';
                            placeholder = '用户名/问题/城市/话题';
                            $(".ga_div_3").show();
                            break;
                        default:
                            break;
                    }
                    $(".public_search_form_input").attr('placeholder',placeholder);
                    $('#search_tip').html(placeholder);
                }
            });*/

            //$(".ga_tab li.selected").removeClass('selected').click();

            //点赞
            var like_lock = false;
            $('.ga_div_1_list').on("click",'.ga_div_1_list_information>.div_list_zambia',function () {//点赞
                var user_id = $(this).attr("id");
                var _self = $(this);
                if (like_lock) return;
                like_lock = true;
                $.ajax({
                    type: 'post',
                    url: '{{route('question.teacher.like')}}',
                    data: {"user_id": user_id},
                    success: function (res) {
                        Popup.init({
                            popHtml: res.message,
                            popFlash: {
                                flashSwitch: true,
                                flashTime: 2000
                            }
                        });
                        if (res.code == 0) {
                            _self.html( ' <img src="/images/public/zambia_on.png" alt=""/>'+"<span>"+res.data+"</span>");
                        }
                        like_lock = false;
                    }
                });
            });
        });


    </script>
@endsection
