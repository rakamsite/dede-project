<?php
add_action('cmb2_admin_init', function () {
    $cmb = new_cmb2_box(array(
        'id' => 'Cart_information',
        'title' => 'سبد خرید',
        'object_types' => array('options-page'),
        'parent_slug' => 'dede-theme-settings',
        'option_key' => 'Cart_information_page',
        'menu_title' => "سبد خرید",
    ));
    $cmb->add_field(array(
        'name' => 'توضیحات زیر قسمت کد تخفیف',
        'id' => "description_under_coupon",
        'type' => 'wysiwyg',
        'options' => array(
            'textarea_rows' => 5,
        ),
    ));
});
