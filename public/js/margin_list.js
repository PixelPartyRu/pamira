$(document).ready(function() {
    
    $("body").on("click",".set_default",function(event) {
        event.preventDefault();
        var that = $(this);
        
        
        $.get( "/dealer/set_default_margin/"+$(this).data("id"),function(data){
            that.closest('.table').find(".default_margin").hide();
            that.closest('.table').find(".default_margin").next().show();
            that.prev().show();
            that.hide();
        });
        
    });
    
    $("body").on("click", ".delete", function (event) {
        event.preventDefault();
        var that = $(this);
        if(confirm('Удалить наценку "' + that.parents(".table-row").find('.table-cell').first().html() + '"?'))
        {
            $.get("/dealer/delete_margin/" + $(this).data("id"), function (data) {
                that.parents(".table-row").remove();
            });
        }
    });
    
    
    
});