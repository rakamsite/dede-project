<?php
add_action('wp_ajax_Upload_Profile_Picture', 'Upload_Profile_Picture_callback');
add_action('wp_ajax_nopriv_Upload_Profile_Picture', 'Upload_Profile_Picture_callback');
add_action('wp_ajax_Get_cities_From_state', 'Get_cities_From_state_callback');
add_action('wp_ajax_nopriv_Get_cities_From_state', 'Get_cities_From_state_callback');
add_action('wp_ajax_Charge_Wallet', 'Charge_Wallet_callback');
add_action('wp_ajax_nopriv_Charge_Wallet', 'Charge_Wallet_callback');
add_action('wp_ajax_user_information_manager', 'user_information_manager_callback');
add_action('wp_ajax_nopriv_user_information_manager', 'user_information_manager_callback');
add_action('wp_ajax_Submit_Product_Comment', 'Submit_Product_Comment_callback');
add_action('wp_ajax_nopriv_Submit_Product_Comment', 'Submit_Product_Comment_callback');
add_action('wp_ajax_get_orders_products', 'get_orders_products_callback');
add_action('wp_ajax_nopriv_get_orders_products', 'get_orders_products_callback');
add_action('wp_ajax_submit_guaranty_request', 'submit_guaranty_request_callback');
add_action('wp_ajax_nopriv_submit_guaranty_request', 'submit_guaranty_request_callback');


function Upload_Profile_Picture_callback()
{
    if ($_SERVER['REQUEST_METHOD'] === "POST") {
        $upload_dir = wp_upload_dir();
        $file = $_FILES['ProfileImage'];
        $file_name = $file['name'];
        $file_type = $file['type'];
        $file_size = $file['size'];

        $allowed_types = array('image/jpeg', 'image/png', 'image/gif');
        if (!in_array($file_type, $allowed_types)) {
            wp_send_json_error('فرمت تصویر مجاز نیست.');
            exit;
        }
        if ($file_size > 10000000) {
            wp_send_json_error('اندازه تصویر بیش از حد مجاز است.');
            exit;
        }
        $new_file_name = uniqid() . '.' . pathinfo($file_name, PATHINFO_EXTENSION);
        move_uploaded_file($file['tmp_name'], $upload_dir['path'] . '/' . $new_file_name);
        $user_id = get_current_user_id();
        $profile_image_url = $upload_dir['url'] . '/' . $new_file_name;
        update_user_meta($user_id, '_dede_profile_picture_', $profile_image_url);
        wp_send_json_success('تصویر با موفقیت آپلود شد.');
    }
}

function Get_cities_From_state_callback()
{
    if ($_SERVER['REQUEST_METHOD'] === "POST") {
        $statsId = $_POST['StateName'];
        $city_name = '';
        $cities = get_terms(array(
            'taxonomy' => 'city_country',
            'parent' => $statsId,
            'orderby' => 'name',
            'hide_empty' => false,
        ));
        foreach ($cities as $city) {
            $city_name .= "<option value='$city->term_id'>$city->name</option>";
        }
    }
    wp_send_json_success(["msg" => "شهر خود را انتخاب کنید.", 1 => $city_name]);
}

