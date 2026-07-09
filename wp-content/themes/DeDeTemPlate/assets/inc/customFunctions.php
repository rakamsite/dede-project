<?php
function custom_woocommerce_not_enough_stock_message($is_not_enough, $product, $values): bool
{
    $held_stock = wc_get_held_stock_quantity($product);
    $required_stock = $values['quantity'];

    if ($product->get_stock_quantity() < ($held_stock + $required_stock)) {
        $error_message = sprintf('مقدار درخواستی شما از محصول "%s"بیش از حد مجاز میباشد.',
            $product->get_name()
        );
        wc_add_notice($error_message, 'error');
    }

    return false;
}

function custom_change_stock_message($message, $product_data, $stock_quantity): string
{
    $new_message = sprintf('مقدار درخواستی شما از محصول "%s"بیش از حد مجاز میباشد.', $product_data->get_name());

    return $new_message;
}

add_filter('woocommerce_cart_product_not_enough_stock_message', 'custom_change_stock_message', 10, 3);
add_filter('woocommerce_cart_item_required_stock_is_not_enough', 'custom_woocommerce_not_enough_stock_message', 10, 3);

add_action('woocommerce_add_to_cart', function () {
    if (function_exists('rocket_clean_domain')) {
        rocket_clean_domain();
    }
});

function custom_svg_mime_types($mime_types)
{
    $mime_types['svg'] = 'image/svg+xml';

    return $mime_types;
}

function add_custom_role(): void
{
    add_role(
        'company',
        'شرکت(حقوقی)',
    );
    add_role(
        'store',
        'فروشگاه',
    );
    add_role(
        'personal',
        'شخصی(حقیقی)',
    );
    $labels = array(
        'name' => 'شهرها و کشورها',
        'singular_name' => 'شهر یا کشور',
        'search_items' => 'جستجو در شهرها و کشورها',
        'all_items' => 'همه شهرها و کشورها',
        'parent_item' => 'شهر یا کشور مادر',
        'parent_item_colon' => 'شهر یا کشور مادر:',
        'edit_item' => 'ویرایش شهر یا کشور',
        'update_item' => 'به‌روزرسانی شهر یا کشور',
        'add_new_item' => 'افزودن شهر یا کشور جدید',
        'new_item_name' => 'نام شهر یا کشور جدید',
        'menu_name' => 'شهرها و کشورها',
    );

    $args = array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'city-country'),
    );
    register_post_status('wc-wallet-charge', array(
        'label' => 'شارژ حساب',
        'public' => true,
        'exclude_from_search' => false,
        'show_in_admin_all_list' => true,
        'show_in_admin_status_list' => true,
        'label_count' => _n_noop('وضعیت سفارش سفارشی <span class="count">(%s)</span>', 'وضعیت سفارش سفارشی <span class="count">(%s)</span>')
    ));

    register_taxonomy('city_country', array('product'), $args);

}

add_action('init', 'add_custom_role');
add_filter('upload_mimes', 'custom_svg_mime_types');
add_action('cmb2_admin_init', function() {
    $prefix = '_cat_';

    $cmb = new_cmb2_box(array(
        'id'               => $prefix . 'woo_cat_options',
        'title'            => __('تنظیمات نمایش دسته‌بندی', 'textdomain'),
        'object_types'     => array('term'), // برای ترم‌ها
        'taxonomies'       => array('product_cat'), // فقط دسته‌بندی محصولات
        'new_term_section' => true, // نمایش در فرم افزودن دسته جدید
    ));

    // فیلد مخفی در هدر
    $cmb->add_field(array(
        'name'    => __('مخفی در هدر', 'textdomain'),
        'id'      => $prefix . 'hide_in_header',
        'type'    => 'checkbox',
    ));

    // فیلد مخفی در صفحه اصلی
    $cmb->add_field(array(
        'name'    => __('مخفی در صفحه اصلی', 'textdomain'),
        'id'      => $prefix . 'hide_on_home',
        'type'    => 'checkbox',
    ));
});
