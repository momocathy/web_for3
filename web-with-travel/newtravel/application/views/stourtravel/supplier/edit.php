<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>思途CMS4.1</title>
    {template 'stourtravel/public/public_js'}
    {php echo Common::getCss('style.css,base.css,base2.css'); }
    {php echo Common::getScript("jquery.validate.js"); }
    {php echo Common::getScript("uploadify/jquery.uploadify.min.js,choose.js,product_add.js,imageup.js"); }
    {php echo Common::getCss('uploadify.css','js/uploadify/'); }
   <style>
        .error{
            color:red;
            padding-left:5px;
        }

    </style>

</head>
<body style="background-color: #fff">
<table class="content-tab">
    <tr>
        <td width="119px" class="content-lt-td" valign="top">
            {template 'stourtravel/public/leftnav'}
            <!--右侧内容区-->
        </td>
        <td valign="top" class="content-rt-td">



            <form id="frm" name="frm">
            <div id="product_grid_panel" class="manage-nr">
                <div class="w-set-con">
                    <div class="w-set-tit bom-arrow" id="nav">
                        <span class="on" id="basic"><s></s>基础信息</span>
                        <span data-id="jieshao"><s></s>介绍</span>
                        <span data-id="tupian"><s></s>图片</span>
                        <span data-id="extend"><s></s>扩展</span>
                        <a href="javascript:;" class="refresh-btn" onclick="window.location.reload()">刷新</a>
                    </div>
                </div>
                        <div class="product-add-div" >
                            <div class="add-class">
                            <dl>
                                <dt>所属分类：</dt>
                                <dd>
                                    <select name="kindid" id="">
                                        <option value="0">默认</option>
                                        {loop $kind $v}
                                        <option value="{$v['id']}" {if $v['id']==$info['kindid'] }selected="selected"{/if}>{$v['kindname']}</option>
                                        {/loop}
                                    </select>
                                </dd>
                            </dl>
                            <dl>
                                <dt>供应商名称：</dt>
                                <dd>

                                    <input type="text" class="set-text-xh mt-4 w400" name="suppliername" id="suppliername" value="{$info['suppliername']}" >
                                </dd>
                            </dl>

                            <dl class="list_dl">
                                <dt class="wid_90">认证状态：</dt>
                                <dd>
                                    <select name="verifystatus">
                                        <option value="0" {if $info['verifystatus']==0}selected="selected"{/if}>未认证</option>
                                        <option value="1" {if $info['verifystatus']==1}selected="selected"{/if}>已认证</option>
                                    </select>
                                </dd>
                            </dl>
                            <dl class="list_dl">
                                <dt class="wid_90">联系人：</dt>
                                <dd><input type="text" class="set-text-xh text_200 mt-4"  name="linkman" id="linkman" value="{$info['linkman']}" ></dd>
                            </dl>
                            <dl class="list_dl">
                                <dt class="wid_90">座机：</dt>
                                <dd><input type="text" class="set-text-xh text_200 mt-4" name="telephone" id="telephone" value="{$info['telephone']}" ></dd>
                            </dl>

                            <dl class="list_dl">
                                <dt class="wid_90">手机：</dt>
                                <dd><input type="text" class="set-text-xh text_200 mt-4" name="mobile" id="mobile" value="{$info['mobile']}" ></dd>
                            </dl>
                            <dl class="list_dl">
                                <dt class="wid_90">传真：</dt>
                                <dd><input type="text" class="set-text-xh text_200 mt-4" name="fax" id="fax" value="{$info['fax']}" ></dd>
                            </dl>

                            <dl class="list_dl">
                                <dt class="wid_90">QQ：</dt>
                                <dd><input type="text" class="set-text-xh text_200 mt-4" name="qq" id="qq" value="{$info['qq']}" ></dd>
                            </dl>
                            <dl class="list_dl">
                                <dt class="wid_90">地址：</dt>
                                <dd><input type="text" class="set-text-xh w400 mt-4" name="address" id="address" value="{$info['address']}" ></dd>
                            </dl>
                            <dl class="list_dl">
                                <dt class="wid_90">坐标：</dt>
                                <dd>
                                    <span class="fl">经度(Lng):</span>
                                    <input type="text" name="lng" id="lng"  class="set-text-xh text_150 mt-2 ml-10 mr-30 w300" value="{$info['lng']}" />
                                    <span class="fl">纬度(Lat):</span>
                                    <input type="text" name="lat" id="lat" class="set-text-xh text_150 mt-2 ml-10 mr-30 w300" value="{$info['lat']}"  />
                                    <a href="javascript:;" class="choose-btn mt-4" onclick="Product.Coordinates(700,500)"  title="选择">选择</a>
                                </dd>

                            </dl>
                            <dl class="list_dl">
                                <dt class="wid_90">目的地：</dt>
                                <dd>
                                    <a href="javascript:;" class="choose-btn mt-4" onclick="Product.getDest(this,'.dest-sel',2)"  title="选择">选择</a>
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
                        <div class="product-add-div" data-id="tupian">
                                <div class="up-pic">
                                    <dl>
                                        <dt>供应商图片：</dt>
                                        <dd>
                                            <div class="up-file-div">
                                                <div id="pic_btn" class="btn-file mt-4">上传图片</div>
                                            </div>
                                            <div class="up-list-div">
                                                <input type="hidden" class="headimgindex" name="imgheadindex" value=""/>
                                                <input type="hidden" name="litpic" id="litpic" value="{$info['litpic']}"/>
                                                <ul class="pic-sel">

                                                </ul>
                                            </div>
                                        </dd>
                                    </dl>
                                </div>
                        </div>
                        <div class="product-add-div" data-id="jieshao">
                            {php Common::getEditor('content',$info['content'],700,400);}
                        </div>
                        <div class="product-add-div" data-id="extend">
                            <div class="add-class">
                                <dl class="list_dl">
                                    <dt class="wid_90">账户：</dt>
                                    <dd>
                                        {if empty($info['account'])}
                                        <input type="text" class="set-text-xh mt-4 w400" name="account" id="account" value="{$info['account']}" >
                                        {else}
                                        {$info['account']}
                                        {/if}
                                    </dd>
                                </dl>
                                <dl class="list_dl">
                                    <dt class="wid_90">密码：</dt>
                                    <dd>
                                        <input type="password" class="set-text-xh mt-4 w400" name="password" id="password" value="" >
                                    </dd>
                                </dl>
                            </div>

                        </div>
						    <div class="opn-btn">

                                <a class="normal-btn" id="btn_save" href="javascript:;">保存</a>
                            </div>

                        <dl class="list_dl">
                            <dt class="wid_90">&nbsp;</dt>
                            <dd>
                              
                                <input type="hidden" id="id" name="id" value="{$info['id']}">
                                <input type="hidden" name="action" value="{$action}">
                                <input type="hidden" name="litpic" id="litpic" value="{$info['litpic']}">
                            </dd>
                        </dl>
            </div>
            </form>
        </td>
    </tr>
