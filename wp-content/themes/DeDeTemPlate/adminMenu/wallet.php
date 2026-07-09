<?php
add_action( 'cmb2_admin_init', function () {
	$prefix = 'wallet_';
	$cmb    = new_cmb2_box( array(
		'id'           => 'wallet_metabox',
		'title'        => 'تنظیمبات کیف پول',
		'object_types' => array( 'options-page' ),
		'parent_slug'  => 'dede-theme-settings', // Make options page a submenu item of the themes menu.
		'option_key'   => 'wallet_option', // The option key and admin menu page slug.
		'menu_title'   => "تنظیمات کیف پول", // Falls back to 'title' (above).
	) );

	$cmb->add_field( array(
		'name' => __( 'فعال بود', 'text-domain' ),
		'desc' => __( 'فعال یا غیرفعال بودن کیف پول را انتخاب کنید.', 'text-domain' ),
		'id'   => $prefix . 'active',
		'type' => 'checkbox',
	) );

	$cmb->add_field( array(
		'name' => __( 'درصد', 'text-domain' ),
		'desc' => __( 'درصد کیف پول را وارد کنید.', 'text-domain' ),
		'id'   => $prefix . 'percentage',
		'type' => 'text',
	) );
	$cmb->add_field( array(
		'name' => __( 'حداعقل میزان خرید', 'text-domain' ),
		'desc' => __( 'حداقل میزان خریدی که باید انجام شود تا درصد به کیف پول اضافه شود.', 'text-domain' ),
		'id'   => $prefix . 'minimum_amount',
		'type' => 'text',
	) );
	$cmb->add_field( array(
		'name' => __( 'حداکثر شارژ کیف پول', 'text-domain' ),
		'desc' => __( 'حد اکثر میزان شارژ کیف پول بابت هر خرید .', 'text-domain' ),
		'id'   => $prefix . 'maximum_charge',
		'type' => 'text',
	) );
	$cmb->add_field( array(
		'name' => __( 'اعتبار به ازای خرید', 'text-domain' ),
		'desc' => __( 'حداکثرر میزان استفاده از کیف پول به ازای خرید', 'text-domain' ),
		'id'   => $prefix . 'credit_per_purchase',
		'type' => 'text',
        'default'=>'0.2'
	) );
} );
