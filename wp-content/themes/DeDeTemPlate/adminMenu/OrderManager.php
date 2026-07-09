<?php
if (!defined('ABSPATH')) {
    exit;
}

add_action("cmb2_admin_init", function () {
    if (!class_exists('CMB2')) {
        return;
    }

    $cmb = new_cmb2_box(array(
        'id' => 'order_manager_setting_box',
        'title' => 'مدیریت سفارشات',
        'object_types' => array('options-page'),
        'option_key' => 'order_manager_setting',
        'parent_slug' => 'dede-theme-settings',
    ));

    $cmb->add_field(array(
        'name' => 'مدت زمان لفو شدن سفارش',
        'id' => 'cancel_order_time_out',
        'type' => 'text',
        'description' => "مدت زمان مورد نیاز برای لفو شدن سفارش ، زمان بر اساس دقیقه میباشد."
    ));
    $cmb->add_field(array(
        'name' => 'ایمیل برای اطلاع رسانی',
        'id' => 'email_send_order_status',
        'type' => 'text',
        'attributes' => [
            "type" => "email"
        ]
    ));
});

add_action('wp', function () {
    if (!wp_next_scheduled('dede_check_pending_orders_hook')) {
        wp_schedule_event(time(), 'ten_minutes', 'dede_check_pending_orders_hook');
    }
});

add_filter('cron_schedules', function ($schedules) {
    $schedules['ten_minutes'] = array(
        'interval' => 600,
        'display' => __('Every 10 Minutes', 'textdomain')
    );
    return $schedules;
});

add_action('dede_check_pending_orders_hook', 'checking_order_function_dede_template');
function checking_order_function_dede_template(): void
{
    if (!class_exists('WC_Order')) {
        return;
    }

    $options = get_option('order_manager_setting', []);
    $cancel_time = isset($options['cancel_order_time_out']) ? intval($options['cancel_order_time_out']) * 60 : 0;
    $notification_email = $options['email_send_order_status'] ?? '';

    if ($cancel_time <= 0 || empty($notification_email)) {
        return;
    }

    $args = array(
        'status' => 'pending',
        'limit' => -1
    );

    $orders = wc_get_orders($args);
    $current_time = current_time('timestamp');

    foreach ($orders as $order) {
        $order_date = strtotime($order->get_date_created());

        error_log('Order ID: ' . $order->get_id());
        error_log('Order Date Timestamp: ' . $order_date);
        error_log('Current Time: ' . $current_time);
        error_log('Cancel Time (Seconds): ' . $cancel_time);

        if (($current_time - $order_date) > $cancel_time) {
            error_log('Order ' . $order->get_id() . ' will be cancelled.');
            $order->update_status('cancelled', __('Order automatically cancelled due to timeout.', 'textdomain'));
        }
    }
}


// هوک برای ارسال ایمیل هنگام لغو شدن سفارش
add_action('woocommerce_order_status_cancelled', 'send_custom_cancel_email', 10, 2);
/**
 * @throws Exception
 */
