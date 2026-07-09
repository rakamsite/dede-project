jQuery(document).ready(function($) {
    const popupClass = $('div.popupDiv');
    const buttonsElements = $('div.popupUnderSlider button');

    let dede_popup_div = $(".dede_popup_div"); // انتخاب jQuery object متناظر با المان مورد نظر
    let dede_popup_div_width = dede_popup_div.width(); // عرض div را دریافت کنید
    let dede_header_image_width = $(".dede_header_image").height(); // عرض تصویر header را دریافت کنید
    dede_popup_div.css({'margin-top':`-${dede_header_image_width}px`, 'height':`${dede_header_image_width}px`}); // استفاده از ارتفاع تصویر به عنوان مقدار margin-top و width

    buttonsElements.on('click', function() {
        const popupDivId = this.id;
        popupClass.addClass('hidden');
        $('.popupArrow').addClass('hidden');
        $(`div#${popupDivId}Popup`).toggleClass('hidden');
        $(this).prev('svg').removeClass('hidden');
    });

    $(document).on('click', function(event) {
        const target = $(event.target);
        if (!target.closest('.popupDiv').length && !target.closest('.popupButton').length) {
            popupClass.addClass('hidden');
            buttonsElements.prev('svg').addClass('hidden');
        }
    });
});
