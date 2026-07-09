<?php
/**
 * Thankyou page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/thankyou.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.7.0
 */
defined('ABSPATH') || exit;
?>
<div class="woocommerce-order container mx-auto mt-5">

    <?php
    if ($order) :
        do_action('woocommerce_before_thankyou', $order->get_id()); ?>
        <?php if ($order->has_status('failed')) : ?>

        <p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed"><?php esc_html_e('Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'woocommerce'); ?></p>

        <p class="woocommerce-notice woocommerce-notice--error woocommerce-thankyou-order-failed-actions">
            <a href="<?php echo esc_url($order->get_checkout_payment_url()); ?>"
               class="button pay"><?php esc_html_e('Pay', 'woocommerce'); ?></a>
            <?php if (is_user_logged_in()) : ?>
                <a href="<?php echo esc_url(wc_get_page_permalink('myaccount')); ?>"
                   class="button pay"><?php esc_html_e('My account', 'woocommerce'); ?></a>
            <?php endif; ?>
        </p>

    <?php else :
        $order_message = "";
        $order_type = get_post_meta($order->get_id(), '_dede_check_order_type_', true);
        $user_wallet_charge = get_user_meta(get_current_user_id(), '_dede_wallet_amount_', true) ?? 0;
//        do_action("dede_dev2_send_complete_message_hook", $order_id);

        /**
         * @throws WC_Data_Exception
         */
        function create_new_order_with_meta($order_id, $currentCharge)
        {
            $currentCharge = empty($currentCharge) ? 0 : $currentCharge;
            $original_order = wc_get_order($order_id);
            $original_order->update_status('wc-completed');
            $gateways = WC_Payment_Gateways::instance();
            $first_gateway = reset($gateways->payment_gateways);
            $original_payment_method = $original_order->get_payment_method();
            update_post_meta($original_order->get_id(), '_calculated_wallet_amount_', 1);
            $wallet_status = cmb2_get_option('wallet_option', 'wallet_active');
            $wallet_percentage = cmb2_get_option('wallet_option', 'wallet_percentage');
            $wallet_min_checkout = cmb2_get_option('wallet_option', 'wallet_minimum_amount');
            $wallet_max_charge = cmb2_get_option('wallet_option', 'wallet_maximum_charge');
            update_post_meta($order_id, '_dede_check_order_type_', 'added');
            $total_amount = $original_order->get_total();
            $charge = 0;

            if ($wallet_status) {
                $coupon_use = false;
                if ($total_amount > $wallet_min_checkout) {
                    $check_max_charge = ((int)$wallet_percentage / 100) * $total_amount;
                    $charge = ($check_max_charge > $wallet_max_charge) ? $wallet_max_charge : $check_max_charge;
                }
                update_post_meta($original_order->get_id(), '_wallet_charge_per_order_', $charge);
                delete_user_meta($original_order->get_user_id(), '_dede_calculated_wallet_discount_code');
                $coupons = $original_order->get_coupon_codes();

                if (!empty($coupons) && is_array($coupons)) {
                    foreach ($coupons as $coupon_code) {
                        if (str_contains($coupon_code, wp_get_current_user()->user_login)) {
                            $coupon_use = true;
                            $coupon = new WC_Coupon($coupon_code);
                            $new_wallet_charge = $currentCharge - $coupon->get_amount();
                            update_user_meta(get_current_user_id(), '_dede_wallet_amount_', $new_wallet_charge);
                            $charge_order = wc_create_order();
                            $charge_order->update_status('wc-wallet-charge');
                            $charge_order->set_total(-$coupon->get_amount());
                            $charge_order->set_customer_id(get_current_user_id());
                            $charge_order->update_meta_data('_dede_check_order_type_', 'added');
                            $charge_order->update_meta_data('_dede_wallet_amount_status_', 'استفاده از کیف پول ');
                            $charge_order->update_meta_data('_dede_parent_order_', $order_id);
                            $charge_order->save();
                        }
                    }
                }
                if ($original_payment_method === $first_gateway->id && !$coupon_use) {
                    $new_order = wc_create_order();
                    $new_order->update_status('wc-wallet-charge');
                    $new_order->set_total($charge);
                    $wallet_charge = $currentCharge + $charge;
                    update_user_meta(get_current_user_id(), '_dede_wallet_amount_', $wallet_charge);
                    $new_order->set_customer_id($original_order->get_customer_id());
                    $new_order->update_meta_data('_dede_check_order_type_', 'added');
                    $new_order->update_meta_data('_dede_wallet_amount_status_', 'خرید از سایت');
                    $new_order->update_meta_data('_dede_parent_order_', $original_order->get_id());
                    $new_order->save();

                    return ' مبلغ ' . wc_price($charge) . ' به کیف پول شما اضافه شد .';
                } else {
                    return "شارژ کیف پول به ازای خرید فقط در صورت پرداخت آنلاین امکان پذیر میباشد.";
                }
            }
        }

        if ($order_type === 'charge_wallet') {
            update_user_meta(get_current_user_id(), '_dede_wallet_amount_', (!empty($user_wallet_charge)) ? $user_wallet_charge : 0 + $order->get_total());
            update_post_meta($order->get_id(), '_dede_wallet_amount_status_', 'افزایش مستقیم');
            update_post_meta($order->get_id(), '_dede_check_order_type_', 'added');
            update_post_meta($order->get_id(), '_calculated_wallet_amount_', true);
            $order->update_status('wc-wallet-charge');
            $order_message = ' مبلغ ' . wc_price($order->get_total()) . ' به کیف پول شما اضافه شد .';
        } elseif ($order_type === "direct_order") {
            $order_message = create_new_order_with_meta($order->get_id(), $user_wallet_charge);
        }
        ?>
        <div class="woocommerce-order-overview woocommerce-thankyou-order-details md:w-1/2 text-gray-900 bg-green-50 border border-gray-200 rounded-lg mx-auto my-10">
            <button type="button"
                    class="relative flex justify-between items-center w-full p-5 rounded-t-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 border-b border-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                     stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class=""> شماره سفارش</p>
                <strong><?php echo $order->get_order_number(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    ?></strong>
            </button>
            <button type="button"
                    class="relative flex justify-between items-center w-full p-5 rounded-t-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 border-b border-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                     stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
                </svg>
                <p>تاریخ</p>
                <strong><?php echo wc_format_datetime($order->get_date_created()); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    ?></strong>
            </button>
            <?php if (is_user_logged_in() && $order->get_user_id() === get_current_user_id() && $order->get_billing_email()) : ?>
                <button type="button"
                        class="relative flex justify-between items-center w-full p-5 rounded-t-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 border-b border-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                         stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round"
                              d="M16.5 12a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zm0 0c0 1.657 1.007 3 2.25 3S21 13.657 21 12a9 9 0 10-2.636 6.364M16.5 12V8.25"/>
                    </svg>
                    <p>ایمیل</p>
                    <strong><?php echo $order->get_billing_email(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
                </button>
            <?php endif; ?>
            <button type="button"
                    class="relative flex justify-between items-center w-full p-5 rounded-t-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 border-b border-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                     stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/>
                </svg>
                <p>قیمت نهایی</p>
                <strong><?php echo $order->get_formatted_order_total(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    ?></strong>
            </button>
            <?php if ($order->get_payment_method_title()) : ?>

                <button type="button"
                        class="relative flex justify-between items-center w-full p-5 rounded-t-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 border-b border-gray-500">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                         stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/>
                    </svg>
                    <p>روش پرداخت</p>
                    <strong><?php echo wp_kses_post($order->get_payment_method_title()); ?></strong>
                </button>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <?php else : ?>

        <p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received"><?php echo apply_filters('woocommerce_thankyou_order_received_text', esc_html__('Thank you. Your order has been received.', 'woocommerce'), null); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>

    <?php endif; ?>

</div>
<div class="w-full flex justify-center items-center p-4">
    <button class="p-2 w-fit bg-[#2F2483] rounded-lg text-white myAccountSelection" value="orders">مشاهده سفارشات
    </button>
</div>
