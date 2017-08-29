@extends('layout.default')
@section('content')

<style>

body{
	background-color: #fff
}

.play-container{
	position:absolute;
	top:12.3rem;
	z-index:10;
	display:flex;
	width:100%;
	justify-content: center;
	background-color: #fff;
}

@media screen and (min-width: 400px) {
    .play-container{
        top:14rem;
    }
}


</style>

<div id="subject" >
    <div id="main" >
        <div class="share-love-hot" style="position:relative">
            <img src="/images/share/yinpinhaibao.jpg" alt=""/>
            <div class="play-container">
            	<img id="play-img" onclick="playPause()" src="/images/share/audio-pause.png" style="width:2.06rem"/>
            </div>
        </div>
        <div style="text-align:center">
	        	<a href="{{route('user.login')}}">
	        		<img style="width:8.125rem;height:2.15rem;margin-top:1rem" src="/images/share/register-btn.png"/>
	        	</a>
	        	<p style="font-size:.95rem;color:#999999;text-align:center;margin-top:0.15rem;margin-bottom:0.55rem">
	        		注册即可获得7天会员体验期
	        	</p>
        </div>
        <audio id="audio1" preload="auto" loop="loop" style="display: none" src="http://oflmtu502.bkt.clouddn.com/%E6%95%99%E8%82%B2%E8%A7%82-%E6%85%A2.mp3">
        </audio>
    </div>
</div>


@endsection
@section('script')
<script type="text/javascript">
var audio1 = document.getElementById('audio1');
function playPause(){
    if(audio1.paused){
    	audio1.play();
    	$('#play-img').attr('src','/images/share/audio-play.png');
    }else{
    	audio1.pause();
    	$('#play-img').attr('src','/images/share/audio-pause.png');
    }
}

$('#audio1').on('touchstart', function() {
	audio1.load()
})

</script>
   	
@endsection