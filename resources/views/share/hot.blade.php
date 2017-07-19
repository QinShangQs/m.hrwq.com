@extends('layout.default')
@section('content')

<style>

body{
	background-color: #fff
}

.play-container{
	position:absolute;
	top:12rem;
	z-index:10;
	display:flex;
	width:100%;
	justify-content: center;
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
        	<a href="{{route('article',['id'=>6])}}">
        		<img style="width:6.25rem;height:1.71rem;margin-top:1rem" src="/images/share/join-btn.png"/>
        	</a>
        </div>
        <audio id="audio1" preload="auto" loop="loop" style="display: none" src="http://oflmtu502.bkt.clouddn.com/%E9%9F%B3%E9%A2%91%20%E6%95%99%E8%82%B2%E8%A7%822.mp3">
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