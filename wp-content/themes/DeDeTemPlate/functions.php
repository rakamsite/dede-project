<?php
define('dedeTemplate', get_template_directory_uri(__DIR__));

function dede_theme_safe_require(string $relative_path): bool
{
    $absolute_path = get_template_directory() . '/' . ltrim($relative_path, '/');

    if (!file_exists($absolute_path)) {
        return false;
    }

    require_once $absolute_path;

    return true;
}

add_filter('woocommerce_enqueue_styles', '__return_empty_array');
dede_theme_safe_require('assets/import.php');
dede_theme_safe_require('adminMenu/mainAdminMenu.php');
dede_theme_safe_require('adminMenu/megamenu.php');
dede_theme_safe_require('assets/inc/CategoryPage.php');
dede_theme_safe_require('assets/inc/WidgetCreate.php');
dede_theme_safe_require('video/videoCustomPostType.php');
dede_theme_safe_require('video/VideoAttechment.php');
dede_theme_safe_require('ajax/blogPosts/php/lodeMore.php');
dede_theme_safe_require('ajax/megamenu/php/product.php');
dede_theme_safe_require('ajax/VideoViewCounter/ViewsCounter.php');
dede_theme_safe_require('ajax/quickViewProduct/php/quickViews.php');
dede_theme_safe_require('ajax/search/php/search.php');
dede_theme_safe_require('ajax/LoginRegister/php/LoginRegister.php');
dede_theme_safe_require('ajax/My-Account/php/My-Account.php');
dede_theme_safe_require('ajax/productListAndPrice/php/ProductListAndPrice.php');
dede_theme_safe_require('guarantees/CreateAndCustomize.php');
dede_theme_safe_require('template/shortcode.php');
dede_theme_safe_require('assets/inc/customCommentMeta.php');
dede_theme_safe_require('assets/inc/customUserMeta.php');
dede_theme_safe_require('assets/inc/customFunctions.php');
// dede_theme_safe_require('assets/inc/customProductMeta.php');

if (class_exists('WooCommerce')) {
    dede_theme_safe_require('assets/inc/productFunctions.php');
    dede_theme_safe_require('ajax/singleProduct/php/singleproduct.php');
    dede_theme_safe_require('ajax/AddToCard/php/AddToCard.php');
    dede_theme_safe_require('woocommerce/customize.php');
    dede_theme_safe_require('ajax/EditCartItems/php/EditCartItems.php');
    dede_theme_safe_require('ajax/CheckOut/php/discountFromWallet.php');
    dede_theme_safe_require('ajax/CheckPaymentId/php/checkPayment.php');
}

dede_theme_safe_require('assets/cmb2/init.php');

function my_theme_setup(): void
{
    add_theme_support('menus');
    add_theme_support('sidebars');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo', array(
        'height' => 50,
        'width' => 170,
        'flex-height' => true,
    ));
    add_theme_support('editor-styles');
    add_theme_support('woocommerce');
    add_theme_support('customize-selective-refresh-widgets');
    add_editor_style('editor-style.css');
    register_nav_menus(
        array(
            'cat-menu' => 'منوی دسته ها',
            'received-menu' => 'منو دریافت ها',
            'about-menu' => 'منو درباره',
            'brand-menu' => 'منو برند ها',
            '404-menu' => 'منو صفحه 404',
            'notification-menu' => 'منوی اطلاعیه حساب کاربری',
            'contact-us' => 'منو تماس ما',
            'mag-content-menu' => 'منوی صفحه مجله',
        )
    );
}

add_action('after_setup_theme', 'my_theme_setup');

add_action('after_switch_theme', function () {
    $check_exist = false;
    $check_page = get_pages(array(
        'post_type' => 'page',
    ));
    foreach ($check_page as $page) {
        if ($page->post_title == "لیست قیمت و موجودی") {
            $check_exist = true;
        }
    }
    if (!$check_exist) {
        $new_page = array(
            'post_title' => 'لیست قیمت و موجودی',
            'post_content' => "[product_list]",
            'post_status' => 'publish',
            'post_type' => 'page',
        );
        wp_insert_post($new_page);
    }

});





