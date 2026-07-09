<?php

namespace classes\abandoned_cart;

use DateTime;

class dede_v2_abandoned_cart
{
    public string $db_name;
    public string $default_sms;

    public function __construct()
    {
        $this->db_name = "abandoned_cart";
        $this->default_sms = <<<HTML
%name% عزیز 
سبد خرید شما با مبلغ %total% در انتظار پرداخت میباشد. جهت جلو گیری از ناموجود شدن محصولات در اسرع وقت خرید خود را نهایی کنید.
[DeDe.ir]
HTML;

    }

    public function run(): void
    {
        $this->abandoned_cart_admin_menu();
        add_action('woocommerce_cart_updated', function () {
            $cart = WC()->cart;
            $total_items = $cart->get_cart_contents_count();
            $total_price = $cart->get_subtotal();
            if ($total_items > 0) {
                $this->update_abandoned_cart_woocommerce($total_price, $total_items);
            }
        }, 9999);
        add_action('woocommerce_checkout_order_processed', function ($order_id, $posted_data, $order) {
            if (is_user_logged_in()) {
                global $wpdb;
                $user_id = get_current_user_id();
                $table_name = $wpdb->prefix . $this->db_name;
                $wpdb->delete(
                    $table_name,
                    ['user_id' => $user_id],
                    ['%d']
                );
            }
        }, 10, 3);
        add_action('cmb2_admin_init', [$this, 'add_custom_settings_to_general']);
        add_action( 'cmb2_save_options-page_fields', function( $cmb_id, $object_id, $updated_fields ) {
            error_log( 'Existing scheduled event cleared: ' . print_r($updated_fields ,true) );
            if ( in_array('hour_after_create' , $updated_fields) ) {
                $hook_name = 'dede_v2_abandoned_sms_timer_hook';
                if ( wp_next_scheduled( $hook_name ) ) {
                    wp_clear_scheduled_hook( $hook_name );
                }
                if ( ! wp_next_scheduled( $hook_name ) ) {
                    wp_schedule_event( time(), 'custom_abandoned_time', $hook_name );
                }
            }
        }, 10, 3 );
    }

    public function create_woocommerce_abandoned_cart_database(): void
    {
        global $wpdb;
        $table_name = $wpdb->prefix . $this->db_name;
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        user_id BIGINT(20) UNSIGNED DEFAULT NULL,
        cart_total BIGINT(12) NOT NULL,
        cart_items_total BIGINT(12) NOT NULL,
        last_updated DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY user_id (user_id)
    ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        $result = dbDelta($sql);

        // لاگ‌گیری برای بررسی خطاها
        if (!empty($result)) {
            error_log('Tables created/updated: ' . print_r($result, true));
        } else {
            error_log('No changes made by dbDelta.');
        }
    }

    function abandoned_cart_admin_menu(): void
    {
        add_action('admin_menu', function () {
            add_menu_page("سبد خرید رهاشده", "سبد خرید رهاشده", "administrator", "abandonedCart");

            add_submenu_page(
                'abandonedCart',
                'سبد خرید رها شده',
                ' سبد خرید رها شده ',
                'administrator',
                'abandonedCart',
                [$this, "abandoned_cart_menu_HTML"]
            );
        });

    }