function user_information_manager_callback(): void
{
    if ($_SERVER['REQUEST_METHOD'] === "POST") {
        function get_term_name_by_id($id)
        {
            $term = get_term($id);

            return $term->name;
        }

        $stateNameWo = [];

        $persianToEnglish = array(
            'آ' => 'A',
            'ا' => 'A',
            'ب' => 'B',
            'پ' => 'P',
            'ت' => 'T',
            'ث' => 'S',
            'ج' => 'J',
            'چ' => 'CH',
            'ح' => 'H',
            'خ' => 'KH',
            'د' => 'D',
            'ذ' => 'Z',
            'ر' => 'R',
            'ز' => 'Z',
            'ژ' => 'ZH',
            'س' => 'S',
            'ش' => 'SH',
            'ص' => 'S',
            'ض' => 'Z',
            'ط' => 'T',
            'ظ' => 'Z',
            'ع' => 'A',
            'غ' => 'GH',
            'ف' => 'F',
            'ق' => 'GH',
            'ک' => 'K',
            'ك' => 'K',
            'گ' => 'K',
            'ل' => 'L',
            'م' => 'M',
            'ن' => 'N',
            'و' => 'V',
            'ه' => 'H',
            'ی' => 'Y',
            'ي' => 'Y',
        );

        $current_user = wp_get_current_user();
        $username = $current_user->user_login;
        $current_user_id = $current_user->ID;
        $user_type = $_POST['select_type'] ?? "";
        $updateUserRole = $_POST['updateUserRol'] ?? false;
        update_user_meta($current_user_id, 'customer_type', $user_type);
        if ($updateUserRole) {
            $user_id_updated = wp_update_user([
                "ID" => $current_user_id,
                "role" => $user_type
            ]);
            update_user_meta($current_user_id, 'customer_type', $user_type);
            if (is_wp_error($user_id_updated)) {
                wp_send_json_error(['msg' => "مشکل در انتخاب نوع حساب کاربری ."]);
            } else {
                wp_send_json_success(['msg' => "نوع کاربری شما با موفقیت بروز شد."]);
            }
        }
        $first_name = strip_tags($_POST['name'] ?? "");
        $last_name = strip_tags($_POST['family'] ?? "");
        $userEmail = strip_tags($_POST['Email'] ?? "");
        $user_data = array(
            'ID' => $current_user_id,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'user_email' => $userEmail
        );

        wp_update_user($user_data);

        $nationCode = strip_tags($_POST['nationCode'] ?? "");
        $BirthDay = strip_tags($_POST['BirthDay'] ?? "");
        $Telegram = strip_tags($_POST['Telegram'] ?? "");
        $trimmed_number = trim($Telegram);
        if (strlen($trimmed_number) === 0) {
            $result_number = '98';
        } elseif (strpos($trimmed_number, '0') === 0) {
            $result_number = '98' . substr($trimmed_number, 1);
        } elseif (strpos($trimmed_number, '98') === 0) {
            $result_number = $trimmed_number;
        } else {
            $result_number = '98' . $trimmed_number;
        }

        foreach (WC()->countries->get_states('IR') as $stateCode => $stateName) {
            if ($stateCode === "CHB") {
                $statesWo["CHHARMHAL BKHTYARY"] = "CHB";
                $stateNameWo ["CHB"] = $stateName;
            } else {
                $statesWo[strtr($stateName, $persianToEnglish)] = $stateCode;
                $stateNameWo [$stateCode] = $stateName;
            }
        }


        $gender = strip_tags($_POST['gender'] ?? "");
        $StateCurrentlyLive = strip_tags($_POST['StateCurrentlyLive']??"");
        $cityCurrentlyLive = strip_tags($_POST['cityCurrentlyLive']??"");
        $PostcodeCurrentlyLive = strip_tags($_POST['PostcodeCurrentlyLive']??"");
        $StaticPhoneNumberCurrentlyLive = strip_tags($_POST['StaticPhoneNumberCurrentlyLive']??"");
        $AddressCurrentlyLive = strip_tags($_POST['AddressCurrentlyLive']??"");
        $StateSendOrder = strip_tags($_POST['StateSendOrder']??"");
        $citySendOrder = strip_tags($_POST['citySendOrder']??"");
        $PostcodeSendOrder = strip_tags($_POST['PostcodeSendOrder']??"");
        $StaticPhoneNumberSendOrder = strip_tags($_POST['StaticPhoneNumberSendOrder'] ?? "");
        $AddressSendOrder = strip_tags($_POST['AddressSendOrder'] ?? "");
        $CompanyName = strip_tags($_POST['CompanyName'] ?? "");
        $RegistrationNumber = strip_tags($_POST['RegistrationNumber'] ?? "");
        $NationalId = strip_tags($_POST['NationalId'] ?? "");
        $EconomicId = strip_tags($_POST['EconomicId'] ?? "");
        $StoreName = strip_tags($_POST['StoreName'] ?? "");
        $BirthDayTimestamp = strip_tags($_POST['birthdayTimeStampUnixFormat'] ?? "");

        update_user_meta($current_user_id, "_dede_national_code_", $nationCode);
        update_user_meta($current_user_id, "_dede_birthday_", $BirthDay);
        update_user_meta($current_user_id, "_dede_birthday_timestamp_", $BirthDayTimestamp);
        update_user_meta($current_user_id, "_dede_Telegram_", $result_number);
        update_user_meta($current_user_id, "_dede_Gender_", $gender);


        update_user_meta($current_user_id, "_dede_registration_number_", $RegistrationNumber);
        update_user_meta($current_user_id, "_dede_national_id_", $NationalId);
        update_user_meta($current_user_id, "_dede_Economic_Code_", $EconomicId);
        update_user_meta($current_user_id, "_dede_shop_name_", $StoreName);


        update_user_meta($current_user_id, 'billing_first_name', $first_name);
        update_user_meta($current_user_id, 'billing_last_name', $last_name);
        update_user_meta($current_user_id, 'billing_company', $CompanyName);
        update_user_meta($current_user_id, 'billing_address_1', $AddressCurrentlyLive);
        update_user_meta($current_user_id, 'billing_city', $cityCurrentlyLive);
        update_user_meta($current_user_id, 'billing_state', $StateCurrentlyLive);
        update_user_meta($current_user_id, 'billing_postcode', $PostcodeCurrentlyLive);
        update_user_meta($current_user_id, 'billing_phone', $StaticPhoneNumberCurrentlyLive);

        update_user_meta($current_user_id, "shipping_first_name", $first_name);
        update_user_meta($current_user_id, "shipping_last_name", $last_name);
        update_user_meta($current_user_id, "shipping_company", $CompanyName);
        update_user_meta($current_user_id, "shipping_state", $StateSendOrder);
        update_user_meta($current_user_id, "shipping_city", $citySendOrder);
        update_user_meta($current_user_id, "shipping_postcode", $PostcodeSendOrder);
        update_user_meta($current_user_id, "shipping_phone", $StaticPhoneNumberSendOrder);
        update_user_meta($current_user_id, "shipping_address_1", $AddressSendOrder);

        update_user_meta($current_user_id, "city_custom_billing", get_term_name_by_id($cityCurrentlyLive));
        update_user_meta($current_user_id, "state_custom_billing", $stateNameWo[$StateCurrentlyLive]);
        update_user_meta($current_user_id, "city_custom_shipping", get_term_name_by_id($citySendOrder));
        update_user_meta($current_user_id, "state_custom_shipping", $stateNameWo[$StateSendOrder]);
        update_user_meta($current_user_id, "custom_first_name", $first_name);
        update_user_meta($current_user_id, "custom_last_name", $last_name);


        wp_send_json_success(['msg' => 'با موفقیت بروز شد.']);

    }
}

