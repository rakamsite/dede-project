<?php
add_action( 'widgets_init', function (){
	register_sidebar( array(
		'name'          => 'فوتر سمت چپ جایگاه نماد ها',
		'id'            => 'footer-first-element-left',
		'description'   => 'فوتر سمت چپ جایگاه نماد ها',
		'before_widget' => '',
		'after_widget'  => '',
		'before_title'  => '',
		'after_title'   => '',

	) );
	register_sidebar( array(
		'name'          => 'فوتر انتهای سایت سمت راست',
		'id'            => 'footer-last-element-right',
		'description'   => 'فوتر آخر سایت ',
		'before_widget' => '',
		'after_widget'  => '',
		'before_title'  => '',
		'after_title'   => '',
	) );

	register_sidebar( array(
		'name'          => 'فوتر انتهای سایت سمت چپ',
		'id'            => 'footer-last-element-left',
		'description'   => 'فوتر آخر سایت ',
		'before_widget' => '',
		'after_widget'  => '',
		'before_title'  => '',
		'after_title'   => '',
	) );

} );