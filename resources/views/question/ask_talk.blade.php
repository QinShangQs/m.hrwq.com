@extends('layout.default')

@section('style')
    <style>
        .gaq_input input { width:100%; height:45px; line-height:45px; border:none; padding:2px 20px;margin-top:-6px; font-size:14px; color:#999;}

        .comment h3{height:28px; line-height:28px}
        .com_form p{height:28px; line-height:28px;}
        /*以上css代码根据实际情况修改，以下css代码必须保留*/
        span.emotion{width:60px; height:20px; overflow:hidden; background:url(../../images/face/icon.gif) no-repeat 2px 2px; padding-left:20px;margin-left: 20px; cursor:pointer;}
        span.emotion:hover{background-position:2px -28px;/*注意hover此属性在ie 6浏览器下是无效的*/}
        .qqFace{margin-top:4px;background:#fff;padding:2px;border:1px #dfe6f6 solid;}
        .qqFace table td{padding:0px;}
        .qqFace table td img{cursor:pointer;border:1px #fff solid;}
        .qqFace table td img:hover{border:1px #0066cc solid;}

        #subject{overflow: visible}
    </style>
@endsection

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
                    <div class="gaq_input">
                        <input id="gaq_title" placeholder="标题，4-25个字符" value="">
                    </div>
                    <div class="gaq_textarea">
                        <textarea id="gaq_textarea" placeholder="内容，20个字符以上" value=""></textarea>
                        <span class="emotion"></span>
                    </div>
                    <div class="gaq_button"><input class="gaq_form_button"  value="提交" readonly></div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript" src="/js/jquery.qqFace.js"></script>
    <script type="text/javascript">
        $(function(){
            $('.emotion').qqFace({
                id : 'facebox',
                assign:'gaq_textarea'
            });
        });
    </script>

    <script>
        $(document).ready(function(){
            var disable = 0;
            $(".gaq_list li").click(function(){//点击选择
                if(disable == 1){
                    return ;
                }
                disable = 1;
                if(!$(this).hasClass('select')){
                    if($(".gaq_list .select").length >= 3){
                        Popup.init({
                            popHtml:'最多只能选择3个标签',
                            popFlash:{
                                flashSwitch:true,
                                flashTime:2000
                            }
                        });
                        disable = 0;
                        return;
                    }
                }
                 $(this).toggleClass("select");
            });

            $(".gaq_form_button").click(function(){//提交表单
                var question_content = $('#gaq_textarea').val();
                var gaq_title = $('#gaq_title').val();

                if(gaq_title.length < 4){
                    Popup.init({
                        popHtml:'标题不能少于4个字符',
                        popFlash:{
                            flashSwitch:true,
                            flashTime:2000
                        }
                    });
                    disable = 0;
                    return false;
                }else if(gaq_title.length > 25){
                    Popup.init({
                        popHtml:'标题不能大于25个字符',
                        popFlash:{
                            flashSwitch:true,
                            flashTime:2000
                        }
                    });
                    disable = 0;
                    return false;
                }

                if(question_content.length<20){
                    Popup.init({
                        popHtml:'你提出的问题不能少于20个字符',
                        popFlash:{
                            flashSwitch:true,
                            flashTime:2000
                        }
                    });
                    disable = 0;
                    return false;
                }/* else if(question_content.length > 100){
                    Popup.init({
                        popHtml:'你提出的问题不能大于150个字符',
                        popFlash:{
                            flashSwitch:true,
                            flashTime:2000
                        }
                    });
                    return false;
                } */

                var tag_ids = '';

                //获取选中的标签id
                $('.gaq_list .select').each(function(){
                    if(tag_ids==''){
                        tag_ids += $(this).attr('data-value');
                    }else{
                        tag_ids += ','+ $(this).attr('data-value');
                    }
                });

                //ajax提交内容
                $.ajax({
                    type: 'post',
                    url: '{{route('question.ask_talk_store')}}',
                    data: {content:question_content,tag_ids:tag_ids,title:gaq_title},
                    dataType: 'json',
                    success: function (res) {
                        Popup.init({
                            popHtml:res.message,
                            popFlash:{
                                flashSwitch:true,
                                flashTime:2000
                            }
                        });

                        if(res.code == 0){
                            location.href = '{{route('question')}}?selected_tab=3'
                        }
                        disable = 0;
                    },
                    error: function (res) {
                        disable = 0;
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
