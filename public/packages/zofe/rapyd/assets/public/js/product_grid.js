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

    function getCookie(name) {
        var matches = document.cookie.match(new RegExp(
            "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
        ));

        return matches ? decodeURIComponent(matches[1]) : undefined;
    }

    function updateYmlExports(data) {
        $.ajax({
            url: '/panel/Product/update_yml_export',
            type: "post",
            data: data,
            headers: {
                'X-XSRF-TOKEN': getCookie("XSRF-TOKEN")
            }
        })
            .done(function() {
                console.log("done");
            })
            .fail(function() {
                alert("Что-то пошло не так")
            })
    }

    $('[data-entity="Product"] input[name="export_to_yml"]').on('change.heromantor', function(e) {
        var $checkbox = $(this);
        var id = $checkbox.data('product-id');
        var data = { };

        data['values[' + id + ']'] = $checkbox.is(':checked') ? 1 : 0;

        updateYmlExports(data);
    });

    $('[data-entity="Product"] input[name="export_to_yml_all"]').change(function(e) {
        var $checkbox = $(this);
        var value = $checkbox.is(':checked') ? 1 : 0;
        var data = { };

        if(!confirm("Вы действительно хотите установить все значения?")) {
            $(this).prop('checked', !$(this).prop('checked'));
            return;
        }

        var $all = $('[data-entity="Product"] input[name="export_to_yml"]')
            .prop('checked', !!value);

        $all.each(function() {
            var id = $(this).data('product-id');

            data['values[' + id + ']'] = $(this).is(':checked') ? 1 : 0;
        });

        updateYmlExports(data);
    });
   
});