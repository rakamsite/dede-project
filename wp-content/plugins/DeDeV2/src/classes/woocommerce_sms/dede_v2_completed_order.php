<?php

namespace classes\woocommerce_sms;

use PW\PWSMS\Helper;

class dede_v2_completed_order
{
    function run(): void
    {
        add_action('woocommerce_order_status_completed',[$this , "send_sms_to_user"] ,10 ,1);
    }

    function send_sms_to_user($order_id): void
    {
        $order = wc_get_order($order_id);
        if (!$order){
            return;
        }
        $wallet_charge = $order->get_meta('_wallet_charge_per_order_',true);
        $sms = new Helper();
        $user = $order->get_user();
        $name = $user->first_name .' ' .$user->last_name;
        $phone_number= $user->user_login;
        $service_sms_enabled = cmb2_get_option("sms_settings","enable_sms_service");
        if (!empty($wallet_charge)) {
            if (!$service_sms_enabled){
                $message_template = cmb2_get_option('sms_settings' , 'completed_order_sms_text');
                $message_place = [
                    '%NAME%' => $name,
                    '%ORDER_ID%' => $order_id,
                    '%WALLET_CHARGE%' => $wallet_charge .' '.'ریال'
                ];
                $message = str_replace(array_keys($message_place), array_values($message_place), $message_template);
            }else{
                $message_template_code = cmb2_get_option('sms_settings' , 'completed_order_sms_text_template_order');
                $message ="@$message_template_code@$name;$order_id;$wallet_charge";
            }
        }else{
            $message= " $name عزیز \n" ;
            $message .="سفارش شماره $order_id ثبت شد.";
        }
        $sms->send_sms(['mobile' => $phone_number, 'message' => $message]);

    }
}