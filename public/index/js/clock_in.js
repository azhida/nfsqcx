time('-','-',' ');
var selectText = '默认第一项';
var id = '';
var type = null;
var imgs = [];
var keys = 1;



// 选择列表点击
$('.select_list').on('click', '.weui-cell_access', function () {

    $('#word_in').hide();

    type = $(this).data('type');

    if (type == 0){
        $('#word_in').show();
        return false;
        $('.select-box-tit').text('办事处选择');
        var sid = $('#office_id').val();
    }
    if (type == 1){
        $('.select-box-tit').text('经销商选择');
        var sid = $('#dealers_id').val();
        var office_id = $('#office_id').val();
        if (office_id < 1) {
            $.alert('请选择办事处');
            $('#word_in').show();
            return;
        }
    }
    if (type == 2){
        // $('.select-box-tit').text('销售点选择');
        $('#word_in').show();
        return
    }
    if (type == 3){
        $('.select-box-tit').text('渠道选择');
        $('#sale_id').val(id);
        var sid = $('#sale_id').val();
    }
    if (type == 4){
        $('.select-box-tit').text('品牌选择');
        $('#activity_item_id').val(id);
        var sid = $('#activity_item_id').val();
    }
    if (type == 5){
        $('#word_in').show();
        return;
    }
    var ajax_status = true;
    if(ajax_status) {
        $.showLoading();
        var data = {};
        data['type'] = type;
        data['office_id'] = office_id;
        data['_token'] = $('#_token').val();
        ajax_status = false;
        $.ajax({
            'url': '/index/activity/getSelectData',
            'data': data,
            'method': 'post',
            'dataType': 'json',
            success: function (data) {
                console.log(data);

                $.hideLoading();
                if (data.code == '0000') {
                    $('#html').html('');
                    var result = '';
                    for(var i = 0; i < data.data.length; i++){
                        if(sid == '')
                        {
                            result = result + "<li><p data-id='"+data.data[i].id+"'>"+data.data[i].name+"</p><i class='iconfont icon-check_circle'></i></li>";
                        }
                        else
                        {
                            if( data.data[i].id == sid)
                            {
                                result = result + "<li><p data-id='"+data.data[i].id+"'>"+data.data[i].name+"</p><i class='iconfont icon-check_circle active'></i></li>";
                            }
                            else
                            {
                                result = result + "<li><p data-id='"+data.data[i].id+"'>"+data.data[i].name+"</p><i class='iconfont icon-check_circle'></i></li>";
                            }
                        }


                    }
                    $("#html").html(result);
                    $('.select-box').show();
                }
                else
                {
                    $.alert('系统错误');
                }
            },
            error: function () {
                $.alert('系统错误');
            }
        })
    }
});

function choose(obj) {

    $('.select-box').hide();
    if (type == 0){
        $('.office').find('p').text(selectText);
        $('.office_text').text(selectText);
        $('#office_id').val(id);
    }
    if (type == 1){
        $('.distributor').find('p').text(selectText);
        $('.dealers_text').text(selectText);
        $('#dealers_id').val(id);
    }
    if (type == 2){
        $('.sale_text').find('p').text(selectText);
    }
    if (type == 3){
        $('.channel').find('p').text(selectText);
        $('.channel_text').text(selectText);
        $('#sale_id').val(id);
    }
    if (type == 4){
        $('.brand').find('p').text(selectText);
        $('.brand_text').text(selectText);
        $('#activity_item_id').val(id);
    }

    $('#word_in').show();

}

