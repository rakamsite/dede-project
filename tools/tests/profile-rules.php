<?php

define('ABSPATH', __DIR__ . '/');

function sanitize_key($value)
{
    return strtolower((string) preg_replace('/[^a-z0-9_\-]/i', '', (string) $value));
}

$updated_meta = array();
function update_user_meta($user_id, $key, $value)
{
    global $updated_meta;
    $updated_meta[] = array($user_id, $key, $value);
    return true;
}

require dirname(__DIR__, 2) . '/wp-content/plugins/dede-store-features/includes/dede-store-features-profile-rules.php';

$allowed = array(
    array('personal', 'personal'),
    array('store', 'store'),
    array('company', 'company'),
    array('personal', 'store'),
    array('store', 'personal'),
);

foreach ($allowed as $transition) {
    if (!dede_store_features_can_change_account_type($transition[0], $transition[1])) {
        fwrite(STDERR, "Allowed account transition was rejected: {$transition[0]} -> {$transition[1]}\n");
        exit(1);
    }
}

$blocked = array(
    array('personal', 'company'),
    array('store', 'company'),
    array('company', 'personal'),
    array('company', 'store'),
    array('customer', 'personal'),
);

foreach ($blocked as $transition) {
    if (dede_store_features_can_change_account_type($transition[0], $transition[1])) {
        fwrite(STDERR, "Blocked account transition was accepted: {$transition[0]} -> {$transition[1]}\n");
        exit(1);
    }
}

dede_store_features_copy_company_national_id_meta(3097, 'company', array(
    'national_id' => '14001234567',
));

if (array(3097, '_dede_national_code_', '14001234567') !== ($updated_meta[0] ?? null)) {
    fwrite(STDERR, "Company national ID was not copied to the compatibility national-code meta.\n");
    exit(1);
}

$before = count($updated_meta);
dede_store_features_copy_company_national_id_meta(3097, 'personal', array(
    'national_id' => '14001234567',
));
if ($before !== count($updated_meta)) {
    fwrite(STDERR, "Personal account unexpectedly copied a company national ID.\n");
    exit(1);
}

echo "Profile rule tests passed.\n";