    function update_abandoned_cart_woocommerce($cart_total, $cart_item_total): void
    {
        if (is_user_logged_in()) {
            global $wpdb;
            $table_name = $wpdb->prefix . $this->db_name;
            $user_id = get_current_user_id();

            $existing_record = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table_name} WHERE user_id = %d", $user_id));

            if ($existing_record) {
                $wpdb->update(
                    $table_name,
                    [
                        'cart_total' => $cart_total,
                        'cart_items_total' => $cart_item_total,

                    ],
                    ['user_id' => $user_id],
                    ['%d', '%d', '%s'],
                    ['%d']
                );
            } else {
                $wpdb->insert(
                    $table_name,
                    [
                        'user_id' => $user_id,
                        'cart_total' => $cart_total,
                        'cart_items_total' => $cart_item_total,
                    ],
                    ['%d', '%d', '%d', '%s']
                );
            }
        }
    }

    /**
     * @throws \Exception
     */
    function findDiffTime($cartTime): string
    {
        $date = new DateTime($cartTime, wp_timezone());
        $now = new DateTime('now', wp_timezone());
        $interval = $date->diff($now);
        $components = [
            'سال' => $interval->y,
            'ماه' => $interval->m,
            'روز' => $interval->d,
            'ساعت' => $interval->h,
            'دقیقه' => $interval->i,
            'ثانیه' => $interval->s,
        ];
        if ($components['سال'] > 0) {
            return implode(', ', array_map(
                fn($key, $value) => "$value $key",
                array_keys($components),
                $components
            ));
        }
        $filtered = array_filter($components, fn($value) => $value > 0);
        return implode(', ', array_map(
            fn($key, $value) => "$value $key",
            array_keys($filtered),
            $filtered
        ));
    }

    /**
     * @throws \Exception
     */
    function abandoned_cart_menu_HTML(): void
    {
        global $wpdb;
        wp_enqueue_script("dede_v2_abandoned_cart", dede_v2_url . "/assets/js/cartAbandoned.js");
        wp_localize_script("dede_v2_abandoned_cart", "admin", ["url" => admin_url("admin-ajax.php")]);
        $table_name = $wpdb->prefix . $this->db_name;
        $abounded_cart = $wpdb->get_results('SELECT * FROM ' . $table_name);
        $cart_html = '';
        foreach ($abounded_cart as $cart) {
            $user = get_user($cart->user_id);
            $user_name = $user->first_name . ' ' . $user->last_name;
            $user_phone_number = $user->user_login;
            $cart_total = wc_price($cart->cart_total);
            $cart_rows = $cart->cart_items_total;
            $cart_last_updated = $this->findDiffTime($cart->last_updated);
            $cart_html .= <<<HTML
<tr>
    <td>{$cart->user_id}</td>
    <td>$user_name</td>
    <td>$user_phone_number</td>
    <td>$cart_total</td>
    <td>$cart_rows</td>
    <td>$cart_last_updated پیش </td>
    <td>
        <button value="{$cart->user_id}" data-total="$cart->cart_total" type="button" class="button-primary widefat send_message_now">ارسال</button>
    </td>
</tr>
HTML;

        }
        echo <<<HTML
<div class="wrap">
    <div class="loading_abandoned_cart" style="position: absolute; top: 0; left: 0.5rem; width: 100%; height:100%; display: none; align-items: center; justify-content: center; background-color: rgba(225,225,225,0.9)">
         <p>لطفا منتظر بمانید</p>
    </div>
    <table class="wp-list-table widefat fixed table-view-list ">
        <thead>
            <th>شماره</th>
            <th>نام کاربر</th>
            <th>شماره کاربر</th>            
            <th>مبلغ سبد خرید</th>
            <th>تعداد محصولات</th>
            <th>آخرید بروز رسانی</th>
            <th style="text-align: center">ارسال پیامک</th>
        </thead>
        <tbody>
            $cart_html
        </tbody>
    </table>
</div>
HTML;
    }

    function add_custom_settings_to_general(): void
    {
        $cmb_options = new_cmb2_box(array(
            'id' => 'abandonedCartSettings',
            'title' => 'تنظیمات',
            'object_types' => array('options-page'),
            'option_key' => 'abandonedCartSettingsOptions',
            'parent_slug' => 'abandonedCart',
            'position' => 10,
        ));

        $cmb_options->add_field(array(
            'name' => 'چند ساعت بعد از ساخت ارسال شود ؟',
            'id' => 'hour_after_create',
            'type' => 'text',
            'default' => 2,
        ));

        $cmb_options->add_field(array(
            'name' => 'متن پیامک',
            'id' => 'sms_text',
            'description' => 'از %name% برای نام و نام خانوادگی و از %total% برای مجموع سبد خرید استفاده کنید.',
            'type' => 'textarea',
            'default' => $this->default_sms
        ));
        $cmb_options->add_field(array(
            'name' => 'آی دی قالب پیامک',
            'id' => 'sms_template_id',
            'type' => 'text',
            'description' => 'در صورتی که از سیستم خدماتی استفاده میکنید ، این فیلد را پر کنید.',
        ));
    }
}

