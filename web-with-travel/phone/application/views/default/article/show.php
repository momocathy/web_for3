<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>{$seoinfo['seotitle']}-{$GLOBALS['cfg_webname']}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    {if $seoinfo['keyword']}
    <meta name="keywords" content="{$seoinfo['keyword']}" />
    {/if}
    {if $seoinfo['description']}
    <meta name="description" content="{$seoinfo['description']}" />
    {/if}
    {Common::css('amazeui.css,style.css')}
    {Common::js('jquery.min.js,amazeui.js')}
</head>

<body>
{request "pub/header/typeid/$typeid/isshowpage/1"}


<section>

    <div class="mid_content">

        <div class="gl_show_box">
            <div class="hd">
                <h1>{$info['title']}</h1>
                <p><span>{date('Y-m-d', $info['modtime'])}</span><span>{$info['author']}</span></p>
            </div>
            <div class="gl_con">
                {Product::strip_style($info['content'])}
            </div>
        </div>

        <div class="gl_hot_list">
            <h3 class="tit"><span>推荐攻略</span></h3>
            <ul>
                {st:article action="query" flag="order" offset="0" row="4" return="articlelist" havepic="true"}
                {loop $articlelist $articlerow}
                <li>
                    <a href="{$articlerow['url']}">
                        <div class="pic"><img src="{Common::img($articlerow['litpic'])}" alt="{$articlerow['title']}" /></div>
                        <div class="con">
                            <p class="bt">{$articlerow['title']}</p>
                            <p class="txt">{Common::cutstr_html($articlerow['summary'],30)}</p>
                            <p class="data">
                                <span class="mdd">{$articlerow['lastdest'][0]['kindname']}</span>
                                <span class="num">{$articlerow['shownum']}</span>
                            </p>
                        </div>
                    </a>
                </li>
                {/loop}
                {/st}
            </ul>
        </div><!--推荐攻略-->
    </div>

</section>

{request 'pub/code'}
{request 'pub/footer'}


</body>
</html>
