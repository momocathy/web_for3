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
{Common::css('amazeui.css,style.css,extend.css')}
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
                    {st:ad action="getad" name="s_article_index_1"}
                        {loop $data['aditems'] $v}
                        <li><a href="{$v['adlink']}"><img src="{Common::img($v['adsrc'])}" title="{$v['adname']}"></a></li>
                        {/loop}
                    {/st}
                </ul>
            </div><!--轮播图-->

            <div class="st_search_box">
                <input type="text" class="st_home_txt" placeholder="搜索攻略" />
                <input type="button" class="st_home_btn" value="搜索" />
            </div><!--搜索-->

            <div class="hot_cp_slide">
                <h3><span>最新{$channelname}</span></h3>
                <div class="news_random">
                    <ul>
                    </ul>
                    <div class="next_btn"><a href="javascript:;" id="btnNextNewArticle"><img src="{$cmsurl}public/images/shuaxin_ico.png" />换一个</a></div>
                </div>
            </div><!--最新攻略-->
            {st:dest action="query" flag="channel_nav" typeid="4" row="3"}
            {loop $data $row}
            <div class="gl_hot_list">
                <h3 class="tit"><span>{$row['kindname']}</span></h3>
                <ul>
                    {st:article action="query" flag="mdd" destid="$row['id']" offset="0" row="3" return="articlelist" havepic="false"}
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
                <div class="list_more"><a href="{$cmsurl}raiders/{$row['pinyin']}">查看更多</a></div>
            </div><!--成都攻略-->
            {/loop}
            {/st}


            <div class="hot_city">
                <h3 class="tit"><span>热门目的地</span></h3>
                <ul class="hot_list">
                    {st:dest action="query" flag="hot" typeid="4" offset="0" row="16"}
                    <li>
                        {loop $data $row}
                        {if $n % 4 == 0}
                            <a href="{$cmsurl}raiders/{$row['pinyin']}">{$row['kindname']}</a>
                            </li><li>
                        {else}
                            <a href="{$cmsurl}raiders/{$row['pinyin']}">{$row['kindname']}</a>
                        {/if}
                        {/loop}
                    </li>
                    {/st}
                </ul>
            </div><!--热门目的地-->

        </div>

    </section>

    {request "pub/footer"}

    <script type="text/html" id="tpl_new_article">
        {{each list as value i}}
        <li>
            <a href="{{value.url}}">
                <span class="pic"><img src="{{value.litpic}}" alt="{{value.title}}" /></span>
                <p class="tit">{{value.title}}</p>
                <p class="txt">{{value.summary}}</p>
            </a>
        </li>
        {{/each}}
    </script>

    {Common::js('layer/layer.m.js')}

    <script>
        var new_article_index = 0;

        $(function(){
            $("#btnNextNewArticle").click(function(){
                get_new_article();
            })
            get_new_article();
            $(".st_home_txt").click(function(){jump_search();})
            $(".st_home_btn").click(function(){jump_search();})
        })

        function get_new_article()
        {
            layer.open({
                type: 2,
                content: '正在加载数据...',
                time :20

            });

            var url = SITEURL+'article/ajax_article_order';
            $.getJSON(url,{offset:new_article_index,count:1,havepic:false},function(data){
                if(data){
                    var html = template("tpl_new_article",data);
                    $(".news_random ul").html(html);
                    new_article_index++;
                }
                layer.closeAll();
            })
        }
        function jump_search()
        {
            var url = SITEURL+'article/search';
            location.href = url;
        }
    </script>

</body>
</html>
