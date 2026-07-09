<?php

add_action('cmb2_admin_init', function () {
    $cmb = new_cmb2_box(array(
        'id' => 'buyConditionBox',
        'title' => 'شرایط خرید',
        'object_types' => array('options-page'),
        'parent_slug' => 'dede-theme-settings',
        'option_key' => 'buy_condition_option_key',
        'menu_title' => "شرایط خرید",
    ));
    $cmb->add_field(array(
        'name' => "مبلغ حداقل خرید برای گزینه تماس با شرکت",
        'desc' => "مبلغ بر اساس واحد پولی کل سایت محاسبه میگردد",
        'id' => 'buy_condition_value',
        'type' => 'text',
    ));
    $cmb->add_field(array(
        'name' => "شناسه پرداخت",
        'desc' => "شناسه پرداخت مورد استفاده در تماس با شرکت",
        'id' => 'buy_condition_payment_id',
        'type' => 'text',
    ));
});
