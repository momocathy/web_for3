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
    {php echo Common::css('amazeui.css,style.css,extend.css');}
    {php echo Common::js('jquery.min.js,amazeui.js,template.js,layer/layer.m.js');}
</head>
<body>
{request "pub/header/typeid/$typeid"}
<section>

    <div class="mid_content">

        <div data-am-widget="slider" class="am-slider am-slider-default" data-am-slider='{}'>
            <ul class="am-slides">
                {st:ad action="getad" name="s_tuan_index_1"}
                {loop $data['aditems'] $v}
                <li><a href="{$v['adlink']}"><img src="{Common::img($v['adsrc'])}" title="{$v['adname']}"></a></li>
                {/loop}
                {/st}
            </ul>
        </div>
        <!--轮播图-->
        <div class="st_search_box">
            <input type="text" class="st_home_txt search" placeholder="搜索{$channelname}"/>
            <input type="button" class="st_home_btn search" value="搜索"/>
        </div>
        <!--搜索-->
        <div class="tuan_home_con">
            <ul class="tuan_hm_list" id="content">
                {st:tuan action="query" flag="new" row="10"}
                {php}$indexCount=count($data);{/php}
                {loop $data $v}
                <li>
                    <a href="{$v['url']}">
                        <img class="pic" src="{Common::img($v['litpic'])}"/>

                        <div class="date_time" start-time="{$v['starttime']}" end-time="{$v['endtime']}">
                            <em></em>
                            <span></span>
                        </div>
                        <p class="tit">{$v['title']}</p>

                        <p class="txt">{$v['sellpoint']}</p>

                        <p class="price">
                            <span>&yen;<b>{$v['price']}</b></span>
                            <i>{$v['discount']}折</i>
                            <em>已售：{$v['sellnum']}</em>
                        </p>
                    </a>
                </li>
                {/loop}
                {/st}
            </ul>
            <div class="tuan_more" ><a href="{$cmsurl}tuan/all">加载更多</a></div>
            {if $indexCount==0}
            <!--没有相关信息-->
            <div class="no-content">
                <img src="{$GLOBALS['cfg_public_url']}images/nocon.png"/>

                <p>啊哦，暂时没有相关信息</p>
            </div>
            {/if}
        </div>
    </div>
</section>
{request 'pub/code'}
{request 'pub/footer'}
<script id="index" type="text/html">
    {{each data as v i}}
    <li>
        <a href="{{v['url']}}">
            <img class="pic" src="{{v['litpic']}}"/>

            <div class="date_time" start-time="{{v['starttime']}}" end-time="{{v['endtime']}}">
                <em>剩余时间</em>
                <span></span>
            </div>
            <p class="tit">{{v['title']}}</p>

            <p class="txt">{{v['sellpoint']}}</p>

            <p class="price">
                <span>&yen;<b>{{v['price']}}</b></span>
                <i>{{v['discount']}}折</i>
                <em>已售：{{v['sellnum']}}</em>
            </p>
        </a>
    </li>
    {{/each}}
</script>
</body>
<script type="text/javascript">
    $('document').ready(function () {
        var interval = 1000;
        $('#more').click(function () {
            var page = $('#page').val();
            layer.open({
                type: 13,
                content: '正在加载数据...',
                time: 20
            });
            $.post(SITEURL + 'tuan/ajax_tuan_data/all-0-0-' + page, {}, function (rs) {
                if (rs.list.length > 0) {
                    var html = template('index', rs);
                    $('#content').append(html);
                    $('#content').find('.date_time').each(function (index, element) {
                        show_count(element);
                    });
                }
                //设置分页
                if (rs.page != -1) {
                    $("#page").val(data.page);
                } else {
                    $("#get-more").hide();
                }
                layer.closeAll();
            }, 'json');
        });
        $('#content').find('.date_time').each(function (index, element) {
            show_count(element);
        });
        function show_count(node) {
            var endTime = $(node).attr('end-time') * 1000;
            var startTime = $(node).attr('start-time') * 1000;
            var timer_rt = window.setInterval(function () {
                var time;
                var now = new Date();
                now = now.getTime();
                if (startTime > now) {
                    time = startTime - now;
                    $(node).find('em').html('开始时间');
                } else if (endTime > now) {
                    time = endTime - now;
                    $(node).find('em').html('结束时间');
                } else {
                    $(node).find('em').html('已结束');
                    $(node).parents('li').remove();
                    clearInterval(timer_rt);
                }
                time = parseInt(time / 1000);
                var day = Math.floor(time / (60 * 60 * 24));
                var hour = Math.floor((time - day * 24 * 60 * 60) / 3600);
                var minute = Math.floor((time - day * 24 * 60 * 60 - hour * 3600) / 60);
                var html = '';
                if (day > 0) {
                    html += day + '天';
                }
                if (hour > 0) {
                    html += hour + '时';
                }
                if (minute > 0) {
                    html += minute + '分';
                }
                $(node).find('span').html(html);
            }, 1000);
        }

        $('.search').click(function () {
            var url = SITEURL + 'tuan/search';
            window.location.href = url;
        })
    });
</script>

</html>
