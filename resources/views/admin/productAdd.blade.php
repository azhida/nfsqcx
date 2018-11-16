<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>添加产品</title>
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
            <label class="layui-form-label"><span class="x-red">*</span>产品分类</label>
            <div class="layui-input-inline">
                <select name="cat_id" id="cat_id" required="required" lay-verify="required" lay-filter="cat_id">
                    <option value="">请选择产品分类</option>
                    @foreach($product_cat_list as $value)
                        <option value="{{ $value->id }}">{{ $value->name ?? '' }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="layui-form-item">
            <label class="layui-form-label"><span class="x-red">*</span>产品口味</label>
            <div class="layui-input-inline">
                <select name="flavor_id" id="flavor_id" required="required" lay-verify="required" lay-filter="flavor_id">
                    <option value="">请选择产品口味</option>
                    {{--@foreach($flavor_list as $value)--}}
                        {{--<option value="{{ $value->id }}">{{ $value->name ?? '' }}</option>--}}
                    {{--@endforeach--}}
                </select>
            </div>
        </div>

        <div class="layui-form-item">
            <label for="dealers_name" class="layui-form-label">
                <span class="x-red">*</span>产品名称
            </label>
            <div class="layui-input-inline">
                <input type="text" id="name" name="name" required="" lay-verify="required"
                       autocomplete="off" class="layui-input">
            </div>
        </div>

        <div class="layui-form-item">
            <label for="product_img" class="layui-form-label">
                <span class="x-red">*</span>产品图片
            </label>
            <div class="layui-upload layui-input-inline">
                <input type="hidden" id="product_img" name="product_img" required="" lay-verify="required"
                       autocomplete="off" class="layui-input">
                <button type="button" class="layui-btn" id="upload_img">上传图片</button>
                <div class="layui-upload-list">
                    <img class="layui-upload-img" id="demo1">
                    <p id="demoText"></p>
                </div>
            </div>
        </div>


        <div class="layui-form-item">
            <label for="L_repass" class="layui-form-label"></label>
            <button type="button" class="layui-btn" lay-filter="add" lay-submit="">增加</button>
        </div>
    </form>
</div>
<script>
    layui.use(['form','layer', 'upload'], function(){
        $ = layui.jquery;
        var form = layui.form;
        var layer = layui.layer;
        var upload = layui.upload;

        //监听提交
        form.on('submit(add)', function(data){
            console.log(data);
            //发异步，把数据提交给php
            $.ajax({
                url: '{{ url('admin/productAdd') }}',
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

        form.on('select(cat_id)', function(data){

            var cat_id = data.value;
            console.log(cat_id);
            if (cat_id == '0') {

                var flavor_list_html = '<option value="0">请选择产品口味</option>';
                $('#flavor_id').html(flavor_list_html);
                form.render('select'); //刷新select选择框渲染

            } else {

                $.ajax({
                    url: '{{ url('admin/getFlavorListByCatId') }}',
                    type: 'post',
                    data: {_token: '{{ csrf_token() }}', cat_id: cat_id},
                    success: function (res) {
                        if (res.code == '0000') {

                            var flavor_list_html = '<option value="">请选择产品口味</option>';
                            for (var i in res.data) {
                                flavor_list_html += '<option value="' + res.data[i].id + '">' + res.data[i].name + '</option>';
                            }
                            $('#flavor_id').html(flavor_list_html);
                            form.render('select'); //刷新select选择框渲染

                        }
                    }
                });
            }
        });

        //普通图片上传
        var uploadInst = upload.render({
            elem: '#upload_img'
            ,url: '{{ url('admin/uploadProductImgToOssOnlyOne') }}'
            ,type: 'post'
            ,data: {_token: '{{ csrf_token() }}'}
            ,before: function(obj){
                //预读本地文件示例，不支持ie8
                obj.preview(function(index, file, result){
                    $('#demo1').attr('src', result); //图片链接（base64）
                });
            }
            ,done: function(res){

                //如果上传失败
                if(res.code > 0){
                    return layer.msg('上传失败');
                } else {
                    $('#product_img').val(res.data); // 赋值
                }
                //上传成功
            }
            ,error: function(){
                //演示失败状态，并实现重传
                var demoText = $('#demoText');
                demoText.html('<span style="color: #FF5722;">上传失败</span> <a class="layui-btn layui-btn-xs demo-reload">重试</a>');
                demoText.find('.demo-reload').on('click', function(){
                    uploadInst.upload();
                });
            }
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