function Charge_Wallet_callback()
{
    if ($_SERVER['REQUEST_METHOD'] === "POST") {
        $order_amount = intval($_POST['amountAddToWallet']);
        $new_order = new WC_Order();
        $new_order->set_total($order_amount);
        $new_order->update_meta_data('_dede_check_order_type_', 'charge_wallet');
        $new_order->set_customer_id(get_current_user_id());
        $payment_gateways = WC()->payment_gateways->get_available_payment_gateways();
        $first_gateway_key = array_keys($payment_gateways)[0];
        $new_order->set_payment_method($first_gateway_key);
        $new_order->set_payment_method_title($payment_gateways[$first_gateway_key]->get_title());
        $new_order->save();
        $payment_url = $new_order->get_checkout_payment_url(true);
        wp_send_json_success(['msg' => 'با موفقیت اضافه شد', 1 => $payment_url]);
    }
}

function Submit_Product_Comment_callback()
{

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $comment_picture = [];
        $comment_video = [];
        $comment_id = [];
        $current_user = wp_get_current_user();
        $user_id = $current_user->ID;
//		delete_user_meta( $user_id, '_dede_commented_var_id_' );
        $var_id_commented = get_user_meta($user_id, '_dede_commented_var_id_', true);
        $product_id = $_POST['product_id'];
        function is_product_in_user_orders($product_id, $user_id)
        {
            if ($user_id) {
                $args = array(
                    'customer_id' => $user_id,
                    'status' => array('completed')
                );

                $orders = wc_get_orders($args);

                foreach ($orders as $order) {
                    foreach ($order->get_items() as $item) {
                        if ($item->get_product()->get_id() == $product_id) {
                            return true;
                        }
                    }
                }
            }

            return false;
        }

        if (is_product_in_user_orders($product_id, $user_id)) {
            $comment_text = sanitize_text_field($_POST['commentText']);
            $ratingStars = $_POST['ratingStars'];
            $var_id_research = (string)$product_id;
            if (strpos($var_id_commented, $var_id_research)) {
                wp_send_json_error("شما قبلا برای این محصول نظر ثبت کرده اید.");
            }
            if (empty($comment_text)) {
                wp_send_json_error("لطفا نظر خود را با ما به اشتراک بگذارید.");
            }
            if ($ratingStars === 'undefined') {
                wp_send_json_error("لطفا با تعداد ستاره ها نظر خود را نسبت به محصول بیان کنید.");
            }
            $upload_dir = wp_upload_dir();

            $commentData = array(
                'comment_post_ID' => $product_id,
                'user_id' => $user_id,
                'comment_content' => $comment_text,
                'comment_author' => $current_user->first_name . ' ' . $current_user->last_name,
                'comment_author_email' => $current_user->user_email,
                'comment_approved' => 0,
            );
            $comment_id = wp_insert_comment($commentData);
            $var_id_commented .= ' ' . $product_id;
            update_user_meta($user_id, '_dede_commented_var_id_', $var_id_commented);
            add_comment_meta($comment_id, '_dede_comment_rating_', $ratingStars);
            if (!empty($_FILES['images'])) {
                $image_files = $_FILES['images'];
                foreach ($image_files['name'] as $index => $name) {
                    if ($image_files['error'][$index] === UPLOAD_ERR_OK) {
                        $temp_path = $image_files['tmp_name'][$index];
                        $file_name = sanitize_file_name($name);
                        $upload_path = $upload_dir['path'] . '/' . $file_name;
                        move_uploaded_file($temp_path, $upload_path);
                        $attachment = array(
                            'guid' => $upload_dir['url'] . '/' . $file_name,
                            'post_mime_type' => mime_content_type($upload_path),
                            'post_title' => preg_replace('/\.[^.]+$/', '', $file_name),
                            'post_content' => '',
                            'post_status' => 'inherit',
                        );
                        $comment_picture [] = wp_insert_attachment($attachment, $upload_path, $product_id);
                    }
                }
                add_comment_meta($comment_id, '_dede_comment_image_', $comment_picture);
            }
            if (!empty($_FILES['videos'])) {
                $video_files = $_FILES['videos'];
                foreach ($video_files['name'] as $index => $name) {
                    if ($video_files['error'][$index] === UPLOAD_ERR_OK) {
                        $temp_path = $video_files['tmp_name'][$index];
                        $file_name = sanitize_file_name($name);
                        $upload_path = $upload_dir['path'] . '/' . $file_name;
                        move_uploaded_file($temp_path, $upload_path);
                        $attachment = array(
                            'guid' => $upload_dir['url'] . '/' . $file_name,
                            'post_mime_type' => mime_content_type($upload_path),
                            'post_title' => preg_replace('/\.[^.]+$/', '', $file_name),
                            'post_content' => '',
                            'post_status' => 'inherit',
                        );
                        $video_attachment_id = wp_insert_attachment($attachment, $upload_path, $product_id);
                        $comment_video[] = $video_attachment_id;
                    }
                }
                add_comment_meta($comment_id, '_dede_comment_video_', $comment_video);
            }
        }
        wp_send_json_success([
            'msg' => 'نظر شما با موفقیت ارسال شد .',
            $comment_picture,
            $comment_video,
            $comment_id
        ]);
    } else {
        wp_send_json_error(['msg' => 'درحال حاظر نمیتوانید برای این محصول نظری ثبت نمایید .']);
    }
}

