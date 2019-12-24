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

        <div class="weui-cell">
            <div class="weui-cell__bd weui-cell_primary" style="padding: 0;">
                <div class="weui-cell__bd">
                    <input class="weui-input" id="office" name="office" type="text" value="" readonly="" placeholder="请选择办事处">
                </div>
            </div>
        </div>

        <div class="weui-cell" style="padding: 37px 15px 5px;">
            <div class="weui-cell__bd weui-cell_primary account">
                <input type="text" id="phone" name="phone" class="weui-input" placeholder="手机号码"/>
            </div>
        </div>
        <div class="weui-cell" style="padding: 37px 15px 5px;">
            <div class="weui-cell__bd weui-cell_primary password">
                <input style="width: 45%;" type="text" id="sms_code" name="sms_code" class="weui-input" placeholder="验证码"/>
                <button type="button" class="weui-btn weui-btn_mini weui-btn_plain-primary right get-code" onclick="genSmsCode('{{ csrf_token() }}')">获取</button>
                <span class="is-code" style="display: none;">重新获取</span>
            </div>
        </div>

        <div class="weui-cell">
            {{--<div class="weui-cell__hd" style="width: 50%;font-size: 0.6rem;" onclick="changeLoginType(this)"><label class="weui-label">短信登录</label></div>--}}
            {{--<div class="weui-cell__hd" style="width: 50%;text-align: right;" onclick="forgetPassword()"><label class="weui-label" style="width: 100%;font-size: 0.6rem;">忘记密码</label></div>--}}
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

        $.ajax({
            'url': '{{ url('index/getOffices') }}',
            'method': 'get',
            'dataType': 'json',
            success: function (res) {
                console.log(res);
                if (res.code == '0000') {
                    $('#office').select("update", {title: "选择办事处", items: res.data })
                } else {
                    alert('网络异常');
                    return false;
                }
            }
        });

        var login_heigt = parseFloat($(window).height());
        $('.login').css('height', login_heigt - 20);

        $('#login').click(function(){
            var office_id = $('#office').data('values');
            var phone = $('#phone').val();
            var sms_code = $('#sms_code').val();
            var ajax_status = true;
            if($.trim(office_id).length == 0 || office_id <= 0) {
                $.toptip('请选择办事处','warning');
                $('#account').focus();
                return false;
            }
            if($.trim(phone).length == 0) {
                $.toptip('手机号码不能为空','warning');
                $('#account').focus();
                return false;
            }
            if($.trim(sms_code).length == 0) {
                $.toptip('验证码不能为空','warning');
                $('#sms_code').focus();
                return false;
            }
            if(ajax_status) {
                $.showLoading();
                var data = {
                    _token: '{{ csrf_token() }}',
                    office_id: office_id,
                    phone: phone,
                    sms_code: sms_code
                };
                ajax_status = false;
                $.ajax({
                    'url': '{{ url('index/login') }}',
                    'data': data,
                    'method': 'post',
                    'dataType': 'json',
                    success: function (data) {
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