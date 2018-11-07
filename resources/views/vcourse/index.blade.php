@extends('layout.default')
@section('title', '和润万青父母学院')
@section('content')
<div id="subject">
    <div id="main">
        <div class="good_looking">            

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
                                <li> <span class="img_{{$item->id}}" style="background:url({{ config('constants.admin_url').$item->image_url}}) left top round; background-size:100%;">
                                    </span></li>
                                @elseif($item->redirect_type == 2)
                                <li>
                                    <a href="{{$item->redirect_url}}">
                                        <span class="img_{{$item->id}}" style="background:url({{ config('constants.admin_url').$item->image_url}}) left top round; background-size:100%">
                                    </span>
                                    </a>
                                @else
                                 <li><a href="{{route('course.staticlink',['id'=>$item->id])}}">
                                        <span class="img_{{$item->id}}" style="background:url({{ config('constants.admin_url').$item->image_url}}) left top  round; background-size:100%">
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
            
            <table border=0 style="width:100%;background-color:#fff;height:3.125rem;border-bottom: 1px solid #f6f6f6;">
            	<tr>
            		<td align='center' valign="middle">
            			@if(request('ob') == 'created_at')
		    				<img src="/images/vcourse/zuixin-2.png" style="width:1.468rem"/>
		    			@else
		    				<img src="/images/vcourse/zuixin-1.png" style="width:1.468rem"
		    					onclick="location.href='{{route('vcourse')}}?ob=created_at'"
		    				/>
		    			@endif
            		</td>
            		<td align='center' valign="middle">
            			@if(request('ob') == 'view_cnt')
		    				<img src="/images/vcourse/zuire-2.png" style="width:1.468rem"/>
		    			@else
		    				<img src="/images/vcourse/zuire-1.png" style="width:1.468rem"
		    					onclick="location.href='{{route('vcourse')}}?ob=view_cnt'"
		    				/>
		    			@endif
            		</td>
            		<td align='center' valign="middle">
            			@if(request('ob') == 'biting')
		    				<img src="/images/vcourse/biting-2.png" style="width:1.468rem"/>
		    			@else
		    				<img src="/images/vcourse/biting-1.png" style="width:1.468rem"
		    					onclick="location.href='{{route('vcourse')}}?ob=biting'"
		    				/>
		    			@endif
            		</td>
            	</tr>
            </table>
            
            @foreach ($vcourseList as $item)
            <div class="vcoures-item">
            	<div class="title">
            		<a href="{{route('vcourse.detail',['id'=>$item->id])}}">{{ $item->title }}</a>
            		<a href="{{route('vcourse.detail',['id'=>$item->id])}}"><img src="/images/vcourse/look_listen.png" /></a>
            	</div>
            	<div class="time">
            		{{ explode(' ',$item->created_at)[0]}}            		
            	</div>
            	
            	@if($item->vcourse_des)
            	<div class="desc">
            		{{ $item->vcourse_des }}
            	</div>
            	@endif
            	<div class="cover">
            		<a href="{{route('vcourse.detail',['id'=>$item->id])}}">
            		@if($item->cover)
                       <img class="lazy" src="/images/vcourse/default-v-cover.jpg" data-echo="{{ config('constants.admin_url').$item->cover}}" alt="" onerror="javascript:this.src='/images/error.jpg'"/>
                    @else
                       <img class="lazy" src="/images/vcourse/default-v-cover.jpg" data-echo="{{ config('qiniu.DOMAIN').$item->video_tran}}?vframe/jpg/offset/{{ config('qiniu.COVER_TIME')}}" alt="" onerror="javascript:this.src='/images/error.jpg'"/>
                    @endif
                    </a>
            	</div>
            	<div class="foot">
            		<span class="cnt">{{$item->view_cnt}}人观看</span>
            		<span class="pinglun"></span>
            		<span class="favor" vid="{{ $item->id }}">
            			<table>
            				<tr>
            					<td valign="middle">
            					@if($item->userFavor)
            						<img src="/images/vcourse/xin-2.png"></td>
            					@else
            						<img src="/images/vcourse/xin-1.png"></td>
            					@endif
            					<td valign="middle">收藏</td>
            				</tr>
            			</table>
            		</span>
            	</div>
            </div>
           @endforeach
           <div style="height:3rem;"></div>
            
        	@include('element.nav', ['selected_item' => 'nav2'])
    </div>
</div>
    
    <style type="text/css">
        .win-qrcode {
            z-index: 1000000;
            position: fixed;
            background-size: contain;
            display: flex;
            height: 100vh;
            width:100%;
            background-color: #333333;
            top: 0;
            opacity: 0.9;
        }
        
        .win-qrcode-body {
            z-index: 1000001;
            position: fixed;
            background-size: contain;
            display: flex;
            top: 0;
            height: 100vh;
        }
        
        .win-qrcode-body .content{
            width: 80%;
            margin: auto;
            position: relative
        }
        
        .win-qrcode-body .content span{
            width: 2rem;
            height: 2rem;
            right: 0;
            top: 2rem;
            /* background-color: #FF7800; */
            position: absolute;
        }
        
        .win-qrcode-body .content img{
            width:100%;
        }
    </style>

