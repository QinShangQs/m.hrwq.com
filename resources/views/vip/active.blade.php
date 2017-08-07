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
			$(document).ready(function () {
				wx.config(<?php echo $wx_js->config(array("onMenuShareAppMessage", "onMenuShareTimeline"), false) ?>);
					wx.ready(function () {
				            wx.onMenuShareAppMessage({
				                title: '365天，和全国精英家长一起，成为更懂教育的父母', // 分享标题
				                desc: '我们穷尽一生的时间爱孩子，却很少关注自身的提升', // 分享描述
				                //link: '{{route('course')}}?from=singlemessage', // 分享链接
				                link: $.UrlUpdateParams(window.location.href, "from", 'singlemessage'), // 分享链接
				                imgUrl: '{{url('/images/my/dis_in_love.jpg')}}', // 分享图标
				                type: '', // 分享类型,music、video或link，不填默认为link
				                dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
				                success: function () {
				                    // 用户确认分享后执行的回调函数
				                },
				                cancel: function () {
				                    // 用户取消分享后执行的回调函数
				                }
				            });
				            wx.onMenuShareTimeline({
				                title: '365天，和全国精英家长一起，成为更懂教育的父母', // 分享标题
				                link: $.UrlUpdateParams(window.location.href, "from", 'singlemessage'), // 分享链接
				                imgUrl: '{{url('/images/my/dis_in_love.jpg')}}', // 分享图标
				                success: function () {
				                    // 用户确认分享后执行的回调函数
				                },
				                cancel: function () {
				                    // 用户取消分享后执行的回调函数
				                }
				            });
				        });
				    });
				    $.ajaxSetup({
				        headers: {
				            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				        }
				    });
</script>
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
                        var _popHtml = '恭喜！您的和会员身份已激活成功，赶快完成注册吧！';
                        if(res.mobile){
                        	_popHtml = "恭喜！您的和会员身份已激活成功！尊享视频免费、旁听免费特权，快去体验吧！";
                        }
                        
                        Popup.init({
                            popHtml: _popHtml,
                            popFlash:{
                                flashSwitch:false
                            },popOkButton:{
                                buttonDisplay:true,
                                buttonName:"好",
                                buttonfunction:  function(){
                                    if(res.mobile){
                                    	location.href = '{{route('user')}}';
                                    }else{
                                    	location.href = '{{route('user.login')}}';
                                    }
                                    
                                }
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

