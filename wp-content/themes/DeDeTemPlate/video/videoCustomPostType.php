<?php
// ثبت پست تایپ جدید
function create_video_playlist_post_type() {
	$labels = array(
		'name'               => 'پلی لیست ویدیو',
		'singular_name'      => 'پلی لیست ویدیو',
		'menu_name'          => 'پلی لیست ویدیو',
		'add_new'            => 'افزودن ویدیو',
		'add_new_item'       => 'افزودن ویدیو جدید',
		'edit_item'          => 'ویرایش ویدیو',
		'new_item'           => 'ویدیو جدید',
		'view_item'          => 'مشاهده ویدیو',
		'search_items'       => 'جستجوی ویدیوها',
		'not_found'          => 'هیچ ویدیویی یافت نشد',
		'not_found_in_trash' => 'هیچ ویدیویی در زباله دان یافت نشد',
		'parent_item_colon'  => '',
	);

	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'video-playlist' ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array( 'title' ),
	);

	register_post_type( 'video_playlist', $args );
}
require get_template_directory(__FILE__).'/video/Video_selection.php';