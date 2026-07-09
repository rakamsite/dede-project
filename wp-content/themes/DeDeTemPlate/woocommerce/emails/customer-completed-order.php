<?php
/**
 * Customer completed order email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-completed-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woo.com/document/template-structure/
 * @package WooCommerce\Templates\Emails
 * @version 3.7.0
 */

use dede_dev_admin_menu\dede_dev_woocommerce_order_email;

if (!defined('ABSPATH')) {
    exit;
}
if (!isset($order)) {
    exit();
}
$prefix = (new dede_dev_woocommerce_order_email())->prefix;
$all_options = ["image_id", "description", "copyright", "instagram", "telegram", "facebook", "x", "aparat", "youtube", "linkedin", "product_1", "product_2", "product_3"];
$social_media = [
    "instagram" => '<img style="border-radius:0.5rem;" src="'.dedeTemplate.'/assets/image/instagram.jpg"  >',
    "telegram" => '<img style="border-radius:0.5rem;" src="'.dedeTemplate.'/assets/image/telegram.jpg"  >',
    "facebook" => '<img style="border-radius:0.5rem;" src="'.dedeTemplate.'/assets/image/facebook.jpg"  >',
    "x" => '<img style="border-radius:0.5rem;" src="'.dedeTemplate.'/assets/image/twitter.jpg"  >',
    "aparat" => '<img style="border-radius:0.5rem;" src="'.dedeTemplate.'/assets/image/aparat.jpg"  >',
    "youtube" => '<img style="border-radius:0.5rem;" src="'.dedeTemplate.'/assets/image/youtube.jpg"  >',
    "linkedin" => '<img style="border-radius:0.5rem;" src="'.dedeTemplate.'/assets/image/linkedin.jpg"  >'
];
$options = get_options([
    "{$prefix}_image_id",
    "{$prefix}_description",
    "{$prefix}_copyright",
    "{$prefix}_instagram",
    "{$prefix}_telegram",
    "{$prefix}_facebook",
    "{$prefix}_x",
    "{$prefix}_aparat",
    "{$prefix}_youtube",
    "{$prefix}_linkedin",
    "{$prefix}_product_1",
    "{$prefix}_product_2",
    "{$prefix}_product_3",
]);
$options_combined = array_combine($all_options, array_values($options));
$dede_logo = "";
$suggested_product = "";
$social_media_icons = "";
for ($i = 1; $i <= 3; $i++) {
    $product = new WC_Product($options_combined["product_$i"]);
    $image_url = wp_get_attachment_url($product->get_image_id());
    $product_title = $product->get_title();
    $product_url = $product->get_permalink();
    $suggested_product .= <<<HTML
    <tr>
        <td style="border-top: 1px solid black; text-align: right; padding: 1rem; width: 100px">
            <a href="{$product_url}" target="_blank">
                <img style="aspect-ratio: 1/1 ; width: 100px; display: inline-flex" src="$image_url">
            </a>
        </td>
        <td class="suggestion-title-container">
            <p style="display: inline-flex;">$product_title </p>
        </td>
        <td class="suggestion-button">
            <a style="color:#0058BF;text-decoration: none; " href="$product_url" >اطلاعات بیشتر</a>
        </td>
    </tr>
HTML;

}
if (isset($options_combined['image_id'])) {
    $dede_logo = wp_get_attachment_url($options_combined['image_id']);
} else {
    $dede_logo = dedeTemplate . '/assets/image/site-dede.svg';
}
foreach ($social_media as $key => $value) {
    if (isset($options_combined[$key])) {
        $social_media_icons .= <<<HTML
        <a style="display: inline-block; width: 36px; height: 36px; margin: 2px" href="$options_combined[$key]">
            $value
        </a>
HTML;

    }
}
$emailStyle = dedeTemplate . '/assets/css/EmailStyle.css';
$info_icon_url = dedeTemplate . '/assets/image/material-symbols_info.jpg';
$shopping_icon_url = dedeTemplate . '/assets/image/mdi_shopping.jpg';
$product_icon = dedeTemplate . '/assets/image/solar_box-bold.jpg';
$emailStyleCode = file_get_contents($emailStyle);
$order_number = $order->get_order_number();
$order_date = apply_filters("dede_v2_jalali_date" , null);
$customer_id = $order->get_customer_id();
$get_user = $order->get_user();
$user_shipping_city =  get_user_meta($customer_id, 'city_custom_shipping', true);
$user_shipping_state =get_user_meta($customer_id, 'state_custom_shipping', true);
$user_roll =$get_user->roles;
$full_name = match ($user_roll[0]) {
    "company" => $get_user->get('billing_company'),
    default => $get_user->first_name . ' ' . $get_user->last_name,
};
$order_address = $order->get_billing_address_1('view');
$order_payment = $order->get_payment_method_title('view');
$orders = $order->get_items('line_item');
$order_total = wc_price($order->get_total() - $order->get_total_tax());
$order_subtotal = wc_price($order->get_subtotal());
$order_tax = wc_price($order->get_total_tax());
$order_discount_percent = ((int)$order->get_discount_total('line_item') / (int)$order->get_subtotal() ) * 100 . "%";
$order_discount = wc_price($order->get_discount_total());
$order_including_tax = wc_price($order->get_total());
$orderItemsHtml = "";
$description = wpautop($options_combined["description"]);
foreach ($orders as $item) {
    $product_name = "";
    $product_sku = "";
    $product_total = "";
    $product_main_unit = "";
    $product_image = "";
    $product_price = "";
    $product_quantity = $item->get_quantity();
    $product_data = $item->get_data();
    $product = $item->get_product();
    $product_url= $product->get_permalink();
    if ($product->post_type === "product_variation") {
        $get_variation = $item->get_product();
        $product_name = $get_variation->get_name();
        $product_sku = $get_variation->get_sku();
        $product_total = wc_price($product_data['total']);
        $product_price = wc_price($get_variation->get_price());
        $product_image = wp_get_attachment_url($get_variation->get_image_id());
        $product_main_unit = get_post_meta($get_variation->get_id(), "_dede_main_unit", true);
    }
    $orderItemsHtml .= <<<HTML
    <tr>
        <td style="border-bottom: 1px solid #4B5259;">
            <a href="$product_url">
                <img style="aspect-ratio: 1/1 ; width: 100px" src="$product_image" alt="$product_name">
            </a>
        </td>
        <td style="border-bottom: 1px solid #4B5259;">
            <p style="text-align: right">$product_name</p>
            <p style="text-align: right">کد کالا:$product_sku </p>
            <p style="text-align: right" >فی:$product_price </p>
            <p style="text-align: right" >واحد (اصلی):$product_main_unit </p>
            <div class="mobile-only">
                <p style="text-align: right">تعداد :$product_quantity</p>
                <p style="text-align: right">تخفیف درصدی :$order_discount_percent</p>
                <p style="text-align: right">مبلغ کل :$product_total</p>
            </div>
        </td>
        <td class="desktop-only" style="border-bottom: 1px solid #4B5259;">
            <p style="text-align: center" >$product_quantity</p>
        </td>
        <td class="desktop-only" style="border-bottom: 1px solid #4B5259; text-align: center;">
            <p style="text-align: center">$order_discount_percent</p>
        </td>
        <td class="desktop-only" style="border-bottom: 1px solid #4B5259;">
                <p style="display: inline-block;width:49%;text-align: center" >$product_total </p>
        </td>
    </tr>
HTML;

}
echo <<<HTML
<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <style>
        body {
            width: 80%;
            margin: 0 auto;
            font-family: tahoma,serif;
            background-color: white;
            direction: rtl;
            font-size: 16px
        }
        .customize-width-dede {
          width: 33.33%;
        }
        .mobile-only {
            display: none;
        }           
        .suggestion-title-container {
            border-top: 1px solid black;
            text-align: right;
            width: 65%
        }
        .suggestion-button {
            border-top: 1px solid black;
            text-align: left;
        }
        .copyright-text {
            text-align: right;
        }
        @media only screen and (max-width: 800px) {
          body {
            width: 95% !important;
          }
          
          /* Hide desktop elements */
          .desktop-only {
            display: none !important;
          }
          
          /* Show mobile elements */
          .mobile-only {
            display: block !important;
          }
          
          /* Fix suggestion area layout */
          .suggestion-title-container {
            width: 50% !important;
            text-align: right !important;
          }
          
          .suggestion-button {
            text-align: center !important;
          }
          
          /* Fix copyright alignment */
          .copyright-text {
            text-align: center !important;
          }
          
          /* Ensure table columns hide properly */
          table th.desktop-only,
          table td.desktop-only {
            display: none !important;
          }
          
          /* Make tables fit on mobile */
          table {
            width: 100% !important;
          }
          
          /* Better spacing for mobile content */
          td {
            padding: 8px !important;
          }
        }
        $emailStyleCode
    </style>
