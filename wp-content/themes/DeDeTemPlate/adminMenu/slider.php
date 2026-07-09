<?php
add_action('cmb2_admin_init', function () {
	$cmb_options = new_cmb2_box(array(
		'id' => 'dede_slider',
		'title' => "تصویر هدر",
		'object_types' => array('options-page'),
		'option_key' => 'DeDeSlder', // The option key and admin menu page slug.
		'menu_title' => "تصویر هدر", // Falls back to 'title' (above).
		'parent_slug' => 'dede-theme-settings', // Make options page a submenu item of the themes menu.
	));
	$group_field_id = $cmb_options->add_field( array(
		'id'          => 'DeDeSliderPage',
		'type'        => 'group',
		'description' => 'تصویر هدر',
        'repeatable'  => false,
	) );
	$cmb_options->add_group_field( $group_field_id, array(
		'name' => 'افزودن تصویر',
		'id'   => 'DeDe_header_slider_image',
		'type' => 'file',
	) );
	$cmb_options->add_group_field( $group_field_id, array(
		'name' => 'افزودن تصویر(نسخه موبایل)',
		'id'   => 'DeDe_header_slider_image_mobile',
		'type' => 'file',
	) );

	$cmb_options->add_group_field( $group_field_id, array(
		'name' => 'لینک',
		'id'   => 'DeDe_header_slider_url',
		'type' => 'text_url',
		'protocols' => array( 'http', 'https'), // Array of allowed protocols
	) );
});
