@extends('layout.default')
@section('content')
    <div id="subject">
        <div id="main">
            <div class="my">
                <div class="my_data_edit">
                    <div class="mde_title">编辑资料</div>
                    <form class="mde_form" id="profile-form">
                        <div class="mde_list_1">
                            <div class="mde_list_title">头像</div>
                            <div class="mde_list_img"><img src="{{url($userInfo['profileIcon'])}}" alt=""
                                                           onclick="$('#upload_image').click();"/></div>
                            <input type="file" id="upload_image" style="display:none" name="upload_image"
                                   onchange="view(this)"/>
                            <input type="hidden" name="head_canvas_data" value="">
                        </div>
                        <ul class="mde_list">
                            <li>
                                <div class="mde_list_title">昵称</div>
                                <div class="mde_list_input"><input type="text" value="{{$userInfo['nickname']}}"
                                                                   placeholder="请输入昵称"
                                                                   name="nickname" class="mde_input_name"></div>
                            </li>
                            <li>
                                <div class="mde_list_title">真实姓名</div>
                                <div class="mde_list_input"><input type="text" value="{{$userInfo['realname']}}" placeholder="请输入真实姓名"
                                                                   name="realname" class="mde_input_realname">
                                </div>
                            </li>
                            <li>
                                <div class="mde_list_title">称呼</div>
                                <div class="mde_list_select">
                                    <select name="label" class="mde_select_name">
                                        @foreach(config('constants.user_label') as $key =>$label)
                                            <option value="{{$key}}"
                                                    @if($key == $userInfo['label']) selected @endif>{{$label}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </li>
                            <li>
                                <div class="mde_list_title">年龄</div>
                                <div class="mde_list_input"><input type="text" value="{{$userInfo['age']}}"
                                                                   placeholder="请输入年龄"
                                                                   name="age" class="mde_input_age"></div>
                            </li>
                            <li>
                                <div class="mde_list_title">生日</div>
                                <div class="mde_list_input"><input type="text" value="{{$userInfo['birth']}}"
                                                                   placeholder="请输入生日"
                                                                   name="birth" class="mde_input_birthday">
                                </div>
                            </li>
                            <li>
                                <div class="mde_list_title">孩子性别</div>
                                <div class="mde_list_select">
                                    <select name="c_sex" class="mde_select_name">
                                        @foreach(config('constants.user_sex') as $key =>$label)
                                            <option value="{{$key}}"
                                                    @if($key == $userInfo['c_sex']) selected @endif>{{$label}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </li>
                            <li>
                                <div class="mde_list_title">孩子生日</div>
                                <div class="mde_list_input"><input type="text" value="{{$userInfo['c_birth']}}"
                                                                   placeholder="请输入孩子生日"
                                                                   name="c_birth"
                                                                   class="mde_input_children_birthday"></div>
                            </li>
                        </ul>
                        <div class="mde_button profile-button"><input type="button" class="mde_button" value="保存修改">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="cut-img" style="display: none">
        <div class="cut-body" id="img-container">
            <img src="">
        </div>
        <div class="mde_button" style="margin-top:50px">
            <input type="button" class="mde_button" value="确认" id="reg_btn">
            <input type="button" class="mde_button" value="取消" id="cancel_btn" style="margin-top:10px">
        </div>
    </div>
@endsection

@section('style')
    <link rel="stylesheet" href="/js/cropper/cropper.min.css">

    <link href="/css/Mobiscroll/mobiscroll.animation.css" rel="stylesheet" type="text/css"/>
    <link href="/css/Mobiscroll/mobiscroll.icons.css" rel="stylesheet" type="text/css"/>
    <link href="/css/Mobiscroll/mobiscroll.frame.css" rel="stylesheet" type="text/css"/>
    <link href="/css/Mobiscroll/mobiscroll.frame.android.css" rel="stylesheet" type="text/css"/>
    <link href="/css/Mobiscroll/mobiscroll.frame.android-holo.css" rel="stylesheet" type="text/css"/>
    <link href="/css/Mobiscroll/mobiscroll.frame.ios-classic.css" rel="stylesheet" type="text/css"/>
    <link href="/css/Mobiscroll/mobiscroll.frame.ios.css" rel="stylesheet" type="text/css"/>
    <link href="/css/Mobiscroll/mobiscroll.frame.jqm.css" rel="stylesheet" type="text/css"/>
    <link href="/css/Mobiscroll/mobiscroll.frame.sense-ui.css" rel="stylesheet" type="text/css"/>
    <link href="/css/Mobiscroll/mobiscroll.frame.wp.css" rel="stylesheet" type="text/css"/>
    <link href="/css/Mobiscroll/mobiscroll.scroller.css" rel="stylesheet" type="text/css"/>
    <link href="/css/Mobiscroll/mobiscroll.scroller.android.css" rel="stylesheet" type="text/css"/>
    <link href="/css/Mobiscroll/mobiscroll.scroller.android-holo.css" rel="stylesheet" type="text/css"/>
    <link href="/css/Mobiscroll/mobiscroll.scroller.ios-classic.css" rel="stylesheet" type="text/css"/>
    <link href="/css/Mobiscroll/mobiscroll.scroller.ios.css" rel="stylesheet" type="text/css"/>
    <link href="/css/Mobiscroll/mobiscroll.scroller.jqm.css" rel="stylesheet" type="text/css"/>
    <link href="/css/Mobiscroll/mobiscroll.scroller.sense-ui.css" rel="stylesheet" type="text/css"/>
    <link href="/css/Mobiscroll/mobiscroll.scroller.wp.css" rel="stylesheet" type="text/css"/>
    <link href="/css/Mobiscroll/mobiscroll.image.css" rel="stylesheet" type="text/css"/>

    <link href="/css/Mobiscroll/mobiscroll.android-holo-light.css" rel="stylesheet" type="text/css"/>
    <link href="/css/Mobiscroll/mobiscroll.wp-light.css" rel="stylesheet" type="text/css"/>
    <link href="/css/Mobiscroll/mobiscroll.mobiscroll-dark.css" rel="stylesheet" type="text/css"/>
@endsection
@section('script')
    <script src="/js/Mobiscroll/mobiscroll.zepto.js"></script>
    <script src="/js/Mobiscroll/mobiscroll.core.js"></script>
    <script src="/js/Mobiscroll/mobiscroll.frame.js"></script>
    <script src="/js/Mobiscroll/mobiscroll.scroller.js"></script>

    <script src="/js/Mobiscroll/mobiscroll.util.datetime.js"></script>
    <script src="/js/Mobiscroll/mobiscroll.datetimebase.js"></script>
    <script src="/js/Mobiscroll/mobiscroll.datetime.js"></script>
    <script src="/js/Mobiscroll/mobiscroll.select.js"></script>
    <script src="/js/Mobiscroll/mobiscroll.listbase.js"></script>
    <script src="/js/Mobiscroll/mobiscroll.image.js"></script>
    <script src="/js/Mobiscroll/mobiscroll.treelist.js"></script>

    <script src="/js/Mobiscroll/mobiscroll.frame.android.js"></script>
    <script src="/js/Mobiscroll/mobiscroll.frame.android-holo.js"></script>
    <script src="/js/Mobiscroll/mobiscroll.frame.ios-classic.js"></script>
    <script src="/js/Mobiscroll/mobiscroll.frame.ios.js"></script>
    <script src="/js/Mobiscroll/mobiscroll.frame.jqm.js"></script>
    <script src="/js/Mobiscroll/mobiscroll.frame.sense-ui.js"></script>
    <script src="/js/Mobiscroll/mobiscroll.frame.wp.js"></script>

    <script src="/js/Mobiscroll/mobiscroll.android-holo-light.js"></script>
    <script src="/js/Mobiscroll/mobiscroll.wp-light.js"></script>
    <script src="/js/Mobiscroll/mobiscroll.mobiscroll-dark.js"></script>

    <script src="/js/Mobiscroll/i18n/mobiscroll.i18n.cs.js"></script>
    <script src="/js/Mobiscroll/i18n/mobiscroll.i18n.de.js"></script>
    <script src="/js/Mobiscroll/i18n/mobiscroll.i18n.en-UK.js"></script>
    <script src="/js/Mobiscroll/i18n/mobiscroll.i18n.es.js"></script>
    <script src="/js/Mobiscroll/i18n/mobiscroll.i18n.fa.js"></script>
    <script src="/js/Mobiscroll/i18n/mobiscroll.i18n.fr.js"></script>
    <script src="/js/Mobiscroll/i18n/mobiscroll.i18n.hu.js"></script>
    <script src="/js/Mobiscroll/i18n/mobiscroll.i18n.it.js"></script>
    <script src="/js/Mobiscroll/i18n/mobiscroll.i18n.ja.js"></script>
    <script src="/js/Mobiscroll/i18n/mobiscroll.i18n.nl.js"></script>
    <script src="/js/Mobiscroll/i18n/mobiscroll.i18n.no.js"></script>
    <script src="/js/Mobiscroll/i18n/mobiscroll.i18n.pl.js"></script>
    <script src="/js/Mobiscroll/i18n/mobiscroll.i18n.pt-BR.js"></script>
    <script src="/js/Mobiscroll/i18n/mobiscroll.i18n.pt-PT.js"></script>
    <script src="/js/Mobiscroll/i18n/mobiscroll.i18n.ro.js"></script>
    <script src="/js/Mobiscroll/i18n/mobiscroll.i18n.ru.js"></script>
    <script src="/js/Mobiscroll/i18n/mobiscroll.i18n.ru-UA.js"></script>
    <script src="/js/Mobiscroll/i18n/mobiscroll.i18n.sk.js"></script>
    <script src="/js/Mobiscroll/i18n/mobiscroll.i18n.sv.js"></script>
    <script src="/js/Mobiscroll/i18n/mobiscroll.i18n.tr.js"></script>
    <script src="/js/Mobiscroll/i18n/mobiscroll.i18n.zh.js"></script>

    <!-- 头像上传 -->
    <script src="/js/cropper/cropper.min.js"></script>
    <script>
        $(function () {
            $('.cut-body').height($('body').width());
            $('#img-container > img').on("load", function () {        // 等待图片加载成功后，才进行图片的裁剪功能
                $('#img-container > img').cropper({
                    aspectRatio: 1 / 1,// 1：1的比例进行裁剪，可以是任意比例，自己调整
                    cropBoxResizable: false,
                    viewMode: 2,
                    dragMode: 'move',
                    minContainerWidth: $('body').width(),
                    built: function () {
                        $('#subject').hide();
                        $('.cut-img').show();
                    }
                });
            });
            $('.cut-img #cancel_btn').on("click", function () {
                $('#img-container > img').cropper('destroy');
                $('#subject').show();
                $('.cut-img').hide();
            })
            $('.cut-img #reg_btn').on("click", function () {
                var head_canvas = $('#img-container > img').cropper('getCroppedCanvas', {
                    width: 100,
                    height: 100
                });
                var head_canvas_data = head_canvas.toDataURL("image/jpeg", 1);
                $('.header-img').attr({
                    'src': head_canvas_data
                });
                $('input[name="head_canvas_data"]').val(head_canvas_data);
                $('.mde_list_img img').attr('src', head_canvas_data);

                $('#img-container > img').cropper('destroy');
                $('#subject').show();
                $('.cut-img').hide();
            });
        });
        function view(obj) {
            var src = "";
            if (document.all) {
                obj.select();
                src = document.selection.createRange().text;
                document.selection.empty();
            } else {
                var file = obj.files[0];
                src = window.createObjectURL && window.createObjectURL(file) || window.URL && window.URL.createObjectURL(file) || window.webkitURL && window.webkitURL.createObjectURL(file);
            }
            $('#img-container img').attr({'src': src});
        }
    </script>
    <!-- 提交修改 -->
    <script>
        $(document).ready(function () {
            var lock = false;
            $('.profile-button').click(function (e) {
                e.preventDefault();
                if (lock) return;
                lock = true;
                $.ajax({
                    type: "post",
                    url: "{{route('user.profile.update')}}",
                    data: $('#profile-form').serialize(),
                    dataType: "json",
                    success: function (res) {
                        if (res.code == 0) {
                            Popup.init({
                                popHtml: '<p>' + res.message + '</p>',
                                popOkButton:{
                                    buttonDisplay:true,
                                    buttonName:"确认",
                                    buttonfunction:function(){
                                        location.href='{{route('user.profile')}}';
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
                                popHtml: '<p>个人资料更新失败！</p>',
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
    <!-- 联动 -->
    <script type="text/javascript">
        $(document).ready(function () {
            $('.mde_select_name').mobiscroll().select({//调用Mobiscroll
                theme: "android-holo",
                mode: "scroller",
                display: "bottom",
                lang: "zh"
            });
            $('.mde_input_birthday').mobiscroll().date({//调用Mobiscroll
                theme: "android-holo",
                mode: "scroller",
                display: "bottom",
                lang: "zh"
            });
            $('.mde_input_children_birthday').mobiscroll().date({//调用Mobiscroll
                theme: "android-holo",
                mode: "scroller",
                display: "bottom",
                lang: "zh"
            });
        });
    </script>
@endsection