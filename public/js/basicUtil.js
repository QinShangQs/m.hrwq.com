//browser compatibility 
if (!String.prototype.trim) {
    String.prototype.trim = function() {
        var s = this;
        while (s.charAt(0) === ' ') {
            s = s.substr(1, s.length);
        }
        while (s.charAt(s.length - 1) === ' ') {
            s = s.substr(0, s.length - 1);
        }
        return s;
    };
}

if (!Array.prototype.indexOf) {
    Array.prototype.indexOf = function(elt /*, from*/) {
        var len = this.length;

        var from = Number(arguments[1]) || 0;
        from = (from < 0)
                ? Math.ceil(from)
                : Math.floor(from);
        if (from < 0)
            from += len;

        for (; from < len; from++) {
            if (from in this && this[from] === elt)
                return from;
        }
        return -1;
    };
}

if (!String.prototype.startsWith) {
    String.prototype.startsWith = function(subString) {
        var s = this;
        if (s.indexOf(subString) === 0) {
            return true;
        } else {
            return false;
        }
    };
}

var basicUtil = {};
basicUtil.string = {
    getNumStringWithComma: function(num) {
        var numS = num + '';
        var formatedString = '';
        for (var i = 0; i < numS.length; i = i + 3) {
            formatedString = numS.substring(numS.length - i - 3, numS.length - i) + ',' + formatedString;
        }
        formatedString = formatedString.substring(0, formatedString.length - 1);
        return formatedString;
    }
};

basicUtil.cookie = {
    setCookie: function(name, value, expires, path, domain, secure)
    {
        // set time, it's in milliseconds
        var today = new Date();
        today.setTime(today.getTime());
        var expires_date = new Date(today.getTime() + (expires * 1000));
        var cookieVal = name + "=" + escape(value) +
                ((expires) ? "; expires=" + expires_date.toGMTString() : "") +
                ((path) ? "; path=" + path : "; path=/") +
                ((domain) ? "; domain=" + domain : "; domain=" + basicUtil.url.getDomain()) +
                ((secure) ? "; secure" : "");
        document.cookie = cookieVal;
        return cookieVal;
    },
    getCookie: function(name) {
        // first we'll split this cookie up into name/value pairs
        // note: document.cookie only returns name=value, not the other components
        var a_all_cookies = document.cookie.split(';');
        var a_temp_cookie = '';
        var cookie_name = '';
        var cookie_value = '';
        var b_cookie_found = false; // set boolean t/f default f

        for (i = 0; i < a_all_cookies.length; i++)
        {
            // now we'll split apart each name=value pair
            a_temp_cookie = a_all_cookies[i].split('=');


            // and trim left/right whitespace while we're at it
            cookie_name = a_temp_cookie[0].replace(/^\s+|\s+$/g, '');

            // if the extracted name matches passed check_name
            if (cookie_name === name)
            {
                b_cookie_found = true;
                // we need to handle case where cookie has no value but exists (no = sign, that is):
                if (a_temp_cookie.length > 1)
                {
                    cookie_value = unescape(a_temp_cookie[1].replace(/^\s+|\s+$/g, ''));
                }
                // note that in cases where cookie is initialized but no value, null is returned
                return cookie_value;
                break;
            }
            a_temp_cookie = null;
            cookie_name = '';
        }
        if (!b_cookie_found)
        {
            return null;
        }
    },
    getCookieInJson: function(name) {
        var cookieVal = this.getCookie(name);
        return eval('(' + decodeURIComponent(cookieVal) + ')');
    },
    delCookie: function(name, path, domain)
    {
        var cookieVal = name + "=" + ((path) ? ";path=" + path : ";path=/") + ((domain) ? ";domain=" + domain : "; domain=" + basicUtil.url.getDomain()) + ";expires=Thu, 01-Jan-1970 00:00:01 GMT";
        document.cookie = cookieVal;
        return cookieVal;
    }
};

basicUtil.url = {
    getUrlArray: function() {
        var url = window.location.href;
        var array = url.split('#');
        url = array[0];
        var array = url.split('?');
        var path = array[0];
        while (true) {
            var path2 = path.replace('//', '/');
            if (path === path2) {
                break;
            }
            path = path2;
        }
        var urlArray = path.split('/');
        return urlArray;
    },
    getQueryArray: function() {
        var url = window.location.href;
        var array = url.split('?');
        var query = array[1];
        if (typeof(query) === 'undefined') {
            return [];
        }
        var queryArray = query.split('&');
        return queryArray;
    },
    getQueryKey: function(key) {
        var queryArray = this.getQueryArray();
        var length = queryArray.length;
        if (length === 0) {
            return null;
        }
        var i = 0;
        var subArray;
        for (i = 0; i < length; i++) {
            subArray = queryArray[i].split('=');
            if (subArray[0] === key) {
                return subArray[1];
            }
        }
        return null;
    },
    getDomain: function() {
        var array = this.getUrlArray();
        return array[1];
    },
    getQueryDir: function(index) {
        var array = this.getUrlArray();
        if (!index) {
            var length = array.length;
            var i = 0;
            var queryUrl = '';
            for (i = 2; i < length; i++) {
                queryUrl += '/' + array[i];
            }
            return queryUrl;
        } else {
            return array[index + 2];
        }
    }
};

