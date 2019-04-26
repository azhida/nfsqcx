<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>上班打卡</title>
    <link rel="stylesheet" href="{{ asset('common/css/normalize.min.css') }}">
    <link rel="stylesheet" href="{{ asset('common/css/swiper.min.css') }}">
    <link rel="stylesheet" href="{{ asset('common/css/weui.min.css') }}">
    <link rel="stylesheet" href="{{ asset('common/css/jquery-weui.min.css') }}">
    <link rel="stylesheet" href="{{ asset('common/css/public.css') }}">
    <link rel="stylesheet" href="{{ asset('common/css/iconfont.css') }}">
    <link rel="stylesheet" href="{{ asset('index/css/clock_in.css') }}">
</head>
<body>
<section id="word_in">
    <header>
        <a href="javascript:history.go(-1)"><img style="height: 20px;" src="{{ asset('index/imgs/go_back.png') }}"></a>
        <div style="font-size: 24px;">上班打卡</div>
        <div> </div>
    </header>
    <input type="hidden" id="office_id" value="{{ $office_info->id ?? 0 }}">
    <input type="hidden" id="dealers_id" value="">
    <input type="hidden" id="sale_id" value="">
    <input type="hidden" id="activity_item_id" value="">
    <input type="hidden" id="product_id" value="0">
    <input type="hidden" id="img_1" name="img_1" value="">
    <input type="hidden" id="img_2" name="img_2" value="">
    <input type="hidden" id="img_3" name="img_3" value="">
    <input type="hidden" id="_token" value="{{ csrf_token() }}">

    <div class="weui-cells select_list">
        <a class="weui-cell weui-cell_access" data-type="0" href="javascript:;">
            <div class="weui-cell__hd"><img  src="{{ asset('index/imgs/office.png') }}"></div>
            <div class="weui-cell__bd">
                <p>办事处</p>
            </div>
            <div class="weui-cell__ft office_text">
                {{ $office_info->name ?? '请选择办事处' }}
            </div>
        </a>
        <a class="weui-cell weui-cell_access" data-type="1" href="javascript:;">
            <div class="weui-cell__hd"><img src="{{ asset('index/imgs/dealers.png') }}"></div>
            <div class="weui-cell__bd">
                <p>经销商</p>
            </div>
            <div class="weui-cell__ft dealers_text">
                请选择
            </div>
        </a>

        <a class="weui-cell weui-cell_access" data-type="2" href="javascript:;">
            <div class="weui-cell__hd"><img src="{{ asset('index/imgs/sale.png') }}"></div>
            <div class="weui-cell__bd">
                <p>售点</p>
            </div>
            <div class="weui-cell__bd">
                <input id="salesOffice" class="weui-input sale_text" type="text" placeholder="请填写售点">
            </div>
        </a>
        <a class="weui-cell weui-cell_access" data-type="3" href="javascript:;">
            <div class="weui-cell__hd"><img src="{{ asset('index/imgs/channel.png') }}"></div>
            <div class="weui-cell__bd">
                <p>渠道</p>
            </div>
            <div class="weui-cell__ft channel_text">
                请选择
            </div>
        </a>
        <a class="weui-cell weui-cell_access" data-type="4" href="javascript:;">
            <div class="weui-cell__hd"><img src="{{ asset('index/imgs/brand.png') }}"></div>
            <div class="weui-cell__bd">
                <p>品牌</p>
            </div>
            <div class="weui-cell__ft brand_text">
                请选择
            </div>
        </a>
        <a class="weui-cell weui-cell_access" data-type="2" href="javascript:;">
            <div class="weui-cell__hd"><img src="{{ asset('index/imgs/phone.png') }}"></div>
            <div class="weui-cell__bd">
                <p>手机号</p>
            </div>
            <div class="weui-cell__bd">
                <input id="phone" class="weui-input sale_text" type="text" maxlength="11" pattern="/^[1-9]{1}[0-9]*$/" placeholder="请填写手机号">
            </div>
        </a>
        <a class="weui-cell weui-cell_access" data-type="5" href="javascript:;">
            <div class="weui-cell__hd"><img src="{{ asset('index/imgs/time.png') }}"></div>
            <div class="weui-cell__bd">
                <p>当前时间</p>
            </div>
            <div><span id="date"></span><span id="time"></span></div>
        </a>
    </div>

    <!--<input type="file" name="pic222" id="a33333" accept="image/*" src="submit.gif" alt="Submit" />-->

    <!--现场促销照片-->
    <section class="pic">
        <p class="pic-tit" style="color: black;">现场促销照片 : </p>
        <ul>
            <!--<li>-->
            <!--<img src="./img/img.png" alt="">-->
            <!--<span class="delImg">删除</span>-->
            <!--</li>-->

            <!--<li class="addImg">-->
            <!--<form id="upImg"  onclick="uploadImg(this)">-->
            <!--<label for="addImg">-->
            <!--<i class="iconfont icon-add"></i>-->
            <!--<input type="file" name="pic" id="addImg" accept="image/*" src="submit.gif" alt="Submit" />-->
            <!--</label>-->
            <!--</form>-->
            <!--</li>-->

            <li class="addImg addImg1">
                <form id="upImg1">
                    <!--<label for="addImg">-->
                    <i class="iconfont icon-add"></i>
                    <input type="file" name="pic1" id="addImg1" accept="image/*" src="submit.gif" alt="Submit" onchange="changeAddImg(event, 1)" />
                    <!--</label>-->
                </form>
                <span style="position: absolute;bottom: -25px; color: black;left: 0;">店头照</span>
            </li>
            <li id="list_id1" num="1" style="display: none;">
                <span class="img_text">店头照</span>
                <img src="" alt="" class="pics" data-id="list_id1">
            </li>

            <li class="addImg addImg2">
                <form id="upImg2">
                    <!--<label for="addImg">-->
                    <i class="iconfont icon-add"></i>
                    <input type="file" name="pic2" id="addImg2" accept="image/*" src="submit.gif" alt="Submit" onchange="changeAddImg(event, 2)" />
                    <!--</label>-->
                </form>
                <span style="position: absolute;bottom: -25px; color: black;left: 0;">现场布建</span>
            </li>
            <li id="list_id2" num="2" style="display: none;">
                <span class="img_text">现场布建</span>
                <img src="" alt="" class="pics" data-id="list_id2">
            </li>

            <li class="addImg addImg3">
                <form id="upImg3">
                    <!--<label for="addImg">-->
                    <i class="iconfont icon-add"></i>
                    <input type="file" name="pic3" id="addImg3" accept="image/*" src="submit.gif" alt="Submit" onchange="changeAddImg(event, 3)" />
                    <!--</label>-->
                </form>
                <span style="position: absolute;bottom: -25px; color: black;left: 0;">促销员照</span>
            </li>
            <li id="list_id3" num="3" style="display: none;">
                <span class="img_text">促销员照</span>
                <img src="" alt="" class="pics" data-id="list_id3">
            </li>

            <!--<li class="addImg" style="display: none">-->
            <!--<form id="upImg">-->
            <!--<label for="addImg">-->
            <!--<i class="iconfont icon-add"></i>-->
            <!--<input class="addimg_input" num="0" type="file" name="pic" id="addImg" accept="image/*" src="submit.gif" alt="Submit" />-->
            <!--</label>-->
            <!--</form>-->
            <!--</li>-->

            <!--<li class="addImg addImg1">-->
            <!--<form id="upImg1" onclick="uploadImg(1)">-->
            <!--<label for="addImg">-->
            <!--<i class="iconfont icon-add"></i>-->
            <!--<input class="addimg_input" type="file" name="pic1" id="addImg1" accept="image/*" src="submit.gif" alt="Submit" />-->
            <!--</label>-->
            <!--</form>-->
            <!--<span style="position: absolute;bottom: -25px; color: black;">店头照</span>-->
            <!--</li>-->

            <!--<li class="addImg addImg2">-->
            <!--<form id="upImg2"  onclick="uploadImg(2)">-->
            <!--<label for="addImg">-->
            <!--<i class="iconfont icon-add"></i>-->
            <!--<input class="addimg_input" type="file" name="pic2" id="addImg2" accept="image/*" src="submit.gif" alt="Submit" />-->
            <!--</label>-->
            <!--</form>-->
            <!--<span style="position: absolute;bottom: -25px; color: black;">现场布建</span>-->
            <!--</li>-->

            <!--<li class="addImg addImg3">-->
            <!--<form id="upImg3" onclick="uploadImg(3)">-->
            <!--<label for="addImg">-->
            <!--<i class="iconfont icon-add"></i>-->
            <!--<input class="addimg_input" type="file" name="pic3" id="addImg3" accept="image/*" src="submit.gif" alt="Submit" />-->
            <!--</label>-->
            <!--</form>-->
            <!--<span>促销员照</span>-->
            <!--</li>-->
        </ul>
    </section>


    <!--打卡-->
    <div class="clock-in">
        <button class="btn" style="background-color: red;color: #fff;">打卡</button>
    </div>
