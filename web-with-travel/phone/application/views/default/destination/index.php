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
{Common::css('amazeui.css,style.css')}
{Common::js('jquery.min.js,amazeui.js,template.js')}
<script>
	$(function(){
		$('#my-st-slide').offCanvas('close');
		$('.st-slider').flexslider({pauseOnAction: false});
	})
</script>
</head>
<body>

  	{request "pub/header/typeid/$typeid"}


    <section>

        <div class="mid_content">

            <div data-am-widget="slider" class="am-slider am-slider-default" data-am-slider='{}' >
                <ul class="am-slides">
                    {st:ad action="getad" name="s_dest_index_1"}
                    {loop $data['aditems'] $v}
                    <li><a href="{$v['adlink']}"><img src="{Common::img($v['adsrc'])}" title="{$v['adname']}"></a></li>
                    {/loop}
                    {/st}
                </ul>
            </div><!--轮播图-->

            <div class="hot_dest">
                <h3 class="tit"><span>热门目的地</span></h3>
                <ul class="hot_dest_list">
                    {st:dest action="query" flag="hot" offset="0" row="6"}
                    {loop $data $row}
                    <li>
                        <a href="{$cmsurl}{$row['pinyin']}">
                            <img src="{Common::img($row['litpic'])}" alt="{$row['kindname']}" />
                            <span>{$row['kindname']}</span>
                        </a>
                    </li>
                    {/loop}
                    {/st}
                </ul>
            </div><!--热门目的地-->

            <div class="dest_main">

                <div class="dest_list_box">
                    {st:dest action="query" flag="next" offset="0" row="9999" pid="0"}
                    {loop $data $row}
                    <h3><a href="{$cmsurl}{$row['pinyin']}">{$row["kindname"]}</a></h3>
                    <div class="con">
                        {st:dest action="query" flag="next" offset="0" row="9999" pid="$row['id']" return="data1"}
                        {loop $data1 $row1}
                        <dl>
                            <dt><a href="{$cmsurl}{$row1['pinyin']}">{$row1["kindname"]}</a></dt>
                            <dd>
                                {st:dest action="query" flag="next" offset="0" row="9999" pid="$row1['id']" return="data2"}
                                {loop $data2 $row2}
                                <a href="{$cmsurl}{$row2['pinyin']}">{$row2["kindname"]}</a>
                                {/loop}
                                {/st}
                            </dd>
                        </dl>
                        {/loop}
                        {/st}
                    </div>
                    {/loop}
                    {/st}
                    <!--目的地列表-->
                </div>
            </div>
    </section>

    {request "pub/footer"}

</body>
</html>
