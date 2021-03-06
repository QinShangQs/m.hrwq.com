@extends('layout.default')

@section('content')
    <div id="subject">
        <div id="main">
            <div class="good_asking">
                <form class="ga_quiz">
                    <div class="clearboth"></div>
                    <p>输入评论</p>
                    <div class="gaq_textarea">
                        <textarea id="gaq_textarea" placeholder="不少于20字符。" value=""></textarea>
                    </div>
                    <div class="gaq_button"><input class="gaq_form_button"  value="提交"></div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function(){
            $(".gaq_form_button").click(function(){//提交表单
		var $this = $(this);
                if($("#gaq_textarea").val().length<20){
                    Popup.init({
                        popHtml:'你提出的问题不能少于20个字符',
                        popFlash:{
                            flashSwitch:true,
                            flashTime:2000
                        }
                    });
                    return false;
                } /* else if($("#gaq_textarea").val().length > 100){
                    Popup.init({
                        popHtml:'你提出的问题不能大于100个字符',
                        popFlash:{
                            flashSwitch:true,
                            flashTime:2000
                        }
                    });
                    return false;
                } */

                var question_content = $('#gaq_textarea').val();

		if ($this.hasClass('disabled')) return false;
		$this.addClass('disabled');

                //ajax提交内容
                $.ajax({
                    type: 'post',
                    url: '{{route('question.talk_comment_store')}}',
                    data: {talk_id:'{{$id}}',content:question_content},
                    dataType: 'json',
                    success: function (res) {
			$this.removeClass('disabled');
                        Popup.init({
                            popHtml:res.message,
                            popFlash:{
                                flashSwitch:true,
                                flashTime:2000
                            }
                        });
                        if(res.code == 0){
                            location.href = '{{route('question.talk')}}/'+'{{$id}}'
                        }
                    },
                    error: function (res) {
			$this.removeClass('disabled');
                        var errors = res.responseJSON;
                        for (var o in errors) {
                            Popup.init({
                                popHtml:errors[o][0],
                                popFlash:{
                                    flashSwitch:true,
                                    flashTime:2000
                                }
                            });
                            break;
                        }
                    }
                });
            });
        });
    </script>
@endsection
