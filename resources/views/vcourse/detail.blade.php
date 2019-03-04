@extends('layout.default')
@section('content')
<link href="/qiniu/js/videojs/video-js.min.css" rel="stylesheet">
<style>
    .vjs-big-play-button {
	    background-image: url(/images/vcourse/play.png);
	    border: none;
	    background-color: transparent;
	    background-size: 50%;
	    background-repeat: no-repeat;
	    background-position: center;
    }
    
    .show-mark-tip {
	    width: 100%;
	    text-align: center;
	    display: flex;
	    justify-content: center;
	    align-items: center;
	    height: 1.5rem;
	}
    
    .show-mark-tip .img {
	    width: 1.01rem;
	    -moz-transform: rotate(-180deg);
	    -webkit-transform: rotate(-180deg);
    	transform: rotate(-180deg);
    	vertical-align: super;
    }
    .show-mark-tip .selected {
	    -moz-transform: rotate(0deg);
	    -webkit-transform: rotate(0deg);
    	transform: rotate(0deg);
    }
    .image-ad {
        position: absolute;
        top: 0;
        margin: auto;
        text-align: center;
        z-index: 101;
        width: 100%;
    }
    .image-ad .image-ad-body{
        position: relative;
        margin: auto;
        width: fit-content;
    }
    .image-ad .image-ad-body img{
        height: 8.5rem;
        margin-top: 1rem;
    }
    .image-ad .image-ad-body .close{
        position: absolute;
        top:1rem;
        right: 0;
    }
    .image-ad .image-ad-body .close img{
         width: 1.5rem;
        height: 1.5rem;
        margin-top: .2rem;
    }
    
    .video-ad-timer {
	position: absolute;
        background: #666;
        opacity: .8;
        top: .1rem;
        right: .1rem;
        z-index: 100;
        font-size: 0.66rem;
        color: white;
        display: flex;
    	align-items: center;
        justify-content: center;
        padding: .1rem .2rem;
    }            
    .video-ad-timer .seconds {
    	color: #fc6c02;
        padding: 0 .3rem;
    }
    
    .video-ad-timer .close{
        box-sizing: border-box;
        padding-left: .3rem;
        color:#fff;
        border-left: 1px solid #fff;
    }
    
    .video-ad-detail {
    	position: absolute;
        background: #e65800;
        opacity: .8;
        top: 8.5rem;
        right: .1rem;
        z-index: 100;
        font-size: 0.66rem;
        color: white;
        display: flex;
    	align-items: center;
        justify-content: center;
        padding: .1rem .2rem;
    }
</style>
<!--视频广告-->
<div class="video-ad-timer" style="display:none">
    <span class="seconds"></span>
    <span class="close">关闭广告&nbsp;X</span>
</div>
<div class="video-ad-detail" style="display:none">了解详情 ></div>

<!--图片广告--> 
<div class="image-ad" style='display:none'>
    <div class="image-ad-body" onclick='window.hrwqAd.toImageLink()'>
        <img id="ad-image" src=""/>
        <div class='close' onclick="window.hrwqAd.hideImageAd()">
            <img src='/images/public/public_search_form_input_delete.png'/>
        </div>
    </div>
</div>

