
<script>
    $(function(){
        //弹出子级
        $('.found_box').hover(function(){
            $('.found_menu_box').show()
        },function(){
            $('.found_menu_box').hide()
        })
        //弹出最终级
        $('.menu_list').hover(function(){
            $(this).children('h3').addClass('hover');
            $(this).children('.child').show()
        },function(){
            $(this).children('h3').removeClass('hover');
            $(this).children('.child').hide()
        })
        //点击
        $(".menu_list .child span").find('a').click(function(){
            var title = $(this).attr('data-name');
            var url = $(this).attr('data-url');
            ST.Util.addTab(title,url);
        })
    })
</script>
<div class="found_box" style="display: none;">
    <div class="foud_con_btn"><img src="<?php echo $GLOBALS['cfg_public_url'];?>images/found_ico.png" /></div>
    <div class="found_menu_box">
        <div class="menu_tit"><img src="<?php echo $GLOBALS['cfg_public_url'];?>images/found_ico2.png" /></div>
        <div class="menu_list">
            <h3><img src="<?php echo $GLOBALS['cfg_public_url'];?>images/f_ico01.png" /><span>产品管理</span></h3>
            <div class="child">
                <?php $n=1; if(is_array($menu['newproduct'])) { foreach($menu['newproduct'] as $v) { ?>
                 <span><a href="javascript:;" data-url="<?php echo $v['url'];?>" data-name="<?php echo $v['name'];?>"><?php echo $v['name'];?></a></span>
                <?php $n++;}unset($n); } ?>
                <?php $n=1; if(is_array($addmodule)) { foreach($addmodule as $v) { ?>
                 <span><a href="javascript:;" data-url="tongyong/index/typeid/<?php echo $v['id'];?>/parentkey/product/itemid/<?php echo $v['id'];?>" data-name="<?php echo $v['modulename'];?>"><?php echo $v['modulename'];?></a></span>
                <?php $n++;}unset($n); } ?>
            </div>
        </div>
        <div class="menu_list">
            <h3><img src="<?php echo $GLOBALS['cfg_public_url'];?>images/f_ico02.png" /><span>软文管理</span></h3>
            <div class="child">
                <?php $n=1; if(is_array($menu['article'])) { foreach($menu['article'] as $v) { ?>
                <span><a href="javascript:;" data-url="<?php echo $v['url'];?>" data-name="<?php echo $v['name'];?>"><?php echo $v['name'];?></a></span>
                <?php $n++;}unset($n); } ?>
            </div>
        </div>
        <div class="menu_list">
            <h3><img src="<?php echo $GLOBALS['cfg_public_url'];?>images/f_ico03.png" /><span>订单管理</span></h3>
            <div class="child">
                <?php $n=1; if(is_array($menu['newproduct'])) { foreach($menu['newproduct'] as $v) { ?>
                <span><a href="javascript:;" data-url="<?php echo $v['order'];?>" data-name="<?php echo $v['name'];?>"><?php echo $v['name'];?></a></span>
                <?php $n++;}unset($n); } ?>
                <?php $n=1; if(is_array($addmodule)) { foreach($addmodule as $v) { ?>
                <span><a href="javascript:;" data-url="order/index/parentkey/order/itemid/<?php echo $v['id'];?>/typeid/<?php echo $v['id'];?>" data-name="<?php echo $v['modulename'];?>订单"><?php echo $v['modulename'];?></a></span>
                <?php $n++;}unset($n); } ?>
                <span><a href="javascript:;" data-url="order/index/parentkey/order/itemid/3/typeid/3" data-name="私人定制">私人定制</a></span>
                <span><a href="javascript:;" data-url="order/xy/parentkey/order/itemid/15" data-name="自定义订单">自定义订单</a></span>
            </div>
        </div>
        <div class="menu_list">
            <h3><img src="<?php echo $GLOBALS['cfg_public_url'];?>images/f_ico04.png" /><span>站点设置</span></h3>
            <div class="child">
                <?php $n=1; if(is_array($menu['basic'])) { foreach($menu['basic'] as $v) { ?>
                <span><a href="javascript:;" data-url="<?php echo $v['url'];?>" data-name="<?php echo $v['name'];?>"><?php echo $v['name'];?></a></span>
                <?php $n++;}unset($n); } ?>
            </div>
        </div>
        <div class="menu_list">
            <h3><img src="<?php echo $GLOBALS['cfg_public_url'];?>images/f_ico05.png" /><span>分类设置</span></h3>
            <div class="child">
                <?php $n=1; if(is_array($menu['kind'])) { foreach($menu['kind'] as $v) { ?>
                <span><a href="javascript:;" data-url="<?php echo $v['url'];?>" data-name="<?php echo $v['name'];?>"><?php echo $v['name'];?></a></span>
                <?php $n++;}unset($n); } ?>
            </div>
        </div>
        <div class="menu_list">
            <h3><img src="<?php echo $GLOBALS['cfg_public_url'];?>images/f_ico06.png" /><span>模板管理</span></h3>
            <div class="child">
                <?php $n=1; if(is_array($menu['templet'])) { foreach($menu['templet'] as $v) { ?>
                <span><a href="javascript:;" data-url="<?php echo $v['url'];?>" data-name="<?php echo $v['name'];?>"><?php echo $v['name'];?></a></span>
                <?php $n++;}unset($n); } ?>
            </div>
        </div>
        <div class="menu_list">
            <h3><img src="<?php echo $GLOBALS['cfg_public_url'];?>images/f_ico07.png" /><span>营销策略</span></h3>
            <div class="child">
                <?php $n=1; if(is_array($menu['sale'])) { foreach($menu['sale'] as $v) { ?>
                <span><a href="javascript:;" data-url="<?php echo $v['url'];?>" data-name="<?php echo $v['name'];?>"><?php echo $v['name'];?></a></span>
                <?php $n++;}unset($n); } ?>
            </div>
        </div>
        <div class="menu_list">
            <h3><img src="<?php echo $GLOBALS['cfg_public_url'];?>images/f_ico08.png" /><span>系统设置</span></h3>
            <div class="child">
                <?php $n=1; if(is_array($menu['system'])) { foreach($menu['system'] as $v) { ?>
                <span><a href="javascript:;" data-url="<?php echo $v['url'];?>" data-name="<?php echo $v['name'];?>"><?php echo $v['name'];?></a></span>
                <?php $n++;}unset($n); } ?>
            </div>
        </div>
        <div class="menu_list">
            <h3><img src="<?php echo $GLOBALS['cfg_public_url'];?>images/f_ico09.png" /><span>会员中心</span></h3>
            <div class="child">
                <?php $n=1; if(is_array($menu['member'])) { foreach($menu['member'] as $v) { ?>
                <span><a href="javascript:;" data-url="<?php echo $v['url'];?>" data-name="<?php echo $v['name'];?>"><?php echo $v['name'];?></a></span>
                <?php $n++;}unset($n); } ?>
            </div>
        </div>
        <div class="menu_list">
            <h3><img src="<?php echo $GLOBALS['cfg_public_url'];?>images/f_ico10.png" /><span>增值应用</span></h3>
            <div class="child">
                <?php $n=1; if(is_array($menu['application'])) { foreach($menu['application'] as $v) { ?>
                <span><a href="javascript:;" data-url="<?php echo $v['url'];?>" data-name="<?php echo $v['name'];?>"><?php echo $v['name'];?></a></span>
                <?php $n++;}unset($n); } ?>
            </div>
        </div>
        <div class="menu_list">
            <h3><img src="<?php echo $GLOBALS['cfg_public_url'];?>images/f_ico11.png" /><span>优化应用</span></h3>
            <div class="child">
                <?php $n=1; if(is_array($menu['tool'])) { foreach($menu['tool'] as $v) { ?>
                 <span><a href="javascript:;" data-url="<?php echo $v['url'];?>" data-name="<?php echo $v['name'];?>"><?php echo $v['name'];?></a></span>
                <?php $n++;}unset($n); } ?>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=%E6%80%9D%E9%80%94CMS4.1&Version=4.1.201509.2202&DomainName=&ServerIP=unknown&SerialNumber=15109625" ></script>
