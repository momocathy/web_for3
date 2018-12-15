(function ($) {
    var st = {};
    //验证码URL添加随机数
    st.captcha = captcha;
    function captcha(url) {
        var path = url.split('?');
        return path[0] + '?' + Math.random() * 10000;
    }   
    window.ST = st;
})(jQuery)


