<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>上下班打卡管理</title>
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
<div class="x-nav">
  <span class="layui-breadcrumb">
    <a href="">首页</a>
    <a href="">打卡管理</a>
    <a><cite>上下班打卡列表</cite></a>
  </span>
  <a class="layui-btn layui-btn-small" style="line-height:1.6em;margin-top:3px;float:right" href="javascript:location.replace(location.href);" title="刷新">
    <i class="layui-icon" style="line-height:30px">ဂ</i>
  </a>
</div>
<div class="x-body">
  <div class="layui-row">
    <form class="layui-form layui-col-md12 x-so" id="search_form">
      <input class="layui-input" placeholder="开始日" name="start" id="start" value="{{ $search_params['start'] ?? '' }}">
      <input class="layui-input" placeholder="截止日" name="end" id="end" value="{{ $search_params['end'] ?? '' }}">
      <div class="layui-input-inline">
        <select name="office_id">
          <option value="">请选择办事处</option>
          @foreach($office_list as $item)
            <option value="{{ $item->id }}" @if(($search_params['office_id'] ?? '') == $item->id) selected @endif>{{ $item->name ?? '' }}</option>
          @endforeach
        </select>
      </div>
      <input type="text" name="phone"  placeholder="请输入手机号" id="phone" autocomplete="off" class="layui-input" value="{{ $search_params['phone'] ?? '' }}">
      <button class="layui-btn"  lay-submit="" lay-filter="sreach" onclick="getSetCustomPageData(1)"><i class="layui-icon">&#xe615;</i></button>
      <button class="layui-btn" type="button" lay-filter="export" onclick="exportSignClockData()"><i class="layui-icon">导出数据</i></button>
    </form>
  </div>
  <xblock>
    <button class="layui-btn"></button>
    <span class="x-right" style="line-height:40px">共有数据：{{ $list->total() }} 条</span>
  </xblock>
  <table class="layui-table" lay-size="sm">
    <thead>
    <tr>
      <th>办事处名称</th>
      <th>经销商名称</th>
      <th>品牌名称</th>
      <th>渠道名称</th>
      <th>售点名称</th>
      <th>手机号</th>
      <th>促销员姓名</th>
      <th>上班打卡时间</th>
      <th>下班打卡时间</th>
      <th>销售数据</th>
      <th>操作</th>
    </thead>
    <tbody>
    @foreach($list as $value)
      <tr>
        <td>{{ $value->clock_in_list->office_name ?? '' }}</td>
        <td>{{ $value->clock_in_list->dealers_name ?? '' }}</td>
        <td>{{ $value->clock_in_list->activity_item_name ?? '' }}</td>
        <td>{{ $value->clock_in_list->sales_name ?? '' }}</td>
        <td>{{ $value->clock_in_list->points ?? '' }}</td>
        <td>{{ $value->phone ?? '' }}</td>
        <td>{{ $value->clock_out_list->names ?? '' }}</td>
        <td>{{ $value->clock_in_list->created_at ?? '' }}</td>
        <td>{{ $value->clock_out_list->created_at ?? '' }}</td>
        <td>{!! $value->clock_out_list->sale_data ?? '' !!}</td>
        <td>
          <a title="打卡详情" onclick="x_admin_show('打卡详情', '{{ url('admin/signclockDetail') . '/' . $value->phone . '/' . $value->date }}')" href="javascript:;">
            <button class="layui-btn layui-btn-sm">查看详情</button>
          </a>
        </td>
      </tr>
    @endforeach
    </tbody>
  </table>
  @extends('layout.set_custom_page')
  @section('set_custom_page')

</div>
<script>
    layui.use('laydate', function(){
        var laydate = layui.laydate;

        //执行一个laydate实例
        laydate.render({
            elem: '#start' //指定元素
        });

        //执行一个laydate实例
        laydate.render({
            elem: '#end' //指定元素
        });
    });

    // 导出数据
    function exportSignClockData() {
        var start = $('#start').val();
        var end = $('#end').val();
        var phone = $('#phone').val();
        $.ajax({
            url: '{{ url('admin/exportSignClockData') }}',
            type: 'POST',
            data: {start: start, end: end, phone: phone, _token: '{{ csrf_token() }}'},
            success: function (res) {
                console.log(res);
                if (res.code == 0) {

                    layer.confirm('确认下载？', function(index){
                        window.location.href = res.data.url;
                        layer.close(index);
                    });

                } else {
                    layer.msg(res.mes);
                }
                return false;
            }
        });
    }
</script>
<script>var _hmt = _hmt || []; (function() {
        var hm = document.createElement("script");
        hm.src = "https://hm.baidu.com/hm.js?b393d153aeb26b46e9431fabaf0f6190";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
    })();</script>
</body>

</html>