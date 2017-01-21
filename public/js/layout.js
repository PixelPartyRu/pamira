
var Layout = function() {

    //ховер на картинку бренда
    this.brand_img = function () {
        $(".brand_menu .brand a").each(function (i, img) {

            $(img).hover(function () {

                $(this).animate({ left:'20px', opacity: 0.7}, 1);


            }, function () {

                $(this).animate({ left:0, opacity: 1}, 1);

            });


        });

    }
    this.dealer_auth_window_show = function() {

        $("#dealer_auth").click(function(event) {

            event.preventDefault();
            $("#diller_login_container").show();


        });




    }

    this.close_popup_event = function() {

        $(".popupClose,.button.ok").click(function() {
           // alert(1);
            $(".popup_container").hide();

        });
    }

    this.dealer_auth = function() {

        $("#dealer_login_form .button").click(function() {
             $("#dealer_login_form .error").hide();

            if( $("#dealer_password").val() === "") {
                $("#dealer_password").css("border","1px solid red");
                return;
            }
            $("#dealer_password").css("border","1px solid grey");
            $.get("/dealer/auth/"+$("#dealer_password").val(),function(data) {

                if(data === 1) {

                    window.location.reload();

                }
                else {
                    $("#dealer_login_form .error").show();
                }

            });

        });
    }

    this.compareClearBind = function() {
        $(".clear_compare").click(function(e) {
            e.preventDefault();
            $.get("/compare_dealer/clear_compare_list", function (data) {
                $(".compare_order .cart_container .line_order").remove();

                //$(".go_to").remove();
                //$(".clear_compare").hide();
                $(".compare_order").hide();
                var compareTab = $('.order_compare_table');
                if(compareTab.length > 0 && compareTab.find('tr').length > 1)
                {
                    compareTab.remove();
                    window.location.href = '/dealer/order_history';
                }

            });
        });
    }

    this.moveToContent = function() {

      $('body:not(.main_page),html:not(.main_page_html)').scrollTop( $(".basic_content").offset().top );

    }
    this.slider = function() {

        var h = $(".center_text").height();
        $(".slider-center").css("top","-"+h+"px")

    }

    this.init = function() {
        this.brand_img();
        this.dealer_auth_window_show();
        this.close_popup_event();
        this.dealer_auth();
      //  this.moveToContent();
        this.slider();
        this.compareClearBind();

    }



}

$(document).ready(function() {
     // alert(1);
var layout = new Layout();
layout.init();




//var user = detect.parse(navigator.userAgent);













});

$(document).ready(function() {

    var width_wrapper_buttons = $('.gb-wrapper-for-buttons').width();
    $('.gb-wrapper-for-buttons')
    .css({
        "left": -width_wrapper_buttons * 1.1 + 'px'
    });

    if(localStorage.viewinfoforprovider === "true"){
        $('#gb-cost-wholesale').show();
        $('#gb-cost-wholesale-old').show();
        $('#gb-cost-mark-up').show();
        $('#gb-button-client').attr('class', 'no');
        $('#gb-button-provider').attr('class', 'current-button');
    } else {
        $('#gb-cost-wholesale').hide();
        $('#gb-cost-wholesale-old').hide();
        $('#gb-cost-mark-up').hide();
        $('#gb-button-client').attr('class', 'current-button');
        $('#gb-button-provider').attr('class', 'no');
    }

    $('.gb-wrapper-for-buttons span')
    .click(function(){
        var button = $(this).attr('id');
        if(button=='gb-button-provider'){
            $('#gb-cost-wholesale').show();
            $('#gb-cost-wholesale-old').show();
            $('#gb-cost-mark-up').show();
            $(this).attr('class', 'current-button');
            $('#gb-button-client').attr('class', 'no');
        }
        else {
            $('#gb-cost-wholesale').hide();
            $('#gb-cost-wholesale-old').hide();
            $('#gb-cost-mark-up').hide();
            $(this).attr('class', 'current-button');
            $('#gb-button-provider').attr('class', 'no');
        }

    });


    $('#gb-button-provider')
    .click(function(){
        localStorage.viewinfoforprovider = true;
    });
    $('#gb-button-client')
    .click(function(){
        localStorage.viewinfoforprovider = false;
    });
    $('#gb-exit')
    .click(function(){
        localStorage.viewinfoforprovider = false;
    });

});