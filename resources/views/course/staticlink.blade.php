@extends('layout.default')
@section('content')
<div>{!! $carousel->redirect_content !!}</div>
@endsection

@section('script')
<script type="text/javascript">
	$(document).ready(function(){
		if(location.href.indexOf('staticlink/29') != -1){
			$('img').css('width','100%');
		}
	});
</script>
@endsection