</section>

<!--选择弹窗-->
<section class="select-box">
    <header>
        <div></div>
        <div class="select-box-tit" style="font-size: 24px;">办事处选择</div>
        <!--<div> </div>-->
        <div class="select-box-choose" style="margin-right: 26px;" onclick="choose(this)">确定</div>
    </header>
    <div style="margin-top: 30px;">
        <ul id="html" style="margin-top: 30px;">
            <li>
                <p>办事处1</p>
                <i class="iconfont icon-check_circle active"></i>
            </li>



        </ul>
    </div>

    <!--<div class="select-box-btn">-->
    <!--<button class="btn">确定</button>-->
    <!--</div>-->
</section>

<div class="weui-gallery" style="display: none" id="big">
    <span class="weui-gallery__img" style="" id="bigimg"></span>
    <div class="weui-gallery__opr">
        <a href="javascript:" class="" id="bigback">返回</a>&nbsp;&nbsp;
        <a href="javascript:" class="" id="bigdel" data-url="" data-id="">删除</a>
    </div>
</div>


<script src="{{ asset('common/js/jquery.min.js') }}"></script>
<script src="{{ asset('common/js/jquery-weui.min.js') }}"></script>
<script src="{{ asset('common/js/swiper.min.js') }}"></script>
<script src="{{ asset('common/js/public.js') }}"></script>
<script src="{{ asset('index/js/clock_in.js') }}"></script>

</body>
</html>