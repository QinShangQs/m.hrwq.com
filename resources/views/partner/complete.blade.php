@extends('layout.default')
@section('content')
<div id="subject">
    <div id="main">
        <div class="my">
            <div class="my_be_teachers_data">
                <div class="mbt_title">完善合伙人资料</div>
                <div class="mbtd_title">基本信息</div>
                <form class="mbtd_form">
                    <ul class="mbtd_list">
                        <li>
                            <div class="mbtd_list_title">真实姓名</div>
                            <div class="mbtd_list_input"><input type="text" value="{{$userInfo['realname']}}" placeholder="请输入真实姓名" name="realname" class="mbtd_input_realname"></div>
                        </li>
                        <li>
                            <div class="mbtd_list_title">性别</div>
                            <div class="mbtd_list_select">
                                <select name="sex" class="mbtd_select_sex">
                                    @foreach(config('constants.user_sex') as $key =>$label)
                                        <option value="{{$key}}"
                                                @if($key == $userInfo['sex']) selected @endif>{{$label}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </li>
                        <li>
                            <div class="mbtd_list_title">邮箱</div>
                            <div class="mbtd_list_input"><input type="text" value="{{$userInfo['email']}}" placeholder="请输入邮箱" name="email" class="mbtd_input_realname"></div>
                        </li>
                        <li>
                            <div class="mbtd_list_title">通讯地址</div>
                            <div class="mbtd_list_input"><input type="text" value="{{$userInfo['address']}}" placeholder="请输入地址" name="address" class="mbtd_input_address2"></div>
                        </li>
                        <li>
                            <div class="mbtd_list_title">期望城市</div>
                            <div class="mbtd_list_input">
                                <ul id="mde_input_city">
                                    @if(!empty($area))
                                        @foreach($area as $parent)
                                            <li data-area-id="{{$parent['area_id']}}">
                                                <span>{{$parent['area_name']}}</span>
                                                @if(!empty($parent['children']))
                                                    <ul>
                                                        @foreach($parent['children'] as $child)
                                                            <li data-area-id="{{$child['area_id']}}">
                                                                <span>{{$child['area_name']}}</span>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            </li>
                                        @endforeach
                                    @endif
                                </ul>
                                <input type="hidden" name="province" value="{{@$userInfo['partner_city_parent']}}">
                                <input type="hidden" name="city" value="{{@$userInfo['partner_city']}}">
                            </div>
                        </li>
                    </ul>
                    <div class="mbtd_button"><input type="submit" class="mbtd_button" value="提交"></div>
                </form>
                
            </div>
        </div>
    </div>
</div>
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

    <!-- 提交信息 -->
    <script>
        $(document).ready(function () {
            var province_id = $('input[name="province"]').val();
            var city_id = $('input[name="city"]').val();

            var defaultValue = [$('li[data-area-id="'+province_id+'"]').index(), $('li[data-area-id="'+city_id+'"]').index()];

            $('#mde_input_city').mobiscroll().treelist({//调用Mobiscroll
                theme: "android-holo",
                mode: "scroller",
                display: "bottom",
                lang: "zh",
                placeholder: "请选择所在城市",
                labels: ['省', '市'],
                formatResult: function (array) { //返回自定义格式结果
                    $('input[name="province"]').val('');
                    $('input[name="city"]').val('');
                    var res = '';
                    if(array[0] != null&&array[0] != -1) {
                        var province = $('#mde_input_city>li').eq(array[0]);
                        res += province.children('span').html();
                        $('input[name="province"]').val(province.data('area-id'));
                        if(array[1] != null) {
                            var city = province.children('ul').children('li').eq(array[1]);
                            res += city.children('span').html();
                            $('input[name="city"]').val(city.data('area-id'));
                            if(array[2]!=null) {
                                var district = city.children('ul').children('li').eq(array[2]);
                                res += district.children('span').html();
                                $('input[name="district"]').val(district.data('area-id'));
                            }
                        }
                    }
                    return res;
                },
                defaultValue: defaultValue,
                onInit: function (inst) {
                    //需要输出编辑前的地址
                    var res = '';
                    var province_id = $('input[name="province"]').val();
                    if($('li[data-area-id="'+province_id+'"]').length)
                        res += $('li[data-area-id="'+province_id+'"]').find('span').html();
                    var city_id = $('input[name="city"]').val();
                    if($('li[data-area-id="'+city_id+'"]').length)
                        res += $('li[data-area-id="'+city_id+'"]').find('span').html();
                    var district_id = $('input[name="district"]').val();
                    if($('li[data-area-id="'+district_id+'"]').length)
                        res += $('li[data-area-id="'+district_id+'"]').find('span').html();
                    $("#mde_input_city_dummy").val(res);
                }
            });
            $('.mbtd_select_sex').mobiscroll().select({//调用Mobiscroll
                theme: "android-holo",
                mode: "scroller",
                display: "bottom",
                lang: "zh"
            });

            var lock = false;
            $('.mbtd_button').click(function (e) {
                e.preventDefault();
                if (lock) return;
                lock = true;
                //检查合伙人城市
                $.ajax({
                    type: "post",
                    url: "{{route('partner.city_check')}}",
                    data: $('.mbtd_form').serialize(),
                    dataType: "json",
                    success: function (res) {
                        if (res.code == 0) {
                           Popup.init({
                                popHtml:'<p>该城市已存在合伙人,点击确定继续申请!</p>',
                                popOkButton:{
                                    buttonDisplay:true,
                                    buttonName:"确定",
                                    buttonfunction:function(){
                                        $.ajax({
                                            type: "post",
                                            url: "{{route('partner.save')}}",
                                            data: $('.mbtd_form').serialize(),
                                            dataType: "json",
                                            success: function (res) {
                                                if (res.code == 0) {
                                                    Popup.init({
                                                        popHtml:'<p>'+res.message+'</p>',
                                                        popFlash:{
                                                            flashSwitch:true,
                                                            flashTime:2000,
                                                        }
                                                    });
                                                    location.href='{{route('user')}}';
                                                } else {
                                                    Popup.init({
                                                        popHtml:'<p>'+res.message+'</p>',
                                                        popFlash:{
                                                            flashSwitch:true,
                                                            flashTime:2000,
                                                        }
                                                    });
                                                }
                                                lock = false;
                                            },
                                            error: function (res) {
                                                Popup.init({
                                                        popHtml:'<p>合伙人资料提交失败！</p>',
                                                        popFlash:{
                                                            flashSwitch:true,
                                                            flashTime:2000,
                                                        }
                                                    });
                                                lock = false;
                                            }
                                        });
                                    }
                                },
                                popCancelButton:{
                                    buttonDisplay:true,
                                    buttonName:"取消",
                                    buttonfunction:function(){}
                                },
                                popFlash:{
                                    flashSwitch:false
                                }
                            });
                            lock = false;
                        } else {
                            $.ajax({
                                type: "post",
                                url: "{{route('partner.save')}}",
                                data: $('.mbtd_form').serialize(),
                                dataType: "json",
                                success: function (res) {
                                    if (res.code == 0) {
                                        Popup.init({
                                            popHtml:'<p>'+res.message+'</p>',
                                            popFlash:{
                                                flashSwitch:true,
                                                flashTime:2000,
                                            }
                                        });
                                        location.href='{{route('user')}}';
                                    } else {
                                        Popup.init({
                                            popHtml:'<p>'+res.message+'</p>',
                                            popFlash:{
                                                flashSwitch:true,
                                                flashTime:2000,
                                            }
                                        });
                                    }
                                    lock = false;
                                },
                                error: function (res) {
                                    Popup.init({
                                            popHtml:'<p>合伙人资料提交失败！</p>',
                                            popFlash:{
                                                flashSwitch:true,
                                                flashTime:2000,
                                            }
                                        });
                                    lock = false;
                                }
                            });
                        }
                        lock = false;
                    },
                    error: function (res) {
                        Popup.init({
                                popHtml:'<p>合伙人资料提交失败！</p>',
                                popFlash:{
                                    flashSwitch:true,
                                    flashTime:2000,
                                }
                            });
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