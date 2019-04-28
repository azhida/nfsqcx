<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>上班打卡管理</title>
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
    <a href="">上班打卡管理</a>
    <a><cite>上班打卡列表</cite></a>
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
      <input type="text" name="account"  placeholder="请输入办事处账号" autocomplete="off" class="layui-input" value="{{ $search_params['account'] ?? '' }}">
      <input type="text" name="office_name"  placeholder="请输入办事处名称" autocomplete="off" class="layui-input" value="{{ $search_params['office_name'] ?? '' }}">
      <input type="text" name="dealers_name"  placeholder="请输入经销商名称" autocomplete="off" class="layui-input" value="{{ $search_params['dealers_name'] ?? '' }}">
      <input type="text" name="activity_item_name"  placeholder="请输入品牌名称" autocomplete="off" class="layui-input" value="{{ $search_params['activity_item_name'] ?? '' }}">
      <input type="text" name="sales_name"  placeholder="请输入渠道名称" autocomplete="off" class="layui-input" value="{{ $search_params['sales_name'] ?? '' }}">
      <input type="text" name="points"  placeholder="请输入售点名称" autocomplete="off" class="layui-input" value="{{ $search_params['points'] ?? '' }}">
      <input type="text" name="phone"  placeholder="请输入手机号" autocomplete="off" class="layui-input" value="{{ $search_params['phone'] ?? '' }}">
      <button class="layui-btn"  lay-submit="" lay-filter="sreach" onclick="getSetCustomPageData(1)"><i class="layui-icon">&#xe615;</i></button>
    </form>
  </div>
  <xblock>
    <button class="layui-btn"></button>
    <span class="x-right" style="line-height:40px">共有数据：{{ $list->total() }} 条</span>
  </xblock>
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
    @foreach($list as $value)
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



    /*用户-删除*/
    function member_del(obj,id){
        layer.confirm('确认要删除吗？',function(index){
            //发异步删除数据
            $.ajax({
                url: '{{ url('admin/productCatDelete') }}',
                type: 'post',
                data: {ids: [id], _token: '{{ csrf_token() }}'},
                success: function (res) {
                    console.log(res);
                    if (res.code == '0000') {
                        $(obj).parents("tr").remove();
                        layer.msg(res.mes, {icon:1, time:1000});
                    } else {
                        layer.msg(res.mes, {icon:2, time:1000});
                    }
                    return false;
                }
            })
        });
    }



    function delAll (argument) {
        var data = tableCheck.getData();
        layer.confirm('确认要删除吗？'+data,function(index){
            //捉到所有被选中的，发异步进行删除
            $.ajax({
                url: '{{ url('admin/productCatDelete') }}',
                type: 'post',
                data: {ids: data, _token: '{{ csrf_token() }}'},
                success: function (res) {
                    console.log(res);
                    if (res.code == '0000') {
                        layer.msg('删除成功', {icon: 1});
                        $(".layui-form-checked[data-id!=1]").not('.header').parents('tr').remove();
                    } else {
                        layer.msg('删除失败', {icon: 2});
                    }
                    return false;
                }
            });
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