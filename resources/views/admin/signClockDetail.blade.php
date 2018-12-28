<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>下班打卡详情</title>
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

<fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
  <legend>打卡详情</legend>
</fieldset>
<table class="layui-table layui-col-xs6" lay-even="" lay-skin="nob" style="margin-left: 50px;">
  <tbody>
  <tr><td>用户账号：</td><td>{{ $info->account ?? '' }}</td></tr>
  <tr><td>上报手机号：</td><td>{{ $info->phone ?? '' }}</td></tr>
  <tr><td>销售姓名：</td><td>{{ $info->names ?? '' }}</td></tr>
  <tr><td>打卡时间：</td><td>{{ date('Y-m-d H:i:s', $info->create_time) }}</td></tr>
  @foreach($info->imgs as $key => $img)
    <tr><td>{{ $key ?? '' }}：</td><td><img src="{{ $img ?? '' }}" alt=""></td></tr>
  @endforeach
  </tbody>
</table>
<hr>
<div class="layui-inline">
  <label class="layui-form-label">销售数据：</label>
</div>
<table class="layui-table layui-col-xs6" lay-even="" lay-skin="" style="margin-left: 50px;">
  <thead>
  <tr><th>产品名称</th><th>数量</th></tr>
  </thead>
  <tbody>
  @foreach($info->data as $value)
    <tr><td>{{ $value['product_name'] ?? '' }}</td><td>{{ $value['product_num'] ?? 0 }}</td></tr>
  @endforeach
  </tbody>
</table>
<script>

</script>

</body>

</html>