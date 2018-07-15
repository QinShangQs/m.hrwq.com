@extends('layout.default')
@section('content')
<link rel="stylesheet" href="/css/partner-card.css"/>

<div class="card-body">
    <div class="banner">
        <img src="{{$card_info->cover_url or "/images/partner/banner.png"}}" />
        <div class="change"></div>
    </div>
    <form id="profile-form">
        <div class="info-content">
            <div class="name-info">
                <div class="author">
                    <img src="{{$user_info['profileIcon']}}"/>
                    <div class="desc">
                        <div class="name">{{$user_info['realname']}}</div>
                        <div class="cityname">{{$user_info['city']['area_name']}}合伙人</div>
                    </div>
                </div>

                <div class="qrcode" style="display:none">
                    <img src="/images/partner/qrcode-logo.png"/>
                    <div class="desc">二维码</div>
                </div> 
            </div>

            <div class='items'>
                <div class='item'>
                    <img src='/images/partner/phone.png' />
                    <div class='remark' >
                        <div class='title'>电话</div>
                        <div class='name'>
                            <input name='tel' type='text' value='{{$card_info->tel}}'/>
                        </div>
                    </div>
                </div>
                <div class='item'>
                    <img src='/images/partner/wechat.png' />
                    <div class='remark' >
                        <div class='title'>微信</div>
                        <div class='name'>
                            <input name='wechat' type='text' value='{{$card_info->wechat}}'/>
                        </div>
                    </div>
                </div>
                <div class='item'>
                    <img src='/images/partner/msg.png' />
                    <div class='remark' >
                        <div class='title'>邮箱</div>
                        <div class='name'>
                            <input name='email' type='email' value='{{$card_info->email}}'/>
                        </div>
                    </div>
                </div>
                <div class='item'>
                    <img src='/images/partner/pointer.png' />
                    <div class='remark' >
                        <div class='title'>地址</div>
                        <div class='name'>                       
                            <input name='address' type='text' value='{{$card_info->address}}'/>
                        </div>
                    </div>
                </div>
                <div class='item'>
                    <img src='/images/partner/ie.png' />
                    <div class='remark' >
                        <div class='title'>网址</div>
                        <div class='name'>
                            <input name='website' type='text' value='{{$card_info->website}}'/>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class='last-content'>
            <div class='item'>
                <div class='title'>
                    <img src='/images/partner/left-line.png'/>
                    <span>简介</span>
                </div>
                <div class='tcont'>
                    <textarea name='remark' rows='8'>{{$card_info->remark}}</textarea>
                </div>
            </div>
            <div class='item'>
                <div class='title'>
                    <img src='/images/partner/left-line.png'/>
                    <span>照片</span>
                </div>
                <div class='tcont'>
                    <img src='/images/partner/pic.png' />
                    <div class='desc'>
                        点击上传图片
                    </div>
                </div>
            </div>
            <div class='item'>
                <div class='title'>
                    <img src='/images/partner/left-line.png'/>
                    <span>视频</span>
                </div>
                <div class='tcont'>
                    <img src='/images/partner/video.png' />
                    <div class='desc'>
                        点击上传视频
                    </div>
                </div>
            </div>

            <img class='save' src='/images/partner/save.png'/>
        </div>
    </form>
</div>

@endsection
@section('script')
<!-- 提交修改 -->
<script>
    $(document).ready(function () {
        var lock = false;
        $('.save').click(function (e) {
            e.preventDefault();
            if (lock)
                return;
            lock = true;
            $.ajax({
                type: "post",
                url: "{{route('partner.card.update')}}",
                data: $('#profile-form').serialize(),
                dataType: "json",
                success: function (res) {
                    if (res.code == 0) {
                        Popup.init({
                            popHtml: '<p>' + res.message + '</p>',
                            popOkButton: {
                                buttonDisplay: true,
                                buttonName: "确认",
                                buttonfunction: function () {
                                    location.href = '{{route('partner.card')}}';
                                }
                            }
                        });

                    } else {
                        Popup.init({
                            popHtml: '<p>' + res.msg + '</p>',
                            popFlash: {
                                flashSwitch: true,
                                flashTime: 2000,
                            }
                        });
                    }
                    lock = false;
                },
                error: function (res) {
                    Popup.init({
                        popHtml: '<p>编辑失败！</p>',
                        popFlash: {
                            flashSwitch: true,
                            flashTime: 2000,
                        }
                    });
                    lock = false;
                }
            });
        });
    });
</script>
@endsection