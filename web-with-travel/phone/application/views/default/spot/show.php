<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>{$seoinfo['seotitle']}-{$webname}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    {if $seoinfo['keyword']}
    <meta name="keywords" content="{$seoinfo['keyword']}" />
    {/if}
    {if $seoinfo['description']}
    <meta name="description" content="{$seoinfo['description']}" />
    {/if}
    {Common::css('amazeui.css,style.css,extend.css')}
    {Common::js('jquery.min.js,amazeui.js')}
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
      	<p class="tit">{$info['title']}</p>
      	<p class="txt">{$info['sellpoint']}</p>
      	<p class="price">
            {if $info['hasticket']}
        	 <span>&yen;<b>{$info['price']}</b>起</span>
             <del>原价:&yen;{$info['sellprice']}</del>
            {/if}
        </p>
       {if $info['hasticket']==1}
      	<p class="num">
            <span><b>{$info['sellnum']}</b>销量</span>
            <span><b>{$info['satisfyscore']}%</b>满意度</span>
            <span class="pl"><b>{$info['commentnum']}</b>评论<i>&gt;</i></span>
        </p>
       {/if}
      </div><!--顶部介绍-->
      
      <div class="cp_show_msg">
      	<h3>产品简介</h3>
        <ul>
          <li><span>景区主题：</span>
              {loop $info['attrlist'] $attr}
                 {$attr['attrname']}&nbsp;&nbsp;
              {/loop}
          </li>
          <li><span>取票方式：</span>{$info['getway']}</li>
          <li><span>景区地址：</span>{$info['address']}</li>
        </ul>
        {if $info['hasticket']}
          <div class="opt_type order" data-id="{$info['id']}">选择门票、价格类型<i>&gt;</i></div>
        {/if}
      </div><!--产品信息-->
        {st:detailcontent action="get_content" typeid="$typeid" productinfo="$info" onlyrealfield="1"}
        {loop $data $row}
        <div class="line_tablist_box">
            <div class="bt_tit"><span>{$row['chinesename']}</span></div>
            <div class="cont_nr">
                {$row['content']}
            </div>
        </div>
        {/loop}
        {/st}


    </div>
    
  </section>


{request 'pub/footer'}
{if $info['hasticket']}
<div class="bom_link_box">
  <div class="bom_fixed">
    <a href="tel:{$GLOBALS['cfg_m_phone']}">电话咨询</a>
      <a class="on order" data-id="{$info['id']}" href="javascript:;">立即预定</a>
    </div>
  </div>
{/if}
<script>
    $('.pl').click(function(){
        var url = SITEURL+"pub/comment/id/{$info['id']}/typeid/{$typeid}";
        window.location.href = url;
    })

    //预订按钮

    $('.order').click(function(){
        var productid = $(this).attr('data-id');
        url = SITEURL+'spot/book/id/'+productid;
        window.location.href = url;
    })

</script>

</body>
</html>
