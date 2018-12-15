<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>广告添加/修改</title>
    {template 'stourtravel/public/public_js'}
    {php echo Common::getCss('style.css,base.css,base2.css'); }
    {php echo Common::getScript("uploadify/jquery.uploadify.min.js,product_add.js,choose.js,imageup.js,template.js");}
    {php echo Common::getCss('uploadify.css','js/uploadify/'); }
    <style>
        .product-add-div .up-list-div ul {
            padding: 10px;
            border: 1px solid #dcdcdc;
        }

        .product-add-div .up-list-div ul li {
            width: auto;
            border: none;
        }

        .product-add-div .up-list-div ul li .img_text {
            margin-left: 10px;
        }

        .product-add-div .up-list-div ul li span {
            display: block;
            width: 600px;
            padding-left: 30px;
            height: 30px;
            margin: 10px 0;
        }

        .product-add-div .up-list-div ul li .set-text-xh {
            float: none;
            width: 500px;
        }
    </style>
</head>

<body>
<table class="content-tab">
    <tr>
        <td width="119px" class="content-lt-td" valign="top">
            {template 'stourtravel/public/leftnav'}
            <!--右侧内容区-->
        </td>
        <td valign="top" class="content-rt-td ">

            <form method="post" name="product_frm" id="product_frm">
                <div class="manage-nr">
                    <div class="w-set-tit bom-arrow" id="nav">
                        <span class="on"><s></s>{$title}</span>
                        <a href="javascript:;" class="refresh-btn" onclick="window.location.reload()">刷新</a>
                    </div>
                    <!--基础信息开始-->
                    <div class="product-add-div">
                        <input type="hidden" name="is_system" value="0">
                        <div class="add-class">
                            {if !$ismobile}
                            <dl>
                                <dt>站点：</dt>
                                <dd>
                                    <select name="webid">
                                        <option value="0"
                                        {if $info['webid']==0}selected="selected"{/if}>主站</option>
                                        {loop $weblist $k}
                                        <option value="{$k['webid']}"
                                        {if $info['webid']==$k['webid']}selected="selected"{/if}
                                        >{$k['webname']}</option>
                                        {/loop}
                                    </select>
                                </dd>
                            </dl>
                            {/if}
                            <dl>
                                <dt>平台：</dt>
                                <dd>
                                    {if $ismobile}
                                    手机 <input type="hidden" name="is_pc" id="ispc" value="0">
                                    {else}
                                     PC <input type="hidden" name="is_pc" id="ispc" value="1">
                                    {/if}
                                </dd>
                            </dl>
                            <dl>
                                <dt>广告开启：</dt>
                                <dd>
                                    <select name="is_show" id="">
                                        <option {if $info['is_show']==1 || $action=='add'}selected="selected"{/if} value="1">开启</option>
                                        <option {if $info['is_show']==0 && $action=='edit'}selected="selected"{/if}value="0">关闭</option>

                                    </select>
                                </dd>
                            </dl>
                            <dl>
                                <dt>广告类型：</dt>
                                <dd>
                                    <input type="radio" name="flag" {if $info['flag']==1||$action=='add'}checked="checked"{/if} value="1"/> 单图 &nbsp; <input type="radio" {if $info['flag']==2}checked="checked"{/if} name="flag" value="2"/> 多图
                                </dd>
                            </dl>
                            <dl>
                                <dt>广告位标示：</dt>
                                <dd id="prefix-num"><span>{$info['position']}</span>
                                    <input type="hidden" name="number" value="{$info['number']}"/>
                                </dd>
                            </dl>
                            <dl>
                                <dt>广告位置：</dt>
                                <dd>
                                    {if empty($info['id'])}
                                    <select name="prefix" id="adposition">
                                        <option>选择广告位</option>
                                    {loop $position $v}
                                            <optgroup label="{$v['kindname']}" style="background: #f4f4f4;"></optgroup>
                                        {loop $v['sub'] $r}
                                        <option {if $info['prefix']==$r['pagename']}selected=selected {/if}value="{$r['pagename']}">&nbsp;&nbsp;{$r['kindname']}</option>
                                        {/loop}
                                    {/loop}
                                    </select>
                                    {else}
                                    {loop $position $v}
                                        {loop $v['sub'] $r}
                                             {if $info['prefix']==$r['pagename']}
                                                {$r['kindname']}
                                             {/if}
                                        {/loop}
                                    {/loop}
                                    {/if}
                                    <span class="whinfo" style="color: red"></span>

                                </dd>
                            </dl>
                            <dl>
                                <dt>目的地选择：</dt>
                                <dd>
                                    <a href="javascript:;" class="choose-btn mt-4"
                                       onclick="Product.getDest(this,'.dest-sel',4)" title="选择">选择</a>

                                    <div class="save-value-div mt-2 ml-10 dest-sel">
                                        {loop $info['kindlist_arr'] $k $v}
                                        <span><s onclick="$(this).parent('span').remove()"></s>{$v['kindname']}<input type="hidden" name="kindlist[]" value="{$v['id']}"></span>
                                        {/loop}
                                    </div>

                                </dd>
                            </dl>
                            <dl>
                                <dt>广告备注：</dt>
                                <dd>
                                    <textarea name="remark" id="" cols="30" rows="10">{$info['remark']}</textarea>

                                </dd>
                            </dl>
                            <dl>
                                <dt>广告图片：</dt>
                                <dd>
                                    <div class="up-file-div" id="updiv">
                                        <div id="pic_btn" class="uploadify"
                                             style="height: 25px; width: 80px; cursor: pointer">
                                            <div id="pic_btn-button" class="uploadify-button "
                                                 style="text-indent: -9999px; height: 25px; line-height: 25px; width: 80px;">
                                                <span class="uploadify-button-text">SELECT FILES</span></div>
                                        </div>
                                    </div>
                                    <div class="up-list-div">

                                        <ul class="pic-sel" id="img-list">
                                            {loop $info['image'] $v}
                                            <li>
                                                <img class="fl" src="{$v[0]}" width="100" height="100">
                                                <input type="hidden" name="adsrc[]" value="{$v[0]}"/>
                                                <span class="fl">标题：<input type="text" name="adname[]" id="linktext" class="set-text-xh img_text" value="{$v[1]}"/></span>
                                                <span class="fl">链接：<input type="text" name="adlink[]" id="linkurl"  class="set-text-xh text_250 img_text fl"value="{$v[2]}"/></span>
                                                <span class="delad" style="cursor: pointer">删除</span>
                                            </li>
                                            {/loop}
                                        </ul>
                                        <script type="text/html" id="img-temp">
                                            {{each images as value i }}
                                            <li>
                                                <img class="fl" src="{{value}}" width="100" height="100">
                                                <input type="hidden" name="adsrc[]" value="{{value}}"/>
                                                <span class="fl">标题：<input type="text" name="adname[]" id="linktext" class="set-text-xh img_text" value="{{}}"/></span>
                                                <span class="fl">链接：<input type="text" name="adlink[]" id="linkurl"  class="set-text-xh text_250 img_text fl"value=""/></span>
                                                <span class="delad" style="cursor: pointer">删除</span>
                                            </li>

                                            {{/each}}
                                        </script>
                                    </div>
                                </dd>
                            </dl>

                        </div>
                    </div>
                    <!--/基础信息结束-->
                    <div class="opn-btn">
                        <input type="hidden" name="id" id="id" value="{$info['id']}"/>
                        <input type="hidden" name="action" id="action" value="{$action}"/>
                        <a class="normal-btn ml5" id="btn_save" href="javascript:;">保存</a>
                    </div>

                </div>
            </form>
        </td>
    </tr>
