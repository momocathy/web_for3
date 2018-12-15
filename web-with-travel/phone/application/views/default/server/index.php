<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>{$info['servername']}-{$webname}</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    {Common::css('amazeui.css,style.css')}
    {Common::js('jquery.min.js,amazeui.js')}
</head>

<body>

{request "pub/header/typeid/0"}
  
  <section>
    
  	<div class="mid_content">
     
	 <div class="aboutus_show">
      	<h1>{$info['servername']}</h1>
        <div class="ab_show_con">
        	{Product::strip_style($info['content'])}
        </div>
      </div><!--aboutus-->

    </div>
    
  </section>
  {request "pub/footer"}

</body>
</html>
