$(document).ready(function () {
    
    function hasValue(select,value) {
        var hasVal = false;
        select.find("option").each(function(i,option){

            if(value === $(option).html() ){
               hasVal = true;
               return hasVal;
            }
        });
        return hasVal;
        
    }

    $(".add_new").click(function (event) { 
                    console.clear();
        event.preventDefault();
        var that = $(this);
        var text_field = $(this).parents(".product_haracteristic_field").find('.new_value');
        
        
            var select = that.parents(".product_haracteristic_field").find('select')
        var value = text_field.val();

        
        if (value !== "") {
            if(hasValue(select,value)) {
                alert("Такое значение уже существует");
                return;
            }
            var data_name = text_field.data('name').replace("_ph", "");
            var dataPH = {
                type:data_name,
                value:value,
                catalog_id: $("#catalog_id option:selected").val()
            };

            console.log(dataPH);
          //  console.log(window.location);


            $.get("/panel/Product/add_ph",dataPH, function (data) {
                
                
                select.prepend("<option value='" + data.id + "'>" + value + "</option>");
                alert("Добавлено");

            });
            
        }
        else {
            alert("Введите значение");
        }




    });
    


});