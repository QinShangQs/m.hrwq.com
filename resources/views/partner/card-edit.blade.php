@extends('layout.default')
@section('content')
<link rel="stylesheet" href="/css/partner-card.css"/>

<input type=hidden id="token_url" value="{{route('home.qav.token')}}"/>
<input type=hidden id="domain_url" value="{{$domain_url}}"/>
<input type=hidden id="uid" value="{{$user_info['id']}}"/>

<div class="card-body">
     <form enctype="multipart/form-data" id="banner-from">
        <div class="banner">
            <img id="banner-img" src="{{$card_info->cover_url or "/images/partner/banner.png"}}" />
            <div class="change" onclick="$('#banner-file').click()"></div>
            <input type="file" name="file" id="banner-file" style="display: none" onchange="uploadBanner()" 
                   accept="image/*"/>
        </div>
    </form>

    <form id="profile-form">
        <div class="info-content">
            <div class="name-info">
                <div class="author">
                    <img src="{{$user_info['profileIcon']}}"/>
                    <div class="desc">
                        <div class="name">{{$user_info['realname']}}</div>
                        <div class="cityname">
                            @if($user_info['role'] == 3)
                                {{$user_info['city']['area_name']}}合伙人
                            @else
                                家庭教育指导师
                            @endif
                        </div>
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
                            <input name='tel' type='text' value='{{$card_info->tel}}' required="" pattern="\d{11}"/>
                        </div>
                    </div>
                </div>
                <div class='item'>
                    <img src='/images/partner/wechat.png' />
                    <div class='remark' >
                        <div class='title'>微信</div>
                        <div class='name'>
                            <input name='wechat' type='text' value='{{$card_info->wechat}}' required=""/>
                        </div>
                    </div>
                </div>
                <div class='item'>
                    <img src='/images/partner/msg.png' />
                    <div class='remark' >
                        <div class='title'>邮箱</div>
                        <div class='name'>
                            <input name='email' type='email' value='{{$card_info->email}}' required=""/>
                        </div>
                    </div>
                </div>
                <div class='item'>
                    <img src='/images/partner/pointer.png' />
                    <div class='remark' >
                        <div class='title'>地址</div>
                        <div class='name'>                       
                            <input name='address' type='text' value='{{$card_info->address}}' required=""/>
                        </div>
                    </div>
                </div>
                <div class='item'>
                    <img src='/images/partner/ie.png' />
                    <div class='remark' >
                        <div class='title'>网址</div>
                        <div class='name'>
                            <input name='website' type='url' value='{{$card_info->website}}'/>
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
                                <img src='{{$image->url}}'/>
                                <img class='remove' src='/images/partner/remove.png'/>
                            </div>
                            @endforeach
                        @endif
                        
                        <div class='lmg' id='photo-div'>
                            <img src='/images/partner/pic.png' onclick="$('#photo-file').click()" />
                            <div class='desc'>
                                点击上传图片
                            </div>
                            <input type="file" name="file" id="photo-file" style="display: none" onchange="uploadPhoto()" 
                                   accept="image/*"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class='item'>
                <div class='title'>
                    <img src='/images/partner/left-line.png'/>
                    <span>视频</span>
                </div>
                <div class='tcont' id="container">
                    <img class='remove' src='/images/partner/remove.png'/>
                    <img src='/images/partner/video.png'  id="pickfiles"/>
                    <div class='desc' >
                        选择视频后立即上传
                    </div>
                    <!-- 上传按钮，由保存资料触发 -->
                    <!--<input type="button" style="display:none" id="avd-btn-upload" />-->
                </div>              
            </div>

            <img class='save' src='/images/partner/save.png'/>
        </div>
    </form>
</div>

<form enctype="multipart/form-data" id="photo-from" style="display:none"></form>

<div class='card-loading' style="display:none">
    <div class='body'>
        文件上传中，请耐心等待...
    </div>
</div>

<div class='avd-loading' style="display:none">
    <div class='body'>
        <table id="tav">
            <tbody id="fsUploadProgress">
                <tr style="opacity: 1;" class="progressContainer" >

                </tr>
            </tbody>
        </table>
    </div>
</div>

