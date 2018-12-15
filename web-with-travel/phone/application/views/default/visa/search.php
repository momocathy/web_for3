<!doctype html>
<html>
<head>
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

{request "pub/header/typeid/$typeid"}
<section>

    <div class="mid_content">

        <div class="st_search_box">
            <input type="text" class="st_home_txt" id="area" name="keywords" placeholder="签证地区" />
            <input type="button" class="st_home_btn" value="搜索" />
        </div><!--搜索-->

        <div class="term-list">
            <div class="tabcon visa">
                <div class="tablist">
                    <ul id="list-province">
                        <li class="cursor on">热门</li>
                        {st:visa action="area" flag="query" pid="0" row="100"}
                        {loop $data $v}
                        <li class="cursor">{$v['kindname']}</li>
                        {/loop}
                        {/st}
                    </ul>
                    <ul class="content">
                        {st:visa action="area" flag="order" row="10"}
                        {loop $data $v}
                        <li><a href="{$v['url']}">{$v['kindname']}</a></li>
                        {/loop}
                        {/st}
                    </ul>
                    {st:visa action="area" flag="query" pid="0" row="100"}
                        {loop $data $v}
                            <ul class="content hide">
                                {st:visa action="area" flag="query" pid="$v['id']" return="son" row="100"}
                                    {loop $son $r}
                                    <li><a href="{$r['url']}">{$r['kindname']}</a></li>
                                    {/loop}
                                {/st}
                            </ul>
                        {/loop}
                    {/st}
                </div>
            </div>
        </div><!--栏目筛选-->
        <script>
            $(function(){
                $('.tabcon').css({position:'static'})
                $('.tabcon,.tablist').show()
            })
        </script>


    </div>

</section>
{request "pub/code"}
</body>
<script type="text/javascript">
    $('document').ready(function(){
         $('#list-province').find('li').click(function(){
             var index=$(this).index();
             $(this).addClass('on').siblings().removeClass('on');
             $('.tablist').find('.content:eq('+index+')').removeClass('hide').siblings('.content').addClass('hide');
         });
        $('.st_home_btn').click(function(){
            var val=$('#area').val();
            if(val.length>1){
                $.post(SITEURL+'visa/ajax_area',{'area':val},function(str){
                    if(str.length>2){
                       window.location.href=str;
                   }else{
                        alert('搜索地区不存在');
                    }
                },'text');
            }
        });
    });
</script>
</html>
