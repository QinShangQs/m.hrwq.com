@extends('layout.default')
@section('content')
    <div id="main">
        <div class="my">
            <div class="my_data_personal">
                <div class="mdp_top">
                    <div class="mdp_top_img"><img src="{{url($user->profileIcon)}}" alt=""/></div>
                    <div class="mdp_top_name">{{$user->nickname}}</div>
                    <a href="{{route('user.profile.edit')}}" class="mdp_top_edit"><img src="/images/my/mdp_top_edit.png" alt=""/></a>
                </div>
                <ul class="mdp_list">
                    <li><span>称呼</span>{{isset(config('constants.user_label')[$user->label])?config('constants.user_label')[$user->label]:'暂无'}}</li>
                    <li><span>真实姓名</span>{{$user->realname}}</li>
                    <li><span>年龄</span>{{$user->age}}</li>
                    <li><span>生日</span>{{$user->birth}}</li>
                    <li><span>所在城市</span>{{($user->c_province?$user->c_province->area_name:'') . ($user->c_city?$user->c_city->area_name:'') . ($user->c_district?$user->c_district->area_name:'')}}</li>
                    <li><span>孩子性别</span>{{isset(config('constants.user_sex')[$user->c_sex])?config('constants.user_sex')[$user->c_sex]:''}}</li>
                    <li><span>孩子生日</span>{{$user->c_birth}}</li>
                </ul>
                <ul class="mmo_list" style="margin-bottom:15px">
                    <li><a href="{{route('my.addresses')}}"><span><img src="/images/public/select_right.jpg" alt=""></span>我的地址</a></li>
                </ul>
            </div>
        </div>
    </div>
@endsection