</head>
<body>
    <div class="dg-w-full dg-flex dg-items-center dg-justify-center dg-p-5">
        <a href="https://dede.ir">
            <img height="40px;" style="aspect-ratio: 4 / 1 ;" src="$dede_logo" alt="DeDe.ir"/>
        </a>
    </div>
    <div style="background-color: #2F2483; color: white; font-weight: bold; padding: 1rem; border-radius: 0.25rem; ">
        <span style="display: inline-block;width:49%;text-align: right" class="dg-divide-x"> کد سفارش: <strong>$order_number</strong></span>
        <span style="display: inline-block; width: 49%; text-align: left;">تاریخ: <strong>$order_date</strong></span>
    </div>
    <div style="border: 1px solid #2F2483;border-radius: 8px; margin-top:10px;">
        <div style="width: 100% ; text-align: right; margin-top: 0.5rem">
            <img src="$info_icon_url" style="border-radius: 100%;" width="30" height="30" alt="" >
            <p style="font-weight: bold; font-size: 18px; display: inline-block">اطلاعات سفارش</p>
        </div>
        <div style="background-color:#F2F2F2; margin-top: 0.5rem; border-radius: 0.25rem; text-align: right; padding: 1rem;">
            <p><strong> خریدار : </strong><span>$full_name</span></p>
            <p><strong>روش پرداخت : </strong><span>$order_payment</span></p>
            <p><strong>آدرس ارسال سفارش : </strong><span>$user_shipping_state , $user_shipping_city ,$order_address</span></p>
        </div>
    </div>
    <div style="border: 1px solid #2F2483;border-radius: 8px; margin-top:10px;">
        <div style="text-align: right; padding: 1rem;">
            <img src="$shopping_icon_url" width="24" height="27" alt="" >
            <p style="font-size: 20px; font-weight: bold; display: inline-block;">
                جزئیات سفارش
            </p>
        </div>
        <div class="columns">
            <table style="width: 100%; border-collapse: collapse; border-spacing: 0;">
                <thead>
                    <tr>
                        <th style="color:#525252;background-color: #F2F2F2; padding:0.75rem;">تصویر</th>
                        <th style="color:#525252;background-color: #F2F2F2; padding:0.75rem">شرح</th>
                        <th class="desktop-only" style="color:#525252;background-color: #F2F2F2; padding:0.75rem">تعداد</th>
                        <th class="desktop-only" style="color:#525252;background-color: #F2F2F2; padding:0.75rem">تخفیف درصدی</th>
                        <th class="desktop-only" style="color:#525252;background-color: #F2F2F2; padding:0.75rem;">مبلغ</th>
                    </tr>    
                </thead>
                <tbody>
                    $orderItemsHtml
                    <tr>
                        <td class="desktop-only"></td>
                        <td class="desktop-only"></td>
                        <td style="border-bottom:1px solid #4B5259;padding:1rem; text-align: right">جمع مبلغ:</td>
                        <td style="border-bottom:1px solid #4B5259;padding:1rem; text-align: left" colspan="2">$order_subtotal</td>
                    </tr>
                    <tr>
                        <td class="desktop-only"></td>
                        <td class="desktop-only"></td>
                        <td style="border-bottom:1px solid #4B5259;padding:1rem; text-align: right;">جمع تخفیف ریالی:</td>
                        <td style="border-bottom:1px solid #4B5259;padding:1rem; text-align: left" colspan="2">$order_discount</td>
                    </tr>
                    <tr>
                        <td class="desktop-only"></td>
                        <td class="desktop-only"></td>
                        <td style="border-bottom:1px solid #4B5259;padding:1rem; text-align: right;">مالیات بر ارزش افزوده:</td>
                        <td style="border-bottom:1px solid #4B5259;padding:1rem; text-align: left" colspan="2">$order_tax</td>
                    </tr>
                    <tr>
                        <td class="desktop-only"></td>
                        <td class="desktop-only"></td>
                        <td style="padding:1rem; text-align: right;">جمع مبلغ:</td>
                        <td style="padding:1rem; text-align: left" colspan="2">$order_including_tax</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <br/>
    <div style="border: 1px solid #2F2483;border-radius: 8px; margin-top:10px; margin-bottom:10px;">
        <div style="text-align: right; padding: 1rem;">
            <img src="$product_icon" width="24" height="27" alt="" >
            <p style="font-size: 20px; font-weight: bold; display: inline-block;">
                محصولات پیشنهادی
            </p>
        </div>
        <table style="width: 100%; border-collapse: collapse; border-spacing: 0; margin: 1rem 0">
            <tbody>
                $suggested_product
            </tbody>
        </table>
    </div>
    <div style="border: 1px solid black; padding: 1rem; border-radius: 0.5rem;">
        $description
    </div>
    <div style="border: 1px solid black; padding: 0.25rem; border-radius: 0.25rem; margin-top:1rem; margin-bottom: 1rem; "> 
       <div class="copyright-text" style="width: 70%; display: inline-flex; padding: 0 0.5rem; font-size: 12px;">
            $options_combined[copyright]
        </div>
       <div style="width: auto; display: inline-flex; margin: 10px 0;">
           $social_media_icons
        </div>
    </div>

</body>
</html>

HTML;