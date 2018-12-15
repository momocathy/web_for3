<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>{$seoinfo['seotitle']}-{$webname}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    {Common::css('amazeui.css,style.css,extend.css')}
    {Common::js('jquery.min.js,amazeui.js,template.js')}
    {Common::js('layer/layer.m.js')}
</head>

<body>
{request "pub/header/typeid/$typeid"}
<section>

    <div class="mid_content">

        <div class="term-list">
            {request "pub/select/typeid/$typeid/destid/$destid"}
        </div>
        <!--栏目筛选-->
        <div class="tuan_home_con" id="list-content">
            <ul class="tuan_hm_list" id="content">

            </ul>
            <div class="tuan_more" id="btn_getmore"><a class="cursor">加载更多</a></div>
        </div>
        <!--没有相关信息-->
        <div class="no-content" id="no-content" style="display: none">
            <img src="{$GLOBALS['cfg_public_url']}images/nocon.png"/>
            <p>啊哦，暂时没有相关信息</p>
        </div>

    </div>

    <input type="hidden" id="page" value="{$page}"/>
    <input type="hidden" id="status" value="{$status}"/>
    <input type="hidden" id="keyword" value="{$keyword}"/>


</section>
<script type="text/html" id="tpl_tuan_item">
    {{each list as value i}}
    <li>
        <a href="{{value.url}}">
            <img class="pic" src="{{value.litpic}}"/>

            <div class="date_time" start-time="{{value.starttime}}" end-time="{{value.endtime}}">
                <em></em>
                <span></span>
            </div>
            <p class="tit">{{value.title}}</p>

            <p class="txt">{{value.sellpoint}}</p>

            <p class="price">
                <span>&yen;<b>{{value.price}}</b></span>
                <i>{{value.discount}}折</i>
                <em>已售：{{value.sellnum}}</em>
            </p>
        </a>
    </li>
    {{/each}}
</script>
{request "pub/code"}
{request "pub/footer"}
</body>
<script>
    //初始页码
    var initpage = '{$page}';
    $(function () {
        if(initpage !=  $("#page").val()){
            $("#page").val(initpage);
        }
        //pub/select 确定选择点击事件在这里重写.
        $('.sure_btn').click(function () {
            $("#page").val(1);
            $('.tabcon').hide();
            $('.tab_bottom_btn').hide();
            var url = SITEURL + 'tuan/' + get_url();
            //get_data();
            window.location.href = url;

        })
        //获取更新数据
        $('.btn_getmore').click(function () {
            get_data();

        })
        //第一次加载数据
        get_data();
    })
    //获取list地址
    function get_url() {
        //获取选中的目的地
        var url = $("#destpy").val();
        var status = $("#status").val();
        //获取选中的属性
        var attr = [];
        $('#lsit-child').find("li[class^='gattr']").each(function (i, obj) {
            if ($(obj).attr('data-type') == 'attrid' && $(this).hasClass('on')) {
                attr.push($(this).attr('data-id'));
            }
        })
        var attrid = $("#attrid").val();
        if (attr.length > 0) {
            attrid = attr.join('_');
        }

        //当前页 page
        var page = $("#page").val();
        url += '-' + status + '-' + attrid + '-' + page;
        //排序规则
        var sorttype = $("#sorttype").val();
        url += '?sorttype=' + sorttype;
        //搜索名称
        var keyword = $('#keyword').val();
        if (keyword != 0) {
            url += '&keyword=' + keyword;
        }
        return url;
    }
    //ajax获取数据
    var contentNum = 0;
    function get_data() {
        var paramUrl = get_url();
        var url = SITEURL + 'tuan/ajax_tuan_data/' + paramUrl;
        layer.open({
            type: 2,
            content: '',
            time: 20
        });
        $.getJSON(url, {}, function (data) {
            if (data.list.length > 0) {
                var itemHtml = template('tpl_tuan_item', data);
                $("#content").append(itemHtml);
                $('#content').find('.date_time').each(function (index, element) {
                    show_count(element);
                });
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
            layer.closeAll();
        })
    }
    function show_count(node) {
        var endTime = $(node).attr('end-time') * 1000;
        var startTime = $(node).attr('start-time') * 1000;
        var timer_rt = window.setInterval(function () {
            var time;
            var now = new Date();
            now = now.getTime();
            if (startTime > now) {
                time = startTime - now;
                $(node).find('em').html('开始时间');
            } else if (endTime > now) {
                time = endTime - now;
                $(node).find('em').html('结束时间');
            } else {
                $(node).find('em').html('已结束');
                $(node).parents('li').remove();
                clearInterval(timer_rt);
            }
            time = parseInt(time / 1000);
            var day = Math.floor(time / (60 * 60 * 24));
            var hour = Math.floor((time - day * 24 * 60 * 60) / 3600);
            var minute = Math.floor((time - day * 24 * 60 * 60 - hour * 3600) / 60);
            var html = '';
            if (day > 0) {
                html += day + '天';
            }
            if (hour > 0) {
                html += hour + '时';
            }
            if (minute > 0) {
                html += minute + '分';
            }
            $(node).find('span').html(html);
        }, 1000);
    }
</script>
</html>
