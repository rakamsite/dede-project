<?php
add_action('cmb2_admin_init', function (){
    $cmb = new_cmb2_box( array(
        'id'           => 'فروش ویژه',
        'title'        => 'فروش ویژه',
        'object_types' => array( 'options-page' ),
        'option_key'   => 'special_sell',
        'parent_slug'  => 'dede-theme-settings',
    ) );

    $cmb->add_field( array(
        'name' => esc_html__( 'عکس', 'cmb2' ),
        'desc' => esc_html__( 'تصویر مناسب نسبت 2/3 دارد .', 'cmb2' ),
        'id'   => 'special_sell_image',
        'type' => 'file',
    ) );

    $cmb->add_field( array(
        'name' => esc_html__( 'لینک', 'cmb2' ),
        'desc' => esc_html__( 'لطفاً لینک مورد نظر خود را وارد کنید.', 'cmb2' ),
        'id'   => 'special_sell_link',
        'type' => 'text_url',
    ) );
});