</table>


<script language="JavaScript">



    var action='{$action}';

    {if $action=='edit'}
        var piclist = ST.Modify.getUploadFile({$info['piclist_arr']});
        $(".pic-sel").html(piclist);
        var litpic = $("#litpic").val();
        $(".img-li").find('img').each(function(i,item){

            if($(item).attr('src')==litpic){
                var obj = $(item).parent().find('.btn-ste')[0];
                Imageup.setHead(obj,i+1);
            }
        })
        window.image_index= $(".pic-sel").find('li').length;//已添加的图片数量
    {/if}

    //表单验证
    $("#frm").validate({

        focusInvalid:false,
        rules: {
            suppliername:
            {
                required: true

            },
            account:{
               required:true
            },
            linkman: {
                required: true

            }
        },
        messages: {

            suppliername:{
                required:"请输入供应商名称"

            },
            account:{
                required:"账户不能为空"
            },
            linkman: {
                required:"请输入联系人"

            }

        },
        errUserFunc:function(element){


        },
        submitHandler:function(form){

            Ext.Ajax.request({
                url   :  SITEURL+"supplier/ajax_save",
                method  :  "POST",
                isUpload :  true,
                form  : "frm",
                success  :  function(response, opts)
                {

                    var data = $.parseJSON(response.responseText);
                    if(data.status)
                    {

                        $("#id").val(data.productid);
                        ST.Util.showMsg('保存成功!','4',2000);


                    }
                    else
                    {
                        ST.Util.showMsg(data.msg,'5',2000);
                    }


                }});
            return false;//阻止常规提交


       }




    });

    $(function(){

        $("#nav").find('span').click(function(){

            Product.changeTab(this,'.product-add-div');//导航切换

        })
        $("#nav").find('span').first().trigger('click');


        //保存
        $("#btn_save").click(function(){


            $("#frm").submit();

            return false;

        })

        //上传图片
        $('#pic_btn').click(function(){
            ST.Util.showBox('上传图片', SITEURL + 'image/insert_view', 430,340, null, null, document, {loadWindow: window, loadCallback: Insert});
            function Insert(result,bool){
                var len=result.data.length;
                for(var i=0;i<len;i++){
                    var temp =result.data[i].split('$$');
                    Imageup.genePic(temp[0],".up-list-div ul",".cover-div");
                }
            }
        })

    })

</script>

</body>
</html>