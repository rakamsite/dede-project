<?php
add_action( 'cmb2_admin_init', function () {
	$cmb    = new_cmb2_box( array(
		'id'           => 'phone_metabox',
		'title'        => 'تماس با ما',
		'object_types' => array( 'options-page' ),
		'parent_slug'  => 'dede-theme-settings',
		'option_key'   => 'phone_option',
		'menu_title'   => "تنظیمات دکمه تماس",
	) );
	$cmb->add_field( array(
		'name' => "شماره تماس",
		'desc' => "کاربر ، بعد از کلیک بر روی تماس در نسخه موبایل به صفحه تماس منتقل میشود",
		'id'   =>  'phone_active',
		'type' => 'text',
	) );
	$cmb->add_field( array(
		'name' => "لینک تماس با ما",
		'desc' => "لینک قسمت تماس با ما منو",
		'id'   =>  'link_active',
		'type' => 'text',
	) );
});
