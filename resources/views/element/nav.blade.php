<style>
	#nav {
	    padding-top: 0px;
		height: 43px;
	}
	#nav li {
		width:20%;
	}
	#nav li a {
		width: 2.5rem;
		height: 5rem;
    	margin-top: -2px;
	}

	.nav_2 {
		background: url(../images/public/nav_new_first.png) 50% top no-repeat;
    	background-size: 180%;
	}
	
	.selected .nav_2 {
		background: url(../images/public/nav_new_first_selected.png) 50%  top no-repeat;
    	background-size: 180%;
	}
	
	.nav_1 {
		background: url(../images/public/nav_new_third.png) 50%  top no-repeat;
    	background-size: 180%;
	}
	
	.selected .nav_1 {
		background: url(../images/public/nav_new_third_selected.png) 50%  top no-repeat;
    	background-size: 180%;
	}
	
	.nav_5 {
		background: url(../images/public/nav_new_my.png) 50%  top no-repeat;
    	background-size: 180%;
	}
	
	.selected .nav_5 {
		background: url(../images/public/nav_new_my_selected.png) 50%  top no-repeat;
    	background-size: 180%;
	}
	
	.nav_4 {
		background: url(../images/public/nav_new_se.png) 50%  top no-repeat;
    	background-size: 180%;
	}
	
	.selected .nav_4 {
		background: url(../images/public/nav_new_se_selected.png) 50%  top no-repeat;
    	background-size: 180%;
	}
	
	.nav_4 {
		background: url(../images/public/nav_new_se.png) 50%  top no-repeat;
    	background-size: 180%;
	}
	
  
	
	.nav-3 {
		background: url(../images/public/nav_center.png) 40% top no-repeat;
    	background-size: 120%;
	}
	
	  .nav-3-selected{
		background: url(../images/public/nav_center_close.png) 40% top no-repeat;
    	background-size: 120%;
	}
	
	.nav-show {
		position: fixed;
	    background-color: #fff;
	    bottom: 43px;
	    width: 100%;
	    padding-top: 1rem;
    	padding-bottom: 1rem;
    	display: none;
	}
	
	.nav-show img {
		width:1.156rem;
		margin-left: 1.25rem;
		margin-right:0.625rem;
	}
	
	.nav-show .title {
		font-size: 0.9375rem;
		color:#666666;
		margin-bottom: 0.4375rem
	}
	
	.nav-show .tip {
		font-size: 0.75rem;
		color:#999999;
		margin-bottom: 0.9375rem
	}
	
	.nav-show-bg {
			bottom: 43px;
		    position: fixed;
		    background-color: black;
		    width: 100%;
		    display: none;
		    height: 100%;
		    opacity: 0.3;
	}
	
</style>

<div class="nav-show-bg"></div>

<div class="nav-show">
	<table width='100%'>
		<tr onclick="location.href='/question?selected_tab=2'">
			<td valign="middle">
				<img src="/images/public/huati.png"/>
			<td>
			<td>
				<div class="title">发话题</div>
				<div class="tip">有什么想聊的，和全国家长们一起讨论!</div>
			<td>
		</tr>
		<tr onclick="location.href='/question?selected_tab=3'"> 
			<td valign="middle">
				<img src="/images/public/wenda.png"/>
			<td>
			<td>
				<div class="title">问大家</div>
				<div class="tip">写下问题，会有热心家长和专家老师帮你解答哦!</div>
			<td>
		</tr>
	</table>
</div>

<ul id="nav"><!--导航，所在li需添加类selected-->
    <li @if($selected_item == 'nav2')class="selected" @endif><a class="nav_2" href="{{route('vcourse')}}" title="首页"></a></li>
    <li @if($selected_item == 'nav4')class="selected" @endif><a class="nav_4" href="{{route('question')}}?selected_tab=2" title="家长圈"></a></li>
    <li style="margin-top: -1rem;"><a class="nav-3" style="width:3rem" href="javascript:;"></a></li>
    <li @if($selected_item == 'nav1')class="selected" @endif><a class="nav_1" href="{{ _get_telecast_link()}}" title="直播"></a></li>
    @if(!session('wechat_user'))
        <li @if($selected_item == 'nav5')class="selected" @endif><a class="nav_5" href="{{route('wechat.qrcode')}}" title="我的"></a></li>
    @else
        <li @if($selected_item == 'nav5')class="selected" @endif><a class="nav_5" href="{{route('user')}}" title="我的"></a></li>
    @endif
</ul>

<script>
	$(document).ready(function(){
		$('.nav-3').click(function(){
			$(".nav-show").stop();
			$(".nav-show-bg").stop();
			if($(this).hasClass('nav-3-selected')){
				$(this).removeClass('nav-3-selected');
			}else{
				$(this).addClass('nav-3-selected')
			}
			$(".nav-show").toggle();
			$(".nav-show-bg").toggle();
		});
	});
</script>