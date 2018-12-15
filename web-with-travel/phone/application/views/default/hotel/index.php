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
{Common::js('jquery.min.js,amazeui.js')}

</head>
<body>
  {request "pub/header/typeid/$typeid"}
  <section>
    
  	<div class="mid_content">
    
      <div data-am-widget="slider" class="am-slider am-slider-default" data-am-slider='{}' >
        <ul class="am-slides">
          {st:ad action="getad" name="s_hotel_index_1"}
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

      <div class="hot_cp_slide">
      	<h3><span>{$channelname}推荐</span></h3>
      	<div class="st-slider">
          <ul class="am-slides">
              <!--按顺序排序酒店列表-->
            {st:hotel action="query" flag="order" row="3"}
              {loop $data $row}
                <li>
                    <a href="{$row['url']}">
                    <div class="pic"><img src="{Common::img($row['litpic'])}" /></div>
                    <div class="rig_txt">
                        <p class="tit">{$row['title']}</p>
                      <p class="attr">
                        {loop $row['attrlist'] $v}
                         <span>{$v['attrname']}</span>
                        {/loop}

                      </p>
                      <p class="num">
                       {if !empty($row['price'])}
                          <span>&yen;<b>{$row['price']}</b>起</span>
                       {else}
                          <span><b>电询</b></span>
                       {/if}
                       {if !empty($row['sellprice'])}
                        <del>原价：{$row['sellprice']}元</del>
                       {/if}
                      </p>
                    </div>
                  </a>
                </li>
              {/loop}
            {/st}
              <!--按顺序排序酒店列表结束-->

          </ul>
        </div>
      </div><!--产品推荐-->

      {st:dest action="query" flag="channel_nav" typeid="2" row="3"}
        {loop $data $row}
        <div class="st_hot_list">
      	<div class="st_tit">
        	<h3>{$row['kindname']}</h3>
          <a class="more" href="{$cmsurl}hotels/{$row['pinyin']}">更多&gt;</a>
        </div>
        <div class="st_list_con">
        	<ul class="st_list_ul">
             {st:hotel action="query" flag="mdd" destid="$row['id']" return="list" row="4"}
                {loop $list $h}
                    <li>
                           <a href="{$h['url']}">
                            <img src="{Common::img($h['litpic'])}" alt="{$h['title']}" />
                            <p class="tit">{$h['title']}</p>
                            <p class="num">
                                {if !empty($h['price'])}
                                  <span>&yen;<strong>{$h['price']}</strong>起</span>
                                {else}
                                  <span><strong>电询</strong></span>
                                {/if}

                            </p>
                          </a>
                    </li>
                {/loop}
          </ul>
          <div class="list_more"><a href="{$cmsurl}hotels/all">查看更多</a></div>
          <div style=" clear:both"></div>
        </div>
      </div><!--周边酒店-->
        {/loop}
      {/st}

    </div>
    
  </section>
  {request "pub/code"}
  {request "pub/footer"}

  <script>
      $(function(){
          $('.search').click(function(){
              var url = SITEURL+'hotel/search';
              window.location.href = url;
          })
      })
  </script>

</body>
</html>
