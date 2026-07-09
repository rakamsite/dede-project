<?php
/**
 * Cart Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.9.0
 */

defined('ABSPATH') || exit;
$discount_total = WC()->cart->get_discount_total();
$total_prices = WC()->cart->get_subtotal();
$cart_discount_percent='';
$wallet_status = cmb2_get_option('wallet_option', 'wallet_active');
$wallet_percentage = cmb2_get_option('wallet_option', 'wallet_percentage');
$wallet_min_checkout = cmb2_get_option('wallet_option', 'wallet_minimum_amount');
$wallet_max_charge = cmb2_get_option('wallet_option', 'wallet_maximum_charge');
$Cart_information_page = cmb2_get_option('Cart_information_page', 'description_under_coupon', 'متن این قسمت را در تنظیمات اضافه کنید');
wp_enqueue_script("single-product-js", dedeTemplate . '/assets/js/singleProduct.js', array('jquery'), '1.0', false,);
wp_enqueue_script("edit-cart-items-js", dedeTemplate . '/ajax/EditCartItems/js/js.js', array('jquery'), '1.0', false,);

get_template_part('template/EditDetailedCartItems');

do_action('woocommerce_before_cart'); ?>
<div class="container mx-auto">
    <form class="woocommerce-cart-form" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
        <?php do_action('woocommerce_before_cart_table'); ?>
        <div class="md:flex md:gap-5 px-3 md:px-0 pt-2">
            <div class="bg-[#F2F2F2] p-3 rounded-lg text-[#525252] mb-5 md:hidden">
                سبد خرید
            </div>
            <table class="table-fixed md:basis-9/12 woocommerce-cart-form__contents md:border-separate md:border-spacing-y-4 h-full">
                <thead class="hidden md:table-header-group ">
                <tr>
                    <th scope="col"
                        class="product-remove py-3 bg-[#F2F2F2] text-[#525252] text-[15px] font-[500] rounded-r-lg">حذف
                    </th>
                    <th scope="col"
                        class="product-name py-3 bg-[#F2F2F2] text-[#525252] text-[15px] font-[500]">کالا
                    </th>
                    <th scope="col"
                        class="product-price py-3 bg-[#F2F2F2] text-[#525252] text-[15px] font-[500]">مقدار
                    </th>
                    <th scope="col"
                        class="product-quantity text-center py-3 bg-[#F2F2F2] text-[#525252] text-[15px] font-[500]">
                        قیمت
                    </th>
                    <th scope="col" colspan="2"
                        class="product-quantity text-center py-3 bg-[#F2F2F2] text-[#525252] text-[15px] font-[500]">
                        عملیات
                    </th>
                </tr>
                </thead>
                <tbody class="text-[14px] font-[700] ">
                <?php do_action('woocommerce_before_cart_contents');
                foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);
                $variation_id = $cart_item['variation_id'];
                $quantity_manager = $cart_item['key'];
                $order_unit_quantity = $cart_item["unit_selected"];
                $order_unit_selected = $cart_item["unit_quantity"];
                $thumbnail_url = get_the_post_thumbnail_url($product_id);
                $min_quantity = $_product->get_meta("minimum_quantity", true);
                $max_quantity = $_product->get_meta("maximum_quantity", true);
                $package_quantity = $_product->get_meta("package_quantity", true);
                $post_type = $_product->get_type();
                if ($post_type === "variation") {
                    $thumbnail_url = get_the_post_thumbnail_url($variation_id);
                }
                $min_quantity = $min_quantity ? $min_quantity : "1";
                $package_quantity = $package_quantity ? $package_quantity : "1";
                $price = "";
                $subtotal = "";
                $quantity_html = "";
                if (empty($min_quantity)) {
                    $min_quantity = 1;
                }
                if (empty($max_quantity)) {
                    $max_quantity = 500;
                }
                if (empty($package_quantity)) {
                    $package_quantity = 1;
                }

                $total_amount = WC()->cart->get_cart_contents_total();
                $total_tax = WC()->cart->get_cart_contents_tax();
                if ($_product->is_on_sale()) {
                    $regular_price = $_product->get_regular_price();
                    $sale_price = $_product->get_sale_price();
                    $discount = ((float)$regular_price - (float)$sale_price) * (int)$cart_item['quantity'];
                    $total = (float)$regular_price * (int)$cart_item['quantity'];
                }
                $cart_discount_percent =(float)$discount_total / (float)$total_prices * 100  ;

                /**
                 * Filter the product name.
                 *
                 * @param string $product_name Name of the product in the cart.
                 * @param array $cart_item The product in the cart.
                 * @param string $cart_item_key Key for the product in the cart.
                 *
                 * @since 2.1.0
                 */
                $product_name = apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key);

                if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) {
                $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
                if (wp_is_mobile()) {
                ?>
                <div class="w-full flex gap-2 py-2 border-b items-center"> <?php
                    $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key);
                    echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        'woocommerce_cart_item_remove_link',
                        sprintf(
                            '<a href="%s" class="remove flex justify-center basis-1/12" aria-label="%s" data-product_id="%s" data-product_sku="%s"><svg width="33" height="33" viewBox="0 0 33 33" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="16.5" cy="16.5" r="16.5"/><rect x="23.7822" y="7.66559" width="2.99892" height="22.7918" rx="1.49946" transform="rotate(45 23.7822 7.66559)" fill="black"/><rect x="25.9028" y="23.7818" width="2.99892" height="22.7918" rx="1.49946" transform="rotate(135 25.9028 23.7818)" fill="black"/></svg></a>',
                            esc_url(wc_get_cart_remove_url($cart_item_key)),
                            /* translators: %s is the product name */
                            esc_attr(sprintf(__('Remove %s from cart', 'woocommerce'), wp_strip_all_tags($product_name))),
                            esc_attr($product_id),
                            esc_attr($_product->get_sku())
                        ),
                        $cart_item_key
                    );
                    if (!$product_permalink) {
                        echo $thumbnail; // PHPCS: XSS ok.
                    } else {
                        printf('<a class="block basis-auto" href="%s"><img class="aspect-square object-cover w-24 h-24 rounded-lg" src="%s" /></a>', esc_url($product_permalink), $thumbnail_url); // PHPCS: XSS ok.
                    }
                    echo "<div class='flex flex-col gap-1 h-full overflow-x-hidden basis-7/12'>";
                    if (!$product_permalink) {
                        echo wp_kses_post($product_name . '&nbsp;');
                    } else {
                        /**
                         * This filter is documented above.
                         *
                         * @since 2.1.0
                         */
                        echo wp_kses_post(apply_filters('woocommerce_cart_item_name', sprintf('<a class="text-[#525252] text-sm inline-flex text-nowrap" href="%s"><p class="truncate">%s(%s)</p></a>', esc_url($product_permalink), $_product->get_name(), $_product->get_sku()), $cart_item, $cart_item_key));
                    }

                    do_action('woocommerce_after_cart_item_name', $cart_item, $cart_item_key);
                    // Meta data.
                    echo wc_get_formatted_cart_item_data($cart_item); // PHPCS: XSS ok.

                    // Backorder notification.
                    if ($_product->backorders_require_notification() && $_product->is_on_backorder($cart_item['quantity'])) {
                        echo wp_kses_post(apply_filters('woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__('Available on backorder', 'woocommerce') . '</p>', $product_id));
                    }
                    ?>
                    <div class="inline-flex text-sm">
                        <p>مقدار: </p><?php echo $order_unit_selected . '&nbsp;' . $order_unit_quantity ?>
                    </div>
                    <div class='h-full text-sm inline-flex flex-wrap items-center gap-2'>
                        <div class=' col-span-2 flex flex-col gap-2 justify-end grow'>
                            <?php echo "<div class='flex w-full'><p class='ml-0.5'>قیمت: </p>" . wc_price($cart_item['line_subtotal']) . "</div>"; ?>
                        </div>
                        <div class="product-quantity w-full bg-[#F2F2F2]text-[#525252] text-center"
                             data-title="<?php esc_attr_e('Quantity', 'woocommerce'); ?>">
                            <button type="button" value="<?php echo $cart_item_key ?>"
                                    class='show_or_edit_order bg-[#D9D9D9] text-[#0058BF] p-1 rounded-lg w-full'>
                                جزئیات / ویرایش
                            </button>
                        </div>
                    </div>
                </div>
        </div>
    <?php } else { ?>
        <tr class="flex items-center gap-1 justify-start md:table-row md:relative woocommerce-cart-form__cart-item <?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>">
            <td scope="row" class="product-remove  md:bg-[#F2F2F2] text-[#525252] rounded-r-lg py-2">
                <?php
                echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    'woocommerce_cart_item_remove_link',
                    sprintf(
                        '<a href="%s" class="remove flex justify-center" aria-label="%s" data-product_id="%s" data-product_sku="%s"><svg class="md:fill-[#E3000F] fill-gray-600" width="33" height="33" viewBox="0 0 33 33" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="16.5" cy="16.5" r="16.5"/><rect x="23.7822" y="7.66559" width="2.99892" height="22.7918" rx="1.49946" transform="rotate(45 23.7822 7.66559)" fill="white"/><rect x="25.9028" y="23.7818" width="2.99892" height="22.7918" rx="1.49946" transform="rotate(135 25.9028 23.7818)" fill="white"/></svg></a>',
                        esc_url(wc_get_cart_remove_url($cart_item_key)),
                        /* translators: %s is the product name */
                        esc_attr(sprintf(__('Remove %s from cart', 'woocommerce'), wp_strip_all_tags($product_name))),
                        esc_attr($product_id),
                        esc_attr($_product->get_sku())
                    ),
                    $cart_item_key
                );
                ?>
            </td>
            <td scope="row"
                class=" flex md:bg-[#F2F2F2] text-sm md:text-base text-[#525252]">
                <?php
                $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key);

                if (!$product_permalink) {
                    echo $thumbnail;
                } else {
                    printf('<a class="h-full h-[80px] w-[80px] py-2 inline-block" href="%s"><img class=" aspect-square h-[80px] rounded-lg" src="%s" /></a>', esc_url($product_permalink), $thumbnail_url); // PHPCS: XSS ok.
                }

                if (!$product_permalink) {
                    echo wp_kses_post($product_name . '&nbsp;');
                } else {
                    /**
                     * This filter is documented above.
                     *
                     * @since 2.1.0
                     */
                    echo wp_kses_post(apply_filters('woocommerce_cart_item_name', sprintf('<a class="text-[#525252] self-center mr-5 flex" href="%s"><p class="truncate">%s</p>(%s)</a>', esc_url($product_permalink), $_product->get_name(), $_product->get_sku()), $cart_item, $cart_item_key));
                }

                do_action('woocommerce_after_cart_item_name', $cart_item, $cart_item_key);
                echo wc_get_formatted_cart_item_data($cart_item);

                ?>
            </td>
            <td scope="row"
                class="product-price hidden md:table-cell md:bg-[#F2F2F2] self-end md:self-auto md:text-[#525252] text-center "
                data-title="<?php esc_attr_e('Price', 'woocommerce'); ?>">
                <?php echo $order_unit_selected . '&nbsp;' . $order_unit_quantity ?>
            </td>
            <td class="product-subtotal  self-end md:self-auto md:bg-[#F2F2F2] truncate hidden md:table-cell md:text-[#525252] text-center "
                data-title="<?php esc_attr_e('Subtotal', 'woocommerce'); ?>">
                <?php
                echo wc_price($cart_item['line_subtotal']);
                ?>
            </td>
            <td scope="row"
                class="product-quantity md:table-cell md:rounded-l-lg self-end md:self-auto md:bg-[#F2F2F2] md:text-[#525252] text-center"
                data-title="<?php esc_attr_e('Quantity', 'woocommerce'); ?>">
                <button type="button" value="<?php echo $cart_item_key ?>"
                        class='show_or_edit_order bg-[#D9D9D9] text-[#0058BF] p-2 rounded-lg'>
                    جزئیات / ویرایش
                </button>
            </td>
        </tr>
        <?php

    }
    }
    }
    ?>
        </tbody>
        <?php do_action('woocommerce_cart_contents'); ?>
        </table>
        <div class="md:basis-3/12 mt-4 overflow-y-auto">
            <table class="p-4 w-full bg-[#E9E9E9] rounded-lg border-separate border-spacing-y-1 shadow-lg">
                <tbody>
                <tr>
                    <td class="border-b-[1px] border-white flex gap-5 justify-between font-[500] text-[17px] p-2">
                        <p class="text-[#525252] ">تعداد ردیف سفارش : </p>
                        <span><?php echo count(WC()->cart->get_cart_contents()); ?> ردیف</span>
                    </td>
                </tr>

                <tr>
                    <td class="border-b-[1px] border-white flex gap-5 justify-between font-[500] text-[17px] p-2">
                        <p class="text-[#525252] ">جمع مبلغ : </p>
                        <span><?php echo wc_price($total_prices) ?></span>
                    </td>
                </tr>

                <tr>
                    <td class="border-b-[1px] border-white flex gap-5 justify-between font-[500] text-[17px] p-2">
                        <p class="text-[#525252] ">تخفیف درصدی : </p>
                        <span><?php echo intval($cart_discount_percent). "%" ?></span>
                    </td>
                </tr>

                <tr>
                    <td class="border-b-[1px] border-white flex gap-5 justify-between font-[500] text-[17px] p-2">
                        <p class="text-[#525252] ">تخفیف ریالی : </p>
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
                    <td class="actions">
                        <?php if (wc_coupons_enabled()) { ?>
                            <div class="coupon flex gap-2">
                                <input type="text" name="coupon_code"
                                       class="input-text text-center text-[18px] font-[500] text-[#525252] p-2 w-1/2 ring-1 ring-[#0058BF] rounded-lg"
                                       id="coupon_code" value=""
                                       placeholder="کد تخفیف"/>
                                <button type="submit"
                                        class="text-center text-[18px] font-[500] text-white bg-[#979797] p-2 w-1/2 focus:bg-[#E3000F] rounded-lg button<?php echo esc_attr(wc_wp_theme_get_element_class_name('button') ? ' ' . wc_wp_theme_get_element_class_name('button') : ''); ?>"
                                        name="apply_coupon"
                                        value="<?php esc_attr_e('Apply coupon', 'woocommerce'); ?>"><?php esc_html_e('Apply coupon', 'woocommerce'); ?></button>
                                <?php do_action('woocommerce_cart_coupon'); ?>
                            </div>
                            <div class="mt-5 mb-2 text-justify">
                                <?php echo($Cart_information_page) ?>
                            </div>
                        <?php } ?>
                        <button type="submit"
                                class="hidden w-full text-center text-[18px] font-[500] text-white bg-[#E3000F] p-2 mt-2 rounded-lg update_cart button<?php echo esc_attr(wc_wp_theme_get_element_class_name('button') ? ' ' . wc_wp_theme_get_element_class_name('button') : ''); ?>"
                                name="update_cart"
                                disabled="false"
                                value="بروز رسانی سبد خرید">
                            <?php esc_html_e('Update cart', 'woocommerce'); ?></button>
                        <?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="w-fit mx-auto mt-5">
                            <svg width="121" height="73" viewBox="0 0 121 73" fill="none"
                                 xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                <rect width="121" height="73" fill="url(#pattern0)"/>
                                <defs>
                                    <pattern id="pattern0" patternContentUnits="objectBoundingBox" width="1"
                                             height="1">
                                        <use xlink:href="#image0_92_128"
                                             transform="matrix(0.005 0 0 0.00828767 0 -0.00140411)"/>
                                    </pattern>
                                    <image id="image0_92_128" width="200" height="121"
                                           xlink:href="data:image/png;base64,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"/>
                                </defs>
                            </svg>
                        </div>
                        <p class="text-[15px] text-center mt-5">کلیه محصولات DeDe.ir با ضمانت شرکت آژاکس عرضه میگردد.<a
                                    href="<?php echo home_url('/warranty') ?>" class="text-blue-600"> اطلاعات بیشتر</a>
                        </p>
                    </td>
                </tr>
                <?php if ($wallet_status) :
                    if ($total_amount > $wallet_min_checkout) {
                        $check_max_charge = ($wallet_percentage / 100) * $total_amount;
                        $charge = ($check_max_charge > $wallet_max_charge) ? $wallet_max_charge : $check_max_charge;
                    } else {
                        $charge = 0;
                    }
                    ?>
                    <tr>
                        <td>
                            <div class="border-dashed border-2 border-[#E3000F] mt-5 rounded-lg p-2 text-center">
                                با پرداخت آنلاین این سفارش ، مبلغ <?php echo wc_price($charge) ?> تحت عنوان اعتبار نقدی به کیف پول شما در سایت واریز خواهد شد.
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <td class="py-4">
                        <?php do_action('woocommerce_proceed_to_checkout'); ?>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </form>
</div>
<script>
    jQuery(document).ready(function ($) {
        let cart_item_manager = $(".cart_item_manager");
        cart_item_manager.on('click tap', function () {
            $(".update_cart").attr("disabled", false)
        })
    });
</script>