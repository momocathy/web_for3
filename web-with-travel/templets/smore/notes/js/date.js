$(function(){
    //时间选择
    var date=new Date();
    var $year=date.getFullYear();
    var $month=date.getMonth()+1;
    var $day=date.getDate();
    makeTimeHtml($year,$month,$day);
    $('#year').change(function(){
        var val=$(this).val();
        if(val==$year){
            makeTimeHtml($year,$month,$day)
        }else{
            var $time=makeDateArray($year,1);
            makeMonthHtml($time[1]);
            makeDayHtml($time[2]);
        }
    });
    $('#month').change(function(){
        var year=$('#year').val();
        var month=$(this).val();
        var day=1;
        if(year==$year && month==$month){
            day=$day;
        }
        var $time=makeDateArray(year,month,day);
        makeDayHtml($time[2]);
    });
    function makeDateArray($year,$month,$day){
        $day=arguments[2]?arguments[2]:1;
        var date=new Date($year,$month,0);
        var days=date.getDate();
        var year=new Array(),month=new Array(),day=new Array();
        for(var i=0;i<5;i++){
            year.push('<option value="'+parseInt($year+i)+'">'+parseInt($year+i)+'年</option>');
        }
        for(var j=$month;j<13;j++){
            month.push('<option value="'+j+'">'+j+'月</option>');
        }
        for(var k=$day;k<=days;k++){
            day.push('<option value="'+k+'">'+k+'日</option>');
        }
        return new Array(year,month,day);
    }
    function makeTimeHtml($year,$month,$day){
        var $time=makeDateArray($year,$month,$day);
        makeYearHtml($time[0]);
        makeMonthHtml($time[1]);
        makeDayHtml($time[2]);
    }
    function makeYearHtml(year){
        $('#year').html(year.join(''));
    }
    function makeMonthHtml(month){
        $('#month').html(month.join(''));
    }
    function makeDayHtml(day){
        $('#day').html(day.join(''));
    }


});