@endsection
@section('script')
<!-- 提交修改 -->
<script>
    $(document).ready(function () {
        
        function checkForm(){
            var form = document.forms['profile-form'];
            var message = '';
            if(form.tel.checkValidity() == false){
                message += "请正确填写手机号。<br/>";
            }
            if(form.wechat.checkValidity() == false){
                message += "请填写微信。<br/>";
            }
            if(form.email.checkValidity() == false){
                message += "请正确填写邮箱。<br/>";
            }
            if(form.address.checkValidity() == false){
                message += "请填写地址。<br/>";
            }
            if(form.website.checkValidity() == false){
                message += "请正确填写网址。<br/>";
            }
            
            if(message.length > 0){
                Popup.init({
                    popHtml: '<p style="text-align:left">' + message + '</p>',
                    popOkButton: {
                        buttonDisplay: true,
                        buttonName: "确认",
                    }
                });
                return false;
            }
            
            return true;
        }
        
        var lock = false;
        $('.save').click(function (e) {
            if(!checkForm()){
                return;
            }
        
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
        if($('.pics .lmg').length >= 7){
            $('#photo-div').hide();
        }else{
            $('#photo-div').show();
        }
    }
    
    function showLoading(txt){
        $('.card-loading').show();
        $('.card-loading .body').text(txt ? txt : '文件上传中，请耐心等待...');
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
 
        var xhrOnProgress=function(fun) {
        xhrOnProgress.onprogress = fun; //绑定监听
        //使用闭包实现监听绑
        return function() {
            //通过$.ajaxSettings.xhr();获得XMLHttpRequest对象
            var xhr = $.ajaxSettings.xhr();
            //判断监听函数是否为函数
            if (typeof xhrOnProgress.onprogress !== 'function')
                return xhr;
            //如果有监听函数并且xhr对象支持绑定时就把监听函数绑定上去
            if (xhrOnProgress.onprogress && xhr.upload) {
                xhr.upload.onprogress = xhrOnProgress.onprogress;
            }
            return xhr;
        }
}
 
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
            },
            xhr:xhrOnProgress(function(e){
                var percent=e.loaded / e.total;//计算百分比
                showLoading("上传进度:" +(percent*100).toFixed(2) + "%");
            })
        });
    }
    
    function uploadBanner(){
        var formData = new FormData($('#banner-from')[0]);
        var file = $('#banner-file')[0].files[0];
        if(!file){
            return;
        }
        uploadImg('{{route('partner.card.change_banner')}}',formData,function(data){
            if (data.code == 0) {
                $('#banner-img').attr('src', data.url);
                Popup.init({
                    popHtml: '<p>上传成功</p>',
                    popFlash: {
                        flashSwitch: true,
                        flashTime: 1000,
                    }
                });
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
        var file = $('#photo-file')[0].files[0];
        if(file){
            formData.append('file',file);
        }else{
            return;
        }
        
        uploadImg('{{route('partner.card.create_img')}}',formData,function(data){
            if (data.code == 0) {
                var div = "<div class='lmg' onclick='removePhoto("+data.id+", this)'>"
                          +"<img src='"+data.url+"' />"
                          +"<img class='remove' src='/images/partner/remove.png'/>"
                          +"</div>";
                //$('.pics').append($(div));
                $(div).insertAfter($('#photo-div'));
                $('#photo-div').insertAfter($('.lmg').eq($('.lmg').length -1));
                hidePhotoDiv();
                
                Popup.init({
                    popHtml: '<p>上传成功</p>',
                    popFlash: {
                        flashSwitch: true,
                        flashTime: 1000,
                    }
                });
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

<!--视频播放-->
@if($card_info->video_url)
<script >
    (function(){
        var width = $('.banner').width();
        $('#container .remove').show().click(function(){
            Popup.init({
                popHtml: '确定要删除视频吗？',
                popOkButton: {
                    buttonDisplay: true,
                    buttonName: "确认",
                    buttonfunction: function () {
                        $.post('{{route('partner.card.remove_video')}}',{}, function(data){
                            $("#container").css('background-image', 'url()');
                            $('#container .remove').hide();
                        },'json');
                    }
                },
                popCancelButton:{
                    buttonDisplay:true,
                    buttonName:"取消",
                    buttonfunction:function(){}
                }
            });
        });
        $("#container").css('background-image', 'url({{ $card_info->video_url }}?vframe/jpg/offset/1)')
                .height((width/4)*3)
                .css('background-size', 'cover');
    })();
</script>
@endif

<script>
    function changeVideo(url , hash){
        $.post('{{route('partner.card.change_video')}}', {video_url: url, video_hash: hash},function(json){
            Popup.init({
                popHtml: json.message,
                popOkButton: {
                    buttonDisplay: true,
                    buttonName: "确认",
                    buttonfunction: function () {
                        location.reload();
                    }
                }
            });
        },'json');
    }
</script>

<!-- 视频上传 -->
<script type="text/javascript" src="/qiniu/js/plupload/plupload.full.min.js"></script>
<script type="text/javascript" src="/qiniu/js/plupload/i18n/zh_CN.js"></script>
<script type="text/javascript" src="/qiniu/js/qiniu.js"></script>
<script type="text/javascript" src="/qiniu/js/partner-card.js"></script>
<script type="text/javascript" src="/qiniu/js/partner-card-ui.js"></script>
@endsection