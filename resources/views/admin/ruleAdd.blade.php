<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>添加权限</title>
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
        <div class="layui-form-item">
            <label for="rule_name" class="layui-form-label">
                <span class="x-red">*</span>权限名称
            </label>
            <div class="layui-input-inline">
                <input type="text" id="rule_name" name="rule_name" required="" lay-verify="required|rule_name"
                       autocomplete="off" class="layui-input">
            </div>
        </div>
        <div class="layui-form-item">
            <label for="route_name" class="layui-form-label">
                <span class="x-red">*</span>访问路由
            </label>
            <div class="layui-input-inline">
                <input type="text" id="real_name" name="route_name" required=""
                       autocomplete="off" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label"><span class="x-red">*</span>上级权限</label>
            <div class="layui-input-inline">
                <select name="parent_id">
                    <option value="0">顶级权限</option>
                    @foreach($list as $value)
                        <option value="{{ $value->id }}">{{ $value->rule_name ?? '' }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="layui-form-item layui-form-text">
            <label for="rule_desc" class="layui-form-label">
                权限描述
            </label>
            <div class="layui-input-block">
                <textarea placeholder="请输入内容" id="desc" name="rule_desc" class="layui-textarea"></textarea>
            </div>
        </div>
        <div class="layui-form-item">
            <label for="L_repass" class="layui-form-label"></label>
            <button type="button" class="layui-btn" lay-filter="add" lay-submit="">增加</button>
        </div>
    </form>
</div>
<script>
    layui.use(['form','layer'], function(){
        $ = layui.jquery;
        var form = layui.form;
        var layer = layui.layer;

        //监听提交
        form.on('submit(add)', function(data){
            console.log(data);
            //发异步，把数据提交给php
            $.ajax({
                url: '{{ url('admin/ruleAdd') }}',
                type: 'post',
                data: data.field,
                success: function (res) {
                    console.log(res);
                    if (res.code == '0000') {
                        layer.alert("增加成功", {icon: 1},function () {
                            // 获得frame索引
                            var index = parent.layer.getFrameIndex(window.name);
                            //关闭当前frame
                            parent.layer.close(index);
                        });
                    } else {
                        layer.alert("增加失败", {icon: 2});
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