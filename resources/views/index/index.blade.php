<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>消费者促销活动</title>
    <link rel="stylesheet" href="{{ asset('common/css/normalize.min.css') }}">
    <link rel="stylesheet" href="{{ asset('common/css/swiper.min.css') }}">
    <link rel="stylesheet" href="{{ asset('common/css/public.css') }}">
    <link rel="stylesheet" href="{{ asset('index/css/index.css') }}">
</head>
<body>
<header><div style="border: 0px solid red;margin-left: 100%;height: 100%;"></div></header>
<!--时间-->
<div class="date">
    <p class="p1"><time id="time">00:00:00</time></p>
    <p class="p2"><span id="date">2018年1月1号</span><span id="day">星期一</span></p>
</div>

<!--打卡-->
<div class="clock">
    <a class="word_in" href="{{ url('index/activity/clockIn') }}">上班打卡</a>
    <a class="word_out" href="{{ url('index/activity/clockOut') }}">下班打卡</a>
</div>
<div style="clear: both;"></div>

<div class="clock-in" style="width: 80%;margin: auto;margin-top: 20%;" onclick="logout()">
    <button class="btn" style="background-color: red;color: #fff;width: 100%;">退出登录</button>
</div>

<script src="{{ asset('common/js/jquery.min.js') }}"></script>
<script src="{{ asset('common/js/swiper.min.js') }}"></script>
<script src="{{ asset('common/js/public.js') }}"></script>
<script>
    $(function () {
        var swiper = new Swiper('.swiper-container', {
            pagination: {
                el: '.swiper-pagination'
            }
        });

        time('年','月','号');

        var login_heigt = parseFloat($(window).height());
        $('body').css('height', login_heigt - 20);
    });

    function logout() {
        window.location.href = '{{ url('index/logout') }}';
    }
</script>
</body>
</html>