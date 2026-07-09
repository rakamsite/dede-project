jQuery(document).ready(function ($) {
    let body = $('body');
    const quickViewProduct = $('#quick_view_container');
    const quickViewProduct_options = {
        placement: 'bottom-right',
        backdrop: 'dynamic',
        backdropClasses: 'bg-[#383838] bg-opacity-50 dark:bg-opacity-80 fixed inset-0 z-[999] quickview-backdrop',
        closable: true,
        onShow:function (){
            body.addClass('!overflow-hidden');
        },
        onHide: () => {
            body.removeClass('!overflow-hidden');
            $(`.quickview-backdrop`).removeClass('z-[999]').addClass('z-30')
        },
    };
    const quickView = new Modal(quickViewProduct[0], quickViewProduct_options)
    const quick_container = $("div#quick_view_container");
    body.on('click', '.quick_post_view', function () {
        let product_id = $(this).val()
        $.ajax({
            url: ajax_admin.ajax_url,
            type: 'post',
            data: {
                action: 'quick_view_product',
                product_id,
            },
            beforeSend:function (){
                quickView.show();
                quick_container.html('<div class="absolute top-1/2 left-1/2 transform -translate-y-1/2 -translate-x-1/2"> <svg class="animate-spin h-24 w-24 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"> <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle> <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path </svg> </div>\n');
            },
            success: function (response) {
                quick_container.html(response);
                $('.excerpt-style > ul').addClass('list-disc marker:text-[#E3000F]');
                $('.quick_view_container_close_button').on('click' ,function () {
                    quickView.hide();
                });
            },
            error: function (err) {
                console.log(err)
            }
        });
        quickView.show();
    });
    body.on('click tap', 'button.slider-inductor' ,function (){
        let imageUrl = $(this).val();
        $('img#image_slider_').attr('src' , imageUrl);
        $('a#image_fancy_').attr('href' , imageUrl);
    });
});
