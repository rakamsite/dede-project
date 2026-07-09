jQuery(document).ready(function ($) {
    let body = $('body');
    let productMegamenu = $('ul.mainmenu-dede > li');
    let megamenu_items = $('div.megamenu_items');
    let masck_glass = $('#glassEffectMask');
    let product_menu = $("div#product");
    let product_megamenu = $("div#product_megamenu");
    let categories = $('#categories');
    let categories_megamenu = $('div#cats_mega_menu');
    let contact_us = $("#contact_us");
    let contact_us_menu = $("#contact_us_menu");
    let parent_product = $('.parents-cat-product');
    let received = $("#received");
    let about = $("#about");
    let icon_holder_img_megamenu = $('.icon-holder-img-megamenu');


    $('input[type="number"]').each(function() {
        $(this).attr('inputmode', 'numeric');
        $(this).attr('pattern', '[0-9]*');
    });

    parent_product.on('mouseenter', function () {
        $('#bran-element').addClass('hidden')
        $('.submenu').removeClass('hidden');
        $('#product-menu-icon').removeClass('hidden');
    });

    product_menu.on('click', function () {
        megamenu_items.addClass('hidden');
        product_megamenu.removeClass('hidden');
        masck_glass.removeClass('hidden');
        disable();
    });

    contact_us.on('click', function () {
        contact_us_menu.removeClass('hidden');
        masck_glass.removeClass('hidden');
        disable()
    });

    categories.on('click', function () {
        megamenu_items.addClass('hidden');
        categories_megamenu.removeClass('hidden');
        masck_glass.removeClass('hidden');
        disable()
    });

    body.on('click', '.menu-main', function () {
        $('.menu-main').removeClass('border-b-[3px] border-[#E3000F]');
        $('.menu-pointer').addClass('rotate-180');
        $(this).children('.menu-pointer').toggleClass('rotate-180');
        $(this).toggleClass('border-b-[3px] border-[#E3000F]');
    });

    productMegamenu.on('mouseenter', function () {
        let buttonVale = $(this).find('button').val();
        let DivId = $(`div.products-megamenu-child-${buttonVale}`);
        $('div.megamenu-child-all').addClass('hidden')
        $(productMegamenu).find('button').removeClass("activeMegaItem rounded-full bg-[#E3000F] after:content-['〱'] text-white");
        $(this).find('button').addClass("activeMegaItem rounded-full bg-[#E3000F] after:content-['〱'] text-white");
        DivId.removeClass('hidden')
    });

    $('.children-cat-product').on('mouseenter', function () {
        $('.children-cat-product').removeClass("activesubMegaItem rounded-full bg-[#E3000F] text-white");
        $(this).addClass("activesubMegaItem rounded-full bg-[#E3000F] text-white");
    });
    $('button#brands').on('mouseenter', function () {
        $('#bran-element').removeClass('hidden')
        $('.submenu').addClass('hidden');
        $('#product-menu-icon').addClass('hidden')
    });

    received.on('click', function (){
        megamenu_items.addClass('hidden');
        $("#received_mega_menu").removeClass('hidden');
        masck_glass.removeClass('hidden');
        disable()

    });
    about.on('click', function (){
        megamenu_items.addClass('hidden');
        $("#about_mega_menu").removeClass('hidden');
        masck_glass.removeClass('hidden');
        disable()

    })

    masck_glass.on('click', function () {
        megamenu_items.addClass('hidden');
        $(this).addClass('hidden');
        $('.menu-main').removeClass('border-b-[3px] border-[#E3000F]');
        $('.menu-pointer').addClass('rotate-180');
        enable();
    });
    icon_holder_img_megamenu.on('mouseenter mouseleave', function() {
        let icons = $(this).find('.icon-img-megamenu');
        icons.toggleClass('filter-white filter-gray');
    });
    $('form#subscrib_catolog').on('submit', function (e){
        e.preventDefault();
        $.ajax({
            type: "get",
            url:this.action()
        })
    });
    $('[data-carousel-slide-to="0"]').addClass('ml-[10px]');
    $("ul.submenu").children().first().removeClass('hidden');
    $("ul.mainmenu-dede  > li:first > button ").addClass("activeMegaItem rounded-full bg-[#E3000F] after:content-['〱'] text-white");
    $("button.myAccountSelection").on('click tap',function () {
        let TabName = $(this).val();
        Cookies.set('myAccountLoc', TabName, {sameSite: 'none', secure: true});
        window.location.href ="/my-account";
    });
    $('.forMyAccountButton').on('click tap' , function (){
       let myAccountLocation = $(this).attr('data-my-account');
        Cookies.set('myAccountLoc', myAccountLocation, {sameSite: 'none', secure: true});
    });
    $(document).on('click', function(event) {
        let excludedElements = $('.megamenu_items, #product ,#categories,#received ,#about,#contact_us');
        
        if (!excludedElements.is(event.target) && !excludedElements.has(event.target).length) {
            $(".megamenu_items").addClass('hidden');
            masck_glass.addClass('hidden');
            enable();
        }
    });
    function disable(){
       $('body').css('overflow', 'hidden');
    }
    
    function enable(){
        $('body').css('overflow', '');
    }
});