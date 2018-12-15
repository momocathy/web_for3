<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>{$seoinfo['seotitle']}-{$GLOBALS['cfg_webname']}</title>
    {if $seoinfo['keyword']}
    <meta name="keywords" content="{$seoinfo['keyword']}" />
    {/if}
    {if $seoinfo['description']}
    <meta name="description" content="{$seoinfo['description']}" />
    {/if}
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    {php echo Common::css('amazeui.css,style.css,extend.css');}
    {php echo Common::js('jquery.min.js,amazeui.js');}
</head>

<body>
{request "pub/header/typeid/$typeid/isshowpage/1"}

<section>

    <div class="mid_content">

        <div data-am-widget="slider" class="am-slider am-slider-default" data-am-slider='{}' >
            <ul class="am-slides">
                {loop $info['piclist'] $pic}
                 <li><img src="{Common::img($pic[0])}"></li>
                {/loop}
            </ul>
        </div><!--轮播图-->

        <div class="line_show_top">
            <p class="tit">{if !empty($info['startcity'])}<span>{if !empty($info['startcity'])}[{$info['startcity']}]{/if}</span>{/if}{$info['title']}</p>
            <p class="txt">{$info['sellpoint']}</p>
            <p class="attr">
                {loop $info['attrlist'] $v}
                 <span>{$v['attrname']}</span>
                {/loop}
            </p>
            <p class="price">
                {if !empty($info['price'])}
                 <span>&yen;<b>{$info['price']}</b>起</span>
                {else}
                 <span><b>电询</b></span>
                {/if}
                <del>原价:&yen;{$info['storeprice']}</del>
            </p>
            <p class="num">
                <span><b>{$info['sellnum']}</b>销量</span>
                <span><b>{$info['satisfyscore']}%</b>满意度</span>
                <span class="pl"><b>{$info['commentnum']}</b>人点评<i class="cursor">&gt;</i></span>
            </p>
        </div><!--顶部介绍-->

        <div class="cp_show_msg">
            <h3>产品信息</h3>
            <ul>
                <li><span>线路编号：</span>{$info['lineseries']}</li>
                <li><span>往返交通：</span>{$info['transport']}</li>
                <li><span>旅游团期：</span>{$info['lineday']}天</li>
                <li><span>提前报名：</span>{$info['linebefore']}天</li>
            </ul>
            <div class="opt_type order" data-id="{$info['id']}">选择线路类型、出发日期<i class="cursor">&gt;</i></div>
        </div><!--产品信息-->
        {st:detailcontent action="get_content" typeid="1" productinfo="$info"}
            {loop $data $row}
            <div class="line_tablist_box">
                <div class="bt_tit"><span>{$row['chinesename']}</span></div>
                <div class="cont_nr">
                    {if preg_match('/^\d+$/',$row['content']) && $info['isstyle']==2}
                    <div class="eachday">
                    {loop Model_Line_Jieshao::detail($row['content']) $v}
                        <div class="day_num">
                            <div class="hd">
                                <span class="day_on">第{$v['day']}天</span>
                                <span class="dest">{$v['title']}</span>
                            </div>
                            <div class="hg">
                                <div class="am-icon-caret-up"></div>
                                <dl class="sum">
                                    <dt class="yc">用餐</dt>
                                    <dd class="con">
                                        {if breakfirsthas}
                                        <span class="tc">早餐：{$v[breakfirst]}</span>
                                        {/if}
                                        {if lunchhas}
                                        <span class="tc">中餐：{$v[lunch]}</span>
                                        {/if}
                                        {if supperhas}
                                        <span class="tc">晚餐：{$v[supper]}</span>
                                        {/if}
                                    </dd>
                                </dl>
                                <dl class="sum">
                                    <dt class="zs">住宿</dt>
                                    <dd class="con">
                                        {$v['hotel']}
                                    </dd>
                                </dl>
                                <dl class="sum">
                                    <dt class="jt">交通</dt>
                                    <dd class="con">
                                        {loop explode(',',$v['transport']) $t}
                                        <span class="gj">{$t}</span>
                                        {/loop}
                                    </dd>
                                </dl>
                                <dl class="sum">
                                    <dt class="xc">行程</dt>
                                    <dd class="con">
                                        {$v['jieshao']}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    {/loop}
                    </div><!--行程安排-->
                    {elseif $info['isstyle']==1 && $row['columnname']=='jieshao'}
                        {$info['jieshao']}
                    {else}
                    {$row['content']}
                    {/if}
                </div>
            </div><!--线路详细-->
            {/loop}
        {/st}
    </div>

</section>
{request "pub/code"}
{request 'pub/footer'}
<div class="bom_link_box">
    <div class="bom_fixed">
        <a href="tel:{$GLOBALS['cfg_m_phone']}">电话咨询</a>
        <a class="on order" href="javascript:;" data-id="{$info['id']}">立即预定</a>
    </div>
</div>
<script>
    $('.pl').click(function(){
        var url = SITEURL+"pub/comment/id/{$info['aid']}/typeid/{$typeid}";
        window.location.href = url;
    })

    //预订按钮
    $('.order').click(function(){
        var productid = $(this).attr('data-id');
        url = SITEURL+'line/book/id/'+productid;
        window.location.href = url;
    })

</script>
</body>
</html>
