@extends('layout.default')
@section('content')
<div id="subject">
    <div id="main">
        <div class="good_looking_list">
            <div class="dll_title">@if($type=='1')免费课程@else畅销课程@endif</div><!--内容随情况而定-->
            <ul class="dll_list">
                @if(count($vcourseList)>0)
                    @foreach ($vcourseList as $item)
                    <li>
                        <a href="{{route('vcourse.detail',['id'=>$item->id])}}">
                            <div class="gl_list2_xz">{{mb_substr($item->agency->agency_name,0,4)}}</div>
                            <div class="dll_list_img">
                            @if($item->cover)
                                <img src="{{ config('constants.admin_url').$item->cover}}" alt="" onerror="javascript:this.src='/images/error.jpg'"/>
                            @else
                                <img src="{{ config('qiniu.DOMAIN').$item->video_tran}}?vframe/jpg/offset/{{ config('qiniu.COVER_TIME')}}" alt="" onerror="javascript:this.src='/images/error.jpg'"/>
                            @endif
                            </div>
                            <div class="dll_list_title">{{ @str_limit($item->title,20) }}</div>
                            @if($type=='2')
                                <div class="dll_list_money">￥{{ $item->price }}</div>
                            @else
                                <div class="dll_list_money">免费</div>
                            @endif
                            <div class="dll_list_people">{{{ $item->view_cnt or 0 }}}人观看</div>
                        </a>
                    </li>
                    @endforeach
                @endif
            </ul>
            @if($vcourseList->hasMorePages())
            <div class="gl_list2_more">点击加载更多...</div>
            @endif
        </div>
    </div>
</div>
@endsection
@section('script')
<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">
$(document).ready(function(){
    wx.config(<?php echo $wx_js->config(array("onMenuShareAppMessage", "onMenuShareTimeline"),false) ?>);
            wx.ready(function(){
                wx.onMenuShareAppMessage({
                    title: '和润万青-让教育孩子变得简单', // 分享标题
                    desc: '爱中管教，互联网时代中国父母的必修课', // 分享描述
                    link: '{{route('vcourse')}}?from=singlemessage', // 分享链接
                    @if(@$carouselList[0]['image_url'])
                    imgUrl: '{{config('constants.admin_url').$carouselList[0]['image_url']}}', // 分享图标
                    @endif
                    type: '', // 分享类型,music、video或link，不填默认为link
                    dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                    success: function () { 
                        // 用户确认分享后执行的回调函数
                    },
                    cancel: function () { 
                        // 用户取消分享后执行的回调函数
                    }
                });
                wx.onMenuShareTimeline({
                    title: '和润万青-让教育孩子变得简单', // 分享标题
                    link: '{{route('vcourse')}}?from=singlemessage', // 分享链接
                    @if(@$carouselList[0]['image_url'])
                    imgUrl: '{{config('constants.admin_url').$carouselList[0]['image_url']}}', // 分享图标
                    @endif
                    success: function () {
                        // 用户确认分享后执行的回调函数
                    },
                    cancel: function () {
                        // 用户取消分享后执行的回调函数
                    }
                });

            });
    /*----------推荐课程加载更多----------*/
     var current_page = 1, last_page = '{{$vcourseList->lastPage()}}';
     $(".gl_list2_more").click(function(){//点击加载更多按钮
         if(last_page > current_page)
         {
             var sheight = $('.gl_list2_more').offset().top;
             current_page ++;
             $.ajax({
                 type: 'post',
                 url: '{{route('vcourse.more',['type'=>$type])}}',
                 data: {page:current_page},
                 dataType: 'json',
                 success: function (res) {
                    if(res)
                    {
                        var vcourse_data = res.data;
                        var recommend_ul_li ='';
                        $.each(vcourse_data,function(k,v){
                            recommend_ul_li += '<li>';
                            recommend_ul_li += '<a href="{{route('vcourse.detail')}}/'+ v.id+'">';
                            recommend_ul_li += '<div class="gl_list2_xz">'+v.agency.agency_name.substring(0,20)+'</div>';
                            if(v.cover==''||v.cover==null){
                                recommend_ul_li += '<div class="dll_list_img"><img src="{{ config("qiniu.DOMAIN")}}'+ v.video_tran+'?vframe/jpg/offset/{{ config("qiniu.COVER_TIME")}}" alt="" onerror="javascript:this.src=\'/images/error.jpg\'"/></div>';
                            }else{
                                recommend_ul_li += '<div class="dll_list_img"><img src="{{config('constants.admin_url')}}'+ v.cover+'" alt=""/></div>';
                            }
                            recommend_ul_li += '<div class="dll_list_title">'+ v.title.substring(0,20)+'</div>';


                            if(v.type=='1'){ 
                                recommend_ul_li += '<div class="dll_list_money">免费</div>';
                            }else{
                                recommend_ul_li += '<div class="dll_list_money">￥'+v.price+'</div>';
                            }
                            if (v.view_cnt==''||v.view_cnt==null) {v.view_cnt=0};
                            recommend_ul_li += '<div class="dll_list_people">'+v.view_cnt+'人观看</div>'
                            recommend_ul_li += '</a></li>';
                        })
                        if(recommend_ul_li!='')
                        {
                            $('.dll_list').append(recommend_ul_li);
                            $("html,body").animate({scrollTop:sheight}, 1000);
                        }
                    }
                 }
             });
             if(current_page >= last_page){
                 $(".gl_list2_more").hide();
             }
         }
     });
});
</script>
@endsection