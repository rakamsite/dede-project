<?php

define('ABSPATH', __DIR__ . '/');
require dirname(__DIR__, 2) . '/wp-content/plugins/dede-store-features/includes/dede-store-features-national-code-guard.php';

$invalid_codes = array(
    '0123456789',
    '۰۱۲۳۴۵۶۷۸۹',
    '1234567890',
    '9876543210',
    '0987654321',
    '1111111111',
);

foreach ($invalid_codes as $code) {
    if (dede_store_features_is_valid_national_code_value($code)) {
        fwrite(STDERR, "Invalid national code accepted: {$code}\n");
        exit(1);
    }
}

$checksum_valid_code = '0013547569';
if (!dede_store_features_is_valid_national_code_value($checksum_valid_code)) {
    fwrite(STDERR, "Checksum-valid national code rejected by validator.\n");
    exit(1);
}

echo "National code validation tests passed.\n";