<script src="/qiniu/js/videojs/video.min.js"></script>
<div id="subject">
    <div id="main">
        <div class="look_charge_details">
        	
            <div class="lcd_banner">
                @if(count($vcourseDetail->order)>0 && $vip_left_day > 0)
                    <div class="lcd_banner_img" order="gt_0" id="video-container" data-flg="tran" data-url="{{config('qiniu.DOMAIN').$vcourseDetail->video_tran}}"></div>
                @elseif($vcourseDetail->type=='2' && $vip_left_day === 0)
                    <div class="lcd_banner_img" order="type_2_vip_1" id="video-container" data-flg="free" data-url="{{config('qiniu.DOMAIN').$vcourseDetail->video_free}}"></div>
                @else
                    <div class="lcd_banner_img" order="other">
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
            
            @if($vcourseDetail->type=='2')
        	<div class="vip-status-show" style="display:none">
        	 @if($vip_left_day === 0)
        		<span></span>试看版
        	 @else
        		VIP完整版
        	 @endif
        	</div>
            @endif
            
            @if($vcourseDetail->type=='2')
            <div style="width:100%;text-align:right;line-height: 1.875rem;color:#666666;font-size:0.65rem;">
            	@if(!@$user_info['mobile'])
        		 	注册即可领取7天会员，收听完整课程&nbsp;&nbsp;<a style="margin-right: 1.25rem;color: white;border-radius: 5px;font-size:0.60rem;padding: 0.15rem;background-color:#ed6d11"
        		 	 href="{{route('user.login')}}">&nbsp;立即注册&nbsp;</a>
	        	@else
	        		邀请好友收听并注册，立得7天会员奖励&nbsp;&nbsp;<img id="share-logo" style="margin-right: 1.25rem;width:0.75rem;vertical-align: text-top;" src="/images/vcourse/share-logo.png"/>
	        	@endif
            </div>
            @endif
            
           
            <ul class="lcd_tab" style="display:none">
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
                    <div class="lcd_div_2_title" style="font-size: 0.9375rem;padding-top: 0.90rem;padding-bottom: 0.90rem;padding-right: 0px;">
                    	<span style="width: 0.1875rem;height:1.875rem;background-color: #ed6d11">&nbsp;</span>&nbsp;	
                    	课程作业：{{ $vcourseDetail->work }}
                    </div>                    
                   
                    @if(count($vcourseMarkListA)>0||count($vcourseMarkListB)>0)
                        <ul class="lcd_div_2_list">
                        @foreach($vcourseMarkListA as $item)
                        <li>
                            <div class="lcd_div_2_list_img"><img src="{{url($item->user->profileIcon)}}" alt=""/></div>
                            <div class="lcd_div_2_list_title">
                                {{@$item->user->nickname}}
                                @if($item->user->role == 2)(指导师)@endif
                            </div><!--需要链接直接加a标签就行-->
                            <div class="lcd_div_2_list_time">{{@date('Y-m-d',strtotime($item->created_at))}}</div><!--需要链接直接加a标签就行-->
                            <div class="lcd_div_2_list_p">
                            	<span style="color:#f39800">@if($item->mark_type=='1')#笔记#@elseif($item->mark_type=='2')#作业#@endif</span>
                            	{{$item->mark_content}}
                            	<div><a href="javascript:replay('{{@$item->user->openid}}',{{$item->id}},'{{@$item->user->nickname}}')" style="color:#52a3ff;cursor: pointer;">回复</a></div>
                            	<div id="replay-{{$item->id}}">
                            	@if(count($item->subs) > 0)
	                            	<div class='replay-list'>
	                            		@foreach($item->subs as $sk=>$sv)                           				                            				
	                            				@if($sk > 1)
	                            					<div class="replay-list-item-hide" style="display:none">
	                            				@else
	                            					<div>
	                            				@endif 	 
	                            					{{@$sv->user->nickname}}: {{$sv->mark_content}}
	                            					<!-- <a href="javascript:replay({{$sv->id}},'{{@$sv->user->nickname}}')" style="color:#52a3ff;cursor: pointer;">回复</a> -->
	                            				</div>	                            			                           			
	                            		@endforeach
	                            	</div>
                            		
                            		@if(count($item->subs) > 2)
                            			<div onclick="replayListMore(this)"><a href="javascript:;" style="font-size:12px;color:#52a3ff;">查看更多 >></a></div>
                            		@endif
                            	@endif
                            	</div>
                            </div><!--需要链接直接加a标签就行-->
                            <div class="lcd_div_2_list_zambia" data-id="{{$item->id}}"><span id='like_{{$item->id}}'>{{$item->likes}}</span> @if(count($item->like_record)>0)<img src="/images/public/zambia_on.png" alt=""/>@else<img src="/images/public/zambia.png" alt=""/>@endif</div><!--data-id为本条信息的id，用来想处理页面传送点赞的信息-->
                        </li>
                        @endforeach
                        @foreach($vcourseMarkListB as $k => $item)
                        <li>
                            <div class="lcd_div_2_list_img"><img src="{{url(@$item->user->profileIcon)}}" alt=""/></div>
                            <div class="lcd_div_2_list_title">
                                {{@$item->user->nickname}}
                                 @if($item->user->role == 2)(指导师)@endif
                            </div><!--需要链接直接加a标签就行-->
                            <div class="lcd_div_2_list_time">{{@date('Y-m-d',strtotime($item->created_at))}}</div><!--需要链接直接加a标签就行-->
                            <div class="lcd_div_2_list_p">
                            	<span style="color:#f39800">@if($item->mark_type=='1')#笔记#@elseif($item->mark_type=='2')#作业#@endif</span>
                            	{{$item->mark_content}}
                            </div><!--需要链接直接加a标签就行-->
                            <div class="lcd_div_2_list_zambia" data-id="{{$item->id}}">@if($k == 0) <img src="/images/vcourse/v-hot-detail.png" style="width:2.68rem"> @endif<span id='like_{{$item->id}}'>{{$item->likes?$item->likes:''}}</span> @if(count($item->like_record)>0)<img src="/images/public/zambia_on.png" alt=""/>@else<img src="/images/public/zambia.png" alt=""/>@endif</div><!--data-id为本条信息的id，用来想处理页面传送点赞的信息-->
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

