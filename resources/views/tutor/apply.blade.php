@extends('layout.default')
@section('content')
    <div id="subject">
        <div id="main">
            <div class="my">
                <div class="my_be_teachers">
                    <div class="mbt_title">成为指导师</div>
                    <dl class="mbt_list">
                        <dt>成为指导师资格要求</dt>
                        <dd>
                            <p>需要身份认证,学校认证、机构认证、实名制认证、银行认证等多重认证。</p>
                        </dd>
                    </dl>
                    <dl class="mbt_list">
                        <dt>申请流程</dt>
                        <dd>
                            <p>流程1：流程就是这样这样和那样那样</p>
                            <p>流程2：流程就是这样这样和那样那样</p>
                        </dd>
                    </dl>
                    @if($tutorCourse!=null) <a class="mbt_button" href="{{route('course.detail', ['id'=>$tutorCourse->id])}}">参加课程培训</a>@endif
                </div>
            </div>
        </div>
    </div>
@endsection