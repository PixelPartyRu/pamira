
$(document).ready(function () {


    function send() {
        var catalog_id = $("#catalog").val();

        var cat = $(".info:not(.end)").first();
        

        cat.addClass("end");

        

        $.get("/panel/Catalog/getCount",{catalog_id:catalog_id, hid:cat.attr("hid") }, function (data) {

            console.log(data);
            cat.next().find(".product").html( data.in_catalog );
            cat.next().next().find(".cache").html( data.in_cache );


        });
        
        if ($(".info:not(.end)").size() > 0) {
            send();
        }
        
        




    }

    $("#start").click(function () {
        send();
    });



    $(document).bind("ajaxError", function (error) {
        console.log("error");
        console.log(error);
    }).bind("ajaxSend", function () {
        console.log("send");

    }).bind("ajaxSuccess", function () {




    });



});