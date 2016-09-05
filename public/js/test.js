
$(document).ready(function(){
    
    $(".test_button").click(function () {
        var f = new Filter();
        console.log("getCountAjaxData");
        console.log(f.getCountAjaxData());
        
        $.get("/product_catalog/test_filter", f.getCountAjaxData(), function (data) {
            f.clearDisabledAll();
        });
        
    });

  
});

alert(1);

$(document).ajaxSuccess(function (event, xhr, settings) {

    console.log("-----------");
    console.log("http://pamira" + settings.url);
    console.log("-----------");


});













