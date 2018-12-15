<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>{$seoinfo['seotitle']}-{$webname}</title>
    {if $seoinfo['keyword']}
    <meta name="keywords" content="{$seoinfo['keyword']}" />
    {/if}
    {if $seoinfo['description']}
    <meta name="description" content="{$seoinfo['description']}" />
    {/if}
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    {Common::css('amazeui.css,style.css,extend.css')}
    {Common::js('jquery.min.js,amazeui.js')}
</head>

<body>

{request "pub/header/typeid/$typeid"}

  <section>

  	<div class="mid_content">

      <div data-am-widget="slider" class="am-slider am-slider-default" data-am-slider='{}' >
        <ul class="am-slides">
            {st:ad action="getad" name="s_spot_index_1"}
                {loop $data['aditems'] $v}
                    <li><a href="{$v['adlink']}"><img src="{Common::img($v['adsrc'])}" title="{$v['adname']}"></a></li>
                {/loop}
            {/st}
        </ul>
      </div><!--轮播图-->

	  <div class="st_search_box">
      	<input type="text" class="st_home_txt search" placeholder="搜索{$channelname}" />
        <input type="button" class="st_home_btn search" value="搜索" />
      </div><!--搜索-->

      <div class="mp_advimg_list">
      	<p>
            {st:ad action="getad" name="spot_index_2"}
               {loop $data['adsrc'] $v}
                 <img src="{Common::img($v)}">
               {/loop}
            {/st}
        </p>
        <p>
          {st:ad action="getad" name="spot_index_3"}
            {loop $data['adsrc'] $v}
              <img src="{Common::img($v)}">
            {/loop}
           {/st}
        </p>
      </div><!--广告位类型-->

      <div class="st_hot_list">
      	<div class="st_tit">
        	<h3>{$channelname}推荐</h3>
        </div>
        <div class="st_list_con">
        	<ul class="st_list_ul">
            {st:spot action="query" flag="order" row="4"}
               {loop $data $row}
                <li>
                    <a href="{$row['url']}">
                    <img src="{Common::img($row['litpic'])}" />
                    <p class="tit">{$row['title']}</p>
                    {if $row['price']}
                    <p class="num">
                        <span>&yen;<strong>{$row['price']}</strong>起</span>
                    </p>
					{else}
                        <p class="num">
                            <span><strong>电询</strong></span>
                        </p>
                    {/if}
                  </a>
                </li>
               {/loop}
            {/st}

          </ul>
          <div class="list_more"><a href="{$cmsurl}spots/all">查看更多</a></div>
          <div style=" clear:both"></div>
        </div>
      </div><!--门票-->
        {st:dest action="query" flag="channel_nav" typeid="$typeid" row="3"}
         {loop $data $row}
              <div class="st_hot_list">
                <div class="st_tit">
                    <h3>{$row['kindname']}{$channelname}</h3>
                </div>
                <div class="st_list_con">
                    <ul class="st_list_ul">
                        {st:spot action="query" flag="mdd" destid="$row['id']" return="list" row="4"}
                        {loop $list $h}
                        <li>
                            <a href="{$h['url']}">
                                <img src="{Common::img($h['litpic'])}" alt="{$h['title']}" />
                                <p class="tit">{$h['title']}</p>
                                {if $h['price']}
                                 <p class="num"><span>&yen;<strong>{$h['price']}</strong>起</span></p>
								 {else}
                        <p class="num">
                            <span><strong>电询</strong></span>
                        </p>
                                {/if}
                            </a>
                        </li>
                        {/loop}
                  </ul>
                  <div class="list_more"><a href="{$cmsurl}spots/all">查看更多</a></div>
                  <div style=" clear:both"></div>
                </div>
              </div>
        {/loop}
        {/st}


    </div>

  </section>

{request "pub/code"}
{request "pub/footer"}

</body>
<script>
    $(function(){
        $('.search').click(function(){
            window.location.href = SITEURL+'spot/search';
        })
    })
</script>
</html>