<a class="lcd_evaluate lcd_evaluate_fa" style="background-color:#fff;background-image: url(/images/new/return.png);background-size: cover;" href="/">
	&nbsp;
</a>

<div class="win-share" style="display: none;background:url(/images/vcourse/share-shadow.jpg);top:0px;opacity: 0.9;z-index:100;width:100%;height:100%;position: absolute;background-size: contain;">
</div>


<form id="replay-form" style="position: fixed;bottom: 0px;
			background-color: #fff;border-top: 1px solid #ccc;
			width: 100%;box-sizing: border-box;display:none;    z-index: 10000; ">
			<input type="hidden" id="replay-parent-openid" name="parent_openid" />
			<input type="hidden" id="replay-vcourse-title" name="vcourse_title" value="{{$vcourseDetail->title}}"/>
			<input type="hidden" id="replay-form-parent-id" name="parent_id" value="0" />
        	<input type="hidden" name="vcourse_id" value="{{$vcourseDetail->id}}"/>
        	<input type='hidden' name='_token' value="{{csrf_token()}}">
        	<input type='hidden' name='mark_type' value='2' />
        	<div class="lcd_div_2_form" style="padding-top:10px">
        			<div class="lcd_div_2_form_textarea" style="text-align: center">
        				<textarea id="replay-form-textarea" 
        					placeholder="xxx 回复 xxx" 
        					name="mark_content" 
        					style="width: 21.875rem;border: 1px solid #ed6d11;box-sizing: border-box;
    							height: 7.9rem;border-radius: 5px;background-color: #fcfcfc;
    							color: #bcbbbb;font-size: 0.82rem; padding: 0.5rem;"></textarea>
        			</div>
                    <div style="text-align: center;padding-top: 0px;padding-bottom:5px">
                    	<input id="replay-form-submit" type="submit" value="回复"
                    	style="border:none;background-color: #ed6d11;color: #fff;width: 8rem;
                    	border-radius: 5px;padding: 0.2rem;box-sizing: content-box;">
                    	
                    	<a style="font-size:0.875rem" href="javascript:closeReplay()">关闭</a>
                    </div>
        	</div>
</form>

