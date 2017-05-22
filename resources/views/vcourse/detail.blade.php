@extends('layout.default')
@section('title','和润好父母学院')
@section('content')
<link href="/qiniu/js/videojs/video-js.min.css" rel="stylesheet">
<script src="/qiniu/js/videojs/video.min.js"></script>
<div id="subject">
    <div id="main">
        <div class="look_charge_details">
            <div class="lcd_banner">
                @if(count($vcourseDetail->order)>0)
                    <div class="lcd_banner_img" id="video-container" data-flg="tran" data-url="{{config('qiniu.DOMAIN').$vcourseDetail->video_tran}}"></div>
                @elseif($vcourseDetail->type=='2'&&@$user_info['vip_flg']=='1')
                    <div class="lcd_banner_img" id="video-container" data-flg="free" data-url="{{config('qiniu.DOMAIN').$vcourseDetail->video_free}}"></div>
                @else
                    <div class="lcd_banner_img">
                    @if($vcourseDetail->cover)
                        <img src="{{ config('constants.admin_url').$vcourseDetail->cover}}" alt="" onerror="javascript:this.src='/images/error.jpg'"/>
                    @else
                        <img src="{{ config('qiniu.DOMAIN').$vcourseDetail->video_tran}}?vframe/jpg/offset/{{ config('qiniu.COVER_TIME')}}" alt="" onerror="javascript:this.src='/images/error.jpg'"/>
                    @endif
                    </div>
                    <div class="lcd_banner_div">
                        <div class="lcd_banner_title">{{ @str_limit($vcourseDetail->title,40) }}</div>
                        <div class="lcd_banner_span">
                        @if($vcourseDetail->type=='1')
                        <span class="lcd_banner_span_1">免费</span> 
                        @else
                        <span class="lcd_banner_span_1">￥{{ $vcourseDetail->price }}</span> 
                        @endif
                        @if($vcourseDetail->current_class)
                        <span class="lcd_banner_span_3">课时：<span>{{ $vcourseDetail->current_class }}/{{ $vcourseDetail->total_class }}</span></span>
                        @endif()
                        </div>
                    </div>
                @endif
            </div>
            <ul class="lcd_tab">
                <li id="lcd_tab_1" >课程详情</li>
                <li id="lcd_tab_2" class="selected">作业&笔记</li>
                <li id="lcd_tab_3">推荐课程</li>
            </ul>
            <div class="lcd_div">
                <div class="lcd_div_1" style="display:none">
                    <dl>
                        <dt>更新时间</dt>
                        <dd><p>{{ $vcourseDetail->created_at }}</p></dd>
                        @if($vcourseDetail->suitable)
                            <dt>适合对象</dt>
                            <dd><p>{!! nl2br($vcourseDetail->suitable) !!}</p></dd>
                        @endif
                        <dt>老师介绍</dt>
                        <dd>
                            @if($vcourseDetail->teacher)
                            <p>{{ $vcourseDetail->teacher }}</p>
                            @endif
                            @if($vcourseDetail->teacher_intr)
                            <p>{!! nl2br($vcourseDetail->teacher_intr) !!}</p>
                            @endif
                        </dd>
                        @if($vcourseDetail->vcourse_obj)
                        <dt>课程目标</dt>
                        <dd>
                            <p>{!! $vcourseDetail->vcourse_obj !!}</p>
                        </dd>
                        @endif
                        @if($vcourseDetail->vcourse_des)
                        <dt>课程简介</dt>
                        <dd>
                            <article>{!! nl2br($vcourseDetail->vcourse_des) !!}</article>
                        </dd>
                        @endif
                    </dl>
                </div>
                <div class="lcd_div_2" >
                    <div class="lcd_div_2_title">课程作业：{{ $vcourseDetail->work }}</div>
                    @if(count($vcourseDetail->order)>0)
                    <form class="lcd_div_2_form">
                        <input type="hidden" name="vcourse_id" value="{{$vcourseDetail->id}}"/>
                        <input type='hidden' name='_token' value="{{csrf_token()}}">
                        <div class="lcd_div_2_form_textarea"><textarea id="lcd_div_2_form_textarea" placeholder="勤记笔记，随时随地查看，永不丢失，不少于20字符。" name="mark_content"></textarea></div>
                        <div class="lcd_div_2_form_select1">
                            <input type="text" id="lcd_div_2_form_select1_dummy" class="" placeholder="" readonly="">
                            <select id="lcd_div_2_form_select1" class="dw-hsel" tabindex="-1" name="mark_type">
                              <option value="2" selected="">作业</option>
                              <option value="1">笔记</option>
                            </select>
                        </div>
                        <div class="lcd_div_2_form_select2" style="display: none;">
                            <input type="text" id="lcd_div_2_form_select2_dummy" class="" placeholder="" readonly=""><select id="lcd_div_2_form_select2" class="dw-hsel" tabindex="-1" name="visible">
                              <option value="2" selected="">私密</option>
                              <option value="1">公开</option>
                            </select>
                        </div>
                        <div class="lcd_div_2_form_button"><input type="submit" value="提交"></div>
                    </form>
                    @endif
                   
                    @if(count($vcourseMarkListA)>0||count($vcourseMarkListB)>0)
                        <ul class="lcd_div_2_list">
                        @foreach($vcourseMarkListA as $item)
                        <li>
                            <div class="lcd_div_2_list_img"><img src="{{url($item->user->profileIcon)}}" alt=""/></div>
                            <div class="lcd_div_2_list_title">{{@$item->user->nickname}}</div><!--需要链接直接加a标签就行-->
                            <div class="lcd_div_2_list_time">{{@date('Y-m-d',strtotime($item->created_at))}}</div><!--需要链接直接加a标签就行-->
                            <div class="lcd_div_2_list_p"><span style="color:#f39800">@if($item->mark_type=='1')#笔记#@elseif($item->mark_type=='2')#作业#@endif</span>{{$item->mark_content}}</div><!--需要链接直接加a标签就行-->
                            <div class="lcd_div_2_list_zambia" data-id="{{$item->id}}"><span id='like_{{$item->id}}'>{{$item->likes}}</span> @if(count($item->like_record)>0)<img src="/images/public/zambia_on.png" alt=""/>@else<img src="/images/public/zambia.png" alt=""/>@endif</div><!--data-id为本条信息的id，用来想处理页面传送点赞的信息-->
                        </li>
                        @endforeach
                        @foreach($vcourseMarkListB as $item)
                        <li>
                            <div class="lcd_div_2_list_img"><img src="{{url(@$item->user->profileIcon)}}" alt=""/></div>
                            <div class="lcd_div_2_list_title">{{@$item->user->nickname}}</div><!--需要链接直接加a标签就行-->
                            <div class="lcd_div_2_list_time">{{@date('Y-m-d',strtotime($item->created_at))}}</div><!--需要链接直接加a标签就行-->
                            <div class="lcd_div_2_list_p"><span style="color:#f39800">@if($item->mark_type=='1')#笔记#@elseif($item->mark_type=='2')#作业#@endif</span>{{$item->mark_content}}</div><!--需要链接直接加a标签就行-->
                            <div class="lcd_div_2_list_zambia" data-id="{{$item->id}}"><span id='like_{{$item->id}}'>{{$item->likes?$item->likes:''}}</span> @if(count($item->like_record)>0)<img src="/images/public/zambia_on.png" alt=""/>@else<img src="/images/public/zambia.png" alt=""/>@endif</div><!--data-id为本条信息的id，用来想处理页面传送点赞的信息-->
                        </li>
                        @endforeach
                        </ul>
                        @endif
                </div>
                <div class="lcd_div_3" style="display:none;">
                    @if(count($recommendVcourseList)>0)
                    <ul>
                        @foreach($recommendVcourseList as $item)
                        <li>
                            <dl>
                                <dt><a href="{{route('vcourse.detail',['id'=>$item->id])}}">
                                @if($item->cover)
                                    <img src="{{ config('constants.admin_url').$item->cover}}" alt="" onerror="javascript:this.src='/images/error.jpg'"/>
                                @else
                                    <img src="{{ config('qiniu.DOMAIN').$item->video_tran}}?vframe/jpg/offset/{{ config('qiniu.COVER_TIME')}}" alt="" onerror="javascript:this.src='/images/error.jpg'"/>
                                @endif
                                <dd><a href="{{route('vcourse.detail',['id'=>$item->id])}}">{{ @str_limit($item->title,30) }}</a></dd><!--需要链接直接加a标签就行-->
                            </dl>
                        </li>
                        @endforeach
                    </ul>
                    @endif
                    <div class="clearboth"></div>
                </div>
            </div>
            <div id='is_favor' @if($userFavor) class="lcd_collection lcd_collection_yes" @else class="lcd_collection lcd_collection_no" @endif></div><!--lcd_collection_no是未收藏，lcd_collection_yes是已收藏，需要链接就把div改成a标签-->
            @if(!count($vcourseDetail->order)>0)
                @if(@$user_info['vip_flg']=='2')
                    <div class="lcd_button" id="vcourse_add" style="display:none">参加该课程(和会员免费)</div>
                @else
                    <div class="lcd_button" id="vcourse_add" style="display:none">参加该课程</div>
                @endif
            @else
                @if($vcourseDetail->order[0]->order_type=='1')
                <div class="lcd_button" onclick="location.href = '{{route('wechat.vcourse_pay')}}?id={{$vcourseDetail->order[0]->id}}';">去付款</div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection
