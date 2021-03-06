@extends('layout.default')

@section('style')
  <style>
      .gap_div_2_time { line-height:36px; color:#333; font-size:16px; text-align:center; background:url(../../images/other/gap_div_1_time.gif) center center no-repeat;}
  </style>
@endsection

@section('content')
    <div id="subject">
        <div id="main">
            <div class="good_answer_page">
                <div class="gap_div">
                    <div class="gap_title">回答</div>
                </div>
                <div class="gap_div">
                    <div class="gap_information">
                        <div class="gap_information_img"><img src="{{url($question->ask_user->profileIcon)}}" /></div>
                        <div class="gap_information_name">{{$question->ask_user->realname or $question->ask_user->nickname}}</div>
                        <div class="gap_information_title">问题：{{$question->content}}</div>
                        <div class="gap_information_answer">{{$question->created_at}}</div>
                        <div class="gap_information_price">￥{{$question->price}}</div>
                    </div>
                </div>
                <div class="gap_div">
                    <div class="gap_main">
                        <div class="gap_div_1">
                            <div class="gap_div_title">点击录音你的答案</div>
                            <div class="gap_div_button gap_div_1_button"><img src="/images/other/gap_div_button1.png" alt=""/></div>
                        </div>
                        <div class="gap_div_2" style="display:none;">
                            <div class="gap_div_title">录音中......</div>
                            <div class="gap_div_1_time"><span>1</span>"</div>
                            <div class="gap_div_button gap_div_2_button"><img src="/images/other/gap_div_button2.png" alt=""/></div>
                        </div>
                        <div class="gap_div_3" style="display:none;">
                            <div class="gap_div_title">已录完</div>
                            <div class="gap_div_2_time" style="display: none"><span>1</span>"</div>
                            <div class="gap_div_button gap_div_3_button"><img src="/images/other/gap_div_button3.png" alt=""/></div>
                            <div class="gap_div_3_button_again">重录</div>
                        </div>
                    </div>
                </div>
                <div class="gap_button">提交</div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js" type="text/javascript" charset="utf-8"></script>
    <script>
        var voice_local_id = '';//微信本地资源id
        var record_s = 0;
        var is_playing = 0;
        $(document).ready(function() {
            wx.config(<?php echo $wx_js->config(array( "translateVoice", "startRecord", "stopRecord", "onVoiceRecordEnd", "playVoice", "onVoicePlayEnd", "pauseVoice", "stopVoice", "uploadVoice"), false) ?>);
            wx.ready(function(){
                    $(".gap_div_1_button,.gap_div_3_button_again").click(function () {//点击开始或重录按钮
                        voice_local_id = '';
                        record_s = 0;
                        is_playing = 0;
                        wx.startRecord({
                            success: function () {
                                $(".gap_div_1").hide();
                                $(".gap_div_3").hide();
                                $(".gap_div_2").show();
                                $(".gap_div_1_time span").html(1);
                                timing(1);
                            },
                            cancel: function () {
                                Popup.init({
                                    popHtml:'用户拒绝授权录音',
                                    popFlash:{
                                        flashSwitch:true,
                                        flashTime:2000
                                    }
                                });
                            }
                        }),

                        wx.onVoiceRecordEnd({
                            complete: function (res) {
                                voice_local_id = res.localId;
                                Popup.init({
                                    popHtml:'录音时间已超过一分钟',
                                    popFlash:{
                                        flashSwitch:true,
                                        flashTime:2000
                                    }
                                });
                                record_complete();
                            }
                        })
                    });
                    $(".gap_div_2_button").click(function () {//点击停止或到达60秒
                        wx.stopRecord({
                            success: function (res) {
                                voice_local_id = res.localId;
                                //已录制部分
                                Popup.init({
                                    popHtml:'录制完成',
                                    popFlash:{
                                        flashSwitch:true,
                                        flashTime:2000
                                    }
                                });
                            },
                            fail: function (res) {
                                alert(JSON.stringify(res));
                            }
                        });
                        record_complete();
                    });
                    $(".gap_div_3_button").click(function () {//点击试听按钮
                        if(is_playing == 1){
                            return false;
                        }
                        is_playing = 1;
                        wx.playVoice({
                            localId: voice_local_id,
                            success: function (e) {
                                $('.gap_div_3 .gap_div_title').html('试听中......');
                                $(".gap_div_2_time").show();
                                paly_timing(1);
                            }
                        }),
                        wx.onVoicePlayEnd({
                            complete: function (e) {
                                Popup.init({
                                    popHtml:'试听结束',
                                    popFlash:{
                                        flashSwitch:true,
                                        flashTime:2000
                                    }
                                });

                                clearInterval(play_tm);
                                $('.gap_div_3 .gap_div_title').html('试听结束');
                                $(".gap_div_2_time").hide();
                                is_playing = 0;
                            }
                        })

                    });

                   //提交语音答案
                    var is_clicked = 0;
                    $('.gap_button').click(function () {
                        alert("提交中，可能需要几分钟时间，请您耐心等待...");
                        if (is_clicked == 1) {
                            return false;
                        }
                        is_clicked = 1;

                        if (voice_local_id == '') {
                            Popup.init({
                                popHtml:'请先录音',
                                popFlash:{
                                    flashSwitch:true,
                                    flashTime:2000
                                }
                            });
                            is_clicked = 0;
                        } else {
                            if (typeof(play_tm) != "undefined") {
                                clearInterval(play_tm);
                            }
                            wx.uploadVoice({
                                localId: voice_local_id,
                                success: function (res) {
                                    $.ajax({
                                        url: '{{route('user.upload_voice')}}',
                                        type: "post",
                                        data: {
                                            media_id: res.serverId,
                                            voice_long: record_s,  //语音时长
                                            question_id: '{{$question->id}}'  //语音时长
                                        },
                                        success: function(res) {
                                            Popup.init({
                                                popHtml:res.msg,
                                                popFlash:{
                                                    flashSwitch:true,
                                                    flashTime:2000
                                                }
                                            });
                                            setInterval(checkStatus, 1000);
                                            //is_clicked = 0;
                                            //两秒钟之后才能再次点击
                                            setTimeOut(function(){
                                                is_clicked = 0;
                                            },2000)
                                        }
                                    })
                                },
                                fail: function (e) {
                                    //is_clicked = 0;
                                    Popup.init({
                                        popHtml:'上传微信服务器失败',
                                        popFlash:{
                                            flashSwitch:true,
                                            flashTime:2000
                                        }
                                    });
                                }
                            })
                        }
                    });
                  });
            });


        function record_complete(){//暂停录制或录制达到一分钟效果
            $(".gap_div_1").hide();
            $(".gap_div_2").hide();
            $(".gap_div_3").show();
            $('.gap_div_3 .gap_div_title').html('已录完'+record_s+'秒');
            clearInterval(tm);
        }

        function timing(value) {//计时
            if (value < 60) {
                $(".gap_div_1_time span").html(value);
                value += 1;
                record_s = value;
                tm = setTimeout("timing(" + value + ")", 1000);
            } else {
                record_complete();
                return false;
            }
        }

        function paly_timing(b_value) {//试听计时
            $(".gap_div_2_time span").html(b_value);
            b_value += 1;
            play_tm = setTimeout("paly_timing(" + b_value + ")", 1000);
        }

        function checkStatus() {
            $.ajax({
                url: "{{route('user.upload_voice_status')}}",
                type: "post",
                data: {question_id: "{{$question->id}}"},
                success: function (res) {
                    if (res.code == 0) {
                        location.href = '{{route('tutor.answers')}}';
                    }
                }
            });
        }


    </script>
@endsection

