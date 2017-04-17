@extends('layout.default')

@section('style')
<style>
    .mi_stat{font-size: 14px;}
    .mi_stat .mi_all{color:#ed6d11;padding-right: 10px;}
    .mi_stat .mi_cash{color:#ed6d11}
    .cash_a {display:block;color: red;margin-top:20px;}
    .cash_a button{padding: 16px 50px;background-color: #ff9900;border-radius: 21px;border: none;color: #fff;font-size: 16px;}

</style>
@endsection
@section('content')
    <div id="subject">
        <div id="main">
            <div class="my">
                <div class="my_integral">
                    <div class="mi_top">
                        <div class="mi_top_title">账户余额</div>
                        <div class="mi_top_score">￥{{$data->current_balance}}</div>
                        <div class="mi_stat">累计收益  <span class="mi_all">￥{{$data->balance}}</span> 待结算金额 <span class="mi_cash">￥{{ isset($data->cash_record[0]) ?  $data->cash_record[0]->cash_amount : 0.00 }}</span></div>

                        <a href="{{route('user.cash')}}" class="cash_a"><button>申请提现</button></a>

                        <a class="mi_top_help" href="{{route('article',['id'=>8])}}"><img src="{{asset('/images/my/mi_top_help.png')}}" alt="收益介绍"/></a>
                    </div>
                    <ul class="mi_list">
                        @foreach($data->balance_record as $item)
                            <li>
                                <a href="javascript:void(0);">
                                    <div class="mi_list_score @if($item->operate_type == 1) plus @else reduce @endif">
                                        @if($item->operate_type == 1)
                                            +
                                        @else
                                            -
                                        @endif
                                        {{$item->amount}}</div>
                                    <div class="mi_list_date">{{date('Y-m-d',strtotime($item->created_at))}}</div>
                                    <div class="mi_list_state">{{config('constants.user_balance_source')[$item->source]}}</div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