function get_orders_products_callback()
{
    if ($_SERVER['REQUEST_METHOD'] === "POST") {
        $order_id = $_POST['order_id'];
        $order = wc_get_order($order_id);
        if (empty($order)) {
            wp_send_json_error("یک سفارش را انتخاب کنید.");
        }
        $orders_product = '';
        foreach ($order->get_items() as $item_id => $item) {
            $product = $item->get_product();
            $product_id = $product->get_id();
            $product_name = $product->get_name();
            $orders_product .= "<option value='$product_id'>$product_name</option>";
        }
        wp_send_json_success(['msg' => 'محصول را انتخاب کنید.', 1 => $orders_product]);
    }
}

function submit_guaranty_request_callback()
{
    if ($_SERVER['REQUEST_METHOD'] === "POST") {
        $order_id = intval($_POST['order_id']);
        $product_id = intval($_POST['product_id']);
        $product_count = intval($_POST['product_count']);
        if (empty($product_id) || empty($order_id) || empty($product_count)) {
            wp_send_json_error("سفارش ، محصول و تعداد محصولی که میخواهید درخواست ثبت گارانتی دهید را انتخاب کنید.");
        }
        $user = wp_get_current_user();
        $first_name = $user->first_name;
        $last_name = $user->last_name;
        $new_post = array(
            'post_status' => 'publish',
            'post_type' => 'guarantee',
        );
        $post_id = wp_insert_post($new_post);
        if ($post_id) {
            wp_update_post([
                'ID' => $post_id,
                'post_title' => " سفارش #$order_id به درخواست $first_name $last_name به شماره پیگیری G$post_id  ",
            ]);
            update_post_meta($post_id, "_dede_guaranty_order_", $order_id);
            update_post_meta($post_id, "_dede_guaranty_product_", $product_id);
            update_post_meta($post_id, "_dede_guaranty_product_count_", $product_count);
            update_post_meta($post_id, "_dede_guarantee_status_", 0);
            update_post_meta($post_id, "_dede_guarantee_number_", 0);
            wp_send_json_success(["msg" => "درخواست ثبت گارانتی شما با موفقیت ثبت شد."]);
        } else {
            wp_send_json_error('خطا در ایجاد پست.');
        }
    }
}