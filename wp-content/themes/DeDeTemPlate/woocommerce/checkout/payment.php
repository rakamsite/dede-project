<?php
/**
 * Checkout Payment Section
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/payment.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.8.0
 */

defined('ABSPATH') || exit;

if (!wp_doing_ajax()) {
    do_action('woocommerce_review_order_before_payment');
}
?>
    <div
            id="checkPaymentId"
            tabindex="-1"
            aria-hidden="true"
            class="fixed left-0 right-0 top-0 z-50 hidden h-[calc(100%-1rem)] max-h-full w-full overflow-y-auto overflow-x-hidden p-4 md:inset-0"
    >
        <div class="relative max-h-full w-full max-w-2xl">
            <div class="relative rounded-lg bg-white shadow-sm">
                <div
                        class="flex items-start justify-between rounded-t border-b p-5"
                >
                    <h3
                            class="text-xl font-semibold text-gray-900 lg:text-2xl"
                    >
                        شناسه پرداخت
                    </h3>
                    <button
                            type="button"
                            class="ms-auto inline-flex h-8 w-8 items-center justify-center rounded-lg bg-transparent text-sm text-gray-400 hover:bg-gray-200 hover:text-gray-900"
                    >
                        <svg
                                class="h-3 w-3"
                                aria-hidden="true"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 14 14"
                        >
                            <path
                                    stroke="currentColor"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"
                            />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <div class="space-y-6 p-6">
                    <p
                            class="text-base leading-relaxed text-gray-500 "
                    >
                        شناسه پرداخت نادرست است. لطفا شناسه صحیح را وارد کنید یا از طریق درگاه آنلاین پرداخت را انجام دهید
                    </p>

                </div>
            </div>
        </div>
    </div>
    <div id="payment" class="woocommerce-checkout-payment">
        <?php if (WC()->cart->needs_payment()) : ?>
            <ul class="wc_payment_methods payment_methods methods">
                <?php
                $total_cart = WC()->cart->get_subtotal();
                $calculated_wallet_discount_code = get_user_meta(get_current_user_id(), '_dede_calculated_wallet_discount_code', true);
                $CardCondition = cmb2_get_option('buy_condition_option_key', 'buy_condition_value');
                if (!empty($available_gateways)) {
                    $payment_index = 0;
                    foreach ($available_gateways as $gateway) {
                        if ($gateway->id === 'bacs' && $total_cart <= $CardCondition) {
                            continue;
                        }
                        if ($payment_index != 0 && !empty($calculated_wallet_discount_code)){
                            continue;
                        }
                        wc_get_template('checkout/payment-method.php', array('gateway' => $gateway));
                        $payment_index++;
                    }
                } else {
                    echo '<li>';
                    wc_print_notice(apply_filters('woocommerce_no_available_payment_methods_message', WC()->customer->get_billing_country() ? esc_html__('Sorry, it seems that there are no available payment methods for your state. Please contact us if you require assistance or wish to make alternate arrangements.', 'woocommerce') : esc_html__('Please fill in your details above to see available payment methods.', 'woocommerce')), 'notice'); // phpcs:ignore WooCommerce.Commenting.CommentHooks.MissingHookComment
                    echo '</li>';
                }
                ?>
            </ul>
        <?php endif; ?>
        <div class="form-row place-order mt-2 leading-relaxed text-justify">
            <noscript>
                <?php
                /* translators: $1 and $2 opening and closing emphasis tags respectively */
                printf(esc_html__('Since your browser does not support JavaScript, or it is disabled, please ensure you click the %1$sUpdate Totals%2$s button before placing your order. You may be charged more than the amount stated above if you fail to do so.', 'woocommerce'), '<em>', '</em>');
                ?>
                <br/>
                <button type="submit"
                        class="button alt<?php echo esc_attr(wc_wp_theme_get_element_class_name('button') ? ' ' . wc_wp_theme_get_element_class_name('button') : ''); ?>"
                        name="woocommerce_checkout_update_totals"
                        value="<?php esc_attr_e('Update totals', 'woocommerce'); ?>"><?php esc_html_e('Update totals', 'woocommerce'); ?></button>
            </noscript>

            <?php wc_get_template('checkout/terms.php'); ?>

            <?php do_action('woocommerce_review_order_before_submit'); ?>

            <?php do_action('woocommerce_review_order_after_submit'); ?>

            <?php wp_nonce_field('woocommerce-process_checkout', 'woocommerce-process-checkout-nonce'); ?>
        </div>
    </div>
<?php
if (!wp_doing_ajax()) {
    do_action('woocommerce_review_order_after_payment');
}
