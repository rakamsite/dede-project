<?php
/**
 * Pay for order form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-pay.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.8.0
 */

defined( 'ABSPATH' ) || exit;

$totals = $order->get_order_item_totals(); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
?>
<form id="order_review" method="post">

	<table class="shop_table container mx-auto border-separate border-spacing-y-4 h-full">
		<thead>
			<tr>
				<th class="product-name py-3 bg-[#F2F2F2] text-[#525252] text-[15px] font-[500] rounded-r-lg"><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
				<th class="product-quantity py-3 bg-[#F2F2F2] text-[#525252] text-[15px] font-[500]"><?php esc_html_e( 'Qty', 'woocommerce' ); ?></th>
				<th class="product-total py-3 bg-[#F2F2F2] text-[#525252] text-[15px] font-[500] rounded-l-lg"><?php esc_html_e( 'Totals', 'woocommerce' ); ?></th>
			</tr>
		</thead>
		<tbody class="text-[14px] font-[700]">
			<?php if ( count( $order->get_items() ) > 0 ) : ?>
				<?php foreach ( $order->get_items() as $item_id => $item ) : ?>
					<?php
					if ( ! apply_filters( 'woocommerce_order_item_visible', true, $item ) ) {
						continue;
					}
					?>
					<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_order_item_class', 'order_item', $item, $order ) ); ?>">
						<td class="product-name  flex bg-[#F2F2F2] text-[#525252] py-1">
							<?php
								echo wp_kses_post( apply_filters( 'woocommerce_order_item_name', $item->get_name(), $item, false ) );

								do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order, false );

								wc_display_item_meta( $item );

								do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order, false );
							?>
						</td>
						<td class="product-quantity flex bg-[#F2F2F2] text-[#525252] py-1"><?php echo apply_filters( 'woocommerce_order_item_quantity_html', ' <strong class="product-quantity">' . sprintf( '&times;&nbsp;%s', esc_html( $item->get_quantity() ) ) . '</strong>', $item ); ?></td><?php // @codingStandardsIgnoreLine ?>
						<td class="product-subtotal flex bg-[#F2F2F2] text-[#525252] py-1"><?php echo $order->get_formatted_line_subtotal( $item ); ?></td><?php // @codingStandardsIgnoreLine ?>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
    </table>
    <div class="grid grid-cols-3 container mx-auto bg-[#F2F2F2] text-[#525252] text-[15px] font-[700] py-3">
			<?php if ( $totals ) : ?>
				<?php foreach ( $totals as $total ) : ?>
					<div class="space-y-3 text-center">
						<div><?php echo $total['label']; ?></div><?php // @codingStandardsIgnoreLine ?>
						<div class="product-total"><?php echo $total['value']; ?></div><?php // @codingStandardsIgnoreLine ?>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>

	<div id="payment">
		<?php if ( $order->get_payment_method() === '') : ?>
			<ul class="wc_payment_methods payment_methods methods">
				<?php
				if ( ! empty( $available_gateways ) ) {
					foreach ( $available_gateways as $gateway ) {
						wc_get_template( 'checkout/payment-method.php', array( 'gateway' => $gateway ) );
					}
				} else {
					echo '<li>';
					wc_print_notice( apply_filters( 'woocommerce_no_available_payment_methods_message', esc_html__( 'Sorry, it seems that there are no available payment methods for your location. Please contact us if you require assistance or wish to make alternate arrangements.', 'woocommerce' ) ), 'notice' ); // phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingHookComment
					echo '</li>';
				}
				?>
			</ul>
		<?php endif; ?>
		<div class="form-row container mx-auto flex justify-end">
			<input type="hidden" name="woocommerce_pay" value="1" />

			<?php do_action( 'woocommerce_pay_order_before_submit' ); ?>

			<?php echo apply_filters( 'woocommerce_pay_order_button_html', '<button type="submit" class="button alt flex bg-[#2F2483] rounded-lg text-white p-3 mt-3 " id="place_order" value="' . esc_attr( $order_button_text ) . '" data-value="' . esc_attr( $order_button_text ) . '">' . esc_html( $order_button_text ) . '</button>' ); // @codingStandardsIgnoreLine ?>

			<?php do_action( 'woocommerce_pay_order_after_submit' ); ?>

			<?php wp_nonce_field( 'woocommerce-pay', 'woocommerce-pay-nonce' ); ?>
		</div>
	</div>
</form>