basicUtil.toolkit = {
    trace: function(url) {
        setTimeout(function() {
            var traceImg123423453456 = new Image();
            traceImg123423453456.src = url;
        }, 1000);
    },
    traceImmediately: function(url) {
        var traceImg123423453456 = new Image();
        traceImg123423453456.src = url;
    },
    form: function(action, inputs, method, target) {
        method = (method ? method : 'post');
        target = (target ? target : 'target');
        inputs = (inputs ? inputs : []);
        var form = document.createElement('form');
        form.action = action;
        form.target = target;
        form.method = method;
        for (var i in inputs) {
            var input = document.createElement('input');
            input.name = inputs[i][0];
            input.value = inputs[i][1];
            form.appendChild(input);
        }
        document.body.appendChild(form);
        return form;
    },
    inArray: function(needle, array) {
        for (var i in array) {
            if (needle === array[i]) {
                return parseInt(i);
            }
        }
        return -1;
    },
    traceBeforeOpenNewWindow: function(traceUrl, newWindowUrl) {
        var traceImg123423453456 = new Image();
        traceImg123423453456.onload = function() {
            window.open(newWindowUrl);
        };
//        traceImg123423453456.onabort = function() {
//            window.open(newWindowUrl);
//        };
        traceImg123423453456.onerror = function() {
            window.open(newWindowUrl);
        };
        traceImg123423453456.src = traceUrl;
        return traceUrl;
    },
    copyToClipboard: function(txt) {
        if (window.clipboardData) {
            window.clipboardData.setData("Text", txt);
            alert('复制成功');
        } else {
            if (navigator.userAgent.indexOf("Opera") != -1) {
                alert('您的浏览器不支持复制，请用IE来完成复制!');
                return false;
            } else {
                if (window.netscape) {
                    try {
                        netscape.security.PrivilegeManager.enablePrivilege("UniversalXPConnect");
                    } catch (e) {
                        alert("您的浏览器不支持复制，请用IE来完成复制!");
                        return false;
                    }
                    var clip = Components.classes["@mozilla.org/widget/clipboard;1"].createInstance(Components.interfaces.nsIClipboard);
                    if (!clip) {
                        return false;
                    }
                    var trans = Components.classes["@mozilla.org/widget/transferable;1"].createInstance(Components.interfaces.nsITransferable);
                    if (!trans) {
                        return false;
                    }
                    trans.addDataFlavor('text/unicode');
                    var str = new Object();
                    var len = new Object();
                    var str = Components.classes["@mozilla.org/supports-string;1"].createInstance(Components.interfaces.nsISupportsString);
                    var copytext = txt;
                    str.data = copytext;
                    trans.setTransferData("text/unicode", str, copytext.length * 2);
                    var clipid = Components.interfaces.nsIClipboard;
                    if (!clip) {
                        return false;
                    }
                    clip.setData(trans, null, clipid.kGlobalClipboard);
                    return false;
                } else {
                    alert('您的浏览器不支持复制，请用IE来完成复制!');
                }
            }
        }
    }
};

basicUtil.browser = {
    getBrowserInfo: function() {
        var Sys = {};
        var ua = navigator.userAgent.toLowerCase();
        var s;
        (s = ua.match(/msie ([\d.]+)/)) ? Sys.ie = s[1] :
                (s = ua.match(/firefox\/([\d.]+)/)) ? Sys.firefox = s[1] :
                (s = ua.match(/chrome\/([\d.]+)/)) ? Sys.chrome = s[1] :
                (s = ua.match(/opera.([\d.]+)/)) ? Sys.opera = s[1] :
                (s = ua.match(/version\/([\d.]+).*safari/)) ? Sys.safari = s[1] : 0;
        var info = {};
        //以下进行测试  
        if (Sys.ie) {
            info.app = 'ie';
            info.version = Sys.ie;
        }
        else if (Sys.firefox) {
            info.app = 'ff';
            info.version = Sys.firefox;
        }
        else if (Sys.chrome) {
            info.app = 'chrome';
            info.version = Sys.chrome;
        }
        else if (Sys.opera) {
            info.app = 'opera';
            info.version = Sys.opera;
        }
        else if (Sys.safari) {
            info.app = 'safari';
            info.version = Sys.safari;
        }
        return info;
    }
};