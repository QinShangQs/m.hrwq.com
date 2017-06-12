@extends('layout.default')
@section('content')
    <div id="subject">
        <div id="main">
            <div class="my">
                <div class="my_integral">
                    <div class="mi_top">
                        <div class="mi_top_title">和会员有效期</div>
                        <div class="mi_top_score">{{computer_vip_left_day($data['vip_left_day'])}}<span>天</span></div>
                        <a class="mi_top_help" href="{{route('article',['id'=>6])}}"><img src="{{asset('/images/my/mi_top_help.png')}}" alt="和会员介绍"/></a>
                    </div>
                    <ul class="mi_list">
                        @foreach($data->user_point_vip as $item)
                        <li>
                            <a href="javascript:void(0);">
                                <div class="mi_list_score plus">
                                    
                                        +
                                    
                                    {{$item->point_value}}</div>
                                <div class="mi_list_date">{{date('Y-m-d',strtotime($item->created_at))}}</div>
                                <div class="mi_list_state">{{config('constants.vip_point_source')[$item->source]}}</div>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
