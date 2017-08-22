@extends('layout.default')
@section('content')
    <div id="subject">
        <div id="main">
            <div class="my">
                <div class="my_be_teachers_data">
                    <div class="mbt_title">完善指导师信息</div>
                    <div class="mbtd_title">基本信息</div>
                    <form class="mbtd_form mmop_form">
                        <ul class="mbtd_list">
                            <li>
                                <div class="mbtd_list_title">真实姓名</div>
                                <div class="mbtd_list_input"><input type="text" value="{{$user->realname}}" placeholder="请输入真实姓名" name="realname" class="mbtd_input_realname"></div>
                            </li>
                            <li>
                                <div class="mbtd_list_title">头衔</div>
                                <div class="mbtd_list_input">
                                <input name="tutor_honor" type="hidden" value="家庭教育指导师">
                                <input type="text" readonly="readonly" value="家庭教育指导师" placeholder="如工作职位/家里蹲大学资深妈妈" class="mbtd_input_realname"></div>
                            </li>
                            <li>
                                <div class="mbtd_list_title">性别</div>
                                <div class="">
                                	<div style="display: flex;flex-direction: row;height: 46px;justify-content: flex-start; align-items: center;">
                                	&nbsp;@foreach(config('constants.user_sex') as $key =>$label)
                                		<input type="radio" name="sex" value="{{$key}}" @if($key == $user->sex) checked @endif />
                                		<span>{{$label}}&nbsp;</span>
                                    @endforeach
                                	</div>
                                </div>
                            </li>
                            <li>
                                <div class="mbtd_list_title">邮箱</div>
                                <div class="mbtd_list_input"><input type="text" value="{{$user->email}}" placeholder="请输入邮箱" name="email" class="mbtd_input_realname"></div>
                            </li>
                            <li>
                                <div class="mbtd_list_title">通讯地址</div>
                                <div class="mbtd_list_input"><input type="text" value="{{$user->address}}" placeholder="请输入真实地址" name="address" class="mbtd_input_address"></div>
                            </li>
                        </ul>
                        <dl>
                            <dt>封面图片</dt>
                            <dd>
                                <div id="filePicker">
                                    选择图片
                                </div>
                                <small style="color:red">(推荐尺寸：750*340px)</small>
                                <div id="fileList" class="uploader-list">
                                    @if($user->tutor_cover)
                                        <div id="WU_FILE_0" class="file-item thumbnail upload-state-done">
                                            <img src="{{asset($user->tutor_cover)}}" style="width: 100%">
                                            <div class="info"></div>
                                        </div>
                                    @endif
                                </div>
                                <input type="hidden" name="tutor_cover" value="{{$user->tutor_cover}}">
                            </dd>
                            <dt style="display: none">向我提问需支付</dt>
                            <dd style="display: none"><input type="text" value="{{$user->tutor_price}}" class="mmop_price" name="tutor_price" placeholder="1-100元"></dd>
                            <dt>个人介绍</dt>
                            <dd><textarea class="mmop_textarea" name="tutor_introduction" placeholder="20-150字，从家庭教育中的成长经历、感悟、擅长技能及孩子的转变情况等角度，介绍下自己是怎样一位父母，或者作为父母自己是怎样看待家庭教育的。">{{$user->tutor_introduction}}</textarea></dd>
                        </dl>
                        <div class="mbtd_button"><input type="button" class="mbtd_button" value="保存修改"></div>

                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection
@section('style')
    <link href="/vendors/webuploader-0.1.5/webuploader.css" rel="stylesheet">
    <style>
        .mbtd_list_select select {
            line-height: 46px;
            padding: 0 10px;
            box-sizing: border-box;
            border: none;
            font-size: 16px;
            color: #323232;
        }
    </style>
