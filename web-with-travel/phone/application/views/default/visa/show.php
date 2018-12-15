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
    <script>
        $(function(){
            $('#my-st-slide').offCanvas('close')
        })
    </script>
</head>
<body>
{request "pub/header/typeid/$typeid/isshowpage/1"}
<section>

    <div class="mid_content">

        <div class="visa_show_top">
            <div class="pic"><img src="{Common::img($info['litpic'])}" /></div>
            <div class="con">
                <p class="tit">{$info['title']}</p>
                <p class="txt">{$info['sellpoint']}</p>
                <p class="price">&yen;<b>{$info['price']}</b></p>
                <p class="num">
                    <span><strong>{$info['sellnum']}</strong>销量</span>
                    <span><b>{$info['satisfyscore']}%</b>满意度</span>
                    <span class="pl"><strong>{$info['commentnum']}</strong>人点评<i class="cursor">&gt;</i></span>
                </p>
            </div>
        </div>

        <div class="show_msg">
            <div class="msg_con">
                <h3>产品信息</h3>
                <ul>
                    {if !empty($info['handleday'])}
                    <li><span>办理时间：</span>{$info['handleday']}</li>
                    {/if}
                    {if !empty($info['kindname'])}
                    <li><span>签证类型：</span>{$info['kindname']}</li>
                    {/if}
                    {if !empty($info['country'])}
                    <li><span>签证地区：</span>{$info['country']}</li>
                    {/if}
                    {if !empty($info['cityname'])}
                    <li><span>签发城市：</span>{$info['cityname']}</li>
                    {/if}
                    {if !empty($info['validday'])}
                    <li><span>有&nbsp;&nbsp;效&nbsp;&nbsp;期：</span>{$info['validday']}</li>
                    {/if}
                    <li><span>面试需要：</span>{if $info['needinterview']==1}需要{else}不需要{/if}</li>
                    <li><span>邀&nbsp;&nbsp;请&nbsp;&nbsp;函</span>{if $info['needletter']==1}需要{else}不需要{/if}</li>
                    {if !empty($info['partday'])}
                    <li><span>停留时间：</span>{$info['partday']}</li>
                    {/if}
                    {if !empty($info['acceptday'])}
                    <li><span>受理时间：</span>{$info['acceptday']}</li>
                    {/if}
                    {if !empty($info['handlepeople'])}
                    <li><span>受理人群：</span>{$info['handlepeople']}</li>
                    {/if}
                    {if !empty($info['belongconsulate'])}
                    <li><span>所属领管：</span>{$info['belongconsulate']}</li>
                    {/if}
                    {if !empty($info['handlerange'])}
                    <li><span>受理范围：</span>{$info['handlerange']}</li>
                    {/if}
                </ul>
            </div>
            <div class="xz_date"><span>选择出行时间</span><i class="on cursor order" data-id="{$info['id']}">&gt;</i></div>
        </div>

        <div class="show_cont">
            {if !empty($info['material'])}
            <div class="list_show_box">
                <div class="tit"><strong>材料所需</strong></div>
                <section data-am-widget="accordion" class="am-accordion am-accordion-gapped" data-am-accordion='{  }'>
                    {if !empty($info['material'])}
                    <dl class="am-accordion-item am-active">
                        <dt class="am-accordion-title">
                            在职人员
                        </dt>
                        <dd class="am-accordion-bd am-collapse am-in">
                            <!-- 规避 Collapase 处理有 padding 的折叠内容计算计算有误问题， 加一个容器 -->
                            <div class="am-accordion-content max_img">
                                {Product::strip_style($info['material'])}
                            </div>
                        </dd>
                    </dl>
                    {/if}
                    {if !empty($info['material2'])}
                    <dl class="am-accordion-item">
                        <dt class="am-accordion-title">
                            自由职业者
                        </dt>
                        <dd class="am-accordion-bd am-collapse ">
                            <!-- 规避 Collapase 处理有 padding 的折叠内容计算计算有误问题， 加一个容器 -->
                            <div class="am-accordion-content max_img">
                                {Product::strip_style($info['material2'])}
                            </div>
                        </dd>
                    </dl>
                    {/if}
                    {if !empty($info['material3'])}
                    <dl class="am-accordion-item">
                        <dt class="am-accordion-title">
                            退休人员
                        </dt>
                        <dd class="am-accordion-bd am-collapse ">
                            <!-- 规避 Collapase 处理有 padding 的折叠内容计算计算有误问题， 加一个容器 -->
                            <div class="am-accordion-content max_img">
                                {Product::strip_style($info['material3'])}
                            </div>
                        </dd>
                    </dl>
                    {/if}
                    {if !empty($info['material4'])}
                    <dl class="am-accordion-item">
                        <dt class="am-accordion-title">
                            在校学生
                        </dt>
                        <dd class="am-accordion-bd am-collapse ">
                            <!-- 规避 Collapase 处理有 padding 的折叠内容计算计算有误问题， 加一个容器 -->
                            <div class="am-accordion-content max_img">
                                {Product::strip_style($info['material4'])}
                            </div>
                        </dd>
                    </dl>
                    {/if}
                    {if !empty($info['material5'])}
                    <dl class="am-accordion-item">
                        <dt class="am-accordion-title">
                            学龄前儿童
                        </dt>
                        <dd class="am-accordion-bd am-collapse ">
                            <!-- 规避 Collapase 处理有 padding 的折叠内容计算计算有误问题， 加一个容器 -->
                            <div class="am-accordion-content max_img">
                                {Product::strip_style($info['material5'])}
                            </div>
                        </dd>
                    </dl>
                    {/if}
                </section>
                <div class="list_show_con">
                </div>
            </div>
            {/if}
            {st:detailcontent action="get_content" typeid="8" productinfo="$info" onlyrealfield="1"}
            {loop $data $row}
            <div class="line_tablist_box">
                <div class="bt_tit"><span>{$row['chinesename']}</span></div>
                <div class="cont_nr">
                    {$row['content']}
                </div>
            </div><!--酒店详细-->
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


<script>
    $('.pl').click(function(){
        var url = SITEURL+"pub/comment/id/{$info['id']}/typeid/{$typeid}";
        window.location.href = url;
    })
    //预订按钮

    $('.order').click(function(){
        var productid = $(this).attr('data-id');
        url = SITEURL+'visa/book/id/'+productid;
        window.location.href = url;
    })
</script>
</body>
</html>
