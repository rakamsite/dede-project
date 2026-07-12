<?php
if (!defined('ABSPATH')) {
    exit;
}

$is_change = 'change' === $mode;
$action = $is_change ? 'dede_store_change_account_type' : 'dede_store_select_account_type';
$submit_id = $is_change ? 'dede_change_account_type_button' : 'select_button';
?>
<div class="dede-account-type"
     data-dede-account-type
     data-account-type-mode="<?php echo esc_attr($mode); ?>"
     data-current-type="<?php echo esc_attr($current_type); ?>"
     data-account-type-action="<?php echo esc_attr($action); ?>">
    <div class="dede-account-type__header">
        <?php if (!$is_change) : ?>
            <span class="dede-account-type__success" aria-hidden="true">✓</span>
            <h2>حساب کاربری شما ایجاد شد</h2>
            <p>نوع حساب را برای خرید و صدور فاکتور مشخص کنید.</p>
        <?php else : ?>
            <h2>تغییر نوع حساب</h2>
            <p>پس از تغییر، اطلاعات اختصاصی نوع جدید را دوباره بررسی و ذخیره کنید.</p>
        <?php endif; ?>
    </div>

    <div class="dede-account-type__options" role="radiogroup" aria-label="نوع حساب کاربری">
        <?php foreach ($options as $value => $option) :
            $selected = $is_change && $current_type === $value;
            ?>
            <button type="button"
                    class="dede-account-type__option <?php echo $selected ? 'is-selected' : ''; ?>"
                    data-account-type="<?php echo esc_attr($value); ?>"
                    role="radio"
                    aria-checked="<?php echo $selected ? 'true' : 'false'; ?>">
                <span class="dede-account-type__icon dede-account-type__icon--<?php echo esc_attr($option['icon']); ?>" aria-hidden="true"></span>
                <span class="dede-account-type__copy">
                    <strong><?php echo esc_html($option['title']); ?></strong>
                    <small><?php echo esc_html($option['description']); ?></small>
                </span>
                <span class="dede-account-type__radio" aria-hidden="true"></span>
                <input name="inputSelect" class="inputSelect" type="radio"
                       value="<?php echo esc_attr($value); ?>" <?php checked($selected); ?> hidden>
            </button>
        <?php endforeach; ?>
    </div>

    <p class="dede-account-type__error" data-account-type-error role="alert"></p>
    <div class="dede-account-type__actions">
        <?php if ($is_change) : ?>
            <button type="button" class="dede-button dede-button--secondary" data-close-account-type>انصراف</button>
        <?php endif; ?>
        <button type="button" id="<?php echo esc_attr($submit_id); ?>"
                class="dede-button dede-button--primary dede-account-type__submit"
                disabled>
            <?php echo $is_change ? 'ثبت نوع حساب جدید' : 'تأیید و ادامه'; ?>
        </button>
    </div>
</div>
