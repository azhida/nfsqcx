<!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>下班班打卡</title>
    <link rel="stylesheet" href="{{ asset('common/css/normalize.min.css') }}">
    <link rel="stylesheet" href="{{ asset('common/css/swiper.min.css') }}">
    <link rel="stylesheet" href="{{ asset('common/css/public.css') }}">
    <link rel="stylesheet" href="{{ asset('common/css/iconfont.css') }}">
    <link rel="stylesheet" href="{{ asset('common/css/weui.min.css') }}">
    <link rel="stylesheet" href="{{ asset('common/css/jquery-weui.min.css') }}">
    <link rel="stylesheet" href="{{ asset('index/css/clock_in.css') }}">
</head>
<body>
<section id="word_in">
    <header>
        <a href="javascript:void(0)" onclick="goBack()"><img style="height: 20px;" src="{{ asset('index/imgs/go_back.png') }}"></a>
        <div id="header_text" style="font-size: 24px;">下班打卡</div>
        <div> </div>
    </header>


    <div>
        <form id="form">
            <input type="hidden" id="office_id" name="office_id" value="{{ $office_info->id ?? 0 }}">
            <input type="hidden" id="dealers_id" name="dealers_id" value="">
            <input type="hidden" id="sale_id" name="sale_id" value="">
            <input type="hidden" id="activity_item_id" name="activity_item_id" value="">
            <input type="hidden" id="product_id" name="product_id" value="0">
            <input type="hidden" id="imgs" name="imgs" value="">
            <input type="hidden" id="img_1" name="img_1" value="">
            <input type="hidden" id="img_2" name="img_2" value="">
            <input type="hidden" id="img_3" name="img_3" value="">
            <input type="hidden" id="_token" name="_token" value="{{ csrf_token() }}">

            <div class="weui-cells product_list">
                <a class="weui-cell weui-cell_access" href="javascript:;" cat_id="1">
                    <div class="weui-cell__hd"><img src="{{ asset('index/imgs/water.png') }}"></div>
                    <div class="weui-cell__bd"><p>水系列</p></div>
                    <div class="weui-cell__ft">
                    </div>
                </a>
                <div class="product_list_son water">
                    <!--<ul class="upladingData-list" id="water_list"></ul>-->
                </div>

                <a class="weui-cell weui-cell_access" href="javascript:;" cat_id="2">
                    <div class="weui-cell__hd"><img src="{{ asset('index/imgs/functional_drinks.png') }}"></div>
                    <div class="weui-cell__bd"><p>功能饮料系列</p></div>
                    <div class="weui-cell__ft">
                    </div>
                </a>
                <div class="product_list_son functional_drinks">
                    <!--<ul class="upladingData-list" id="functional_drinks_list"></ul>-->
                </div>

                <a class="weui-cell weui-cell_access" href="javascript:;" cat_id="3">
                    <div class="weui-cell__hd"><img src="{{ asset('index/imgs/tea.png') }}"></div>
                    <div class="weui-cell__bd"><p>茶系列</p></div>
                    <div class="weui-cell__ft">
                    </div>
                </a>
                <div class="product_list_son tea">
                    <!--<ul class="upladingData-list" id="tea_list"></ul>-->
                </div>

                <a class="weui-cell weui-cell_access" href="javascript:;" cat_id="4">
                    <div class="weui-cell__hd"><img src="{{ asset('index/imgs/fruit_juice.png') }}"></div>
                    <div class="weui-cell__bd"><p>果汁系列</p></div>
                    <div class="weui-cell__ft">
                    </div>
                </a>
                <div class="product_list_son fruit_juice">
                    <!--<ul class="upladingData-list" id="fruit_juice_list"></ul>-->
                </div>

                <a class="weui-cell weui-cell_access" href="javascript:;" cat_id="5">
                    <div class="weui-cell__hd"><img src="{{ asset('index/imgs/fruit_juice.png') }}"></div>
                    <div class="weui-cell__bd"><p>奶系列</p></div>
                    <div class="weui-cell__ft">
                    </div>
                </a>
                <div class="product_list_son milk">
                    <!--<ul class="upladingData-list" id="fruit_juice_list"></ul>-->
                </div>

            </div>

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
                        <input id="salesOffice" class="weui-input sale_text" name="salesOffice" type="text" placeholder="请填写售点">
                    </div>
                </a>

                <a class="weui-cell weui-cell_access" data-type="6" href="javascript:;">
                    <div class="weui-cell__hd"><img src="{{ asset('index/imgs/sale_data.png') }}"></div>
                    <div class="weui-cell__bd">
                        <p>当日销量</p>
                    </div>
                    <div class="weui-cell__ft">
                        请填写
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

            <div class="weui-cells weui-cells_form saler_info">
                <div class="weui-cell">
                    <div class="weui-cell__hd"><label class="weui-label">促销员姓名</label></div>
                    <div class="weui-cell__bd">
                        <input class="weui-input" type="text" placeholder="请输入姓名" id="names" name="names">
                    </div>
                </div>
                <div class="weui-cell">
                    <div class="weui-cell__hd"><label class="weui-label">手机号</label></div>
                    <div class="weui-cell__bd">
                        <input class="weui-input" type="number" pattern="[0-9]*" placeholder="请输入手机号" id="phone" name="phone">
                    </div>
                </div>
                <div class="weui-cell weui-cell_vcode">
                    <div class="weui-cell__hd">
                        <label class="weui-label">验证码</label>
                    </div>
                    <div class="weui-cell__bd">
                        <input class="weui-input" type="number" name="code" placeholder="请输入验证码" id="code" pattern="\d*">
                    </div>
                    <div class="weui-cell__ft">
                        <button class="weui-vcode-btn" id="get-code" type="button" onclick="getCode();">获取验证码</button>
                    </div>
                </div>
            </div>


        </form>

    </div>




    <!--现场促销照片-->
    <section class="pic">
        <p class="pic-tit" style="color: black;">现场促销照片 : </p>
        <ul>
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
        </ul>
    </section>


    <!--打卡-->
    <div class="clock-in">
        <button class="btn post_clock_out_data" style="background-color: red;color: #fff;" onclick="postClockOutData()">打卡</button>
        <button class="btn save_sale_data" style="background-color: red;color: #fff;display: none;" onclick="saveSaleData()">保存销量</button>
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
<script src="{{ asset('index/js/clock_out.js') }}"></script>

</body>
</html>