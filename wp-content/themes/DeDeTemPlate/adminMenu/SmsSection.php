<?php
if (!defined('ABSPATH')) {
    exit;
}

add_action("cmb2_admin_init", function () {
    wp_enqueue_script("admin_menu_sms_setting" ,get_template_directory_uri() . "/assets/js/SmsSettingAdminMenu.js");

    if (!class_exists('CMB2')) {
        return;
    }

    $cmb = new_cmb2_box(array(
        'id' => 'sms_settings_box',
        'title' => 'تنظیمات پیامک خدماتی',
        'object_types' => array('options-page'),
        'option_key' => 'sms_settings',
        'parent_slug' => 'dede-theme-settings',
    ));

    $cmb->add_field(array(
        'name' => 'فعال کردن پیامک خدماتی',
        'id' => 'enable_sms_service',
        'type' => 'checkbox',
        'description'=>"برای استفاده از سیستم خدماتی سرویس پیامک ، در تنظیمات مربوط به پلاگین پیامک ، نوع سرویس خدماتی را انتخاب کنید."
    ));

    $cmb->add_field(array(
        'name' => 'کد قالب پیامک ورود',
        'id' => 'login_sms_template_code',
        'type' => 'text',
        'attributes' => array(
            'data-conditional-id' => 'enable_sms_service',
            'data-conditional-value' => "true",
        ),
    ));
    $cmb->add_field(array(
        'name' => 'متن پیامک ورود',
        'id' => 'login_sms_message_template',
        'type' => 'textarea',
        'description'=>'از # برای کد فعال سازی استفاده کنید',
        'attributes' => array(
            'data-conditional-id' => 'enable_sms_service',
            'data-conditional-value' => "false",
        ),
    ));
    $cmb->add_field(array(
        'name' => 'کد قالب پیامک ثبت نام',
        'id' => 'register_sms_template_code',
        'type' => 'text',
        'attributes' => array(
            'data-conditional-id' => 'enable_sms_service',
            'data-conditional-value' => "true",
        ),
    ));
    $cmb->add_field(array(
        'name' => 'متن پیامک ثبت نام',
        'id' => 'register_sms_message_template',
        'type' => 'textarea',
        'description'=>'از # برای کد فعال سازی استفاده کنید',
        'attributes' => array(
            'data-conditional-id' => 'enable_sms_service',
            'data-conditional-value' => "false",
        ),
    ));
    $cmb->add_field(array(
        'name' => 'کد قالب پیامک فراموشی رمز عبور',
        'id' => 'forget_sms_template_code',
        'type' => 'text',
        'attributes' => array(
            'data-conditional-id' => 'enable_sms_service',
            'data-conditional-value' => "true",
        ),
    ));
    $cmb->add_field(array(
        'name' => 'متن پیامک فراموشی رمز عبور',
        'id' => 'forget_sms_message_template',
        'type' => 'textarea',
        'description'=>'از # برای کد فعال سازی استفاده کنید',
        'attributes' => array(
            'data-conditional-id' => 'enable_sms_service',
            'data-conditional-value' => "false",
        ),
    ));
    $cmb->add_field(array(
        'name' => 'کد قالب تکمیل خرید ووکامرس ',
        'id' => 'completed_order_sms_text_template_order',
        'type' => 'text',
        'attributes' => array(
            'data-conditional-id' => 'enable_sms_service',
            'data-conditional-value' => "true",
        ),
    ));
    $cmb->add_field(array(
        'name' => 'متن تکمیل خرید ووکامرس ',
        'id' => 'completed_order_sms_text',
        'type' => 'textarea',
        'desc' => "از %NAME% برای  نام و نام خانوادگی، از %ORDER_ID% برای شماره سفارش و از %WALLET_CHARGE% برای میزان شارژ کیف پول استفاده کنید .",
        'attributes' => array(
            'data-conditional-id' => 'enable_sms_service',
            'data-conditional-value' => "false",
        ),
        'default'=> "%NAME% عزیز \n  سفارش شماره %ORDER_ID% ثبت شد \n %WALLET_CHARGE% اعتبار به ازای خرید به شما تعلق گرفت که میتوانید در خرید بعدی از آن استفاده کنید. "
    ));
    $cmb->add_field(array(
        'name' => 'کد قالب موجود شد محصول ',
        'id' => 'in_stock_sms_text_template_order',
        'type' => 'text',
        'attributes' => array(
            'data-conditional-id' => 'enable_sms_service',
            'data-conditional-value' => "true",
        ),
    ));
    $cmb->add_field(array(
        'name' => 'متن موجود شد محصول ',
        'id' => 'in_stock_sms_message',
        'type' => 'textarea',
        'desc' => "از %NAME% برای  نام و نام خانوادگی، از %VAR_NAME% برای نام محصول استفاده کنید .",
        'attributes' => array(
            'data-conditional-id' => 'enable_sms_service',
            'data-conditional-value' => "false",
        ),
        'default'=> "%NAME% عزیز \n %VAR_NAME% موجود شد."
    ));
    
//    var_dump(cmb2_get_option("sms_settings","enable_sms_service"));
});