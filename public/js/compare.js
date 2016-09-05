/* 
 * Сравнение товаров
 */

$(document).ready(function() {
    
    $("#compare_add").click(function(event) {
        
        event.preventDefault();
  
        var pid = $(this).attr("pid");
        $.get("/compare_user/add_to_compare/"+pid+"/product",function(data) {
            
            $(".compare_cart .summ").html(data);
            
        });
        
    });
    
});
