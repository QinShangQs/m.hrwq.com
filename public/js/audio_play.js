$(document).ready(function(){//列表音频播放
	window.audioPlay={//
		init:function(value){//初始化ajax请求的 问题qid 音频类型aid
            value.url=value.url||"";
			this.conduct( value.url);
		},
		conduct:function(request_url){//开始执行
            //涉及多音频唯一播放，单音频暂停，继续
            var playing = false, currentAudio = null, currentAudioContainer = null,beforeAudioContainer = null ;
            $(document).on('click','.audio_can_play',function(){
                var that  = this;
                //当前音频容器对象
                if(currentAudioContainer == null){
                    currentAudioContainer = this;
                }

                //是否正在播放  暂停播放 1.播放中 2.其他音频
                if (playing) {
                    //关闭播放
                    currentAudio.pause();
                    //关闭播放效果
                    $(currentAudioContainer).css({'background-image':'url(../../images/ask/ga_div_2_list_answer_voice.png)'});
                    //当前播放对象存储
                    beforeAudioContainer = currentAudioContainer;
                    //音频容器 改为当前
                    currentAudioContainer = this;
                }

                //判断 暂停/播放
                var is_playing_audio = $(this).hasClass('on_play');
                if(is_playing_audio){
                    //暂停 操作
                    //播放中音频 则为 暂停,取消 播放状态描述
                    $(currentAudioContainer).removeClass('on_play');
                    //增加 暂停状态描述
                    $(currentAudioContainer).addClass('is_stop');
                    //console.log('stop');
                }else{
                    //播放 操作
                    //是否点击新音频
                    var is_paused_audio = $(this).hasClass('is_stop');
                    if(!is_paused_audio)
                    {
                        //是否写入收听记录
                        if(request_url!=''){
                            //新音频 ajax记录收听记录--异步操作 减少重复请求
                            var audio_type = $(this).attr('aid'),question_id = $(this).attr('qid');
                            $.ajax({
                                type: 'post',
                                url: request_url,
                                data: {aid:audio_type,qid:question_id},
                                dataType: 'json'
                            });
                        }
                    //非暂停的音频,创建新音频对象
                    var $audio = $(this).find("audio");
                    $audio.attr("src", $audio.data("src"));
                    currentAudio = $audio.get(0);

                    //新音频 之前音频容器初始化
                    if(beforeAudioContainer)
                    {
                        $(beforeAudioContainer).removeClass('on_play');
                        $(beforeAudioContainer).removeClass('is_stop');
                    }
                }else{
                    //继续之前的暂停音频
                    //取消 暂停状态描述
                    $(this).removeClass('is_stop');
                    //播放从上次暂停往前推3秒
                    var pause_audio_start_time = currentAudio.currentTime - 3;
                    currentAudio.currentTime = (pause_audio_start_time > 0) ? pause_audio_start_time : 0;
                    //console.log(currentAudio.currentTime);
                }

                //增加播放状态
                playing = true;
                $(this).css({'background-image':'url(../../images/ask/ga_div_2_list_answer_voice.gif)'});
                $(this).addClass('on_play');
                //音频播放
                currentAudio.play();

                //播放结束监听回调 初始化状态
                currentAudio.addEventListener("ended", function() {
                    playing = false;
                    currentAudio = null;
                    currentAudioContainer = null;
                    beforeAudioContainer = null;
                    $(that).css({'background-image':'url(../../images/ask/ga_div_2_list_answer_voice.png)'});
                    $(that).removeClass('on_play');
                    $(that).removeClass('is_stop');
                });

            }
        });
		}
	}
});