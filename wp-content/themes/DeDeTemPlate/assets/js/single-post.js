jQuery(function ($) {
    const content = $('#content');
    const headings = content.find('h1, h2, h3');
    const related_product_container = $(".sliderItemHolder");
    const sliderFixHeight = $(".sliderFixHeight");
    const tocContainer = $('<div></div>');
    headings.each(function (index) {
        const heading = $(this);
        const headingText = heading.text();
        const tagName = heading.prop('tagName');
        
        if (!heading.attr('id')) {
            heading.attr('id', 'heading-' + index);
        }
        
        const button = $('<button></button>')
            .attr('data-target', heading.attr('id'))
            .html(headingText)
            .on('click', function () {
                $('html, body').animate({
                    scrollTop: $('#' + $(this).attr('data-target')).offset().top
                }, 500);
            }).css({
                'width': '100%', 'text-align': 'right' , 'margin-bottom': '5px'
            });
        
        if (tagName === 'H1') {
            tocContainer.append(button);
        } else if (tagName === 'H2') {
            tocContainer.append(button);
        } else if (tagName === 'H3') {
            button.css('margin-right', ' 35px')
            tocContainer.append(button);
        }
    });
    $('#content-menu').append(tocContainer);
    
    const removeButtonInMobile = $("#removeButtonInMobile");
    removeButtonInMobile.on('click', function (e) {
        e.preventDefault();
        $(this).parent().parent().remove();
    });
    related_product_container.height(sliderFixHeight.height());
});
