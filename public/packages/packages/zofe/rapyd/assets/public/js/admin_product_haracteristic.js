$(document).ready(function () {

    $(".add_new").click(function (event) {
        event.preventDefault();
        var that = $(this);
        var text_field = $(this).parents(".product_haracteristic_field").find('.new_value');

        var value = text_field.val();
        if (value !== "") {
            var table = text_field.data('name').replace("_id", "");
            console.log(value);
            console.log(table);
            $.get("/addProductHaracteristic/" + table + "/" + value, function (data) {
                var select = that.parents(".product_haracteristic_field").find('select');
                select.prepend("<option value='" + data.id + "'>" + value + "</option>");

            });
        }




    });

});