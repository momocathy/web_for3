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
    {Common::css('amazeui.css,style.css,extend.css,../js/layer/need/layer.css')}
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
        <div class="fabu_xq_box">
            <form id="st_form" method="post" action="{$cmsurl}jieban/do_join" enctype="application/x-www-form-urlencoded">
                <div class="xq_faq">
                    <h3 class="tit"><strong>您准备去什么地方旅游？</strong><i></i></h3>

                    <div class="xq_con">
                        <ul class="xq_mdd_list" id="stop1">
                            <input id="kindlist" name="kindlist" type="hidden" value="">
                            {st:dest action="query" flag="dest" pid="0" typeid="$typeid" row="99"}
                            {loop $data $row}
                            <li data-id="{$row['id']}" data-py="{$row['pinyin']}" data-flag="dest"><a
                                    href="javascript:;">{$row['kindname']}</a></li>
                            {/loop}
                            {/st}
                        </ul>
                    </div>
                </div>

                <div class="xq_confirm" id="stop1_1" style="display: none"><i></i></div>

                <div class="xq_faq" id="stop2_div" style="display: none">
                    <h3 class="tit"><strong>选择要去这个区域的目的地</strong><i></i></h3>

                    <div class="xq_con">
                        <ul class="xq_mdd_list" id="stop2">
                        </ul>
                        <div class="write_in">
                            <p>没有想去的？直接说您想去哪里</p>
                            <input type="text" class="xq_text" name="userdest" id="userdest"/>
                            <a class="qd_btn btn_userdest" href="javascript:;">确定</a>
                        </div>
                    </div>
                </div>
                <!--第二项需求-->

                <input id="dest_mainid" name="dest_mainid" type="hidden" value="">
                <input id="dest_childid" name="dest_childid" type="hidden" value="">
                <div class="xq_confirm" id="stop2_1" style="display: none"><i></i></div>
                <!--第二项确认-->

                <div class="xq_faq" id="stop3_div" style="display: none">
                    <h3 class="tit"><strong>填写醒目的活动标题</strong><i></i></h3>

                    <div class="xq_con">
                        <div class="write_in">
                            <input type="text" class="xq_text" name="title" id="title" placeholder="活动标题"/>
                            <a class="qd_btn btn_title" href="javascript:;">确定</a>
                        </div>
                    </div>
                </div>
                <!--第三项需求-->

                <div class="xq_confirm" id="stop3_1" style="display: none">
                    <i></i>
                </div>
                <!--第三项确认-->

                <div class="xq_faq" id="stop4_div" style="display: none">
                    <h3 class="tit"><strong>计划旅行时间</strong><i></i></h3>

                    <div class="xq_con">
                        <div class="write_in">
                            <p><input type="text" class="xq_text kbtn" readonly="readonly" name="startdate" id="startdate"
                                      placeholder="请选择您的出发日期"/></p>

                            <p><input type="number" class="xq_text" name="vartime" id="vartime"
                                      placeholder="出发日期可以早或晚多少天"/><span>天</span></p>

                            <p><input type="number" class="xq_text" name="day" id="day"
                                      placeholder="请输入您的出行天数"/><span>天</span></p>
                            <a class="qd_btn btn_time" href="javascript:;">确定</a>
                        </div>
                    </div>
                </div>
                <!--第四项需求-->

                <div class="xq_confirm" id="stop4_1" style="display: none"><i></i></div>
                <!--第四项确认-->

                <div class="xq_faq" id="stop5_div" style="display: none">
                    <h3 class="tit"><strong>出行人数</strong><i></i></h3>

                    <div class="xq_con">
                        <div class="num_peo">
                            <p>
                                <em>成人：</em>
                                <span>
                                    <a class="sub" href="javascript:;">-</a><input type="text" name="adultnum" id="adultnum" readonly="readonly" value="1"/><a class="add" href="javascript:;">+</a>
                                </span>
                            </p>

                            <p>
                                <em>儿童：</em>
                                <span>
                                    <a class="sub" href="javascript:;">-</a><input type="text" name="childnum" id="childnum" readonly="readonly" value="0"/><a class="add" href="javascript:;">+</a>
                                </span>
                            </p>
                            <a class="qd_btn btn_num" href="javascript:;">确定</a>
                        </div>
                    </div>
                </div>
                <!--第五项需求-->

                <div class="xq_confirm" id="stop5_1" style="display: none"><i></i></div>
                <!--第五项确认-->

                <div class="xq_faq" id="stop6_div" style="display: none">
                    <h3 class="tit"><strong>您的行程需求（可多选）</strong><i></i></h3>

                    <div class="xq_con">
                        <ul class="xq_mdd_list" id="stop6_ul">

                        </ul>
                        <div class="write_in">
                            <p>以上都没有，自己写行程需求（多个需求用；分号隔开）</p>
                            <input type="text" class="xq_text" name="usertheme" id="usertheme" placeholder="活动行程"/>
                            <a class="qd_btn btn_themelist" href="javascript:;">确定</a>
                        </div>
                    </div>
                </div>
                <!--第六项需求-->

                <input id="themelist" name="themelist" type="hidden" value="">
                <input id="attrid" name="attrid" type="hidden" value="">
                <div class="xq_confirm" id="stop6_1" style="display: none">川藏线<i></i></div>
                <!--第六项确认-->

                <div class="xq_faq" id="stop7_div" style="display: none">
                    <h3 class="tit"><strong>请输入你的手机号码</strong><i></i></h3>

                    <div class="xq_con">
                        <div class="write_in">
                            <p><input type="tel" class="xq_text" name="mobile" id="mobile" placeholder="手机号码"/></p>

                            <p>
                                <input type="text" class="xq_text" name="msg" id="msg" placeholder="验证码"/>
                                {if $isopen}
                                <a class="yz_num" id="resend" href="javascript:;" do-send="true">获取短信验证码</a>
                                {else}
                                <img class="captcha yzm_pic cursor " src="" height="30"/>
                                {/if}
                            </p>
                            <input type="submit" style="display: none" class="btn_save" value="提交订制"/>
                            <a class="qd_btn btn_mobile" href="javascript:;">确定</a>
                        </div>
                    </div>
                </div>
                <!--第七项需求-->

                <div class="xq_faq" id="stop8_div" style="display: none">
                    <h3 class="tit"><strong>结伴信息发布成功</strong><i></i></h3>

                    <div class="xq_con">
                        <div class="write_in">
                            <p>你的结伴信息发布成功,我们会在工作时间和您电话沟通细节和初步行程报价,请注意接听。</p>
                            <a class="qd_btn btn_goto" href="javascript:;">回结伴首页</a>
                        </div>
                    </div>
                </div>
                <div class="error_txt"></div>
            </div>
            <!--第八项需求-->
        </form>
    </div>
    </div>
