@extends('layout.default')
@section('content')
	<form action="" method="" class="leave-word-form">
		<div class="leave-word">		
			<div class="title">建议留言</div>
			<div class="content">
				<textarea name="content" id="content" placeholder="您对和润好父母学院有什么建议或意见，欢迎留言!"></textarea>
			</div>		
			
		</div>
		<div class="leave-word-footer">
				<button id="submit-btn" type="button">提交</button>
		</div>
	</form>
@endsection
@section('script')
<script type="text/javascript">
    $(document).ready(function(){
        $('#submit-btn').click(function(){
            var content = $.trim($('#content').val());
            if(content=='') {
                Popup.init({
                    popHtml:'请填写留言内容！',
                    popFlash:{
                        flashSwitch:true,
                        flashTime:2000
                    }
                });
                return;
            }

			if(content.length > 200){
				Popup.init({
                    popHtml:'字符在200以内！',
                    popFlash:{
                        flashSwitch:true,
                        flashTime:2000
                    }
                });
                return;
			}
            
            $.ajax({
                type:'post',
                url :'{{route('leaveword.create')}}',
                data:{content:content},
                dataType:'json',
                success:function(res){
                    Popup.init({
                        popHtml:res.message,
                        popFlash:{
                            flashSwitch:true,
                            flashTime:2000
                        }
                    });
                    if(res.code == 0){
                        Popup.init({
                            popTitle:"建议留言已成功提交",
                            popHtml:"感谢您对中国家庭教育事业的关注！",
                            popFlash:{
                                flashSwitch:false,
                                flashTime:2000
                            },popOkButton:{
                                buttonDisplay:true,
                                buttonName:"确定",
                                buttonfunction:  function(){location.href = '{{route('user')}}';}
                            }
                        });
						setTimeout(function(){location.href = '{{route('user')}}';},2000);
                        
                    }
                },
                error:function(res){
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
            })
        })
    })
</script>
@endsection