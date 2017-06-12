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
                        		<!-- @if($myWalletCount)<span>{{$myWalletCount}}</span>@endif -->
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
                        		@if($data['role']=='1'&&$data['vip_flg']=='2')
                        			<img src="/images/my/vip-2.png" alt=""/>
                        		@else
                        			<img src="/images/my/vip-1.png" alt=""/>
                        		@endif
                        	</div>                        	
                        	<div class="mmo_right_menu">
                        		<a href="{{route('my.courses')}}">收藏</a> 
                        		<!--  @if($order_read_num>0)<span>{{$order_read_num}}</span>@endif</a>-->
                        		| 
                        		<a href="{{route('my.orders')}}">订单</a></div>
                        </div>
                        
                        @if($data['role'] != 2 )
                        <div class="mmo_love">
							<a href="{{route('share.angle')}}">爱心大使&nbsp;></a>                      	
                        </div>
                        @endif
                        
                        <div class="mmo_identity">
                            <a href="{{route('vip')}}">
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
                    <ul class="mmo_list">
                        <li><a href="{{route('my.notes')}}"><span><img src="/images/public/select_right.jpg"
                                                                       alt=""/></span>作业&笔记</a></li>
                        <li><a href="{{route('user.question')}}"><span><img src="/images/public/select_right.jpg"
                                                                            alt=""/></span>我的问答 @if($new_answer_questions_count>0)
                                    <div>{{$new_answer_questions_count}}</div>@endif</a></li>
                        <li><a href="{{route('user.question')}}"><span><img src="/images/public/select_right.jpg"
                                                                        alt=""/></span>家长圈子 @if($unreadTalkCommentCount)
                                    <div>{{$unreadTalkCommentCount}}</div>@endif</a></li>
  
                    </ul>
                    <ul class="mmo_list">
                        @if($data['vip_flg'] == 1 )
                            <li><a href="{{route('vip')}}"><span> <img
                                                src="/images/public/select_right.jpg" alt=""/></span>会员状态</a></li>
                        @endif
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