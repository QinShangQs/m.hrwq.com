@extends('layout.default')
@section('content')
<div id="subject">
    <div id="main">
        <div class="share-love">
            <div class="banner"><img src="/images/share/love-bg.png" alt=""/></div>
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