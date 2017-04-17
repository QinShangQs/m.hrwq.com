@extends('layout.default')
@section('content')
<div id="subject">
    <div id="main">
        <div class="coupon_receive">
            <input type='hidden' name='invite_user' value="{{$invite_user}}">
            <div class="cr_banner"><img src="/images/other/cr_banner.jpg" alt=""/></div>
            <div class="cr_p">
                <p>送您<span>100元</span>家庭教育爱心基金</p>
                <p>让教育孩子变得简单</p>
            </div>
            <div class="cr_button">
                <a href="/" class="cr_button_no">残忍拒绝</a>
                <a href="#" class="cr_button_yes">立即领取</a>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script type="text/javascript">
    $(document).ready(function(){
        $("body").css("background","#e73d26");

        var locks = false;
        $(".cr_button_yes").click(function(e){//点击立即领取
            e.preventDefault();
            /*-----ajax开始-----*/
             if(locks) {return false;}
             locks = true;
            //给glr_prize重新赋值
            $.ajax({
                type: 'post',
                url: '{{route('my.get_coupon')}}',
                data: {user_id: '{{$invite_user}}'},
                success: function (res) {
                    if (res.code == 0) {
                        locks = false;
                        Popup.init({
                            popTitle:'领取成功',//此处标题随情况改变，需php调用
                            //popHtml:'<p>领取成功！注册后可在我的钱包中查看。</p>',//此处信息会涉及到变动，需php调用
                            popHtml:'<p>领取成功！快去使用。</p>',
                            popOkButton:{
                                buttonDisplay:true,
                                buttonName:"确认",
                                buttonfunction:function(){
                                    location.href='{{route("course")}}?invite_user={{$invite_user}}';
                                    //location.href='{{route("user.login")}}?invite_user={{$invite_user}}';
                                }
                            },
                            popFlash:{
                                flashSwitch:false
                            }
                        });
                    }else {
                        Popup.init({
                            popTitle:'失败',
                            popHtml:'<p>'+res.message+'</p>',
                            popFlash:{
                                flashSwitch:true,
                                flashTime:3000,
                            }
                        });
                        locks = false;
                    }
                }
            });
            //返回事件结束
            /*-----ajax结束-----*/
        });
    });
</script>
@endsection