<div class="win-qrcode-body" style="display:none;">
    <div class="content">
        <span onclick='$(".win-qrcode").hide();$(".win-qrcode-body").hide()'>&nbsp;</span>
        <img src="/images/index/tipleft0.png" onclick="location.href='/article/6'" />
   </div>
</div>
<div class="win-qrcode" style="display:none;">
    
</div>
    
@endsection
@section('script')
<script type="text/javascript" src="/js/jquery.event.drag-1.5.min.js"></script><!--幻灯效果-->
<script type="text/javascript" src="/js/jquery.touchSlider.js"></script><!--幻灯效果-->
<script type="text/javascript" src="/js/echo.min.js"></script>
<script type="text/javascript" src="/js/history_search.js"></script>
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js" type="text/javascript" charset="utf-8"></script>
<script>
//懒加载图片
Echo.init({
    offset: 0,
    throttle: 0
});
</script>
<script>
    (function(){
        var leftday = {{get_vip_left_day_number()}};
        if(leftday >= 1 && leftday <= 4){
            $('.win-qrcode-body .content img').attr('src', '/images/index/tipleft'+(leftday-1)+'.png');
            $('.win-qrcode-body').show();
            $('.win-qrcode').show();
        }
    })();
</script>
<script>
	$(document).ready(function(){
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
        //$(".main_visual").height($(".main_visual").width()/750*300);
	});
</script>

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

		$(".public-search-menu img").click(function(){
			$(".public-search-menu .list").toggle();
		});

		$(".public-search-menu .list .item").click(function(){
			 var search_key = $(".public_search_form_input").val();
			 location.href = '{{route('vcourse')}}'+'?search_key='+search_key+"&ob="+$(this).attr('desc');
		});
         
         //点击搜索
         $('.public_search_hot').click(function(){
             //搜索内容
             var search_key = $('>div >span',this).html();
             historySearch.store(search_key);

             location.href = '{{route('vcourse')}}'+'?search_key='+search_key+"&ob="+$('.public-search-menu .list .selected').attr('desc');
         })

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
                location.href = '{{route('vcourse.search')}}'+'?search_key='+value+"&ob="+$('.public-search-menu .list .selected').attr('desc');;
                /*----------ajax开始----------*/
                //传值为value，value是要搜索的内容

                /*----------ajax结束----------*/
            });
            $(".public_search_delete").click(function(){//删除最近搜索记录
                historySearch.empty(); //删除vc_kwd_list这个键值的里面所有的值
                $(".public_search_delete").remove();
                $(".public_search_delete_con").remove();
            });

  
});
</script>

<script type="text/javascript">
$(document).ready(function(){
	var lockf = false;
    $(".favor").click(function(){//点击收藏
        @if(!session('wechat_user'))
            window.location.href = '{{route('wechat.qrcode')}}';
        @endif
        /*----------ajax开始----------*/
        var vid = $(this).attr('vid');
        var img = $(this).find('img').eq(0);
        if (lockf) {return;}
        lockf = true;
        $.get("{{route('vcourse.add_favor')}}",{ vcourse_id:vid },function(res){
               if (res.code == 0) {
                    //ajax成功返回事件开始
                    img.attr('src','/images/vcourse/xin-1.png');
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
                    img.attr('src','/images/vcourse/xin-2.png');
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

<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">
	$(document).ready(function(){
		wx.config(<?php echo $wx_js->config(array("onMenuShareAppMessage", "onMenuShareTimeline"), false) ?>);
	    wx.ready(function () {
	        wx.onMenuShareAppMessage({
	            title: '365天，和全国精英家长一起，成为更懂教育的父母', // 分享标题
	            desc: '我们穷尽一生的时间爱孩子，却很少关注自身的提升', // 分享描述
	            link: '{{route('vcourse')}}?from=singlemessage', // 分享链接
	            imgUrl: '{{url('/images/my/dis_in_love.jpg')}}', // 分享图标
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
	            title: '365天，和全国精英家长一起，成为更懂教育的父母', // 分享标题
	            link: '{{route('vcourse')}}', // 分享链接
	            imgUrl: '{{url('/images/my/dis_in_love.jpg')}}', // 分享图标
	            success: function () {
	                // 用户确认分享后执行的回调函数
	            },
	            cancel: function () {
	                // 用户取消分享后执行的回调函数
	            }
	        });
	    });
	});
</script>

@endsection