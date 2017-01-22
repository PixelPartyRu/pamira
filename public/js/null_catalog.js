$(document).ready(function () {
    function send() {
        var cat = $(".cat_id:not(.end)").first();
        
        if(!cat.length) {
            alert("Система уже определила каталог для каждого товара.");
            return false;
        }

        cat.addClass("wait");

        $.get("/panel/Product/set_cat?id=" + cat.attr("pid"), function (data) {
            cat.addClass("end");
            console.log("script_send");
            console.log(data);

            cat.find(".catalog_name").html(data); 

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
    }

    $("#set").click(function () {
        send();
    });
    
    $(".button_td .btn-info").click(function() {
        
        var cid = $(this).parents(".cat_id").find("select").find("option:selected").val();
        var pid = $(this).parents(".cat_id").find("select").attr("pid");
        var cat = $(this).parents(".cat_id");

        $.get("/panel/Product/set_new_cat",{cid:cid,pid:pid}, function (data) {
            console.log("script_send");
            console.log(data);

            cat.find(".catalog_name").html(cat.find("select").find("option:selected").html());
        });
    });
});