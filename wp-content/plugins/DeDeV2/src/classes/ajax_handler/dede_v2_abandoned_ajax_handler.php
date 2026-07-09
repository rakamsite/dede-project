<?php

namespace classes\ajax_handler;

use classes\abandoned_cart\dede_v2_abandoned_cart;
use PW\PWSMS\Helper;

class dede_v2_abandoned_ajax_handler
{

    function run(): void
    {
        add_action('wp_ajax_send_abandoned_cart_message', [$this, 'wc_ajax_send_abandoned_cart_message']);
    }

    function wc_ajax_send_abandoned_cart_message(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $send = new Helper();
            $user_id = $_POST['user_id'];
            $abandoned_cart_total = $_POST['abandoned_cart_total'] . ' ' . 'ریال';
            $user = get_user_by('id', $user_id);
            $user_phone_number = $user->user_login;
            $user_name_family = $user->first_name . ' ' . $user->last_name;
            $sms_text = cmb2_get_option('abandonedCartSettingsOptions', 'sms_text', (new dede_v2_abandoned_cart())->default_sms);
            $data['message'] = str_replace(['%name%', '%total%'], [$user_name_family, $abandoned_cart_total], $sms_text);
            $data['mobile'] = $user_phone_number;
            if ($send->send_sms($data) === true) {
                wp_send_json_success();
            } else {
                wp_send_json_error();
            }
        }
    }
}