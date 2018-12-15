<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>{$seoinfo['seotitle']}-{$GLOBALS['cfg_webname']}</title>
    {if $seoinfo['keyword']}
    <meta name="keywords" content="{$seoinfo['keyword']}"/>
    {/if}
    {if $seoinfo['description']}
    <meta name="description" content="{$seoinfo['description']}"/>
    {/if}
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    {Common::css('amazeui.css,style.css,../js/layer/need/layer.css')}
    {Common::css('../js/mobiscroll/css/mobiscroll.frame.css,../js/mobiscroll/css/mobiscroll.frame.android.css')}
    {Common::css('../js/mobiscroll/css/mobiscroll.scroller.css,../js/mobiscroll/css/mobiscroll.scroller.android.css')}
    {Common::js('jquery.min.js,amazeui.js,template.js,layer/layer.m.js,common.js,jquery.validate.min.js')}
    {Common::js('mobiscroll/js/mobiscroll.core.js,mobiscroll/js/mobiscroll.frame.js,mobiscroll/js/mobiscroll.scroller.js')}
    {Common::js('mobiscroll/js/mobiscroll.util.datetime.js,mobiscroll/js/mobiscroll.datetimebase.js,mobiscroll/js/mobiscroll.datetime.js')}
    {Common::js('mobiscroll/js/mobiscroll.frame.android.js,mobiscroll/js/i18n/mobiscroll.i18n.zh.js')}
</head>

<body>

{request "pub/header/typeid/$typeid"}


<section>

    <div class="mid_content">

        <div class="sr_homeTop_pic"><img src="{$GLOBALS['cfg_public_url']}images/siren.jpg"/></div>
        <!--顶部图片-->

        <form id="st_form" method="post" action="{$cmsurl}customize/ajax_do_add" enctype="application/x-www-form-urlencoded">
            <div class="customMade_con">
                <h3 class="made_tit"><i>1</i><span>您的旅行计划</span></h3>

                <div class="made_con">
                    <ul>
                        <li>
                            <strong class="bt">目的地</strong>
                            <input type="text" class="mdd" name="dest" id="dest" placeholder="必填,您要去的地方"/>
                        </li>
                        <li>
                            <strong class="bt">出发地点</strong>
                            <input type="text" class="mdd" name="startplace" id="startplace" placeholder="必填,您的出发地点"/>
                        </li>
                        <li>
                            <strong class="bt">出发时间</strong>
                            <input type="text" class="date" name="starttime" id="starttime" placeholder="选择您的出发时间"/>
                        </li>
                        <li>
                            <strong class="bt">出行时长</strong>
                            <span class="before">
                                <a class="sub" href="javascript:;">-</a>
                                <input type="text" name="days" id="days" readonly="readonly" value="1"/>
                                <a class="add" href="javascript:;">+</a>
                                <em>天左右</em>
                            </span>
                        </li>
                        <li>
                            <strong class="bt">成人数</strong>
                            <span class="before">
                                <a class="sub" href="javascript:;">-</a>
                                <input type="text" name="adultnum" id="adultnum" readonly="readonly" value="1"/>
                                <a class="add" href="javascript:;">+</a>
                                <em>人</em>
                            </span>
                        </li>
                        <li>
                            <strong class="bt">儿童数</strong>
                            <span class="before">
                                <a class="sub" href="javascript:;">-</a>
                                <input type="text" name="childnum" id="childnum" readonly="readonly" value="0"/>
                                <a class="add" href="javascript:;">+</a>
                                <em>人</em>
                            </span>
                        </li>
                        <li>
                            <dl>
                                <dt>交通方式</dt>
                                <dd class="planerank">
                                    <input type="hidden" name="planerank" id="planerank" value="飞机">
                                    <a class="on" href="javascript:;"><span>飞机</span></a>
                                    <a href="javascript:;"><span>火车</span></a>
                                    <a href="javascript:;"><span>自驾</span></a>
                                    <a href="javascript:;"><span>游轮</span></a>
                                </dd>
                            </dl>
                        </li>
                        <li>
                            <dl>
                                <dt>酒店星级</dt>
                                <dd class="hotelrank">
                                    <input type="hidden" name="hotelrank" id="hotelrank" value="三星级">
                                    <a class="on" href="javascript:;"><span>三星级</span></a>
                                    <a href="javascript:;"><span>四星级</span></a>
                                    <a href="javascript:;"><span>五星级</span></a>
                                    <a href="javascript:;"><span>五星级以上</span></a>
                                    <a href="javascript:;"><span>快捷经济型</span></a>
                                    <a href="javascript:;"><span>其它</span></a>
                                </dd>
                            </dl>
                        </li>
                        <li>
                            <dl>
                                <dt>需要房型</dt>
                                <dd class="room">
                                    <input type="hidden" name="room" id="room" value="单人">
                                    <a class="on" href="javascript:;"><span>单人</span></a>
                                    <a href="javascript:;"><span>双人大床房</span></a>
                                    <a href="javascript:;"><span>双人分床房</span></a>
                                    <a href="javascript:;"><span>套间</span></a>
                                    <a href="javascript:;"><span>海景房</span></a>
                                    <a href="javascript:;"><span>其它</span></a>
                                </dd>
                            </dl>
                        </li>
                        <li>
                            <dl>
                                <dt>用餐形式</dt>
                                <dd class="food">
                                    <input type="hidden" name="food" id="food" value="自理">
                                    <a class="on" href="javascript:;"><span>自理</span></a>
                                    <a href="javascript:;"><span>部分自理</span></a>
                                    <a href="javascript:;"><span>全包用餐</span></a>
                                </dd>
                            </dl>
                        </li>
                    </ul>
                </div>
            </div>
            <!--第三步-->

            <div class="customMade_con">
                <h3 class="made_tit"><i>2</i><span>您的联系方式</span></h3>

                <div class="made_con">
                    <ul>
                        <li>
                            <strong class="bt">您的称呼</strong>
                            <input type="text" class="mdd" name="contactname" id="contactname" placeholder="必填,方便客服与您联系"/>
                        </li>
                        <li>
                            <strong class="bt">性别</strong>
                            <span class="sex">
                                <input type="hidden" name="sex" id="sex" value="先生">
                                <a class="on" href="javascript:;">先生</a>
                                <a href="javascript:;">女士</a>
                            </span>
                        </li>
                        <li>
                            <strong class="bt">所在地点</strong>
                            <input type="text" class="mdd" name="address" id="address" placeholder="必填,方便客服与您联系"/>
                        </li>
                        <li>
                            <strong class="bt">手机号码</strong>
                            <input type="text" class="mdd" name="phone" id="phone" placeholder="必填,方便客服与您联系"/>
                        </li>
                        <li>
                            <strong class="bt">E-mail</strong>
                            <input type="text" class="mdd" name="email" id="email" placeholder="必填,电子邮箱"/>
                        </li>
                        <li>
                            <dl>
                                <dt>合适的联系时间</dt>
                                <dd class="contacttime">
                                    <input type="hidden" name="contacttime" id="contacttime" value="9:00-12:00">
                                    <a class="on" href="javascript:;"><span>9:00-12:00</span></a>
                                    <a href="javascript:;"><span>14:00-18:00</span></a>
                                    <a href="javascript:;"><span>19:00-22:00</span></a>
                                </dd>
                            </dl>
                        </li>
                        <li>
                            <dl>
                                <dt>其他要求</dt>
                                <dd>
                                    <textarea name="content" id="content" cols="" rows="" placeholder="您还有其他的要求吗？"></textarea>
                                </dd>
                            </dl>
                        </li>
                        <li>
                            <strong class="bt">验证码</strong>
                            <input type="text" class="mdd" name="code" id="code" placeholder="验证码"/><img
                                class="captcha yzm_pic cursor" src="" height="30"/>
                        </li>
                    </ul>
                </div>
            </div>
            <!--第二步-->
            <div class="error_txt"></div>

            <div class="sr_submit"><input type="submit" class="btn_save" value="提交订制"/></div>
        </form>

    </div>

