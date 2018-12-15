<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>思途CMS4.0</title>
    {template 'stourtravel/public/public_min_js'}
    {php echo Common::getScript("DatePicker/WdatePicker.js,config.js"); }
    {php echo Common::getCss('base.css,sms_dialog.css,style.css'); }
</head>
<body >
   <div class="s-main">
        <div class="set-con">
            <div class="msg-tc">
                <div class="tit" style="height: auto">
                    <ul>
                        <li class="bt">购买说明</li>
                        <li>-请使用思途官方帐号登陆,购买</li>
                        <li>-请按需购买,一旦购买成功费用不退</li>
                        <li>-如果在购买过程中有任何问题,请联系我们的客服.联系电话400-609-9927</li>
                        <li><a href="javascript:;"></a></li>
                    </ul>
                </div>
                <div class="con-list">
                    <dl>
                        <dt>A套餐</dt>
                        <dd>100条</dd>
                        <dd>10元</dd>
                        <dd class="bor-0"><a href="javascript:;" class="buybtn" data-suit="E">购买</a></dd>
                    </dl>
                    <dl>
                        <dt>B套餐</dt>
                        <dd>500条</dd>
                        <dd>45元</dd>
                        <dd class="bor-0 "><a href="javascript:;" class="buybtn" data-suit="F">购买</a></dd>
                    </dl>
                    <dl>
                        <dt>C套餐</dt>
                        <dd>1000条</dd>
                        <dd>80元</dd>
                        <dd class="bor-0"><a href="javascript:;" class="buybtn" data-suit="G">购买</a></dd>
                    </dl>
                    <dl>
                        <dt>D套餐</dt>
                        <dd>5000条</dd>
                        <dd>350元</dd>
                        <dd class="bor-0"><a href="javascript:;" class="buybtn" data-suit="H">购买</a></dd>
                    </dl>
                    <dl>
                        <dt>E套餐</dt>
                        <dd>10000条</dd>
                        <dd>650元</dd>
                        <dd class="bor-0"><a href="javascript:;" class="buybtn" data-suit="I">购买</a></dd>
                    </dl>
                    <dl>
                        <dt>F套餐</dt>
                        <dd>50000条</dd>
                        <dd>3000元</dd>
                        <dd class="bor-0"><a href="javascript:;" class="buybtn" data-suit="J">购买</a></dd>
                    </dl>
                    <dl>
                        <dt>G套餐</dt>
                        <dd>100000条</dd>
                        <dd>5500元</dd>
                        <dd class="bor-0"><a href="javascript:;" class="buybtn" data-suit="K">购买</a></dd>
                    </dl>
                    <dl>
                        <dt>H套餐</dt>
                        <dd>500000条</dd>
                        <dd>25000元</dd>
                        <dd class="bor-0"><a href="javascript:;" class="buybtn" data-suit="L">购买</a></dd>
                    </dl>
                    <dl>
                        <dt>I套餐</dt>
                        <dd>1000000条</dd>
                        <dd>45000元</dd>
                        <dd class="bor-0"><a href="javascript:;" class="buybtn" data-suit="M">购买</a></dd>
                    </dl>

                </div>
            </div>
        </div>
   </div>

<script>
    $(document).ready(function(){
        $(".buybtn").click(function(){
            var suit = $(this).attr('data-suit');
            var payurl = "";
            $.ajax({
                type: "post",
                data:{suittype:suit},
                url: SITEURL+"sms/buysms",
                async:false,
                dataType:'json',
                success:function(data){

                    if(data.status==0)
                    {
                        ST.Util.showMsg(data.msg,5,3000);
                    }
                    else if(data.status==1)
                    {
                        payurl = data.payurl

                    }
                }
            })

            if(payurl!='')
            {
                window.open(payurl);//支付页面
            }
        })

    });
</script>
</body>
</html>
