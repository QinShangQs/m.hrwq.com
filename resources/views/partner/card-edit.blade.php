@extends('layout.default')
@section('content')
<link rel="stylesheet" href="/css/partner-card.css"/>

<div class="card-body">
     <form enctype="multipart/form-data" id="banner-from">
        <div class="banner">
            <img id="banner-img" src="{{$card_info->cover_url or "/images/partner/banner.png"}}" />
            <div class="change" onclick="$('#banner-file').click()"></div>
            <input type="file" name="file" id="banner-file" style="display: none" onchange="uploadBanner()" >
        </div>
    </form>

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
                    <div class='pics'>
                        @if ($card_info->images)
                            @foreach($card_info->images as $image)
                            <div class='lmg' onclick='removePhoto({{$image->id}}, this)'>
                                <img src='{{$image->url}}' />
                                <img class='remove' src='/images/partner/remove.png'/>
                            </div>
                            @endforeach
                        @endif
                    </div>
                </div>
                <div class='tcont' id='photo-div'>
                    <img src='/images/partner/pic.png' onclick="$('#photo-file').click()" />
                    <div class='desc'>
                        点击上传图片
                    </div>
                    <input type="file" name="file" id="photo-file" style="display: none" onchange="uploadPhoto()" >
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

<form enctype="multipart/form-data" id="photo-from" style="display:none"></form>

<div class='card-loading' style="display:none">
    <div class='body'>上传中...</div>
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

<script>
    function hidePhotoDiv(){
        if($('.pics .lmg').length >= 6){
            $('#photo-div').hide();
        }else{
            $('#photo-div').show();
        }
    }
    
    function showLoading(){
        $('.card-loading').show();
    }
    
    function hideLoading(){
        $('.card-loading').hide();
    }
    
    (function(){
        hidePhotoDiv();
    })();
    
    var bannerLock = false;
    
    function uploadImg(upload_url, formData, callback){
        if (bannerLock)
            return;
        bannerLock = true;
        showLoading();
 
        $.ajax({
            url: upload_url,
            type: 'POST',
            data: formData,
            //这两个设置项必填
            contentType: false,
            processData: false,
            success : function(data) {
                    console.log(data);
                    callback(data);
                    bannerLock = false;
                    hideLoading();
            },error: function (res) {
                    Popup.init({
                        popHtml: '<p>编辑失败！</p>',
                        popFlash: {
                            flashSwitch: true,
                            flashTime: 2000,
                        }
                    });
                    bannerLock = false;
                    hideLoading();
            }
        });
    }
    
    function uploadBanner(){
        var formData = new FormData($('#banner-from')[0]);
        uploadImg('{{route('partner.card.change_banner')}}',formData,function(data){
            if (data.code == 0) {
                $('#banner-img').attr('src', data.url);
            } else {
                Popup.init({
                    popHtml: '<p>' + data.message + '</p>',
                    popFlash: {
                        flashSwitch: true,
                        flashTime: 2000,
                    }
                });
            }
        })
    }
    
    function uploadPhoto(){
        var formData = new FormData($('#photo-from')[0]);
        formData.append('file',$('#photo-file')[0].files[0]);
        uploadImg('{{route('partner.card.create_img')}}',formData,function(data){
            if (data.code == 0) {
                var div = "<div class='lmg' onclick='removePhoto("+data.id+", this)'>"
                          +"<img src='"+data.url+"' />"
                          +"<img class='remove' src='/images/partner/remove.png'/>"
                          +"</div>";
                $('.pics').append($(div));
                
                hidePhotoDiv();
            } else {
                Popup.init({
                    popHtml: '<p>' + data.message + '</p>',
                    popFlash: {
                        flashSwitch: true,
                        flashTime: 2000,
                    }
                });
            }
        })
    }
    
    function removePhoto(id, img){
         Popup.init({
            popHtml: '确定要删除该图片吗？',
            popOkButton: {
                buttonDisplay: true,
                buttonName: "确认",
                buttonfunction: function () {
                    $.post('{{route('partner.card.remove_img')}}',{id:id}, function(data){
                        $(img).remove();
                        hidePhotoDiv();
                    },'json');
                }
            },
            popCancelButton:{
                buttonDisplay:true,
                buttonName:"取消",
                buttonfunction:function(){}
            }
        });
    }
</script>
@endsection