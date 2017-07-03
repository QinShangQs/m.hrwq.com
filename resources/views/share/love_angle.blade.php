@extends('layout.default')
@section('content')
<div id="qrcode" style="display:none;margin-top:15px;"></div>
<canvas id="namevas" width="200px" height="15px" style="display:none">
您的浏览器不支持canvas标签。
</canvas>
<img id="vasimg" style="display:none" />

<img id="lovebg" style="display:none" src="{{$lovebg64}}" >
<div id="subject">
    <div id="main">
        <div class="share-love">
        	<div style="text-align:center;color:black;font-size:1.05rem;line-height:2.2rem">长按图片保存到相册</div>
            <div class="banner"><img id="banner" src="/images/share/love-bg.jpg" alt=""/></div>
            <div class="footer">
            	<div class="tip">
            		<span class="title">爱心大使二维码海报使用指南</span>
            		<span class="detail">了解详情  <span class="forward"></span></span>
            	</div>
            	<div class="content" style="display: none">
            		<p>
            			<b>1、我居然是爱心大使？</b><br/>
            			没错，您已经是和润好父母学院的爱心大使了。爱心大使肩负“完善自我，帮助他人”的美好使命。我们时刻以你为荣哦。
            		</p>
            		<p>
            			<b>2、怎么使用您的专属爱心大使二维码呢？</b><br/>
            			长按图片即可保存您的专属二维码图片至手机相册。如果您想邀请他人加入好父母学院，通过微信将您的二维码图片发送给他，让他长按识别二维码，按提示操作即可加入。
            		</p>
            		<p>
            			<b>3、成为爱心大使的好处是？</b>
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
			ctx.strokeStyle = "#ff6000";
			ctx.stroke();
			ctx.font="18px";
			ctx.textAlign = 'Center';
			ctx.strokeText("我是{{$data['nickname']}}", 50, 13);
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
			ctx.drawImage(img2,300,860,155,155);
			var img3 = new Image();
			img3.src = $("#vasimg").attr('src');
			ctx.drawImage(img3,235,1060,400,30);

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