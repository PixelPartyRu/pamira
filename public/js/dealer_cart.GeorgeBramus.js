function Dealer_form() {
    this.edit_form = ".cart_list", this.edit_button = "#edit", this.save_button = "#save_form", this.edit_cost_button = "#edit_cost", this.formalize_order_button = "#formalize_order_button", this.withoutSaveButton = "#withoutSaveButton", this.changeCostsButton = "#change_costs", this.rowClass = ".product_info_row_data", this.idDataAttr = "id", this.countProductName = "[name='count_product']", this.costName = "[name='cost_trade']", this.discountName = "[name='discount']", this.totalPriceWithoutDiscount = ".total_price_without_discount", this.totalPriceWithDiscount = ".total_price_with_discount", this.totalCount = ".count_total", this.priceWithoutDiscount = ".cost_without_discount", this.priceWithDiscount = ".cost_with_discount", this.showEditModeButtons = function() {}, this.deletePosition = function() {
        var t = this;
        $(t.rowClass + " .delete").click(function(i) {
            i.preventDefault();
            var o = $(this).parents(t.rowClass).data("id"),
                e = $(this).parents(t.rowClass);
            e.remove(), t.recalcForm(), $.get("/buy/remove_position/" + o, function() {})
        })
    }, this.getTotalCount = function() {
        return $(this.totalCount)
    }, this.getTotalPriceWithoutDiscount = function() {
        return $(this.totalPriceWithoutDiscount)
    }, this.getTotalPriceWithDiscount = function() {
        return $(this.totalPriceWithDiscount)
    }, this.getSaveButton = function() {
        return $(this.save_button)
    }, this.getProductsInfoRows = function() {
        return $(this.rowClass)
    }, this.getRowsData = function() {
        var t = this,
            i = new Array;
            // window.glob='';
        return $.each(this.getProductsInfoRows(), function(o, e) {
            i.push(t.getRowData($(e)));
            // glob+=o;

            // console.log(i[o])

        }), i
    }, this.setFormInputInValue = function() {
        var t = this;
        $.each(this.getProductsInfoRows(), function(i, o) {
            t.setRowValueData($(o))
        })
    }, this.getRowData = function(t) {
        return {
            id: t.data("id"),
            count: parseInt(t.find(this.countProductName).val()),
            cost_trade: parseFloat(t.find(this.costName).val()),
            discount: parseInt(t.find(this.discountName).val()),
            withDCell: t.find(this.priceWithDiscount),
            withoutDCell: t.find(this.priceWithoutDiscount)
        }
    }, this.setRowValueData = function(t) {
        t.find(this.countProductName).prev(".value").html(parseInt(t.find(this.countProductName).val()) ), t.find(this.costName).prev(".value").html(this.format(parseFloat(t.find(this.costName).val()))), t.find(this.discountName).prev(".value").html(parseInt(t.find(this.discountName).val()))
    }, this.getRowDataById = function(t) {
        var i = this.getRowById(t);
        return this.getRowData(i)
    }, this.getRowById = function(t) {
        return this.getForm().find("." + this.rowClass + "[data-id='" + t + "']")
    }, this.recalcRow = function(t) {
        var i = this.getRowData(t);
        i.withoutDCell.html(this.format(i.cost_trade * i.count)), isNaN(i.discount) ? price = i.cost_trade * i.count : price = i.cost_trade * i.count - i.cost_trade * i.count / 100 * i.discount, i.withDCell.find("span").html(this.format(price)), i.withDCell.find("input").val(this.format(price))
    }, this.recalcRows = function() {
        var t = this;
        $.each(this.getProductsInfoRows(), function(i, o) {
            t.recalcRow($(o)),
            $(o).find(".table-cell.id").html(i + 1)
        })
    }, this.recalcTotal = function() {
        var t = this.getRowsData(),
            i = 0,
            o = 0,
            e = 0;
        $.each(t, function(t, n) {
            i += n.count, o += n.cost_trade * n.count, e += isNaN(n.discount) ? n.cost_trade * n.count : n.cost_trade * n.count - n.cost_trade * n.count / 100 * n.discount
        }), this.getTotalPriceWithDiscount().html(this.format(e)), this.getTotalPriceWithoutDiscount().html(this.format(o)), this.getTotalCount().html(i), $(".cart:not(.compare_cart)").find(".summ").html(this.format(e)), $(".cart:not(.compare_cart)").find(".count").html(t.length)
    }, this.recalcRowById = function(t) {}, this.getWithoutSaveButton = function() {
        return $(this.withoutSaveButton)
    }, this.getEditCostButton = function() {
        return $(this.edit_cost_button)
    }, this.getEditButton = function() {
        return $(this.edit_button)
    }, this.getChangeCostsButton = function() {
        return $(this.changeCostsButton)
    }, this.getForm = function() {
        return $(this.edit_form)
    }, this.getFormInputs = function() {
        return this.getForm().find("input:not([name='cost_with_discount'])")
    }, this.getCostInputs = function() {
        return this.getForm().find("input[name='cost_with_discount']")
    }, this.setEditMode = function() {
        this.getForm().addClass("edit_mode")
    }, this.exitEdit = function() {
        $(this.discountName).each(function() {
            var t = $(this);
            "" === t.val() && (t.val(0), t.prev(".value").html("0"))
        }), this.getForm().removeClass("edit_mode").removeClass("edit_cost").removeClass("can_change_costs")
    }, this.unsetEditMode = function() {}, this.setEditCostMode = function() {
        var t = $("#all_discount");
        t.val() > 100 && t.val(100);
        var i = t.val();
        $('[name="discount"]').val(i), this.recalcForm()
    }, this.bindEditModeEvent = function() {
        var t = this;
        t.getEditButton().click(function() {
            t.setEditMode()
        })
    }, this.bindEditCostEvent = function() {
        var t = this;
        t.getEditCostButton().click(function() {
            t.setEditCostMode()
        })
    }, this.bindExitWithoutSave = function() {
        var t = this;
        t.getWithoutSaveButton().click(function() {
            t.exitEdit()
        })
    }, this.bindChangeInput = function() {
        var t = this;
        this.getFormInputs().on("change keyup", function() {
            var i = $(this);
            "discount" === i.attr("name") && i.val() > 100 && i.val(100), "count_product" === i.attr("name") && i.val() < 1 && i.val(1), t.recalcForm()
        })
    }, this.bindChangeCost = function() {
        var t = this;
        t.getCostInputs().on("change keyup", function() {
            var i, o = $(this),
                e = o.parents(t.rowClass),
                n = e.find(t.costName).val(),
                s = e.find(t.countProductName).val(),
                a = n * s,
                r = o.val().replace(/[^0-9.]/g, "");
            r > a ? (o.val(t.format(a)), o.parent().find("span").html(t.format(a)), i = 0) : (i = 100 - Math.round(100 * r / a), o.val(t.format(r)), o.parent().find("span").html(t.format(r))), e.find(t.discountName).val(parseInt(i)), e.find(t.countProductName).prev(".value").html(parseInt(i)), this.setFormInputInValue(), this.recalcTotal()
        }).on("focusout", function() {
            t.recalcForm()
        })
    }, this.bindSaveFormData = function() {
        var t = this;
        t.getSaveButton().click(function() {
            t.recalcForm(), t.exitEdit();
            var i = t.getRowsData();


            var truncated_array = new Array;
            // window.glob = i.length;

            // while(iterator < i.length) {
            //     iterator++;
            // }

            i = i.slice(0,9);

            t.setFormInputInValue(),
            $.get("/buy/dealer/save_dealer_cart_ajax", {cart: $.toJSON(i)}, function(t) {});

            // console.log($.toJSON(i))
            // console.log(i);

        })
    },

    this.recalcForm = function() {
        this.recalcRows(), this.recalcTotal(), this.setFormInputInValue();
        var t = this;
        if ($(".cart_list .table").hasClass("customer")) {
            var i = t.getRowsData();
            i = i.slice(0,9);
            t.setFormInputInValue(), $.get("/buy/dealer/save_dealer_cart_ajax", {
                cart: $.toJSON(i)
            }, function(t) {})
        }
    },


    this.testHelper = function() {}, this.format = function(t) {
        return helper.number_format(t, 0, " ", " ")
    }, this.sendMenegerOrderClick = function() {
        var t = this;
        $("#send_meneger_order").click(function() {
            var i = $(".save_customer_pdf").attr("data-user");
            "0" === i ? t.customerFormData() : t.sendMenegerOrder()
        })
    }, this.check_order_status = function() {
        var t = this;
        $(".save_customer_pdf").click(function(i) {
            var o = $(this).attr("data-user");
            if ("0" === o) {
                i.preventDefault();
                var e = t.customerFormData();
                e.addClass("save_pdf")
            }
        })
    }, this.sendMenegerOrder = function() {
        $("#message_window_box .button.ok").click(function() {
            window.location.reload()
        });
        var t = $(".cart_list").data("id");
        $("#message_window_box").show(), $.get("/customer/manager_mail_send/" + t, function() {
            $("#message_window_box .before_add").hide(), $("#message_window").height("150px"), $("#message_window_box .after_add").show(), $("#message_window_box .message").html("Заказ менеджеру успешно отправлен. В ближайшее время с Вами свяжется наш менеджер, для уточнения деталей заказа, и сроков доставки.")
        })
    }, this.customerFormData = function() {
        return $(".popup_container").hide(), $("#customer_form_box").show(), $("#customer_form_box")
    }, this.customerFormSendEvent = function() {
        var t = this;
        $("body").on("click", "#customer_form_box .button", function() {
            $("#customer_form_box .error").html("&nbsp;");
            var i = $("#customer_form_box input,select").serializeArray();
            $.get("/customer/sendFormData", i, function(i) {
                if (i.valid === !1 && $.each(i.error, function(t, i) {
                        $("#customer_form_box [name='" + t + "']").next().html(i[0])
                    }), 1 == i.valid)
                    if ($("#customer_form_box").hide(), $("#customer_form_box").hasClass("save_pdf")) {
                        $(".save_customer_pdf").attr("data-user", "1"), $(".popup_container").hide(), $(".save_customer_pdf").unbind();
                        var o = $(".save_customer_pdf").attr("href");
                        window.location.href = o
                    } else t.sendMenegerOrder()
            })
        })
    }, this.bindCanChangeCosts = function() {
        var t = this;
        this.getChangeCostsButton().click(function() {
            t.getForm().addClass("can_change_costs")
        })
    }, this.init = function() {
        this.bindEditModeEvent(), this.bindEditCostEvent(), this.bindExitWithoutSave(), this.bindChangeInput(), this.bindChangeCost(), this.bindSaveFormData(), this.bindCanChangeCosts(), this.deletePosition(), this.customerFormSendEvent(), this.sendMenegerOrderClick(), this.check_order_status()
    }
}
$(document).ready(function() {
    var t = new Dealer_form;
    t.init()
    console.log("Не могу обновить кэш");
});