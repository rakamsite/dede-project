<?php

define('ABSPATH', __DIR__ . '/');
require dirname(__DIR__, 2) . '/wp-content/plugins/dede-store-features/includes/dede-store-features-contact-validation.php';

$valid_postcodes = array(
    '1234567891',
    '۴۵۸۷۶۱۲۳۹۰',
);
foreach ($valid_postcodes as $postcode) {
    if (!dede_store_features_is_valid_postcode_value($postcode)) {
        fwrite(STDERR, "Valid-looking postcode rejected: {$postcode}\n");
        exit(1);
    }
}

$invalid_postcodes = array(
    '1111111111',
    '2222222222',
    '0123456789',
    '1234567890',
    '9876543210',
    '0987654321',
    '1234',
    '22222222222',
);
foreach ($invalid_postcodes as $postcode) {
    if (dede_store_features_is_valid_postcode_value($postcode)) {
        fwrite(STDERR, "Synthetic postcode accepted: {$postcode}\n");
        exit(1);
    }
}

$valid_landlines = array(
    '02148275',
    '۰۶۱۳۲۵۴۷',
    '02144556677',
    '083382745619876',
);
foreach ($valid_landlines as $phone) {
    if (!dede_store_features_is_valid_landline_value($phone)) {
        fwrite(STDERR, "Valid landline rejected: {$phone}\n");
        exit(1);
    }
}

$invalid_landlines = array(
    '0214827',
    '02111111',
    '02112345',
    '09944556677',
    '2144556677',
    '0611234',
);
foreach ($invalid_landlines as $phone) {
    if (dede_store_features_is_valid_landline_value($phone)) {
        fwrite(STDERR, "Invalid landline accepted: {$phone}\n");
        exit(1);
    }
}

if ('06132547' !== dede_store_features_normalize_landline_value('', '61', '32547')) {
    fwrite(STDERR, "Five-digit landline was not normalized to the legacy storage format.\n");
    exit(1);
}

if ('061325478963147' !== dede_store_features_normalize_landline_value('', '61', '325478963147')) {
    fwrite(STDERR, "Long landline was unexpectedly truncated during normalization.\n");
    exit(1);
}

if (31 !== count(dede_store_features_landline_area_codes())) {
    fwrite(STDERR, "Province area-code list is incomplete.\n");
    exit(1);
}

echo "Contact validation tests passed.\n";
