// vanilla js ajax handler
var ajax = {};
ajax.x = function () {
    var xhr;
    if (typeof XMLHttpRequest !== 'undefined') {
        xhr = new XMLHttpRequest();
        xhr.withCredentials = true;
        return xhr;
    }
    var versions = [
        "MSXML2.XmlHttp.6.0",
        "MSXML2.XmlHttp.5.0",
        "MSXML2.XmlHttp.4.0",
        "MSXML2.XmlHttp.3.0",
        "MSXML2.XmlHttp.2.0",
        "Microsoft.XmlHttp"
    ];

    for (var i = 0; i < versions.length; i++) {
        try {
            xhr = new ActiveXObject(versions[i]);
            xhr.withCredentials = true;
            break;
        } catch (e) {
        }
    }
    return xhr;
};

ajax.send = function (url, callback, method, data, async) {
    if (async === undefined) {
        async = true;
    }
    var x = ajax.x();
    x.open(method, url, async);
    x.onreadystatechange = function () {
        if (x.readyState == 4) {
            callback(x.responseText)
        }
    };
    if (method == 'POST') {
        x.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    }
    x.send(data)
};

ajax.get = function (url, data, callback, async) {
    var query = [];
    for (var key in data) {
        query.push(encodeURIComponent(key) + '=' + encodeURIComponent(data[key]));
    }
    ajax.send(url + (query.length ? '?' + query.join('&') : ''), callback, 'GET', null, async)
};

ajax.post = function (url, data, callback, async) {
    var query = [];
    for (var key in data) {
        query.push(encodeURIComponent(key) + '=' + encodeURIComponent(data[key]));
    }
    ajax.send(url, callback, 'POST', query.join('&'), async)
};

// turnstile function
window.turnstile = (function () {
    function Event(link){
        // ajax call to create / add a view for this page
        ajax.post('https://turnstile.me/event', {"url": link}, function(data) {
           console.log(data);
        });


    }

    var turnstile = {
        event: function(type){
            return new Event(window.location.toString());
        },

        doLogin: function(url) {
            var IFRAME_PARENT_EL = '#TB_ajaxContent';

            function showLogin() {
                jQuery('<iframe />', {
                    id: 'ts-login-frame',
                    title: "Turnstile Login",
                    marginheight: "0",
                    marginwidth: "0",
                    src: url,
                    style: "width:100%;height:100%;",
                }).appendTo(IFRAME_PARENT_EL);
            }

            jQuery(document).ready(function() {
                if (jQuery('.btn-turnstile-readmore').length) {
                    var _tb_show = tb_show;

                    tb_show = function() {
                        _tb_show.apply(_tb_show, arguments);
                        showLogin();
                    };
                }
            });
        }
    };

    return turnstile;
}());

turnstile.event();
