<?php
function custom_guarantee_post_type() {

	$labels = array(
		'name'               => 'گارانتی‌ها',
		'singular_name'      => 'گارانتی',
		'menu_name'          => 'گارانتی‌ها',
		'all_items'          => 'همه گارانتی‌ها',
		'add_new'            => 'افزودن گارانتی',
		'add_new_item'       => 'افزودن گارانتی جدید',
		'edit_item'          => 'ویرایش گارانتی',
		'new_item'           => 'گارانتی جدید',
		'view_item'          => 'مشاهده گارانتی',
		'search_items'       => 'جستجوی گارانتی',
		'not_found'          => 'گارانتی یافت نشد',
		'not_found_in_trash' => 'گارانتی در زباله دان یافت نشد',
	);

	$args = array(
		'label'         => 'گارانتی',
		'labels'        => $labels,
		'description'   => 'گارانتی‌های محصولات',
		'public'        => true,
		'menu_position' => 5,
		'menu_icon'     => 'dashicons-shield',
		'supports'      => array( 'title' ),
		'has_archive'   => false,
		'rewrite'       => array( 'slug' => 'guarantees' ),
	);

	register_post_type( 'guarantee', $args );

}

add_action( 'init', 'custom_guarantee_post_type' );

function status_guaranty_meta_box_callback( $post ) {
	$meta_status      = get_post_meta( $post->ID, '_dede_guarantee_status_', true );
	$guarantee_status = [ "در انتظار برسی", "درحال برسی", "تایید شد", "رد شد" ];
	echo '<label style="width: 100%; display: inline-block; margin-bottom: 10px" for="_dede_guarantee_status_">وضعیت گارانتی:</label>';
	echo '<select name="_dede_guarantee_status_">';
	echo isset( $meta_status ) ? "<option value='$meta_status'>$guarantee_status[$meta_status].</option>" : "<option>وضعیت گارانتی را انتخاب کنید.</option>";
	for ( $i = 0; $i <= count( $guarantee_status ) - 1; $i ++ ) {
		echo "<option value='$i'>$guarantee_status[$i]</option>";
	}
	echo '</select>';
}

function guaranty_data_information_callback( $post ) {
	$order_id      = get_post_meta( $post->ID, '_dede_guaranty_order_', true );
	$product_id    = get_post_meta( $post->ID, '_dede_guaranty_product_', true );
	$product_count = get_post_meta( $post->ID, '_dede_guaranty_product_count_', true );
	$product       = wc_get_product( $product_id );
	?>
	<table class="form-table">
		<tr valign="top">
			<th scope="row">ای دی سفارش</th>
			<td>
				<input style="width: 100%" type="text" value="<?php echo $order_id ?>" />
			</td>
			<th scope="row">آی دی محصول</th>
			<td>
				<input style="width: 100%" type="text" value="<?php echo $product_id ?>" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row">نام محصول</th>
			<td>
				<input style="width: 100%" type="text" value="<?php echo $product->get_name() ?>" />
			</td>
			<th scope="row">تعداد محصول</th>
			<td>
				<input style="width: 100%" type="text" value="<?php echo $product_count ?>" />
			</td>
		</tr>
	</table>
<?php

}

add_action( 'add_meta_boxes', function () {
	add_meta_box(
		'status_guaranty_meta_box',
		'تعیین وضعیت گارانتی',
		'status_guaranty_meta_box_callback',
		'guarantee',
		'side',
		'default'
	);
	add_meta_box(
		'guaranty_data_information',
		'اطلاعات محصول ثبت شده',
		'guaranty_data_information_callback',
		'guarantee',
		'normal',
		'default'
	);
} );

add_action( 'save_post', function ( $post_id ) {
	if ( isset( $_POST['_dede_guarantee_status_'] ) ) {
		$meta_value = sanitize_text_field( $_POST['_dede_guarantee_status_'] );
		update_post_meta( $post_id, '_dede_guarantee_status_', $meta_value );
	}

} );

add_action( 'cmb2_admin_init', function () {
	$cmb = new_cmb2_box( array(
		'id'           => 'guarantee_policy_page',
		'title'        => 'زیرمنوی سفارشی',
		'object_types' => array( 'options-page' ),
		'context'      => 'normal',
		'priority'     => 'default',
	) );
	$cmb->add_field( array(
		'name' => 'متن شرایط گارانتی',
		'id'   => '_dede_guarantee_text_field',
		'type' => 'textarea',
		'desc' => 'این فیلد در صفحه ثبت گارانتی پنل کاربری نشان داده میشود.',
	) );
} );

function custom_submenu_page_callback() {
	echo '<div class="wrap">';
	cmb2_metabox_form( 'guarantee_policy_page', 'guarantee_policy' );
	echo '</div>';
}

add_action( 'admin_menu', function () {
	add_submenu_page(
		'edit.php?post_type=guarantee',
		'متن شرایط گارانتی',
		' شرایط گارانتی',
		'manage_options',
		'guarantee_policy',
		'custom_submenu_page_callback'
	);
} );