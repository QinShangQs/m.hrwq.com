@extends('layout.default')
@section('content')
    <div id="subject">
        <div id="main">
            <div class="my">
                <div class="my_integral">
                    <div class="mi_top">
                        <div class="mi_top_title">当前和贝</div>
                        <div class="mi_top_score">{{$data->score}}<span>分</span></div>
                        <a class="mi_top_help" href="{{route('article',['id'=>3])}}"><img src="{{asset('/images/my/mi_top_help.png')}}" alt="和贝介绍"/></a>
                    </div>
                    <ul class="mi_list">
                        @foreach($data->user_point as $item)
                        <li>
                            <a href="javascript:void(0);">
                                <div class="mi_list_score @if($item->move_way == 1) plus @else reduce @endif">
                                    @if($item->move_way == 1)
                                        +
                                    @else
                                        -
                                    @endif
                                    {{$item->point_value}}</div>
                                <div class="mi_list_date">{{date('Y-m-d',strtotime($item->created_at))}}</div>
                                <div class="mi_list_state">{{config('constants.income_point_source')[$item->source]}}</div>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

<a class="lcd_evaluate lcd_evaluate_fa" style="background-color:#fff;background-image: url(/images/new/return.png);background-size: cover;" href="/">
	&nbsp;
</a>
@endsection
