<div id="orders" class="w-full text-[15px] font-[500] <?php echo $_GET['order'] ?? 'hidden' ?>">
    <?php if (! empty( $_GET['order'] )) : ?>
        <?php get_template_part( 'woocommerce/myaccount/orderInfo' ); ?>
    <?php else: ?>
    <div class="grid grid-cols-4 text-[#525252] p-3 bg-[#F2F2F2] rounded-lg justify-items-center ">
        <div class="">کد سفارش</div>
        <div class=""> تاریخ</div>
        <div class="">مبلغ کل</div>
        <div class="">جزئیات سفارش</div>
    </div>
    <div class="divide-y mt-5">
		<?php

		$args = array(
			'status'       => 'completed',
			'numberposts'  => - 1,
			'post_type'    => 'shop_order',
			'orderby'      => 'date',
			'order'        => 'DESC',
			'customer'     => get_current_user_id(),
		);

		$orders = wc_get_orders( $args );
		if ( ! empty( $orders ) ):
			foreach ( $orders as $order ) :
				?>
                <div class="grid grid-cols-4 justify-items-center items-center py-3">
                    <div class=""><?php echo $order->get_order_number() ?></div>
                    <div class="wallet_add_amount"><?php echo $order->get_date_created()->format( 'Y-m-d ' ); ?></div>
                    <div class=""><?php echo wc_price( $order->get_total() ) ?></div>
                    <div class="justify-self-stretch px-2">
                        <a href="<?php echo add_query_arg( [ 'page' => 'orders', 'order' => $order->get_id() ], '' ) ?>"
                           class="p-2 rounded-lg text-white bg-[#E3000F] w-full block text-center">
                            مشاهده
                        </a>
                    </div>
                </div>
			<?php
			endforeach;
		else:
			echo "هنوز سفارشی ثبت نکرده اید.";
		endif;
		endif; ?>
    </div>
</div>