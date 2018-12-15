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

            <div class="dest_home">

                <div class="dest_hd">
                    <div class="pic">
                        <div data-am-widget="slider" class="am-slider am-slider-default" data-am-slider='{}' >
                            <ul class="am-slides">
                                {st:ad action="getad" name="destination_main_1" destid="$destinfo['id']"}
                                {loop $data['aditems'] $v}
                                <li><a href="{$v['adlink']}"><img src="{Common::img($v['adsrc'])}" title="{$v['adname']}"></a></li>
                                {/loop}
                                {/st}
                            </ul>
                        </div>
                    </div>
                    <div class="dest_msg">
                        <div class="name">
                            <strong>{$destinfo['kindname']}</strong>
                            <span>{strtoupper($destinfo['pinyin'])}</span>
                        </div>
                    </div>
                </div><!--目的地介绍-->

                <div class="dest_menu">
                    <a href="{$cmsurl}lines/{$destinfo['pinyin']}"><span><img src="{$cmsurl}public/images/menu_ico02.png" /><strong>旅游线路</strong></span></a>
                    <a href="{$cmsurl}photos/{$destinfo['pinyin']}"><span><img src="{$cmsurl}public/images/menu_ico10.png" /><strong>相册</strong></span></a>
                    <a href="{$cmsurl}hotels/{$destinfo['pinyin']}"><span><img src="{$cmsurl}public/images/menu_ico01.png" /><strong>酒店住宿</strong></span></a>
                    <a href="{$cmsurl}spots/{$destinfo['pinyin']}"><span><img src="{$cmsurl}public/images/menu_ico03.png" /><strong>景点门票</strong></span></a>
                    <a href="{$cmsurl}visa/{$destinfo['pinyin']}"><span><img src="{$cmsurl}public/images/menu_ico04.png" /><strong>签证</strong></span></a>
                    <a href="{$cmsurl}cars/{$destinfo['pinyin']}"><span><img src="{$cmsurl}public/images/menu_ico05.png" /><strong>租车</strong></span></a>
                    <a href="{$cmsurl}tuan/{$destinfo['pinyin']}"><span><img src="{$cmsurl}public/images/menu_ico06.png" /><strong>团购</strong></span></a>
                    <a href="{$cmsurl}raiders/{$destinfo['pinyin']}"><span><img src="{$cmsurl}public/images/menu_ico09.png" /><strong>攻略游记</strong></span></a>
                </div><!--目的地导航-->

            </div><!--目的地-->
        </div>

    </section>

    {request "pub/footer"}

</body>
</html>
