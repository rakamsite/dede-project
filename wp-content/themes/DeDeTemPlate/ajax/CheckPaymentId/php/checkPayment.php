<?php
add_action('wp_ajax_check_payment_id', 'ajax_checkPayment');
add_action('wp_ajax_nopriv_check_payment_id', 'ajax_checkPayment');
function ajax_checkPayment(): void
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $payment_id = get_option('buy_condition_option_key')['buy_condition_payment_id'];
        $checkout_payment_id = $_POST['payment_id'];
        if ($checkout_payment_id == $payment_id) {
            wp_send_json_success();
        }else{
            wp_send_json_error();
        }
    }
}