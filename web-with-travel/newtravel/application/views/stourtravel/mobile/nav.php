<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>思途CMS</title>
    {template 'stourtravel/public/public_js'}
    {php echo Common::getCss('style.css,base.css'); }
    {php echo Common::getScript('config.js,template.js');}
    <style>
    .table-div-b-m table, .table-div-b-m table td.cursor-default{cursor:default}
    </style>
</head>

<body>

<table class="content-tab">
    <tr>
        <td width="119px" class="content-lt-td"  valign="top">

  	<!--左侧导航区-->
        {template 'stourtravel/public/leftnav'}
    <!--右侧内容区-->
         </td>
  <td valign="top" class="content-rt-td">
        <div class="w-set-con">
        	<div class="w-set-tit bom-arrow"><span class="on"><s></s>手机导航</span></div>
          <div class="w-set-nr">
            
            <div class="add_menu-btn">
            	<a href="javascript:;" class="add-btn-class btn_add ml-10" onclick="addNav()">添加</a>
            </div>
            
            <div class="table-div-b-m">
               <form name="navfrm" id="navfrm">
            	<table width="100%" border="0" cellspacing="0" cellpadding="0" id="mainnav">
                    <tr style="border-bottom:#f6f6f6 1px solid">
                      <th scope="col" width="5%" height="40px">排序</th>
                      <th scope="col" width="15%" align="left">导航名称</th>
                      <th scope="col" width="25%" align="left">链接地址</th>
                      <th scope="col" width="10%">
                      	<div>
                      		<span class="fl">ico图标</span>
                        </div>
                      </th>
                      <th scope="col" width="10%">显示</th>
                      <th scope="col" width="10%">删除</th>
                    </tr>
                </table>
              </form>
            </div>
            
            <div class="opn-btn">
            	<a class="normal-btn" href="javascript:;" onclick="saveNav()">保存</a>
                <!--<a class="cancel" href="#">取消</a>-->
                <input type="hidden" id="webid" value="0"/>
            </div>


          </div>
        </div>

    </td>
    </tr>
</table>
    <script type="text/html" id="tpl_nav">
        {{each list as value i}}
        <tr style="border-bottom:#f6f6f6 1px solid">
            <td height="40px" align="center"><input type="text"  name="displayorder[]" class="tb-text text_60 al" value="{{value.displayorder}}" /></td>
            <td><input type="text"  name="shortname[]" class="tb-text pl-5" value="{{value.navname}}" /></td>
            <td><input type="text"  name="url[]" class="tb-text pl-5" style="width:90%" {{value.editcls}} value='{{value.linkurl}}' /></td>
            <td class="cursor-default">
                <a href="javascript:;" data-navid="{{value.id}}" class="setico">
                {{if value.ico!=''}}
                 <img src="{{value.ico}}" width="30" height="30"/>
                {{else}}
                 未设置
                {{/if}}
                </a>
            </td>
            <td align="center" class='st_switch cursor-default'>{{#value.openstatus}}<input type="hidden" name="isopen[]" value='{{value.isopen}}'></td>
            <td align="center" class="cursor-default"><a href="javascript:;" class="row-del-btn" onclick="navDel(this,{{value.id}},'{{value.issystem}}')" title="删除"></a><input type="hidden" name="id[]" value='{{value.id}}'></td>
        </tr>
        {{/each}}
    </script>
  
	<script>

	$(function(){
        getNav();
        $("body").delegate('.setico','click',function(){
            var id = $(this).attr('data-navid');
            set_ico(id);
        })
    })

     function getNav()
     {
         var webid=$("#webid").val();
         $.getJSON(SITEURL+"mobile/ajax_getnav",function(data){

             $("#mainnav tr:not(:eq(0))").remove();//先清除内容
             var html = template('tpl_nav',data);
             $("#mainnav tr:last").after(html);
             $("#mainnav").find('.st_switch').each(function(){
                $(this).find('img').attr({style:'cursor:pointer',onclick:'changeShow(this)'});
             });

         });

     }
     function saveNav()
     {
         var webid=$("#webid").val();

         ST.Util.showMsg('保存中,请稍后...',6,5000);
         Ext.Ajax.request({
             url: SITEURL+'mobile/ajax_savenav',
             params: { webid: webid},
             method: 'POST',
             form : 'navfrm',
             success: function (response, options) {

                var data = $.parseJSON(response.responseText);
                if(data.status)
                {
                    ST.Util.showMsg('保存成功',4);
                }

             }

         });

     }
    //添加自定义导航
     function addNav()
     {
         var webid=$("#webid").val();
         ajaxurl =SITEURL+'config/addnav/webid/'+webid;
         ST.Util.showBox('添加导航',ajaxurl,'','',function(){
            //ST.Util.showMsg('保存成功',4);
            getNav();});

     }
     //隐藏显示
     function changeShow(obj)
     {   var obj=$(obj).parent();
         var url = "{$GLOBALS['cfg_public_url']}";
         var showstatus = obj.find('img').attr('data-show');
         if(showstatus == 1)
         {
             var imgurl = url+'images/close-s.png';
             obj.find('img').attr('src',imgurl);
             obj.find('input').first().val(0);
             obj.find('img').attr('data-show',0)
         }
         else
         {
             var imgurl = url+'images/show-ico.png';
             obj.find('img').attr('src',imgurl);
             obj.find('input').first().val(1);
             obj.find('img').attr('data-show',1)
         }
     }
    //设置ico
    function set_ico(id)
    {




        var boxurl = SITEURL+'mobile/dialog_ico/id/'+id;

        ST.Util.showBox('导航ICO配置',boxurl,'400','',function(){ getNav();});


    }
    //导航删除
     function navDel(obj,id,issystem)
     {
         if(issystem==1){
             ST.Util.showMsg('这是系统栏目,不能删除',1);
             return false;
         }

         ST.Util.confirmBox('删除导航','确定删除这个导航吗?',function(){
             $.getJSON(SITEURL+"mobile/ajax_delnav","id="+id,function(data){

                 if(data.status == true){
                     $(obj).parents('tr').first().remove();
                     ST.Util.showMsg('删除成功',4);
                 }
                 else{
                     ST.Util.showMsg('删除失败',5);
                 }

             });
         })
         return false;
     }



	</script>
</body>
</html>