</table>

<script>

    var action = "{$action}";
    $(document).ready(function () {
        $(".choosetime").click(function () {
            WdatePicker({skin: 'whyGreen', dateFmt: 'yyyy-MM-dd', minDate: '%y-%M-%d'})
        })
        var imgNum=$('input[name="mark"]').val();
        $('input[name="mark"]').change(function(){
            imgNum=$(this).val();
        });
        //上传图片
        $('#pic_btn-button').css('backgroundImage', 'url("' + PUBLICURL + 'images/upload-ico.png' + '")');
        $('#pic_btn').click(function () {
            ST.Util.showBox('上传图片', SITEURL + 'image/insert_view', 430, 340, null, null, document, {loadWindow: window, loadCallback: Insert});
            function Insert(result, bool) {
                if (result.data.length > 0) {
                    $data=new Array();
                    $image=new Array();
                    for(i=0;i<result.data.length;i++){
                        var temp = result.data[i].split('$$');
                        $image.push(temp[0]);
                    }
                    if($image.length>0 && imgNum==1){
                        $data.images=[$image[0]];
                        $('#img-list').html(template('img-temp',$data));
                    }else{
                        $data.images=$image;
                        $('#img-list').append(template('img-temp',$data));
                    }
                }
            }
        });
        //广告位标示
        $('#adposition').change(function(){
            var $prefix=$(this).val();
            var issystem=0;
            var ispc=$('#ispc').val();
           $.post(SITEURL + "advertise5x/ajax_number",{'prefix':$prefix,'is_system':issystem,'is_pc':ispc},function(num){
               $('#prefix-num').find('span').html('c_'+$prefix+'_'+num);
               $('#prefix-num').find('input').val(num);
           },'json')
        });

        //保存
        $("#btn_save").click(function () {
            Ext.Ajax.request({
                url: SITEURL + "advertise5x/ajax_save",
                method: "POST",
                form: "product_frm",
                datatype: "JSON",
                success: function (response, opts) {
                    try {
                        var data = $.parseJSON(response.responseText);
                    }
                    catch (e) {
                        ST.Util.showMsg("{__('norightmsg')}", 5, 1000);
                    }

                    if (data.status) {
                        if (data.productid != null) {
                            $("#productid").val(data.productid);
                        }
                        ST.Util.showMsg('保存成功!', '4', 2000);
                        setTimeout(function () {
                            parent.window.gbl_tabs.remove(parent.window.currentTab)
                        }, 2000);
                    }
                }});
        })

        //删除功能
        $("body").delegate('.delad','click',function(){
                var obj = $(this);
                ST.Util.confirmBox('删除广告','确认删除广告吗?',function(){
                    obj.parent().remove();
                },function(){})
        })
    });


</script>

</body>
</html>
<script type="text/javascript" src="http://update.souxw.com/service/api_V3.ashx?action=releasefeedback&ProductName=%E6%80%9D%E9%80%94CMS4.1&Version=4.1.201511.1203&DomainName=&ServerIP=unknown&SerialNumber=76621174" ></script>