// 选择列表点击
$('.list').on('click','li',function () {
    type = $(this).data('type');

    if (type == 0){
        $('.select-box-tit').text('办事处选择');
        var sid = $('#office_id').val();
    }
    if (type == 1){
        $('.select-box-tit').text('经销商选择');
        var sid = $('#dealers_id').val();
        var office_id = $('#office_id').val();
        if (office_id < 1) {
            $.alert('请选择办事处');return;
        }
    }
    if (type == 2){
        // $('.select-box-tit').text('销售点选择');
        return
    }
    if (type == 3){
        $('.select-box-tit').text('渠道选择');
        $('#sale_id').val(id);
        var sid = $('#sale_id').val();
    }
    if (type == 4){
        $('.select-box-tit').text('品牌选择');
        $('#activity_item_id').val(id);
        var sid = $('#activity_item_id').val();
    }
    if (type == 5){
        return;
    }
    var ajax_status = true;
    if(ajax_status) {
        $.showLoading();
        var data = {};
        data['type'] = type;
        data['office_id'] = office_id;
        ajax_status = false;
        $.ajax({
            'url': '/index/activity/getSelectData',
            'data': data,
            'method': 'post',
            'dataType': 'json',
            success: function (data) {
                $.hideLoading();
                if (data.code == '0000') {
                    $('#html').html('');
                    var result = '';
                    for(var i = 0; i < data.data.length; i++){
                        if(sid == '')
                        {
                            result = result + "<li><p data-id='"+data.data[i].id+"'>"+data.data[i].name+"</p><i class='iconfont icon-check_circle'></i></li>";
                        }
                        else
                        {
                            if( data.data[i].id == sid)
                            {
                                result = result + "<li><p data-id='"+data.data[i].id+"'>"+data.data[i].name+"</p><i class='iconfont icon-check_circle active'></i></li>";
                            }
                            else
                            {
                                result = result + "<li><p data-id='"+data.data[i].id+"'>"+data.data[i].name+"</p><i class='iconfont icon-check_circle'></i></li>";
                            }
                        }


                    }
                    $("#html").html(result);
                    $('.select-box').show();
                }
                else
                {
                    $.alert('系统错误');
                }
            },
            error: function () {
                $.alert('系统错误');
            }
        })
    }
});

// 弹窗确定按钮 关闭弹窗
$('.select-box-choose').on('click','.btn',function () {

    $('.select-box').hide();
    if (type == 0){
        $('.office').find('p').text(selectText);
        $('#office_id').val(id);
    }
    if (type == 1){
        $('.distributor').find('p').text(selectText);
        $('#dealers_id').val(id);
    }
    if (type == 2){
        $('.salesOffice').find('p').text(selectText);
    }
    if (type == 3){
        $('.channel').find('p').text(selectText);
        $('#sale_id').val(id);
    }
    if (type == 4){
        $('.brand').find('p').text(selectText);
        $('#activity_item_id').val(id);
    }

});

// 弹窗内的列表项点击
$('.select-box').on('click','li',function () {
    $(this).find('i').addClass('active');
    $(this).siblings().find('i').removeClass('active');
    selectText = $(this).find('p').text();
    id = $(this).find('p').attr('data-id');
});

// 图片点击
$(document).on("click", ".pics", function() {
    var url = $(this).attr('src');
    $("#bigimg").css("background-image","url(" + url + ")");
    $('#bigdel').attr('data-url',url);
    $('#bigdel').attr('data-id',$(this).attr('data-id'));
    $('#big').show();
})

$('#bigback').click(function(){
    $('#big').hide();
});

$('#bigdel').click(function(){

    var url = $(this).attr('data-url');
    var sign = $(this).attr('data-id');
    imgs.splice($.inArray(url,imgs),1);
    $('#big').hide();

    var num = $('#' + sign).attr('num');

    $.alert('删除成功');

    $('.addImg' + num).show();
    $('#'+sign).hide();

});

// 上传图片
function changeAddImg(e, num) {

    var file = e.target.files[0]; // 获取input file控件选择的文件
    ImgToBase64(file, 720, 1, function (base64) { // 图片最大长度（或宽度）：720px；图片最大尺寸：1m；目的：减轻服务器压力

        // $("#img1")[0].src = base64;//预览页面上预留一个img元素，载入base64
        // $("#img1")[0].width = 300;//设定宽高，不然会自动按照压缩过的图片宽高设定，有可能超出预想的范围。
        //直接利用ajax上传base64到服务器，完毕

        uploadBase64Img(base64, num);
    });
}

