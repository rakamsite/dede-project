<?php

namespace classes\functionality;

use classes\abandoned_cart\dede_v2_abandoned_cart;
use PW\PWSMS\Helper;


class dede_v2_scheduling
{
    public mixed $abandoned_options;

    public function __construct()
    {
        $this->abandoned_options = get_option('abandonedCartSettingsOptions');
    }

    function run(): void
    {
        add_filter('cron_schedules', [$this, 'add_custom_cronjob_timer']);
        add_action('dede_v2_abandoned_sms_timer_hook', [$this, 'dede_v2_abandoned_sms_timer_callback']);
        add_action('init', [$this, 'schedule_custom_time_function']);

    }

    function add_custom_cronjob_timer($schedules)
    {
        $hour_interval = (int)isset($this->abandoned_options['hour_after_create']) ? $this->abandoned_options['hour_after_create'] : 2;
        $second = $hour_interval * 3600;
        $schedules['custom_abandoned_time'] = array(
            'interval' => $second,
            'display' => "هر {$hour_interval} ساعت",
        );
        return $schedules;
    }

    function schedule_custom_time_function(): void
    {
        if (!wp_next_scheduled('dede_v2_abandoned_sms_timer_hook')) {
            wp_schedule_event(time(), 'custom_abandoned_time', 'dede_v2_abandoned_sms_timer_hook');
            error_log('تنظیم شد');
        }
    }

    function dede_v2_abandoned_sms_timer_callback(): void
    {
        global $wpdb;
        $sms = new Helper();
        date_default_timezone_set('Asia/Tehran');
        $database_name = $wpdb->prefix . (new dede_v2_abandoned_cart())->db_name;
        $list = $wpdb->get_results("SELECT * FROM " . $database_name);
        $service_sms_enabled = cmb2_get_option("sms_settings","enable_sms_service");
        foreach ($list as $item) {
            $last_updated = strtotime($item->last_updated);
            $current_time = time();
            $hour_interval = $this->abandoned_options['hour_after_create'] ?? 2;
            $second = $hour_interval * 3600;
            if (($current_time - $last_updated) >= $second) {
                $user_id = $item->user_id;
                $abandoned_cart_total = $item->cart_total . ' ' . 'ریال';
                $user = get_user_by('id', $user_id);
                if (!$user) {
                    error_log('Invalid user ID: ' . $user_id);
                    return;
                }
                $user_phone_number = $user->user_login;
                $user_name_family = $user->first_name . ' ' . $user->last_name;
                $sms_text = $this->abandoned_options['sms_text'] ?? (new dede_v2_abandoned_cart())->default_sms;
                if (!$service_sms_enabled){
                    $data['message'] = str_replace(['%name%', '%total%'], [$user_name_family, $abandoned_cart_total], $sms_text);
                }else{
                    $message_template_code = $this->abandoned_options['sms_template_id'];
                    $message =str_replace(["*","NAME", "TOTAL"],[$message_template_code , $user_name_family, $abandoned_cart_total], "@*@NAME;TOTAL");
                }
    
                $data['mobile'] = $user_phone_number;

                if ($sms->send_sms($data) === true) {
                    $result = $wpdb->delete($database_name, ['user_id' => $item->user_id], ['%d']);
                    if ($result === false) {
                        error_log('Failed to delete user ID: ' . $user_id);
                    }
                } else {
                    error_log('SMS sending failed for user ID: ' . $user_id);
                }
            }
        }
    }
}