<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>产品管理</title>
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
    <a href="">产品管理</a>
    <a><cite>产品列表</cite></a>
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
        <select name="cat_id" id="cat_id" lay-filter="cat_id">
          <option value="0">请选择产品分类</option>
          @foreach($cat_list as $item)
          <option value="{{ $item->id }}" @if(($search_params['cat_id'] ?? '') == $item->id) selected @endif>{{ $item->name ?? '' }}</option>
          @endforeach
        </select>
      </div>
      <div class="layui-input-inline">
        <select name="flavor_id" id="flavor_id" lay-filter="flavor_id">
          <option value="">请选择产品口味</option>
          @foreach($flavor_list as $item)
            <option cat_id="{{ $item->cat_id }}" value="{{ $item->id }}" @if(($search_params['flavor_id'] ?? '') == $item->id) selected @endif>{{ $item->name ?? '' }}</option>
          @endforeach
        </select>
      </div>
      <input type="text" name="name"  placeholder="请输入产品名称" autocomplete="off" class="layui-input" value="{{ $search_params['name'] ?? '' }}">
      <button class="layui-btn"  lay-submit="" lay-filter="sreach" onclick="getSetCustomPageData(1)"><i class="layui-icon">&#xe615;</i></button>
    </form>
  </div>
  <xblock>
    <button class="layui-btn layui-btn-danger" onclick="delAll()"><i class="layui-icon"></i>批量删除</button>
    <button class="layui-btn" onclick="x_admin_show('添加产品','{{ url('admin/productAdd') }}')"><i class="layui-icon"></i>添加</button>
    <span class="x-right" style="line-height:40px">共有数据：{{ $list->total() }} 条</span>
  </xblock>
  <table class="layui-table">
    <thead>
    <tr>
      <th>
        <div class="layui-unselect header layui-form-checkbox" lay-skin="primary"><i class="layui-icon">&#xe605;</i></div>
      </th>
      <th>ID</th>
      <th>产品分类</th>
      <th>产品口味</th>
      <th>产品名称</th>
      <th>产品图片</th>
      <th>添加时间</th>
      <th>修改时间</th>
      <th>操作</th>
    </thead>
    <tbody>
    @foreach($list as $value)
      <tr>
        <td>
          <div class="layui-unselect layui-form-checkbox" lay-skin="primary" data-id='{{ $value->id }}'><i class="layui-icon">&#xe605;</i></div>
        </td>
        <td>{{ $value->id }}</td>
        <td>{{ $value->cat_name ?? '' }}</td>
        <td>{{ $value->flavor_name ?? '' }}</td>
        <td>{{ $value->name ?? '' }}</td>
        <td style="text-align: center;"><img src="{{ $value->oss_img_url ?? '' }}" alt=""></td>
        <td>{{ $value->add_time }}</td>
        <td>{{ $value->update_time }}</td>
        <td class="td-manage">
          <a title="编辑"  onclick="x_admin_show('编辑','{{ url('admin/productEdit') . '/' . $value->id }}')" href="javascript:;">
            <i class="layui-icon">&#xe642;</i>
          </a>
          <a title="删除" onclick="member_del(this, '{{ $value->id }}')" href="javascript:;">
            <i class="layui-icon">&#xe640;</i>
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
    layui.use(['laydate', 'form'], function(){
        var laydate = layui.laydate;
        var form = layui.form;

        //执行一个laydate实例
        laydate.render({
            elem: '#start' //指定元素
        });

        //执行一个laydate实例
        laydate.render({
            elem: '#end' //指定元素
        });

        form.on('select(cat_id)', function (data) {
            console.log(data);
            var flavor_list_html = '<option value="0">请选择产品口味</option>';
            @foreach($flavor_list as $value)
            if (data.value == '0') {
                flavor_list_html += '<option value="{{ $value->id }}">{{ $value->name }}</option>';
            } else {
                if (data.value == '{{ $value->cat_id }}') {
                    flavor_list_html += '<option value="{{ $value->id }}">{{ $value->name }}</option>';
                }
            }
            @endforeach
            $('#flavor_id').html(flavor_list_html);
            form.render('select');
        });
    });
    
    /*用户-删除*/
    function member_del(obj,id){
        if (id == 1) {
            layer.msg('初始管理员不能删除！');
            return false;
        }
        layer.confirm('确认要删除吗？',function(index){
            //发异步删除数据
            $.ajax({
                url: '{{ url('admin/productDelete') }}',
                type: 'post',
                data: {ids: [id], _token: '{{ csrf_token() }}'},
                success: function (res) {
                    console.log(res);
                    if (res.code == '0000') {
                        $(obj).parents("tr").remove();
                        layer.msg('已删除!',{icon:1,time:1000});
                    } else {
                        layer.msg('删除失败!',{icon:2,time:1000});
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
                url: '{{ url('admin/productDelete') }}',
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