<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>{$channelname}搜索-{$webname}</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    {Common::css('amazeui.css,style.css,extend.css')}
    {Common::js('jquery.min.js,amazeui.js')}

</head>

<body>

{request "pub/header/typeid/$typeid"}
  
  <section>
    
  	<div class="mid_content">
     
	 <div class="st_search_box">
      	<input type="text" class="st_home_txt" id="keyword" placeholder="{$channelname}名称" />
        <input type="button" class="st_home_btn btn_search" value="搜索" />
      </div><!--搜索-->
      
      <div class="mp_search_menu">
      	<dl class="hot">
        	<dt>热门{$channelname}目的地</dt>
          <dd id="destlist">
             {st:dest action="query" flag="hot" typeid="$typeid" row="10"}
              {loop $data $r}
              <span><a href="javascript:;" data-id="{$r['id']}" data-pinyin="{$r['pinyin']}">{$r['kindname']}</a></span>
              {/loop}
             {/st}

          </dd>
        </dl>

       {st:attr action="query" flag="grouplist" typeid="$typeid"}
        {loop $data $group}
      	<dl class="theme">
        	<dt>{$group['attrname']}</dt>
          <dd>
            {st:attr action="query" flag="childitem" return="list" typeid="$typeid" groupid="$group['id']" row="20"}
              {loop $list $ar}
          	    <span><a href="javascript:;" data-id="{$ar['id']}">{$ar['attrname']}</a></span>
              {/loop}

          </dd>
        </dl>
        {/loop}
      {/st}
      </div>

    </div>
    
  </section>
<input type="hidden" id="destpy" name="destpy" value="all"/>
{request "pub/code"}
{request "pub/footer"}
<script>
    $(function(){

        //目的地点击
        $("#destlist").find('a').click(function(){
            var url = SITEURL+'spots/'+$(this).attr('data-destpy');
            window.location.href = url;

        })

        //属性点击
        $(".theme").find('a').click(function(){
            var attrid = $(this).attr('data-id');
            var url = SITEURL+'spots/all-0-0-0-'+attrid;
            window.location.href = url;

        })



        //搜索页面
        $(".btn_search").click(function(){

            var keyword = $("#keyword").val();
            var url = SITEURL+'spots/all?keyword='+encodeURIComponent(keyword);
            window.location.href = url;
        })
    })
</script>
</body>
</html>