@if(count($vcourseDetail->order)>0)
		<form id="mark-form" style="position: fixed;bottom: 0px;
			background-color: #fff;border-top: 1px solid #ccc;
			width: 100%;box-sizing: border-box; ">
        	<input type="hidden" name="vcourse_id" value="{{$vcourseDetail->id}}"/>
        	<input type='hidden' name='_token' value="{{csrf_token()}}">
        	<div class="show-mark-tip" >
        		<img class="img" src="/images/vcourse/v-down-detail.png"/>
        	</div>
        	
        	<div id="mark-input-tip" style="width:100%;display: flex;flex-direction: row;align-items: center;margin: 0 0 0.625rem 0;">
        		<span onclick="$('#mark-input-tip').hide();$('.lcd_div_2_form').show();" 
        			 style="padding:0.05rem 0 0.05rem.5rem;;font-size:0.9375rem;width:13rem;background-color: #f7f7f7;color:#bfbfbf;line-height:1.718rem;border-radius:20px;border:1px solid #bfbfbf;margin: 0 1.25rem 0 1.25rem;">
        			<img src="/images/vcourse/v-pan-detail.png" style="width:0.84rem;"/>
        			&nbsp;写作业聊教子感悟
        		</span>
        		<img src="/images/vcourse/v-detail.png" 
        			onclick="$('.lcd_tab li').removeClass('selected');$('#lcd_tab_1').addClass('selected');$('.lcd_div>div').hide();$('.lcd_div_1').show();" 
        			style="width:1.84rem;margin-right:1.375rem"/>
        		<img src="/images/vcourse/v-suggest.png" 
        			onclick="$('.lcd_tab li').removeClass('selected');$('#lcd_tab_3').addClass('selected');$('.lcd_div>div').hide();$('.lcd_div_3').show();"
        			style="width:1.84rem;"/>
        	</div>
        	<div style="display:none" class="lcd_div_2_form">
        			<div class="lcd_div_2_form_textarea" style="text-align: center">
        				<textarea id="lcd_div_2_form_textarea" 
        					placeholder="写作业聊教子感悟，做更懂教育的父母。" 
        					name="mark_content" 
        					style="width: 21.875rem;border: 1px solid #ed6d11;box-sizing: border-box;
    							height: 7.9rem;border-radius: 5px;background-color: #fcfcfc;
    							color: #bcbbbb;font-size: 0.82rem; padding: 0.5rem;"></textarea>
        			</div>
                    <div class="lcd_div_2_form_select1" style="display: none">
                       <input type="text" id="lcd_div_2_form_select1_dummy" class="" placeholder="" readonly="">
                       <select id="lcd_div_2_form_select1" class="dw-hsel" tabindex="-1" name="mark_type">
                          <option value="2" selected="">作业</option>
                          <option value="1">笔记</option>
                       </select>
                     </div>
                    <div class="lcd_div_2_form_select2" style="display: none;">
                        <input type="text" id="lcd_div_2_form_select2_dummy" class="" placeholder="" readonly="">
                        <select id="lcd_div_2_form_select2" class="dw-hsel" tabindex="-1" name="visible">
                           <option value="2" selected="">私密</option>
                           <option value="1">公开</option>
                         </select>
                    </div>
                    <div class="lcd_div_2_form_button" style="text-align: center;padding-top: 0px;">
                    	<input type="submit" value="提交"
                    	style="background-color: #ed6d11;color: #fff;width: 8rem;
                    	border-radius: 5px;padding: 0.2rem;box-sizing: content-box;">
                    </div>
        	</div>
		</form>
