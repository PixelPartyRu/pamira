$(document).ready(function() {
        var main_href = $(".Product.add_entity_button").attr("href");
    if( $(".info-div.Product").size() > 0 ) {
        $("#catalog_id").change(function() {
            var catalog = $("#catalog_id option:selected").val();
            var button = $(".Product.add_entity_button");
            button.attr("href", main_href + "?catalog_id="+catalog);
            
        });
        $("#catalog_id").change();
    }
        

   $(".Product.add_entity_button").click(function(event) {

       
var catalog = $("#catalog_id option:selected").val();
       if(catalog === "") {
           alert("Выбирите каталог");
           $("#catalog_id").css("border","1px solid red");
           event.preventDefault();
           return;
           
       }
       
       
       
   });
   
});