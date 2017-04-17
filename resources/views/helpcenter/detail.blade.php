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
			<div class="my_help_details">
				<dl class="my_help_details_list">
					<dt>{{ $data->title }}</dt>
					<dd >
						{!! $data->content !!}
					</dd>
				</dl>
			</div>
        </div>
    </div>
</div>
@endsection