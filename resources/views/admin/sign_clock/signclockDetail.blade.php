<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>当日打卡详情</title>
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

<body style="padding: 20px;">
<h2 style="margin-bottom: 20px;">促销员手机号码：{{ $info['phone'] ?? '' }} ；  打卡日期：{{ $info['date'] ?? '' }}</h2>
<h3 style="margin-left: 0px;">上班打卡详情：</h3>
<table class="layui-table" lay-size="sm">
  <thead>
  <tr>
    <th>ID</th>
    <th>办事处账号</th>
    <th>办事处名称</th>
    <th>经销商名称</th>
    <th>品牌名称</th>
    <th>渠道名称</th>
    <th>售点名称</th>
    <th>手机号</th>
    <th>上班打卡时间</th>
    <td>查看详情</td>
  </tr>
  </thead>
  <tbody>
  @foreach($info['sign_clock_in_list'] as $value)
    <tr>
      <td>{{ $value->id }}</td>
      <td>{{ $value->user_name ?? '' }}</td>
      <td>{{ $value->office_name ?? '' }}</td>
      <td>{{ $value->dealers_name ?? '' }}</td>
      <td>{{ $value->activity_item_name ?? '' }}</td>
      <td>{{ $value->sales_name ?? '' }}</td>
      <td>{{ $value->points ?? '' }}</td>
      <td>{{ $value->phone ?? '' }}</td>
      <td>{{ $value->created_at ?? '' }}</td>
      <td>
        <a title="打卡详情" onclick="x_admin_show('打卡详情', '{{ url('admin/signclockinDetail') . '/' . $value->id }}')" href="javascript:;">
          <button class="layui-btn layui-btn-sm">查看详情</button>
        </a>
      </td>
    </tr>
  @endforeach
  </tbody>
</table>
<hr>
<h3 style="margin-left: 0px;">下班打卡详情：</h3>
<table class="layui-table" lay-size="sm">
  <thead>
  <tr>
    <th>ID</th>
    <th>办事处账号</th>
    <th>办事处名称</th>
    <th>经销商名称</th>
    <th>售点名称</th>
    <th>手机号</th>
    <th>促销员姓名</th>
    <th>下班打卡时间</th>
    <th>操作</th>
  </thead>
  <tbody>
  @foreach($info['sign_clock_out_list'] as $value)
    <tr>
      <td>{{ $value->id }}</td>
      <td>{{ $value->user_name ?? '' }}</td>
      <td>{{ $value->office_name ?? '' }}</td>
      <td>{{ $value->dealers_name ?? '' }}</td>
      <td>{{ $value->points ?? '' }}</td>
      <td>{{ $value->phone ?? '' }}</td>
      <td>{{ $value->names ?? '' }}</td>
      <td>{{ $value->created_at ?? '' }}</td>
      <td class="td-manage">
        <a title="打卡详情"  onclick="x_admin_show('打卡详情','{{ url('admin/signclockoutDetail') . '/' . $value->id }}')" href="javascript:;">
          <button class="layui-btn layui-btn-sm">打卡详情</button>
        </a>
      </td>
    </tr>
  @endforeach
  </tbody>
</table>

<script>

</script>

</body>

</html>