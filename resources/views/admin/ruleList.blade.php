<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>权限管理</title>
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
    <a href="">管理员管理</a>
    <a><cite>权限管理</cite></a>
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
      <input type="text" name="rule_name"  placeholder="请输入角色名" autocomplete="off" class="layui-input" value="{{ $search_params['rule_name'] ?? '' }}">
      <button class="layui-btn"  lay-submit="" lay-filter="sreach" onclick="getSetCustomPageData(1)"><i class="layui-icon">&#xe615;</i></button>
    </form>
  </div>
  <xblock>
    <button class="layui-btn layui-btn-danger" onclick="delAll()"><i class="layui-icon"></i>批量删除</button>
    <button class="layui-btn" onclick="x_admin_show('添加','{{ url('admin/ruleAdd') }}')"><i class="layui-icon"></i>添加</button>
    <span class="x-right" style="line-height:40px"></span>
  </xblock>
  <table class="layui-table">
    <thead>
    <tr>
      <th>
        <div class="layui-unselect header layui-form-checkbox" lay-skin="primary"><i class="layui-icon">&#xe605;</i></div>
      </th>
      <th>ID</th>
      <th>权限名称</th>
      <th>权限描述</th>
      <th>访问路由</th>
      <th>添加时间</th>
      <th>修改时间</th>
      <th>权限详情</th>
      <th>排序</th>
      <th>操作</th>
    </thead>
    <tbody>
    @foreach($list as $key => $value)
      <tr class="parent parent{{ $value->id }}">
        <td>
          <div class="layui-unselect layui-form-checkbox" lay-skin="primary" data-id='{{ $value->id }}'><i class="layui-icon">&#xe605;</i></div>
        </td>
        <td>{{ $value->id }}</td>
        <td>{{ $value->rule_name }}</td>
        <td>{{ $value->rule_desc }}</td>
        <td>{{ $value->route_name }}</td>
        <td>{{ $value->add_time }}</td>
        <td>{{ $value->update_time }}</td>
        <td><button class="layui-btn layui-btn-xs" show_status="0" onclick="showSonList(this, 'son{{ $value->id }}')">点击展开</button></td>
        <td>
          @if($key > 0)
            <button class="layui-btn layui-btn-xs" onclick="changeSort('{{ $value->id }}', 'up')">上移</button>
          @endif
          @if($key + 1 < count($list))
            <button class="layui-btn layui-btn-xs" onclick="changeSort('{{ $value->id }}', 'down')">下移</button>
          @endif
        </td>
        <td class="td-manage">
          <a title="编辑"  onclick="x_admin_show('编辑','{{ url('admin/ruleEdit') . '/' . $value->id }}')" href="javascript:;">
            <i class="layui-icon">&#xe642;</i>
          </a>
          <a title="删除" onclick="member_del(this, '{{ $value->id }}')" href="javascript:;">
            <i class="layui-icon">&#xe640;</i>
          </a>
        </td>
      </tr>
      @if(isset($value->son_list))
        @foreach($value->son_list as $k => $v)
          <tr class="son son{{ $value->id }}" style="display: none;">
            <td>
              <div class="layui-unselect layui-form-checkbox" lay-skin="primary" data-id='{{ $v->id }}'><i class="layui-icon">&#xe605;</i></div>
            </td>
            <td>{{ $v->id }}</td>
            <td>|— {{ $v->rule_name }}</td>
            <td>{{ $v->rule_desc }}</td>
            <td>{{ $v->route_name }}</td>
            <td>{{ $v->add_time }}</td>
            <td>{{ $v->update_time }}</td>
            <td>|—<button class="layui-btn layui-btn-xs">详情</button></td>
            <td>
              @if($k > 0)
                <button class="layui-btn layui-btn-xs" onclick="changeSort('{{ $v->id }}', 'up')">上移</button>
              @endif
              @if($k + 1 < count($value->son_list))
                <button class="layui-btn layui-btn-xs" onclick="changeSort('{{ $v->id }}', 'down')">下移</button>
              @endif
            </td>
            <td class="td-manage">
              <a title="编辑"  onclick="x_admin_show('编辑','{{ url('admin/ruleEdit') . '/' . $v->id }}')" href="javascript:;">
                <i class="layui-icon">&#xe642;</i>
              </a>
              <a title="删除" onclick="member_del(this, '{{ $v->id }}')" href="javascript:;">
                <i class="layui-icon">&#xe640;</i>
              </a>
            </td>
          </tr>
        @endforeach
      @endif
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

    // 展开 顶级权限的子权限列表
    function showSonList(obj, son_class) {

        if ($(obj).attr('show_status') == 0) {
            $(obj).text('点击收起').attr('show_status', 1);
            $('.' + son_class).show();
        } else {
            $(obj).text('点击展开').attr('show_status', 0);
            $('.' + son_class).hide();
        }

    }

    // 移动排序位置
    function changeSort(id, action_type) {

        $.ajax({
            url: '{{ url('admin/changeSort') }}',
            type: 'post',
            data: {id: id, type: action_type, _token: '{{ csrf_token() }}'},
            success: function (res) {
                console.log(res);
                if (res.code == '0000') {
                    layer.msg('操作成功!',{icon: 1,time:1000});

                    if (res.parent_id == '0') {
                        // 父类权限 ，直接刷新页面
                        location.href = location.href;
                    } else {

                        var parent_id = res.data.parent_id;
                        var son_list = res.data.list;

                        var son_list_html = '';
                        for (var i in son_list) {
                            console.log(i);
                            console.log(son_list.length);

                            son_list_html += '<tr class="son son' + son_list[i].parent_id + '">'
                                + '<td>'
                                + '<div class="layui-unselect layui-form-checkbox" lay-skin="primary" data-id="' + son_list[i].id + '"><i class="layui-icon"></i></div>'
                                + '</td>'
                                + '<td>' + son_list[i].id + '</td>'
                                + '<td>|— ' + son_list[i].rule_name + '</td>'
                                + '<td>' + son_list[i].rule_desc + '</td>'
                                + '<td>' + son_list[i].route_name + '</td>'
                                + '<td>' + son_list[i].add_time + '</td>'
                                + '<td>' + son_list[i].update_time + '</td>'
                                + '<td>|—<button class="layui-btn layui-btn-xs">详情</button></td>'
                                + '<td>';

                            if (parseInt(i) > 0) {
                                son_list_html += '<button class="layui-btn layui-btn-xs" onclick="changeSort(' + son_list[i].id + ', \'up\')">上移</button>';
                            }
                            if (parseInt(i) + 1 < son_list.length) {
                                son_list_html += '<button class="layui-btn layui-btn-xs" onclick="changeSort(' + son_list[i].id + ', \'down\')">下移</button>'
                            }

                            son_list_html += '</td>'
                                + '<td class="td-manage">'
                                + '<a title="编辑" onclick="x_admin_show(\'编辑\',\'http://nfsqcx.test/admin/ruleEdit/' + son_list[i].id + '\')" href="javascript:;">'
                                + '<i class="layui-icon"></i>'
                                + '</a>'
                                + '<a title="删除" onclick="member_del(this, ' + son_list[i].id + ')" href="javascript:;"><i class="layui-icon"></i></a>'
                                + '</td>'
                                + '</tr>';

                        }

                        $('.son' + parent_id).remove();
                        $('.parent' + parent_id).after(son_list_html);

                    }

                } else {
                    layer.msg('操作失败!',{icon: 2,time:1000});
                }
                return false;
            }
        });
    }

    /*用户-停用*/
    function member_stop(obj,id){
        layer.confirm('确认要停用吗？',function(index){

            if($(obj).attr('title')=='启用'){

                //发异步把用户状态进行更改
                $(obj).attr('title','停用')
                $(obj).find('i').html('&#xe62f;');

                $(obj).parents("tr").find(".td-status").find('span').addClass('layui-btn-disabled').html('已停用');
                layer.msg('已停用!',{icon: 5,time:1000});

            }else{
                $(obj).attr('title','启用')
                $(obj).find('i').html('&#xe601;');

                $(obj).parents("tr").find(".td-status").find('span').removeClass('layui-btn-disabled').html('已启用');
                layer.msg('已启用!',{icon: 5,time:1000});
            }

        });
    }

    /*用户-删除*/
    function member_del(obj,id){
        layer.confirm('确认要删除吗？',function(index) {
            //发异步删除数据
            $.ajax({
                url: '{{ url('admin/ruleDelete') }}',
                type: 'post',
                data: {ids: [id], _token: '{{ csrf_token() }}'},
                success: function (res) {
                    console.log(res);
                    if (res.code == '0000') {
                        location.href = location.href;
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
                url: '{{ url('admin/ruleDetele') }}',
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