$(document).ready(function() {
    
    
    $(".compare_order_link").click(function (event) {
        event.preventDefault();
        var oid = $(this).attr("oid");
        if($(".compare_order .cart_container .line_order").size() == 0){
            $(".compare_order").show();
        }
        if ($(".compare_order .cart_container .line_order").size() < 3 && $("[oid_line='"+oid+"']").size() === 0 ) {
            url = "/compare_dealer/add_to_compare/" + oid + "/order";
            $.ajax({
                url: url,
                method: 'get',
                complete: function (data) {
                    var html = '<div class="line_order" oid_line="' + oid + '"><a href="/dealer/completed_order/' + oid + '">Заказ №' + oid + '</a></div>';
                    $(".compare_order .cart_container .after").last().after(html);
                    }
                });
        }

    });
    
    $('.remove_order').on('click' , function (e) {
        e.preventDefault();
        var _self = $(this);
        var link = _self.attr('href');
        var num  = _self.parents('.orderrow').find('.id').first().html();
        if(confirm('Удалить заказ №' + num + '?'))
        {
            $.get(link, function (data) {
                _self.parents('.orderrow').remove();
            });
        }
    });
    
    function order_table_width() {
    if( $(".order_compare_table").size() == 1 ){
        
        if( $(".order_compare_table .header td").size() == 2 ){
            $(".order_compare_table").attr("style","width:66%");
        }
        if ($(".order_compare_table .header td").size() == 1) {
            $(".order_compare_table").attr("style", "width:33%");
        }
        $(".body.type_caption").attr("colspan",$(".order_compare_table .header td").size());
    }
    }
    order_table_width();
    
    //Удаление позиции из сранения
    $(".cross_del").click(function() {
        var oid = $(this).attr("order_id");
        $.get("/compare_dealer/remove_position_compare/" + oid + "/order", function (data) {
            
            $("[order_id='"+oid+"']").html("<span class='empty'></span>");
            $("[oid_line='"+oid+"']").remove();
            order_table_width();
            

        });
        //remove_position_compare
    });
    
    
});