<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="dede-account-type" data-dede-account-type>
    <div class="dede-account-type__header">
        <span class="dede-account-type__success" aria-hidden="true">✓</span>
        <h2>حساب کاربری شما ایجاد گردید</h2>
        <p>لطفاً نوع حساب کاربری خود جهت خرید و دریافت فاکتور را مشخص نمایید.</p>
    </div>

    <div class="dede-account-type__options" role="radiogroup" aria-label="نوع حساب کاربری">
        <?php foreach ($options as $value => $option) : ?>
            <button type="button"
                    class="dede-account-type__option"
                    data-account-type="<?php echo esc_attr($value); ?>"
                    role="radio"
                    aria-checked="false">
                <span class="dede-account-type__icon dede-account-type__icon--<?php echo esc_attr($option['icon']); ?>" aria-hidden="true"></span>
                <span class="dede-account-type__copy">
                    <strong><?php echo esc_html($option['title']); ?></strong>
                    <small><?php echo esc_html($option['description']); ?></small>
                </span>
                <span class="dede-account-type__radio" aria-hidden="true"></span>
                <input name="inputSelect" class="inputSelect" type="radio" value="<?php echo esc_attr($value); ?>" hidden>
            </button>
        <?php endforeach; ?>
    </div>

    <p class="dede-account-type__error" data-account-type-error role="alert"></p>
    <button type="button" id="select_button" class="dede-button dede-button--primary dede-account-type__submit" disabled>
        تأیید و ادامه
    </button>
</div>
