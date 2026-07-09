<?php
/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 */
if (!defined('ABSPATH')) {
    exit;
}
do_action('woocommerce_before_checkout_form', $checkout);

$discount_total = WC()->cart->get_discount_total();
$total_prices = WC()->cart->get_subtotal();
$current_user = wp_get_current_user();
$user_role = $current_user->roles;

foreach ($user_role as $role) {
    $national_code_validate = match ($role) {
        "company" => get_user_meta($current_user->ID, '_dede_national_id_', true),
        default => get_user_meta($current_user->ID, '_dede_national_code_', true),
    };
}

$first_name = $current_user->first_name;
$last_name = $current_user->last_name;
$national_code = get_user_meta($current_user->ID, '_dede_national_code_', true);
foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
    $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
    $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);
    $thumbnail_url = get_the_post_thumbnail_url($product_id);
    $total_amount = WC()->cart->get_cart_contents_total();
    $total_tax = WC()->cart->get_cart_contents_tax();

    if ($_product->is_on_sale()) {
        $regular_price = $_product->get_regular_price();
        $sale_price = $_product->get_sale_price();
        $discount = ((float)$regular_price - (float)$sale_price) * (int)$cart_item['quantity'];
        $total = (float)$regular_price * (int)$cart_item['quantity'];
        $discount_total += $discount;
    } else {
    }
}
$CardCondition = cmb2_get_option('buy_condition_option_key', 'buy_condition_value');
$cart_discount_percent = $discount_total / $total_prices * 100;
if (!$checkout->is_registration_enabled() && $checkout->is_registration_required() && !is_user_logged_in()) {
    echo esc_html(apply_filters('woocommerce_checkout_must_be_logged_in_message', __('You must be logged in to checkout.', 'woocommerce')));

    return;
}
$adminPanelLink = "'" . home_url('/my-account') . "'";
$calculated_wallet_discount_code = get_user_meta($current_user->ID, '_dede_calculated_wallet_discount_code', true);
if (!$first_name || !$last_name || empty($national_code_validate)) { ?>
    <div class='fixed w-full h-full top-0 bg-black/80 rounded-lg flex justify-center items-center z-50 '>
        <div class="bg-white rounded-lg w-full md:w-1/3 auto p-10 text-center space-y-1">
            <h3>اطلاعات حساب کاربری شما کامل نیست .</h3>
            <button class="bg-[#2F2483] p-2 text-white rounded-lg forMyAccountButton w-full"
                    data-my-account="Information" onclick="<?php echo 'location.href=' . $adminPanelLink . ';'; ?>">
                تکمیل اطلاعات حساب کاربری
            </button>
        </div>
    </div>
<?php } ?>
<div id="LoadingMyAccount"
     class="absolute w-full h-full bg-black/25 rounded-lg flex justify-center items-center hidden z-50">
    <svg class="animate-spin -ml-1 mr-3 h-32 w-32 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
         viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor"
              d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
    </svg>
