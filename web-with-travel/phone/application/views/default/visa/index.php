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
    {php echo Common::js('jquery.min.js,amazeui.js,common.js');}

</head>
{request "pub/header/typeid/$typeid"}
<section>
    <div class="mid_content">
    <div data-am-widget="slider" class="am-slider am-slider-default" data-am-slider='{}' >
        <ul class="am-slides">
            {st:ad action="getad" name="s_visa_index_1"}
                {loop $data['aditems'] $v}
                    <li><a href="{$v['adlink']}"><img src="{Common::img($v['adsrc'])}" title="{$v['adname']}"></a></li>
                {/loop}
            {/st}
        </ul>
    </div><!--轮播图-->

    <div class="mid_content">

        <div class="st_search_box" id="search">
            <input type="text" class="st_home_txt" placeholder="国家/地区" />
            <input type="button" class="st_home_btn" value="搜索" />
        </div><!--搜索-->

        <div class="hot_country">
            <div class="tit">
                <h3>热门国家/地区</h3>
                <a href="{$cmsurl}visa/search">更多</a>
            </div>
            <div class="hot_cou_con">
                <ul>
                    {st:visa action="area" flag="order" row="6"}
                        {loop $data $v}
                            <li><a href="{$v['url']}"><img src="{$v['litpic']}" /><span>{$v['kindname']}</span></a></li>
                        {/loop}
                    {/st}
                    <ul>
                    <div style=" clear:both"></div>
            </div>
        </div>

        <div class="flow_path">
            <div class="tit">
                <h3>签证办理，快人一步</h3>
                <p>省时 · 省事 · 省心 · 省力</p>
            </div>
            <div class="path_con">
                <ul>
                    <li><span><img src="/phone/public/images/visa_home01.png" /></span>提交签证材料</li>
                    <li><span><img src="/phone/public/images/visa_home02.png" /></span>审核材料</li>
                    <li><span><img src="/phone/public/images/visa_home03.png" /></span>送签(面试)</li>
                    <li><span><img src="/phone/public/images/visa_home04.png" /></span>签证</li>
                </ul>
                <p><i></i>出签成功率99.8%</p>
            </div>
        </div>

    </div>

</section>
{request 'pub/code'}
{request 'pub/footer'}

<body>
<script type="text/javascript">

    $('doucument').ready(function(){
        $('#search').find('input').focus(function(){
          window.location.href=SITEURL+'visa/search';
        });

    });
</script>
</body>
</html>