@endsection
@section('script')
    <!--<script src="/js/Mobiscroll/mobiscroll.zepto.js"></script>
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

    <script type="text/javascript">
        $(document).ready(function(){
            $('.mbtd_select_sex').mobiscroll().select({//调用Mobiscroll
                theme: "android-holo",
                mode: "scroller",
                display: "bottom",
                lang: "zh"
            });
        });
    </script>-->

    <!-- 上传图片 -->
    <script src="/vendors/webuploader-0.1.5/webuploader.min.js" type="text/javascript"></script>
    <script>
        jQuery(function () {
            var $ = jQuery,
                    $list = $('#fileList'),
                    // 优化retina, 在retina下这个值是2
                    ratio = window.devicePixelRatio || 1,

                    // 缩略图大小
                    thumbnailWidth = 100 * ratio,
                    thumbnailHeight = 100 * ratio,

                    // Web Uploader实例
                    uploader;

            // 初始化Web Uploader
            uploader = WebUploader.create({

                // 自动上传。
                auto: true,

                // swf文件路径
                swf: '/vendors/webuploader-0.1.5/Uploader.swf',

                // 文件接收服务端。
                server: '{{route('tutor.upload')}}',

                // 选择文件的按钮。可选。
                // 内部根据当前运行是创建，可能是input元素，也可能是flash.
                pick: '#filePicker',

                // 只允许选择文件，可选。
                accept: {
                    title: 'Images',
                    extensions: 'gif,jpg,jpeg,bmp,png',
                    mimeTypes: 'image/*'
                },

                //上传数量限制
                fileNumLimit: 1
            });

            // 当有文件添加进来的时候
            uploader.on('fileQueued', function (file) {
                var $li = $(
                                '<div id="' + file.id + '" class="file-item thumbnail">' +
                                '<img>' +
                                '<div class="info">' + file.name + '</div>' +
                                '</div>'
                        ),
                        $img = $li.find('img');

                $list.html($li);

                // 创建缩略图
                uploader.makeThumb(file, function (error, src) {
                    if (error) {
                        $img.replaceWith('<span>不能预览</span>');
                        return;
                    }

                    $img.attr('src', src);
                }, thumbnailWidth, thumbnailHeight);
            });
            //发送文件之前，加上csrf_token数据
            uploader.on('uploadBeforeSend', function (block, data) {
                data._token = '{{csrf_token()}}';
            });

            // 文件上传过程中创建进度条实时显示。
            uploader.on('uploadProgress', function (file, percentage) {
                var $li = $('#' + file.id),
                        $percent = $li.find('.progress span');

                // 避免重复创建
                if (!$percent.length) {
                    $percent = $('<p class="progress"><span></span></p>')
                            .appendTo($li)
                            .find('span');
                }
                $percent.css('width', percentage * 100 + '%');
            });

            // 文件上传成功，给item添加成功class, 用样式标记上传成功。将地址传入隐藏input
            uploader.on('uploadSuccess', function (file, response) {
                $('#' + file.id).addClass('upload-state-done');
                if (response.code == 0)
                    $('input[name="tutor_cover"]').val(response.data);
                uploader.reset();
            });

            // 文件上传失败，现实上传出错。
            uploader.on('uploadError', function (file) {
                var $li = $('#' + file.id),
                        $error = $li.find('div.error');

                // 避免重复创建
                if (!$error.length) {
                    $error = $('<div class="error"></div>').appendTo($li);
                }

                $error.text('上传失败');
            });
            uploader.on('error', function (type) {
                if (type === 'Q_EXCEED_NUM_LIMIT') {
                    alert('最多允许上传1张图片');
                }
            });
            // 完成上传完了，成功或者失败，先删除进度条。
            uploader.on('uploadComplete', function (file) {
                $('#' + file.id).find('.progress').remove();
            });
        });
    </script>
    <!-- 提交信息 -->
    <script>
        $(document).ready(function () {
            var lock = false;
            $('.mbtd_button').click(function (e) {
                e.preventDefault();
                if (lock) return;
                lock = true;
                $.ajax({
                    type: "post",
                    url: "{{route('tutor.save')}}",
                    data: $('.mbtd_form').serialize(),
                    dataType: "json",
                    success: function (res) {
                        if (res.code == 0) {
                            Popup.init({
                                popHtml: res.message,
                                popFlash: {
                                    flashSwitch: true,
                                    flashTime: 2000
                                }
                            });
                            location.href='{{route('user')}}';
                        } else {
                            Popup.init({
                                popHtml: res.message,
                                popFlash: {
                                    flashSwitch: true,
                                    flashTime: 2000
                                }
                            });
                        }
                        lock = false;
                    },
                    error: function (res) {
                        var errors = res.responseJSON;
                        for (var o in errors) {
                            Popup.init({
                                popHtml: errors[o][0],
                                popFlash: {
                                    flashSwitch: true,
                                    flashTime: 2000
                                }
                            });
                            break;
                        }
                        lock = false;
                    }
                });
            });
        });
    </script>
@endsection

@section('style')
    <link href="/css/Mobiscroll/mobiscroll.animation.css" rel="stylesheet" type="text/css" />
    <link href="/css/Mobiscroll/mobiscroll.icons.css" rel="stylesheet" type="text/css" />
    <link href="/css/Mobiscroll/mobiscroll.frame.css" rel="stylesheet" type="text/css" />
    <link href="/css/Mobiscroll/mobiscroll.frame.android.css" rel="stylesheet" type="text/css" />
    <link href="/css/Mobiscroll/mobiscroll.frame.android-holo.css" rel="stylesheet" type="text/css" />
    <link href="/css/Mobiscroll/mobiscroll.frame.ios-classic.css" rel="stylesheet" type="text/css" />
    <link href="/css/Mobiscroll/mobiscroll.frame.ios.css" rel="stylesheet" type="text/css" />
    <link href="/css/Mobiscroll/mobiscroll.frame.jqm.css" rel="stylesheet" type="text/css" />
    <link href="/css/Mobiscroll/mobiscroll.frame.sense-ui.css" rel="stylesheet" type="text/css" />
    <link href="/css/Mobiscroll/mobiscroll.frame.wp.css" rel="stylesheet" type="text/css" />
    <link href="/css/Mobiscroll/mobiscroll.scroller.css" rel="stylesheet" type="text/css" />
    <link href="/css/Mobiscroll/mobiscroll.scroller.android.css" rel="stylesheet" type="text/css" />
    <link href="/css/Mobiscroll/mobiscroll.scroller.android-holo.css" rel="stylesheet" type="text/css" />
    <link href="/css/Mobiscroll/mobiscroll.scroller.ios-classic.css" rel="stylesheet" type="text/css" />
    <link href="/css/Mobiscroll/mobiscroll.scroller.ios.css" rel="stylesheet" type="text/css" />
    <link href="/css/Mobiscroll/mobiscroll.scroller.jqm.css" rel="stylesheet" type="text/css" />
    <link href="/css/Mobiscroll/mobiscroll.scroller.sense-ui.css" rel="stylesheet" type="text/css" />
    <link href="/css/Mobiscroll/mobiscroll.scroller.wp.css" rel="stylesheet" type="text/css" />
    <link href="/css/Mobiscroll/mobiscroll.image.css" rel="stylesheet" type="text/css" />

    <link href="/css/Mobiscroll/mobiscroll.android-holo-light.css" rel="stylesheet" type="text/css" />
    <link href="/css/Mobiscroll/mobiscroll.wp-light.css" rel="stylesheet" type="text/css" />
    <link href="/css/Mobiscroll/mobiscroll.mobiscroll-dark.css" rel="stylesheet" type="text/css" />
@endsection