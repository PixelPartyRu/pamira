
$(document).ready(function() {
    
    var sec = 0; var min = 0;var timer_i;
function timer(){
    var elem=$("#stopwatch").html();
    var milliSec=0;
     
    timer_i=setInterval(function(){
        sec++;
        if(sec == 60){
            min++;
            sec = 0;
            
        }
        $("#stopwatch").html(min+":"+sec);

    },1000);
}
    var step = 0;
    var interval;
    
    function next_step() {
        
            var cur_elem = $(".line_elem").not(".load").first();
            var num = cur_elem.attr("step");
            cur_elem.addClass("load");

            console.log("/panel/Product/update_by_xml?step=" + num);
            
            $.get("/panel/Product/update_by_xml?step=" + num, function (data) {
                $(".console").html("");
                console.log("finish"+num);
                console.log(data);
                $(".console").append("<div class='console_info'>Время парсинга для шага - "+data.sec+" сек.</div>");
                $.each(data.products,function(i,el) {
                    $(".console").append("<div class='console_line'>"+el+"</div>");
                });
            });



        

    }
    
    if( $("#parts_line").size() > 0 ) {
        var count = parseInt($("#parts_line").attr("count"));
       // alert(count);
        for(var i = 0; i < count+1; i++) {
            $("#parts_line").append("<div class='line_elem' step='"+i+"'></div>");
            
        }
        var width = Math.floor(100/(count+1));
        if(width == 0) width = 0.1;
     
        $(".line_elem").css("width",width + "%");
        
        $(".start").click(function(){
           next_step(); 
           timer();
        });
        
        
        

        
        
        
        
    }
    $(document).bind("ajaxError", function (error) {
        console.log("error");
        console.log(error);
    }).bind("ajaxSend", function () {
        //$("#loading").show();
    }).bind("ajaxSuccess", function () {
        
        if (!$("#parts_line").hasClass("end")) {
            if ($(".line_elem").not(".load").size() > 0) {
                next_step();
            } else {
                $.get("/panel/Product/getParseInfo", function (data) {
                   // alert(1);
                    $("#parts_line").addClass("end");
                    $(".parse_info").append("<p>Обновлено: "+data.update+", Добавлено "+data.new+"</p>");
                    clearInterval(timer_i);
                });
            }
        }


    });
    

    
});