@endif

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
<style type="text/css">
    .vip-status-show {
	    position: absolute;
	    background: #ed6d11;
	    top: .5rem;
	    right: 1.375rem;
	    width: 3.43rem;
	    height: 0.93rem;
	    z-index: 100;
	    font-size: 0.5rem;
	    color: white;
	    border-radius: 5px;
	    display: flex;
    	align-items: center;
        justify-content: center;
    }            
    .vip-status-show span {
    	background-image: url(/images/vcourse/try-look.png);
	    display: inline-block;
	    width: 1rem;
	    height: 0.6rem;
	    background-size: 1.3rem;
	    vertical-align: middle;
    }
    
    .vjs-big-play-button {
	    display: block;
	    background-image: url(/images/vcourse/play.png);
	    border: none;
	    background-color: transparent;
	    background-size: 50%;
	    /* height: 5rem; */
	    background-repeat: no-repeat;
	    background-position: center;
    }
    
    .replay-list {
    	background-color: #F6F6F6;
    	padding: 0.5rem;
    	border-radius: 5px; 
    	font-size: 12px;
    }
       
</style>
<script type="text/javascript" src="{{ url('/js/ueditor.parse.min.js') }}?r=1"></script>
<script type="text/javascript" src="{{ url('/js/ad.js') }}?r=1"></script>
<script type="text/javascript">
function replace_china_char(str){
    return str.replace(/&ldquo;/g,"“").replace(/&rdquo;/g,"”");
}
                
