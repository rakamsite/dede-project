<?php
$url = "http://127.0.0.1/wp-json/shygun-woocommerce-sync/v1/cron?action=sync_remain_price&key=123@abc";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_MAXREDIRS, 10);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    $error = curl_error($ch);
    error_log("Curl error in sync_remain_price: $error\n", 3, "/home/dedeir/public_html/cron_errors2.log");
}

curl_close($ch);