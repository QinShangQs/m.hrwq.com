@extends('layout.default')
@section('content')
<link rel="stylesheet" href="/css/partner-card.css"/>

<div class="card-body">
    <div class="banner">
        <img src="/images/partner/banner.png" />
        <div class="change"></div>
    </div>
    
    <div class="info-content">
        <div class="name-info">
            <div class="author">
                <img src="http://dev.m.hrwq.com/uploads/profileIcon/20161029/ot3XZtzWe3i6nZVo4XrDmvLB4zAA.jpg"/>
                <div class="desc">
                    <div class="name">包老师</div>
                    <div class="cityname">北京市朝阳区合伙人</div>
                </div>
            </div>
            
            <div class="qrcode">
                <img src="/images/partner/qrcode-logo.png"/>
                <div class="desc">二维码</div>
            </div> 
        </div>
        
        <div class='items'>
            <div class='item'>
                <img src='/images/partner/phone.png' />
                <div class='remark' >
                    <div class='title'>电话</div>
                    <div class='name'>188888888888888</div>
                </div>
            </div>
            <div class='item'>
                <img src='/images/partner/wechat.png' />
                <div class='remark' >
                    <div class='title'>微信</div>
                    <div class='name'>188888888888888</div>
                </div>
            </div>
            <div class='item'>
                <img src='/images/partner/msg.png' />
                <div class='remark' >
                    <div class='title'>邮箱</div>
                    <div class='name'>abccsds@qq.com</div>
                </div>
            </div>
            <div class='item'>
                <img src='/images/partner/pointer.png' />
                <div class='remark' >
                    <div class='title'>地址</div>
                    <div class='name'>北京市朝阳区旱河路93号路南</div>
                </div>
            </div>
            <div class='item'>
                <img src='/images/partner/ie.png' />
                <div class='remark' >
                    <div class='title'>网址</div>
                    <div class='name'>www.hreq.com</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class='mid-content'>
        <div class='show'>
            <div>点击查看更多信息</div>
            <img src='/images/partner/more.png' />
        </div>
    </div>
    
    <div class='last-content'>
        <div class='item'>
            <div class='title'>
                <img src='/images/partner/left-line.png'/>
                <span>简介</span>
            </div>
            <div class='tcont'>
                <p>
                    和润万青（北京）教育科技有限公司，专注华人家庭教育和青少年成长教育15年，是由华人家庭教育领域唯一的父子专家——全国十佳教育公益人物贾容韬老师、北京师范大学心理学硕士贾语凡老师共同创立。
                <br/>全国免费咨询电话：400-6363-555
                </p>
            </div>
        </div>
        <div class='item'>
            <div class='title'>
                <img src='/images/partner/left-line.png'/>
                <span>照片</span>
            </div>
            <div class='tcont'>
                <div class='pics'>
                    <img src='/images/partner/pic-it.png'/>
                    <img src='/images/partner/pic-it.png'/>
                    <img src='/images/partner/pic-it.png'/>
                    <img src='/images/partner/pic-it.png'/>
                    <img src='/images/partner/pic-it.png'/>
                </div>
            </div>
        </div>
        <div class='item'>
            <div class='title'>
                <img src='/images/partner/left-line.png'/>
                <span>视频</span>
            </div>
            <div class='tcont'>
                <div class='video' >
                    <img src='/images/partner/play.png'/>
                </div>
            </div>
        </div>
        
        <img class='save' src='/images/partner/to-learn.png'/>
    </div>
    
</div>

@endsection
@section('script')
    <!-- 提交信息 -->
    <script>

    </script>
@endsection