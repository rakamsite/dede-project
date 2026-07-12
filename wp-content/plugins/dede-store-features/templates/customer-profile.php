<?php
if (!defined('ABSPATH')) {
    exit;
}

$years = range($max_birth_year, 1300);
$months = array(
    1 => 'فروردین', 2 => 'اردیبهشت', 3 => 'خرداد', 4 => 'تیر', 5 => 'مرداد', 6 => 'شهریور',
    7 => 'مهر', 8 => 'آبان', 9 => 'آذر', 10 => 'دی', 11 => 'بهمن', 12 => 'اسفند',
);
$identity_title = 'company' === $role ? 'اطلاعات شرکت' : ('store' === $role ? 'اطلاعات فروشگاه و مدیر' : 'اطلاعات شخصی');
$billing_title = 'company' === $role ? 'آدرس محل فعالیت (دفتر مرکزی)' : ('store' === $role ? 'آدرس محل فعالیت' : 'آدرس محل سکونت');
?>
<div id="<?php echo 'account' === $context ? 'Information' : 'dede-checkout-profile'; ?>" class="dede-profile dede-profile--<?php echo esc_attr($context); ?> <?php echo 'account' === $context ? 'hidden' : ''; ?>"
         data-dede-profile
         data-start-step="<?php echo esc_attr($start_step); ?>"
         data-context="<?php echo esc_attr($context); ?>"
         data-complete="<?php echo $is_complete ? '1' : '0'; ?>">
    <header class="dede-profile__header">
        <div>
            <span class="dede-profile__eyebrow">نوع حساب: <?php echo esc_html($role_labels[$role]); ?></span>
            <h2><?php echo 'checkout' === $context ? 'تکمیل اطلاعات خرید' : 'اطلاعات حساب کاربری'; ?></h2>
            <p><?php echo $is_complete ? 'اطلاعات ثبت‌شده را بررسی کنید یا در صورت نیاز ویرایش کنید.' : 'برای ادامه خرید، اطلاعات ضروری را در سه مرحله تکمیل کنید.'; ?></p>
        </div>
        <div class="dede-profile__status <?php echo $is_complete ? 'is-complete' : 'is-incomplete'; ?>" data-profile-status>
            <span class="dede-profile__status-dot"></span>
            <span class="dede-profile__status-text" data-profile-status-text><?php echo $is_complete ? 'اطلاعات کامل است' : 'اطلاعات ناقص است'; ?></span>
        </div>
    </header>

    <nav class="dede-profile__steps" aria-label="مراحل تکمیل اطلاعات">
        <button type="button" data-step-target="1"><span>۱</span><strong>مشخصات</strong></button>
        <i></i>
        <button type="button" data-step-target="2"><span>۲</span><strong>آدرس</strong></button>
        <i></i>
        <button type="button" data-step-target="3"><span>۳</span><strong>تأیید</strong></button>
    </nav>

    <div class="dede-profile__mobile-progress">
        <div><strong data-mobile-step>مرحله <?php echo esc_html($start_step); ?> از ۳</strong><span data-mobile-title></span></div>
        <progress max="3" value="<?php echo esc_attr($start_step); ?>" data-mobile-progress></progress>
    </div>

    <form class="dede-profile__form" novalidate>
        <input type="hidden" name="action" value="dede_store_save_profile">
        <input type="hidden" name="nonce" value="<?php echo esc_attr(wp_create_nonce('dede_store_profile')); ?>">
        <input type="hidden" name="context" value="<?php echo esc_attr($context); ?>">

        <div class="dede-profile__panel" data-step="1">
            <div class="dede-profile__section-heading">
                <span>۱</span>
                <div><h3><?php echo esc_html($identity_title); ?></h3><p>فیلدهای ضروری متناسب با نوع حساب نمایش داده شده‌اند.</p></div>
            </div>

            <div class="dede-profile__grid">
                <?php if ('store' === $role) : ?>
                    <label class="dede-field dede-field--wide">
                        <span>نام فروشگاه <b>ضروری</b></span>
                        <input type="text" name="store_name" value="<?php echo esc_attr($profile['store_name']); ?>" autocomplete="organization" required>
                        <small data-error-for="store_name"></small>
                    </label>
                <?php endif; ?>

                <?php if ('company' === $role) : ?>
                    <label class="dede-field dede-field--wide">
                        <span>نام شرکت <b>ضروری</b></span>
                        <input type="text" name="company_name" value="<?php echo esc_attr($profile['company_name']); ?>" autocomplete="organization" required>
                        <small data-error-for="company_name"></small>
                    </label>
                    <label class="dede-field">
                        <span>شناسه ملی <b>ضروری</b></span>
                        <input type="text" name="national_id" value="<?php echo esc_attr($profile['national_id']); ?>" inputmode="numeric" maxlength="11" autocomplete="off" required>
                        <small data-error-for="national_id"></small>
                    </label>
                <?php else : ?>
                    <label class="dede-field">
                        <span><?php echo 'store' === $role ? 'نام مدیر' : 'نام'; ?> <b>ضروری</b></span>
                        <input type="text" name="first_name" value="<?php echo esc_attr($profile['first_name']); ?>" autocomplete="given-name" required>
                        <small data-error-for="first_name"></small>
                    </label>
                    <label class="dede-field">
                        <span><?php echo 'store' === $role ? 'نام خانوادگی مدیر' : 'نام خانوادگی'; ?> <b>ضروری</b></span>
                        <input type="text" name="last_name" value="<?php echo esc_attr($profile['last_name']); ?>" autocomplete="family-name" required>
                        <small data-error-for="last_name"></small>
                    </label>
                    <label class="dede-field">
                        <span><?php echo 'store' === $role ? 'جنسیت مدیر' : 'جنسیت'; ?> <b>ضروری</b></span>
                        <select name="gender" required>
                            <option value="">انتخاب کنید</option>
                            <option value="male" <?php selected($profile['gender'], 'male'); ?>>آقا</option>
                            <option value="female" <?php selected($profile['gender'], 'female'); ?>>خانم</option>
                        </select>
                        <small data-error-for="gender"></small>
                    </label>
                    <label class="dede-field">
                        <span><?php echo 'store' === $role ? 'کد ملی مدیر' : 'کد ملی'; ?> <b>ضروری</b></span>
                        <input type="text" name="national_code" value="<?php echo esc_attr($profile['national_code']); ?>" inputmode="numeric" maxlength="10" autocomplete="off" required>
                        <small data-error-for="national_code"></small>
                    </label>
                <?php endif; ?>

                <label class="dede-field">
                    <span>شماره همراه <em>تأییدشده</em></span>
                    <input type="text" value="+<?php echo esc_attr($profile['mobile']); ?>" readonly aria-readonly="true">
                    <small data-error-for="mobile"></small>
                </label>
            </div>

            <details class="dede-profile__optional" <?php echo ('company' === $role && ($profile['first_name'] || $profile['last_name'])) ? 'open' : ''; ?>>
                <summary><span>اطلاعات تکمیلی</span><small>اختیاری</small></summary>
                <div class="dede-profile__grid">
                    <?php if ('company' === $role) : ?>
                        <label class="dede-field">
                            <span>نام رابط <em>اختیاری</em></span>
                            <input type="text" name="first_name" value="<?php echo esc_attr($profile['first_name']); ?>" autocomplete="given-name">
                            <small data-error-for="first_name"></small>
                        </label>
                        <label class="dede-field">
                            <span>نام خانوادگی رابط <em>اختیاری</em></span>
                            <input type="text" name="last_name" value="<?php echo esc_attr($profile['last_name']); ?>" autocomplete="family-name">
                            <small data-error-for="last_name"></small>
                        </label>
                        <label class="dede-field">
                            <span>جنسیت رابط <em>اختیاری</em></span>
                            <select name="gender">
                                <option value="">انتخاب نشده</option>
                                <option value="male" <?php selected($profile['gender'], 'male'); ?>>آقا</option>
                                <option value="female" <?php selected($profile['gender'], 'female'); ?>>خانم</option>
                            </select>
                            <small data-error-for="gender"></small>
                        </label>
                        <label class="dede-field">
                            <span>کد اقتصادی <em>اختیاری</em></span>
                            <input type="text" name="economic_code" value="<?php echo esc_attr($profile['economic_code']); ?>" inputmode="numeric">
                            <small data-error-for="economic_code"></small>
                        </label>
                    <?php endif; ?>

                    <label class="dede-field">
                        <span>ایمیل <em>اختیاری</em></span>
                        <input type="email" name="email" value="<?php echo esc_attr($profile['email']); ?>" autocomplete="email">
                        <small data-error-for="email"></small>
                    </label>
                    <label class="dede-field">
                        <span>تلگرام <em>اختیاری</em></span>
                        <input type="tel" name="telegram" value="<?php echo esc_attr($profile['telegram']); ?>" inputmode="tel" placeholder="مثلاً 09121234567">
                        <small data-error-for="telegram"></small>
                    </label>
                    <fieldset class="dede-field dede-field--wide dede-birthday">
                        <legend>تاریخ تولد <em>اختیاری</em></legend>
                        <div>
                            <select name="birthday_day" aria-label="روز تولد"><option value="">روز</option><?php for ($day = 1; $day <= 31; $day++) : ?><option value="<?php echo esc_attr($day); ?>" <?php selected((int) $profile['birthday_day'], $day); ?>><?php echo esc_html($day); ?></option><?php endfor; ?></select>
                            <select name="birthday_month" aria-label="ماه تولد"><option value="">ماه</option><?php foreach ($months as $number => $month) : ?><option value="<?php echo esc_attr($number); ?>" <?php selected((int) $profile['birthday_month'], $number); ?>><?php echo esc_html($month); ?></option><?php endforeach; ?></select>
                            <select name="birthday_year" aria-label="سال تولد"><option value="">سال</option><?php foreach ($years as $year) : ?><option value="<?php echo esc_attr($year); ?>" <?php selected((int) $profile['birthday_year'], $year); ?>><?php echo esc_html($year); ?></option><?php endforeach; ?></select>
                        </div>
                        <small data-error-for="birthday_year"></small>
                    </fieldset>
                </div>
            </details>
        </div>

        <div class="dede-profile__panel" data-step="2">
            <div class="dede-profile__section-heading">
                <span>۲</span>
                <div><h3>اطلاعات آدرس</h3><p>آدرس اصلی و محل ارسال سفارش را مشخص کنید.</p></div>
            </div>

            <section class="dede-address-block">
                <h4><?php echo esc_html($billing_title); ?></h4>
                <div class="dede-profile__grid">
                    <label class="dede-field">
                        <span>استان <b>ضروری</b></span>
                        <select name="billing_state" data-state-select="billing" required>
                            <option value="">انتخاب استان</option>
                            <?php foreach ($states as $state) : ?><option value="<?php echo esc_attr($state['code']); ?>" <?php selected($profile['billing_state'], $state['code']); ?>><?php echo esc_html($state['name']); ?></option><?php endforeach; ?>
                        </select>
                        <small data-error-for="billing_state"></small>
                    </label>
                    <label class="dede-field">
                        <span>شهر <b>ضروری</b></span>
                        <select name="billing_city" data-city-select="billing" required>
                            <option value="">انتخاب شهر</option>
                            <?php foreach ($billing_cities as $city) : ?><option value="<?php echo esc_attr($city['id']); ?>" <?php selected((int) $profile['billing_city'], $city['id']); ?>><?php echo esc_html($city['name']); ?></option><?php endforeach; ?>
                        </select>
                        <small data-error-for="billing_city"></small>
                    </label>
                    <label class="dede-field">
                        <span>کد پستی <b>ضروری</b></span>
                        <input type="text" name="billing_postcode" value="<?php echo esc_attr($profile['billing_postcode']); ?>" inputmode="numeric" maxlength="10" autocomplete="postal-code" required>
                        <small data-error-for="billing_postcode"></small>
                    </label>
                    <label class="dede-field">
                        <span>تلفن ثابت <b>ضروری</b></span>
                        <input type="tel" name="billing_phone" value="<?php echo esc_attr($profile['billing_phone']); ?>" inputmode="tel" autocomplete="tel" required>
                        <small data-error-for="billing_phone"></small>
                    </label>
                    <label class="dede-field dede-field--wide">
                        <span>آدرس کامل <b>ضروری</b></span>
                        <textarea name="billing_address_1" rows="3" autocomplete="street-address" required><?php echo esc_textarea($profile['billing_address_1']); ?></textarea>
                        <small data-error-for="billing_address_1"></small>
                    </label>
                </div>
            </section>

            <label class="dede-same-address">
                <input type="checkbox" name="same_as_billing" value="1" <?php checked($profile['same_as_billing']); ?>>
                <span><strong>آدرس ارسال با آدرس اصلی یکسان است</strong><small>در این حالت اطلاعات آدرس ارسال به‌صورت خودکار ذخیره می‌شود.</small></span>
            </label>

            <section class="dede-address-block dede-address-block--shipping" data-shipping-fields>
                <h4>آدرس ارسال سفارش</h4>
                <div class="dede-profile__grid">
                    <label class="dede-field">
                        <span>استان <b>ضروری</b></span>
                        <select name="shipping_state" data-state-select="shipping" required>
                            <option value="">انتخاب استان</option>
                            <?php foreach ($states as $state) : ?><option value="<?php echo esc_attr($state['code']); ?>" <?php selected($profile['shipping_state'], $state['code']); ?>><?php echo esc_html($state['name']); ?></option><?php endforeach; ?>
                        </select>
                        <small data-error-for="shipping_state"></small>
                    </label>
                    <label class="dede-field">
                        <span>شهر <b>ضروری</b></span>
                        <select name="shipping_city" data-city-select="shipping" required>
                            <option value="">انتخاب شهر</option>
                            <?php foreach ($shipping_cities as $city) : ?><option value="<?php echo esc_attr($city['id']); ?>" <?php selected((int) $profile['shipping_city'], $city['id']); ?>><?php echo esc_html($city['name']); ?></option><?php endforeach; ?>
                        </select>
                        <small data-error-for="shipping_city"></small>
                    </label>
                    <label class="dede-field">
                        <span>کد پستی <b>ضروری</b></span>
                        <input type="text" name="shipping_postcode" value="<?php echo esc_attr($profile['shipping_postcode']); ?>" inputmode="numeric" maxlength="10" required>
                        <small data-error-for="shipping_postcode"></small>
                    </label>
                    <label class="dede-field">
                        <span>تلفن ثابت <b>ضروری</b></span>
                        <input type="tel" name="shipping_phone" value="<?php echo esc_attr($profile['shipping_phone']); ?>" inputmode="tel" required>
                        <small data-error-for="shipping_phone"></small>
                    </label>
                    <label class="dede-field dede-field--wide">
                        <span>آدرس کامل <b>ضروری</b></span>
                        <textarea name="shipping_address_1" rows="3" required><?php echo esc_textarea($profile['shipping_address_1']); ?></textarea>
                        <small data-error-for="shipping_address_1"></small>
                    </label>
                </div>
            </section>
        </div>

        <div class="dede-profile__panel" data-step="3">
            <div class="dede-profile__section-heading">
                <span>۳</span>
                <div><h3>بررسی و تأیید اطلاعات</h3><p>پیش از ذخیره، اطلاعات اصلی را یک بار بررسی کنید.</p></div>
            </div>

            <div class="dede-review">
                <article><div><span>نوع حساب</span><strong><?php echo esc_html($role_labels[$role]); ?></strong></div><button type="button" data-step-target="1">ویرایش</button></article>
                <article><div><span><?php echo 'company' === $role ? 'شرکت' : ('store' === $role ? 'فروشگاه و مدیر' : 'مشخصات شخص'); ?></span><strong data-review="identity"></strong><small data-review="identifier"></small></div><button type="button" data-step-target="1">ویرایش</button></article>
                <article><div><span><?php echo esc_html($billing_title); ?></span><strong data-review="billing"></strong></div><button type="button" data-step-target="2">ویرایش</button></article>
                <article><div><span>آدرس ارسال سفارش</span><strong data-review="shipping"></strong></div><button type="button" data-step-target="2">ویرایش</button></article>
            </div>

            <div class="dede-profile__notice" data-form-message role="status"></div>
        </div>

        <footer class="dede-profile__actions">
            <button type="button" class="dede-button dede-button--secondary" data-previous>مرحله قبل</button>
            <button type="button" class="dede-button dede-button--primary" data-next>ادامه</button>
            <button type="submit" class="dede-button dede-button--primary" data-submit><?php echo 'checkout' === $context ? 'ذخیره و ادامه پرداخت' : 'ذخیره اطلاعات'; ?></button>
        </footer>
    </form>
</div>
