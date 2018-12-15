<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>租车搜索-{$webname}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    {Common::css('amazeui.css,style.css')}
    {Common::js('jquery.min.js,amazeui.js')}
</head>

<body>

{request "pub/header/typeid/$typeid"}
<section>

    <div class="mid_content">
        <form method="get">
            <div class="search_list_page">
                <div class="search_list_box">
                    <p class="text"><input type="text" name="keyword" id="keyword" placeholder="输入{$channelname}名称"/></p>

                    <p class="term"><span>目的地/车型/车辆属性<i>&gt;</i></span></p>
                </div>
                <div class="search_btn"><a href="javascript:;" class="btn_search">搜索</a></div>
            </div>
            <!--条件搜索-->
            <input type="hidden" id="destpy" name="destpy" value="all"/>
            <input type="hidden" id="kindid" name="kindid" value="0"/>
            <input type="hidden" id="attrid" name="attrid" value="0"/>
        </form>

        <div class="layer-page" style="display: none">
            <div class="tran_box"></div>
            <div class="tc_tj_con">
                <div class="tj_list_con">
                    <dl>
                        <dt>目的地</dt>
                        <dd id="dest_list">
                            <a href="javascript:;" data-id="0" data-pinyin="all" class="on">不限</a>
                            {st:dest action="query" flag="dest" pid="0" typeid="$typeid" row="99"}
                            {loop $data $r}
                            <a href="javascript:;" data-id="{$r['id']}" data-pinyin="{$r['pinyin']}">{$r['kindname']}</a>
                            {/loop}
                            {/st}
                        </dd>
                    </dl>
                    <dl>
                        <dt>车型</dt>
                        <dd id="kind_list">
                            <a href="javascript:;" data-id="0" class="on">不限</a>
                            {st:car action="kind_list"}
                            {loop $data $r}
                            <a href="javascript:;" data-id="{$r['id']}">{$r['title']}</a>
                            {/loop}
                            {/st}
                        </dd>
                    </dl>
                    <dl>
                        <dt>车辆属性</dt>
                        <dd id="attr_list">
                            <a href="javascript:;" data-id="0" class="on">不限</a>
                            {st:car action="attr_list"}
                            {loop $data $r}
                            <a href="javascript:;" data-id="{$r['id']}">{$r['title']}</a>
                            {/loop}
                            {/st}
                        </dd>
                    </dl>

                    <div class="sure_btn"><a href="javascript:;" class="btn_sure_choose">确定</a></div>
                </div>
            </div>
        </div>
    </div>
</section>

{request 'pub/code'}
{request 'pub/footer'}


<script>
    $(function () {
        var $outTerm = $('.term');
        var $layerPage = $('.layer-page');
        var $sy_h = $(window).height() - 221;
        $('.tj_list_con').css('height', $sy_h)
        $outTerm.click(function () {
            $layerPage.show()
        })
        $('.tran_box').click(function (event) {
            $layerPage.hide();
        })

        $(".tj_list_con").find('a').click(function () {
            $(this).addClass('on').siblings().removeClass('on');
        })

        //确认选择
        $(".btn_sure_choose").click(function () {
            var txt = '';
            var destObj = $("#dest_list").find('.on')
            var destid = destObj.attr('data-id');
            var destpy = destObj.attr('data-pinyin');//目的地拼音
            txt += destid != 0 ? destObj.text() : '目的地不限';

            var kindObj = $("#kind_list").find('.on');
            var kindid = kindObj.attr('data-id');
            txt += '/' + (kindid != 0 ? kindObj.text() : '车型不限');

            var attrObj = $("#attr_list").find('.on');
            var attrid = attrObj.attr('data-id');
            txt += '/' + (attrid != 0 ? attrObj.text() : '车辆属性不限');

            $(".term").find('span').html(txt);
            $layerPage.hide();

            $("#destpy").val(destpy);
            $("#kindid").val(kindid);
            $("#attrid").val(attrid);


        })

        //搜索页面
        $(".btn_search").click(function () {
            var destpy = $("#destpy").val();
            var kindid = $("#kindid").val();
            var attrid = $("#attrid").val();
            var keyword = $("#keyword").val();
            var url = SITEURL + 'cars/' + destpy + '-0-' + kindid + '-0-'  + attrid + '-1?keyword=' + encodeURIComponent(keyword);
            window.location.href = url;
        })
    })
</script>
</body>
</html>
