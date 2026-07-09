<?php
add_action('wp_ajax_discount_wallet_calculation', 'discount_wallet_calculation_callback');
add_action('wp_ajax_nopriv_discount_wallet_calculation', 'discount_wallet_calculation_callback');
add_action('dede_calculate_discount_per_perches', 'calculate_discount_per_perches_show_price',10,2);

function discount_wallet_calculation_callback(): void
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $checked = $_POST['checked'];
        $user = wp_get_current_user();
        $user_id = $user->ID;
        $cart = WC()->cart;
        $discount_amount = calculate_discount_per_perches($cart->get_subtotal(), $user_id);
        $discount_code = $user->user_login . '_' . $discount_amount;
        if ($checked=='true') {
            $check_discount = check_and_create_discount_code($user_id, $discount_code, $discount_amount);
            if ($check_discount || $check_discount == $discount_code) {
                update_user_meta($user_id, '_dede_calculated_wallet_discount_code', $discount_code);
                $cart->add_discount($discount_code);
                wp_send_json_success();
            }
        } else {
            $cart->remove_coupon($discount_code);
            delete_user_meta($user_id, '_dede_calculated_wallet_discount_code');
        }
    }
}

function calculate_discount_per_perches($cart_total, $user_id): int|float
{
    $wallet_charge = get_user_meta($user_id, '_dede_wallet_amount_', true);
    $credit_per_purchase = cmb2_get_option('wallet_option', 'wallet_credit_per_purchase', 0.2);
    $cpp_percent = (int)$wallet_charge / (int)$cart_total;
    if ($cpp_percent <= $credit_per_purchase) {
        $total_discount = $wallet_charge;
    } else {
        $total_discount = $credit_per_purchase * $cart_total;
    }
    return (int)$total_discount;
}

function calculate_discount_per_perches_show_price($cart_total, $user_id): void
{
    $wallet_charge = get_user_meta($user_id, '_dede_wallet_amount_', true);
    $credit_per_purchase = cmb2_get_option('wallet_option', 'wallet_credit_per_purchase', 0.2);
    $cpp_percent = (int)$wallet_charge / (int)$cart_total;
    if ($cpp_percent <= $credit_per_purchase) {
        $total_discount = $wallet_charge;
    } else {
        $total_discount = $credit_per_purchase * $cart_total;
    }
    echo wc_price($total_discount);
}

function check_and_create_discount_code($user_id, $discount_code, $discount_amount): bool|string
{
    $coupon = new WC_Coupon($discount_code);
    if ($coupon->get_id() === 0) {
        return create_new_discount_code($user_id, $discount_code, $discount_amount);
    }
    $usage_count = $coupon->get_usage_count();
    $usage_limit = $coupon->get_usage_limit();
    $expiry_date = $coupon->get_date_expires();

    if ($expiry_date) {
        $current_date = new DateTime();
        if ($expiry_date < $current_date) {
            wp_delete_post($coupon->get_id(), true);
            return create_new_discount_code($user_id, $discount_code, $discount_amount);
        }
    }
    if ($usage_count < $usage_limit) {
        return true;
    } else {
        return create_new_discount_code($user_id, $discount_code, $discount_amount);
    }
}

function create_new_discount_code($user_id, $new_code, $amount): string
{

    $coupon = new WC_Coupon();
    $coupon->set_code($new_code);
    $coupon->set_discount_type('fixed_cart');
    $coupon->set_amount($amount);
    $coupon->set_individual_use(true);
    $coupon->set_usage_limit(1);
    $coupon->set_email_restrictions([get_userdata($user_id)->user_email]);
    $coupon->set_date_expires(date('Y-m-d', strtotime('+7 days')));
    $coupon->save();
    return $new_code;
}
