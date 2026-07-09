<?php

namespace classes\stock_quantity_handler;

use PW\PWSMS\Helper;

class dede_v2_stock_quantity
{
    public string $db_name;
    public function __construct()
    {
        $this->db_name = 'product_stock_subscription';
    }

    function run(): void
    {
        add_action('woocommerce_variation_set_stock', [$this , 'dede_v2_product_update_message'], 999, 1);
    }

    function create_woocommerce_quantity_database(): void
    {
        global $wpdb;
        $table_name = $wpdb->prefix . $this->db_name;
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        user_id INT(5) UNSIGNED DEFAULT NULL,
        productId BIGINT(12) NOT NULL ,
        variationId BIGINT(12) NOT NULL ,
        PRIMARY KEY (id),
        KEY user_id (user_id)
    ) $charset_collate;";
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);

    }

    function dede_v2_product_update_message(\WC_Product $variation): void
    {
        global $wpdb;
        $service_sms_enabled = cmb2_get_option("sms_settings","enable_sms_service");
        $variation_id = $variation->get_id();
        $current_stock = $variation->get_stock_quantity();
        $current_status = $variation->get_stock_status(); // instock, outofstock
        $previous_status = get_post_meta($variation_id, '_previous_stock_status', true);
        if ($previous_status === '') {
            $previous_status = 'outofstock'; // مقدار پیش‌فرض
        }
        if ($previous_status === 'outofstock' && $current_status === 'instock') {
            $sms = new Helper();
            $table_name = $wpdb->prefix . $this->db_name;
            $variation_id = $variation->get_id();
            $subscriptions = $wpdb->get_results(
                $wpdb->prepare("SELECT * FROM $table_name WHERE variationId = %d", $variation_id)
            );
            foreach ($subscriptions as $sub) {
                $user = get_user_by('ID', $sub->user_id);
                if ($user) {
                    $name = $user->first_name . ' ' . $user->last_name;
                    $variation_name = $variation->get_name();
                    $phone_number = $user->user_login;
                    if (!$service_sms_enabled){
                        $message_template = cmb2_get_option('sms_settings' , 'in_stock_sms_message');
                        $message_place = [
                            '%NAME%' => $name,
                            '%VAR_NAME%'=>$variation_name
                        ];
                        $message = str_replace(array_keys($message_place), array_values($message_place), $message_template);
                    }else{
                        $message_template_code = cmb2_get_option('sms_settings' , 'in_stock_sms_text_template_order');
                        $message =str_replace(["%TEMPLATE_CODE%","%NAME%","%VAR_NAME%"],[$message_template_code , $name , $variation_name], "@%TEMPLATE_CODE%@%NAME%;%VAR_NAME%");
                    }
                    $sms->send_sms([
                        'mobile' => $phone_number,
                        'message' => $message
                    ]);
                }
                $wpdb->delete("$table_name", ['id' => $sub->id], ['%d']);
            }
        }
        update_post_meta($variation_id, '_previous_stock_status', $current_status);
    }
}