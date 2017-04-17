@extends('layout.default')
@section('content')
    <div id="subject">
        <div id="main">
            <div class="my">
                <div class="my_data_edit">
                    <div class="mde_title" style="text-align: center">激活会员卡</div>
                    <form class="mde_form" id="profile-form">
                        <ul class="mde_list">
                            <li>
                                <div class="mde_list_title">你的卡号</div>
                                <div class="mde_list_input"><input type="text" placeholder="" name="card_no"  id="card_no"></div>
                            </li>
                        </ul>
                        <div class="mde_button profile-button active_btn" ><input type="button" class="mde_button"  value="提交">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
    $(document).ready(function(){
        $('.active_btn').click(function(){
            var code = $.trim($('#card_no').val());
            if(code=='') {
                Popup.init({
                    popHtml:'卡号不能为空',
                    popFlash:{
                        flashSwitch:true,
                        flashTime:2000
                    }
                });
                return;
            }

            $.ajax({
                type:'post',
                url :'{{route('vip.active_store')}}',
                data:{code:code},
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
                            popHtml:"恭喜！您的和会员身份已激活成功！尊享视频免费、旁听免费特权，快去体验吧！",
                            popFlash:{
                                flashSwitch:false
                            },popOkButton:{
                                buttonDisplay:true,
                                buttonName:"好",
                                buttonfunction:  function(){location.href = '{{route('user')}}';}
                            }
                        });
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

