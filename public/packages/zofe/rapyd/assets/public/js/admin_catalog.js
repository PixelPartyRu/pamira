$(document).ready(function () {



    $("#first_level_catalog").change(function () {
        // alert(1);

        var parent_id = $(this).find("option:selected").val();

        $.get("/getCatalogList/" + parent_id, function (data) { 

            console.log(data);
            console.log(Object.keys(data).length);
            if (Object.keys(data).length > 0) {
                second_level.find("select").html("");
                third_level.find("select").html("");

                $.each(data, function (i, el) {

                    second_level.find("select").append("<option value='" + i + "'>" + el + "</option>");


                });
                second_level.show();
                second_level.change();

            }
            else {
                second_level.hide();
                third_level.hide();

            }

        });


    });


    $("#second_level_catalog").change(function () {
        // alert(1);

        var parent_id = $(this).find("option:selected").val();

        $.get("/getCatalogList/" + parent_id, function (data) {

            console.log(data);
            console.log(Object.keys(data).length);
            if (Object.keys(data).length > 0) {
                third_level.find("select").html("");

                $.each(data, function (i, el) {

                    third_level.find("select").append("<option value='" + i + "'>" + el + "</option>");


                });
                third_level.show();

            }
            else {
                third_level.hide();

            }

        });


    });


    $(".form-horizontal").submit(function () {

        $("[name='first_level_catalog]'").each(function (i, el) {
            if ($(el).find("option:selected").val() == -1) {
                $(el).remove();
            }

        });


    });


    $("#first_level_catalog").change();






});