<?php
add_action('cmb2_admin_init', function () {
    $cmb = new_cmb2_box(array(
        'id' => 'dede_mag_page',
        'title' => 'مجله',
        'object_types' => array('options-page'),
        'parent_slug' => 'dede-theme-settings',
        'option_key' => 'dede_mag_page_setting',
        'menu_title' => "مجله",
    ));




    $group_special_banner_1 = $cmb->add_field([
        'name' => 'بنر ویژه 1',
        'id' => 'special_banner_1',
        'type' => 'group',
        'repeatable' => false,
    ]);
    $cmb->add_group_field($group_special_banner_1, [
        'name' => 'لینک بنر ویژه 1',
        'id' => 'special_banner_1_url',
        'description' => 'لینک بنر ویژه',
        'type' => 'text_url',
        'protocols' => array('http', 'https'),
    ]);
    $cmb->add_group_field($group_special_banner_1, [
        'name' => 'تصویر بنر ویژه 1 (دسکتاپ)',
        'desc' => 'انتخاب یا آپلود بنر ویژه (دسکتاپ)',
        'id' => 'special_banner_1_img_desktop',
        'type' => 'file',
        'options' => array(
            'url' => false,
        ),
        'text' => array(
            'add_upload_file_text' => 'افزودن تصویر'
        ),
        'query_args' => array(
            'type' => array(
                'image/gif',
                'image/jpeg',
                'image/png',
            ),
        ),
        'preview_size' => 'small',
    ]);

    $cmb->add_group_field($group_special_banner_1, [
        'name' => 'تصویر بنر ویژه 1 (موبایل)',
        'desc' => 'انتخاب یا آپلود بنر ویژه 1 (موبایل)',
        'id' => 'special_banner_1_img_mobile',
        'type' => 'file',
        'options' => array(
            'url' => false,
        ),
        'text' => array(
            'add_upload_file_text' => 'افزودن تصویر'
        ),
        'query_args' => array(
            'type' => array(
                'image/gif',
                'image/jpeg',
                'image/png',
            ),
        ),
        'preview_size' => 'small',
    ]);




    $group_special_banner_2 = $cmb->add_field([
        'name' => 'بنر ویژه 2',
        'id' => 'special_banner_2',
        'type' => 'group',
        'repeatable' => false,
    ]);
    $cmb->add_group_field($group_special_banner_2, [
        'name' => 'لینک بنر ویژه 2',
        'id' => 'special_banner_2_url',
        'description' => 'لینک بنر ویژه 2',
        'type' => 'text_url',
        'protocols' => array('http', 'https'),
    ]);
    $cmb->add_group_field($group_special_banner_2, [
        'name' => 'تصویر بنر ویژه 2 (دسکتاپ)',
        'desc' => 'انتخاب یا آپلود بنر ویژه 2 (دسکتاپ)',
        'id' => 'special_banner_2_img_desktop',
        'type' => 'file',
        'options' => array(
            'url' => false,
        ),
        'text' => array(
            'add_upload_file_text' => 'افزودن تصویر'
        ),
        'query_args' => array(
            'type' => array(
                'image/gif',
                'image/jpeg',
                'image/png',
            ),
        ),
        'preview_size' => 'small',
    ]);
    $cmb->add_group_field($group_special_banner_2, [
        'name' => 'تصویر بنر ویژه 2 (موبایل)',
        'desc' => 'انتخاب یا آپلود بنر ویژه 2 (موبایل)',
        'id' => 'special_banner_2_img_mobile',
        'type' => 'file',
        'options' => array(
            'url' => false,
        ),
        'text' => array(
            'add_upload_file_text' => 'افزودن تصویر'
        ),
        'query_args' => array(
            'type' => array(
                'image/gif',
                'image/jpeg',
                'image/png',
            ),
        ),
        'preview_size' => 'small',
    ]);


    $special_post = $cmb->add_field([
        'name' => 'پست ویژه',
        'id' => 'special_post_big_right',
        'type' => 'group',
        'repeatable' => false,
    ]);
    $cmb->add_group_field($special_post, [
        'name'=>'آی دی پست ویژه',
        'id'=>'special_post_big_right_id',
        'description'=> 'آیدی پست مورد نظر برای صفحه مجله',
        'type'=>'text'
    ]);


    $special_post_square = $cmb->add_field([
        'name' => 'قسمت پست ویژه مربعی',
        'id' => 'special_post_square',
        'type' => 'group',
        'repeatable' => false,
    ]);
    $cmb->add_group_field($special_post_square, [
        'name'=>'آیدی پست اول راست بالا',
        'id'=>'special_post_square_right_top_id',
        'description'=> 'آیدی پست ویژه بالا سمت راست',
        'type'=>'text'
    ]);
    $cmb->add_group_field($special_post_square, [
        'name'=>'آیدی پست اول چپ بالا',
        'id'=>'special_post_square_let_top_id',
        'description'=> 'آیدی پست ویژه بالا سمت چپ',
        'type'=>'text'
    ]);
    $cmb->add_group_field($special_post_square, [
        'name'=>'آیدی پست اول راست پایین',
        'id'=>'special_post_square_right_bottom_id',
        'description'=> 'آیدی پست ویژه پایین سمت راست',
        'type'=>'text'
    ]);
    $cmb->add_group_field($special_post_square, [
        'name'=>'آیدی پست اول چپ پایین',
        'id'=>'special_post_square_left_top_id',
        'description'=> 'آیدی پست ویژه پایین سمت چپ',
        'type'=>'text'
    ]);


});
