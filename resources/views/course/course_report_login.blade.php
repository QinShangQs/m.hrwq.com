@extends('layout.default')
@section('content')
<div id="subject">
    <div id="main">
        <div class="my">
            <div class="my_member_activation">
                <div class="mma_top">激活课程报到权限</div>
                <form class="mma_form">
                    {!! csrf_field() !!}
                    <input type="hidden" name="order_id" id="order_id" value="{{$order->id}}">
                    <div class="mma_input_div"><span>课程名</span><input type="text" value="{{$order->order_name}}" class="mma_card" name="order_name" readonly="true"></div>
                    <div class="mma_input_div"><span>验证密码</span><input type="text" value="" class="mma_card" name="verify_password"></div>
                    <div class="mma_button_div"><input type="submit" value="提交" class="mma_button"></div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script type="text/javascript">
$(document).ready(function(){
    var lock = false;
    $(".mma_button").click(function(e){
        e.preventDefault();
        if (lock) {return;}
        var form_data = $('form').serialize();
        lock = true;
        /*-----ajax事件开始-----*/
        $.post("{{route('course.course_report_login')}}", form_data,function(data){
            if(data.status){
               location.href = "{{route('course.course_report',['id'=>$order->id])}}";
            }else{
               Popup.init({
                    popHtml:'<p>'+data.msg+'</p>',
                    popFlash:{
                        flashSwitch:true,
                        flashTime:2000,
                    }
                });
                lock = false;
            }
        },'json')
        /*-----ajax事件结束-----*/
        return false;
    });
});
</script>
@endsection
