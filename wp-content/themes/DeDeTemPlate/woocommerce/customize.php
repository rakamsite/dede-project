<?php
add_filter( 'wc_order_statuses', function ( $order_statuses ) {
	$order_statuses['wc-wallet-charge'] = 'شارژ حساب';

	return $order_statuses;
} );

add_action( 'woocommerce_checkout_update_order_meta', function ( $order_id ) {
	if ( ! empty( $_POST['check_out_custom_meta_data'] ) ) {
		update_post_meta( $order_id, '_dede_check_order_type_', 'direct_order' );
	}
} );
add_action( 'add_meta_boxes', function () {
	add_meta_box(
		'product_custom_meta_box',
		'بسته بندی',
		'render_product_custom_meta_box',
		'product',
		'side',
		'high'
	);
} );

function render_product_custom_meta_box( $post ) {

	$custom_value1 = get_post_meta( $post->ID, 'minimum_quantity', true );
	$custom_value2 = get_post_meta( $post->ID, 'maximum_quantity', true );
	$custom_value3 = get_post_meta( $post->ID, 'package_quantity', true );
	$ajax_code = get_post_meta( $post->ID, 'ajax_code', true );
	?>
    <table>
        <tbody>
            <tr>
                <th><label for="_min_continent_">حداعقل:</label></th>
                <td><input type="number" name="_min_continent_" id="_min_continent_"
                           value="<?php echo esc_attr( $custom_value1 ); ?>"/></td>
            </tr>
            <tr>
                <th><label for="_mx_continent_">حد اکثر:</label></th>
                <td>
                    <input type="number" name="_mx_continent_" id="_mx_continent_"
                           value="<?php echo esc_attr( $custom_value2 ); ?>"/></td>
            </tr>
            <tr>
                <th><label for="_package_continent">بسته بندی:</label></th>
                <td><input type="number" name="_package_continent" id="_package_continent"
                           value="<?php echo esc_attr( $custom_value3 ); ?>"/></td>
            </tr>
        </tbody>
    </table>
	<?php
}

add_action( 'save_post', function ( $post_id ) {
	if ( isset( $_POST['_min_continent_'] ) ) {
		update_post_meta( $post_id, 'minimum_quantity', sanitize_text_field( $_POST['_min_continent_'] ) );
	}
	if ( isset( $_POST['_mx_continent_'] ) ) {
		update_post_meta( $post_id, 'maximum_quantity', sanitize_text_field( $_POST['_mx_continent_'] ) );
	}
	if ( isset( $_POST['_package_continent'] ) ) {
		update_post_meta( $post_id, 'package_quantity', sanitize_text_field( $_POST['_package_continent'] ) );
	}
} );

function add_variant_quantity_manager( $loop, $variation_data, $variation ) {
	woocommerce_wp_text_input(
		array(
			'id'          => 'minimum_quantity' . $loop,
			'name'        => 'minimum_quantity[' . $loop . ']',
			'label'       => 'حداقل تعداد',
			'value'       => get_post_meta( $variation->ID, 'minimum_quantity', true ),
			'desc_tip'    => 'true',
			'description' => ' حداقل میزان سفارش'
		)
	);
	woocommerce_wp_text_input(
		array(
			'id'          => 'maximum_quantity_' . $loop,
			'name'        => 'maximum_quantity[' . $loop . ']',
			'label'       => 'حداکثر تعداد',
			'value'       => get_post_meta( $variation->ID, 'maximum_quantity', true ),
			'desc_tip'    => 'true',
			'description' => ' حداکثر میزان سفارش'
		)
	);
	woocommerce_wp_text_input(
		array(
			'id'          => 'package_quantity_' . $loop,
			'name'        => 'package_quantity[' . $loop . ']',
			'label'       => 'بسته بندی',
			'value'       => get_post_meta( $variation->ID, 'package_quantity', true ),
			'desc_tip'    => 'true',
			'description' => 'تعداد هر بسته'
		)
	);
	woocommerce_wp_text_input(
		array(
			'id'          => 'AJAX-Code' . $loop,
			'name'        => 'AJAX-Code[' . $loop . ']',
			'label'       => 'آژاکس کد',
			'value'       => get_post_meta( $variation->ID, 'AJAX-Code', true ),
			'desc_tip'    => 'true',
			'description' => 'کد افزونه شایگان'
		)
	);
}

add_action( 'woocommerce_variation_options', 'add_variant_quantity_manager', 10, 3 );

add_action( 'woocommerce_save_product_variation', function ( $variation_id, $variation ) {
	$minimum_quantity = $_POST['minimum_quantity'][ $variation ];
	if ( ! empty( $minimum_quantity ) ) {
		update_post_meta( $variation_id, 'minimum_quantity', esc_attr( $minimum_quantity ) );
	}

	$maximum_quantity = $_POST['maximum_quantity'][ $variation ];
	if ( ! empty( $maximum_quantity ) ) {
		update_post_meta( $variation_id, 'maximum_quantity', esc_attr( $maximum_quantity ) );
	}

	$package_quantity = $_POST['package_quantity'][ $variation ];
	if ( ! empty( $package_quantity ) ) {
		update_post_meta( $variation_id, 'package_quantity', esc_attr( $package_quantity ) );
	}
}, 10, 2 );

add_action( 'transition_comment_status', function ( $new_status, $old_status, $comment ) {
	if ( $old_status != $new_status ) {
		if ( $new_status == 'approved' ) {
			$user_id   = $comment->user_id;
			$new_order = wc_create_order();
			$new_order->update_status( 'wc-wallet-charge' );
			$currentCharge = get_user_meta( $user_id, '_dede_wallet_amount_', true );
			$currentCharge = ! empty( $currentCharge ) ? $currentCharge : 0;
			$new_order->set_total( 20000 );
			$wallet_charge = $currentCharge + 20000;
			update_user_meta( $user_id, '_dede_wallet_amount_', $wallet_charge );
			$new_order->set_customer_id( $user_id );
			$new_order->update_meta_data( '_dede_check_order_type_', 'added' );
			$new_order->update_meta_data( '_dede_wallet_amount_status_', 'ثبت دیدگاه' );
			$new_order->save();
		}
	}
}, 10, 3 );
