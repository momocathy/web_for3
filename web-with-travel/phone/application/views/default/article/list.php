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
    {Common::js('jquery.min.js,amazeui.js,template.js')}


</head>

<body>
{request "pub/header/typeid/$typeid"}
<section>

    <div class="mid_content">
        <div class="term-list">
            {request "pub/select/typeid/$typeid/destid/$destid"}
        </div><!--栏目筛选-->

        <div class="gl_hot_list" id="list-content">
            <ul>

            </ul>
            <div class="tuan_more" id="btn_getmore"><a class="cursor">加载更多</a></div>
        </div><!--成都攻略-->
        <!--没有相关信息-->
        <div class="no-content" id="no-content" style="display: none">
            <img src="{$GLOBALS['cfg_public_url']}images/nocon.png"/>
            <p>啊哦，暂时没有相关信息</p>
        </div>

        <div class="hot_city">
            <h3 class="tit"><span>热门目的地</span></h3>
            <ul class="hot_list">
                {st:dest action="query" flag="hot" typeid="4" offset="0" row="16"}
                <li>
                    {loop $data $row}
                    {if $n % 4 == 0}
                    <a href="{$cmsurl}raiders/{$row['pinyin']}">{$row['kindname']}</a>
                </li><li>
                    {else}
                    <a href="{$cmsurl}raiders/{$row['pinyin']}">{$row['kindname']}</a>
                    {/if}
                    {/loop}
                </li>
                {/st}
            </ul>
        </div><!--热门目的地-->
    </div>
    <input type="hidden" id="page" value="{$page}"/>

</section>

<script type="text/html" id="tpl_article_list">

    {{each list as value i}}
    <li>
        <a href="{{value.url}}">
            <div class="pic"><img src="{{value.litpic}}" alt="{{value.title}}" /></div>
            <div class="con">
                <p class="bt">{{value.title}}</p>
                <p class="txt">{{value.summary}}</p>
                <p class="data">
                    <span class="mdd">{{value.lastdest.kindname}}</span>
                    <span class="num">{{value.shownum}}</span>
                </p>
            </div>
        </a>
    </li>
    {{/each}}
</script>

{Common::js('layer/layer.m.js')}

<script>

    var keyword = "{$keyword}";
    var destid = "{$destid}";
    var sorttype = "{$sorttype}";
    var attrid = "{$attrid}"
    //初始页码
    var initpage = '{$page}';
    $(function () {
        if(initpage !=  $("#page").val()){
            $("#page").val(initpage);
        }
        //pub/select 确定选择点击事件在这里重写.
        $('.sure_btn').click(function(){
            $("#page").val(1);
            $('.tabcon').hide();
            $('.tab_bottom_btn').hide();
            var url =SITEURL+'raiders/'+ get_url();
            window.location.href = url;
        })

        //获取更新数据
        $('#btn_getmore').click(function(){
            get_data();
        })

        //第一次加载数据
        get_data();
    })

    //获取list地址
    function get_url()
    {
        //获取选中的目的地
        var pinyin = $("#destpy").val();
        var url = pinyin == "" ? "all" : pinyin;

        //获取选中的属性
        var attr=[];
        $('#lsit-child').find("li[class^='gattr']").each(function(i,obj){
            if($(obj).attr('data-type')=='attrid' && $(this).hasClass('on')){
                attr.push($(this).attr('data-id'));
            }
        })
        var attrid = $("#attrid").val();
        if(attr.length>0){
            attrid = attr.join('_');
        }
        attrid = attrid == "" ? "0" : attrid;

        //排序规则
        var sorttype = $("#sorttype").val();
        sorttype = sorttype == "" ? "0" : sorttype;
        keyword = keyword == "" ? "0" : keyword;

        url += '-' + sorttype + '-' + attrid + '?keyword=' + encodeURIComponent(keyword);
        return url;
    }

    //ajax获取数据
    var contentNum = 0;
    function get_data()
    {
        layer.open({
            type: 2,
            content: '正在加载数据...',
            time :20

        });

        var url = SITEURL+'article/ajax_article_more';
        $.getJSON(url,{page:$("#page").val(),destid:destid,sorttype:sorttype,attrid:attrid,keyword:keyword},function(data){


            if (data.list.length > 0) {
                var itemHtml = template('tpl_article_list', data);
                console.log(itemHtml);
                $(".gl_hot_list ul").append(itemHtml);
                contentNum++;
            }
            //设置分页
            if (data.page != -1) {
                $("#page").val(data.page);
            } else {
                $("#btn_getmore").hide();
            }
            //设置内内容显示
            if (contentNum == 0) {
                $('#list-content').hide();
                $("#no-content").show();
            }
            if (data.page != -1) {
                $("#page").val(data.page);
            } else {
                $("#btn_getmore").hide();
            }
            layer.closeAll();
        });

    }
</script>
{request "pub/code"}
{request "pub/footer"}

</body>
</html>
