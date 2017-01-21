/**
 * Модуль Filter
 */

function Filter() {

    this.message_window = $(".filter_tooltip");
    this.catalog_id = $("[name=catalog]").val();
    //текущий чекбокс, который изменен
    this.checkbox_changed = null;
    this.product_box = _.template($("#product_template").html());
    this.timerId = null;
    this.getCatalogId = function() {
        return this.catalog_id;
    }
    this.getProductBox = function() {
            return this.product_box;
        }
        //события клика на фильтр и показ текущих значений
    this.select_live_event = function() {
        $(".select_line").click(function() {
            if (!$(this).hasClass("open")) {
                $(".select_line").removeClass("open");
                $(this).addClass("open");
            } else {
                $(this).removeClass("open");
            }
        });
    }


    // var checkbox_click_color = false;
    // $('input.gb-color').click(function(){
    //     checkbox_click_color = true;
    // });
    // $('input:not(.gb-color)').click(function(){
    //     checkbox_click_color = false;
    // });


    this.reset_event = function() {
        var that = this;

        $(".filter .reset").click(function(event) {
            event.preventDefault();
            $("input").removeAttr("checked");
            $(".select_line").html("");
            that.clearDisabled();
            
            that.show_products();
            $('#is_products_filtered').val(0);
        });

    }
    this.setChangedCheckbox = function(checkbox) {

        this.checkbox_changed = checkbox;

    }
    this.getChangedCheckbox = function() {

        return $(this.checkbox_changed);

    }



    //все чекбоксы филтра
    this.getFilterCollection = function() {

            return $(".select_checkbox input[type=checkbox],.filter_brands input[type=checkbox]");

        }

        // Обновление набора выбранных чекбоксов
    this.getCheckedFilterCollection = function() {
            return $(".select_checkbox input[type=checkbox]:checked,.filter_brands input[type=checkbox]:checked,");

        }
        //очистка всех выбранных чекбоксов
    this.clearFilter = function() {

            $("input").removeAttr("checked");

        }
        //
    this.getSort = function() {

        return $('[name="sort"]:checked').val();

    }

    this.getDisabledBy = function() {
        if (this.getChangedCheckbox().parent().hasClass("select_checkbox")) {
            return "haracteristic";
        }
        else {
            return "brand";
        }

    }

    //формирования данных для аякс-получения количсетва достпуных товаров
    this.getCountAjaxData = function($cur_check) {



        var ob = {
            filter: this.getFilterJson(),
            disabled_by: this.getDisabledBy(),
            catalog: this.getCatalogId(),
            cost_trade:this.getPrice(),
            cur:$cur_check.val()
        };
        return ob;
    }


    // window.clickColor


    this.sendAjaxCountData = function ($cur_check, donePre, donePost) {
        var that = this;

        console.log("sendAjaxCountData");


        $.get("/product_catalog/filter", that.getCountAjaxData($cur_check), function (data) {
            if(donePre) donePre(data);
            
            that.setDataForWindow(data.count);

            var bh = (that.getDisabledBy() == "haracteristic"
                    &&
                    that.getCheckedHaracteristicCollection().size() > 0)
                    || (
                            that.getCheckedBrandCollection().size() == 0
                            &&
                            that.getCheckedHaracteristicCollection().size() > 0
                            &&
                            that.getDisabledBy() == "brand"
                            );
            ;
            var bb = (
                    that.getCheckedBrandCollection().size() > 0
                    &&
                    that.getDisabledBy() == "brand"
                    ) || (
                    that.getCheckedBrandCollection().size() > 0
                    &&
                    that.getCheckedHaracteristicCollection().size() == 0
                    &&
                    that.getDisabledBy() == "haracteristic"
                    );





            if (bh || bb) {
                console.log("data");
                console.log(data);
                console.log("disable_filters");
                console.log(data.disable_filters);
                that.disable_filters(data.disable_filters, window.clickColor);
            }

            if(donePost) donePost(data);
        });

    }

    this.clearDisabledAll = function() {
       var collection =  this.getFilterCollection();
       $(".select_checkbox, .brand_filter_elem").removeClass("disabled");
       collection.removeAttr("disabled").removeClass("disabled");
    }
    this.clearDisabled = function() {
        //убираются недоступные галки только из другой категории
        var collection;
        if(this.getDisabledBy() === "brand") {
            collection = this.getHaracteristicCollection();
            $(".select_checkbox").removeClass("disabled");
            collection.removeAttr("disabled").removeClass("disabled");
        }
        else {
        this.clearDisabledAll();
        }


    }
    this.getHaracteristicCollection  = function() {
        return $(".select_checkbox input[type=checkbox]");
    }
    this.getCheckedHaracteristicCollection = function() {
        return $(".select_checkbox input[type=checkbox]:checked");
    }
    this.getBrandCollection = function() {
        return $(".filter_brands input[type=checkbox]");

    }
        this.getCheckedBrandCollection = function() {
        return $(".filter_brands input[type=checkbox]:checked");

    }

    //событие клика на чекбокс фильтра
    this.changeFilter = function() {

            var that = this;
            var collection = this.getFilterCollection();
            collection.change(function() {

                that.setChangedCheckbox(this);
                that.clearDisabled();
                that.setLine();
                that.showWindowWithMessage();
                that.sendAjaxCountData( $(this) );

            });

        }

    this.setLine = function() {

        if( this.getChangedCheckbox().parent().hasClass("select_checkbox")) {

            if(this.getChangedCheckbox().is(":checked")) {
                this.appendFilterFromLine();
            }
            else {
                this.deleteFilterFromLine();
            }

        }


    }

    this.appendFilterFromLine = function() {

            var label = this.getChangedCheckbox().parent().find("label").html();
            this.getChangedCheckbox().parents(".select").find(".select_line").append("<div>"+label+"</div>");

    }

    this.deleteFilterFromLine = function() {

        if(this.deleteFilterFromLine.arguments.length > 0) {
            var filter_class = this.deleteFilterFromLine.arguments[0];

            var label = $(filter_class).find("label").html();
            $(filter_class).parents(".select").find(".select_line").find("div:contains('" + label + "')").remove();

        }
        else {
        var label = this.getChangedCheckbox().parent().find("label").html();
        this.getChangedCheckbox().parents(".select").find(".select_line").find("div:contains('"+label+"')").remove();
        }

    }
    //получение значений филтра в JSON
    this.getFilterJson = function() {

            return $.toJSON(this.getCheckedFilterCollection().serializeArray());
        }
        //показ всплывающего окна с сообщение о загрузке
        // chekbox - чекбок возле которого надо показать окно
    this.showWindowWithMessage = function() {
            clearTimeout(this.timerId);
            $(".filter_tooltip").removeClass("show");
            var line = $(this.checkbox_changed).parent();
            var top = line.offset().top + line.height();
            this.message_window.css("top", top);
            this.message_window.css("left", line.offset().left);
            this.message_window.addClass("show width_message");
        }
        //Устанавливаем количество товара в окне и показываем
    this.setDataForWindow = function(count) {
        // console.log("count");
        // console.log(count);
        // console.log(this.message_window.find(".product_info span:first a").html());
        this.message_window.find(".product_info span:first a").html(count);
        $(".filter_tooltip").removeClass("width_message");
        this.timerId = setTimeout(function() {
            $(".filter_tooltip").removeClass("show");
        }, 2000);

    }
    this.getProductHaracteristicChecked = function() {
        return $(".product_haracteristic_filter input:checked");
    }
    this.getProductHaracteristicCheckedSize = function() {
        return $(".product_haracteristic_filter input:checked").size();
    }
    this.getBrandChecked = function() {
        return $(".product_haracteristic_filter input:checked");
    }
    this.getBrandCheckedSize = function() {
        return $(".filter_brands input:checked").size();
    }

    //нужни ли делать недоступными чекбоксы
    this.needDisabled = function() {

        return (this.getDisabledBy() === "haracteristic" && this.getProductHaracteristicCheckedSize() > 0) || (this.getDisabledBy() === "brand" && this.getBrandCheckedSize() > 0);

    }




    this.disable_filters = function(values,clickColor="false") {
        /**
         * 03/005 | George Bramus | 2016-11-25
         * Отследим нажатие чекбокса. Нас интересует Материал и Цвет.
         */

        var that = this;
        that.clearDisabled();
        // var clickColor = that.clickCheckboxColor();


        if (that.needDisabled() === true)

        {

            $.each(values, function(name, values) {

                $.each(values, function(i, value) {

                    // if( !(clickColor == true && name == "color") )
                        that.set_disable_checkbox(name + value);

                });


            });
        }
    }

    this.set_disable_checkbox = function(filter_class) {

        // console.log("Фильтр: " + filter_class);

        $("." + filter_class).addClass("disabled");
        $("." + filter_class + " input").removeAttr("checked");
        $("." + filter_class + " input").attr("disabled", "disabled");
        //Удаляем из видимой линии
        //
        this.deleteFilterFromLine("." + filter_class);
    }



    this.filter_button_click = function() {
        var that = this;
        $(".filter .filter-products").click(function() {
            that.show_products();
        });
    }

    this.show_products = function () {
        $('#is_products_filtered').val(1);
            
        //console.clear();
        var that = this;
        $(".product_list").html("");
        $.get("/product_catalog/getFilterProduct", that.getProductUpdateAjaxData(), function (data) {
            $.each(data, function (i, product) {
                that.appendProduct(product);
            });

        });
    }
    this.show_click = function() {
        var that = this;
        $("body").on("click",".show_link",function(event) {
            //alert(1);
           event.preventDefault();
           that.show_products();
        });


    }
    this.appendProduct = function(product) {
        var product_html = this.product_box({
            img: product.img,
            cost_trade: product.cost_trade,
            name: product.name,
            id:product.id,
            alias:product.alias,
            sales_leader:product.sales_leader,
            sticker_promo: product.sticker_promo,
            sticker_action: product.sticker_action
        });
        product_html = $(product_html);
        if(product.viewcost == 1)
        {
            product_html.find('.price_info.nocost').remove();
        }
        else
        {
            product_html.find('.price_info:not(.nocost)').remove();
            product_html.find('.buy_button').remove();
        }


            if(product.sticker_promo == 1) {
                product_html.prepend('<div class="sticker-promo">&nbsp;</div>');
            }
            else if(product.sticker_action == 1) {
                product_html.prepend('<div class="sticker-action">&nbsp;</div>');
            }

        if(product.sales_leader == 1) {
            product_html.prepend('<div class="sticker">&nbsp;</div>');
        }

        if(product.sales_leader == 1 || product.sticker_action == 1 || product.sticker_promo == 1){
            product_html.addClass("with_sticker");
        }

        $(".product_list").append(product_html);


    }

    //подготовка данных для отправки аяксом
    this.getProductUpdateAjaxData = function() {


        var ob = {
            filter: this.getFilterJson(),
            catalog: this.getCatalogId(),
            sort: this.getSort(),
            cost_trade:this.getPrice()

        };

        return ob;
    }
    this.getPrice = function() {

        return {
            min: $("#minCost").val(),
            max: $("#maxCost").val()
        }
    }
    this.window_close_event = function() {

        $(".filter_tooltip .close").click(function() {

            clearTimeout(this.timerId);
            $(".filter_tooltip").removeClass("show");
        });
    }

    this.init = function() {
        var that = this;
        //this.clearDisabledAll();
        this.select_live_event();
        this.changeFilter();
        this.reset_event();
        this.filter_button_click();
        this.window_close_event();
        this.show_click();
        
        var $checked = this.getCheckedFilterCollection();
        //if($checked.length) {
        if(+$('#is_products_filtered').val() > 0) {
            this.setChangedCheckbox($checked.first());
            
            $(".product_list").hide();
            this.sendAjaxCountData($('<input type="checkbox" value="1" />'), null, function(data) {
                that.show_products();
                $(".product_list").show();
            });
        }
        // this.sh();

      //  this.test_cache();




    }
    this.test_cache = function() {
        var that = this;
        $(".filter").before("<div class='tc' style='background:red;width:30px;height:30px'></div>");
        $(".filter").before("<div class='link_info' ></div>");

        $(".tc").click(function() {
            //console.clear();
            var inf = that.getCountAjaxData();

            $.ajax({
                url: "/catalog/frame",
                type: "POST",
                data:inf,
                beforeSend: function (jqXHR, settings) {


                    $(".link_info").html("<a class='hhh'>1</a>");
                    $(".hhh").attr("href","/product_catalog/filter/?"+settings.data);


                },
                success: function (data) {
                   // alert("Прибыли данные: " + data);
                }
            });


        });

    }



};




