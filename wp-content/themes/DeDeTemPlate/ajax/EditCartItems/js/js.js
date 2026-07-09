jQuery(document).ready(function ($) {
    const body = $("body");
    const Edit_detailed_cart_item_container = $("#Edit_detailed_cart_item_container");
    const Edit_detailed_cart_item_spinner = $("#Edit_detailed_cart_item_spinner");
    const Edit_cart_update_spinner = "Edit_cart_update_spinner";
    const Edit_detailed_cart_item_content_container = $("#Edit_detailed_cart_item_content_container");
    let Edit_detailed_cart_item_button = ".show_or_edit_order";
    let Edit_cart_update = ".Edit_cart_update";
    let close_edit_container = $("#close_edit_container");
    let update_button = $('button[name=update_cart]');
    let formData;
    const options = {
        placement: 'bottom-right',
        backdrop: 'dynamic',
        backdropClasses:
            'bg-gray-900/50 dark:bg-gray-900/80 fixed inset-0 z-[70]',
        closable: true,
        onShow:function (){
            body.addClass('!overflow-hidden');
        },
        onHide : function () {
            body.removeClass('!overflow-hidden');
        }
    };

    let UpdateEnable = function () {
        update_button.removeAttr('disabled');
    }

    const modal = new Modal(Edit_detailed_cart_item_container[0], options,);

    body.on('click tap', Edit_detailed_cart_item_button, function () {
        modal.show();
        Edit_detailed_cart_item_spinner.removeClass("hidden");
        Edit_detailed_cart_item_content_container.addClass("hidden");
        close_edit_container.addClass("hidden");
        formData = {
            action: "dede_get_edit_cart",
            cart_item_key: $(this).val(),

        };

        getItemsDataAjax(formData).then((res) => {
            Edit_detailed_cart_item_spinner.addClass("hidden");
            Edit_detailed_cart_item_content_container
                .removeClass("hidden")
                .html(res);
            close_edit_container.removeClass("hidden")
        })

    })

    close_edit_container.on("click tap", function () {
        modal.hide();
    });

    body.on("click tap", '.quantityUp_card_list', function () {
        UpdateEnable();
        ManageEditCartQuantity("P");
    });

    body.on('click tap', '.quantityDown_card_list', function () {
        UpdateEnable();
        ManageEditCartQuantity("N");
    });

    body.on("click tap", Edit_cart_update, function () {
        let product_id = $(this).val();
        let var_id = $(this).attr('data-var-id');
        let quantity_final = $("#Edit_cart_final_quantity").text();
        let unit_selected = $("#Edit_cart_unit_selected").text();
        let quantity = $("#Edit_cart_quantity");
        let order_main_unit_name = $("#Edit_cart_main_unit");
        $(`#${Edit_cart_update_spinner}`).removeClass('hidden')
        formData = {
            action: 'add_to_cart_ajax',
            product_id,
            var_id,
            quantity: quantity_final,
            unit_selected: unit_selected,
            unit_quantity: quantity.val(),
            unit_selected_pakage: quantity.attr("data-package-quantity"),
            main_unit_name: order_main_unit_name.text(),
        }

        getItemsDataAjax(formData).then((res) => {
            $(`#${Edit_cart_update_spinner}`).addClass('hidden')
            alert("بروز شد");
            modal.hide();
            window.location.reload()
        })
    })

    function getItemsDataAjax(formData) {
        return new Promise(function (resolve, reject) {
                $.ajax({
                    url: ajax_admin.ajax_url,
                    type: 'post',
                    data: formData,
                    success: function (res) {
                        resolve(res)
                    }
                });
            }
        )
    }

    function ManageEditCartQuantity(type) {
        let quantity_final = $('#Edit_cart_final_quantity');
        let quantity = $("#Edit_cart_quantity");
        let price_final_symbol = $(".Edit_cart_price > span > bdi > .woocommerce-Price-currencySymbol").text();
        let price_final = $(".Edit_cart_price > span > bdi").text();
        let quantity_val = parseInt(quantity.val());
        let pakage_quantity = parseInt(quantity.attr("data-package-quantity"));
        let edit_cart_main_unit_name = $("#edit_cart_main_unit_name");
        let edit_cart_selected_unit = $("#edit_cart_selected_unit");

        price_final = price_final.replace(".", '');
        price_final = price_final.replace(/[^0-9]/g, '');
        let New_quantity_val;

        if (type === "P") {
            if (edit_cart_main_unit_name.val() === edit_cart_selected_unit.val()) {
                quantity.val(quantity_val + pakage_quantity)
            } else {
                quantity.val(quantity_val + 1)
            }
        } else if (type === "N") {
            if (quantity_val <= 1 || quantity_val <= pakage_quantity) {
                return false;
            }
            if (edit_cart_main_unit_name.val() === edit_cart_selected_unit.val()) {
                quantity.val(quantity_val - pakage_quantity)
            } else {
                quantity.val(quantity_val - 1)
            }
        } else {
            quantity.val(quantity_val);
        }
        if (edit_cart_main_unit_name.val() === edit_cart_selected_unit.val()) {
            New_quantity_val = parseInt(quantity.val())
        } else {
            New_quantity_val = parseInt(quantity.val()) * pakage_quantity
        }

        quantity_final.text(New_quantity_val);
        calculateTotalPrice(price_final, price_final_symbol, New_quantity_val)
    }

    body.on('blur', '#Edit_cart_quantity', function () {
        let val = this.value;
        let pakage_quantity = parseInt($(this).attr("data-package-quantity"));
        if (val > 0){
            if (val % pakage_quantity !== 0) {
                alert(
                    `مقدار وارد شده صحیح نیست . باید مقدار بر ${pakage_quantity} بخش پذیر باشد.`,
                );
                $(this).val(pakage_quantity);
            }else {
                if (val.trim() === "") {
                    $(this).val(pakage_quantity);
                }
                ManageEditCartQuantity("x");
            }

        }else {
            alert('مقدار صحیح نیست.');
            $(this).val(pakage_quantity);
        }
    })

    function calculateTotalPrice(price_final, price_final_symbol, quantity_final_val) {
        price_final = price_final.replace(".", '');
        price_final = price_final.replace(/[^0-9]/g, '');
        let number = price_final * quantity_final_val;
        let formattedNumber = number.toLocaleString("fa-IR") +"&nbsp;"+ price_final_symbol;
        $(".Edit_cart_price_total").html(formattedNumber);
    }
})
;