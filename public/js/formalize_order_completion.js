$(document).ready(function () {

    $(".product_info_row:not(.caption)").each(function (i, row) {

        $(row).find(".table-cell").each(function (j, cell) {
            var width = $(".product_info_row.caption .table-cell:eq(" + j + ")").width();
            $(cell).width(width);

        });

    });

    $('.dragging .table').sortable({
        placeholder: 'sortable-placeholder',
        items: ".product_info_row_data",
        update: function (event, ui) {
            var data = {'ids': [], '_token': $('input[name="_token"]').val()};

            $('.dragging .product_info_row_data').each(function(i, el) {
                $(el).find('.id .index').html(i+1);
                data['ids'][i] = $(el).attr('data-id');
            });

            $.ajax({
                data: data,
                type: 'POST',
                url: '/dealer/order_products_order'
            });
        }
    });
    var action = "/dealer/option_completed_order/";
    
    $("#edit").click(function() {
        $(".complited_order_links").show();
        $("#edit").hide();
    });
    
    $(".complited_order_links .button_submit").click(function(event) {
        event.preventDefault();  
        //alert(1);

        var href = $(this).attr("href");
        $("#option_completed_order").attr("action", href);
        $("#option_completed_order").submit();
//$(".hidden_id").serializeArray()
        
    });
    
    $(".complited_order_links .caterer_mail_send_button").click(function(event) {
        event.preventDefault();  

        var href = $(this).attr("href");
        $("#message_window_box").show();
        $.get(href, $(".hidden_id").serializeArray(), function () {
            
            $("#message_window_box .button.ok").click(function() {
                window.location.href = "/";
            });
            $("#message_window_box .before_add").hide();
            $("#message_window_box .after_add").show();
            $("#message_window_box .message").html("Заказ менеджеру успешно отправлен");
        });
        
    });
    
    $("#caterer_mail_send").click(function(event) {
        console.log( $(".hidden_id").serializeArray());

        event.preventDefault();
        $("#message_window_box").show();
        $.get("/dealer/option_completed_order/caterer_mail_send", $(".hidden_id").serializeArray(), function () {
            
            $("#message_window_box .button.ok").click(function() {
                window.location.href = "/";
            });
            $("#message_window_box .before_add").hide();
            $("#message_window_box .after_add").show();
            $("#message_window_box .message").html("Заказ менеджеру успешно отправлен");
        });

        
    });
    $("#caterer_save_order").click(function(event) {
        //console.log( $(".hidden_id").serializeArray());
        //console.log(1);
        event.preventDefault();
        $("#message_window_box").show();
        $.get("/dealer/save_order", $(".hidden_id").serializeArray(), function () {
            
            $("#message_window_box .button.ok").click(function() {
                window.location.href = "/dealer/order_history";
            });
            $("#message_window_box .before_add").hide();
            $("#message_window_box .after_add").show();
            $("#message_window_box .message").html("Заказ успешно сохранен ");
        });

        
    });
    
    $(".button_submit").click(function (event) {
        //event.preventDefault();
        //console.log(event);

        $("#option_completed_order").attr("action", action + $(this).attr("id"));


    });
    
    $("#cart_restore").click(function (event) {
        event.preventDefault();
        var href = $(this).attr("href");
        $.get(href, function (data) {
            console.log(data);
            alert("Заказ отменен");
            window.location.href = "/dealer/cart";

        });

    });

});