$(document).ready(function(){
    uParse('article');
    wx.config(<?php echo $wx_js->config(array("onMenuShareAppMessage", "onMenuShareTimeline"),false) ?>);
    wx.ready(function(){
        wx.onMenuShareAppMessage({
            title: replace_china_char('{{strip_tags($vcourseDetail->title)}}'), // 分享标题
            desc: '我看到一个很好的家长课堂，可能很适合你呦', // 分享描述
            //link: '{{route('vcourse.detail',['id'=>$vcourseDetail->id])}}?from=singlemessage', // 分享链接
            link:"{{ route('share.hot',['id'=> $user_info['id'] ] ) }}"+"?back="+location.pathname,
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
            title: replace_china_char('{{strip_tags($vcourseDetail->title)}}'), // 分享标题
            //link: '{{route('vcourse.detail',['id'=>$vcourseDetail->id])}}?from=singlemessage', // 分享链接
            link:"{{ route('share.hot',['id'=> $user_info['id'] ] ) }}"+"?back="+location.pathname,
            imgUrl: '{{config('constants.admin_url').$vcourseDetail->cover}}', // 分享图标
            success: function () {
                // 用户确认分享后执行的回调函数
            },
            cancel: function () {
                // 用户取消分享后执行的回调函数
            }
        });

    });

    $('.show-mark-tip').click(function(){
        if($('#mark-input-tip').css('display') != 'none'){
            $(this).find('img').addClass('selected');
        }else{
            $(this).find('img').removeClass('selected');
        }
        $('#mark-input-tip').toggle();
        $('.lcd_div_2_form').toggle();

        $('.lcd_div>div').hide();
        $('.lcd_div_2').show();
    });
    $("#share-logo").click(function(){
    	$(".win-share").show();
    });
    $(".win-share").click(function(){
    	$(".win-share").hide();
    });
    
    //初始化广告和视频
    window.hrwqAd.init(
        $(".lcd_banner_img").data('url'), 
        @if($adImage) 
            '{{ config('constants.admin_url').$adImage->display_url}}'
        @else 
            null 
        @endif,
        @if($adImage) 
            '{{$adImage->redirect_url}}' 
        @else 
            null 
        @endif,
        @if($adVideo) 
            '{{config('qiniu.DOMAIN').$adVideo->display_url}}' 
        @else 
            null 
        @endif,
        @if($adVideo) 
            '{{$adVideo->redirect_url}}' 
        @else 
            null 
        @endif
    );
    
    var videoHight = 360/680*$(".lcd_banner_img").width();
    $(".lcd_banner_img").height(videoHight);
    
    @if($vcourseDetail->cover)
        var poster = '{{ config('constants.admin_url').$vcourseDetail->cover}}';
    @else
        var poster = window.hrwqAd.old_video_url + '?vframe/jpg/offset/{{ config('qiniu.COVER_TIME')}}';
    @endif;
    
    var player = $('<video id="video-embed" class="video-js vjs-default-skin vjs-big-play-centered" style="width: 100%;" x5-video-player-type="h5" controls="false" controlsList="nodownload" poster="'+poster+'"></video>');
    $('#video-container').empty();
    $('#video-container').append(player);

    var videoPlay = function(){
      //增加观看次数
      if (!viewSearch.has('{{@$vcourseDetail->id}}')) {
          $.post("{{route('vcourse.add_view_cnt')}}",{id:"{{@$vcourseDetail->id}}",_token:"{{csrf_token()}}"},
          function(data){},'json');
      }
      viewSearch.init('{{@$vcourseDetail->id}}');
      window.hrwqAd.videoPlayEvent();
    };
    var videoPause = function(){
        $('.lcd_banner_div').show();
        window.hrwqAd.videoPauseEvent();
    };

    var videoEnd = function(){
        if(window.hrwqAd.videoEndedEvent() == true){
            return;
        }
        
        try{
            if (browserOS() === 'android') {
                this.srcObject = new window.webkitMediaStream;
            }else if (browserOS() === 'ios'){
                this.webkitExitFullScreen();
            }
            console.log(browserOS() + "videoEnd");
        }catch(ex){
        }

        if ($('#video-container').data('flg')=='free') {
    	  @if(empty(@$user_info['mobile']))
    		Popup.init({
                    popTitle:'试看结束',
                    popHtml:'<p>完成注册可获得7天会员奖励，期间可收听父母学院全部完整版课程</p>',                
                    popOkButton:{
                        buttonDisplay:true,
                        buttonName:"立即注册",
                        buttonfunction:function(){
                            location.href="{{ route('user.login') }}";
                            return false;
                        }
                    },
                    popCancelButton:{
                        buttonDisplay:true,
                        buttonName:"狠心离开",
                        buttonfunction:function(){}
                    },
                    popFlash:{
                       flashSwitch:false
                    }
                });
          @elseif($vip_left_day === 0)
            Popup.init({
              	popTitle:'试看结束',
                  popHtml:'<p>成为和会员可收听父母学院每周更新的完整版课程</p>',                
                  popOkButton:{
                      buttonDisplay:true,
                      buttonName:"立即加入",
                      buttonfunction:function(){
                      	location.href='{{ url("article/6") }}';
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
          @else
            Popup.init({
              	popTitle:'试看结束',
                  popHtml:'<p>加入好父母学院，观看每周一期完整视频</p>',                
                  popOkButton:{
                      buttonDisplay:true,
                      buttonName:"我要加入",
                      buttonfunction:function(){
                      	location.href='{{ url("article/6") }}';
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
          @endif
      };
    };
    
    //alert('{{$vip_left_day}}');
    //已经参加课程或收费课程试看或vip
    //if(count($vcourseDetail->order)>0||$vcourseDetail->type=='2'&&@$user_info['vip_flg']=='1')
    @if((count($vcourseDetail->order) > 0 && $vip_left_day > 0) 
    	    || ($vcourseDetail->type=='2'&&@$user_info['vip_flg']=='1')
            || ($vcourseDetail->type=='2'&& $vip_left_day === 0))
	var waitingPub = null;
	try{
	    videojs('video-embed', {
	        "width": "100%",
	        "height": videoHight,
	        "controls": true,
	        "autoplay": false,
	        "preload": "auto",
	        //"poster": poster
	    }, function() {
	    	window.hrwqAd.playVideo();
            }).on("play", videoPlay).on("pause", videoPause).on("ended", videoEnd);
	}catch(exx){
	}
        
	$('.lcd_banner_div').click(function(event) {
	    var player = videojs('video-embed');
	    player.load();
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
    $(".lcd_div_2_form_button input").click(function(){//提交作业笔记表单
    	@if(empty(@$user_info['mobile']))
    		Popup.init({
            	popTitle:'',
                popHtml:'<p>注册后即可参与课程作业</p>',                
                popOkButton:{
                    buttonDisplay:true,
                    buttonName:"立即注册",
                    buttonfunction:function(){
                    	location.href="{{ route('user.login') }}";
                        return false;
                    }
                },
                popFlash:{
                    flashSwitch:false
                }
            });
        	return false;
        @endif

        
        if (lockm) {return;}
        if($("#lcd_div_2_form_textarea").val().length<10){
            alert("填写的作业笔记信息不能少于10个字符");
            return false;
        }
        var form_data = $('#mark-form').serialize();
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

                Popup.init({
                    popTitle:'提示',
                    popHtml:'<p>'+data.msg+'</p>',
                    popFlash:{
                        flashSwitch:true,
                        flashTime:1000,
                    }
                });

                $('.show-mark-tip').click();
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
            //您尚未注册，请先完成注册
        @endif
        //免费
        @if($vcourseDetail->type=='1'||@$vip_left_day > 0)
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

<script type="text/javascript">
	function replay (parent_openid,parent_id, replay_user){
		parent_openid = 'obpqNs_GdrHPLOGJig50qNcFZRGk';
		$("#replay-parent-openid").val(parent_openid)
		$("#replay-vcourse-title").val('{{$vcourseDetail->title}}')
		$('#replay-form-parent-id').val(parent_id);
		$('#replay-form-textarea').attr('placeholder', '回复 '+replay_user);
		$("#replay-form").show();
	}

	function closeReplay(){
		$("#replay-parent-openid").val('');
		$('#replay-form-parent-id').val(0);
		$('#replay-form-textarea').attr('placeholder', '');
		$("#replay-form").hide();
	}

	function replayListMore(div){
		var itemHides = $(div).parent().find('.replay-list .replay-list-item-hide');
		if($(div).find('a').eq(0).text() == '查看更多 >>'){
			itemHides.show();
			$(div).find('a').eq(0).text('隐藏更多');
		}else{
			itemHides.hide();
			$(div).find('a').eq(0).text('查看更多 >>');
		}
		
	}

	$("#replay-form-submit").click(function(){//提交作业笔记表单
    	@if(empty(@$user_info['mobile']))
    		Popup.init({
            	popTitle:'',
                popHtml:'<p>注册后即可参与课程作业</p>',                
                popOkButton:{
                    buttonDisplay:true,
                    buttonName:"立即注册",
                    buttonfunction:function(){
                    	location.href="{{ route('user.login') }}";
                        return false;
                    }
                },
                popFlash:{
                    flashSwitch:false
                }
            });
        	return false;
        @endif
        
        //if (lockm) {return;}
        if($("#replay-form-textarea").val().length<10){
            alert("填写的回复信息不能少于10个字符");
            return false;
        }
        var form_data = $('#replay-form').serialize();
        var parent_id = $('#replay-form-parent-id').val();
        lockm = true;
        $.post("{{route('vcourse.add_mark')}}", form_data,function(data){
            if(data.status){
                var mark_data = jQuery.parseJSON(data.vcourseMarkInfo);
                var mark_ul_li ='';
				var replayList = $('#replay-' + parent_id).find('.replay-list').eq(0);
				if(replayList.length == 0){
					replayList = $('<div class="replay-list"></div');
					replayList.append('<div>'+mark_data.user.nickname+':'+mark_data.mark_content+'</div>')
					$('#replay-' + parent_id).append(replayList);
				}else{
					replayList.append('<div>'+mark_data.user.nickname+':'+mark_data.mark_content+'</div>')
				} 

                $('#replay-form-textarea').val('');
                lockm = false;

                Popup.init({
                    popTitle:'提示',
                    popHtml:'<p>'+data.msg+'</p>',
                    popFlash:{
                        flashSwitch:true,
                        flashTime:1000,
                    }
                });

                $('#replay-form').hide();
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
</script>
@endsection