$(document).ready(function() {

    $('input[type="checkbox"]').click(function(){
        window.clickColor = false;
    });
    $('input.gb-color').click(function(){
        window.clickColor = true;
    });

    var filter = new Filter();
    filter.init();


});








$(document).ready(function(){



/* слайдер цен */
var min = +$("#slider").data("min");
var max = +$("#slider").data("max");

if(+$("#minCost").val() < min || +$("#minCost").val() >= max) {
    $("#minCost").val(min);
}

if(+$("#maxCost").val() <= +$("#minCost").val()) {
    $("#maxCost").val(max);
}

    $("#slider").slider({
        min: min,
        max: max,
        values: [$("#minCost").val(), $("#maxCost").val()],
        range: true,
        stop: function (event, ui) {
            $("input#minCost").val($("#slider").slider("values", 0));
            $("input#maxCost").val($("#slider").slider("values", 1));

        },
        slide: function (event, ui) {
            $("input#minCost").val($("#slider").slider("values", 0));
            $("input#maxCost").val($("#slider").slider("values", 1));
        }
    });

    $("input#minCost").change(function () {

        var value1 = $("input#minCost").val();
        var value2 = $("input#maxCost").val();

        if (parseInt(value1) > parseInt(value2)) {
            value1 = value2;
            $("input#minCost").val(value1);
        }
        $("#slider").slider("values", 0, value1);
    });


$("input#maxCost").change(function(){

	var value1=$("input#minCost").val();
	var value2=$("input#maxCost").val();

	if (value2 > max) { value2 = max; $("input#maxCost").val(max)}

	if(parseInt(value1) > parseInt(value2)){
		value2 = value1;
		$("input#maxCost").val(value2);
	}
	$("#slider").slider("values",1,value2);
});



// фильтрация ввода в поля
	$('input').keypress(function(event){
		var key, keyChar;
		if(!event) var event = window.event;

		if (event.keyCode) key = event.keyCode;
		else if(event.which) key = event.which;

		if(key==null || key==0 || key==8 || key==13 || key==9 || key==46 || key==37 || key==39 ) return true;
		keyChar=String.fromCharCode(key);

		if(!/\d/.test(keyChar))	return false;

	});


});



// $(document).ready(function(){
//     $('div.color .select_checkbox input')
//     .click(function(){
//         console.log("Выбрали чекбокс");
//         $('div.color .select_checkboxes .clear .select_checkbox')
//         .removeClass('disabled');
//         console.log("После удаления классов..");
//     });
// });