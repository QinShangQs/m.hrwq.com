<ul id="nav"><!--导航，所在li需添加类selected-->
    <li @if($selected_item == 'nav2')class="selected" @endif><a class="nav_2" href="{{route('vcourse')}}" title="视频"></a></li>
    <?php //<li @if($selected_item == 'nav3')class="selected" @endif><a class="nav_3" href="{{route('opo')}}" title="壹家壹"></a></li>?>
    <li @if($selected_item == 'nav4')class="selected" @endif><a class="nav_4" href="{{route('question')}}?selected_tab=2" title="问答"></a></li>
    <li @if($selected_item == 'nav1')class="selected" @endif><a class="nav_1" href="{{route('course')}}" title="课程"></a></li>
    @if(!session('wechat_user'))
        <li @if($selected_item == 'nav5')class="selected" @endif><a class="nav_5" href="{{route('wechat.qrcode')}}" title="我的"></a></li>
    @else
        <li @if($selected_item == 'nav5')class="selected" @endif><a class="nav_5" href="{{route('user')}}" title="我的"></a></li>
    @endif
</ul>