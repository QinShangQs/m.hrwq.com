
window.hrwqAd = {
    ad_image_url: null,
    ad_image_link: null,
    old_video_url: null,
    ad_video_url: null,
    ad_video_link: null,
    is_ad_video: false,
    is_first: true,
    video_ad_timer: null,
    init: function (old_video_url, ad_image_url, ad_image_link, ad_video_url, ad_video_link) {
        var that = this;
        that.old_video_url = old_video_url;

        that.ad_image_url = ad_image_url;
        that.ad_image_link = ad_image_link;
        that.ad_video_url = ad_video_url;
        that.ad_video_link = ad_video_link;
        that.is_ad_video = that.ad_video_url !== null ? true : false;

        if (that.ad_image_url !== null) {
            $('#ad-image').attr('src', that.ad_image_url);
        }

        if (that.is_ad_video === true) {
            $('.video-ad-timer .close').click(function () {
                $('.video-ad-timer').hide();
                that.is_ad_video = false;
                that.is_first = false;
                that.playVideo();
            });
            $('.video-ad-detail').click(function () {
                location.href = that.ad_video_link;
            });
        }
    },
    showImageAd: function () {
        if (this.is_ad_video === false && this.ad_image_url !== null) {
            $('.image-ad').show();
        }
    },
    hideImageAd: function () {
        $('.image-ad').hide();
    },
    toImageLink: function () {
        location.href = this.ad_image_link;
    },
    getVideoDuration: function (url) {
        var seconds = 0;
        $.ajax({
            url: url + "?avinfo",
            async: false
        }).done(function (info) {
            seconds = parseInt(info.format.duration);
        });
        return seconds;
    },
    videoAdTimer: function () {
        var that = this;
        if (that.video_ad_timer === null) {
            that.video_ad_timer = window.setInterval(function () {
                if ($('.video-ad-timer .seconds').text() == '') {
                    $('.video-ad-timer .seconds').text(that.getVideoDuration(that.ad_video_url));
                } else if ($('.video-ad-timer .seconds').text() == '1') {
                    $('.video-ad-timer').hide();
                    window.clearInterval(that.video_ad_timer);
                } else {
                    $('.video-ad-timer .seconds').text(parseInt($('.video-ad-timer .seconds').text()) - 1);
                }
            }, 1000);
        }
    },
    getVideoType: function () {
        $.ajaxSetup({
            headers: ''
        });
        var type = '';
        $.ajax({
            url: this.getVideoLink() + "?stat",
            async: false
        }).done(function (info) {
            type = info.mimeType;
            if (type === 'application/x-mpegurl') {
                type = 'application/x-mpegURL';
            }
        });

        return type;
    },
    getVideoLink: function () {
        if (this.is_ad_video === true) {
            return this.ad_video_url;
        }
        return this.old_video_url;
    },
    realPlay: function () {
        var that = this;
        var player = videojs('video-embed');
        player.src({type: that.getVideoType(), src: that.getVideoLink()});
        console.log('browserOS is ' + browserOS());
        if (browserOS() !== 'android') {
            player.load();
            player.play();

            waitingPub = Popup.init({
                popTitle: "",
                popHtml: "正在加载，请稍后...",
                popFlash: {
                    flashSwitch: true,
                    flashTime: 3000
                }
            });
        }
    },
    playVideo: function () {
        var that = this;
        var vLink = that.getVideoLink();
        if (browserOS() === 'pc') {
            var player = videojs('video-embed');
            player.src({type: that.getVideoType(), src: vLink});
            player.load();
            player.play();
        } else {
            if (that.is_first === true) {
                document.addEventListener("WeixinJSBridgeReady", function () {
                    that.realPlay();
                }, true);
            } else {
                that.realPlay();
            }
        }
    },
    videoPlayEvent: function () {
        var that = this;
        that.hideImageAd();
        if (that.is_ad_video === true) {
            $('.vip-status-show').hide();
            $('.video-ad-timer').show();
            $('.video-ad-detail').show();
            that.videoAdTimer();
        } else {
            $('.vip-status-show').show();
            $('.video-ad-timer').hide();
            $('.video-ad-detail').hide();
        }
    },
    videoPauseEvent: function () {
        var that = this;
        that.showImageAd();
        if (that.video_ad_timer) {
            window.clearInterval(that.video_ad_timer);
            that.video_ad_timer = null;
        }
    },
    videoEndedEvent: function () {
        this.hideImageAd();
        if (this.is_ad_video === true) {
            this.is_ad_video = false;
            this.is_first = false;
            this.playVideo();
            return true;
        }
        return false;
    }

};