function send_custom_cancel_email($order_id, $order): void
{
    if (!$order || !is_a($order, 'WC_Order')) {
        $order = wc_get_order($order_id);
        if (!$order) {
            error_log("❌ سفارش با شناسه #" . $order_id . " یافت نشد.");
            return;
        }
    }

    // دریافت آدرس ایمیل از تنظیمات
    $options = get_option('order_manager_setting', []);
    $recipient_email = $options['email_send_order_status'] ?? '';

    // بررسی معتبر بودن ایمیل
    if (!is_email($recipient_email)) {
        error_log("❌ آدرس ایمیل گیرنده معتبر نیست: " . $recipient_email);
        return;
    }

    $order_datetime = $order->get_date_created();

    if ($order_datetime) {
        $timestamp = $order_datetime->getTimestamp(); // این همیشه میلادیه و وابسته به لوکال نیست
        $jalali_date = date('Y-m-d', $timestamp);     // تولید دستی تاریخ میلادی واقعی

//        $jalali_date = apply_filters('dede_v2_convert_to_jalali', $date_string);
    } else {
        $jalali_date = "نامشخص";
    }

    // تنظیم موضوع ایمیل با تاریخ شمسی
    $subject = "پیگیری فوری: لغو سفارش شماره {$order_id} مورخ {$jalali_date}";
    $subject = "پیگیری فوری: لغو سفارش شمارسفارش لغو شده جهت پیگیری فوری: سفارش شماره {$order_id} مورخ {$jalali_date} (ارسال به آژاکس)";
    

    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: ' . get_option('blogname') . ' <' . get_option('admin_email') . '>'
    );

    // دریافت اطلاعات تکمیلی مشتری
    $billing_name = $order->get_formatted_billing_full_name();
    $order_total = $order->get_formatted_order_total();

    // دریافت شناسه کاربر و نام کاربری (به عنوان شماره تماس)
    $user_id = $order->get_user_id();
    $username = '';

    if ($user_id) {
        $user = get_user_by('id', $user_id);
        if ($user) {
            $username = $user->user_login;
        }
    }

    // اگر کاربر مهمان است (بدون حساب کاربری)
    if (empty($username)) {
        $username = "مهمان (بدون نام کاربری)";
    }

    // دریافت آدرس کامل مشتری
    $address_parts = array(
        $order->get_billing_country(),
        $order->get_billing_state(),
        $order->get_billing_city(),
        $order->get_billing_address_1(),
        $order->get_billing_address_2(),
        $order->get_billing_postcode()
    );

    // حذف بخش‌های خالی و ایجاد آدرس کامل
    $address_parts = array_filter($address_parts);
    $full_address = implode('، ', $address_parts);

    // جدول محصولات سفارش
    $items_table = '<table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
        <thead>
            <tr>
                <th style="border: 1px solid #ddd; padding: 8px; text-align: right;">محصول</th>
                <th style="border: 1px solid #ddd; padding: 8px; text-align: center;">تعداد</th>
                <th style="border: 1px solid #ddd; padding: 8px; text-align: left;">قیمت</th>
            </tr>
        </thead>
        <tbody>';

    foreach ($order->get_items() as $item) {
        $product = $item->get_product();
        $items_table .= '<tr>
            <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">' . $item->get_name() . '</td>
            <td style="border: 1px solid #ddd; padding: 8px; text-align: center;">' . $item->get_quantity() . '</td>
            <td style="border: 1px solid #ddd; padding: 8px; text-align: left;">' . wc_price($item->get_total()) . '</td>
        </tr>';
    }

    $items_table .= '</tbody></table>';

    // ساخت قالب ایمیل
    $message = '<div style="direction: rtl; text-align: right; font-family: Tahoma, Arial, sans-serif;">
        <h2 style="color: #c0392b;">اطلاعیه لغو سفارش</h2>
        <p>سفارش با شماره <strong>#' . $order_id . '</strong> مورخ <strong>' . $jalali_date . '</strong> لغو شده است.</p>
        
        <div style="background-color: #f8f8f8; border: 1px solid #ddd; padding: 15px; margin: 15px 0;">
            <h3>اطلاعات سفارش:</h3>
            <p><strong>تاریخ سفارش:</strong> ' . $jalali_date . '</p>
            <p><strong>نام مشتری:</strong> ' . $billing_name . '</p>
            <p><strong>شماره تماس (نام کاربری):</strong> ' . $username . '</p>
            <p><strong>آدرس:</strong> ' . $full_address . '</p>
            <p><strong>مبلغ کل:</strong> ' . $order_total . '</p>
        </div>
        
        <h3>محصولات سفارش شده:</h3>
        ' . $items_table . '
        
        <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #ddd;">
            <p>این سفارش با موفقیت لغو شده است. لطفاً نسبت به پیگیری و بررسی آن اقدام فرمایید.</p>
            <p style="color: #c0392b;"><strong>توجه:</strong> این ایمیل نیاز به پیگیری فوری دارد.</p>
        </div>
    </div>';

    // ارسال ایمیل با استفاده از تابع وردپرس
    $mail_sent = wp_mail($recipient_email, $subject, $message, $headers);

    if ($mail_sent) {
        error_log("✅ ایمیل لغو سفارش برای شماره #" . $order_id . " با موفقیت ارسال شد.");
        // ثبت یادداشت در سفارش
        $order->add_order_note("ایمیل اطلاع‌رسانی لغو سفارش به {$recipient_email} ارسال شد.");
    } else {
        error_log("❌ ارسال ایمیل لغو سفارش برای شماره #" . $order_id . " ناموفق بود.");
        // ثبت خطا در سفارش
        $order->add_order_note("خطا در ارسال ایمیل اطلاع‌رسانی لغو سفارش به {$recipient_email}.");
    }
}