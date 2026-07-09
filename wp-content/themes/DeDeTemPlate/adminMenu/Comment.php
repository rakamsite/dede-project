<?php
add_action( 'cmb2_admin_init', function () {
	$cmb    = new_cmb2_box( array(
		'id'           => 'Comment_section',
		'title'        => 'نظرات',
		'object_types' => array( 'options-page' ),
		'parent_slug'  => 'dede-theme-settings',
		'option_key'   => 'Comment_section_key',
		'menu_title'   => "تنظیمات قسمت نظرات",
	) );
	$cmb->add_field( array(
		'name' => "قوانین و مقررات",
		'desc' => "متن داخل قسمت نظر گذاری در پنل کاربر نمایش داده میشود.",
		'id'   =>  'illegal_text',
		'type' => 'wysiwyg',
		'options' => array(
			'textarea_rows' => 5,
		),
	) );
});
