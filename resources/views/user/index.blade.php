@extends('layout.default')
@section('content')
    <div id="subject">
        <div id="main">
            <div class="my">
                <div class="my_member_ordinary">
                    <div class="mmo_top">
                        <div class="mmo_portrait"><a href="{{route('user.profile')}}"><img
                                        src="{{url($data['profileIcon'])}}" alt=""/></a></div>
                        <div class="mmo_title">{{$data['realname'] or $data['nickname']}}</div>
                        <div class="mmo_identity">
                            <a href="{{route('article',['id'=>4])}}"><div class="mmo_identity_1">成长值:{{$data['grow']}}</div></a><div class="mmo_identity_2">@if($data['role']=='1'&&$data['vip_flg']=='2')和会员@else{{config('constants.user_role')[$data['role']]}}@endif</div>
                        </div>
                    </div>
                    <div class="mmo_top2"><span><img src="/images/my/mmo_top2_1.png" alt=""/> 已提问 <a
                                    href="{{route('user.question')}}">{{$ask_question_num}}</a></span><span><img
                                    src="/images/my/mmo_top2_2.png" alt=""/> 和贝 <a
                                    href="{{route('user.score')}}">{{$data['score']}}</a></span></div>
                    <ul class="mmo_list">
                        <li><a href="{{route('user.wallet')}}"><span><img src="/images/public/select_right.jpg" alt=""/></span>我的钱包@if($myWalletCount)
                                    <div>{{$myWalletCount}}</div>@endif</a>
                        </li>
                        <li><a href="{{route('my.orders')}}"><span><img src="/images/public/select_right.jpg"
                                                                        alt=""/></span>我的订单 @if($order_read_num>0)
                                    <div>{{$order_read_num}}</div>@endif</a></li>
                        <li><a href="{{route('my.courses')}}"><span><img src="/images/public/select_right.jpg" alt=""/></span>我的课程</a>
                        </li>
                        <li><a href="{{route('my.notes')}}"><span><img src="/images/public/select_right.jpg"
                                                                       alt=""/></span>作业&笔记</a></li>
                        <li><a href="{{route('user.question')}}"><span><img src="/images/public/select_right.jpg"
                                                                            alt=""/></span>我的好问 @if($new_answer_questions_count>0)
                                    <div>{{$new_answer_questions_count}}</div>@endif</a></li>
                        <li><a href="{{route('user.talk')}}"><span><img src="/images/public/select_right.jpg"
                                                                        alt=""/></span>我的帖子 @if($unreadTalkCommentCount)
                                    <div>{{$unreadTalkCommentCount}}</div>@endif</a></li>
                        @if($data['role'] != 2 )
                        <li><a href="{{route('my.invite_user')}}"><span><img src="/images/public/select_right.jpg" alt=""/></span>推荐有奖</a></li>
                         @endif
                    </ul>
                    <ul class="mmo_list">
                        @if($data['vip_flg'] == 1 )
                            <li><a href="{{route('vip')}}"><span>与最优秀的父母一起学习 <img
                                                src="/images/public/select_right.jpg" alt=""/></span>成为和会员</a></li>
                        @endif
                        @if($data['role'] == 1 )
                            <li id="tutor_apply"><a href="#"><span>加入智慧榜，帮助更多家庭 <img
                                                src="/images/public/select_right.jpg" alt=""/></span>成为指导师</a></li>
                            <li id="partner_apply"><a href="{{route('partner.apply')}}"><span>加盟合作，普及现代家庭教育 <img
                                                src="/images/public/select_right.jpg" alt=""/></span>成为合伙人</a></li>
                        @elseif($data['role'] == 2)
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
                        <li><a href="{{route('article',['id'=>2])}}"><span><img src="/images/public/select_right.jpg"
                                                                                alt=""/></span>关于我们</a></li>
                        <li><a href="{{route('article.helpcenter',['type'=>7])}}"><span><img
                                            src="/images/public/select_right.jpg" alt=""/></span>帮助中心</a></li>
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
            $("#tutor_apply").click(function () {
                @if($tutorCourse != null)
                    Popup.init({
                    popTitle: '指导师申请',//此处标题随情况改变，需php调用
                    popHtml: '<p>是否立即前往参加指导师培训课程？</p>',//此处信息会涉及到变动，需php调用
                    popOkButton: {
                        buttonDisplay: true,
                        buttonName: "是",
                        buttonfunction: function () {
                            window.location.href = '{{route('course.detail', ['id'=>$tutorCourse->id])}}';
                        }
                    },
                    popCancelButton: {
                        buttonDisplay: true,
                        buttonName: "否",
                        buttonfunction: function () {
                        }
                    },
                    popFlash: {
                        flashSwitch: false
                    }
                });
                @else
                    Popup.init({
                    popHtml: '指导师培训课程暂未开设！',
                    popFlash: {
                        flashSwitch: true,
                        flashTime: 2000
                    }
                });
                @endif
            });

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