</section>

{request 'pub/code'}
{request 'pub/footer'}

<script>
    $(function(){
        function goto(){
            location.href = SITEURL;
        }
        //时间选择器
        var stdate = new Date();
        $('#starttime').mobiscroll().date({
            theme: 'android',
            mode: 'scroller',
            display: 'modal',
            lang: 'zh',
            dateFormat: 'yy-mm-dd',
            minDate: new Date(stdate.getFullYear(), stdate.getMonth(), stdate.getDate())
        });

        //验证码切换
        $('.captcha').attr('src', ST.captcha(SITEURL+'captcha'));
        $('.captcha').click(function () {
            $(this).attr('src', ST.captcha($(this).attr('src')));
        });

        $(".sub").click(function () {
            var obj = $(this).parent().children('input');
            if (parseInt($(obj).val()) > 0) {
                $(obj).val(parseInt($(obj).val()) - 1);
            }
        });
        $(".add").click(function () {
            var obj = $(this).parent().children('input');
            $(obj).val(parseInt($(obj).val()) + 1);
        });

        //交通方式
        $("body").delegate(".planerank a", 'click', function () {
            $(this).siblings().removeClass('on');
            $(this).addClass('on');
            $("#planerank").val($(this).children('span').html());
        });

        //酒店星级
        $("body").delegate(".hotelrank a", 'click', function () {
            $(this).siblings().removeClass('on');
            $(this).addClass('on');
            $("#hotelrank").val($(this).children('span').html());
        });

        //需要房型
        $("body").delegate(".room a", 'click', function () {
            $(this).siblings().removeClass('on');
            $(this).addClass('on');
            $("#room").val($(this).children('span').html());
        });

        //用餐形式
        $("body").delegate(".food a", 'click', function () {
            $(this).siblings().removeClass('on');
            $(this).addClass('on');
            $("#food").val($(this).children('span').html());
        });

        //性别
        $("body").delegate(".sex a", 'click', function () {
            $(this).siblings().removeClass('on');
            $(this).addClass('on');
            $("#sex").val($(this).html());
        });

        //合适的联系时间
        $("body").delegate(".contacttime a", 'click', function () {
            $(this).siblings().removeClass('on');
            $(this).addClass('on');
            $("#contacttime").val($(this).children('span').html());
        });

        //提交定制
        $("body").delegate(".btn_save", 'click', function () {
            //验证
            $('#st_form').validate({
                rules: {
                    dest: 'required',
                    startplace: 'required',
                    starttime: {
                        required: true,
                        dateISO: true
                    },
                    days: {
                        required: true,
                        min: 1
                    },
                    adultnum: {
                        required: true,
                        min: 1
                    },
                    childnum: {
                        required: true,
                        min: 0
                    },
                    planerank: 'required',
                    hotelrank: 'required',
                    room: 'required',
                    food: 'required',
                    contactname: 'required',
                    sex: 'required',
                    address: 'required',
                    phone: {
                        required: true,
                        mobile: true
                    },
                    email: {
                        required: true,
                        email: true
                    },
                    contacttime: 'required',
                    code: 'required'
                },
                messages: {
                    dest: '{__("error_customize_dest_not_empty")}',
                    startplace: '{__("error_customize_startplace_not_empty")}',
                    starttime: {
                        required: '{__("error_customize_starttime_not_empty")}',
                        dateISO: '{__("error_date")}'
                    },
                    days: {
                        required: '{__("error_customize_days_digit")}',
                        min: '{__("error_customize_days_digit")}'
                    },
                    adultnum:{
                        required: '{__("error_adultnum_digit")}',
                        min: '{__("error_adultnum_digit")}'
                    },
                    childnum:{
                        required: '{__("error_childnum_digit")}',
                        min: '{__("error_childnum_digit")}'
                    },
                    planerank: '{__("error_customize_planerank_not_empty")}',
                    hotelrank: '{__("error_customize_hotelrank_not_empty")}',
                    room: '{__("error_customize_room_not_empty")}',
                    food: '{__("error_customize_food_not_empty")}',
                    contactname: '{__("error_customize_contactname_not_empty")}',
                    sex: '{__("error_customize_sex_not_empty")}',
                    address: '{__("error_customize_address_not_empty")}',
                    phone: {
                        required: '{__("error_user_phone")}',
                        mobile: '{__("error_user_phone")}'
                    },
                    email:  {
                        required: '{__("error_user_email")}',
                        email: '{__("error_user_email")}'
                    },
                    contacttime: '{__("error_customize_contacttime_not_empty")}',
                    code: '{__("error_code")}'

                },
                errorElement: "em",
                errorPlacement: function (error, element) {
                    var content = $('#st_form').find('.error_txt:eq(0)').html();
                    console.log(element)
                    if (content == '') {
                        $('#st_form').find('.error_txt:eq(0)').html('<i></i>');
                        error.appendTo($('#st_form').find('.error_txt:eq(0)'));
                    }
                },
                showErrors: function (errorMap, errorList) {
                    if (errorList.length < 1) {
                        $('#st_form').find('.error_txt:eq(0)').html('');
                    } else {
                        this.defaultShowErrors();
                    }
                }, submitHandler: function (form) {
                    //表单提交句柄,为一回调函数，带一个参数：form
                    $.post('{$cmsurl}customize/ajax_check_code', {
                        'code': $.trim($("#code").val())
                    }, function (data) {
                        if(data ==  true){
                            $.post('{$cmsurl}customize/ajax_do_add', {
                                'dest': $('#dest').val(),
                                'startplace': $('#startplace').val(),
                                'starttime': $('#starttime').val(),
                                'days': $('#days').val(),
                                'adultnum': $('#adultnum').val(),
                                'childnum': $('#childnum').val(),
                                'planerank': $('#planerank').val(),
                                'hotelrank': $('#hotelrank').val(),
                                'room': $('#room').val(),
                                'food': $('#food').val(),
                                'contactname': $('#contactname').val(),
                                'sex': $('#sex').val(),
                                'address': $('#address').val(),
                                'phone': $('#phone').val(),
                                'email': $('#email').val(),
                                'contacttime': $('#contacttime').val(),
                                'content': $('#content').val(),
                                'code': $('#code').val()
                            }, function (data) {
                                if (data.status == 1) {
                                    layer.open({
                                        content: '{__("success_customize_add_insert")}',
                                        time: 2,
                                        end:function(){
                                            goto();
                                        }
                                    });
                                }else{
                                    layer.open({
                                        content: data.message,
                                        time: 2
                                    });
                                }
                            }, 'json')
                        }else{
                            layer.open({
                                content: '{__("error_code")}',
                                time: 2
                            });
                        }
                    }, 'json');
                }
            });
        });

    })
</script>
</body>
</html>
