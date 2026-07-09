jQuery(document).ready(function ($) {
    let body = $('body');
    let mouseEnterTimer;
    body.on('mouseenter', '.children-cat-product-ajax', function (e) {
        mouseEnterTimer = setTimeout(function () {
            let term_id = e.target.value;
            $.ajax({
                url: ajax_admin.ajax_url,
                type: 'post',
                dataType: 'json',
                data: {
                    action: 'dede_get_child_product_thumbnail_cat',
                    term_id
                },
                beforeSend: function () {
                    $("#product-img-preview").html('<svg class="animate-spin -ml-1 mr-3 h-20 w-20 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"> <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle> <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>')
                },
                success: function (response) {
                    if (typeof response.data !== 'undefined') {
                        $("#product-img-preview").html('<img src="' + response.data + '" class="object-fill rounded-lg w-auto h-full">')
                    } else {
                        $("#product-img-preview").html('<div class="font-bold text-[#4B5259] text-3xl my-auto">درحال حاظر این محصول تصویر ندارد</div>');
                    }
                },
                error: function (error) {
                    console.log(error);
                }
            });
        }, 500); // 5 ثانیه تایم‌اوت
    }).on('mouseleave', '.children-cat-product-ajax', function () {
        clearTimeout(mouseEnterTimer);
    });
    body.on('mouseenter', '.get_child_cat_get_icon_link', function (e) {
        mouseEnterTimer = setTimeout(function () {
            let term_id = e.target.value;
            $.ajax({
                url: ajax_admin.ajax_url,
                type: 'post',
                dataType: 'json',
                data: {
                    action: 'dede_get_child_cat_get_icon_link',
                    term_id
                },
                beforeSend: function () {
                    $("#product-menu-icon").html('<svg class="animate-spin -ml-1 mr-3 h-20 w-20 text-gray-900" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"> <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle> <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>')
                },
                success: function (response) {
                    if (response.data !== '') {
                        $("#product-menu-icon").html('<img src="' + response.data + '" class="object-cover rounded-lg h-full w-auto">')
                    } else {
                        $("#product-menu-icon").html('<div class="font-bold text-[#4B5259] text-3xl my-auto">درحال حاظر آی کنی وجود ندارد</div>');
                    }
                },
                error: function (error) {
                    console.log(error);
                }
            });
        }, 500);
    }).on('mouseleave', '.get_child_cat_get_icon_link', function () {
        clearTimeout(mouseEnterTimer);
    });

    body.on('tap click', '.dede_mobile_product_menu', function () {
        let cat_id = this.value;
        $.ajax({
            url: ajax_admin.ajax_url,
            type: 'post',
            dataType: 'json',
            data: {
                action: 'dede_get_product_posts_child',
                cat_id
            },
            success: function (response) {
                $(`div#Posts_holder_${cat_id}`).html(response.data)
            }
        });
    })
});