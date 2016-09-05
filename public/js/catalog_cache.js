$(document).ready(function () {


    function send() {

        var cat = $(".cat_id:not(.end)").first();

        cat.addClass("end");
        cat.addClass("wait");

        $.get("/catalog/cache_all/" + cat.attr("num"), function (data) {
            console.log("script_send");
            console.log(data);

        });




    }

    $(".button").click(function () {
        
        $(".check:checked").each(function(i,c) {
            
            $(c).parent().addClass("cat_id");
            
        });
        send();
    });
    
    $(".all").click(function(event) {
        event.preventDefault();
        $(".check").prop("checked", true);
    });



    $(document).bind("ajaxError", function (error) {
        console.log("error");
        console.log(error);
    }).bind("ajaxSend", function () {
        console.log("send");

    }).bind("ajaxSuccess", function () {

        if (!$("#parts_line").hasClass("end")) {
            if ($(".cat_id:not(.end)").size() > 0) {
                console.log("next_send");
                $(".cat_id.end").last().removeClass("wait");
                $(".cat_id.end").last().addClass("complete");
                
                send();
            } else {
                $(".cat_id.end").last().removeClass("wait");
                $(".cat_id.end").last().addClass("complete");
            }
        } else {
            
        }


    });



});