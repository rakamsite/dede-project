<?php
add_action('cmb2_admin_init', function () {
    // متاباکس برای تنظیمات عمومی استوری‌ها در صفحه تنظیمات
    $cmb = new_cmb2_box(array(
        'id' => 'story_locations',
        'title' => 'مکان استوری ها',
        'object_types' => array('options-page'), // مخصوص صفحات تنظیمات
        'parent_slug' => 'dede-theme-settings',
        'option_key' => 'manage_story_locations',
        'menu_title' => "مکان استوری ها",
    ));

    $cmb->add_field(array(
        'name' => 'صفحه اصلی',
        'description' => 'شورت کد استوری های صفحه اصلی',
        'id' => 'main_page_story_shortcode',
        'type' => 'text',
    ));

    $single_product = new_cmb2_box(array(
        'id' => 'product_short_code_meta_box',
        'title' => "شورت کد استوری",
        'object_types' => array('product'), // مخصوص محصولات
    ));

    $single_product->add_field(array(
        'name' => "شورت کد استوری صفحه سینگل",
        'id' => '_dede_story_shortcode',
        'type' => 'text',
    ));

    $product_category = new_cmb2_box(array(
        'id' => 'product_category_story_meta_box',
        'title' => "شورت کد استوری دسته‌بندی",
        'object_types' => array('term'), // مخصوص ترم‌ها (دسته‌بندی و تگ‌ها)
        'taxonomies' => array('product_cat'), // دسته‌بندی محصولات ووکامرس
    ));

    $product_category->add_field(array(
        'name' => "شورت کد استوری دسته‌بندی",
        'id' => '_dede_term_story_shortcode',
        'type' => 'text',
    ));

    $product_tag = new_cmb2_box(array(
        'id' => 'product_tag_story_meta_box',
        'title' => "شورت کد استوری تگ",
        'object_types' => array('term'), // مخصوص ترم‌ها (دسته‌بندی و تگ‌ها)
        'taxonomies' => array('product_tag'), // تگ‌های محصولات ووکامرس
    ));

    $product_tag->add_field(array(
        'name' => "شورت کد استوری تگ",
        'id' => '_dede_term_story_shortcode',
        'type' => 'text',
    ));
});
