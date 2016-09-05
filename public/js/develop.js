//    $(document).ready(function() {
//        var t;
//        $(".cat_id").click(function() {
//            $(this).addClass("selected");
//        });
//        
//        $(".script_elem").click(function() {
//            $(".script_elem").removeClass("selected");
//            $(this).addClass("selected");
//            //alert( $(this).data("script") );
//            
//        });
//        
//        function send() {
//            console.log("send");
//            var script = $(".script_elem.selected").data("script");
//
//            
//            if($(".cat_id.selected:not(.end)").size() == 0) {
//               clearInterval(t);
//               return 0;
//
//                
//            }
//            var cat = $(".cat_id.selected:not(.end)").first();
//            console.log(script);
//            console.log(script+cat.html());
//            console.log(cat.html());
//            cat.addClass("end");
//
//            $.get(script+cat.html(),function(data){
//                console.log("script_send");
//                console.log(data);
//                
//            });
//
//            
//            
//            
//        }
//        
//        $(".button").click(function() {
//            var interval = $(".interval input").val();
//            alert(interval);
//            
//            t = setInterval(send, interval);
//            console.log(t);
//        });
//       // setInterval(send, 20000);
//
//        
//        
//        
//        
//        
//    });


var step = 0;
$(document).ready(function() {
    
    function send() {
        
        $.get("/test/test_products/"+step,function(data){
            console.log(data);
            if(data > 0) {
                step++;
                send();
            }
        
        });
        
    }
    send();
    
    
});