@section('script')
<!--Mobiscroll插件调用文件开始-->
<script src="/js/Mobiscroll/mobiscroll.core.js"></script>
<script src="/js/Mobiscroll/mobiscroll.frame.js"></script>
<script src="/js/Mobiscroll/mobiscroll.scroller.js"></script>
<script src="/js/Mobiscroll/mobiscroll.select.js"></script>
<script src="/js/Mobiscroll/mobiscroll.frame.android-holo.js"></script>
<script src="/js/Mobiscroll/mobiscroll.i18n.zh.js"></script>
<link href="/css/Mobiscroll/mobiscroll.frame.css" rel="stylesheet" type="text/css" />
<link href="/css/Mobiscroll/mobiscroll.frame.android-holo.css" rel="stylesheet" type="text/css" />
<link href="/css/Mobiscroll/mobiscroll.scroller.css" rel="stylesheet" type="text/css" />
<link href="/css/Mobiscroll/mobiscroll.scroller.android-holo.css" rel="stylesheet" type="text/css" />
<!--Mobiscroll插件调用文件结束-->
<script src="/js/video_play.js"></script>
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js" type="text/javascript" charset="utf-8"></script>
@if ($vcourseDetail->type==2 && count($vcourseDetail->order)==0)
    <style type="text/css">
        .vjs-big-play-button:after {
            content: '试    看';
            position: relative;
            color: #f39800;
            top: -7px;
            white-space: pre;
            font-size: 0.8em;
            padding: 0 6px;
        }
    </style>
