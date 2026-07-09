<?php
function add_user_meta_field($user): void
{
    ?>
    <h3>اطلاعات اختصاصی</h3>
    <table class="form-table">
        <tr>
            <th><label for="_dede_national_code_">کد ملی</label></th>
            <td>
                <input type="text" name="_dede_national_code_" id="_dede_national_code_"
                       value="<?php echo esc_attr(get_user_meta($user->ID, '_dede_national_code_', true)); ?>"
                       class="regular-text"/>
            </td>
        </tr>
        <tr>
            <th><label for="_dede_birthday_">تاریخ تولد</label></th>
            <td>
                <input type="text" name="_dede_birthday_" id="_dede_birthday_"
                       value="<?php echo esc_attr(get_user_meta($user->ID, '_dede_birthday_', true)); ?>"
                       class="regular-text"/>
            </td>
        </tr>
        <tr>
            <th><label for="_dede_Telegram_">تلگرام</label></th>
            <td>
                <input type="text" name="_dede_Telegram_" id="_dede_Telegram_"
                       value="<?php echo esc_attr(get_user_meta($user->ID, '_dede_Telegram_', true)); ?>"
                       class="regular-text"/>
            </td>
        </tr>
        <tr>
            <th><label for="_dede_Gender_">جنسیت</label></th>
            <td>
                <input type="text" name="_dede_Gender_" id="_dede_Gender_"
                       value="<?php echo esc_attr(get_user_meta($user->ID, '_dede_Gender_', true)); ?>"
                       class="regular-text"/>
            </td>
        </tr>
        <tr>
            <th><label for="_dede_shop_name_">نام فروشگاه</label></th>
            <td>
                <input type="text" name="_dede_shop_name_" id="_dede_shop_name_"
                       value="<?php echo esc_attr(get_user_meta($user->ID, '_dede_shop_name_', true)); ?>"
                       class="regular-text"/>
            </td>
        </tr>
        <tr>
            <th><label for="_dede_registration_number_">شماره ثبت</label></th>
            <td>
                <input type="text" name="_dede_registration_number_" id="_dede_registration_number_"
                       value="<?php echo esc_attr(get_user_meta($user->ID, '_dede_registration_number_', true)); ?>"
                       class="regular-text"/>
            </td>
        </tr>
        <tr>
            <th><label for="_dede_national_id_">شناسه ملی</label></th>
            <td>
                <input type="text" name="_dede_national_id_" id="_dede_national_id_"
                       value="<?php echo esc_attr(get_user_meta($user->ID, '_dede_national_id_', true)); ?>"
                       class="regular-text"/>
            </td>
        </tr>
        <tr>
            <th><label for="state_custom_billing">استان محل سکونت</label></th>
            <td>
                <input type="text" name="state_custom_billing" id="state_custom_billing"
                       value="<?php echo esc_attr(get_user_meta($user->ID, 'state_custom_billing', true)); ?>"
                       class="regular-text"/>
            </td>
        </tr>
        <tr>
            <th><label for="city_custom_billing">شهر محل سکونت</label></th>
            <td>
                <input type="text" name="city_custom_billing" id="city_custom_billing"
                       value="<?php echo esc_attr(get_user_meta($user->ID, 'city_custom_billing', true)); ?>"
                       class="regular-text"/>
            </td>
        </tr>
        <tr>
            <th><label for="state_custom_shipping">استان ارسال سفارش</label></th>
            <td>
                <input type="text" name="state_custom_shipping" id="state_custom_shipping"
                       value="<?php echo esc_attr(get_user_meta($user->ID, 'state_custom_shipping', true)); ?>"
                       class="regular-text"/>
            </td>
        </tr>
        <tr>
            <th><label for="city_custom_shipping">شهر ارسال سفارش</label></th>
            <td>
                <input type="text" name="city_custom_shipping" id="city_custom_shipping"
                       value="<?php echo esc_attr(get_user_meta($user->ID, 'city_custom_shipping', true)); ?>"
                       class="regular-text"/>
            </td>
        </tr>
    </table>
    <?php
}

add_action('show_user_profile', 'add_user_meta_field');
add_action('edit_user_profile', 'add_user_meta_field');

function save_user_meta_field($user_id): void
{
    if (current_user_can('edit_user', $user_id)) {
        update_user_meta($user_id, '_dede_national_code_', sanitize_text_field($_POST['user_meta_data']));
        update_user_meta($user_id, '_dede_birthday_', sanitize_text_field($_POST['_dede_birthday_']));
        update_user_meta($user_id, '_dede_Telegram_', sanitize_text_field($_POST['_dede_Telegram_']));
        update_user_meta($user_id, '_dede_Gender_', sanitize_text_field($_POST['_dede_Gender_']));
        update_user_meta($user_id, '_dede_shop_name_', sanitize_text_field($_POST['_dede_shop_name_']));
        update_user_meta($user_id, '_dede_registration_number_', sanitize_text_field($_POST['_dede_registration_number_']));
        update_user_meta($user_id, '_dede_national_id_', sanitize_text_field($_POST['_dede_national_id_']));
        update_user_meta($user_id, 'city_custom_billing', sanitize_text_field($_POST['city_custom_billing']));
        update_user_meta($user_id, 'state_custom_billing', sanitize_text_field($_POST['state_custom_billing']));
        update_user_meta($user_id, 'state_custom_shipping', sanitize_text_field($_POST['state_custom_shipping']));
        update_user_meta($user_id, 'city_custom_shipping', sanitize_text_field($_POST['city_custom_shipping']));
    }
}

add_action('personal_options_update', 'save_user_meta_field');
add_action('edit_user_profile_update', 'save_user_meta_field');
