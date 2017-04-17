@extends('layout.default')
@section('content')
<div id="subject">
    <div id="main">
        <div class="look_charge_details_comment">
            <form id="post_form" action="{{route('course.do_add_address')}}" method="post">
                {!! csrf_field() !!}
                <input type="hidden" name="course_id" id="course_id" value="{{$course->id}}">
                收货人：<input name="linkman" ><br>
                电话：<input name="telphone" ><br>
                选择地区：<input name="city" ><br>
                详细地址：<input name="address" ><br>
                <button  class="lcdc_form_button" type="submit">保存</button>
            </form>
        </div>
    </div>
</div>
@endsection