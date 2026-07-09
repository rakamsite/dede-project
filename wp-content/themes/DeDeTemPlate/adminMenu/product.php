<?php
add_action( 'cmb2_admin_init', function () {
	$cmb    = new_cmb2_box( array(
		'id'           => 'product_section',
		'title'        => 'تنظیملت محصولات',
		'object_types' => array( 'options-page' ),
		'parent_slug'  => 'dede-theme-settings',
		'option_key'   => 'product_section_page',
		'menu_title'   => "تنظیمات محصولات",
	) );
	$cmb->add_field( array(
		'name' => 'قسمت مربوط به ارسال رایگان',
		'id' => "product_under_title",
		'type' => 'wysiwyg',
		'options' => array(
			'textarea_rows' => 5,
		),
	) );
});
