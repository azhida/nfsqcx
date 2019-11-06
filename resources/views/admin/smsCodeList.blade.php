<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>短信发送记录</title>
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
    <a href="">数据统计</a>
    <a><cite>短信发送记录</cite></a>
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
      <input type="text" name="phone"  placeholder="请输入手机号码" autocomplete="off" class="layui-input" value="{{ $search_params['phone'] ?? '' }}">
      <div class="layui-input-inline">
        <select name="status">
          <option value="">请选择发送状态</option>
          @foreach(\App\Models\SmsCode::$statusMap as $key => $item)
            <option value="{{ $key }}" @if(($search_params['status'] ?? '') === (string)$key) selected @endif>{{ $item ?? '' }}</option>
          @endforeach
        </select>
      </div>
      <button class="layui-btn"  lay-submit="" lay-filter="sreach" onclick="getSetCustomPageData(1)"><i class="layui-icon">&#xe615;</i></button>
    </form>
  </div>
  <xblock>
    <button class="layui-btn layui-btn-danger" onclick="delAll()"><i class="layui-icon"></i>批量删除</button>
    <button class="layui-btn" onclick="x_admin_show('添加用户','{{ url('admin/sellersAdd') }}')"><i class="layui-icon"></i>添加</button>
    <span class="x-right" style="line-height:40px">共有数据：{{ $list->total() }} 条</span>
  </xblock>
  <table class="layui-table">
    <thead>
    <tr>
      <th>ID</th>
      <th>手机号码</th>
      <th>验证码</th>
      <th>发送状态</th>
      <th>发送时间</th>
      <th>运营商提示</th>
    </thead>
    <tbody>
    @foreach($list as $value)
      <tr>
        <td>{{ $value->id }}</td>
        <td>{{ $value->phone }}</td>
        <td>{{ $value->code }}</td>
        <td>{{ $value->status_text }}</td>
        <td>{{ date('Y-m-d H:i:s', $value->create_time) ?? '' }}</td>
        <td>{{ $value->errmsg }}</td>
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
</script>
<script>var _hmt = _hmt || []; (function() {
        var hm = document.createElement("script");
        hm.src = "https://hm.baidu.com/hm.js?b393d153aeb26b46e9431fabaf0f6190";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(hm, s);
    })();</script>
</body>

</html>