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
    {php echo Common::js('jquery.min.js,amazeui.js');}
</head>
<body>
{request "pub/header/typeid/$typeid/isshowpage/1"}
  
  <section>
   <div class="mid_content">
  	<div class="tuan_slide_pic">
      <div class="date_time" id="time" start-time="{$info['starttime']}" end-time="{$info['endtime']}">
        <em></em>
        <span></span>
      </div>
      <div data-am-widget="slider" class="am-slider am-slider-default" data-am-slider='{}' >
        <ul class="am-slides">
            {loop $info['piclist'] $pic}
            <li><img src="{Common::img($pic[0])}"></li>
            {/loop}
        </ul>
      </div><!--轮播图-->
      <div class="show_pic_nr">
      	<p class="tit">{$info['title']}</p>
        <p class="txt">{$info['sellpoint']}</p>
        <p class="price"><span>&yen;<b>{$info['price']}</b></span><i>{$info['discount']}折</i></p>
        <p class="num">
        	<span><strong>{$info['sellnum']}</strong>销量</span>
            <span><strong>{$info['satisfyscore']}%</strong>满意度</span>
          <span class="pl"><strong>{$info['commentnum']}</strong>人点评<i class="cursor">&gt;</i></span>
        </p>
      </div>
    </div>
    
    <div class="mid_content">
    	<div class="tuan_cont_list">
            {st:detailcontent action="get_content" typeid="13" productinfo="$info"}
            {loop $data $row}
                <div class="list_show_box">
                    <div class="tit"><strong>{$row['chinesename']}</strong></div>
                  <div class="list_show_con">
                      {$row['content']}
                  </div>
                </div>
            {/loop}
            {/st}
        </div>
    </div>
  </section>
{request 'pub/code'}
{request 'pub/footer'}
  
  <div class="bom_link_box">
  	<div class="bom_fixed">
    	<a href="tel:{$GLOBALS['cfg_m_phone']}">电话咨询</a>
      <a class="on cursor order" data-id="{$info['id']}">立即预定</a>
    </div>
  </div>
<script type="text/javascript">
    $('.pl').click(function(){
        var url = SITEURL+"pub/comment/id/{$info['aid']}/typeid/{$typeid}";
        window.location.href = url;
    })
    $('.order').click(function(){
        var productid = $(this).attr('data-id');
        url = SITEURL+'tuan/book/id/'+productid;
        window.location.href = url;
    })
</script>
<script type="text/javascript">
    $('document').ready(function(){
        var endTime=$('#time').attr('end-time')*1000;
        var startTime=$('#time').attr('start-time')*1000;
        var timer_rt = window.setInterval(function(){
            var time;
            var now = new Date();
            now =now.getTime();
            if(startTime>now){
                time=startTime-now;
                $('#time').find('em').html('开始时间');
            }else if(endTime>now){
                time=endTime-now;
                $('#time').find('em').html('结束时间');
            }else{
                $('#time').find('em').html('已结束');
                clearInterval(timer_rt);
            }
            time = parseInt(time/1000);
            var day=Math.floor(time/(60*60*24));
            var hour=Math.floor((time-day*24*60*60)/3600);
            var minute=Math.floor((time-day*24*60*60-hour*3600)/60)
            var html='';
            if(day>0){
                html+=day+'天';
            }
            if(hour>0){
                html+=hour+'时';
            }
            if(minute>0){
                html+=minute+'分';
            }
            $('#time').find('span').html(html);
        }, 1000);
    });
</script>
</body>
</html>