</section>

{request 'pub/code'}
{request 'pub/footer'}

<script type="text/html" id="tpl_li_item">
    {{each list as value i }}
    <li data-id="{{value.id}}" data-py="{{value.pinyin}}"><a href="javascript:;">{{value.kindname}}</a></li>
    {{/each}}
</script>
<script type="text/html" id="tpl_li_themelist">
    {{each list as value i }}
    <li data-id="{{value.id}}"><a href="javascript:;">{{value.attrname}}</a></li>
    {{/each}}
</script>
<script>
    var typeid = '{$typeid}';
    $(function () {
        //验证码切换
        $('.captcha').attr('src', ST.captcha(SITEURL+'captcha'));
        $('.captcha').click(function () {
            $(this).attr('src', ST.captcha($(this).attr('src')));
        });

        function goJieban(){
            location.href = SITEURL + 'jieban';
        }
        //时间选择初始化
        stdate = new Date(),
        $('#startdate').mobiscroll().date({
            theme: 'android',
            mode: 'scroller',
            display: 'modal',
            lang: 'zh',
            dateFormat: 'yy-mm-dd',
            minDate: new Date(stdate.getFullYear(), stdate.getMonth(), stdate.getDate())
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

        //您准备去什么地方旅游？
        $('#stop1 li').click(function () {
            $(this).siblings().children('a').removeClass('on');
            $(this).children('a').addClass('on');
            $("#kindlist").val($(this).attr('data-id'));
            $("#dest_mainid").val($(this).attr('data-id'));
            $("#stop1_1").css('display', 'block');
            $("#stop1_1").html($(this).children('a').html() + '<i></i>');

            $("#stop2_div").css('display', 'block');

            //读取子目的地
            get_next($(this).attr('data-id'), typeid);
        });

        //读取子目的地
        function get_next(id, typeid) {
            $("#stop2").empty();
            //读取热门线路
            if (id > 0) {
                //目的地获取
                $.getJSON(SITEURL + 'pub/ajax_get_dest', {
                    flag: 'dest',
                    destid: id,
                    typeid: typeid
                }, function (data) {
                    var html = template('tpl_li_item', data);
                    $('#stop2').html(html);
                })
            }
        }

        //选择要去这个区域的目的地
        $("body").delegate("#stop2 li", 'click', function () {
            $(this).siblings().children('a').removeClass('on');
            $(this).children('a').addClass('on');
            $("#userdest").val('');

            $("#stop2_1").html($(this).children('a').html() + '<i></i>');
            $("#kindlist").val($("#dest_mainid").val() + '-' + $(this).attr('data-id'));
            $("#dest_childid").val($(this).attr('data-id'));
            $('.error_txt').html('');
        });
        //选择要去这个区域的目的地
        $("body").delegate(".btn_userdest", 'click', function () {
            $("#stop2_1").html('<i></i>');
            if ($("#userdest").val() != '') {
                $("#stop2 li").siblings().children('a').removeClass('on');
                $("#stop2_1").html($("#userdest").val() + '<i></i>');
                $("#kindlist").val($("#stop1 li a.on").parent().attr('data-id'));
            } else {
                var ischange = true;

                $("#stop2 li a.on").each(function () {
                    ischange = false;
                    $("#stop2_1").html($(this).html() + '<i></i>');
                    $("#kindlist").val($("#dest_mainid").val() + ',' + $(this).parent().attr('data-id'));
                    $("#dest_childid").val($(this).parent().attr('data-id'));
                });

                if (ischange) {
                    $('.error_txt').html('<i></i><em id="title-error" class="error">{__("error_userdest_not_empty")}</em>');
                    return false;
                }
                $('.error_txt').html('');
            }
            $("#stop2_1").css('display', 'block');
            $("#stop3_div").css('display', 'block');
        });

        //填写醒目的活动标题
        $("body").delegate(".btn_title", 'click', function () {
            $("#stop3_1").html('<i></i>');
            if ($("#title").val() != '') {
                $("#stop3_1").html($("#title").val() + '<i></i>');
            } else {
                $('.error_txt').html('<i></i><em id="title-error" class="error">{__("error_title_not_empty")}</em>');
                return false;
            }
            $('.error_txt').html('');
            $("#stop3_1").css('display', 'block');
            $("#stop4_div").css('display', 'block');
        });
        //计划出发日期
        $("body").delegate(".btn_time", 'click', function () {
            $("#stop4_1").html('<i></i>');
            if ($("#startdate").val() == '') {
                $('.error_txt').html('<i></i><em id="title-error" class="error">{__("error_startdate_not_empty")}</em>');
                return false;
            }
            $('.error_txt').html('');
            if ($("#vartime").val() == '') {
                $('.error_txt').html('<i></i><em id="title-error" class="error">{__("error_vartime_digit")}</em>');
                return false;
            }
            $('.error_txt').html('');
            if ($("#day").val() == '') {
                $('.error_txt').html('<i></i><em id="title-error" class="error">{__("error_day_digit")}</em>');
                return false;
            }
            $('.error_txt').html('');
            $("#stop4_1").html('计划出发日期：' + $("#startdate").val() + '±' + parseInt($("#vartime").val()) + '天，出行天数' + parseInt($("#day").val()) + '天。<i></i>');

            $("#stop4_1").css('display', 'block');
            $("#stop5_div").css('display', 'block');
        });

        //出行人数
        $("body").delegate(".btn_num", 'click', function () {

            if (parseInt($("#adultnum").val()) == 0 && parseInt($("#childnum").val()) == 0) {
                $('.error_txt').html('<i></i><em id="title-error" class="error">{__("error_adultnum_digit")}</em>');
                return false;
            }
            $('.error_txt').html('');
            $("#stop5_1").html('成人：'+parseInt($("#adultnum").val())+'名,儿童:'+parseInt($("#childnum").val())+'名。<i></i>');

            //读取您的行程需求
            get_themelist(11);

            $("#stop5_1").css('display', 'block');
            $("#stop6_div").css('display', 'block');
        });
        //读取您的行程需求
        function get_themelist(typeid) {
            $("#stop6_ul").empty();
            //读取您的行程需求
            $.getJSON(SITEURL + 'pub/ajax_get_attr', {
                attrid: 1,
                typeid: typeid
            }, function (data) {
                var html = template('tpl_li_themelist', data);
                $('#stop6_ul').html(html);
            })
        }

        //您的行程需求
        $("body").delegate("#stop6_ul li", 'click', function () {
            if($(this).children('a').attr('class') == 'on'){
                $(this).children('a').removeClass('on');
            }else{
                $(this).children('a').addClass('on');
            }
        });
        //您的行程需求
        $("body").delegate(".btn_themelist", 'click', function () {

            $("#stop6_1").html('<i></i>');
            if ($("#usertheme").val() != '') {
                $("#stop6_ul li a").removeClass('on');
                $("#stop6_1").html($("#usertheme").val() + '<i></i>');
                $("#attrid").val();

            } else {
                var ischange = true;

                $("#stop6_ul li a.on").each(function () {
                    ischange = false;
                    $("#stop6_1").html($(this).html() + '<i></i>');
                    $("#attrid").val($("#attrid").val() + $(this).parent().attr('data-id') + ',');
                });

                if (ischange) {
                    $('.error_txt').html('<i></i><em id="title-error" class="error">{__("error_usertheme_not_empty")}</em>');
                    return false;
                }
                $('.error_txt').html('');
            }

            $("#stop6_1").css('display', 'block');
            $("#stop7_div").css('display', 'block');
        });


        //验证码
        $('#resend').click(function () {
            var bool = $(this).attr('do-send');
            var node = this;
            if (bool === 'true') {
                //发送验证码
                $.post('{$cmsurl}pub/ajax_send_message', {'phone': $('#mobile').val()}, function (bool) {
                    if (bool != 1) {
                        var message = bool == 0 ? '发送失败，稍后在试' : bool;
                        $('.error_txt').html('<i></i><em id="title-error" class="error">'+message+'</em>');
                    }else{
                        $('.error_txt').html('');
                        count_down(60);
                    }
                    return false;
                }, 'text')
            }
        });
        function count_down(v) {
            if (v > 0) {
                $('#resend').html(--v);
                setTimeout(function () {
                    count_down(v);
                }, 1000);
            }
            else {
                $('#resend').attr('do-send', 'true').addClass('cursor').html('重新发送');
            }
        }


        //发布结伴
        $("body").delegate(".btn_mobile", 'click', function () {
            $('.btn_save').click();
        });
        $("body").delegate(".btn_save", 'click', function () {
            //验证
            $('#st_form').validate({
                rules: {
                    kindlist: 'required',
                    title: 'required',
                    startdate: {
                        required: true,
                        dateISO: true
                    },
                    vartime: {
                        required: true,
                        min: 1
                    },
                    day: {
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
                    mobile: {
                        required: true,
                        mobile: true
                    },
                    msg: 'required'
                },
                messages: {
                    kindlist: '{__("error_kindlist_not_empty")}',
                    title: '{__("error_title_not_empty")}',
                    startdate: {
                        required: '{__("error_startdate_not_empty")}',
                        dateISO: '{__("error_date")}'
                    },
                    vartime: {
                        required: '{__("error_vartime_digit")}',
                        min: '{__("error_vartime_digit")}'
                    },
                    day: {
                        required: '{__("error_day_digit")}',
                        min: '{__("error_day_digit")}'
                    },
                    adultnum: {
                        required: '{__("error_adultnum_digit")}',
                        min: '{__("error_adultnum_digit")}'
                    },
                    childnum: {
                        required: '{__("error_adultnum_digit")}',
                        min: '{__("error_adultnum_digit")}'
                    },
                    mobile: {
                        required: '{__("error_user_phone")}',
                        mobile: '{__("error_user_phone")}'
                    },
                    msg: '{__("error_code")}'
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
                    //要去这个区域的目的地
                    var ischange = true;
                    $("#stop2 li a.on").each(function () {
                        ischange = false;
                    });
                    if($.trim($("#userdest").val()) == '' && ischange){
                        layer.open({
                            content: '{__("error_userdest_not_empty")}',
                            time: 2
                        });
                        return false;
                    }
                    //您的行程需求
                    var ischange = true;
                    $("#stop6_ul li a.on").each(function () {
                        ischange = false;
                    });
                    if($.trim($("#usertheme").val()) == '' && ischange){
                        layer.open({
                            content: '{__("error_usertheme_not_empty")}',
                            time: 2
                        });
                        return false;
                    }

                    $.post('{$cmsurl}pub/ajax_check_msg', {
                        'msg': $.trim($("#msg").val())
                    }, function (data) {
                        if(data ==  true){
                            $.post('{$cmsurl}jieban/ajax_do_add', {
                                'kindlist': $('#kindlist').val(),
                                'dest_mainid': $('#dest_mainid').val(),
                                'dest_childid': $('#dest_childid').val(),
                                'userdest': $('#userdest').val(),
                                'title': $('#title').val(),
                                'startdate': $('#startdate').val(),
                                'vartime': $('#vartime').val(),
                                'day': $('#day').val(),
                                'attrid': $('#attrid').val(),
                                'usertheme': $('#usertheme').val(),
                                'mobile': $('#mobile').val(),
                                'adultnum': $('#adultnum').val(),
                                'childnum': $('#childnum').val(),
                                'msg': $('#msg').val()
                            }, function (data) {
                                if (data.status == 1) {
                                    layer.open({
                                        content: '{__("success_jieban_add_insert")}',
                                        time: 2,
                                        end: function() {
                                            $(".btn_mobile").css('display', 'none')
                                            $("#stop8_div").css('display', 'block');
                                            //setTimeout(goJieban,3000);
                                        }
                                    });
                                }else{
                                    layer.open({
                                        content: '{__("error_jieban_add_insert")}',
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

        $("body").delegate(".btn_goto", 'click', function () {
            goJieban();
        });
    });
</script>
</body>
</html>
