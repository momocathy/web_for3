<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>{$seoinfo['seotitle']}-{$GLOBALS['cfg_webname']}</title>
    {if $seoinfo['keyword']}
    <meta name="keywords" content="{$seoinfo['keyword']}"/>
    {/if}
    {if $seoinfo['description']}
    <meta name="description" content="{$seoinfo['description']}"/>
    {/if}
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    {Common::css('amazeui.css,style.css')}
    {Common::js('jquery.min.js,amazeui.js')}
</head>

<body>

{request "pub/header/typeid/$typeid"}

<section>

    <div class="mid_content">
        <div class="jb_show_top">
            <h1>{$info['title']}</h1>

            <div class="jb_user_name">
                <a href="#">
                    <img src="{$info['member']['litpic']}"/>

                    <p>
                        <strong>{$info['member']['name']}</strong>
                        <span>{date('Y-m-d',$info['addtime'])}发表</span>
                    </p>
                </a>

                <div class="pelple_num"><span>{$info['joinnum']}</span>人加入</div>
            </div>
        </div>

        <div class="jb_huodong_msg">
            <ul>
                <li><em class="mdd">目的地</em><span>{$info['kindnamelist']}</span></li>
                <li><em class="day">行程天数</em><span>{$info['day']}天</span></li>
                <li><em class="date">出发日期</em><span>{$info['startdate']}  可调动日期±{$info['vartime']}天</span></li>
                <li><em class="num">Ta的人数</em><span>成人{$info['adultnum']}名  儿童{$info['childnum']}名</span></li>
                <li>
                    <em class="type">活动类型</em>
                  <span>
                      {loop $info['attrlist'] $attr}
                      {$attr['attrname']}
                      {/loop}
                  </span>
                </li>
            </ul>
        </div>
        <!--结伴内容-->

        <div class="hd_cont_nr">
            <div class="tit">活动说明</div>
            <div class="txt">
                {$info['memo']}
            </div>
        </div>
        <!--活动说明-->

        <div class="tuijian_jb">
            <div class="tit">参考推荐</div>
            <div class="con">
                <ul>
                    {loop $info['recommendlist'] $row}
                    <li>
                        <a href="{$row['url']}">
                            <div class="pic"><img src="{$row['litpic']}"/></div>
                            <div class="txt">
                                <p>{$row['title']}</p>
                                <span>参考价：&yen;<b>{$row['price']}</b>起/人</span>
                            </div>
                        </a>
                    </li>
                    {/loop}
                    {/st}
                </ul>
            </div>
        </div>
        <!--参考推荐-->
    </div>

</section>

{request 'pub/code'}
{request 'pub/footer'}

<div class="bom_link_box">
    <div class="bom_fixed">
        <a href="javascript:void(0)">{$info['joinnum']}人已加入</a>
        <a class="on" href="{$cmsurl}jieban/join/id/{$info['id']}">立即加入</a>
    </div>
</div>

</body>
</html>
