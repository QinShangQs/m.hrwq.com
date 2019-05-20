@extends('layout.default')
@section('content')
    <div id="subject">
        <div id="main">
            <div class="my">
            	<div class="my-records">
            		直播码列表
            	</div>
            	<div class="my-records-list">
            		@foreach($tvcodes as $item)
            		<div class="item" >
            			<table width="100%">
                                    <tr onclick="location.href='{{$item->code}}'">
            					<td>赠送直播码</td>
            					<td>{{date('Y-m-d',strtotime($item->updated_at))}}</td>
            					<td>+ 365天&nbsp;点击领取</td>
            				</tr>
            			</table>
            		</div>
            		@endforeach
            	</div>

            </div>
        </div>
    </div>
@endsection
