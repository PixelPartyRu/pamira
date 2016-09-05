
$(document).ready(function() {
    var im = new Inputmask("9{1,3},99");
   // $(selector).inputmask();
    //im.mask("[name='margin_for_all']");
    //im.mask(".margin_brand_value");
    $(".set_margin_for_all").click(function() {
        //alert(1);
        
        var val = $("[name='margin_for_all']").val();
        //alert(val);
        $(".margin_brand_value").val(val);
    });
    $("body").on('change keyup',"[name='margin_for_all'], .margin_brand_value", function(){
        var _self = $(this);
        //console.log($(this).val());
        _self.val(parseInt(_self.val().replace(/[^-0-9]/gim,''),10));
        if(_self.val() > 100)
        {
            _self.val(100);
        }
        if(_self.val() < (-100))
        {
            _self.val(-100);
        }
    });
    
    
});