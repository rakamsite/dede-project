<?php
define('dedeTemplate', get_template_directory_uri(__DIR__));
if (!defined('ABSPATH')) {
    define('ABSPATH', dirname(__FILE__) . '/');
}
if (!defined('WPINC')) {
    require ABSPATH . 'wp-includes/load.php';
}
if (!defined('WOOCOMMERCE_PLUGIN_FILE')) {
    define('WOOCOMMERCE_PLUGIN_FILE', ABSPATH . 'wp-content/plugins/woocommerce/woocommerce.php');
}
if (!defined('WC_ABSPATH')) {
    define('WC_ABSPATH', ABSPATH . 'wp-content/plugins/woocommerce/');
}
require_once WOOCOMMERCE_PLUGIN_FILE;
add_filter('woocommerce_enqueue_styles', '__return_empty_array');
require_once 'assets/import.php';
require_once 'adminMenu/mainAdminMenu.php';
require_once 'adminMenu/megamenu.php';
require_once 'assets/inc/CategoryPage.php';
require_once 'assets/inc/WidgetCreate.php';
require_once 'video/videoCustomPostType.php';
require_once 'video/VideoAttechment.php';
require_once 'assets/inc/productFunctions.php';
require_once 'ajax/blogPosts/php/lodeMore.php';
require_once 'ajax/megamenu/php/product.php';
require_once 'ajax/VideoViewCounter/ViewsCounter.php';
require_once 'ajax/singleProduct/php/singleproduct.php';
require_once 'ajax/AddToCard/php/AddToCard.php';
require_once 'ajax/quickViewProduct/php/quickViews.php';
require_once 'ajax/search/php/search.php';
require_once 'ajax/LoginRegister/php/LoginRegister.php';
require_once 'woocommerce/customize.php';
require_once 'ajax/My-Account/php/My-Account.php';
require_once 'ajax/productListAndPrice/php/ProductListAndPrice.php';
require_once 'guarantees/CreateAndCustomize.php';
require_once 'ajax/EditCartItems/php/EditCartItems.php';
require_once 'ajax/CheckOut/php/discountFromWallet.php';
require_once 'ajax/CheckPaymentId/php/checkPayment.php';
require_once 'template/shortcode.php';
require_once 'assets/inc/customCommentMeta.php';
require_once 'assets/inc/customUserMeta.php';
require_once 'assets/inc/customFunctions.php';
//require_once 'assets/inc/customProductMeta.php';
require_once 'assets/cmb2/init.php';

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






