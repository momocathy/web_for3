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

        <div class="st_search_box">
            <input type="text" class="st_home_txt" placeholder="攻略关键词" />
            <input type="button" class="st_home_btn" value="搜索" />
        </div><!--搜索-->

        <div class="mp_search_menu">
            <dl class="hot">
                <dt>热门目的地</dt>
                <dd>
                    {st:dest action="query" flag="hot" typeid="4" offset="0" row="9"}
                    {loop $data $row}
                    <span><a href="{$cmsurl}raiders/{$row['pinyin']}">{$row['kindname']}</a></span>
                    {/loop}
                    {/st}
                </dd>
            </dl>
        </div>
    </div>

</section>


{request 'pub/code'}
{request 'pub/footer'}

<script>

    $(function () {
        $(".st_home_btn").click(function () {
            var keyword = $('.st_home_txt').val();
            var url = SITEURL + 'raiders/all' + (keyword == "" ? "" : "?keyword=" + keyword);
            location.href = url;
        });
    })
</script>
</body>
</html>
