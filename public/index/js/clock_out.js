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

    if (type == 6) {
        $('#word_in').show();
        // 展示产品列表，并填报当日销量
        showProductList();
        return false;
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

// 展示产品列表，并填报当日销量
function showProductList() {

    $('.select_list').hide();
    $('.product_list').show();
    $('.saler_info').hide();
    $('.pic').hide();
    $('#header_text').text('当日销量');

    $('.clock-in .post_clock_out_data').hide();
    $('.clock-in .save_sale_data').show();

    $('#word_in header a').attr('href', 'javascript:void(0)').attr('onclick', 'saveSaleData()');

    return false;
}

// 保存销量
function saveSaleData() {

    $('.select_list').show();
    $('.product_list').hide();
    $('.saler_info').show();
    $('.pic').show();
    $('#header_text').text('下班打卡');

    $('.clock-in .post_clock_out_data').show();
    $('.clock-in .save_sale_data').hide();

    $('#word_in header a').attr('href', 'javascript:void(0)').attr('onclick', 'goBack()');
}

// 返回上一步
function goBack() {
    history.go(-1);
}

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
    console.log(data);
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


// 提交下班打卡数据
function postClockOutData() {
    var ajax_status = true;
    var office_id = $('#office_id').val();
    var dealers_id = $('#dealers_id').val();
    var sale_id = $('#sale_id').val();
    var product_id = $('#product_id').val();
    var activity_item_id = $('#activity_item_id').val();
    var salesOffice = $('#salesOffice').val();
    if($.trim( office_id).length == 0) {
        $.toptip('请选择办事处', 'warning');
        return false;
    }
    if($.trim( dealers_id).length == 0) {
        $.toptip('请选择经销商', 'warning');
        return false;
    }
    if($.trim( salesOffice).length == 0) {
        $.toptip('请填写销售点', 'warning');
        $('#salesOffice').focus();
        return false;
    }
    if($.trim( sale_id).length == 0) {
        // $.toptip('请选择销售渠道','warning');
        // return false;
    }
    if($.trim( activity_item_id).length == 0) {
        // $.toptip('请选择销售品牌','warning');
        // return false;
    }
    if(imgs.length == 0) {
        $.toptip('请至少上传一张图片', 'warning');
        return false;
    }
    if(ajax_status) {
        $.showLoading();

        $('#imgs').val(imgs);

        ajax_status = false;
        $.ajax({
            'url': '/index/activity/saveClockOutData',
            'data': $('#form').serialize(),
            'method': 'post',
            'dataType': 'json',
            success: function (data) {
                $.hideLoading();
                if (data.code == '0000') {
                    $.toast("打卡成功", function () {
                        location.href = '/index/index';
                    });
                } else {
                    $.alert(data.mes);
                }
            },
            error: function () {
                $.alert('网络较慢，请检查后重试');
                $.hideLoading();
            }
        })
    }
}


$(function () {
    // 获取 口味列表
    getFlavorList();

    // 获取 水系列 产品列表
    getUploadingData(1);

    // 点击展开 产品列表
    $('.product_list .weui-cell_access').click(function () {
        var obj = $(this).next('.product_list_son');
        if ($(obj).attr('is_show') != 1) {
            console.log($(this).next('.product_list_son').html() == '')
            if ($(this).next('.product_list_son').children('.upladingData-list').html() == '') {
                getUploadingData($(this).attr('cat_id'));
            }
            $(obj).show().attr('is_show', 1);
        } else {
            $(obj).hide().attr('is_show', 0);
        }

    });
});

// 获取 口味列表
function getFlavorList() {

    $.ajax({
        url: '/index/activity/getFlavorList',
        type: 'post',
        dataType: 'json',
        data: {_token: $('#_token').val()},
        success: function (data) {
            console.log(data);

            if (data.code == '0000') {

                var data = data.data;
                for (var i in data) {
                    if (data[i].cat_id == 1) {
                        $('.water').append('<ul class="upladingData-list flavor_id_' + data[i].id + '"></ul>');
                    } else if (data[i].cat_id == 2) {
                        $('.functional_drinks').append('<ul class="upladingData-list flavor_id_' + data[i].id + '"></ul>');
                    } else if (data[i].cat_id == 3) {
                        $('.tea').append('<ul class="upladingData-list flavor_id_' + data[i].id + '"></ul>');
                    } else if (data[i].cat_id == 4) {
                        $('.fruit_juice').append('<ul class="upladingData-list flavor_id_' + data[i].id + '"></ul>');
                    }
                }

            }
        }
    })
}

// 获取 产品列表
function getUploadingData(cat_id) {
    console.log(cat_id)
    $.showLoading();
    $.ajax({
        url: '/index/activity/getUploadingData',
        type: 'post',
        data: {cat_id: cat_id, _token: $('#_token').val()},
        dataType: 'json',
        success: function (data) {
            console.log(data, data.code == '0000');
            if (data.code == '0000') {

                var water_list = '';
                var functional_drinks_list = '';
                var tea_list = '';
                var fruit_juice_list = '';

                var product_list = data.data;
                for (var i in product_list) {

                    var item = '<li>'
                        + '<label for="addImg1">'
                        // + '<img src="/static/' + product_list[i].img_url + '" alt="" class="product_img">'
                        + '<img src="' + product_list[i].oss_img_url + '" alt="" class="product_img">'
                        + '</label>'
                        + '<p>' + product_list[i].name + '</p>'
                        + '<input type="number" class="sales-volumes" placeholder="输入销售数量" name="product_num[]" value="0" pattern="\d*">'
                        + '<input type="hidden" name="product_id[]" class="upInput" id="addImg' + product_list[i].id + '" value="' + product_list[i].id + '"/>'
                        + '</li>';

                    // 按口味分类
                    console.log('.flavor_id_' + product_list[i].flavor_id);
                    $('.flavor_id_' + product_list[i].flavor_id).append(item);

                }

                // 获取屏幕宽度
                var win_width = $(window).width();
                var li_width = $('.upladingData-list li').width();

                var margin_left = (win_width - li_width * 4) / 5;
                console.log(win_width, li_width, margin_left);
                $('.upladingData-list li').css('margin-left', margin_left);

                $.hideLoading();
            }
        }
    })
}

// 获取短信验证码
function getCode() {
    var val = $('#phone').val();
    var ajax_status = true;
    if(ajax_status) {
        $.showLoading();
        var data = {};
        data['phone'] = val;
        data['sms_type'] = 3;
        data['_token'] = $('#_token').val();
        ajax_status = false;
        $.ajax({
            'url': '/index/sms/send',
            'data': data,
            'method': 'post',
            'dataType': 'json',
            success: function (data) {
                $.hideLoading();
                console.log(data);
                if(data.code == '0000') {
                    $('.get-code').hide();
                    $('.is-code').show();
                    countTime(60); // 倒计时
                } else {
                    $.alert(data.mes);
                }
            },
            error: function () {
                $.alert('系统错误');
                $.hideLoading();
            }
        })
    }
}

// 倒计时
function countTime(s) {
    if (s == 0){
        $('#get-code').text('获取验证码');
        return;
    }
    s--;
    $('#get-code').text(s + '秒后重新获取');
    setTimeout(function () {
        countTime(s)
    },1000)
}