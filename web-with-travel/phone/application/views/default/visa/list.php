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
    {php echo Common::js('jquery.min.js,amazeui.js,template.js,layer/layer.m.js');}


</head>
<body>
{request "pub/header/typeid/$typeid"}
<section>

    <div class="mid_content">
        <div class="visa_top">
            <div class="tit">
                <div class="pic"><img src="{$area['litpic']}" /></div>
                <div class="bt">
                    <span>{$area['kindname']}</span>
                    <p>{php echo strip_tags($area['jieshao']);}</p>
                </div>
            </div>
            <div class="sig">
                <span>签发地<i class="am-icon-caret-down cursor" id="down"></i></span>
            </div>
            <div class="mdd_sub" id="mdd_sub">
                {loop $city $v}
                <a data="{$v['id']}" class="cursor">{$v['kindname']}</a>
                {/loop}
            </div>
        </div>

        <div class="visa_list_con">
            <ul id="content">

            </ul>
            <div class="tuan_more" id="btn_getmore" style="display: none"><a class="cursor" id="more">加载更多</a></div>
        </div>
        <!--没有相关信息-->
        <div class="no-content" id="no-content" style="display: none">
            <img src="{$GLOBALS['cfg_public_url']}images/nocon.png"/>
            <p>啊哦，暂时没有相关信息</p>
        </div>

    </div>
    <input type="hidden" id="page" value="{$page}"/>
    <input type="hidden" id="cityid" value="{$cityid}"/>
    <input type="hidden" id="area" value="{$areapy}"/>
    <input type="hidden" id="typeid" value="{$typeid}"/>
</section>
{request 'pub/code'}
{request 'pub/footer'}
<script id="template" type="text/html">
        {{each list as v i}}
        <li>
            <a href="{{v['url']}}">
                <div class="pic"><img src="{{v['litpic']}}" /></div>
                <div class="con">
                    <p class="tit">{{v['title']}}</p>
                    <p class="txt">{{v['handleday']}}</p>
                    {{if v['city']}}
                    <p class="city">{{v['city']}}</p>
                    {{/if}}
                    <p class="price">&yen;<span>{{v['price']}}</span></p>
                </div>
            </a>
        </li>
        {{/each}}
</script>

</body>
<script type="text/javascript">
    //初始页码
    var initpage = '{$page}';
    $(function () {
        if(initpage !=  $("#page").val()){
            $("#page").val(initpage);
        }
        visa_ajax();
        var contentNum = 0;
        //mdd_sub
        $('#mdd_sub').find('a').click(function(){
           $url=implode_uri($(this).attr('data'));
            window.location.href=SITEURL+'visa/'+$url;
        });
        function implode_uri(){
            var cityid,page,area,typeid;
            cityid=arguments[0]?arguments[0]:$('#cityid').val();
            page=$('#page').val();
            area=$('#area').val();
            typeid=$('#typeid').val();
            return area+'-'+cityid+'-'+typeid+'-'+page;
        }
        function visa_ajax(){
            layer.open({
                type: 8,
                content: '正在加载数据...',
                time :20

            });
            $.get(SITEURL+'visa/ajax_visa_more/'+implode_uri(),{},function(data){
                if(data.list.length>0){
                    contentNum++;
                    var html = template('template', data);
                    $('#content').append(html);
                }
                //设置分页
                if (data.page != -1) {
                    $("#btn_getmore").show();
                    $("#page").val(data.page);
                } else {
                    $("#btn_getmore").hide();
                }
                //设置内内容显示
                if (contentNum == 0) {
                    $('#list-content').hide();
                    $("#no-content").show();
                }
                layer.closeAll();
            },'json');
        }
        $('#more').click(function(){
            visa_ajax();
        });
        //down
         var toggle=false;
        $('#down').click(function(){
            toggle=!toggle
            toggle?$('#mdd_sub').css('display','inline'):$('#mdd_sub').css('display','none');
        });
     });
</script>
</html>