</div>
<div id="myAccountNotification" class="fixed top-0 right-5 pt-20 z-50"></div>
<div class="container mx-auto">
    <div class="flex md:flex-row flex-col gap-5 pt-6">
        <div class="basis-9/12 p-4">
            <?php get_template_part('woocommerce/myaccount/Information'); ?>
        </div>
        <div class="md:basis-3/12 px-3 md:px-0 overflow-y-auto">
            <form name="checkout" method="post" class="checkout woocommerce-checkout "
                  action="<?php echo esc_url(wc_get_checkout_url()); ?>" enctype="multipart/form-data">
                <?php if ($checkout->get_checkout_fields()) : ?>
                    <?php do_action('woocommerce_checkout_before_customer_details'); ?>
                    <input type="hidden" name="check_out_custom_meta_data" value="true">
                    <div class="col2-set hidden" id="customer_details">
                        <div class="col-1">
                            <?php do_action('woocommerce_checkout_billing'); ?>
                        </div>

                        <div class="col-2">
                            <?php do_action('woocommerce_checkout_shipping'); ?>
                        </div>
                    </div>
                    <?php do_action('woocommerce_checkout_after_customer_details'); ?>
                <?php endif; ?>

                <table class="p-2 w-full bg-[#E9E9E9] rounded-lg border-separate border-spacing-y-1 shadow-lg ">
                    <tbody>
                    <?php do_action('woocommerce_checkout_before_order_review_heading'); ?>
                    <tr class="border-b-[1px] border-white flex gap-5  justify-between font-[500] text-[17px] p-2">
                        <td>
                            <label for="use-wallet-amount" class="inline-flex items-center justify-between cursor-pointer update_totals_on_change">
                                <input name="use-wallet-amount" id="use-wallet-amount" type="checkbox" <?php echo $total_prices <= $CardCondition ? 'disabled' :'';  ?> <?php echo $calculated_wallet_discount_code !==''  ? 'checked' : '' ?> value="" class="sr-only peer">
                                <div class="relative w-11 h-6 bg-gray-600 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#E3000F]"></div>
                                <span class="ms-3 text-sm font-medium text-gray-900 dark:text-gray-300 peer-checked:text-black">
                                    <span class="text-xs" id="wallet_charge_per_buy"><?php do_action('dede_calculate_discount_per_perches', $total_prices, get_current_user_id()) ?></span>
                                    اعتبار به ازای خرید
                                    <span class="text-xs">(فقط در پرداخت آنلاین)</span>
                                </span>
                            </label>

                        </td>
                    </tr>
                    <tr>
                        <td class="border-b-[1px] border-white flex gap-5  justify-between font-[500] text-[17px] p-2">
                            <p class="text-[#525252] ">تعداد ردیف سفارش : </p>
                            <span><?php echo count(WC()->cart->get_cart_contents()); ?> ردیف</span>
                        </td>
                    </tr>

                    <tr>
                        <td class="border-b-[1px] border-white flex gap-5  justify-between font-[500] text-[17px] p-2">
                            <p class="text-[#525252] ">جمع مبلغ : </p>
                            <span><?php echo wc_price($total_prices) ?></span>
                        </td>
                    </tr>

                    <tr>
                        <td class="border-b-[1px] border-white flex gap-5  justify-between font-[500] text-[17px] p-2">
                            <p class="text-[#525252] ">تخفیف درصدی : </p>
                            <span><?php echo intval($cart_discount_percent) .'%' ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td class="border-b-[1px] border-white flex gap-5 justify-between font-[500] text-[17px] p-2">
                            <p class="text-[#525252] ">جمع تخفیف ریالی : </p>
                            <span><?php echo wc_price($discount_total) ?></span>
                        </td>
                    </tr>

                    <tr>
                        <td class="border-b-[1px] border-white flex gap-5 justify-between font-[500] text-[17px] p-2">
                            <p class="text-[#525252] ">مالیات بر ارزش افزوده : </p>
                            <span><?php echo wc_price($total_tax); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td class="border-b-[1px] border-white flex gap-5 justify-between font-[500] text-[17px] p-2">
                            <p class="text-[#525252] ">مبلغ نهایی : </p>
                            <span><?php echo wc_price($total_amount + $total_tax) ?> </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="pt-4 text-[15px] text-[#525252]">
                            <p class="my-3">یادداشت تکمیلی</p>
                            <textarea id="order_comments" name="order_comments"
                                      class="border-[1px] mt-2 border-black rounded-lg w-full h-32 p-2"></textarea>
                        </td>
                    </tr>
                    <tr>
                        <?php do_action('woocommerce_checkout_before_order_review'); ?>

                        <td class="pt-4 text-[15px] text-[#525252] product-name">
                            <p class="my-5">روش پرداخت</p>
                            <?php do_action('woocommerce_checkout_order_review'); ?>
                        </td>
                        <?php do_action('woocommerce_checkout_after_order_review'); ?>
                    </tr>
                    <tr>
                        <td class="py-5">
                            <div class="grid grid-cols-2 gap-3 text-[18px] font-[500]">
                                <a class="rounded-lg text-white bg-[#E3000F] p-2 text-center"
                                   href="<?php echo wc_get_cart_url() ?>">باز بینی سبد خرید</a>
                                <button type="submit" id="submit_order"
                                        class="rounded-lg text-white bg-[#4B5259] p-2 hover:bg-[#2F2483] focus:bg-[#2F2483] ">
                                    ثبت سفارش
                                </button>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </div>
</div>
<?php do_action('woocommerce_after_checkout_form', $checkout); ?>
