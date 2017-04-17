@extends('layout.default')
@section('style')
<style type="text/css">
    img{width: 100%}
</style>
@endsection
@section('content')
    <div id="subject">
		<div id="main">
	    	<div class="my">
				<div class="my_help">
					<div class="my_help_title">问题知识列表</div>
					<ul class="my_help_list">
					@foreach ($data as $val)
						<li><a href="{{route('article.helpcenterdetail',['id'=>$val->id])}}"><span><img src="/images/public/select_right.jpg" alt=""/></span>{{ $val->title }}</a></li>
					@endforeach	
					</ul>
				</div>
	        </div>
	    </div>
	</div>
@endsection