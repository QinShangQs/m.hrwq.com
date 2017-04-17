@extends('layout.default')
@section('content')
    <div id="subject">
        <div id="main">
            <div class="look_charge_details_comment">
                <form class="lcdc_form">
                    <input type="hidden" name="id" id="id" value="{{$opo->id}}">
                    <div class="lcdc_title">输入评论</div>
                    <div class="lcdc_textarea"><textarea id="lcdc_textarea" name="content" placeholder="不少于20字符。"></textarea></div>
                    <div class="lcdc_button"><input class="lcdc_form_button" type="submit" value="提交"></div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        $(document).ready(function(){
            $(".lcdc_form_button").click(function(){//提交评论
                if($("#lcdc_textarea").val().length<20){
                    Popup.init({
                        popTitle:'提交失败',
                        popHtml:'<p>您输入的评论信息不能少于20个字符</p>',
                        popFlash:{
                            flashSwitch:true,
                            flashTime:1000
                        }
                    });
                    return false;
                }

                var id = $('#id').val();
                var content = $('#lcdc_textarea').val();
                $.ajax({
                    type: 'post',
                    url: '{{route('opo.comment.store', ['id'=>$opo->id])}}',
                    data: {id: id,content: content},
                    success: function (res) {
                        if (res.code == 0) {
                            //ajax成功返回事件开始
                            Popup.init({
                                popTitle:'评论成功',
                                popHtml:'<p>'+res.message+'</p>',
                                popFlash:{
                                    flashSwitch:true,
                                    flashTime:3000
                                }
                            });
                            window.location.href='{{route('opo')}}';
                            //ajax成功返回事件结束
                        } else {
                            Popup.init({
                                popTitle:'评论失败',
                                popHtml:'<p>'+res.message+'</p>',
                                popFlash:{
                                    flashSwitch:true,
                                    flashTime:3000
                                }
                            });
                        }
                    }
                });
                return false;
            });
        });
    </script>
@endsection