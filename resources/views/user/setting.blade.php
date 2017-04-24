@extends('layout.default')
@section('content')
    <div id="subject">
        <div class="my">
            <div class="my_member_ordinary">
                <ul class="mmo_list">
                	@if($data['role'] == 1 )
                    	<li id="tutor_apply"><a href="#"><span>加入智慧榜，帮助更多家庭&nbsp;<img src="/images/public/select_right.jpg" alt=""/></span>成为指导师</a></li>
                    	<li id="partner_apply"><a href="{{route('partner.apply')}}"><span>加盟合作，普及现代家庭教育&nbsp;<img src="/images/public/select_right.jpg" alt=""/></span>成为合伙人</a></li>
                    @endif
                    <li><a href="{{route('question')}}"><span><img src="/images/public/select_right.jpg" alt=""/></span>建议留言</a></li>
                    <li><a href="{{route('article',['id'=>2])}}"><span><img src="/images/public/select_right.jpg" alt=""/></span>关于我们</a></li>
                    <li><a href="{{route('article.helpcenter',['type'=>7])}}"><span><img src="/images/public/select_right.jpg" alt=""/></span>帮助中心</a></li>

                </ul>
            </div>
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
        });
    </script>
@endsection