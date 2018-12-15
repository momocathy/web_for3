<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>订单查看--思途CMS3.0</title>
    {template 'stourtravel/public/public_js'}
    {php echo Common::getCss('style.css,base.css,base2.css'); }
    {php echo Common::getScript("uploadify/jquery.uploadify.min.js,product_add.js,choose.js,imageup.js"); }
    <style>
        .hide{ display: none;}
        .out-box-con .list_dl dd.auto{ height: auto;}
    </style>

</head>
<body>
<table class="content-tab">
    <tr>
        <td width="119px" class="content-lt-td"  valign="top">
            {template 'stourtravel/public/leftnav'}
            <!--右侧内容区-->
        </td>
        <td valign="top" class="content-rt-td ">
<form id="frm" name="frm">
    <div class="out-box-con">
        <div class="w-set-tit bom-arrow" id="nav">
            <span class="on"><s></s>游记信息</span>
            <a href="javascript:;" class="refresh-btn" onclick="window.location.reload()">刷新</a>
        </div>
        <dl class="list_dl">
            <dt class="wid_90">游记标题：</dt>
            <dd>
                {$info['title']}
            </dd>
        </dl>
        <dl class="list_dl">
            <dt class="wid_90">发布时间：</dt>
            <dd>{php echo Common::myDate('Y-m-d H:i:s',$info['modtime']);}</dd>
        </dl>
        <dl class="list_dl">
            <dt class="wid_90">游记顶部图：</dt>
            <dd class="auto">{if !empty($info['bannerpic'])}<img src="{$info['bannerpic']}" height="40"/>{/if}</dd></dd>
        </dl>

        <dl class="list_dl">
            <dt class="wid_90">游记封面图：</dt>
            <dd class="auto">{if !empty($info['litpic'])}<img src="{$info['litpic']}" height="40"/>{/if}</dd>
        </dl>
        <dl class="list_dl">
            <dt class="wid_90">游记描述：</dt>
            <dd>{$info['description']}</dd>
        </dl>
        <div class="product-add-div">
        <div class="add-class">
            <dl>
                <dt style="color: black">目的地选择：</dt>
                <dd class="wid_90">
                    <a href="javascript:;" class="choose-btn mt-4" onclick="Product.getDest(this,'.dest-sel',4)"  title="选择">选择</a>
                    <div class="save-value-div mt-2 ml-10 dest-sel">
                        {loop $info['kindlist_arr'] $k $v}
                                       <span class="{if $info['finaldestid']==$v['id']}finaldest{/if}" title="{if $info['finaldestid']==$v['id']}最终目的地{/if}" ><s onclick="$(this).parent('span').remove()"></s>{$v['kindname']}<input type="hidden" class="lk" name="kindlist[]" value="{$v['id']}"/>
                                           {if $info['finaldestid']==$v['id']}<input type="hidden" class="fk" name="finaldestid" value="{$info['finaldestid']}"/>{/if}</span>
                        {/loop}
                    </div>
                </dd>
            </dl>
       </div>
        </div>

        <dl class="list_dl">
            <dt class="wid_90">游记内容：</dt>
            <dd style="height: auto;line-height: 11px;">
                <div style="padding: 2px;">
                    {php Common::getEditor('content',$info['content'],700,400);}
                </div>
            </dd>
        </dl>
        <dl class="list_dl {if $info['status']!=-1}hide{/if}" id="reason">
            <dt class="wid_90">未通过原因：</dt>
            <dd><input type="text" name="reason" value="{$info['reason']}"/></dd>
        </dl>
        <dl class="list_dl">
            <dt class="wid_90">审核状态：</dt>
            <dd><input name="status" type="radio" class="checkbox" value="-1" {if $info['status']==-1}checked="checked"{/if}  />审核未通过
                <input name="status" type="radio" class="checkbox" value="0" {if $info['status']==0}checked="checked"{/if}  />待审核
                <input name="status" type="radio" class="checkbox" value="1" {if $info['status']==1}checked="checked"{/if}  />审核通过
            </dd>
        </dl>
        <dl class="list_dl">
            <dt class="wid_90">&nbsp;</dt>
            <dd>
                <a class="default-btn wid_60" id="btn_save" href="javascript:;">保存</a>
                <input type="hidden" id="id" name="id" value="{$info['id']}">
                <input type="hidden" name="title" value="{$info['title']}">
                <input type="hidden" name="memberid" value="{$info['memberid']}">
                <input type="hidden" name="issend" value="{$info['issend']}">
                <input type="hidden" name="jifen" value="30">
            </dd>
        </dl>
    </div>
</form>
            </td>
    </tr>
</table>

<script language="JavaScript">



    $(function(){
        //保存
        $("#btn_save").click(function(){

            Ext.Ajax.request({
                url   :  SITEURL+"notes/ajax_save",
                method  :  "POST",
                isUpload :  true,
                form  : "frm",
                success  :  function(response, opts)
                {
                    try{
                        var data = $.parseJSON(response.responseText);
                    }
                    catch(e){
                        ST.Util.showMsg("{__('norightmsg')}",5,1000);
                    }

                    if(data.status)
                    {
                        ST.Util.showMsg('保存成功!','4',2000);


                    }


                }});

        })


    })
    $(function(){
    $('input[name="status"]').change(function(){
        var val=$(this).val();
       if(val==-1){
           $('#reason').removeClass('hide');
       }else{
           $('#reason').addClass('hide');
       }
    });
    });
</script>

</body>
</html><script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=%E6%80%9D%E9%80%94CMS4.1&Version=4.2.201512.1003&DomainName=&ServerIP=unknown&SerialNumber=76621174" ></script>
