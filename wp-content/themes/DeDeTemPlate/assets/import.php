<?php
add_action("wp_enqueue_scripts" , function (){
    wp_enqueue_script( "flowbite", dedeTemplate . '/node_modules/flowbite/dist/flowbite.min.js', array( 'jquery' ), '1.0', true, );
    wp_enqueue_script( "menu-script-DeDe", dedeTemplate . '/assets/js/menu.js', array( 'jquery' ), '1.0', true, );
    wp_enqueue_script( "popup-script-DeDe", dedeTemplate . '/assets/js/PopupOver.js', array( 'jquery' ), '1.0', false );
    wp_enqueue_script( "search-product-DeDe", dedeTemplate . '/ajax/search/js/js.js', array( 'jquery' ), '1.0', false, );
    wp_enqueue_script( "megamenu-product-DeDe", dedeTemplate . '/ajax/megamenu/js/product.js', array( 'jquery' ), '1.0', false, );
    wp_enqueue_script( "add-To-Card-ajax-js", dedeTemplate . '/ajax/AddToCard/js/AddToCard.js', array( 'jquery' ), '1.1', false );
    wp_localize_script( 'megamenu-product-DeDe', 'ajax_admin', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
    wp_enqueue_script( 'ajax-quick-views', dedeTemplate . '/ajax/quickViewProduct/js/quickViews.js', array( 'jquery' ), '1.1', false );
    wp_enqueue_script("edit-cart-items-js", dedeTemplate . '/ajax/EditCartItems/js/js.js', array('jquery'), '1.0', false,);
    wp_enqueue_style( "main-style-DeDe", dedeTemplate . '/assets/css/style.css' );
    wp_enqueue_script("single-product-js", dedeTemplate . '/assets/js/singleProduct.js', array('jquery'), '1.0', false,);
    wp_enqueue_script( 'my-account-js', dedeTemplate . '/ajax/My-Account/js/My-Account.js', array( 'jquery' ), 1.0, true );
    wp_enqueue_script( 'persian-date-picker-js', dedeTemplate . '/node_modules/persian-datepicker/dist/js/persian-datepicker.min.js', array(), 1.0, false );
    wp_enqueue_script( 'persian-date-js', dedeTemplate . '/node_modules/persian-date/dist/persian-date.min.js', array(), 1.0, false );
    wp_enqueue_style( 'persian-date-picker-css', dedeTemplate . '/node_modules/persian-datepicker/dist/css/persian-datepicker.min.css' );
    if (is_checkout()) {
        wp_enqueue_script('check-out-script', get_template_directory_uri() . '/ajax/CheckOut/js/CheckOut.js', array('jquery'), null, true);
        wp_enqueue_script('persian-date-picker-js', get_template_directory_uri() . '/node_modules/persian-datepicker/dist/js/persian-datepicker.min.js', array(), '1.0', true);
        wp_enqueue_script('persian-date-js', get_template_directory_uri() . '/node_modules/persian-date/dist/persian-date.min.js', array(), '1.0', true);
        wp_enqueue_script('check-payment-id', get_template_directory_uri() . '/ajax/CheckPaymentId/js/checkPayment.js', array(), '1.0', true);
        wp_enqueue_style('persian-date-picker-css', get_template_directory_uri() . '/node_modules/persian-datepicker/dist/css/persian-datepicker.min.css');
    }
    if (!is_user_logged_in()){
        wp_enqueue_script( "Login-Register-ajax-dede", dedeTemplate . '/ajax/LoginRegister/js/loginregister.js', array( 'jquery' ), '1.0', true, );
    }
    if (is_single()){
        wp_enqueue_script( 'related-products', dedeTemplate . '/assets/js/relatedProducts.js', array(), 1.0, false );
    }
});
function dequeue_custom_css_dashboard(): void
{
    wp_dequeue_style( 'main-style-DeDe' );
}

add_action( 'admin_enqueue_scripts', 'dequeue_custom_css_dashboard' );
