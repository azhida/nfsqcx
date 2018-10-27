<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>编辑角色</title>
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
    <form action="" method="post" class="layui-form layui-form-pane">
        <div class="layui-form-item">
            <label for="name" class="layui-form-label">
                <span class="x-red">*</span>角色名
            </label>
            <div class="layui-input-inline">
                <input type="text" id="role_name" name="role_name" required="" lay-verify="required"
                       autocomplete="off" class="layui-input" value="{{ $role_info->role_name ?? '' }}">
            </div>
        </div>
        <div class="layui-form-item layui-form-text" id="rule_list" role_rule_ids="{{ $role_rule_ids ?? [] }}">
            <label class="layui-form-label">
                拥有权限
            </label>
            <table  class="layui-table layui-input-block">
                <tbody>
                @foreach($list as $value)
                    <tr>
                        <td>
                            <input type="checkbox" name="rule_ids[]" lay-skin="primary" title="{{ $value->rule_name }}" value="{{ $value->id }}">
                        </td>
                        <td>
                            <div class="layui-input-block">
                                @foreach($value->son_list as $v)
                                    <input name="rule_ids[]" lay-skin="primary" type="checkbox" title="{{ $v->rule_name }}" value="{{ $v->id }}">
                                @endforeach

                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="layui-form-item layui-form-text">
            <label for="role_desc" class="layui-form-label">
                描述
            </label>
            <div class="layui-input-block">
                <textarea placeholder="请输入内容" id="role_desc" name="role_desc" class="layui-textarea"></textarea>
            </div>
        </div>
        <div class="layui-form-item">
            <button class="layui-btn" lay-submit="" lay-filter="add">增加</button>
        </div>
        {{ csrf_field() }}
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
            ,pass: [/(.+){6,12}$/, '密码必须6到12位']
            ,repass: function(value){
                if($('#L_pass').val()!=$('#L_repass').val()){
                    return '两次密码不一致';
                }
            }
        });

        //监听提交
        form.on('submit(add)', function(data){
            console.log(data);
            data.field.id = '{{ $role_info->id }}';
            //发异步，把数据提交给php
            $.ajax({
                url: '{{ url('admin/roleEdit') }}',
                data: data.field,
                type: 'post',
                success: function (res) {
                    console.log(res);
                    layer.alert("增加成功", {icon: 6},function () {
                        // 获得frame索引
                        var index = parent.layer.getFrameIndex(window.name);
                        //关闭当前frame
                        parent.layer.close(index);
                    });
                }
            });
            return false;
        });

        // 渲染
        var role_rule_ids = $('#rule_list').attr('role_rule_ids').split('|');
        $('#rule_list input').each(function(event){
            var a = $.inArray($(this).val(), role_rule_ids);
            if (a >= 0) {
                $(this).attr('checked', true).next().addClass('layui-form-checked').attr('checked', true);
            }
        })

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