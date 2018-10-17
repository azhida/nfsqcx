<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>后台登录-农夫山泉</title>
	<meta name="renderer" content="webkit|ie-comp|ie-stand">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,user-scalable=yes, minimum-scale=0.4, initial-scale=0.8,target-densitydpi=low-dpi" />
    <meta http-equiv="Cache-Control" content="no-siteapp" />

    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="{{ asset('admin/xadmin/css/font.css') }}">
	<link rel="stylesheet" href="{{ asset('admin/xadmin/css/xadmin.css') }}">
    <script type="text/javascript" src="{{ asset('common/js/jquery.min.js') }}"></script>
    <script src="{{ asset('admin/xadmin/lib/layui/layui.js') }}" charset="utf-8"></script>
    <script type="text/javascript" src="{{ asset('admin/xadmin/js/xadmin.js') }}"></script>

</head>
<body class="login-bg">
    
    <div class="login layui-anim layui-anim-up">
        <div class="message">农夫山泉-管理登录</div>
        <div id="darkbannerwrap"></div>
        
        <form method="post" class="layui-form" id="form">
            {{ csrf_field() }}
            <input name="user_name" placeholder="用户名"  type="text" lay-verify="required" class="layui-input" >
            <hr class="hr15">
            <input name="password" lay-verify="required" placeholder="密码" type="password" class="layui-input">
            <hr class="hr15">
            <input value="登录" style="width:100%;" type="button" onclick="login()">
            <hr class="hr20" >
        </form>
    </div>

    <script>

        function login() {
            $.ajax({
                url: '{{ url('admin/login') }}',
                type: 'post',
                data: $('#form').serialize(),
                success: function (res) {
                    console.log(res);
                    if (res.code == '9999') {
                        layer.msg(res.mes);
                    } else {
                        window.location.href = '{{ url('admin/index') }}';
                        return false;
                    }
                }
            });
        }

    </script>

    
    <!-- 底部结束 -->
    <script>
    //百度统计可去掉
    var _hmt = _hmt || [];
    (function() {
      var hm = document.createElement("script");
      hm.src = "https://hm.baidu.com/hm.js?b393d153aeb26b46e9431fabaf0f6190";
      var s = document.getElementsByTagName("script")[0]; 
      s.parentNode.insertBefore(hm, s);
    })();
    </script>
</body>
</html>