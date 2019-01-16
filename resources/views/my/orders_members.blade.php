@extends('layout.default')
@section('content')
<div id="subject">
    <div id="main">
        <div class="my_order">
            <span class="left" onclick="history.back()">
                <img src="/images/public/to-left.png" 
                     style="height: 20px;margin-top: 12px;padding-left: 10px;box-sizing: border-box;"/>
            </span>
            <div class="mo_top" style="color: #ee7930;font-size: 18px">团队列表</div>
            <ul class="mo_list" style="margin-top: 1px;padding: 0 1.25rem;padding-top:14px;background-color: #fff;box-sizing: border-box;font-size: .80rem">
                @foreach($team_members as $member)
                <li style="height:3.93rem;margin-top: 1px;display: flex;flex-direction: row;align-items: center ;border-bottom: 1px solid #f7f7f7;">
                    <div style="margin-right: 1rem;position: relative">
                        <img src="{{$member['profileIcon']}}" style="width: 3rem; border-radius: 100%"/>
                        @if($member['member_type'] == 1)
                            <img src='/images/order/king-head.png' style='position: absolute; right: -.3rem;top: -.7rem;'/>
                        @endif
                    </div>
                    <div style="line-height: 1.25rem">
                        <div style='font-size: .88rem'>
                            {{$member['nickname']}}
                            @if($member['member_type'] == 1)
                            <span style='background-color: #ee7930;color:#fff;padding: 0 .2rem;box-sizing: border-box;
                                    border-radius: 10%;font-size: .66rem;'>
                                发起人
                            </span>
                            @endif
                        </div>
                        <div style="color: #666">参团时间：{{$member['join_time']}}</div>
                    </div>
                </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endsection

@section('script')

@endsection