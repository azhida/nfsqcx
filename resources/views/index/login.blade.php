<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>登陆</title>
    <link rel="stylesheet" href="{{ asset('common/css/normalize.min.css') }}">
    <link rel="stylesheet" href="{{ asset('common/css/swiper.min.css') }}">
    <link rel="stylesheet" href="{{ asset('common/css/weui.min.css') }}">
    <link rel="stylesheet" href="{{ asset('common/css/jquery-weui.min.css') }}">
    <link rel="stylesheet" href="{{ asset('common/css/public.css') }}">
    <link rel="stylesheet" href="{{ asset('index/css/login.css') }}">
</head>

<body>
<main class="login">
    <div class="login-logo">
        <img src="{{ asset('index/imgs/login/logo_1.png') }}" alt="农夫山泉">
    </div>
    <div style="padding-left:10%;padding-right: 12%;">
        <div class="weui-cell" style="padding: 11px 15px 5px;">
            <div class="weui-cell__bd weui-cell_primary account">
                <input type="text" id="account" class="weui-input" placeholder="请输入账号"/>
            </div>
        </div>
        <div class="weui-cell" style="padding: 37px 15px 5px;">
            <div class="weui-cell__bd weui-cell_primary password">
                <input login_type="1" type="password" id="password" class="weui-input" placeholder="请输入密码"/>
                <button style="margin: 0 0 0 0.5rem;width: 25%;height: 1.5rem;display: none;" class="btn get-code" type="button" style="" onclick="genSmsCode()">获取</button>
                <span class="is-code" style="display: none;">0s后重新获取</span>
            </div>
        </div>
        <div class="weui-cell">
            <div class="weui-cell__hd" style="width: 50%;font-size: 0.6rem;display: none;" onclick="changeLoginType(this)"><label class="weui-label">短信登录</label></div>
            <div class="weui-cell__hd" style="width: 50%;text-align: right;display: none;" onclick="forgetPassword()"><label class="weui-label" style="width: 100%;font-size: 0.6rem;">忘记密码</label></div>
        </div>
    </div>
    <div class="btn">
        <button id="login">登录</button>
    </div>
</main>
<script src="{{ asset('common/js/jquery.min.js') }}"></script>
<script src="{{ asset('common/js/swiper.min.js') }}"></script>
<script src="{{ asset('common/js/jquery-weui.min.js') }}"></script>
<script src="{{ asset('common/js/public.js') }}"></script>
<script src="{{ asset('index/js/login.js') }}"></script>
<script>
    $(function () {

        var login_heigt = parseFloat($(window).height());
        $('.login').css('height', login_heigt - 20);

        $('#login').click(function(){
            var account = $('#account').val();
            var password = $('#password').val();
            var ajax_status = true;
            if($.trim( account).length == 0) {
                $.toptip('账号不能为空','warning');
                $('#account').focus();
                return false;
            }
            if($.trim( password).length == 0) {
                $.toptip('密码不能为空','warning');
                $('#passwrod').focus();
                return false;
            }
            if(ajax_status) {
                $.showLoading();
                var data = {};
                data['account'] = account;
                data['password'] = password;
                data['login_type'] = $('#password').attr('login_type');
                data['_token'] = '{{ csrf_token() }}';

                ajax_status = false;
                $.ajax({
                    'url': '{{ url('index/login') }}',
                    'data': data,
                    'method': 'post',
                    'dataType': 'json',
                    success: function (data) {

                        console.log(data);

                        $.hideLoading();
                        if (data.code == '0000') {
                            $.toast("登陆成功", function () {
                                location.href = '{{ url('index/login') }}';
                            });
                        }
                        else {
                            $.alert(data.mes);
                            ajax_status = false;
                        }
                    },
                    error: function () {
                        $.hideLoading();
                        ajax_status = false;
                    }
                })
            }

        });
    });
</script>
</body>

</html>