@extends('layout.default')
@section('content')
    <div id="subject">
        <div id="main">
            <div class="my">
                <div class="my_member_ordinary">
                    <div class="mmo_top">
                        <div class="mmo_portrait">
                        	<a href="{{route('user.profile')}}">
                        	<img src="{{url($data['profileIcon'])}}" alt=""/></a>
                        </div>
                        
                        <div class="mmo_menus">
                        	<span class="money">
                        	 <a href="{{route('user.wallet')}}">
                        		<img src="/images/my/money.png"/>
                        		@if($balanceCount)
                        			<img style="width: 0.3rem;height: 0.3rem;margin-left: -0.4rem;vertical-align: top;"
                        			 src="/images/public/point.png"/>
                        		@endif
                        	 </a>
                        	</span>
                        	<span class="setting">
                        		<a href="{{route('user.setting')}}">
                        			<img src="/images/my/setting.png" />
                        		</a>
                        	</span>
                        </div>
                        
                        <div class="mmo_line">
                        	<div class="mmo_title">
                        		{{$data['realname'] or $data['nickname']}}
                        		@if(computer_vip_left_day($data['vip_left_day']) > 0)
                        			<img src="/images/my/vip-2.png" alt=""/>
                        		@else
                        			<img src="/images/my/vip-1.png" alt=""/>
                        		@endif
                        	</div>                        	
                        	<div class="mmo_right_menu">
                        		<a href="{{route('my.courses')}}">收藏</a> 
                        		| 
                        		<a href="{{route('my.orders')}}">订单</a></div>
                        </div>                        

                        <div class="mmo_identity">
                            <a href="{{route('article',['id'=>6])}}">
                            	<div class="mmo_identity_1">
                            		<div class="grow_txt">和会员有效期</div>
                            		<div class="grow">
                            		 {{ computer_vip_left_day($data['vip_left_day']) }}
                            		 <span style="font-size:0.8rem">天</span>
                            		</div>
                            	</div>
                            </a>
                        </div>      
                                          
                    </div>
                    
                    <ul class="mmo_list" style="margin-top:0px;border-top: 1px solid #f6f6f6;">
                    	 <li>
                    	 	@if(empty(@$data['mobile']))
                    	 	<a href="{{route('user.login')}}">                    	 	
                    	 	@else
                    	 	<a href="{{route('share.angle')}}">
                    	 	@endif
                    	 		<span style="font-size:13px" >
                    	 			邀请好友注册立得7天会员&nbsp;<img src="/images/my/new-tip.png" style="width: 1.4rem;"/>&nbsp;
                    	 			<span><img src="/images/public/select_right.jpg" alt=""/></span>
                    	 		</span>
                    	 	爱心大使
                    	 	</a>
                    	</li>
                        @if($show_card === true)
                        <li>
                            <a href="{{route('partner.card')}}"><span><img src="/images/public/select_right.jpg" alt=""/></span>个人名片</a>
                        </li>
                        @endif
                    </ul>
                   <ul class="mmo_list">
                        <li><a href="{{route('my.notes')}}"><span><img src="/images/public/select_right.jpg"
                                                                       alt=""/></span>我的作业</a></li>
                        <li><a href="{{route('user.question')}}"><span><img src="/images/public/select_right.jpg"
                                                                            alt=""/></span>我的问答 @if($new_answer_questions_count>0)
                                    <div>{{$new_answer_questions_count}}</div>@endif</a></li>
                        <li style="display: none"><a href="{{route('user.question')}}"><span><img src="/images/public/select_right.jpg"
                                                                        alt=""/></span>家长圈子 @if($unreadTalkCommentCount)
                                    <div>{{$unreadTalkCommentCount}}</div>@endif</a></li>
  
                    </ul>
                    <ul class="mmo_list">
                        <li>
                        	<a href="{{route('article',['id'=>6])}}">
                        		<span> <img src="/images/public/select_right.jpg" alt=""/></span>
                        		@if(computer_vip_left_day($data['vip_left_day']) > 0 && @user_info()['finish_order'])
                        			会员续费
                        		@else
                        			开通会员
                        		@endif
                        	</a>
                        </li>                        
                       <li><a href="{{route('leaveword')}}"><span><img src="/images/public/select_right.jpg" alt=""/></span>建议留言</a></li>
                        
                        
                        @if($data['role'] == 2)
                            <li data-value="0" data-state="false" class="mmo_list_button">
                                <div class="mmo_list_button_1">+</div>
                                <div class="mmo_list_button_2">-</div>
                                指导师中心 @if($questions_to_answer_count)
                                    <div>{{$questions_to_answer_count}}</div>@endif
                            </li>
                            @if($data['tutor_price']!='')
                                <li data-value="0" class="mmo_list_li" style="display:none;"><a
                                            href="{{route('question.teacher', ['id'=>$data['id'], 'come_from'=>'tutor_center'])}}"><span><img
                                                    src="/images/public/select_right.jpg"
                                                    alt=""/></span>&nbsp;&nbsp;我的主页</a>
                                </li>
                                <li data-value="0" class="mmo_list_li" style="display:none;"><a
                                            href="{{route('tutor.answers')}}"><span><img
                                                    src="/images/public/select_right.jpg" alt=""/></span>&nbsp;&nbsp;我的回答 @if($questions_to_answer_count)<div>{{$questions_to_answer_count}}</div>@endif</a>
                                </li>
                            @endif
                            <li data-value="0" class="mmo_list_li" style="display:none;"><a
                                        href="{{route('tutor.complete')}}"><span><img
                                                src="/images/public/select_right.jpg"
                                                alt=""/></span>&nbsp;&nbsp;完善资料</a>
                            </li>
                        @elseif($data['role'] == 3)
                            <li data-value="0" data-state="false" class="mmo_list_button">
                                <div class="mmo_list_button_1">+</div>
                                <div class="mmo_list_button_2">-</div>
                                合伙人中心 @if($partnerNewOrderCount)<div>{{$partnerNewOrderCount}}</div>@endif
                            </li>
                            @if($data['partner_city']!='')
                                <li data-value="0" class="mmo_list_li" style="display:none;"><a href="{{route('partner.operate')}}"><span><img
                                                    src="images/public/select_right.jpg" alt=""/></span>&nbsp;&nbsp;运营数据</a></li>
                                <li data-value="0" class="mmo_list_li" style="display:none;"><a href="{{route('partner.orders')}}"><span><img
                                                    src="images/public/select_right.jpg" alt=""/></span>&nbsp;&nbsp;订单管理 @if($partnerNewOrderCount)<div>{{$partnerNewOrderCount}}</div>@endif</a></li>
                            @endif
                            <li data-value="0" class="mmo_list_li" style="display:none;"><a
                                        href="{{route('partner.complete')}}"><span><img
                                                src="images/public/select_right.jpg" alt=""/></span>&nbsp;&nbsp;完善资料</a></li>
                        @endif
                        
                    </ul>
                </div>
            </div>
            @include('element.nav', ['selected_item' => 'nav5'])
        </div>
    </div>
@endsection

@section('script')
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">
	$(document).ready(function(){
		wx.config(<?php echo $wx_js->config(array("onMenuShareAppMessage", "onMenuShareTimeline"), false) ?>);
	    wx.ready(function () {
	        wx.onMenuShareAppMessage({
	            title: '365天，和全国精英家长一起，成为更懂教育的父母', // 分享标题
	            desc: '我们穷尽一生的时间爱孩子，却很少关注自身的提升', // 分享描述
	            link: '{{route('user')}}?from=singlemessage', // 分享链接
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
	            link: '{{route('user')}}?from=singlemessage', // 分享链接
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

    <script type="text/javascript">
        $(document).ready(function () {
            $(".mmo_list_button").click(function () {//伸缩菜单
                if ($(this).attr("data-state") == "false") {
                    $(this).attr("data-state", "true");
                    $(".mmo_list_li[data-value=" + $(this).attr("data-value") + "]").show();
                } else {
                    $(this).attr("data-state", "false");
                    $(".mmo_list_li[data-value=" + $(this).attr("data-value") + "]").hide();
                }
            });
        });
    </script>
@endsection