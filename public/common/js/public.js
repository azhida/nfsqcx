function time(yy,mm,dd){
    if (!yy){
        yy = '-'
    }
    if (!mm){
        mm = '-'
    }
    if (!dd){
        dd = ''
    }
    var date = new Date();
    var y = date.getFullYear();
    var m = date.getMonth() + 1;
    var d = date.getDate();
    var h = date.getHours();
    var min = date.getMinutes();
    var second = date.getSeconds()
    if (m < 10){
        m = '0' + m;
    }
    if (d < 10){
        d = '0' + d;
    }
    if (h < 10){
        h = '0' + h;
    }
    if (min < 10){
        min = '0' + min;
    }
    if (second < 10){
        second = '0' + second;
    }

    var day = new Array("星期日", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六")[date.getDay()]

    $('#date').text(y + '-' + m + '-' + d);
    $('#time').text(h + ':' + min + ':' + second);
    $('#day').text(day);

    setTimeout(function () {
        time(yy, mm, dd);
    }, 1000);

}

$('.iicon-back').click(function () {
    history.go(-1);
});



// maxLen ：最大长度；maxSize：最大尺寸
function ImgToBase64(file, maxLen, maxSize, callBack) {
    var img = new Image();

    var reader = new FileReader();//读取客户端上的文件
    reader.onload = function () {
        var url = reader.result;//读取到的文件内容.这个属性只在读取操作完成之后才有效,并且数据的格式取决于读取操作是由哪个方法发起的.所以必须使用reader.onload，
        img.src = url;//reader读取的文件内容是base64,利用这个url就能实现上传前预览图片
    };
    img.onload = function () {
        //生成比例
        var width = img.width, height = img.height;

        //计算缩放比例
        var rate = 1;

        // 根据最大图片长度（图片长宽）计算
        // if (width >= height) {
        //     if (width > maxLen) {
        //         rate = maxLen / width;
        //     }
        // } else {
        //     if (height > maxLen) {
        //         rate = maxLen / height;
        //     }
        // };

        // 根据最大尺寸（图片大小）计算
        if (file.size > maxSize * 1024 * 1024) {
            rate = maxSize * 1024 * 1024 / file.size;
        }

        img.width = width * rate;
        img.height = height * rate;

        //生成canvas
        var canvas = document.createElement("canvas");
        var ctx = canvas.getContext("2d");
        canvas.width = img.width;
        canvas.height = img.height;
        ctx.drawImage(img, 0, 0, img.width, img.height);
        var base64 = canvas.toDataURL('image/jpeg', 0.9);
        callBack(base64);
    };
    reader.readAsDataURL(file);
}