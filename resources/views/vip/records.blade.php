@extends('layout.default')
@section('content')
    <div id="subject">
        <div id="main">
            <div class="my">
            	<div class="my-records">
            		会员状态
            	</div>
            	<div class="my-records-list">
            		@foreach($data->user_point_vip as $item)
            		<div class="item" >
            			<table width="100%">
            				<tr>
            					<td>{{config('constants.vip_point_source')[$item->source]}}</td>
            					<td>{{date('Y-m-d',strtotime($item->created_at))}}</td>
            					<td>+ {{$item->point_value}}天</td>
            				</tr>
            			</table>
            		</div>
            		@endforeach
            	</div>

            </div>
        </div>
    </div>
@endsection
