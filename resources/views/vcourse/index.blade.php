@extends('layout.default')
@section('title', '好父母学院')
@section('content')
<div id="subject">
    <div id="main">
        <div class="good_looking">            
            <div class="public_search"  style="display:block;" >
                <form class="public_search_form">
                    <div class="public_search_form_div"><input class="public_search_form_input" type="text" name="search_key" value="{{request('search_key')}}" placeholder="搜索讲师/课程" ></div>
                    <div class="public-search-menu">
                    	<img src="/images/vcourse/menu.png"/>
                    	<div class="list" style="display:none">
                    		@if(request('ob') == 'created_at')
                    			<div desc="created_at" class="item selected">更新时间</div>
                    			<div desc="view_cnt" class="item">观看人数</div>
                    		@else
                    			<div desc="created_at" class="item">更新时间</div>
                    			<div desc="view_cnt" class="item  selected">观看人数</div>
                    		@endif
                    	</div>
                    </div>
                </form>
                <div class="public_search_hot" style="display: none">
                    <div>搜索"<span>东风</span>"</div>
                </div>
                <dl class="public_search_quick" style="display: none">
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
            <div style="height:3rem"></div>
            <div style="width:100%;background-color:#fff;height: 2.5rem;margin-bottom: 0.5rem;">
            	<table style="width:100%;height:100%;text-align:center">
            		<tr>
            			<td><a style="font-size:1rem;" href="{{$telecast}}">进入直播</a> <img style="width:1rem" src="/images/vcourse/telecast.png"/></td>
            			<td style="width:1%"><img style="height:1.2rem" src="/images/vcourse/line.png"/></td>
            			<td><a style="font-size:1rem;" href="{{$foreshow}}">精彩预告</a> <img style="width:0.8rem" src="/images/vcourse/foreshow.png"/></td>
            		</tr>
            	</table>
            </div>
            
            @foreach ($vcourseList as $item)
            <div class="vcoures-item">
            	<div class="title">
            		<a href="{{route('vcourse.detail',['id'=>$item->id])}}">{{ $item->title }}</a>
            		<a href="{{route('vcourse.detail',['id'=>$item->id])}}"><img src="/images/vcourse/look.png" /></a>
            	</div>
            	<div class="time">
            		{{ $item->created_at}}            		
            	</div>
            	
            	@if($item->vcourse_des)
            	<div class="desc">
            		{{ $item->vcourse_des }}
            	</div>
            	@endif
            	<div class="cover">
            		<a href="{{route('vcourse.detail',['id'=>$item->id])}}">
            		@if($item->cover)
                       <img src="{{ config('constants.admin_url').$item->cover}}" alt="" onerror="javascript:this.src='/images/error.jpg'"/>
                    @else
                       <img src="{{ config('qiniu.DOMAIN').$item->video_tran}}?vframe/jpg/offset/{{ config('qiniu.COVER_TIME')}}" alt="" onerror="javascript:this.src='/images/error.jpg'"/>
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
@endsection
@section('script')

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
@endsection