@extends('layout.default')

@section('content')
    <div id="subject">
        <div id="main">
            <div class="good_asking">
                <form class="ga_quiz">
                    <ul class="gaq_list">
                        @foreach($data as $item)
                            <li data-value="{{$item->id}}">#{{$item->title}}#</li>
                        @endforeach
                    </ul>
                    <div class="clearboth"></div>
                    <p>描述你要提出的问题</p>
                    <div class="gaq_textarea" style="padding-right: 20px;">
                        <textarea id="gaq_textarea" placeholder="请在100个字内描述你的家庭教育问题，等Ta语音回答，超过48小时未回答，将全额退款。答案每被旁听1次，你将获得¥0.5家庭教育爱心奖励" value=""></textarea>
                        <input type="hidden" value="{{$uid}}" name="uid" id="uid">
                    </div>
                    <div class="gaq_button"><input type="submit" class="gaq_form_button"  value="提交"></div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function(){
            $(".gaq_list li").click(function(){//点击选择
                if(!$(this).hasClass('select')){
                    if($(".gaq_list .select").length >= 3){
                        Popup.init({
                            popHtml:'最多只能选择3个标签',
                            popFlash:{
                                flashSwitch:true,
                                flashTime:2000
                            }
                        });
                        return;
                    }
                }
                 $(this).toggleClass("select");
            });

            $(".gaq_form_button").click(function(e){//提交表单
                e.preventDefault();
		var $this = $(this);
                if($("#gaq_textarea").val().length<1){
                    Popup.init({
                        popHtml:'请先填写问题',
                        popFlash:{
                            flashSwitch:true,
                            flashTime:2000
                        }
                    });
                    return false;
                }else if($("#gaq_textarea").val().length > 100){
                    Popup.init({
                        popHtml:'你提出的问题不能大于100个字符',
                        popFlash:{
                            flashSwitch:true,
                            flashTime:2000
                        }
                    });
                    return false;
                }

                var question_content = $('#gaq_textarea').val();
                var tutor_id = $('#uid').val();
                var tag_ids = '';

                //获取选中的标签id
                $('.gaq_list .select').each(function(){
                    if(tag_ids==''){
                        tag_ids += $(this).attr('data-value');
                    }else{
                        tag_ids += ','+ $(this).attr('data-value');
                    }
                });

		if ($this.hasClass('disabled')) return false;
		$this.addClass('disabled');

                //ajax提交内容
                $.ajax({
                    type: 'post',
                    url: '{{route('question.ask_question_store')}}',
                    data: {tutor_id:tutor_id,content:question_content,tag_ids:tag_ids},
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
                            location.href = '{{route('wechat.question_ask_confirm')}}?id='+res.qid
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
