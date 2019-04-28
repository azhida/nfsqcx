<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>上班打卡详情</title>
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

<h3 style="margin-left: 10px;">打卡详情：</h3>
<table class="layui-table layui-col-xs6" lay-even="" lay-skin="nob" style="margin-left: 50px;">
  <tbody>
  <tr><td>办事处账号：</td><td>{{ $info->user_name ?? '' }}</td></tr>
  <tr><td>办事处名称：</td><td>{{ $info->office_name ?? '' }}</td></tr>
  <tr><td>经销商名称：</td><td>{{ $info->dealers_name ?? '' }}</td></tr>
  <tr><td>品牌名称：</td><td>{{ $info->activity_item_name ?? '' }}</td></tr>
  <tr><td>渠道名称：</td><td>{{ $info->sales_name ?? '' }}</td></tr>
  <tr><td>售点名称：</td><td>{{ $info->points ?? '' }}</td></tr>
  <tr><td>手机号：</td><td>{{ $info->phone ?? '' }}</td></tr>
  <tr><td>上班打卡时间：</td><td>{{ $info->created_at ?? '' }}</td></tr>
  <tr>
    <td>现场促销照片：</td>
    <td>
      <img src="{{ $info->oss_img_1 ?? '' }}" alt="">
      <img src="{{ $info->oss_img_2 ?? '' }}" alt="">
      <img src="{{ $info->oss_img_3 ?? '' }}" alt="">
    </td>
  </tr>
  </tbody>
</table>
<script>

</script>

</body>

</html>