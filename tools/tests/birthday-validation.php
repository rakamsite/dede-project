<?php

define('ABSPATH', __DIR__ . '/');
define('DEDE_STORE_FEATURES_PATH', dirname(__DIR__, 2) . '/wp-content/plugins/dede-store-features/');
define('DEDE_STORE_FEATURES_URL', 'https://example.test/');
define('DEDE_STORE_FEATURES_VERSION', 'test');

class WP_Error
{
    private $code;
    private $message;

    public function __construct($code, $message)
    {
        $this->code = $code;
        $this->message = $message;
    }

    public function get_error_code()
    {
        return $this->code;
    }

    public function get_error_message()
    {
        return $this->message;
    }
}

function absint($value)
{
    return abs((int) $value);
}

function wp_timezone()
{
    return new DateTimeZone('Asia/Tehran');
}

function current_datetime()
{
    return new DateTimeImmutable('2026-07-13 12:00:00', wp_timezone());
}

function add_action()
{
}

require DEDE_STORE_FEATURES_PATH . 'includes/dede-store-features-birthday.php';

$cutoff = dede_store_features_birthday_gregorian_to_jalali(2011, 7, 13);
$too_young = dede_store_features_birthday_gregorian_to_jalali(2011, 7, 14);
$adult = dede_store_features_birthday_gregorian_to_jalali(1990, 1, 1);

$result = dede_store_features_validate_birthday_age($cutoff[0], $cutoff[1], $cutoff[2], current_datetime());
if ($result instanceof WP_Error) {
    fwrite(STDERR, "Exactly 15 years old must be accepted.\n");
    exit(1);
}

$result = dede_store_features_validate_birthday_age($too_young[0], $too_young[1], $too_young[2], current_datetime());
if (!($result instanceof WP_Error) || 'under_age' !== $result->get_error_code()) {
    fwrite(STDERR, "A user younger than 15 was accepted.\n");
    exit(1);
}

$result = dede_store_features_validate_birthday_age($adult[0], $adult[1], $adult[2], current_datetime());
if ($result instanceof WP_Error) {
    fwrite(STDERR, "A valid adult birthday was rejected.\n");
    exit(1);
}

$result = dede_store_features_validate_birthday_age('', '', '', current_datetime());
if (true !== $result) {
    fwrite(STDERR, "Optional empty birthday must remain valid.\n");
    exit(1);
}

echo "Birthday age validation passed.\n";
