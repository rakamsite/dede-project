jQuery(document).ready(function ($) {
    let body = $('body');
    const AfterAddCart = $('#after_add_to_card');
    const add_to_cart_status_after_add_to_cart = $(".add_to_cart_status_after_add_to_cart");
    const item_added_to_cart = $("div.item_added_to_cart");
    const mini_card_information = $("div#mini_card_information");
    const AfterAddCartOptions = {
        placement: 'bottom-right',
        backdrop: 'dynamic',
        backdropClasses: 'bg-[#383838] bg-opacity-50 fixed inset-0 z-[99999] add_to_card_back_ground',
        closable: false,
    };
    let minicard_final_price = $("#minicard_final_price")
    let showMoreButtonsCart = $(".showMore-card");

    const after_cart_added = new Modal(AfterAddCart[0], AfterAddCartOptions);
    body.on('click tap', '.add-to-card', function () {
        let quickView = $("#quick_view_post_viwed");
        let stock_manager = $('.stock_manager > p').text();
        let loading_spin = $('.add-to-card-loading');
        let product_id = $(this).val();
        let card_red_dot = $(".card-red-dot");
        let unit_selected,unit_quantity,quantity_final,quantity,var_id,order_main_unit_name;

        if (quickView.length > 0 ) {
            quantity = quickView.find("#quantity");
            quantity_final = quickView.find('#quantity_final').text();
            unit_selected = quickView.find("#unit_selected");
            unit_quantity = quickView.find("#unit_quantity");
            order_main_unit_name = quickView.find("#order_main_unit_name");
        } else {
            quantity = $("#quantity");
            quantity_final = $('#quantity_final').text();
            unit_selected = $("#unit_selected");
            unit_quantity = $("#unit_quantity");
            order_main_unit_name = $("#order_main_unit_name");
        }

        if (stock_manager === "نا موجود") {
            alert('محصول مورد نیاز شما موجود نمیباشد.')
            return false;
        }

        if ($(this).attr('data-var-id')) {
            var_id = $(this).attr('data-var-id');
        }
        body.on('click', 'button.close_after_add_to_cart', function () {
            after_cart_added.hide();
            $(".add_to_card_back_ground").removeClass("z-[99999]").addClass("z-40");
        })
        $.ajax({
            url: ajax_admin.ajax_url,
            type: 'post',
            data: {
                action: 'add_to_cart_ajax',
                product_id,
                quantity:quantity_final,
                var_id,
                unit_selected: unit_selected.val(),
                unit_quantity: unit_quantity.val(),
                unit_selected_pakage : quantity.attr("data-pakage-quantity"),
                main_unit_name : order_main_unit_name.text(),
            },
            beforeSend: function () {
                loading_spin.removeClass('hidden')
            },
            success: function (response) {
                if (response.success === true) {
                    if (response.data.added === "true") {
                        loading_spin.addClass('hidden');
                        card_red_dot.removeClass('hidden').text(response.data.cart_count);
                        item_added_to_cart.html(response.data.product_info);
                        mini_card_information.html(response.data.minicartinformation);
                        add_to_cart_status_after_add_to_cart
                            .removeClass('text-[#E3000F]')
                            .addClass('text-[#008826]')
                            .text(response.data.message);
                        minicard_final_price.html(" جمع کل:"+"&nbsp;"+response.data.final_price)
                    } else {
                        loading_spin.addClass('hidden');
                        item_added_to_cart.html(response.data.product_info);
                        mini_card_information.html(response.data.minicartinformation);
                        add_to_cart_status_after_add_to_cart
                            .removeClass('text-[#008826]')
                            .addClass('text-[#E3000F]')
                            .text(response.data.message);
                    }
                    after_cart_added.show();
                } else {
                    loading_spin.addClass('hidden');
                    alert(response.data)
                }
            },
            error: function (err) {
                console.log(err)
            }
        });
    });

    body.on('click tap', 'svg.remove-product', function () {
        let product_id = $(this).attr('date-product-id');
        let cart_item_key = $(this).attr('data-cart-items');
        $.ajax({
            url: ajax_admin.ajax_url,
            type: 'post',
            data: {
                action: 'add_to_cart_ajax',
                product_id,
                cart_item_key,
                remove_product: 0
            },
            success: function (response) {
                $('.card-red-dot').removeClass('hidden').text(response.data.cart_count);
                location.reload();
            },
            error: function (err) {
                console.log(err)
            }
        });
    });

    $("button#all_videos_view").on("click tap", function () {
        $(this).remove();
        $('div.video-sidebar').removeClass('h-[500px]');
    });

    showMoreButtonsCart.on('click tap', function () {
        let variationElement;
        if ($(this).next('.variation').length) {
            variationElement = $(this).next('.variation')
        } else {
            variationElement = $(this).parent('.variation');
        }
        variationElement.slideToggle()
    });

    function get_quantity_value (){
        let quickView = $("#quick_view_post_viwed");
        let unit_quantity;
        if (quickView.length > 0 ) {
            unit_quantity = quickView.find("#unit_quantity");
        }else{
            unit_quantity = $("#unit_quantity");
        }
        return unit_quantity.val();
    }
});

