<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>{$info['title']}预订-{$webname}</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    {Common::css('amazeui.css,style.css,extend.css')}
    {Common::js('jquery.min.js,amazeui.js,template.js,layer/layer.m.js')}
    {Common::css('../js/mobiscroll/css/mobiscroll.frame.css,../js/mobiscroll/css/mobiscroll.frame.android.css')}
    {Common::css('../js/mobiscroll/css/mobiscroll.scroller.css,../js/mobiscroll/css/mobiscroll.scroller.android.css')}
    {Common::js('mobiscroll/js/mobiscroll.core.js,mobiscroll/js/mobiscroll.frame.js,mobiscroll/js/mobiscroll.scroller.js')}
    {Common::js('mobiscroll/js/mobiscroll.util.datetime.js,mobiscroll/js/mobiscroll.datetimebase.js,mobiscroll/js/mobiscroll.datetime.js')}
    {Common::js('mobiscroll/js/mobiscroll.frame.android.js,mobiscroll/js/i18n/mobiscroll.i18n.zh.js')}

</head>

<body>
{request "pub/header/typeid/$typeid/isbookpage/1"}
  <section>
    
  	<div class="mid_content">
     
	   <div class="confirm_order_msg">
      	<dl>
        	<dt><img src="{Common::img($info['litpic'])}" /></dt>
          <dd>
          	<span>{$info['title']}</span>
            <strong>&yen;<b>{$info['price']}</b>起</strong>
          </dd>
        </dl>
      </div><!--产品简介-->
      
      <div class="book_type">
      	<h3 class="book_tit">产品类型</h3>
        <div class="book_con_box">
        	<ul>
            {st:spot action="suit" productid="$info['id']"}
              {loop $data $r}
               <li data-suitid="{$r['id']}" data-price="{$r['ourprice']}" data-storage="{$r['number']}">{$r['title']}（库存：{if $r['number'] > 0}{$r['number']}{else}0{/if}）</li>
              {/loop}
            {/st}
          </ul>
        </div>
      </div><!--产品类型-->

      <form action="{$cmsurl}spot/create" id="orderfrm" method="post">
      <div class="book_type">
      	<h3 class="book_tit">预定日期</h3>
        <div class="people_num">
          <p>
          	<strong>使用日期：</strong>
            <span>
               <input type="text" style="  width: 200px;border: 0;text-align: left" class="startdate" name="usedate" id="usedate" value="{date('Y-m-d')}"/>
            </span>
              <span>&gt;</span>
          </p>

            <p>
                <strong>门票数量：</strong>
              <span>
                   <select id="dingnum" name="dingnum" onchange="count_price()">
                       <option value="1">1</option>
                       <option value="2">2</option>
                       <option value="3">3</option>
                       <option value="4">4</option>
                       <option value="5">5</option>
                       <option value="6">6</option>
                       <option value="7">7</option>
                       <option value="8">8</option>
                       <option value="9">9</option>
                       <option value="10">10</option>
                   </select>
              </span>
            </p>
          <p>
              <strong>价格：</strong>
              <span>
                    <em>&yen;<b class="totalprice"></b></em>
              </span>
          </p>

        </div>
      </div><!--预定人数-->
      
      <div class="book_type">
      	<h3 class="book_tit">联系人<a href="javascript:;" id="chooseman">选择常用联系人&gt;</a></h3>
        <div class="linkman">
        	<dl>
          	<dt>预订联系人</dt>
            <dd><strong>姓名：</strong><input type="text" id="linkman" name="linkman" placeholder="预订联系人姓名" class="" /><span>(必填)</span></dd>
            <dd><strong>联系电话：</strong><input type="text" id="linktel" name="linktel" class="" placeholder="手机号或固定电话" /><span>(必填)</span></dd>
            <dd><strong>身份证号：</strong><input type="text" id="linkidcard" name="linkidcard" class="" placeholder="输入18位身份证号码" /></dd>
            <dd><strong>订单备注：</strong><textarea name="remark"></textarea></dd>
          </dl>
        </div>
      </div><!--预定人数-->
          <!--隐藏域-->
          <input type="hidden" name="suitid" id="suitid" value=""/>
          <input type="hidden" name="productid" id="productid" value="{$info['id']}"/>
          <input type="hidden" name="typeid"  value="{$typeid}"/>
          <input type="hidden" name="price" id="price" value="{$info['price']}">
          <input type="hidden" name="storage" id="storage" value="">



      </form>

    </div>


    <!--常用联系人-->
     <div id="linkman_list" style="display: none">
         <div class="linkman_page">
             <h3 class="tit">选择一个联系人</h3>
             <ul class="linkman_list">
                 {st:member action="linkman" memberid="$userinfo['mid']"}
                  {loop $data $r}
                     <li>
                         <strong>{$r['linkman']}</strong>
                         <span>联系电话{$r['mobile']}</span>
                         <span>身份证号{$r['idcard']}</span>
                         <i class="lk_choose" data-linkman="{$r['linkman']}" data-mobile="{$r['mobile']}" data-idcard="{$r['idcard']}"></i>
                     </li>
                 {/loop}
                {/st}

             </ul>
         </div><!--选择联系人-->
     </div>


    
  </section>
  {request "pub/code"}
  {request "pub/footer"}
  <div class="bom_link_box">
    <div class="bom_fixed">
      <a class="price" href="javascript:;">总额：<span>&yen;<b class="totalprice"></b></span></a>
      <a class="on btn_book" href="javascript:;">立即预定</a>
    </div>
  </div>
  <script>
      $(function(){

          //套餐选择
          $(".book_con_box").find('li').click(function(){
                var suitid = $(this).attr('data-suitid');
                var price = $(this).attr('data-price');
                var storage = $(this).attr('data-storage');
                $("#suitid").val(suitid);
                $("#storage").val(storage);
                $(this).addClass('on').siblings().removeClass('on');
               // get_mindate_book();
               $("#price").val(price);
              count_price();

          })
          $(".book_con_box").find('li').first().trigger('click');
          //开始日期
          //时间选择初始化
              stdate = new Date(),
              $('#usedate').mobiscroll().date({
                  theme: 'android',
                  mode: 'scroller',
                  display: 'modal',
                  lang: 'zh',
                  dateFormat: 'yy-mm-dd',
                  minDate: new Date(stdate.getFullYear(), stdate.getMonth(), stdate.getDate())
              });


          //常用联系人选择事件
          $("body").delegate('.linkman_list li','click',function(){
              $(this).find('i').toggleClass('on');
          });
          //常用联系人选择
          $("#chooseman").click(function () {
              $.getJSON('{$cmsurl}member/login/ajax_is_login', {}, function (data) {
                  if (data.status == 1) {
                      var content = $("#linkman_list").html();
                      layer.open({
                          type: 1,
                          content: content,
                          style: 'width:80%; border:none;',
                          btn:['{__("OK")}'],
                          yes:function(){
                              $(".linkman_list").find('.on').each(function(i,obj){
                                  var lkman = $(obj).attr('data-linkman');
                                  var lkmobile = $(obj).attr('data-mobile');
                                  var idcard = $(obj).attr('data-idcard');
                                  $("#linkman").val(lkman);
                                  $("#linktel").val(lkmobile);
                                  $("#linkidcard").val(idcard);
                              })
                              layer.closeAll();
                          }
                      });
                  }else{
                      layer.open({
                          content: '{__("error_linktel_not_login")}',
                          btn: ['确认', '取消'],
                          shadeClose: false,
                          yes: function () {
                              window.location.href = "{$cmsurl}member/login";
                          }, no: function () {
                          }
                      });
                  }
              });
          });

          //提交订单
          $(".btn_book").click(function(){
              var lkman = $("#linkman").val();
              var lkmobile = $("#linktel").val();
			  var linkidcard = $("#linkidcard").val();
              //联系人信息验证
              if(lkman==''){
                  layer.open({
                      content: '{__("error_linkman_not_empty")}',
                      btn: ['{__("OK")}']
                  });
                  return false;
              }
              //联系人手机验证
              re = /^1\d{10}$/
              if (!re.test(lkmobile)) {
                  layer.open({
                      content: '{__("error_user_phone")}',
                      btn: ['{__("OK")}']
                  });
                  return false;
              }
			  //联系人身份证验证
                  re = /^(\d{18,18}|\d{15,15}|\d{17,17}x)$/
                  if(linkidcard != ''){
                      if (!re.test(linkidcard)) {
                          layer.open({
                              content: '{__("身份证不合法")}',
                              btn: ['{__("OK")}']
                          });
                          return false;
                      }
                  }
              //订单金额验证
              var totalprice = Number($('.totalprice').first().text());
              if(totalprice==0){
                  layer.open({
                      content: '{__("error_no_product")}',
                      btn: ['{__("OK")}']
                  });
                  return false;
              }
              //库存验证
              var storage = $("#storage").val();
              var dingnum = parseInt($('#dingnum').val());
              if(storage!=-1 && (storage<dingnum)){
                  layer.open({
                      content: '{__("error_no_storage")}',
                      btn: ['{__("OK")}']
                  });
                  return false;
              }

              $("#orderfrm").submit();


          })

      })

      //统计价格
      function count_price()
      {
          var dingnum = $("#dingnum").val();
          var price = $("#price").val();
          var totalprice = Number(dingnum) * Number(price);
          $('.totalprice').html(totalprice);
      }

  </script>

</body>
</html>
