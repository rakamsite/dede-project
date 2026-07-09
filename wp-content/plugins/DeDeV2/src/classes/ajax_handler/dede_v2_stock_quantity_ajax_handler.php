<?php

namespace classes\ajax_handler;

use classes\stock_quantity_handler\dede_v2_stock_quantity;

class dede_v2_stock_quantity_ajax_handler
{
    public dede_v2_stock_quantity $main_stock_handler;

    public function __construct()
    {
        $this->main_stock_handler = new dede_v2_stock_quantity();
    }

    public function run(): void
    {
        add_action('wp_ajax_nopriv_add_user_to_subscription', [$this, "add_user_to_subscription"]);
        add_action('wp_ajax_add_user_to_subscription', [$this, "add_user_to_subscription"]);
    }

    function add_user_to_subscription(): void
    {
        global $wpdb;
        $table_name = $wpdb->prefix . $this->main_stock_handler->db_name;
        if (is_user_logged_in()) {
            $user_id = get_current_user_id();
            $variation_id = $_POST['variation_id'];
            $product_id = $_POST['product_id'];
            $check_exist = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE user_id = %d AND variationId = %d AND productId = %d", [$user_id, $variation_id, $product_id]));
            if (!$check_exist) {
                $insert_subscription = $wpdb->insert($table_name, [
                    'user_id' => $user_id,
                    'variationId' => $variation_id,
                    'productId' => $product_id
                ], ['%d', '%d', '%d']);
                if (is_wp_error($insert_subscription)) {
                    wp_send_json_error(['msg' => 'مشکلی در ثبت !']);
                } else {
                    wp_send_json_success(['msg' => 'به محض موجود شدن از طریق پیامک به شما اطلاع میدهیم.']);
                }
            } else {
                wp_send_json_success(['msg' => 'به محض موجود شدن از طریق پیامک به شما اطلاع میدهیم.']);
            }
        } else {
            wp_send_json_error(['msg' => 'برای عضویت در خبرنامه، ابتدا وارد شوید.']);
            wp_send_json_error(['msg' => 'برای عضویت در خبرنامه، ابتدا وارد شوید.']);
        }
    }
}