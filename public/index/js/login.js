// 切换登录方式：密码登录 1 ， 短信验证码 2
function changeLoginType(obj) {
    var login_type = $('#password').attr('login_type');
    if (login_type == 1) { // 当前 是 密码，即将切换到 验证码
        $(obj).children('.weui-label').text('密码登录');
        $('#password').attr({login_type: 2, type: 'text', placeholder: '请输入验证码'}).css({width: '65%'}).next().css('display', 'block');
        $('#password_text').text('验证码');
    } else { // 当前 是 验证码，即将切换到 密码
        $(obj).children('.weui-label').text('短信登录');
        $('#password').attr({login_type: 1, type: 'password', placeholder: '请输入密码'}).siblings().remove();
        $('#password_text').text('密码');

    }
}

// 获取短信验证码
function genSmsCode() {
    var val = $('#account').val();
    if (val == '') {
        $.alert('请输入手机号码');return;
    }
    var ajax_status = true;
    if(ajax_status) {
        $.showLoading();
        var data = {};
        data['phone'] = val;
        data['sms_type'] = 1;
        ajax_status = false;
        $.ajax({
            'url': '/index/sms/send',
            'data': data,
            'type': 'post',
            'dataType': 'json',
            success: function (data) {
                console.log(data)
                $.hideLoading();
                if(data.code == '0000')
                {
                    $('.get-code').hide();
                    $('.is-code').show();
                    countTime(60); // 倒计时
                }
                else
                {
                    $.alert(data.mes);
                }
            },
            error: function () {
                $.alert('系统错误');
            }
        })
    }
}

// 倒计时
function countTime(s) {
    if (s == 0){
        $('.is-code').hide();
        $('.get-code').show();
        return;
    }
    s--;

    $('.is-code').text(s + 's后重新获取');
    setTimeout(function () {
        countTime(s)
    },1000)
}