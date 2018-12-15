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
    {Common::css('amazeui.css,style.css')}
    {Common::js('jquery.min.js,amazeui.js,common.js,jquery.validate.min.js,layer/layer.m.js')}
</head>

<body>

{request "pub/header/typeid/$typeid"}

<section>
    <div class="mid_content">
        <div class="st_join_box">
            <form id="st_form" method="post" action="{$cmsurl}jieban/do_join" enctype="application/x-www-form-urlencoded">
                <div class="st_login">
                    <div class="number_password">
                        <p>
                            <strong>联系人：</strong>
                            <input type="text" name="linkman" id="linkman" class="" placeholder="请输入联系人姓名"/>
                        </p>

                        <p>
                            <strong>手机号：</strong>
                            <input type="tel" name="mobile" id="mobile" class="" placeholder="请输入手机号"/>
                        </p>

                        <p>
                            <strong>验证码：</strong>
                            <input type="text" name="msg" id="msg" class="" placeholder="请输入短信验证码"/>
                            {if $isopen}
                            <a class="yz_num" id="resend" href="javascript:;" do-send="true">获取短信验证码</a>
                            {else}
                            <img class="captcha yzm_pic cursor " src="" height="30"/>
                            {/if}
                        </p>
                    </div>
                    <div class="error_txt"></div>
                </div>

                <div class="num_peo">
                    <p>
                        <em>成人：</em>
                        <span class="before">
                            <a class="sub" href="javascript:;">-</a><input type="text" name="adultnum" id="adultnum" readonly="readonly" value="1"/><a class="add" href="javascript:;">+</a>
                        </span>
                    </p>

                    <p>
                        <em>儿童：</em>
                        <span class="before">
                            <a class="sub" href="javascript:;">-</a><input type="text" name="childnum" id="childnum" readonly="readonly" value="0"/><a class="add" href="javascript:;">+</a>
                        </span>
                    </p>
                </div>
                <input id="id" name="id" type="hidden" value="{$info['id']}">

                <div class="submit_btn"><input type="submit" value="确定"/></div>
            </form>
        </div>
    </div>
</section>

{request 'pub/code'}
{request 'pub/footer'}

<script>
    $(function () {
        function goJieban(){
            location.href = SITEURL + 'jieban';
        }

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

        //验证码切换
        $('.captcha').attr('src', ST.captcha(SITEURL+'captcha'));
        $('.captcha').click(function () {
            $(this).attr('src', ST.captcha($(this).attr('src')));
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
                        $('#st_form').find('.error_txt:eq(0)').html('<i></i>' + message);
                    } else {
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

        //验证
        $('#st_form').validate({
            rules: {
                linkman: 'required',
                mobile: {
                    required: true,
                    mobile: true
                },
                msg: {
                    required: true,
                    remote: {
                        url: "{$cmsurl}pub/ajax_check_msg", //后台处理程序
                        type: "post",  //数据发送方式
                        dataType: "json",       //接受数据格式
                        data: {
                            msg: function () {
                                return $("#msg").val();
                            }
                        }
                    }
                },
                adultnum:{
                    required: true,
                    min: 1
                },
                childnum:{
                    required: true,
                    min: 0
                }
            },
            messages: {
                linkman: '{__("error_linkman_not_empty")}',
                mobile: {
                    required: '{__("error_user_phone")}',
                    mobile: '{__("error_user_phone")}'
                },
                msg: {
                    required: '{__("error_code_not_empty")}',
                    remote: '{__("error_code")}'
                },
                adultnum: {
                    required: '{__("error_adultnum_digit")}',
                    min: '{__("error_adultnum_digit")}'
                },
                childnum: {
                    required: '{__("error_childnum_digit")}',
                    min: '{__("error_childnum_digit")}'
                }
            },
            errorElement: "em",
            errorPlacement: function (error, element) {
                var content = $('#st_form').find('.error_txt:eq(0)').html();
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
            }, submitHandler: function (form) { //表单提交句柄,为一回调函数，带一个参数：form
                $.post('{$cmsurl}jieban/ajax_do_join', {
                    'id': $('#id').val(),
                    'linkman': $('#linkman').val(),
                    'mobile': $('#mobile').val(),
                    'msg': $('#msg').val(),
                    'adultnum': $('#adultnum').val(),
                    'childnum': $('#childnum').val()
                }, function (data) {
                    if (data.status == 1) {
                        layer.open({
                            content: '{__("success_jieban_join_insert")}',
                            time: 2,
                            end:function(){
                                goJieban();
                            }
                        });
                    }else{
                        layer.open({
                            content: data.message,
                            time: 2
                        });
                    }
                }, 'json')
            }
        });
    });
</script>
</body>
</html>

