<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>编辑管理员信息</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,user-scalable=yes, minimum-scale=0.4, initial-scale=0.8,target-densitydpi=low-dpi" />
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="{{ asset('admin/xadmin/css/font.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/xadmin/css/xadmin.css') }}">
    <script type="text/javascript" src="{{ asset('common/js/jquery.min.js') }}"></script>
    <script src="{{ asset('admin/xadmin/lib/layui/layui.js') }}" charset="utf-8"></script>
    <script type="text/javascript" src={{ asset('admin/xadmin/js/xadmin.js') }}></script>
    <!-- 让IE8/9支持媒体查询，从而兼容栅格 -->
    <!--[if lt IE 9]>
    <script type="text/javascript" src="{{ asset('common/js/html5.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('common/js/respond.min.js') }}"></script>
    <![endif]-->
</head>

<body>
<div class="x-body">
    <form class="layui-form">
        {{ csrf_field() }}
        <input type="hidden" name="id" value="{{ $data->id ?? 0 }}">
        <div class="layui-form-item">
            <label for="username" class="layui-form-label">
                <span class="x-red">*</span>管理员名称
            </label>
            <div class="layui-input-inline">
                <input type="text" id="username" name="user_name" required="" lay-verify="required"
                       autocomplete="off" class="layui-input" value="{{ $data->user_name ?? '' }}">
            </div>
            <div class="layui-form-mid layui-word-aux">
                <span class="x-red">*</span>将会成为您唯一的登入名
            </div>
        </div>
        <div class="layui-form-item">
            <label for="real_name" class="layui-form-label">
                <span class="x-red">*</span>真实姓名
            </label>
            <div class="layui-input-inline">
                <input type="text" id="real_name" name="real_name" required=""
                       autocomplete="off" class="layui-input" value="{{ $data->real_name ?? '' }}">
            </div>
        </div>

        <div class="layui-form-item">
            <label for="phone" class="layui-form-label">
                <span class="x-red">*</span>手机
            </label>
            <div class="layui-input-inline">
                <input type="text" id="phone" name="phone" required="" lay-verify="phone"
                       autocomplete="off" class="layui-input" value="{{ $data->phone ?? '' }}">
            </div>
        </div>
        <div class="layui-form-item">
            <label for="L_email" class="layui-form-label">
                <span class="x-red">*</span>邮箱
            </label>
            <div class="layui-input-inline">
                <input type="text" id="L_email" name="email" required="" lay-verify="email"
                       autocomplete="off" class="layui-input" value="{{ $data->email ?? '' }}">
            </div>
            <div class="layui-form-mid layui-word-aux">
                <span class="x-red">*</span>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label"><span class="x-red">*</span>选择角色</label>
            <div class="layui-input-inline">
                <select name="role_id">
                    @foreach($list as $value)
                        <option value="{{ $value->id }}" @if($value->id == $data->role_id) selected="" @endif>{{ $value->role_name ?? '' }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="layui-form-item">
            <label for="L_pass" class="layui-form-label">
                <span class="x-red">*</span>密码
            </label>
            <div class="layui-input-inline">
                <input type="password" id="L_pass" name="password" required="" lay-verify="pass"
                       autocomplete="off" class="layui-input">
            </div>
            <div class="layui-form-mid layui-word-aux">
                6到16个字符
            </div>
        </div>
        <div class="layui-form-item">
            <label for="L_repass" class="layui-form-label">
                <span class="x-red">*</span>确认密码
            </label>
            <div class="layui-input-inline">
                <input type="password" id="L_repass" name="" required="" lay-verify="repass"
                       autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label for="L_repass" class="layui-form-label"></label>
            <button type="button" class="layui-btn" lay-filter="add" lay-submit="">编辑</button>
        </div>
    </form>
</div>
<script>
    layui.use(['form','layer'], function(){
        $ = layui.jquery;
        var form = layui.form
            ,layer = layui.layer;

        //自定义验证规则
        form.verify({
            nikename: function(value){
                if(value.length < 5){
                    return '昵称至少得5个字符啊';
                }
            }
            ,repass: function(value){
                if($('#L_pass').val()!=$('#L_repass').val()){
                    return '两次密码不一致';
                }
            }
        });

        //监听提交
        form.on('submit(add)', function(data){
            console.log(data);
            $.ajax({
                url: '{{ url('admin/adminEdit') }}',
                type: 'post',
                data: data.field,
                success: function (res) {
                    console.log(res);
                    if (res.code == '0000') {
                        layer.alert(res.mes, {icon: 1},function () {
                            // 获得frame索引
                            var index = parent.layer.getFrameIndex(window.name);
                            //关闭当前frame
                            parent.layer.close(index);
                            location.href = location.href;
                        });
                    } else {
                        layer.alert(res.mes, {icon: 2});
                    }
                }
            });
            return false;
        });


    });
</script>
<script>var _hmt = _hmt || []; (function() {
        var hm = document.createElement("script");
        hm.src = "https://hm.baidu.com/hm.js?b393d153aeb26b46e9431fabaf0f6190";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
    })();</script>
</body>

</html>