function uploadBase64Img(base64, num) {
    $.showLoading();
    var data = {};
    data['base64'] = base64;
    data['_token'] = $('#_token').val();
    ajax_status = false;
    $.ajax({
        'url': '/index/activity/uploadClockInPic',
        'data': data,
        'method': 'post',
        'dataType': 'json',
        success: function (data) {
            $.hideLoading();

            imgs.push(num + '|' + data.data.url);

            $('.addImg' + num).hide();
            $('#list_id' + num).show().children('img').attr('src', data.data.url);

        },
        error: function () {
            $.alert('网络较慢，请检查后重试');
            $.hideLoading();
        }
    })
}


// 上传图片
$('#upImg').change(function (e) {
    var file = e.target.files[0];
    var reader = new FileReader();
    reader.readAsDataURL(file); // 读出 base64
    reader.onload = function(e) {
        // 图片的 base64 格式, 可以直接当成 img 的 src 属性值
        var dataURL = reader.result; // dataURL 为图片 base64 码
        // 下面逻辑处理
        var ajax_status = true;
        if(ajax_status) {
            $.showLoading();
            var data = {};
            data['base64'] = dataURL;
            ajax_status = false;
            $.ajax({
                'url': '/index/activity/uploadClockInPic',
                'data': data,
                'method': 'post',
                'dataType': 'json',
                success: function (data) {
                    $.hideLoading();
                    imgs.push(data.data.url);
                    var html = "<li id='list_id"+keys + "' num='" + $('#addImg').attr('num') + "'><img src='"+data.data.url+"' alt='' class='pics' data-id='list_id"+ keys +"'></li>";
                    $('.addImg' + $('#addImg').attr('num')).before(html).remove();
                    $('#addImg').attr('num', 0);
                    keys ++;
                },
                error: function () {
                    $.alert('网络较慢，请检查后重试');
                    $.hideLoading();
                }
            })
        }
    };
})


// 打卡
$('.clock-in').on('click','.btn',function () {
    var ajax_status = true;
    var office_id = $('#office_id').val();
    var dealers_id = $('#dealers_id').val();
    var sale_id = $('#sale_id').val();
    var product_id = $('#product_id').val();
    var activity_item_id = $('#activity_item_id').val();
    var salesOffice = $('#salesOffice').val();
    var phone = $('#phone').val();
    if($.trim( office_id).length == 0)
    {
        $.toptip('请选择办事处','warning');
        return false;
    }
    if($.trim( dealers_id).length == 0)
    {
        $.toptip('请选择经销商','warning');
        return false;
    }
    if($.trim( salesOffice).length == 0)
    {
        $.toptip('请填写销售点','warning');
        $('#salesOffice').focus();
        return false;
    }
    if($.trim( sale_id).length == 0)
    {
        $.toptip('请选择销售渠道','warning');
        return false;
    }
    if($.trim( activity_item_id).length == 0)
    {
        $.toptip('请选择销售品牌','warning');
        return false;
    }
    if($.trim( phone).length == 0)
    {
        $.toptip('请填写手机号码','warning');
        $('#phone').focus();
        return false;
    }
    if(imgs.length == 0)
    {
        $.toptip('请至少上传一张图片','warning');
        return false;
    }
    if(ajax_status) {
        $.showLoading();
        var data = {};
        data['office_id'] = office_id;
        data['dealers_id'] = dealers_id;
        data['sale_id'] = sale_id;
        data['product_id'] = product_id;
        data['activity_item_id'] = activity_item_id;
        data['salesOffice'] = salesOffice;
        data['phone'] = phone;
        data['imgs'] = imgs;
        data['_token'] = $('#_token').val();
        ajax_status = false;
        $.ajax({
            'url': '/index/activity/saveClockInData',
            'data': data,
            'method': 'post',
            'dataType': 'json',
            success: function (data) {
                $.hideLoading();
                if (data.code == '0000') {
                    $.toast("打卡成功", function () {
                        location.href = '/index/index';
                    });
                }
                else
                {
                    $.alert(data.mes);
                }
            },
            error: function () {
                $.alert('网络较慢，请检查后重试');
                $.hideLoading();
            }
        })
    }
});