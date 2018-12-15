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
    {Common::css('amazeui.css,style.css,../js/layer/need/layer.css')}
    {Common::js('jquery.min.js,amazeui.js,template.js,layer/layer.m.js')}
</head>

<body>

{request "pub/header/typeid/$typeid"}

<section>

    <div class="mid_content">

        <div class="jb_top_banner">
            <img src="{$GLOBALS['cfg_public_url']}images/jb_banner.jpg" width="100%"/>
            <a class="fb_jb_btn" href="{$cmsurl}jieban/add"><img src="{$GLOBALS['cfg_public_url']}images/jieban_join_btn.png"/></a>
        </div>

        <div class="jb_list_home" id="list-content">
            <h3><span>推荐结伴</span></h3>
            <ul>
            </ul>
            <div class="tuan_more" id="btn_getmore"><a href="javascript:;">查看更多</a></div>
        </div>

        <div class="jieban_lc">
            <div class="bt">轻松四步玩转结伴速拼</div>
            <div class="con"><img src="{$GLOBALS['cfg_public_url']}images/jieban_lc.png"/></div>
            <div class="jieban_open"><i></i><a href="{$cmsurl}jieban/add">发布结伴</a></div>
        </div>

    </div>
    <input type="hidden" id="page" value="{$page}"/>

</section>

{request 'pub/code'}
{request 'pub/footer'}

<script type="text/html" id="tpl_jieban_list">
    {{each list as value i }}
    <li id="li_{{value.id}}">
        <a class="pic" href="{{value.url}}">
            <img src="{{value.litpic}}" alt="{{value.title}}"/>
            <span class="day_date">
                <strong><b>{{value.day}}</b>日游</strong>
                <em>{{value.startdate}}出发</em>
            </span>
        </a>
        <p class="tit"><a href="{{value.url}}">{{value.title}}</a></p>

        <p class="txt">{{value.description}}</p>

        <p class="attr">
            {{each value.attrlist as attr j }}
            <span>{{attr.attrname}}</span>
            {{/each}}
        </p>

        <p class="join"><span>已有<b>{{value.joinnum}}</b>人报名</span><em>{{value.shownum}}</em><a href="{$cmsurl}jieban/join/id/{{value.id}}">立即加入</a></p>
    </li>
    {{/each}}
</script>
<script>
    //初始页码
    var initpage = '{$page}';
    $(function () {
        if(initpage !=  $("#page").val()){
            $("#page").val(initpage);
        }
        get_data();
        $("#btn_getmore").click(function(){
            get_data();
        });
        //ajax获取数据
        var contentNum = 0;
        function get_data() {
            layer.open({
                type: 2,
                content: '正在加载数据...',
                time :20
            });
            var url = '{$cmsurl}jieban/ajax_jieban_more';
            $.getJSON(url,{page:$("#page").val()},function(data){
                if (data.list.length > 0) {
                    var html = template("tpl_jieban_list",data);
                    $(".jb_list_home ul").append(html);
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
                }
                layer.closeAll();
            });
        }
    })
</script>
</body>
</html>
