<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>{$info['title']}预订-{$webname}</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    {Common::css('amazeui.css,style.css,extend.css')}
    {Common::js('jquery.min.js,amazeui.js,template.js,layer/layer.m.js')}

</head>

<body>

{request "pub/header/typeid/$typeid/isbookpage/1"}
  <section>
    
  	<div class="mid_content">
     
	   <div class="confirm_order_msg">
      	<dl>
        	<dt><img src="{$info['litpic']}" /></dt>
          <dd>
          	<span>{$info['title']}</span>
            <strong>&yen;<b id="unit-price">{$info['price']}</b></strong>
          </dd>
        </dl>
      </div><!--产品简介-->


      <form action="{$cmsurl}tuan/create" id="orderfrm" method="post">
      <div class="book_type">
      	<h3 class="book_tit">预定产品</h3>
        <div class="people_num">

            <p>
                <strong>数量：</strong>
              <span>
                   <select id="dingnum" name="dingnum">
                       <option selected value="1">1</option>
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
              <strong>单价：</strong>
              <span>
                    <em>&yen;<b>{$info['price']}</b></em>
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
          <input type="hidden" name="productid" id="productid" value="{$info['id']}"/>
          <input type="hidden" id="storage" value="{$info['totalnum']}"/>
      </form>

    </div>
    <div id="js_calendar" style="display: none">

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
          //计算总金额

          var unitPrice=parseInt($('#unit-price').text());
          count();
          function count(){
              var num=parseInt($('#dingnum').val());
              $('.totalprice').text(num*unitPrice);
          }
          //数量选择
          $('#dingnum').change(function(){
              count();
          });
      })
  </script>

</body>
</html>
