@extends('layout.default')
@section('content')
<style>
::-webkit-scrollbar {width: 5px;height: 5px;}                        
::-webkit-scrollbar-track,::-webkit-scrollbar-thumb {border-radius: 999px;border:0px solid transparent;}
::-webkit-scrollbar-track {box-shadow: 1px 1px 5px rgba(100,100,100,.2) inset;}
::-webkit-scrollbar-thumb {min-height: 20px;background-clip: content-box;box-shadow: 0 0 0 5px rgba(100,100,100,.5) inset;}
::-webkit-scrollbar-corner {background:;transparent}-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
</style>

<div id="qrcode" style="display:none;margin-top:15px;"></div>
<canvas id="namevas" width="200px" height="15px" style="display:none">
您的浏览器不支持canvas标签。
</canvas>
<img id="vasimg" style="display:none" />

<img id="lovebg" style="display:none" src="{{$lovebg64}}" >
<div id="subject">
    <div id="main">
        <div class="share-love">
        	<div style="text-align:center;color:black;font-size:0.9rem;line-height:2.2rem">长按图片保存到相册</div>
            <div class="banner" style="margin-bottom:1rem"><img id="banner" src="/images/share/love-bg.jpg" alt=""/></div>
            <div class="footer" >
            	<div class="tip">
            		<span class="title">&nbsp;&nbsp;&nbsp;&nbsp;</span>
            		<span class="detail">如何获取7天会员  <span class="forward"></span></span>
            	</div>
            	<div class="content" style="display: none;height: 15rem;overflow-y: scroll;">
            		<p>
            			<b>1.我居然是爱心大使？</b><br/>
            			没错，您已经是和润万青父母学院的爱心大使啦。爱心大使肩负“完善自我，帮助他人”的美好使命。我们时刻以你为荣哦。
            		</p>
            		
            		<p>
            			<b>2.如何免费获得7天会员奖励？</b><br/>
            			长按图片即可保存您的专属二维码图片至手机相册。如果您想邀请他人加入好父母学院，通过微信将您的二维码图片发送给他，让他长按识别二维码，按照提示操作完成注册即可加入。
            		</p>
            		<p>
            			<b>3.成为爱心大使的好处是？</b><br/>
            			当您朋友扫描您的二维码成功注册父母学院后，您和对方都将获得7天的会员资格奖励。奖励期间，您可以免费收听和润万青父母学院所有完整课程。和会员奖励可累计叠加，您每成功邀请一个好友注册父母学院，您都将获得7天和会员奖励。
<br/>分享教子好知识本身就快乐：将自己教育孩子的体悟和充满了期望的二维码图片分享到您的社交圈，帮助身边的人成为更智慧的父母，让更多孩子在学习型家庭中收获美好人生。
            			
            		</p>
            	</div>
            </div>
        </div>
    </div>
</div>



@endsection
@section('script')
	<script type="text/javascript" src="/js/qrcode.min.js"></script>
	<script type="text/javascript">
	$(document).ready(function(){
		var qrcode = new QRCode(document.getElementById("qrcode"), {
			text: "{{ route('share.hot',['id'=> $data['id'] ] ) }}",
			width : 155,
			height : 155
		});

		(function(){
			var canvas = document.getElementById("namevas");
			var ctx = canvas.getContext("2d");
			ctx.fillStyle = "#000000";//海报名称文字颜色
			ctx.stroke();
			ctx.font="12px Heiti SC";
			ctx.textAlign = 'center';
			ctx.fillText("我是{{$data['nickname']}}", 100, 12);
			$("#vasimg").attr('src', canvas.toDataURL("image/png")); 
		})();

		setTimeout(function(){
			var c = document.createElement('canvas');
			var ctx = c.getContext('2d');
			
			c.width = 750;
			c.height = 1344;
			ctx.rect(0,0,c.width,c.height);
			ctx.fillStyle='#fff';//画布填充颜色
			ctx.fill();

			var img = new Image();
			img.src = $('#lovebg').attr('src');
			ctx.drawImage(img,0,0,c.width,c.height);
			var img2 = new Image();
			img2.src = $("#qrcode img").eq(0).attr('src');
			ctx.drawImage(img2,295,855,155,155);
			var img3 = new Image();
			img3.src = $("#vasimg").attr('src');
			ctx.drawImage(img3,150,1062,450,35);

			var finalSrc = c.toDataURL("image/jpeg");
			$("#banner").attr('src', finalSrc) ;
		},1000);
	});
	</script>
   	<script type="text/javascript">
		$(function(){
			$(".share-love .footer .detail").click(function(){
				if($(".share-love .footer .content").css('display') == 'none'){
					$(".share-love .footer .content").show();
					$(".share-love .footer .forward").addClass('forward-selected');
				}else{
					$(".share-love .footer .content").hide();
					$(".share-love .footer .forward").removeClass('forward-selected');
				}
			});
		});
	</script>
@endsection