@endif
<script type="text/javascript" src="{{ url('/js/ueditor.parse.min.js') }}?r=1"></script>
<script type="text/javascript">
$(document).ready(function(){
    uParse('article');
    wx.config(<?php echo $wx_js->config(array("onMenuShareAppMessage", "onMenuShareTimeline"),false) ?>);
            wx.ready(function(){
                wx.onMenuShareAppMessage({
                    title: '{{strip_tags($vcourseDetail->title)}}', // 分享标题
                    desc: '我看到一个很好的家长课堂，可能很适合你呦', // 分享描述
                    link: '{{route('vcourse.detail',['id'=>$vcourseDetail->id])}}?from=singlemessage', // 分享链接
                    imgUrl: '{{config('constants.admin_url').$vcourseDetail->cover}}', // 分享图标
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
                    title: '我看到一个很好的家长课堂，可能很适合你呦', // 分享标题
                    link: '{{route('vcourse.detail',['id'=>$vcourseDetail->id])}}?from=singlemessage', // 分享链接
                    imgUrl: '{{config('constants.admin_url').$vcourseDetail->cover}}', // 分享图标
                    success: function () {
                        // 用户确认分享后执行的回调函数
                    },
                    cancel: function () {
                        // 用户取消分享后执行的回调函数
                    }
                });

            });

    var videoHight = 360/680*$(".lcd_banner_img").width();
    $(".lcd_banner_img").height(videoHight);
    var vLink = $(".lcd_banner_img").data('url');
    var vType = function() {
        $.ajaxSetup({
            headers: ''
        });
        var type = '';
        $.ajax({
            url: vLink + "?stat",
            async: false
        }).done(function(info) {
            type = info.mimeType;
            if (type == 'application/x-mpegurl') {
                type = 'application/x-mpegURL';
            }
        });

        return type;
    };
    var player = $('<video id="video-embed" class="video-js vjs-default-skin vjs-big-play-centered" style="width: 100%;"></video>');
    $('#video-container').empty();
    $('#video-container').append(player);

    var videoPlay = function(){
      //增加观看次数
      if (!viewSearch.has('{{@$vcourseDetail->id}}')) {
          $.post("{{route('vcourse.add_view_cnt')}}",{id:"{{@$vcourseDetail->id}}",_token:"{{csrf_token()}}"},function(data){
            },'json');
      }
      viewSearch.init('{{@$vcourseDetail->id}}');
    };
    var videoPause = function(){
      //$('.lcd_banner_div').show();
    };

    var videoEnd = function(){
      if ($('#video-container').data('flg')=='free') {
         Popup.init({
        	popTitle:'试看结束',
            popHtml:'<p>加入和润父母学院可永久免费观看全部视频</p>',
            popOkButton:{
                buttonDisplay:true,
                buttonName:"我要加入",
                buttonfunction:function(){
                     
                     @if(@$user_info['vip_flg']=='2')
                    	 location.href='{{ url("vip.buy") }}';
                     @else
                    	 location.href='{{ url("/user/login?url=/vcourse/detail/".@$vcourseDetail->id) }}';
                     @endif
                     return false;
                }
            },
            popCancelButton:{
                buttonDisplay:true,
                buttonName:"我不关心",
                buttonfunction:function(){}
            },
            popFlash:{
                flashSwitch:false
            }
        });
      };
    };

    @if($vcourseDetail->cover)
        var poster = '{{ config('constants.admin_url').$vcourseDetail->cover}}';
    @else
        var poster = vLink + '?vframe/jpg/offset/{{ config('qiniu.COVER_TIME')}}';
    @endif;

    //已经参加课程或收费课程试看或vip
    @if(count($vcourseDetail->order)>0||$vcourseDetail->type=='2'&&@$user_info['vip_flg']=='1')
    videojs('video-embed', {
        "width": "100%",
        "height": videoHight,
        "controls": true,
        "autoplay": false,
        "preload": "none",
        "poster": poster
    }, function() {
        this.src({
            type: vType(),
            src: vLink
        });
        var player = this;
        player.play();
    }).on("play", videoPlay).on("pause", videoPause).on("ended", videoEnd);

    $('.lcd_banner_div').click(function(event) {
        var player = videojs('video-embed');
        player.play();
    });
    @else
    $('.lcd_banner').click(function(event) {
        Popup.init({
            popHtml:'<p>参加该课程后可观看视频</p>',
            popFlash:{
                flashSwitch:true,
                flashTime:2000,
            }
        });
    });
    @endif

    
    var lockm = false;
    $(".lcd_div_2_form_button").click(function(){//提交作业笔记表单
        if (lockm) {return;}
        if($("#lcd_div_2_form_textarea").val().length<10){
            alert("填写的作业笔记信息不能少于10个字符");
            return false;
        }
        var form_data = $('form').serialize();
        lockm = true;
        $.post("{{route('vcourse.add_mark')}}", form_data,function(data){
            if(data.status){
               var mark_data = jQuery.parseJSON(data.vcourseMarkInfo);
               var mark_type ='';
               if (mark_data.mark_type=='1') {mark_type='#笔记#'};
               if (mark_data.mark_type=='2') {mark_type='#作业#'};
                var mark_ul_li ='';
                if ($('.lcd_div_2_list').length>0) {
                    mark_ul_li+='<li>';
                    mark_ul_li+='<div class="lcd_div_2_list_img">';
                    mark_ul_li+='<img src="'+mark_data.user.profileIcon+'" alt=""/></div>';
                    mark_ul_li+='<div class="lcd_div_2_list_title">'+mark_data.user.nickname+'</div>';
                    mark_ul_li+='<div class="lcd_div_2_list_time">'+mark_data.created_at.substring(0,10)+'</div>';
                    mark_ul_li+='<div class="lcd_div_2_list_p"><span style="color:#f39800">'+mark_type+'</span>'+mark_data.mark_content+'</div>';
                    $(mark_ul_li).hide().prependTo($('.lcd_div_2_list')).fadeIn('slow');
                }else{
                    mark_ul_li+='<ul class="lcd_div_2_list">';
                    mark_ul_li+='<li>';
                    mark_ul_li+='<div class="lcd_div_2_list_img">';
                    mark_ul_li+='<img src="'+mark_data.user.profileIcon+'" alt=""/></div>';
                    mark_ul_li+='<div class="lcd_div_2_list_title">'+mark_data.user.nickname+'</div>';
                    mark_ul_li+='<div class="lcd_div_2_list_time">'+mark_data.created_at.substring(0,10)+'</div>';
                    mark_ul_li+='<div class="lcd_div_2_list_p"><span style="color:#f39800">'+mark_type+'</span>'+mark_data.mark_content+'</div>';
                    mark_ul_li+='</ul>';
                    $(mark_ul_li).hide().appendTo($('.lcd_div_2')).fadeIn('slow');
                };
                
                $('#lcd_div_2_form_textarea').val('');
                lockm = false;
            }else{
               Popup.init({
                        popTitle:'失败',
                        popHtml:'<p>'+data.msg+'</p>',
                        popFlash:{
                            flashSwitch:true,
                            flashTime:2000,
                        }
                    });
               lockm = false;
            }
        },'json')
        .fail( function( jqXHR ) {
            if (jqXHR.status == 422){
                var str = '';
                $.each($.parseJSON(jqXHR.responseText), function (key, value) {
                    str += value+'<br>';
                });
                Popup.init({
                        popTitle:'失败',
                        popHtml:'<p>'+str+'</p>',
                        popFlash:{
                            flashSwitch:true,
                            flashTime:2000,
                        }
                    });
                lockm = false;
            }
        });
        return false;
    });
    $('#lcd_div_2_form_select1').mobiscroll().select({//调用Mobiscroll
        theme: "android-holo",
        mode: "scroller",
        display: "bottom",
        lang: "zh"
    });
    $('#lcd_div_2_form_select2').mobiscroll().select({//调用Mobiscroll
        theme: "android-holo",
        mode: "scroller",
        display: "bottom",
        lang: "zh"
    });
    $('#lcd_div_2_form_select1').on('change', function() {//触发lcd_div_2_form_select1
        if($(this).val()==1){
            $('.lcd_div_2_form_select2').show();
        }else{
            $('.lcd_div_2_form_select2').hide();
        }
    });
    $('#lcd_div_2_form_select1').trigger('change');//触发一次lcd_div_2_form_select1
    
    //点击参加课程
    var lockp = false;
    $("#vcourse_add").click(function(){
        @if(!session('wechat_user'))
            window.location.href = '{{route('wechat.qrcode')}}';return;
        @endif
        if (lockp) {return;}
        //未登录
        @if(empty(@$user_info['mobile']))
            Popup.init({
                popHtml:'<p>您尚未注册，请先完成注册。</p>',
                popOkButton:{
                    buttonDisplay:true,
                    buttonName:"去注册",
                    buttonfunction:function(){
                         location.href='{{ url("/user/login?url=/vcourse/detail/".@$vcourseDetail->id) }}';
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
        @endif
        //免费
        @if($vcourseDetail->type=='1'||@$user_info['vip_flg']=='2')
            lockp = true;
            $.post("{{route('vcourse.order_free')}}", { vcourse_id:{{ $vcourseDetail->id }} },function(data){
                if(data.status){
                   location.reload();
                   lockp = false;
                }else{
                   Popup.init({
                            popTitle:'失败',
                            popHtml:'<p>'+data.msg+'</p>',
                            popFlash:{
                                flashSwitch:true,
                                flashTime:2000,
                            }
                        });
                   lockp = false;
                }
            },'json')
            .fail( function( jqXHR ) {
                if (jqXHR.status == 422){
                    var str = '';
                    $.each($.parseJSON(jqXHR.responseText), function (key, value) {
                        str += value+'<br>';
                    });
                    Popup.init({
                            popTitle:'失败',
                            popHtml:'<p>'+str+'</p>',
                            popFlash:{
                                flashSwitch:true,
                                flashTime:2000,
                            }
                        });
                    lockp = false;
                }
            });
        @else
        //收费
        //location.href = '{{route('vcourse.order',['id'=>$vcourseDetail->id])}}';
        @endif
        return false;
    });
    $("#vcourse_add").click();//自动参加课程
    
    $(".lcd_tab li").click(function(){//tab切换
        if($(this).attr("class")!="selected"){
            $(".lcd_tab li").attr("class","");
            $(this).attr("class","selected");
            $(".lcd_div>div").hide();
            switch($(this).attr("id")){
                case "lcd_tab_1":
                    $(".lcd_div_1").show();
                    break;
                case "lcd_tab_2":
                    $(".lcd_div_2").show();
                    break;
                case "lcd_tab_3":
                    $(".lcd_div_3").show();
                    $('.lcd_div_3 img').height($('.lcd_div_3 img').width()*91/172)
                    break;
                default:
                    break;
            }
        }
    });
    
    var lockf = false;
    $("#is_favor").click(function(){//点击收藏
        @if(!session('wechat_user'))
            window.location.href = '{{route('wechat.qrcode')}}';
        @endif
        /*----------ajax开始----------*/
        if (lockf) {return;}
        lockf = true;
        $.get("{{route('vcourse.add_favor')}}",{ vcourse_id:{{ $vcourseDetail->id }} },function(res){
               if (res.code == 0) {
                    //ajax成功返回事件开始
                    $("#is_favor").attr("class","lcd_collection lcd_collection_no");
                    lockf = false;
                    //ajax成功返回事件结束
                }else if(res.code == 2){
                    Popup.init({
                        popHtml: '您已成功收藏该课程，可在 <b>我的</b>-<b>我的课程</b> 中查看',
                        popFlash:{
                        flashSwitch:true,
                        flashTime:2000
                        }
                    });
                    $("#is_favor").attr("class","lcd_collection lcd_collection_yes");
                    lockf = false;
                }else {
                    Popup.init({
                        popTitle:'失败',
                        popHtml:'<p>'+res.message+'</p>',
                        popFlash:{
                            flashSwitch:true,
                            flashTime:2000,
                        }
                    });
                    lockf = false;
                }
        },'json')
    });

    var lockl = false;
    $(".lcd_div_2_list_zambia").click(function(){//点赞
        var value=$(this).attr("data-id");
        if (lockl) {return;}
        lockl = true;
        $.get("{{route('vcourse.add_like')}}",{ id:value },function(res){
               if (res.code == 2) {
                    //ajax成功返回事件开始
                    var a = $("#like_"+value).text();
                    if (a=='') {a=0;}
                    $("#like_"+value).text(parseInt(a)+1);
                    $("#like_"+value).next('img').attr('src','/images/public/zambia_on.png');
                    lockl = false;
                }else {
                    Popup.init({
                        popTitle:'失败',
                        popHtml:'<p>'+res.message+'</p>',
                        popFlash:{
                            flashSwitch:true,
                            flashTime:2000,
                        }
                    });
                    lockl = false;
                }
        },'json')
    });
});
</script>
@endsection
