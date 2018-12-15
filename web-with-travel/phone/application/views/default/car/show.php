<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{$seoinfo['seotitle']}-{$GLOBALS['cfg_webname']}</title>
    {if $seoinfo['keyword']}
    <meta name="keywords" content="{$seoinfo['keyword']}"/>
    {/if}
    {if $seoinfo['description']}
    <meta name="description" content="{$seoinfo['description']}"/>
    {/if}
    {Common::css('amazeui.css,style.css')}
    {Common::js('jquery.min.js,amazeui.js')}
</head>
<body>

{request "pub/header/typeid/$typeid/isshowpage/1"}

<section>

    <div class="mid_content">

        <div data-am-widget="slider" class="am-slider am-slider-default" data-am-slider='{}'>
            <ul class="am-slides">
                {loop $info['piclist'] $pic}
                <li><img src="{$pic[0]}" alt="{$pic[1]}"></li>
                {/loop}
            </ul>
        </div>
        <!--轮播图-->

        <div class="line_show_top">
            <p class="tit">{$info['title']}</p>

            <p class="txt">推荐理由：{$info['sellpoint']}</p>

            <p class="attr">
                {loop $info['attrlist'] $attr}
                <span>{$attr['attrname']}</span>
                {/loop}
            </p>

            <p class="price">
                <span>
                    {if $info['price'] > 0}
                    &yen;<b>{$info['price']}</b>起
                    {else}
                    电询
                    {/if}
                </span>
            </p>

            <p class="num">
                <span><b>{$info['sellnum']}</b>销量</span>
                <span><b>{$info['satisfyscore']}%</b>满意度</span>
                <span class="pl"><b>{$info['commentnum']}</b>人点评<i class="cursor">&gt;</i></span>
            </p>
        </div>
        <!--顶部介绍-->

        <div class="cp_show_msg">
            <h3>产品简介</h3>
            <ul>
                <li><span>车辆编号：</span>D0006</li>
                <li><span>车辆车型：</span>{$info['carkindname']}</li>
            </ul>
            <div class="opt_type order" data-id="{$info['id']}">选择车型价格<i class="cursor">&gt;</i></div>
        </div>
        <!--产品信息-->

        {st:detailcontent action="get_content" typeid="3" productinfo="$info" onlyrealfield="1"}
        {loop $data $row}
        <div class="line_tablist_box">
            <div class="bt_tit"><span>{$row['chinesename']}</span></div>
            <div class="cont_nr">
                {$row['content']}
            </div>
        </div>
        <!--车辆信息-->
        {/loop}
        {/st}

    </div>

</section>


{request 'pub/code'}
{request 'pub/footer'}

<div class="bom_link_box">
    <div class="bom_fixed">
        <a href="tel:{$GLOBALS['cfg_m_phone']}">电话咨询</a>
        <a class="on order" data-id="{$info['id']}" href="javascript:;">立即预定</a>
    </div>
</div>

<script>
    //预订按钮
    $('.order').click(function(){
        var productid = $(this).attr('data-id');
        url = SITEURL+'car/book/id/'+productid;
        window.location.href = url;
    });
    //发表评论
    $('.pl').click(function(){
        var url = SITEURL+"pub/comment/id/{$info['id']}/typeid/{$typeid}";
        window.location.href = url;
    })
</script>
</body>
</html>
