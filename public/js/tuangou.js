
window.tuangouTimer = {
    ended_at: 0,
    left: {day: 0, hour: 0, minite: 0, second: 0, value: 0},
    dom: null,
    timer: null,
    draw_style: 0,
    init: function (php_timestamp, selector, draw_style) {
        this.ended_at = parseInt(php_timestamp);
        this.dom = $(selector);
        this.draw_style = draw_style;
        this.start();
    },
    computerLeft: function () {
        var now = parseInt((new Date().getTime()) / 1000);
        var leftSeconds = (this.ended_at - now);
        this.left.day = Math.floor(leftSeconds / (24 * 60 * 60));
        this.left.hour = Math.floor(leftSeconds / (60 * 60)) - this.left.day * 24;
        this.left.minite = Math.floor(leftSeconds / 60 - this.left.day * 24 * 60  - this.left.hour * 60);
        this.left.second = leftSeconds - this.left.day * 24 * 60 * 60 - this.left.hour * 60 * 60 - this.left.minite * 60;
        this.left.value = leftSeconds;
    },
    drawHtml: function () {
        if (this.left.value <= 0) {
            this.dom.html('拼团活动已结束');
            return;
        }
        var html = "距离拼团结束时间还有" + this.left.day + "天"
                + this.left.hour + '时'
                + this.left.minite + '分'
                + this.left.second + '秒';
        if (this.draw_style == 1) {
            html = "剩余时间 <span class='tuantime'>" + this.left.day + "</span> 天"
                    + "<span class='tuantime'>" + this.left.hour + "</span> 时"
                    + "<span class='tuantime'>" + this.left.minite + "</span> 分"
                    + "<span class='tuantime'>" + this.left.second + "</span> 秒";
        }
        this.dom.html(html);
    },
    start: function () {
        var that = this;
        if(that.timer === null){
            that.timer = window.setInterval(function () {
                that.computerLeft();
                if (that.left.value <= 0) {
                    window.clearInterval(that.timer);
                }
                that.drawHtml();
            }, 1000);
        }
    }

};

