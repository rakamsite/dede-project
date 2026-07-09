<?php
add_action( 'cmb2_admin_init', function () {
	$cmb    = new_cmb2_box( array(
		'id'           => 'dede_licence',
		'title'        => 'لایسنس',
		'object_types' => array( 'options-page' ),
		'parent_slug'  => 'dede-theme-settings',
		'option_key'   => 'dede_licence_key',
		'menu_title'   => "لایسنس",
	) );
	$cmb->add_field( array(
		'name' => "لایسنس",
		'desc' => "کد لایسنس خود را وارد کنید",
		'id'   =>  'licence_key_input',
		'type' => 'text',
	) );
});
