//Управление формой заказов
function Dealer_form() {

    //Кнопки
    this.edit_form = ".cart_list";
    this.edit_button = "#edit";
    this.save_button = "#save_form";
    this.edit_cost_button = "#edit_cost";
    this.formalize_order_button = "#formalize_order_button";
    this.withoutSaveButton = "#withoutSaveButton";
    this.changeCostsButton = "#change_costs";

    this.rowClass = ".product_info_row_data";
    this.idDataAttr = "id";
    this.countProductName = "[name='count_product']";
    this.costName = "[name='cost_trade']";
    this.discountName = "[name='discount']";
    //Итоговые ячейки сумм
    this.totalPriceWithoutDiscount = ".total_price_without_discount";
    this.totalPriceWithDiscount = ".total_price_with_discount";
    this.totalCount = ".count_total";
    //Ячейки сумм для позиции
    this.priceWithoutDiscount = ".cost_without_discount";
    this.priceWithDiscount = ".cost_with_discount";

    this.showEditModeButtons = function() {


    }

    this.deletePosition = function () {
        var that = this;

        $(that.rowClass + " .delete").click(function (event) {
            event.preventDefault();
            var id = $(this).parents(that.rowClass).data("id");
            var row = $(this).parents(that.rowClass);
                            row.remove();
                that.recalcForm();
            //alert(1);
            $.get("/buy/remove_position/" + id, function () {


            })


        });
    }


    this.getTotalCount = function() {
        return $( this.totalCount );
    }
    this.getTotalPriceWithoutDiscount = function() {
        return $( this.totalPriceWithoutDiscount );
    }
    this.getTotalPriceWithDiscount = function() {
        return $( this.totalPriceWithDiscount );
    }
    this.getSaveButton = function() {
        return $(this.save_button);
    }
    this.getProductsInfoRows = function() {
        return $(this.rowClass);
    }
    this.getRowsData = function() {
        var that = this;
        var data = new Array;
        $.each(this.getProductsInfoRows(),function(i,row) {
            data.push( that.getRowData( $(row) ) );
        });
        return data;
    }
    //устанавливает значение из изменненых инпутов в таблицу
    this.setFormInputInValue = function() {
       var that = this;
       $.each(this.getProductsInfoRows(),function(i,row) {
           that.setRowValueData( $(row) );
        });

    }
    //Получает объект row и возвразает данные
    this.getRowData = function (row) {
        return {
            id:row.data("id"),
            count: parseInt(row.find(this.countProductName).val()),
            cost_trade: parseFloat(row.find(this.costName).val()),
            discount: parseInt(row.find(this.discountName).val()),
            withDCell:row.find(this.priceWithDiscount),
            withoutDCell:row.find(this.priceWithoutDiscount)
        }
    }

    this.setRowValueData = function(row) {

        row.find(this.countProductName).prev(".value").html( parseInt(row.find(this.countProductName).val()) );
        row.find(this.costName).prev(".value").html( this.format(parseFloat(row.find(this.costName).val())) );
        row.find(this.discountName).prev(".value").html( parseInt(row.find(this.discountName).val()) );

    }
    this.getRowDataById = function(id) {
        var row = this.getRowById(id);
        return this.getRowData( row );
    }
    this.getRowById = function(id) {
        return this.getForm().find("."+this.rowClass+"[data-id='"+id+"']");
    }
    this.recalcRow = function(row) {
        var data = this.getRowData( row );
        data.withoutDCell.html( this.format(data.cost_trade * data.count) );
        if(!isNaN(data.discount)) {
            price = data.cost_trade * data.count - (data.cost_trade * data.count/100*data.discount);
        }
        else {
            price = data.cost_trade * data.count;
        }
        data.withDCell.find('span').html(this.format(price));
        data.withDCell.find('input').val(this.format(price));


    }
    this.recalcRows = function() {
        var that = this;
        $.each(this.getProductsInfoRows(),function(i,row) {
            that.recalcRow( $(row) ) ;
            $(row).find('.table-cell.id').html(i+1);
        });
    }
    this.recalcTotal = function() {

        var data = this.getRowsData();
        var total_count = 0,total_cost_witoutD = 0,total_cost_withD = 0;
        $.each(data,function(i,row) {
            total_count+=row.count;
            total_cost_witoutD += (row.cost_trade * row.count);
            if(!isNaN(row.discount)) {
             total_cost_withD += (row.cost_trade * row.count - (row.cost_trade * row.count/100*row.discount));
            }
            else{
               total_cost_withD += (row.cost_trade * row.count);
            }

        });

        this.getTotalPriceWithDiscount().html( this.format(total_cost_withD) );
        this.getTotalPriceWithoutDiscount().html( this.format(total_cost_witoutD) );
        this.getTotalCount().html( total_count );
        $('.cart:not(.compare_cart)').find('.summ').html(this.format(total_cost_withD));
        $('.cart:not(.compare_cart)').find('.count').html(data.length);
    }
    this.recalcRowById = function (id) {

    }

    this.getWithoutSaveButton = function() {
        return $(this.withoutSaveButton);
    }
    this.getEditCostButton = function() {
        return $(this.edit_cost_button);
    }
    this.getEditButton = function() {
        return $(this.edit_button);
    }
    this.getChangeCostsButton = function() {
        return $(this.changeCostsButton);
    }

    this.getForm = function() {
        return $(this.edit_form);
    }
    this.getFormInputs = function () {
        return this.getForm().find("input:not([name='cost_with_discount'])");
    }
    this.getCostInputs = function () {
        return this.getForm().find("input[name='cost_with_discount']");
    }
    this.setEditMode = function() {
        this.getForm().addClass("edit_mode");


    }
    this.exitEdit = function() {

        $(this.discountName).each(function() {
            var obj = $(this)
            if(obj.val() === '')
            {
                obj.val(0);
                obj.prev(".value").html('0');
            }
        });
        this.getForm().removeClass("edit_mode").removeClass("edit_cost").removeClass('can_change_costs');
    }

    this.unsetEditMode = function() {

    }

    this.setEditCostMode = function() {

        //this.getForm().addClass("edit_cost");
        var _this = $("#all_discount");
        if( _this.val() > 100 )
            {
                _this.val(100);

            }
        var discount = _this.val();
        console.log('Скидка: '+discount);
        $('[name="discount"]').val( discount );
        this.recalcForm();
    }
    //Включить режим редактирования формы
    this.bindEditModeEvent = function() {
        var that = this;
        that.getEditButton().click(function() {
            that.setEditMode();
        });

    }
    this.bindEditCostEvent = function() {
        var that = this;
        that.getEditCostButton().click(function() {
            that.setEditCostMode();
        });

    }
    this.bindExitWithoutSave = function() {
        var that = this;
        that.getWithoutSaveButton().click(function() {
            that.exitEdit();
        });

    }
    this.bindChangeInput = function(){
        var that = this;
        //console.log(this.getFormInputs());
        this.getFormInputs().on('change keyup',function() {
            var _this = $(this);
            if(_this.attr('name') === 'discount' && _this.val() > 100)
            {
                _this.val(100)
            }
            if( _this.attr('name') === 'count_product' && _this.val() < 1 )
            {
                _this.val(1)
            }
            that.recalcForm();
        });
    }
    this.bindChangeCost = function() {
        var that = this;
        that.getCostInputs().on('change keyup',function() {
            var _this = $(this);
            var row = _this.parents(that.rowClass);

            var singlePrice = row.find(that.costName).val();
            var count = row.find(that.countProductName).val();
            var fullPrice = singlePrice*count;
            var currentValue = _this.val().replace(/[^0-9.]/g, "");
            var percent;
            if(fullPrice<currentValue)
            {
                _this.val(that.format(fullPrice));
                _this.parent().find('span').html(that.format(fullPrice));
                percent = 0;
            }
            else
            {
                percent = 100 - Math.round(currentValue*100/fullPrice);
                _this.val(that.format(currentValue));
                _this.parent().find('span').html(that.format(currentValue));
            }
            row.find(that.discountName).val( parseInt(percent) )
            row.find(that.countProductName).prev(".value").html( parseInt(percent) );

            this.setFormInputInValue();
            this.recalcTotal();

            /*that.recalcForm();*/

        }).on('focusout', function(){
            that.recalcForm();
        });
    }

    this.bindSaveFormData = function() {

        var that = this;
        that.getSaveButton().click(function() {

            that.recalcForm();
            that.exitEdit();
            var rows = that.getRowsData();
            that.setFormInputInValue();
            //console.log(rows);
            $.get("/buy/dealer/save_dealer_cart_ajax",{cart:$.toJSON(rows)},function(data){

            });

        });


    }

    this.recalcForm = function() {

        this.recalcRows();
        this.recalcTotal();
        this.setFormInputInValue();
        var that = this;

        //alert($(".cart_list .table").hasClass("customer"));
        if ($(".cart_list .table").hasClass("customer")) {
            var rows = that.getRowsData();
            that.setFormInputInValue();
            //console.log(rows);
            $.get("/buy/dealer/save_dealer_cart_ajax", {cart: $.toJSON(rows)}, function (data) {

            });
        }

    }
    this.testHelper = function() {

    }
    this.format = function(number) {
        return helper.number_format(number,0,' ',' ');
    }

    this.sendMenegerOrderClick = function (){
        var that = this;
        $("#send_meneger_order").click(function () {

          var user = $(".save_customer_pdf").attr("data-user");
            if(user === "0") {
            that.customerFormData();
            }
            else {
               that.sendMenegerOrder();
            }

        });
    }

    this.check_order_status = function() {

        //При скачивании pdf файла могут быть не заполнены личные данные
        //Проверяем, если не заполнены, запрашиваем
        var that = this;
        $(".save_customer_pdf").click(function(event){
            var user = $(this).attr("data-user");
         //   alert(user);
            if(user === "0") {
                event.preventDefault();
                var form = that.customerFormData();
                form.addClass("save_pdf");

            }
        });
    }
    this.sendMenegerOrder = function () {
        $("#message_window_box .button.ok").click(function () {
           // alert(1);
            window.location.reload();
        });
        //$("#send_meneger_order").click(function () {
           // alert(1);
            var id = $(".cart_list").data("id");
            $("#message_window_box").show();
            $.get("/customer/manager_mail_send/" + id, function () {
                $("#message_window_box .before_add").hide();
                $("#message_window").height('150px');
                $("#message_window_box .after_add").show();
                $("#message_window_box .message").html("Заказ менеджеру успешно отправлен. В ближайшее время с Вами свяжется наш менеджер, для уточнения деталей заказа, и сроков доставки.");



            })
        //});
    }
    //Показ формы заполнения личных данных покупателя
    this.customerFormData = function() {

        $(".popup_container").hide();
        $("#customer_form_box").show();
        return $("#customer_form_box");
        //$("#customer_form_box").attr("pid",id);
    }
    this.customerFormSendEvent = function () {
        var that = this;
        $('body').on('click', "#customer_form_box .button", function () {
            //alert(1);
            $("#customer_form_box .error").html("&nbsp;");
            var data = $("#customer_form_box input,select").serializeArray();
            $.get("/customer/sendFormData", data, function (data) {
                if (data.valid === false) {
                    $.each(data.error, function (i, e) {
                        $("#customer_form_box [name='" + i + "']").next().html(e[0]);

                    });
                }

                if (data.valid == true) {
                    $("#customer_form_box").hide();
                    if( !$("#customer_form_box").hasClass("save_pdf") ){
                        that.sendMenegerOrder();
                    }
                    else {

                        $(".save_customer_pdf").attr("data-user","1");
                        $(".popup_container").hide();
                        $(".save_customer_pdf").unbind();
                        var href = $(".save_customer_pdf").attr("href");
                        window.location.href = href;



                    }


                }

            });
        });
    };
    this.bindCanChangeCosts = function() {
        var that = this;
        this.getChangeCostsButton().click( function() {
            that.getForm().addClass('can_change_costs');
        });
    }

    this.init = function() {

        this.bindEditModeEvent();
        this.bindEditCostEvent();
        this.bindExitWithoutSave();

        this.bindChangeInput();
        this.bindChangeCost();
        this.bindSaveFormData();
        this.bindCanChangeCosts();
        this.deletePosition();

        this.customerFormSendEvent();
        this.sendMenegerOrderClick();
        this.check_order_status();
    }

}

$(document).ready(function() {

    var dealer_form = new Dealer_form();
    dealer_form.init();
    // dealer_form.lengthData();

});