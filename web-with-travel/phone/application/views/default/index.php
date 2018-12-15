<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>{$seoinfo['seotitle']}-{$webname}</title>
    {if $seoinfo['keyword']}
    <meta name="keywords" content="{$seoinfo['keyword']}"/>
    {/if}
    {if $seoinfo['description']}
    <meta name="description" content="{$seoinfo['description']}"/>
    {/if}
    <meta name="author" content="{$webname}"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="icon" href="{$GLOBALS['cfg_m_main_url']}/favicon.ico" type="image/x-icon"/>
    <link rel="shortcut icon" href="{$GLOBALS['cfg_m_main_url']}/favicon.ico" type="image/x-icon"/>
    {Common::css('amazeui.css,style.css,index.css,extend.css')}
    {Common::js('jquery.min.js,amazeui.js,template.js,layer/layer.m.js')}
</head>
<body>
{request "pub/header/typeid/0"}
<section>

    <div class="mid_content">
        <div data-am-widget="slider" class="am-slider am-slider-default" data-am-slider='{}'>
            <ul class="am-slides">
                {st:ad action="getad" name="s_index_1"}
                {loop $data['aditems'] $v}
                <li><a href="{$v['adlink']}"><img src="{Common::img($v['adsrc'])}" title="{$v['adname']}"></a></li>
                {/loop}
                {/st}

            </ul>
        </div>
        <!--轮播图-->

        <div class="st_search_box">
            <input type="text" class="st_home_txt" id="keyword" placeholder="搜索产品"/>
            <input type="button" class="st_home_btn" value="搜索"/>
        </div>
        <!--搜索-->

        <nav>
            <div class="st_home_menu">
                <p>
                    {st:channel action="getchannel" row="100"}
                    {loop $data $row}
                    <a href="{$row['url']}">
                        <span><img src="{$row['ico']}"/></span>
                        <strong>{$row['title']}</strong>
                    </a>
                    {/loop}
                    {/st}
                </p>
            </div>
        </nav>
        <!--导航-->

        <div class="st_sale_box">
            <h3><i></i>特价优惠</h3>

            <div class="st_sale_con">
                <div class="st_advpic_l">
                    {st:ad action="getad" name="s_index_2"}
                    {loop $data['aditems'] $v}
                    <a href="{$v['adlink']}"><img src="{Common::img($v['adsrc'])}" title="{$v['adname']}"></a>
                    {/loop}
                    {/st}
                </div>
                <div class="st_advpic_r">
                    {st:ad action="getad" name="s_index_3"}
                    {loop $data['aditems'] $v}
                    <a href="{$v['adlink']}"><img src="{Common::img($v['adsrc'])}" title="{$v['adname']}"></a>
                    {/loop}
                    {/st}
                    {st:ad action="getad" name="s_index_4"}
                    {loop $data['aditems'] $v}
                    <a href="{$v['adlink']}"><img src="{Common::img($v['adsrc'])}" title="{$v['adname']}"></a>
                    {/loop}
                    {/st}

                </div>
            </div>
        </div>
        <!--特价优惠-->
        {if $channel['line']['isopen']==1}
        <div class="st_hot_list">
            <div class="st_tit">
                <h3>{$channel['line']['channelname']}</h3>
            </div>
            <div class="st_list_con">
                <ul class="st_list_ul">
                    {st:line action="query" flag="order" row="4"}
                    {loop $data $row}
                    <li>
                        <a href="{$row['url']}">
                            <img src="{Common::img($row['litpic'])}" alt="{$row['title']}"/>

                            <p class="tit">{$row['title']}{$row['sellpoint']}</p>

                            <p class="num">
                                {if !empty($row['price'])}
                                <span>&yen;<strong>{$row['price']}</strong>起</span>
                                {else}
                                <span><strong>电询</strong></span>
                                {/if}
                                {if !empty($row['storeprice'])}
                                <del>原价：{$row['storeprice']}元</del>
                                {/if}
                            </p>
                        </a>
                    </li>
                    {/loop}
                    {/st}

                </ul>
                <div class="list_more"><a href="{$cmsurl}lines/all/">查看更多</a></div>
                <div style=" clear:both"></div>
            </div>
        </div>
        <!--热门线路-->
        {/if}

        {if $channel['hotel']['isopen']}
        <div class="st_hot_list">
            <div class="st_tit">
                <h3>{$channel['hotel']['channelname']}</h3>
            </div>
            <div class="st_list_con">
                <ul class="st_list_ul">
                    {st:hotel action="query" flag="order" row="4"}
                    {loop $data $row}
                    <li>
                        <a href="{$row['url']}">
                            <img src="{Common::img($row['litpic'])}" alt="{$row['title']}"/>

                            <p class="tit">{$row['title']}{$row['sellpoint']}</p>

                            <p class="num">
                                {if !empty($row['price'])}
                                <span>&yen;<strong>{$row['price']}</strong>起</span>
                                {else}
                                <span><strong>电询</strong></span>
                                {/if}
                                {if !empty($row['storeprice'])}
                                <del>原价：{$row['storeprice']}元</del>
                                {/if}
                            </p>
                        </a>
                    </li>
                    {/loop}
                    {/st}

                </ul>
                <div class="list_more"><a href="{$cmsurl}hotels/all/">查看更多</a></div>
                <div style=" clear:both"></div>
            </div>
        </div>
        <!--热门酒店-->
        {/if}
        {if $channel['car']['isopen']}
        <div class="st_hot_list">
            <div class="st_tit">
                <h3>{$channel['car']['channelname']}</h3>
            </div>
            <div class="st_list_con">
                <ul class="st_list_ul">
                    {st:car action="query" flag="order"}
                    {loop $data $row}
                    <li>
                        <a href="{$row['url']}">
                            <img src="{Common::img($row['litpic'])}" alt="{$row['title']}"/>

                            <p class="tit">{$row['title']}{$row['sellpoint']}</p>

                            <p class="num">
                                {if !empty($row['price'])}
                                <span>&yen;<strong>{$row['price']}</strong>起</span>
                                {else}
                                <span><strong>电询</strong></span>
                                {/if}
                            </p>
                        </a>
                    </li>
                    {/loop}
                    {/st}
                </ul>
                <div class="list_more"><a href="{$cmsurl}cars/all/">查看更多</a></div>
                <div style=" clear:both"></div>
            </div>
        </div>
        <!--热门租车-->
        {/if}
        {if $channel['spot']['isopen']}
        <div class="st_hot_list">
            <div class="st_tit">
                <h3>{$channel['spot']['channelname']}</h3>
            </div>
            <div class="st_list_con">
                <ul class="st_list_ul">
                    {st:spot action="query" flag="order" row="4"}
                    {loop $data $row}
                    <li>
                        <a href="{$row['url']}">
                            <img src="{Common::img($row['litpic'])}" alt="{$row['title']}"/>

                            <p class="tit">{$row['title']}{$row['sellpoint']}</p>

                            <p class="num">
                                {if !empty($row['price'])}
                                <span>&yen;<strong>{$row['price']}</strong>起</span>

                                {/if}
                            </p>
                        </a>
                    </li>
                    {/loop}
                    {/st}

                </ul>
                <div class="list_more"><a href="{$cmsurl}spots/all/">查看更多</a></div>
                <div style=" clear:both"></div>
            </div>
        </div>
        <!--景点门票-->
        {/if}
        {if $channel['tuan']['isopen']}
        <div class="tuan_home_con st_hot_list">
            <div class="st_tit">
                <h3>{$channel['tuan']['channelname']}</h3>
            </div>
            <ul class="tuan_hm_list">
                {st:tuan action="query" flag="new" row="2"}
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
                            {if !empty($v['sellprice'])}
                            <del>原价：{$v['sellprice']}元</del>
                            {/if}
                            <em>已售：{$v['sellnum']}</em>
                        </p>
                    </a>
                </li>
                {/loop}
                {/st}
            </ul>
            <div class="tuan_more"><a href="{$cmsurl}tuan/">查看更多</a></div>
        </div>
        <!--限时特价-->
        {/if}
        {if $channel['jieban']['isopen']}
        <div class="jb_list_home">
            <h3><span>{$channel['jieban']['channelname']}</span></h3>
            <ul>
                {st:jieban action="query" row="2" flag="new"}
                {loop $data $row}
                <li>
                    <a class="pic" href="{$row['url']}"><img src="{$row['litpic']}"/><span class="day_date"><strong><b>{$row['day']}</b>日游</strong><em>{$row['startdate']}出发</em></span></a>

                    <p class="tit">{$row['title']}片</p>

                    <p class="txt">{Common::cutstr_html($row['description'],20)}</p>

                    <p class="attr">
                        {loop $row['attrlist'] $v}
                        <span>{$v['attrname']}</span>
                        {/loop}
                    </p>

                    <p class="join"><span>已有<b>{$row['joinnum']}</b>人报名</span><em>{$row['shownum']}</em><a
                            href="{$row['url']}">立即加入</a></p>
                </li>
                {/loop}
                {/st}
            </ul>
            <div class="tuan_more"><a href="{$cmsurl}jieban/">查看更多</a></div>
        </div>
        <!--结伴-->
        {/if}
        {if $channel['article']['isopen']}
        <div class="gl_hot_list">
            <h3 class="tit"><span>{$channel['article']['channelname']}</span></h3>
            <ul>
                {st:article action="query" flag="order" row="3"}
                {loop $data $row}
                <li>
                    <a href="{$row['url']}">
                        <div class="pic"><img src="{Common::img($row['litpic'])}" alt="{$row['title']}"/></div>
                        <div class="con">
                            <p class="bt">{$row['title']}</p>

                            <p class="txt">{Common::cutstr_html($row['summary'],20)}</p>

                            <p class="data">
                                <span class="mdd">{$row['kindname']}</span>
                                <span class="num">{$row['shownum']}</span>
                            </p>
                        </div>
                    </a>
                </li>
                {/loop}
                {/st}

            </ul>
            <div class="list_more"><a href="{$cmsurl}raiders/all/">查看更多</a></div>
        </div>
        <!--游记攻略-->
        {/if}
    </div>

</section>

{request "pub/code"}
{request "pub/footer"}

<script>
    $(function () {

        //全局搜索
        $('.st_home_btn').click(function () {
            var keyword = $("#keyword").val();
            if (keyword == '') {
                layer.open({
                    content: '{__("error_keyword_not_empty")}',
                    btn: ['{__("OK")}']
                });
                return false;
            } else {
                url = SITEURL + 'search?keyword=' + encodeURIComponent(keyword);
                window.location.href = url;
            }
        })

        //团购时间
        $('.tuan_hm_list').find('.date_time').each(function (index, element) {
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
    })
</script>


</body>
</html>
