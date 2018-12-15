//初始化
function C_slider(frame,list,Lframe,Llist,forwardEle,backEle,scrollType,LscrollType,acitonType,autoInterval){
 this.frame = frame;
 this.list = list;
 this.Lframe = Lframe;
 this.Llist = Llist;
 this.forwardEle = forwardEle;
 this.backEle = backEle;
 this.scrollType = scrollType;
 this.LscrollType = LscrollType;
 this.acitonType = acitonType;
 this.autoInterval = autoInterval;
 
 this.slideLength = $("#"+this.Llist+" > li").length;//总的slider数量
 this.currentSlide = 0;
 this.FrameHeight = $("#"+this.frame).height();
 this.FrameWidth = $("#"+this.frame).width();
 this.lFrameHeight = $("#"+this.Lframe).height();
 this.lFrameWidth = $("#"+this.Lframe).width();
 this.lListHeight = $("#"+this.Llist+" >li").eq(0).outerHeight(true);
 this.lListWidth = $("#"+this.Llist+" >li").eq(0).outerWidth(true);
 
 var self = this;
 
 for(var i = 0; i<this.slideLength; i++) {
  $("#"+this.Llist+" > li").eq(i).data("index",i);
  $("#"+this.Llist+" > li").eq(i).bind(this.acitonType,function(){
   self.go($(this).data("index"));
  });
 };
 
 //给"上一个"、"下一个"按钮添加动作
 $("#"+this.forwardEle).bind('click',function(){
  self.forward();
  return false;
 });
 $("#"+this.backEle).bind('click',function(){
  self.back();
  return false;
 });
 
 //定论鼠标划过时，自动轮换的处理
 $("#"+this.frame+",#"+this.Lframe+",#"+this.forwardEle+",#"+this.backEle).bind('mouseover',function(){
  clearTimeout(self.autoExt);
 });
 
 $("#"+this.frame+",#"+this.Lframe+",#"+this.forwardEle+",#"+this.backEle).bind('mouseout',function(){
  clearTimeout(self.autoExt);
  self.autoExt = setTimeout(function(){
   self.extInterval();
  },self.autoInterval);
 }); 
 
 
 //开始自动轮换
 this.autoExt = setTimeout(function(){
  self.extInterval();
 },this.autoInterval);
}
//执行运动
C_slider.prototype.go = function(index){
 this.currentSlide = index;

 var imgobj = $("#"+this.list+" >li").eq(index).find('img').eq(0);
 if(imgobj.attr('src')==""){
	imgobj.attr('src',imgobj.attr('_src'));
 }
 if(index>0 && index<this.slideLength-1){
	 var nextimgobj = $("#"+this.list+" >li").eq(index+1).find('img').eq(0);
	 if(nextimgobj.attr('src')==""){
		nextimgobj.attr('src',nextimgobj.attr('_src'));
	 }
 }
 if (this.scrollType == "left"){
  $("#"+this.list).animate({
   marginLeft: (-index*this.FrameWidth)+"px"
  }, {duration:600,queue:false});   
 } else if (this.scrollType == "top"){
  $("#"+this.list).animate({
   marginTop: (-index*this.FrameHeight)+"px"
  }, {duration:600,queue:false});   
 }
 
 $("#"+this.Llist+" > li").removeClass("cur");
 $("#"+this.Llist+" > li").eq(index).addClass("cur");
  
 //对于缩略图的滚动处理
 if(this.LscrollType == "left"){
  if(this.slideLength*this.lListWidth > this.lFrameWidth){
   //var spaceWidth = (this.lFrameWidth - this.lListWidth)/2;
   var spaceWidth = this.lListWidth;
   var hiddenSpace = this.lListWidth*this.currentSlide - spaceWidth;
   //console.log('lFrameWidth:'+this.lFrameWidth);
   //console.log('lListWidth:'+this.lListWidth);
   //console.log('spaceWidth:'+spaceWidth);
   if (hiddenSpace > 0){
    if(hiddenSpace+this.lFrameWidth <= this.slideLength*this.lListWidth){
     $("#"+this.Llist).animate({
      marginLeft: -hiddenSpace+"px"
     }, {duration:600,queue:false}); 
    } else {
     var endHidden = this.slideLength*this.lListWidth - this.lFrameWidth;
     $("#"+this.Llist).animate({
      marginLeft: -endHidden+"px"
     }, {duration:600,queue:false}); 
    }
   } else {
    $("#"+this.Llist).animate({
     marginLeft: "0px"
    }, {duration:600,queue:false}); 
   }
  }
  
 } else if (this.LscrollType == "top"){
  if(this.slideLength*this.lListHeight > this.lFrameHeight){
   var spaceHeight = (this.lFrameHeight - this.lListHeight)/2;
   var hiddenSpace = this.lListHeight*this.currentSlide - spaceHeight;
   
   if (hiddenSpace > 0){
    if(hiddenSpace+this.lFrameHeight <= this.slideLength*this.lListHeight){
     $("#"+this.Llist).animate({
      marginTop: -hiddenSpace+"px"
     }, {duration:600,queue:false}); 
    } else {
     var endHidden = this.slideLength*this.lListHeight - this.lFrameHeight;
     $("#"+this.Llist).animate({
      marginTop: -endHidden+"px"
     }, {duration:600,queue:false}); 
    }
   } else {
    $("#"+this.Llist).animate({
     marginTop: "0px"
    }, {duration:600,queue:false}); 
   }
  }
  
 }
 
}
//前进
C_slider.prototype.forward = function(){
 if(this.currentSlide<this.slideLength-1){
  this.currentSlide += 1;
  this.go(this.currentSlide);
 }else {
  this.currentSlide = 0;
  this.go(0);
 }
}
//后退
C_slider.prototype.back = function(){
 if(this.currentSlide>0){
  this.currentSlide -= 1;
  this.go(this.currentSlide);
 }else {
  this.currentSlide = this.slideLength-1;
  this.go(this.slideLength-1);
 }
}
//自动执行
C_slider.prototype.extInterval = function(){
 if(this.currentSlide<this.slideLength-1){
  this.currentSlide += 1;
  this.go(this.currentSlide);
 }else {
  this.currentSlide = 0;
  this.go(0);
 }
 
 var self = this;
 this.autoExt = setTimeout(function(){
  self.extInterval();
 },this.autoInterval);
}