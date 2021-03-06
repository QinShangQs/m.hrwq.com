@extends('layout.default')
@section('content')
<div id="subject">
    <div id="main">
        <div class="my">
            <div class="my_operation_data">
                <div class="mod_top">运营数据</div>
                <div class="mod_div">
                    <div class="mod_div_left">
                        <div class="mod_div_p1">累计注册用户</div>
                        <div class="mod_div_p2"><span>{{$userAllCnt or 0}}</span>人</div>
                    </div>
                    <div class="mod_div_right">
                        <div class="mod_div_p1">昨日新增用户</div>
                        <div class="mod_div_p2"><span>{{$userYesterdayCnt or 0}}</span>人</div>
                    </div>
                </div>
                <ul class="mod_tab">
                    <li id="mod_tab_1" class="selected">最近一周</li>
                    <li id="mod_tab_2" >最近一月</li>
                    <li id="mod_tab_3">最近一年</li>
                </ul>
                <div class="mod_div_0 mod_div_1" style="height:200px"></div>
                <div class="mod_div_0 mod_div_2" style="display:none;height:200px;"></div>
                <div class="mod_div_0 mod_div_3" style="display:none;height:200px;"></div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script src="/vendors/bower_components/flot/jquery.flot.js"></script>
<script src="/vendors/bower_components/flot/jquery.flot.resize.js"></script>
<script src="/vendors/bower_components/flot-orderBars/js/jquery.flot.orderBars.js"></script>
<script src="/vendors/bower_components/flot.curvedlines/curvedLines.js"></script>
<script src="/vendors/bower_components/flot-orderBars/js/jquery.flot.orderBars.js"></script>
<script type="text/javascript">
$(document).ready(function(){
    $(".mod_tab li").click(function(){//tab切换
        if($(this).attr("class")!="selected"){
            $(".mod_tab li").attr("class","");
            $(this).attr("class","selected");
            $(".mod_div_0").hide();
            switch($(this).attr("id")){
                case "mod_tab_1":
                    day7('.mod_div_1');
                    $(".mod_div_1").show();
                    break;
                case "mod_tab_2":
                    stat_user({{$cur_year}},{{$cur_month}},'.mod_div_2');
                    $(".mod_div_2").show();
                    break;
                case "mod_tab_3":
                    stat_user({{$cur_year}},0,'.mod_div_3');
                    $(".mod_div_3").show();
                    break;
                default:
                    break;
            }
        }
    });

    day7('.mod_div_1');

    function stat_user(select_s_year,select_s_month,operatediv){
        $.ajax({
            type: 'post',
            url: '{{route('partner.user')}}',
            data:{select_s_month:select_s_month,select_s_year:select_s_year},
            dataType: 'json',
            success: function (res) {
                if(res.code == 0){
                    barData = res.content;
                    somePlot = $.plot($(operatediv), barData, {
                        points: { show: true, fill: false },
                        grid : {
                            borderWidth: 1
                        },
                        xaxis: {
                            tickDecimals: 0,
                            ticks: res.tick,
                            font: {
                                lineHeight: 13,
                                style: "normal",
                                color: "#FF5722"
                            }
                        },
                        yaxis: {
                            tickColor: '#000',
                            tickDecimals: 0,
                            font :{
                                lineHeight: 13,
                                style: "normal",
                                color: "#000"
                            }
                        },
                        lines: {
                            show: true
                        },
                        colors: ["#4CAF50"],
                    });
                }
            }
        });
    }
    function day7(operatediv){
        $.ajax({
            type: 'post',
            url: '{{route('partner.day7')}}',
            data:{},
            dataType: 'json',
            success: function (res) {
                if(res.code == 0){
                    barData = res.content;
                    somePlot = $.plot($(operatediv), barData, {
                        points: { show: true, fill: false },
                        grid : {
                            borderWidth: 1
                        },
                        xaxis: {
                            tickDecimals: 0,
                            ticks: res.tick,
                            font: {
                                lineHeight: 13,
                                style: "normal",
                                color: "#FF5722"
                            }
                        },
                        yaxis: {
                            tickColor: '#000',
                            tickDecimals: 0,
                            font :{
                                lineHeight: 13,
                                style: "normal",
                                color: "#000"
                            }
                        },
                        lines: {
                            show: true
                        },
                        colors: ["#4CAF50"],
                    });
                }
            }
        });
    }
});
</script>
@endsection