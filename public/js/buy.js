$(document).ready(function () {

  
    $('body').on('click', ".buy_button", function () {


        var id = $(this).data("id");
        $("#message_window_box").find(".before_add").show();

        $("#message_window_box").find(".after_add").hide();
        $("#message_window_box").show();
        //console.log(id);
        $.get("/buy/" + id, function (data) {

            if(data.is_login === 1) {
            forLoginUser(data);
            }
            else {
                customerFormData(id);
            }
        });
    });
    
    

    $('body').on('click', ".popup_container .button.ok", function () {
        $("#message_window_box").hide();
    });

    

    
    
    
    //Пользователь залогинен, кладем товар в корзину
    function forLoginUser(data) {

        $("#message_window_box").find(".before_add").hide();


        if (data.success === 1) {
            $(".cart_container .count").html(data.count);
            $(".cart_container .summ").html(data.summ);
            if ($(".cart.no_vis").size() == 1) {
                $(".cart").removeClass("no_vis");
            }
        }
        else {
            $("#message_window_box").find(".after_add").find(".message").html(data.error_message);
        }
        $("#message_window_box").find(".after_add").show();

    }
    




});